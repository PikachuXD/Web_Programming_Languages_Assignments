<?php
  //set up database
   $db = new mysqli("localhost", "root", "", "hw4");
   if ($db->connect_error):
      die ("Could not connect to db " . $db->connect_error);
      return "sup";
   endif;
   $guess = $_POST["guess"];
  //  echo $_POST["guess"] . " Word: " . $_POST["word"];
   $query = "select * from Words";
   $result = $db->query($query);
   $found = false;
   // roundabout way of checking membership
   while($row = $result->fetch_assoc()) {
         if ($row["word"] == $guess) {
           $found = true;
           echo "true";
      }
    }
    if (!$found) {
      echo "false";
    }
    echo "";
?>
