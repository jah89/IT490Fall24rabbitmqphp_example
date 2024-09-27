<?php

require_once('get_host_info.inc.php');

class rabbitMQServer
{
	private array $machine;
	public  string $BROKER_HOST;
	private int $BROKER_PORT;
	private string $USER;
	private string $PASSWORD;
	private string $VHOST;

	//created and changed names to strings for less confusion
	private string $clientExchangeName;
	private string $serverQueueName;
	private $routing_key = '*';
	private $exchange_type = "topic";
	private $serverQueue;
	private $callback;
	private $auto_delete = false;
	private $responses = array();


	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine 			    = getHostInfo(array($machine));
		$this->BROKER_HOST  	    = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT  	    = $this->machine[$server]["BROKER_PORT"];
		$this->USER     		    = $this->machine[$server]["USER"];
		$this->PASSWORD 		    = $this->machine[$server]["PASSWORD"];
		$this->VHOST 			    = $this->machine[$server]["VHOST"];
		$this->clientExchangeName   = $this->machine[$server]['CLIENT_EXCHANGE']; 
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->serverQueue = $this->machine[$server]["QUEUE"];
	}
	/**
	 * Function to process message from queue.
	 *
	 * @param AMQPEnvelope $msg The message being sent.
	 * @return void
	 */
	function process_message($msg)
	{
		if ($msg->getRoutingKey() !== "*")
    {
      return;
    }
	
	// send the ack to clear the item from the queue
    $this->serverQueue->ack($msg->getDeliveryTag());
		try
		{
			if ($msg->getReplyTo())
			{
				// message wants a response, process the request
				$body = $msg->getBody();
				$payload = json_decode($body, true);

				//$payload = $msg->getBody();
				if (isset($this->callback))
				{
					$response = call_user_func($this->callback, $payload);
				}

      $params = array();
      $params['host'] = $this->BROKER_HOST;
      $params['port'] = $this->BROKER_PORT;
      $params['login'] = $this->USER;
      $params['password'] = $this->PASSWORD;
      $params['vhost'] = $this->VHOST;
			$conn = new AMQPConnection($params);
			$conn->connect();
			$channel = new AMQPChannel($conn);
			$exchange = new AMQPExchange($channel);
      $exchange->setName($this->clientExchangeName);
      $exchange->setType($this->exchange_type);

			$serverQueue = new AMQPQueue($channel);
			$serverQueue->setName($msg->getReplyTo());
			$replykey = $this->routing_key.".response";
			$serverQueue->bind($exchange->getName(),$replykey);
			$exchange->publish(
				$response,
				$msg->getReplyTo(),
				AMQP_NOPARAM,
				array('correlation_id'=>$msg->getCorrelationId())
			);

				return;
			} else {
				//if no response required send an ack automatically,
				$body = $msg->getBody();
				$payload = json_decode($body, true);
                if (isset($this->callback)) {
                    call_user_func($this->callback, $payload);
                }
				echo "processed one-way message\n";
            }
		}
		catch(Exception $e)
		{
			// ampq throws exception if get fails...
            echo "error: rabbitMQServer: process_message: exception caught: ".$e;
		}
	} //end of process_message

	/**
	 * Function to connect to server and begin processing requests.
	 *
	 * @param callable? $callback Callback func when a request comes in.
	 * @return void
	 */
	function process_requests($callback)
	{
		try
		{
			$this->callback 	 = $callback;
            $params 			 = array();
            $params['host'] 	 = $this->BROKER_HOST;
            $params['port'] 	 = $this->BROKER_PORT;
            $params['login'] 	 = $this->USER;
            $params['password']  = $this->PASSWORD;
            $params['vhost'] 	 = $this->VHOST;
			//add heartbeat to keep queue active
			$params['heartbeat'] = 60;

			$conn 				 = new AMQPConnection($params);
			$conn->connect();

			$channel = new AMQPChannel($this->$conn);

			$exchange = new AMQPExchange($channel);
            $exchange->setName($this->clientExchangeName);
            $exchange->setType($this->exchange_type);
            $exchange->declareExchange();

			$this->serverQueue = new AMQPQueue($channel);
			$this->serverQueue->setName($this->serverQueueName);
            //$this->serverQueue->setFlags(AMQP_DURABLE);  // Ensure that the queue is declared as durable
            $this->serverQueue->declareQueue();  // Now declare the queue
			$this->serverQueue->bind($exchange->getName(),$this->routing_key);
			$this->serverQueue->consume(array($this,'process_message'));

			// Loop as long as the channel has callbacks registered
			while (count($channel->callbacks))
			{
				$channel->wait();
			}
		}
		catch (Exception $e)
		{
			trigger_error("Failed to start request processor: ".$e,E_USER_ERROR); 
		}
	}
}

