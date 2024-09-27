<?php

require_once(__DIR__.'/get_host_info.inc');

class rabbitMQServer
{
	private array $machine;
	public  string $BROKER_HOST;
	private int $BROKER_PORT;
	private string $USER;
	private string $PASSWORD;
	private string $VHOST;
	private string $exchange;
	private $routing_key = '*';
	private $exchange_type = "topic";
	private $serverQueue;
	private $conn_queue;
	private $callback;
	private $responses_array = array();


	private $auto_delete = false;

	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine 			= getHostInfo(array($machine));
		$this->BROKER_HOST  	= $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT  	= $this->machine[$server]["BROKER_PORT"];
		$this->USER     		= $this->machine[$server]["USER"];
		$this->PASSWORD 		= $this->machine[$server]["PASSWORD"];
		$this->VHOST 			= $this->machine[$server]["VHOST"];
		$this->exchange		    = $this->machine[$server]['CLIENT_EXCHANGE']; 
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->exchange = $this->machine[$server]["EXCHANGE"];
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
				// these two lines are the original, they don't seem quite right
				//$body = $msg->getBody();
				//$payload = json_decode($body, true);

				$payload = $msg->getBody();
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
      $exchange->setName($this->exchange);
      $exchange->setType($this->exchange_type);

			$conn_queue = new AMQPQueue($channel);
			$conn_queue->setName($msg->getReplyTo());
			$replykey = $this->routing_key.".response";
			$conn_queue->bind($exchange->getName(),$replykey);
			$exchange->publish(
				$response,
				$msg->getReplyTo(),
				AMQP_NOPARAM,
				array('correlation_id'=>$msg->getCorrelationId())
			);

				return;
			} else {
				//if no response required send an ack automatically,
				$payload = $msg->getBody();
                if (isset($this->callback)) {
                    call_user_func($this->callback, $payload);
                }
            }
		}
		catch(Exception $e)
		{
			// ampq throws exception if get fails...
            echo "error: rabbitMQServer: process_message: exception caught: ".$e;
		}
		// Original code below:
		// message does not require a response, send ack immediately
		// $body = $msg->getBody();
		// $payload = json_decode($body, true);
		// if (isset($this->callback))
		// {
		// 	call_user_func($this->callback,$payload);
		// }
		// echo "processed one-way message\n";
	} //end of process_message

	/**
	 * Function to connect to server and being processing requests.
	 *
	 * @param callable? $callback Callback func when a request comes in.
	 * @return void
	 */
	function process_requests($callback)
	{
		try
		{
			$this->callback 	= $callback;
            $params 			= array();
            $params['host'] 	= $this->BROKER_HOST;
            $params['port'] 	= $this->BROKER_PORT;
            $params['login'] 	= $this->USER;
            $params['password'] = $this->PASSWORD;
            $params['vhost'] 	= $this->VHOST;
			$conn 				= new AMQPConnection($params);
			$this->$conn->connect();

			$channel = new AMQPChannel($this->$conn);

			$exchange = new AMQPExchange($channel);
            $exchange->setName($this->exchange);
            $exchange->setType($this->exchange_type);
            $exchange->declareExchange();

			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->serverQueue);
            $this->conn_queue->setFlags(AMQP_DURABLE);  // Ensure that the queue is declared as durable
            $this->conn_queue->declareQueue();  // Now declare the queue
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);
			$this->conn_queue->consume(array($this,'process_message'));

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
	private $machine = [];
	public  $BROKER_HOST;
	private $BROKER_PORT;
	private $USER;
	private $PASSWORD;
	private $VHOST;
	private $exchange;
	private $clientExchange;
	private $queuePrefix;
	private $routing_key = '*';
	private $response_queue;
	private $exchange_type = "topic";
	private $responses;
	private $auto_delete;

	/**
    * Create new RabbitMq client.
    *
    * @param string $machine Path to ini file.
    * @param string $client Name of the server that receives.
    */
	function __construct($machine, string $server)
	{
		$this->machine 		 	= getHostInfo(array($machine));
		$this->BROKER_HOST   	= $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT   	= $this->machine[$server]["BROKER_PORT"];
		$this->USER     	 	= $this->machine[$server]["USER"];
		$this->PASSWORD 	 	= $this->machine[$server]["PASSWORD"];
		$this->VHOST 		 	= $this->machine[$server]["VHOST"];
		$this->exchange 		= $this->machine[$server]['SERVER_EXCHANGE'];
        $this->clientExchange   = $this->machine[$clientExchange]['CLIENT_EXCHANGE'];
        $this->queuePrefix      = $this->machine[$server]['CLIENT_QUEUE_PREFIX'];
        $this->responses        = [];
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->exchange = $this->machine[$server]["EXCHANGE"];
		$this->conn_queue = $this->machine[$server]["QUEUE"];
	}


	/**
	 * Function to process to reponse to request. Sets the responses array.
	 *
	 * @param AMPQEnvelope $respone the response message.
	 * @param AMPQQueue $response_queue the queue that responses came in on.
	 * @return void
	 */
	function process_response($response, $response_queue)
	{
		$uid = $response->getCorrelationId();
		if (!isset($this->response_queue[$uid]))
		{
		  echo  "unknown uid\n";
		  return true;
		}
    	$response_queue->ack($response->getDeliveryTag());
		$body = $response->getBody();
		$payload = json_decode($body, true);
		if (!(isset($payload)))
		{
			$payload = "[empty response]";
		}
		$this->response_queue[$uid] = $payload;
		return false;
	}

	/**
	 * Function to send a request to rabbitMQ.
	 *
	 * @param [type] $message Messages to send.
	 * @param $contentType The type of message.
	 * @return message msg_response.
	 */
	function send_request($message, $contentType)
	{
		$uid = uniqid();

		//$json_message = json_encode($message);
		try
		{
      $params 			  = array();
      $params['host']     = $this->BROKER_HOST;
      $params['port'] 	  = $this->BROKER_PORT;
      $params['login'] 	  = $this->USER;
      $params['password'] = $this->PASSWORD;
      $params['vhost']    = $this->VHOST;

			$conn = new AMQPConnection($params);
			$conn->connect();

			$channel = new AMQPChannel($conn);

			$exchange = new AMQPExchange($channel);
      $exchange->setName($this->exchange);
      $exchange->setType($this->exchange_type);
	
      $response_queue = new AMQPQueue($channel);
      $response_queue->setName($this->queuePrefix.'.'.$uid);
	  $response_queue->setFlags(AMQP_AUTODELETE);
      $response_queue->declareQueue();
			$response_queue->bind($exchange->getName(),$this->routing_key.".response");

			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
        // Ensure that the queue is durable
            $this->conn_queue->setFlags(AMQP_DURABLE);

// Declare the queue (with durability)
            $this->conn_queue->declareQueue();
            $this->conn_queue->declareQueue();
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);

			$exchange->publish(
				$message,
				$this->routing_key,
				AMQP_NOPARAM,
				array(
				'reply_to'=>$callback_queue->getName(),
				'content_type'=> $contentType,
				    'correlation_id'=>$uid
				));

         $response_queue[$uid] = "waiting";
			$response_queue->consume(array($this,'process_response'));

			$response = $this->responses_array[$uid];
			unset($this->responses_array[$uid]);
			return $response;
		}
		catch(Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}

	/**
	*  @brief send a one-way message to the server.  These are
	*  auto-acknowledged and give no response.

	* @param message the body of the request.  This must make sense to the
	* server
	 */
	function publish($message)
	{
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
			$this->conn_queue = new AMQPQueue($channel);
			$this->conn_queue->setName($this->conn_queue);
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
