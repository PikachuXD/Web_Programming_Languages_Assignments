<?php
session_start();
ini_set("max_execution_time", 0);
//make a new sql database
$db = new mysqli("localhost", "root", "", "hw4");

//check for errors
if(mysqli_connect_errno()) {
  echo "sorry did not connect";
}
else {
  //initializing the tables
  $db->query("drop table Words");
  $query = "CREATE TABLE IF NOT EXISTS Words (
        id int primary key not null auto_increment,
        word 		varchar(50) not null
  )";
  $db->query($query) or die("Invalid table" . $db->error);

  $wordsfile = file_get_contents("dictionary.txt");
  $indivWords = explode("\n", $wordsfile);
  //$db->query("INSERT INTO Words values (NULL, '$indivWords[0]'), (NULL, '$indivWords[1]')") or die ("Invalid insert" . $db->error);

  //had to make two batch queries because of sql timeout
  //making the batch query
  $query = "INSERT INTO Words values ";
  //for ($i = 0; $i < 10; $i++) {
  for ($i = 0; $i < count($indivWords) / 2 - 1; $i++ ) {
    $tmp = strip_tags(rtrim($indivWords[$i]));
    $query .= "(NULL, '$tmp'), ";
  }
  //accounting for last insert
  $finalWordi = count($indivWords) / 2;
  $fWord = strip_tags(rtrim($indivWords[$finalWordi]));
  //echo $fWord;
  $query .= "(NULL, '$fWord')";
  //echo $query;
  $db->query($query) or die ("Invalid insert" . $db->error);

  $query = "INSERT INTO Words values ";
  for ($i = $finalWordi + 1; $i < count($indivWords) - 2; $i++ ) {
    $tmp = strip_tags(rtrim($indivWords[$i]));
    $query .= "(NULL, '$tmp'), ";
  }
  //accounting for last insert
  $finalWordi = count($indivWords) - 2;
  $fWord = strip_tags(rtrim($indivWords[$finalWordi]));
  $query .= "(NULL, '$fWord')";
  //echo $query;
  $db->query($query) or die ("Invalid insert" . $db->error);

  //move to the main page
  $newURL = "home.php";
  echo count($indivWords);
  //var_dump($indivWords);
  header('Location: '.$newURL);
  }
 ?>
