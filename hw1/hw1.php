<?php include 'hw1funcs.php'?>
<!DOCTYPE html>
<html>
 <head>
  <title>Homework 1</title>
 </head>
 <body>
<form action = "hw1redirect.php"
      method = "POST">
<?php
	$schedule = getSched();
	$userstimes = getUsers();
	makeTable($schedule, $userstimes);
	newButtonRow($schedule);
	getTotalTimesRow($userstimes, count($schedule));
?>


 </body>
</html>
