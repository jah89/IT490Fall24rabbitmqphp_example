<?php
require(__DIR__."/../lib/nav.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> NBA Fantasy Lookup Tool</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/output.css">
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <h1 class="text-3xl font-bold underline">hello there sports fans, how is your day today?</h1>
        <script src="" async defer></script>
        <h2 class="text-xl font-bold">Don't have an account?</h2>
        <a href="/account/register.php"> Sign Up</a>
    </body>
</html>


<?php
require(__DIR__ . "/lib/nav.php");
?>
<script src="../js/validation.js"></script>
<form onsubmit="return validateForm(this)" method="POST">
    <div>
        <label for="email">Email/Username</label>
        <input type="text" name="email" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    
    <input type="submit" value="Login" />
</form>

<?php
if(isset($_POST["email"]) && isset($_POST["password"])){
    $email = se($_POST,"email", "", false);
    $password = se($_POST,"password", "", false);

    $hasError = false;
    if(empty($email)){
        flash("Email must be provided");
        $hasError = true;
    }
    if (str_contains($email, "@")) {
        //sanitize
        $email = sanitize_email($email);
        //validate
        if (!is_valid_email($email)) {
            flash("Invalid email address");
            $hasError = true;
        }
    } else {
        if (!is_valid_username($email)) {
            flash("Invalid username");
            $hasError = true;
        }
    }
    if(empty($password)){
        flash("Password must be provided");
        $hasError = true;
    }
    if(!is_valid_password($password)) {
        flash("Password must be at least 8 characters long ");
        $hasError = true;
    }
    if(!$hasError){
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users where email = :email or username = :email");
        try{
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {                        
                        $_SESSION["user"] = $user;
                        try {
                            //lookup potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        } catch (Exception $e) {
                            error_log(var_export($e, true));
                        }
                        //save roles or empty array
                        if (isset($roles)) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        flash("Welcome, " . get_username());
                        die(header("Location: home.php"));
                    } else{
                        flash("Invalid Password");
                    }
                }   else {
                        flash("Email not found.");
                    }
                }
            } catch (Exception $e){
                flash("<pre>" . var_export($e, true) . "</pre>");
            }
    }
}
?>
<?php 
require(__DIR__ . "/../../partials/flash.php");
?>