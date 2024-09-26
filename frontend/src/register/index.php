<?php
//require(__DIR__ . "/../lib/nav.php");
require(__DIR__. "/../../lib/safer_echo.php");
require(__DIR__. "/../../lib/sanitizers.php");

$directory = __DIR__.'/../../../rabbit'; // Path to your directory

// Get all PHP files from the directory
$files = scandir($directory);

// Loop through the files
foreach ($files as $file) {
    // Check if the file is a PHP file
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php' || pathinfo($file, PATHINFO_EXTENSION) == 'inc') {
        require_once "$directory/$file"; // Require the file
    }
}
//reset_session();
?>

<!DOCTYPE html>
<html>
    <head>
    <script src="/js/validation.js"></script> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> NBA Fantasy Lookup Tool</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/output.css">
    </head>
<body>
    <form id="registerForm" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" name="confirmPassword" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>

<div id="statusMessage"></div>
</body>

<?php
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST,"confirmPassword", "", false);

    $hasError = false;
    
    if (empty($email)) {
        $hasError = true;
    }
    
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo ("Bad email");
        $hasError = true;
    }
    if (empty($password)) {
        echo "Bad password";
        $hasError = true;
    }
    if (empty($confirm)) {
        echo ("Bad confirm");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        echo ("invalid pass");
        $hasError = true;
    }
    if ((strlen($password) > 0) && ($password !== $confirmPassword)) {
        $hasError = true;
    }
    if (!$hasError) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $message = [
          'email' => $email,
          'password' => $hashedPassword
        ];
        echo ($message);
        $client = new rabbitMQClient("host.ini", "testServer");
        if($client->publish($message)) {
          echo "Message published successfuly:  $message";
        } else {
          echo "Failed to publish message: $message";
        }
    } 
}
    ?>
</html>