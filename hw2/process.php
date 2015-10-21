<?php
session_start();
//checks the login related stuff
if (isset($_POST['login'])) {
      //set local variables for email and password from forms
      $email = $_POST['email'];
      $password = $_POST['password'];

      //set up database again
      $db = new mysqli("localhost", "root", "", "hw2");
      if(mysqli_connect_errno()) {
  		    echo "sorry did not connect";
  		}
      else {
          //pick out the maker from database
          $query = "SELECT * FROM Makers";
          $result = $db->query($query);
          $found = false;
          $makername = "";
          // output data of each row
          while($row = $result->fetch_assoc()) {
                if ($row["Email"] == $email && $row["Password"] == $password) {
                  $found = true;
                  $makername = $row["Name"];
                }
            }
          //straightforward. if maker found go to the makerschedule form
          //if not, go back to the login page
          if ($found):
            $_SESSION["maker"] = $makername;
            header('Location: makerloggedon.php');
          else:
            $_SESSION["error"] = "Account not found! Please try again";
            header('Location: login.php');
          endif;
      }
}

//forgetpassword redirect
if (isset($_POST["forgetpwd"])):
  header('Location: forgetpwd.php');
endif;

//initial mail setup related stuff
$mailpath = 'C:\xampp\htdocs\PHPMailer-master';
$path = get_include_path();
set_include_path($path . PATH_SEPARATOR . $mailpath);
require 'PHPMailerAutoload.php';

//send the password
if (isset($_POST["sendpwd"])):
  $mail = new PHPMailer();

  $mail->IsSMTP(); // telling the class to use SMTP
  $mail->SMTPAuth = true; // enable SMTP authentication
  $mail->SMTPSecure = "tls"; // sets tls authentication
  $mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
  $mail->Port = 587; // set the SMTP port for GMAIL server; or your email server port
  $mail->Username = "cs4501fall15@gmail.com"; // email username
  $mail->Password = "UVACSROCKS"; // email password

  $receiver = strip_tags($_POST["fpemail"]);
  $subj = "Homework2: Your password is";
  $msg = "";
  $db = new mysqli("localhost", "root", "", "hw2");
  if(mysqli_connect_errno()) {
      echo "sorry did not connect";
  }
  else {
      //pick out the maker from database
      $query = "SELECT * FROM Makers";
      $result = $db->query($query);
      $found = false;
      // output data of each row
      while($row = $result->fetch_assoc()) {
            if ($row["Email"] == $receiver) {
              $msg = $row["Password"];
              $found = true;
            }
        }
      //straightforward. if maker found go to the makerschedule form
      //if not, go back to the login page
      if ($found):
        $_SESSION["error"] = "Your password was sent to your email!";
        header('Location: login.php');
      else:
        $_SESSION["error"] = "Account not found! Please try again";
        header('Location: forgetpwd.php');
      endif;
  }
  $mail->addAddress($receiver);
  $mail->SetFrom($sender);
  $mail->Subject = "$subj";
  $mail->Body = "$msg";
  if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
   }
   else { echo 'Message has been sent'; }
endif;

//if user clicks on make a schedule
if (isset($_POST['makeasched'])):
  header("Location: makerschedmakeform.php");
endif;

if (isset($_POST['logoutbtn'])):
  unset($_SESSION["maker"]);
  header("Location: login.php");

endif;