/**
 * Professor's client class. The client that sends messages to rabbitMQ.
 */
class rabbitMQClient
{
	private $machine = "";
	public  $BROKER_HOST;
	private $BROKER_PORT;
	private $USER;
	private $PASSWORD;
	private $VHOST;
	private $exchange;
	private $queue;
	private $routing_key = '*';
	private $response_queue = array();
	private $exchange_type = "topic";

	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine = getHostInfo(array($machine));
		$this->BROKER_HOST   = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT   = $this->machine[$server]["BROKER_PORT"];
		$this->USER     = $this->machine[$server]["USER"];
		$this->PASSWORD = $this->machine[$server]["PASSWORD"];
		$this->VHOST = $this->machine[$server]["VHOST"];
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->exchange = $this->machine[$server]["EXCHANGE"];
		$this->queue = $this->machine[$server]["QUEUE"];
	}

	function process_response($response)
	{
		$uid = $response->getCorrelationId();
		if (!isset($this->response_queue[$uid]))
		{
		  echo  "unknown uid\n";
		  return true;
		}
    $this->conn_queue->ack($response->getDeliveryTag());
		$body = $response->getBody();
		$payload = json_decode($body, true);
		if (!(isset($payload)))
		{
			$payload = "[empty response]";
		}
		$this->response_queue[$uid] = $payload;
		return false;
	}

	function send_request($message)
	{
		$uid = uniqid();

		$json_message = json_encode($message);
		try
		{
      $params = array();
      $params['host'] = $this->BROKER_HOST;
      $params['port'] = $this->BROKER_PORT;
      $params['login'] = $this->USER;
      $params['password'] = $this->PASSWORD;
      $params['vhost'] = $this->VHOST;

			$conn = new AMQPConnection($params);
			$conn->connect();

			$channel = new AMQPChannel($conn);

			$exchange = new AMQPExchange($channel);
      $exchange->setName($this->exchange);
      $exchange->setType($this->exchange_type);

      $callback_queue = new AMQPQueue($channel);
      $callback_queue->setName($this->queue."_response");
      $callback_queue->declare();
			$callback_queue->bind($exchange->getName(),$this->routing_key.".response");

			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);

			$exchange->publish($json_message,$this->routing_key,AMQP_NOPARAM,array('reply_to'=>$callback_queue->getName(),'correlation_id'=>$uid));
      $this->response_queue[$uid] = "waiting";
			$callback_queue->consume(array($this,'process_response'));

			$response = $this->response_queue[$uid];
			unset($this->response_queue[$uid]);
			return $response;
		}
		catch(Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}

	/**
	  @brief send a one-way message to the server.  These are
	  auto-acknowledged and give no response.

	  @param message the body of the request.  This must make sense to the
	  server
	 */
	function publish($json_message)
	{
		//$json_message = json_encode($message);
		try
		{
      $params = array();
      $params['host'] = $this->BROKER_HOST;
      $params['port'] = $this->BROKER_PORT;
      $params['login'] = $this->USER;
      $params['password'] = $this->PASSWORD;
      $params['vhost'] = $this->VHOST;
			$conn = new AMQPConnection($params);
			$conn->connect();
			$channel = new AMQPChannel($conn);
			$exchange = new AMQPExchange($channel);
      $exchange->setName($this->exchange);
      $exchange->setType($this->exchange_type);
			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);
			return $exchange->publish($json_message,$this->routing_key);
		}
		catch(Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}
}
?>
