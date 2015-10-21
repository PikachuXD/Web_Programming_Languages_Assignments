<?php
  session_start();
?>
<!DOCTYPE html>
<html>
<head><title>Homework 2</title></head>

<body>
 <form action = "process.php"
     method = "POST">
     <?php
        if (isset($_SESSION["maker"])):
            echo "Welcome " . $_SESSION["maker"] . "!</br>";
            unset($_SESSION["error"]);
        endif;
     ?>
     <input type = "submit" name = "makeasched" value = "Make a Schedule" ><br/>
     <input type ="submit" name = "finalizesched" value = "Finalize a schedule"><br/>
     <input type = "submit" name = "logoutbtn" value = "Log out" ><br/>
 </form>
</body>
</html>
