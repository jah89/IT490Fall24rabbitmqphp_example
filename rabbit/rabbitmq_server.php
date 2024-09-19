#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Simulated login function
function doLogin($username, $password) {
    // Lookup username in database
    // Check password
    // For now, return true to simulate successful login
    return true;
}

// processor function to handle different request types
function requestProcessor($request) {
    echo "received request".PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }

    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "validate_session":
            return doValidate($request['sessionId']); //define this function as needed
    }

    return array("returnCode" => '0', 'message' => "Server received request and processed");
}

// Set up the rabbit server
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "rabbitmq_server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "rabbitmq_server END".PHP_EOL;

exit();
