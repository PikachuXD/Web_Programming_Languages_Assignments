<?php
session_start();
//make a new sql database
$db = new mysqli("localhost", "root", "", "hw3");

//check for errors
if(mysqli_connect_errno()) {
  echo "sorry did not connect";
}
else {
  //initializing the tables
  $db->query("drop table Category");
  $db->query("drop table Questions");
  $db->query("drop table Answers");
  $db->query("drop table Averages");

  //makers table with name, email, password
  $query = "CREATE TABLE IF NOT EXISTS Category (
        Category_id     int not null,
        Category_name 		varchar(50) not null,
        No_questions    int not null
      )";
  $db->query($query) or die("Invalid table" . $db->error);

  //category id binds questions to categories
  $query = "CREATE TABLE IF NOT EXISTS Questions (
    Category_id int not null,
    Question_id int not null,
    Question varchar(1000) not null,
    No_answers int not null,
    Answer varchar(1000) not null
  )";
  $db->query($query) or die ("Invalid table" . $db->error);

  $query = "CREATE TABLE IF NOT EXISTS Answers (
    Category_id int not null,
    Question_id int not null,
    Answer int not null
  )";
  $db->query($query) or die ("Invalid table" . $db->error);

  $query = "CREATE TABLE IF NOT EXISTS Averages (
    date_string varchar(90),
    totalpoints int not null,
    aggregatepoints int not null,
    no_users  int not null
  )";
  $db->query($query) or die ("Invalid table" . $db->error);

  //quiz file
  $quizfile = file_get_contents("quizzes.txt");
  //categories separated by @
  $categories = explode("@", strip_tags($quizfile));
  //var_dump($categories);

  //category id
  $cid = 0;
  foreach ($categories as $category):
    //category name ~ rest
    $catnameandqs = explode("~", strip_tags($category));
    //Question ? Question ?
    $theqs = explode("?", rtrim(strip_tags($catnameandqs[1])));
    //insert category name with category id

    //count the no of questions in one category
    $qid = 0;

    //loop through each question
    foreach ($theqs as $oneq):
      //question = question*answers
      $splitqanda = explode("*", rtrim(strip_tags($oneq)));
      $thisq = strip_tags($splitqanda[0]);
      if (count($splitqanda) == 2) {
        //answers = blah&blah&blah&blah^correctblah
        $ansandcorrans = explode("^", rtrim(strip_tags($splitqanda[1])));
        $qans = rtrim(strip_tags($ansandcorrans[1]));

        //question with answer
        //var_dump($ansandcorrans);
        $answers = explode("&", strip_tags($ansandcorrans[0]));
        $acount = 0;
        foreach ($answers as $oneanswer):

          //var_dump($oneanswer);
          $oneans = strip_tags($oneanswer);
          $query = "INSERT INTO Answers values('$cid', '$qid', '$oneans')";
          $db->query($query) or die ("Invalid insert" . $db->error);
          $acount++;
        endforeach;
        $query = "INSERT INTO Questions values('$cid', '$qid', '$thisq', '$acount', '$qans')";
        $db->query($query) or die ("Invalid insert" . $db->error);
        $qid += 1;
      }
    endforeach;
    $query = "INSERT INTO Category values ('$cid', '$catnameandqs[0]', '$qid')";
    $db->query($query) or die ("Invalid insert" . $db->error);
    $cid += 1;
  endforeach;

  $newURL = "quizpage.php";
  header('Location: '.$newURL);

  }
 ?>
