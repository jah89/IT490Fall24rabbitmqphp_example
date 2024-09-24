<?php


require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = $_POST['inputData'] ?? '';

    if (!empty($inputData)) {
        try {
            $rabbitMQ = new rabbitMQClient("testRabbitMQ.ini", "testServer"); 
            $rabbitMQ->publish(['data' => $inputData]); 

            echo json_encode(['status' => 'success', 'message' => 'Message sent to queue']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Input data is empty']);
    }
}
?>
