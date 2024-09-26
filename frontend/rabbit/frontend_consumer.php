<!--consumer/listener script-->
<?php
require __DIR__.'/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('front_consumer_queue', false, true, false, false);

echo " [*] Waiting for messages.  To exit press CTRL+C\n";

$callback = function ($msg) use ($channel){
    $data = json_decode($msg->body, true);

    //switch to handle different use cases
    switch ($data['action']) {
        case 'register':
            handleRegistration($data);
            break;

        case 'login':
            handleLogin($data);
            break;
           
        //case 'changePassword'???
        //TODO: create function and relevant logic
            //handleChangePasswd($data);
            //break;

        default:
        error_log("unknown action: " . $data['action']);
        break;
    }

    $msg->ack();
};

function handleRegistration($data) {
    //TODO: add logic here
    error_log("Handling registration for user with email: " . $data['emailAddr']);
}

function handleLogin($data) {
    //TODO: add logic here
    error_log("Handling login of user with email: " . $data['emailAddr']);
}

$channel->basic_consume('front_consumer_queue', 'front', false, true, false, false, $callback);

try {
    while ($channel->is_consuming()){
        $channel->wait();
    }
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
?>