<!DOCTYPE html>
<html>
 <head>
  <title>In class Exercise 2</title>
 </head>
 <body>
 <?php
	$fileptr = fopen("names.txt", "r");
	$name = $_POST["name"];
	if (flock($fileptr, LOCK_SH))
	{
		$check = 0;
		while ($currword = fgetss($fileptr, 512))
		{
			$currword = rtrim($currword);
			if ($currword == $name) 
			{
				$check = 1;
			}
		}
		flock($fileptr, LOCK_UN);
		fclose($fileptr);
		
		if ($check == 0)
		{
			echo "$name , your name has been registered";
		} else {
			echo "$name , you have previously registered";
		}
	} else {
		// error
	}
  ?>
 </body>
</html>