//add schedule
if (isset($_POST['submitsched'])):
  //setting up database
  $db = new mysqli("localhost", "root", "", "hw2");
  if(mysqli_connect_errno()):
      echo "sorry did not connect";
  else:
      //take info from the forms
      $name = $_POST['schedname'];
      $dates = $_POST['dates'];
      $recipients = $_POST['rcpt'];
      $mker = $_SESSION["maker"];

      //schedule id will just be the number of rows of the table prior to adding the schedule
      $query = "SELECT * FROM ScheduleInfo";
      $result = $db->query($query);
      $schedid = $result->num_rows;
      //this just makes it easier to parse later on
      $dates = strip_tags($dates);

      //getting rid of the stupid escapable stuff
      $datearray = explode(",", $dates);
      $dates2 = "";
      foreach($datearray as $key => $onedate):
        $tmp = explode("@", strip_tags($onedate));
        $tmpdate = strip_tags($tmp[0]);
        $tmptimes = explode("|", strip_tags($tmp[1]));
        $tmpymd = explode("-", $tmpdate);
        $tmpdate = implode("a", $tmpymd);
        foreach ($tmptimes as $onetime):
          $tmphrmin = explode(":", $onetime);
          $tmponetime = implode("b", $tmphrmin);
          if ($dates2 == "") {
            $dates2 = $tmpdate . "d" . $tmponetime;
          } else {
            $dates2 .= "t" . $tmpdate . "d" . $tmponetime;
          }
        endforeach;
      endforeach;

      echo $dates2;
      //queries the stuff into database
      $query = "INSERT INTO ScheduleInfo values ('$schedid', '$name', '$mker', '$dates2')";
      //echo "$mker";
      $db->query($query) or die ("Invalid insert" . $db->error);

      //recipient/user related stuff
      $recipients = strip_tags($recipients);
      $listofrecipients = explode(",", $recipients);

      //one phpmailer/recipient
      foreach($listofrecipients as $onercpt):
        $mail = new PHPMailer();
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->SMTPSecure = "tls"; // sets tls authentication
        $mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
        $mail->Port = 587; // set the SMTP port for GMAIL server; or your email server port
        $mail->Username = "purasle@gmail.com"; // email username
        $mail->Password = "kuroyuki12"; // email password
        $mail->SetFrom("purasle@gmail.com");

        //onercpt = name~emailaddress
        $tmp = explode("~", $onercpt);
        $name = strip_tags($tmp[0]);
        $email = strip_tags($tmp[1]);

        $tmpemail = implode("ATTO", explode("@", $email));

        $query = "INSERT INTO Users values('$name', '$tmpemail', '$schedid', 'none')";
        $db->query($query) or die("Invalid table" . $db->error);

        //this is the email sent
        $msg = "Hello " . $name . "! The link is at http://localhost/hw2/schedfile.php?sched_id=" . $schedid . "&email=" . $email;

        //send the mail
        $mail->addAddress($email);
        $mail->Subject = "Homework2: Select the times";
        $mail->Body = $msg;
        if(!$mail->send()):
          echo 'Message could not be sent.';
          echo 'Mailer Error: ' . $mail->ErrorInfo;
        endif;
      endforeach;

      //go back to maker logged on page
      header("Location: makerloggedon.php");
    endif;
endif;

//finalizing the schedule
if (isset($_POST["finalizesched"])) {
  //setting up database
  header("Location: finalizesched.php");

}

