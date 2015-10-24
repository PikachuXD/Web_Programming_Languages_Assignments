<!DOCTYPE html>
<?php session_start(); ?>
<html>
 <head>
  <title>In-Class Exercise 4</title>
 </head>
 <body>
<form action="process.php"
      method="POST">
<?php
	if (isset($_SESSION["error"])):
		echo $_SESSION["error"] . "</br>";
		unset($_SESSION["error"]);
	endif;
?>
<input type="text" name = "username" ></br>
<input type="text" name = "password" >
<input type="submit" name = "login" >

 </body>
</html>
