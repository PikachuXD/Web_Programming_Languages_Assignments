<?php include 'hw1funcs.php'?>
<!DOCTYPE html>
<html>
 <head>
  <title>Homework 1</title>
 </head>
 <body>
<form action = "hw1cookiedirect.php"
      method = "POST">
<?php
	$schedule = getSched();
	$userstimes = getUsers();
	if (isset($_POST['edit'])):
		$rowToChange = findEditBtnClicked();
		makeEditableTable($rowToChange, $schedule, $userstimes);
		newButtonRow($schedule);
	endif;

	if (isset($_POST['new'])):
		makeTable($schedule, $userstimes);
		editableFieldsRow($schedule);
	endif;
	getTotalTimesRow($userstimes, count($schedule));
?>


 </body>
</html>
