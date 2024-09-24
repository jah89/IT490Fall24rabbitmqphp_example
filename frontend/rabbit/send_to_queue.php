<?php
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the email and password from the POST request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate that email and password are not empty
    if (!empty($email) && !empty($password)) {
        try {
            //hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $rabbitMQ = new rabbitMQClient("testRabbitMQ.ini", "testServer"); 
            // Send email and password as data to RabbitMQ
            $rabbitMQ->publish(['email' => $email, 'password' => $hashedPassword]);

            echo json_encode(['status' => 'success', 'message' => 'Message sent to queue']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email or password is missing']);
    }
}
?>
