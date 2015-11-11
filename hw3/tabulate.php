<?php
session_start();

//initialize database
$db = new mysqli("localhost", "root", "", "hw3");

if(mysqli_connect_errno()):
  die( "sorry did not connect" . $db->connect_error);
endif;

//check the type of post method
$type = $_POST["type"];
//get category id
$category_id = $_POST["category_id"];
$i = $_POST["question_id"];
$totalquestions = $_SESSION["total_questions"];

//date stuff
date_default_timezone_set("EST");
$today = getdate();
$category_id = $today["wday"] % 2;
//use this to check if the test has been taken on the given date
$fulldate =  $today["year"] . $today["mon"] . $today["mday"];
$cookiestring = "Taken" . $fulldate;

//if the thing is to load the question
if ($type == 1):
  //take the question from the database
  $query = "SELECT * FROM Questions";
  $result = $db->query($query);
  $question = "";
  $theanswer = "";
  $acount = 0;
  while($row = $result->fetch_assoc()) {
    //make sure the category id and question id match
        if ($row["Category_id"] == $category_id && $row["Question_id"] == $i) {
          $question = $row["Question"];
          $theanswer = $row["Answer"];
          $acount = $row["No_answers"];
        }
  }

  //pick up the answers too
  $query = "SELECT * FROM Answers";
  $result = $db->query($query);
  $answers = array();
  while($row = $result->fetch_assoc()) {
        if ($row["Category_id"] == $category_id && $row["Question_id"] == $i) {
          array_push($answers, $row["Answer"]);
        }
  }
  //answerstring = question$answer1@answer2@...$correctanswer
  $answerstring = implode("@", $answers);
  $answerstring = $question . "$" . $answerstring . "$" . $theanswer;

  //set the cookie for the date
  setcookie($cookiestring, 1);
  echo $answerstring;

elseif ($type == 2):
  //answer that the user picked
  $selected = $_POST["selected"];
  //take the question from the database
  $query = "SELECT * FROM Questions";
  $result = $db->query($query);
  $question = "";
  $theanswer = "";
  $acount = 0;
  $tmpcount = $_SESSION["individual_score"];
  while($row = $result->fetch_assoc()) {
        //check category id match and check question id matches previous question
        if ($row["Category_id"] == $category_id && $row["Question_id"] == $i-1) {
          $theanswer = $row["Answer"];
        }
        //make sure the category id and question id match for next q
        else if ($row["Category_id"] == $category_id && $row["Question_id"] == $i) {
          $question = $row["Question"];
          $acount = $row["No_answers"];
        }
  }

  //check on question's answer
  if ($theanswer == $selected) {
    $tmpcount += 1;
  }
  $_SESSION["individual_score"] = $tmpcount;

  //check if next question should be displayed or just end it.
  if ($i >= $totalquestions) {
    //if next question isn't to be displayed
    $aggpoints = $_SESSION["individual_score"];
    $nousers = 1;
    $tmpfulldate = $_SESSION["fulldate"];
    $tmptotalq = $_SESSION["total_questions"];

    $found = false;
    //pick out the row from averages
    $query = "SELECT * FROM Averages";
    $result = $db->query($query);
    //add individual score to aggregate
    while($row = $result->fetch_assoc()) {
          //check category id match and check question id matches previous question
          if ($row["date_string"] == $_SESSION["fulldate"]) {
            $found = true;
            $nousers += $row["no_users"];
            $aggpoints += $row["aggregatepoints"];
          }
    }
    if ($found == false) {
      //Initialize average row

      $query = "INSERT INTO Averages values('$tmpfulldate', '$tmptotalq', '$aggpoints', '$nousers')";
      $db->query($query) or die ("Invalid insert" . $db->error);
    } else {
      $query = "UPDATE Averages SET aggregatepoints = '$aggpoints' WHERE date_string = '$tmpfulldate'";
      $db->query($query) or die ("Invalid update" . $db->error);
      $query = "UPDATE Averages SET no_users = '$nousers' WHERE date_string = '$tmpfulldate'";
      $db->query($query) or die ("Invalid update" . $db->error);
    }
    //return the individual score
    $finalret = "Done@" . $_SESSION["individual_score"] . "@" . $_SESSION["total_questions"];
    echo $finalret;
  } else {

    //pick up the answers too
    $query = "SELECT * FROM Answers";
    $result = $db->query($query);
    $answers = array();
    while($row = $result->fetch_assoc()) {
          if ($row["Category_id"] == $category_id && $row["Question_id"] == $i) {
            array_push($answers, $row["Answer"]);
          }
    }
    //answerstring = question$answer1@answer2@...$correctanswer
    $answerstring = implode("@", $answers);
    $answerstring = $question . "$" . $answerstring . "$" . $theanswer;

    echo $answerstring;
  }
else:
  $answerTaken = $_POST["answerTaken"];
  $category_id = $_POST["category_id"];
  $question_id = $_POST["question_id"];
endif;

 ?>
