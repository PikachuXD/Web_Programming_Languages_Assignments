<?php
	session_start();
	//list of basic functions that is required at every step	
	//global variables
	$globalediteduser = '';
	$editclicked = false;
	$newclicked = false;
	//making the header part of the table
	function getSched() {
		$times = array();
		$schedfile = file("schedule.txt");
		foreach($schedfile as $currline):
			$currsched = strip_tags($currline);
			//date and time are split by ^
			$splitdatetime = explode("^", $currsched);
			//dates are split by -
			$splitdate = explode("-", $splitdatetime[0]);
			//times are split by |
			$splittimes = explode("|", $splitdatetime[1]);
			//iterate through the times of the date
			foreach ($splittimes as $onetime):
				$hrmin = explode(":", $onetime);
				//date is in year-month-day
				//minutes have to be rtrim'd because of the issue of endline stuff
				$d = mktime($hrmin[0], rtrim($hrmin[1]), 0, $splitdate[1], $splitdate[2], $splitdate[0]);
				//using the mktime thing to format the headers
				array_push($times, date("l \n m/d/Y \n h:i a", $d));
			endforeach;
		endforeach;
		return $times; 
	}
	
	//getting the userfile
	function getUsers() {
		$users = array();
		//deal with users.txt		
		if (file_exists("users.txt")):
			$fileptr = fopen("users.txt", "r");
			if (flock($fileptr, LOCK_EX)):
				//check each user line
				while ($curruser = fgetss($fileptr, 512)):
					//user = thing before ^, or splitusertime[0]
					//times = comes after ^, or splitusertime[1]
					//times split by |
					$splitusertime = explode("^", $curruser);
					$splittimes = explode("|", rtrim($splitusertime[1]));
					$users[$splitusertime[0]] = $splittimes; 	
				endwhile;
			endif;
			flock($fileptr, LOCK_UN);
		endif;
		//users are in username => list of times pair
		return $users;
	}
	
	//take in a dynamic userset, a static schedule of times and makes the table
	//fairly straightforward
	function makeTable($schedtimes, $users) {
		echo "<table border = \"1\" id=\"table\">";
		echo "<caption><h2> Select your Meeting Times </h2></caption>";
		echo "<tr align = \"center\">";
		echo "<th> User </th>";
		echo "<th> Action</th>";
		foreach ($schedtimes as $onetime):
			echo "<th> . $onetime . </th>";	
		endforeach;	
		echo "</tr>";
		
		foreach ($users as $oneuser => $histimes):
			echo "<tr align = \" center \">";
			echo "<td>" . $oneuser . "</td>";
			if (isset($_COOKIE["user"][$oneuser])):
				echo "<td> <input type=\"submit\" name = \"edit[$oneuser]\" value = \"Edit\" > </td>";
			else:
				echo "<td> </td>";
			endif;
			$tmpctr = 0;
			while ($tmpctr < count($schedtimes)):
				if (in_array($tmpctr, $histimes)):
					echo "<td>Y</td>";
				else:
					echo "<td> </td>";
				endif;
				$tmpctr++;
			endwhile;
		endforeach;

	 
	}

	//the row with all the totals
	//takes in users and the number of times as ctr
	//users is changeable so the parameter has to be there
	function getTotalTimesRow($users, $ctr) {
		//start of the row
		echo "<tr align = \" center \">";
		echo "<td> Total:</td>";
		echo "<td></td>";
		//initialize an array of zeros
		$totalctr = array();
		$tmpctr = 0;
		while ($tmpctr < $ctr):
			array_push($totalctr, 0);
			$tmpctr++;
		endwhile;
		//iterate through each user's available times and add to the counter array
		foreach ($users as $oneuser => $histimes):
			foreach ($histimes as $onetime):
				$totalctr[$onetime]++;
			endforeach;
		endforeach;
		//display the totals
		foreach ($totalctr as $i => $onectr):
			echo "<td>" . $onectr . "</td>";
		endforeach;
		//end the row
		echo "</tr>";
		echo "</table>";
	}
	
	//the row with the new button	
	function newButtonRow($schedtimes) {
		echo "<tr id=\"newbtnrow\">";
		echo "<td></td>";
		echo "<td><input type=\"submit\" name = \"new\" value = \"New\" ></td>";
		foreach ($schedtimes as $schedtime):
			echo "<td></td>";
		endforeach;
	}
	
	//the editable fields row when just a new button is clicked
	function editableFieldsRow($schedtimes) {
		//goal of the counter is to give each of the checkboxes a unique name
		//this allows for easier iteration through the checkboxes
		$tmp = 0;
		echo "<tr id=\"submittedrow\">";
		echo "<td><input type = \"text\" name = \"newuser\" size = \"30\" maxlength = \"30\"></td>";
		echo "<td><input type=\"submit\" name = \"Submit\" value = \"Submit\"></td>";
		foreach ($schedtimes as $schedtime):
			echo "<td><input type=\"checkbox\" name=\"newtime[] \" value=\"". $tmp . "\" /></td>";
			$tmp++;
		endforeach;
	}

	//this sets the cookie after the submit button
	function aftersubmittingnew() {
		if (isset($_POST["newuser"]) && isset($_POST['newtime'])):
			$user = $_POST["newuser"];
			$listtimesavailable = $_POST['newtime'];
			$timesavailable = implode("|", $listtimesavailable);
			setcookie("user[$user]", $timesavailable);
			$fileptr = fopen("users.txt", "a");
			if (flock($fileptr, LOCK_EX)): 
				fwrite($fileptr, "$user^$timesavailable" . PHP_EOL);
			endif;
			flock($fileptr, LOCK_UN);
		endif;
		return false;
	}
	
	//find the user name of the row to be edited
	function findEditBtnClicked() {
		foreach ($_POST as $key => $value):
			if (isset($_POST[$key])):
				return key($value);	
			endif;	
		endforeach;
	}
		
	//this is the table after clicking the edit button
	function makeEditableTable($rowToChange, $schedtimes, $users) {
		echo "<table border = \"1\" id=\"table\">";
		echo "<caption><h2> Select your Meeting Times </h2></caption>";
		echo "<tr align = \"center\">";
		echo "<th> User </th>";
		echo "<th> Action</th>";
		foreach ($schedtimes as $onetime):
			echo "<th> . $onetime . </th>";	
		endforeach;	
		echo "</tr>";
		
		foreach ($users as $oneuser => $histimes):
			echo "<tr align = \" center \" id =\"submittedrow\" >";
			if ($rowToChange == $oneuser):
				$tmp=0;
				echo "<td><input type = \"text\" name = \"editeduser[$oneuser]\" size = \"30\" maxlength = \"30\" value=\"$oneuser\"></td>";
				echo "<td><input type=\"submit\" name = \"Submit\" value = \"Submit\"></td>";
				foreach ($schedtimes as $schedtime):
					if (!in_array($tmp, $histimes)): 
						echo "<td><input type=\"checkbox\" name=\"editedtime[] \" value=\"". $tmp . "\" /></td>";
					else:
						echo "<td><input type=\"checkbox\" name=\"editedtime[] \" value=\"". $tmp . "\" checked /></td>";
					endif;
					$tmp++;
				endforeach;	
			else:
				echo "<td>" . $oneuser . "</td>";
				if (isset($_COOKIE["user"][$oneuser])):
					echo "<td> <input type=\"submit\" name = \"$oneuser\" value = \"Edit\" formaction = \"hw1editpressed.php\" formmethod = \"POST\"> </td>";
				else:
					echo "<td> </td>";
				endif;
				$tmpctr = 0;
				while ($tmpctr < count($schedtimes)):
					if (in_array($tmpctr, $histimes)):
						echo "<td>Y</td>";
					else:
						echo "<td> </td>";
					endif;
					$tmpctr++;
				endwhile;
			endif;
		endforeach;
		
	}
	
	//this is what happens after submitting the edit row field
	function aftersubmittingedit() {
		if (isset($_POST["editeduser"]) && isset($_POST['editedtime'])):
			$user = '';
			$newuser = '';
			//the key of the post value is the old user
			//the value of this is the new user
			foreach ($_POST["editeduser"] as $key => $value):
				$user = $key;
				$newuser = $value;	
			endforeach;
			$listtimesavailable = $_POST['editedtime'];
			$timesavailable = implode("|", $listtimesavailable);
			setcookie("user[$newuser]", $timesavailable);
			$getListOfUsers = getUsers();
			$newFile = '';
			foreach ($getListOfUsers as $oneuser => $oneuseravailabletimes):
				//echo "Iteratin'" . $oneuser . PHP_EOL;
				//echo "Sup " . $user . PHP_EOL;
				if ($oneuser == $user):
					$newFile .= $newuser . "^" . $timesavailable . PHP_EOL;
				else:
					$tmpavtime = implode("|", $oneuseravailabletimes);
					$newFile .= $oneuser . "^" . $tmpavtime . PHP_EOL;
				endif;
			endforeach;
			//var_dump($newFile);
			$fileptr = fopen("users.txt", "w+");
			if (flock($fileptr, LOCK_EX)):
				fwrite($fileptr, $newFile);
			endif;
			flock($fileptr, LOCK_UN);
			setcookie("user[$user]", null, time() -3600);
			return false;	
		endif;
		
	}
?>
