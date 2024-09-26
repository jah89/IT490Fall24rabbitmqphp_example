<?php
//require(__DIR__ . "/../lib/nav.php");
require(__DIR__. "/../../lib/safer_echo.php");
require(__DIR__. "/../../lib/sanitizers.php");

$directory = __DIR__.'/../../rabbit'; // Path to your directory

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
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> NBA Fantasy Lookup Tool</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/output.css">
    </head>
    <body>
        <h1 class="text-3xl font-bold underline">hello there sports fans, how is your day today?</h1>

        <form  id="loginForm" method="POST">
            <div>
                <label for="email">Email Address</label>
                <input type="text" name="email" required />
            </div>
            <div>
                <label for="pw">Password</label>
                <input type="password" id="pw" name="password" required minlength="8" />
            </div>
            
            <input type="submit" value="Login" />
        </form>
        <div id="statusMessage"></div>

        <h2 class="text-xl font-bold">Don't have an account?</h2>
        <a href="../register/"> Sign Up</a>
    </body>
</html>
<?php
  if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
      $email = se($_POST, "email", "", false);
      $password = se($_POST, "password", "", false);

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
