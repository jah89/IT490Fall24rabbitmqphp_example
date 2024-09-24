<!-- producer/sender script -->

<?php 
require '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$requestData = json_decode(file_get_contents('php://input'), true);
try {
    // Establish connection with RabbitMQ
    $connection = new AMQPStreamConnection('172.30.103.53', 5672, 'test', 'test', 'testHost');
    $channel = $connection->channel();

    // Declare queue
    $channel->queue_declare('frontend_producer_queue', false, true, false, false);

    // Create message to send
    $data = json_encode(['action' => 'query_data', 'query' => $_POST['query']]);
    $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

    // Publish message
    $channel->basic_publish($msg, '', 'frontend_producer_queue');

    echo 'Message sent to RabbitMQ!';

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

// Close channel/connection
$channel->close();
$connection->close();
?>