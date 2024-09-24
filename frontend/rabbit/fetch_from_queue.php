<?php
require_once('rabbitTESTLib.inc'); // RabbitMQ client class

try {
    $rabbitMQ = new rabbitMQClient();  
    $response = $rabbitMQ->consume();

    if ($response) {
        echo json_encode(['status' => 'success', 'data' => $response]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No messages in queue']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
