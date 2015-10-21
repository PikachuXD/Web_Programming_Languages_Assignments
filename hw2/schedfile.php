<?php session_start() ?>
 <!DOCTYPE html>
 <html>
 <head>
 </head>

 <body>
  <form action = "process.php"
      method = "POST">
      <?php
      //take up the schedule info!
      $db = new mysqli("localhost", "root", "", "hw2");
      if(mysqli_connect_errno()) {
        echo "Sorry. Did not connect";
      } else {
        //all major variables
        $email = $_GET["email"];
        $sched_id = $_GET["sched_id"];
        $dates = "";
        $title = "";
        $maker = "";
        $useremail = "";
        $slotsfromtable = "";
        //arrays used for the tables
        $users = array();
        $times = array();
        $slotspicked = array();
        $slottallies = array();

        //pick up schedule info
        $query = "SELECT * FROM ScheduleInfo";
        $result = $db->query($query);

        //basic schedule info matching
        while($row = $result->fetch_assoc()) {
              if ($row["sched_id"] == $sched_id) {
                //name => email for each user
                $dates = $row["Times"];
                $maker = $row["Maker"];
                $title = $row["Title"];
              }
        }

        //basic table set up
        echo "<table border = \"1\" id=\"table\">";
        echo "<caption><h2> $title by $maker : Select your meeting times! </h2></caption>";
        echo "<tr align = \"center\">";
        echo "<th> User </th>";

        //pick up the users for schedule id
        $query = "SELECT * FROM Users";
        $result = $db->query($query);
        // output data of each row
        while($row = $result->fetch_assoc()) {
              if ($row["sched_id"] == $sched_id) {
                //name => email for each user
                $useremail = implode("@", explode("ATTO", $row["Email"]));
                $users[$row["Name"]] = $useremail;
                $tmp = $row["slotspicked"];
                if ($tmp == "none"):
                  $slotspicked[$row["Name"]] = array();
                else:
                  $slotspicked[$row["Name"]] = explode("a", $row["slotspicked"]);
                endif;
              }
        }

        //making the actual array of dates
        $datearray = explode("t", $dates);
        foreach($datearray as $key => $onedate):
          $tmp = strip_tags($onedate);
          echo "</br>";
          $splitdatetime = explode("d", $tmp);
          $splitdate = explode("a", rtrim($splitdatetime[0]));
          //var_dump($splitdate);
          $hrmin = explode("b", $splitdatetime[1]);
          //date is in year-month-day
          //minutes have to be rtrim'd because of the issue of endline stuff
          $d = mktime($hrmin[0], $hrmin[1], 0, $splitdate[0], $splitdate[1], $splitdate[2]);
          //using the mktime thing to format the headers
          array_push($times, date("l \n m/d/Y \n h:i a", $d));
        endforeach;

        //make rest of first row of table
        foreach ($times as $onetime):
          echo "<th>$onetime</th>";
        endforeach;
        echo "<th> Action</th>";
        echo "</tr>";

        foreach ($users as $oneuser => $hisemail):
    			echo "<tr align = \" center \">";
    			echo "<td>" . $oneuser . "</td>";
    			$tmpctr = 0;
          $timesofoneuser = $slotspicked[$oneuser];
    			while ($tmpctr < count($times)):
            if (in_array("none", $timesofoneuser)):
              echo "<td> </td>";
            else:
              if (in_array($tmpctr, $timesofoneuser)):
    					       echo "<td>&#10004</td>";
    				  else:
    					       echo "<td> </td>";
    				  endif;
            endif;
    				$tmpctr++;
    			endwhile;
          /*foreach($times as $onetime):
            echo "<td></td>";
          endforeach;*/
          if ($hisemail == $email):
    				echo "<td> <input type=\"submit\" name = \"edit\" value = \"Edit\" > </td>";
    			else:
    				echo "<td> </td>";
    			endif;
    		endforeach;

        //tally the total amount of people who selected each time slot
        $slotctr = 0;
        foreach($times as $derp):
          $slottallies[$slotctr] = 0;
          $slotctr++;
        endforeach;
        foreach($slotspicked as $auser => $sparray):
          foreach ($sparray as $onepick):
            $slottallies[$onepick]++;
          endforeach;
        endforeach;

        //total row
        echo "<tr align = \" center \">";
        echo "<td> Total: </td>";
        foreach ($slottallies as $onepick):
          echo "<td>" . $onepick . "</td>";
        endforeach;
        echo "<td> </td>";
        $_SESSION['curr_sched_id'] = $sched_id;
        $_SESSION['curr_email'] = $email;
      }

       ?>
  </form>
 </body>
 </html>
