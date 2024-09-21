<?php
require __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;



try {
    // Connect to broker(me/jason)
    $connection = new AMQPStreamConnection('172.30.103.53', 5672, 'test', 'test', 'testHost');
    $channel = $connection->channel();

    // frontend consumer queue
    $channel->queue_declare('front_consumer_queue', false, true, false, false);
    echo " [*] Frontend consumer queue declared successfully.\n";

    //backend consumer queue
    $channel->queue_declare('back_consumer_queue', false, true, false, false);
    echo " [*] Backend consumer queue declared successfully.\n";

    // frontend producer queue
    $channel->queue_declare('frontend_producer_queue', false, true, false, false);
    echo " [*] Frontend producer queue declared successfully.\n";

    // Send message to the frontend consumer queue
    $frontMsgBody = json_encode(['action' => 'login', 'emailAddr' => 'example@mail.com']);
    $frontMsg = new AMQPMessage($frontMsgBody);

    $channel->basic_publish($frontMsg, '', 'front_consumer_queue');
    echo " [x] Sent 'Login Request' to the frontend consumer queue\n";

    // message the backend consumer queue
    $backMsgBody = json_encode(['action' => 'process', 'data' => 'some_data']);
    $backMsg = new AMQPMessage($backMsgBody);

    $channel->basic_publish($backMsg, '', 'back_consumer_queue');
    echo " [x] Sent 'Process Request' to the backend consumer queue\n";

    // message the frontend producer queue
    $producerMsgBody = json_encode(['action' => 'produce', 'data' => 'producer_data']);
    $producerMsg = new AMQPMessage($producerMsgBody);

    $channel->basic_publish($producerMsg, '', 'frontend_producer_queue');
    echo " [x] Sent 'Produce Request' to the frontend producer queue\n";

    $channel->close();
    $connection->close();
    echo " [*] Connection closed successfully.\n";

} catch (Exception $e) {
    // exceptions
    echo " [!] An error occurred: " . $e->getMessage() . "\n";
    if (isset($channel) && $channel->is_open()) {
        $channel->close();
    }
    if (isset($connection) && $connection->isConnected()) {
        $connection->close();
    }
    exit(1); 
}
