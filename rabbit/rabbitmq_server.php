<?php
require __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

try {
    // Connect to broker (me/Jason)
    $connection = new AMQPStreamConnection('172.30.103.53', 5672, 'test', 'test', 'testHost');
    $channel = $connection->channel();


    // frontend consumer queue
    $channel->queue_declare('front_consumer_queue', false, true, false, false);
    echo " [*] Frontend consumer queue declared successfully.\n";


    // backend consumer queue
    $channel->queue_declare('back_consumer_queue', false, true, false, false);
    echo " [*] Backend consumer queue declared successfully.\n";


    // frontend producer queue
    $channel->queue_declare('frontend_producer_queue', false, true, false, false);
    echo " [*] Frontend producer queue declared successfully.\n";


    // backend producer queue 
    $channel->queue_declare('back_producer_queue', false, true, false, false);
    echo " [*] Backend producer queue declared successfully.\n";


    //check if an email and password is sent thru a form
    $email = $_POST['email'];
    $password = $_POST['password']; 
    // message the frontend consumer queue
    $frontMsgBody = json_encode(['action' => 'login', 'emailAddr' => $email, 'password' => $password]);
    $frontMsg = new AMQPMessage($frontMsgBody);
    $channel->basic_publish($frontMsg, '', 'front_consumer_queue');
    echo " [x] Sent 'Login Request' to the frontend consumer queue\n";


    $dataToSend = 'insert data when we get to tht point';  // data to be processed
    // Message the backend consumer queue
    $backMsgBody = json_encode(['action' => 'process', 'data' => $dataToSend]);
    $backMsg = new AMQPMessage($backMsgBody);
    $channel->basic_publish($backMsg, '', 'back_consumer_queue');
    echo " [x] Sent 'Process Request' to the backend consumer queue\n";

    // Message the frontend producer queue
    $producerMsgBody = json_encode(['action' => 'produce', 'data' => 'producer_data']);
    $producerMsg = new AMQPMessage($producerMsgBody);
    $channel->basic_publish($producerMsg, '', 'frontend_producer_queue');
    echo " [x] Sent 'Produce Request' to the frontend producer queue\n";

    // Message the backend producer queue 
    $backProducerMsgBody = json_encode(['action' => 'back_produce', 'data' => 'back_producer_data']);
    $backProducerMsg = new AMQPMessage($backProducerMsgBody);
    $channel->basic_publish($backProducerMsg, '', 'back_producer_queue');
    echo " [x] Sent 'Back Produce Request' to the backend producer queue\n";

    //continously listen for incoming messages
    $callback = function ($msg) {
        $data = json_decode($msg->body, true);
        if ($data['action'] == 'login') {
            echo "Processing login for email: " . $data['emailAddr'] . "\n";
        } elseif ($data['action'] == 'process') {
            echo "Processing data: " . $data['data'] . "\n";
        } elseif ($data['action'] == 'produce') {
            echo "Producing data: " . $data['data'] . "\n";
        } elseif ($data['action'] == 'back_produce') {
            echo "Processing backend producer data: " . $data['data'] . "\n";
        }
        $msg->ack(); // ack the message 
    };
    $channel->basic_consume('front_consumer_queue', '', false, false, false, false, $callback);
    $channel->basic_consume('back_consumer_queue', '', false, false, false, false, $callback);

    // wait for incoming messages 
    echo " [*] Waiting for messages\n";
    while ($channel->is_consuming()) {
        $channel->wait();
    }
    $channel->close();
    $connection->close();
    echo " [*] Connection closed successfully.\n";

} catch (Exception $e) {
    // Exception
    echo " [!] An error occurred: " . $e->getMessage() . "\n";
    if (isset($channel) && $channel->is_open()) {
        $channel->close();
    }
    if (isset($connection) && $connection->isConnected()) {
        $connection->close();
    }
    exit(1);
}
