<!DOCTYPE html>
<html>
<head>
	<?php
	if (isset($_POST['LName'])) {
		$LName = $_POST['LName'];
		$FName = $_POST['FName'];

		$db = new mysqli("localhost", "root", "", "cs4501");

		if(mysqli_connect_errno()) {
			echo "sorry did not connect";
		}
		else {
			$stmt = $db->stmt_init();
			if ($stmt->prepare("SELECT LName, FName FROM Students WHERE 
					LName = ? AND FName = ?")) {
				mysqli_stmt_bind_param($stmt, "ss", $LName, $FName);
				$stmt->execute();
			    $stmt->bind_result($tempL, $tempF);
			    $stmt->store_result();  
			    if ($stmt->num_rows == 1) {
			    	echo "you are already entered";
			    }
			    else {
			    	$sql = "INSERT INTO Students (LName, FName) VALUES
			    		('$LName', '$FName')";
			    	if (!mysqli_query($db, $sql)) {
			    		echo "could not insert";
			    	}
			    }
			}
			$stmt = $db->stmt_init();
			if ($stmt->prepare("SELECT LName, FName FROM Students")) {
				$stmt->execute();
			    $stmt->bind_result($tempL, $tempF);
			    $stmt->store_result();  
			    echo "<br/>";
			  	while ($stmt->fetch()) {
			  		echo $tempL . "        " . $tempF . "<br/>";
			  	}
			    
			}
			
		}
	}
?>
</head>
<body>
	<form 	action = "form.php"
			method = "POST">

			<input type = "text" name = "LName" >
			<br/>
			<input type ="text" name ="FName" >
			<input type ="submit" value = "submit">
	</form>
</body>
</html>