#!/usr/bin/php

<?php

require_once('rabbitMQLib.inc'); 

$host = '172.30.17.239';  
$user = 'miz';           
$password = 'teamfantasy'; 
$dbname = 'login_auth';      

$mydb = new mysqli($host, $user, $password, $dbname);

if ($mydb->connect_error) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(0);
}

echo "Successfully connected to database." . PHP_EOL;


$sql = "SELECT email, hashed_password FROM users";
$result = $mydb->query($sql);

// Check if there are results
if ($result && $result->num_rows > 0) {
   
    $rabbitMQ = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    
    // gets each row from db and then publishs the data
    while ($row = $result->fetch_assoc()) {
        $data = [
            'email' => $row['email'],
            'hashed_password' => $row['hashed_password']
        ];
        
      
        $response = $rabbitMQ->publish($data);
        
        // checks if message was published successfully
        if ($response) {
            echo "Message published for email: " . $row['email'] . PHP_EOL;
        } else {
            echo "Failed to publish message for email: " . $row['email'] . PHP_EOL;
        }
    }
} else {
    echo "No results found." . PHP_EOL;
}


$mydb->close();

echo "Script execution completed." . PHP_EOL;
?>


