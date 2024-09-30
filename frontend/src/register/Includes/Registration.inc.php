<?php
//require(__DIR__ . "/../lib/nav.php");
require(__DIR__. "/../../lib/sanitizers.php");
include(__DIR__."/../../rabbit/rabbitMQLib.inc.php");

abstract class Registration {

    /**
    * Displays main login page.
    * @return void
    */
    public static function displayRegistration() {
        self::handleRegistration();
?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
            <?php include(__DIR__.'/../../lib/components/Head.inc.php');
            echo \NBA\Frontend\Lib\Components\Head::displayHead(); ?> 
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
    </html>

    <?php
    } //end of displayRegistration()

    private static function handleRegistration() {
        try{
            if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
                $email = filter_input(INPUT_POST,'email');
                $password = filter_input(INPUT_POST,'password');
                $confirm = filter_input(INPUT_POST,'confirmPassword');
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
                    echo "empty password";
                    $hasError = true;
                }
                if (empty($confirm)) {
                    echo ("empty confirm");
                    $hasError = true;
                }
                if (!is_valid_password($password)) {
                    echo ("invalid pass");
                    $hasError = true;
                }
                if ((strlen($password) > 0) && ($password !== $confirm)) {
                    echo ("password and confirm must match");
                    $hasError = true;
                }
                if (!$hasError) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    //echo $password;
                    //echo $hashedPassword;
        
                    $json_message = json_encode(['username' => $email, 'password' => $hashedPassword]);
                    $client = new rabbitMQClient("host.ini", "testServer");
                    print_r($json_message);
                    if($client->publish($json_message)) {
                    echo "Message published successfuly:  $json_message";
                    } else {
                    echo "Failed to publish message: $json_message";
                    }
                }
            } 
        } catch (Exception $e){
            echo ('Error processing registration'.$e->getMessage());
        }
    }
}