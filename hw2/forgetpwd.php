<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  Homework 2
</head>

<body>
 <form action = "process.php"
     method = "POST">
     <?php
        //check if there's an error with login
        if (isset($_SESSION["error"])):
            echo $_SESSION["error"] . "</br>";
            unset($_SESSION["error"]);
        endif;
     ?>
     <b> Email: </b> <br/>
     <input type = "text" name = "fpemail" >
     <br/>
     <input type ="submit" value = "Submit" name = "sendpwd">
</form>
</body>
</html>
