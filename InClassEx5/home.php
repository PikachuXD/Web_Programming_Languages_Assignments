<?php
	$db = new mysqli("localhost", "root", "", "cs4501");

	if(mysqli_connect_errno()) {
		echo "sorry did not connect";
	}
	else {
		$sql = "CREATE TABLE Students (
					LName 		varchar(50),
					FName 		varchar(50)
				)";
		if (!mysqli_query($db, $sql)) {
			echo "did not create";
		}
		else {
			echo "created";
			$newURL = 'form.php';
			header('Location: '.$newURL);
		}
		
	}

?>