//after the maker selects which schedules to finalizesched
if (isset($_POST["submitFinalize"])):
  $db = new mysqli("localhost", "root", "", "hw2");
  if(mysqli_connect_errno()):
    echo "sorry did not connect";
  else:
    $listofschedules = array();
    $mapTitletoID = array();
    $currmaker = $_SESSION["maker"];
    $query = "SELECT * FROM ScheduleInfo";
    $result = $db->query($query);
    // output data of each row
    while($row = $result->fetch_assoc()) {
      if ($row["Maker"] == $currmaker) {
        //get list of schedules with titles as key and times as value for easy access
        $tmp = $row["Maker"];
        $listofschedules[$row["Title"]] = $row["Times"];
        $mapTitletoID[$row["Title"]] = $row["sched_id"];
      }
    }
    foreach($listofschedules as $oneTitle => $onetimeset) {
      //if checkbox for that given schedule is checked,
      //send the mail to everyone who has that given schedule id
      if (isset($_POST[$oneTitle])) {
        //fetching users of the schedule id
        $query = "SELECT * FROM Users";
        $result = $db ->query($query);
        $this_schedid = $mapTitletoID[$oneTitle];
        $slotspicked = array();
        $times = array();
        $users = array();

        while ($row = $result->fetch_assoc()):
          //for all users of given schedule id
          if ($row["sched_id"] == $this_schedid) {
            //name => time slots picked for each user
            $useremail = implode("@", explode("ATTO", $row["Email"]));
            $users[$row["Name"]] = $useremail;
            $tmp = $row["slotspicked"];
            if ($tmp == "none"):
              $slotspicked[$row["Name"]] = array();
            else:
              $slotspicked[$row["Name"]] = explode("a", $row["slotspicked"]);
            endif;
          }
        endwhile;

        //making the actual array of dates
        $datearray = explode("t", $onetimeset);
        foreach($datearray as $key => $onedate):
          $tmp = strip_tags($onedate);
          $splitdatetime = explode("d", $tmp);
          $splitdate = explode("a", rtrim($splitdatetime[0]));
          //var_dump($splitdate);
          $hrmin = explode("b", $splitdatetime[1]);
          //date is in year-month-day
          //minutes have to be rtrim'd because of the issue of endline stuff
          $d = mktime($hrmin[0], $hrmin[1], 0, $splitdate[0], $splitdate[1], $splitdate[2]);
          //using the mktime thing to format the headers
          array_push($times, date("l, m/d/Y, h:i a", $d));
        endforeach;

        //tally the total amount of people who selected each time slot
        $slotctr = 0;
        $slottallies = array();
        //var_dump($times);
        foreach($times as $derp):
          $slottallies[$slotctr] = 0;
          $slotctr++;
        endforeach;
        //var_dump($slotspicked);
        //echo "</br>";
        foreach($slotspicked as $auser => $sparray):
          //var_dump($sparray);
          //echo "</br>";
          foreach ($sparray as $onepick):
            $slottallies[$onepick]++;
          endforeach;
        endforeach;

        var_dump($slottallies);
        //find the index of most amount of picks
        $maxPicked = 0;
        $maxIndex = 0;
        $i = 0;
        foreach($slottallies as $onetally):
          if ($maxPicked < $onetally):
            $maxPicked = $onetally;
            $maxIndex = $i;
          endif;
          $i++;
        endforeach;

        $idealdate = $times[$maxIndex];
        //send the mail to each of the users
        echo $idealdate . "</br>";

        foreach($users as $username =>$useremail):
          //initial mail setup related stuff
          $mail = new PHPMailer();
          $mail->IsSMTP(); // telling the class to use SMTP
          $mail->SMTPAuth = true; // enable SMTP authentication
          $mail->SMTPSecure = "tls"; // sets tls authentication
          $mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
          $mail->Port = 587; // set the SMTP port for GMAIL server; or your email server port
          $mail->Username = "purasle@gmail.com"; // email username
          $mail->Password = "kuroyuki12"; // email password
          $mail->SetFrom("purasle@gmail.com");

          $db->query($query) or die("Invalid table" . $db->error);

          //this is the email sent
          $msg = "Hello " . $username . "! The finalized date is at " . $idealdate;

          //send the mail
          $mail->addAddress($useremail);
          $mail->Subject = "Homework2: Finalized time";
          $mail->Body = $msg;
          if(!$mail->send()):
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
          endif;
        endforeach;
      }
    }
    header("Location: makerloggedon.php");
  endif;
endif;

//the user edits his/her row
if (isset($_POST["edit"])) {
  $curr_sched_id = $_SESSION['curr_sched_id'];
  $curr_email = $_SESSION['curr_email'];
  header("Location: http://localhost/hw2/editschedfile.php?sched_id=" . $curr_sched_id . "&email=" . $curr_email);
}

//the user submits the changes given in the row
if (isset($_POST["submittimes"])) {
  $listtimesavailable = $_POST['editedtime'];
  $timesavailable = implode("a", $listtimesavailable);
  $tmpemail = implode("ATTO", explode("@", $_SESSION["curr_email"]));
  $tmpsesh = $_SESSION["curr_sched_id"];
  //setting up database
  $db = new mysqli("localhost", "root", "", "hw2");
  if(mysqli_connect_errno()):
    echo "sorry did not connect";
  else:
    $query = "UPDATE Users SET slotspicked = '$timesavailable' WHERE Email = '$tmpemail' AND sched_id = '$tmpsesh'";
    $db->query($query) or die("Invalid update" . $db->error);
    header("Location: http://localhost/hw2/schedfile.php?sched_id=" . $tmpsesh . "&email=" . $_SESSION["curr_email"]);
  endif;
}

?>
