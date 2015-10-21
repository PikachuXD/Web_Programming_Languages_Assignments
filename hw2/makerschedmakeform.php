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
     <b> Schedule Name: </b> <br/>
     <input type = "text" name = "schedname" ><br/>
     <b> Dates ([mm-dd-yyyy@hh:mm,]*mm-dd-yyyy): </b> <br/>
     <textarea rows="5" cols="80" name="dates"/></textarea> <br/>
     <b> Names and Emails: ([name~email,]*name~email)</b> <br/>
     <textarea rows="5" cols="80" name="rcpt"/></textarea> <br/>
     <input type ="submit" value = "Submit" name = "submitsched">
 </form>
</body>
</html>
