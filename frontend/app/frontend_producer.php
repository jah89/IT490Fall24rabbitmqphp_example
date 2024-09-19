<!-- producer/sender script -->

<?php 
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Hello here are some sports');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello Message'\n";

$channel->close();
$connection->close();

?>