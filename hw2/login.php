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
     <input type = "text" name = "email" >
     <br/>
     <b> Password: </b> <br/>
     <input type ="text" name ="password" > <br/>
     <input type ="submit" value = "Submit" name = "login"> <br/>
     <input type ="submit" value = "Forgot Password?" name ="forgetpwd">
 </form>
</body>
</html>
