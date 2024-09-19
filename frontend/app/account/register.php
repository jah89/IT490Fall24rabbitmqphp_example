<?php
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> Registration Form</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/output.css">
    </head>

    <body>        
        <h2 class="text-xl font-bold">Enter Account Details</h2>

        <form id="registrationForm">
            <label for="emailAddr">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter Your E-mail" required></input>
            </br>
            <label for="passwd">Password:</label>
            <input type="password" id="passwd" name="passwd" placeholder="Enter Password" required></input>
            </br>
            <label for="confirmPasswd">Confirm Password:</label>
            <input type="password" id="confirmPasswd" name="confirmPasswd" placeholder="Enter Password Again" required></input>
            <button type="submit">Sign Up</button>
        </form>

        <div id="responseMsg"></div>

        <script src="../../js/register.js"></script>
    </body>
</html>
