<?php
//require(__DIR__ . "/../lib/nav.php");
require(__DIR__. "/../lib/safer_echo.php");
require(__DIR__."/../lib/sanitizers.php");
//reset_session();
?>

<!DOCTYPE html>
<html>
    <head>
    <script src="/../js/validation.js"></script> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> NBA Fantasy Lookup Tool</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/output.css">
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
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must only contain 3-30 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
    }
}
    ?>



<script>
document.querySelector('form').addEventListener('submit', function(event) {
  event.preventDefault();  // Prevent traditional form submission

  let email = document.querySelector('input[name="email"]').value;
  let password = document.querySelector('input[name="password"]').value;

  // Client-side validation happens here
  if (!validateForm(this)) {
    return; // Stop if validation fails
  }

  // Send data to PHP backend
  const formData = new URLSearchParams();
  formData.append('email', email);
  formData.append('password', password);

  fetch('../rabbit/send_to_queue.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    // Handle the success or error response from the PHP server
    const statusMessage = document.getElementById('statusMessage');
    if (data.status === 'success') {
      statusMessage.innerText = 'Data sent to queue successfully';
    } else {
      statusMessage.innerText = 'Error: ' + data.message;
    }
  })
  .catch((error) => {
    console.error('Error:', error);
    document.getElementById('statusMessage').innerText = 'An error occurred while sending the data';
  });
});

</script>
</html>
        
        <!-- Backend/DB logic
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (Exception $e) {
            flash("There was an error with registration, please try again.", "danger");
            users_check_duplicate($e->errorInfo);
        } -->
<?php
//require(__DIR__ . "/../../partials/flash.php");
?>