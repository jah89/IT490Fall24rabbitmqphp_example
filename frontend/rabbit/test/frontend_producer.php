<!-- test producer/sender script -->

<?php 
require '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$requestData = json_decode(file_get_contents('php://input'), true);

// Establish connection with RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'username', 'password');
$channel = $connection->channel();

// Declare queue
$channel->queue_declare('frontend_producer_queue', false, true, false, false);

// Create message to send
$data = json_encode(['action' => 'query_data', 'query' => $_POST['query']]);
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// Publish message
$channel->basic_publish($msg, '', 'frontend_producer_queue');

echo 'Message sent to RabbitMQ!';

// Close channel/connection
$channel->close();
$connection->close();
?>