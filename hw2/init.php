<?php
session_start();
//make a new sql database
$db = new mysqli("localhost", "root", "", "hw2");

//check for errors
if(mysqli_connect_errno()) {
  echo "sorry did not connect";
}
else {
  //initializing the tables
  $db->query("drop table Makers");
  $db->query("drop table Users");
  $db->query("drop table ScheduleInfo");

  //makers table with name, email, password
  $query = "CREATE TABLE IF NOT EXISTS Makers (
        Name 		varchar(50) not null,
        Email 		varchar(50) not null,
        Password  varchar(50) not null,
        Maker_id int primary key not null
      )";
  $db->query($query) or die("Invalid table" . $db->error);

  //sample makers
  $query = "INSERT INTO Makers values ('purasle', 'purasle@gmail.com', 'sample1', '0')";
  $db->query($query) or die("Invalid insert" . $db->error);
  $query = "INSERT INTO Makers values ('rk5dy', 'rk5dy@virginia.edu', 'sample2', '1')";
  $db->query($query) or die("Invalid insert" . $db->error);

  //users table with email and name info for access for schedules
  $query = "CREATE TABLE IF NOT EXISTS Users (
        Name  varchar(50),
        Email varchar(50),
        sched_id  int not null,
        slotspicked varchar(50)
  )";
  $db->query($query) or die("Invalid table" . $db->error);

  //schedules information with scheduleid, title, maker and timeslots
  $query = "CREATE TABLE IF NOT EXISTS ScheduleInfo (
      sched_id  int not null,
      Title     varchar(50),
      Maker     varchar(50),
      Times      varchar(10000)
  )";
  $db->query($query) or die("Invalid table" . $db -> error);

  $newURL = 'login.php';
  header('Location: '.$newURL);
}
 ?>
