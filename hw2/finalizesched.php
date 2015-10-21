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
            echo "Please check which schedules you want finalized then click the submit button </br>";
            unset($_SESSION["error"]);
        endif;

     $db = new mysqli("localhost", "root", "", "hw2");
     if(mysqli_connect_errno()):
       echo "sorry did not connect";
     else:
       $currmaker = $_SESSION["maker"];
       $query = "SELECT * FROM ScheduleInfo";
       $result = $db->query($query);
       // output data of each row
       while($row = $result->fetch_assoc()) {
         if ($row["Maker"] == $currmaker) {
           //get list of schedules with titles as key and times as value for easy access
           $tmp = $row["Title"];
           echo "<input type=\"checkbox\" name=\"$tmp\" value=\"$tmp\" />";
           echo $tmp . "</br>";
         }
       }
     endif;
     ?>
     <input type="submit" name="submitFinalize" value="Submit" />
 </form>
</body>
</html>
