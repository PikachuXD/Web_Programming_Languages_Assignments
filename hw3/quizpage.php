<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php
  date_default_timezone_set("EST");
  $today = getdate();
  $category_id = $today["wday"] % 2;
  //use this to check if the test has been taken on the given date
  $fulldate =  $today["year"] . $today["mon"] . $today["mday"];
  $_SESSION["fulldate"] = $fulldate;
  $cookiestring = "Taken" . $fulldate;

  //make sure the user can't access the quiz if he/she quits
  if (isset($_COOKIE[$cookiestring])) {
    echo "Nope you can't take the quiz again";
  }
  else {

?>

<head><h1> Homework 3 </h1></head>


<script type="text/javascript">
/*
Two major types of loads:
  1 - load the quiz info
  2 - take in answer
*/
function loadQuiz() {
  var httpRequest;

  var type = arguments[0];  // get type of call
  //default ajax stuff
  if (window.XMLHttpRequest) { // Mozilla, Safari, ...
      //alert('XMLHttpRequest');
      httpRequest = new XMLHttpRequest();
      if (httpRequest.overrideMimeType) {
          httpRequest.overrideMimeType('text/xml');
      }
  }
  else if (window.ActiveXObject) { // Older versions of IE
      //alert('IE -- XMLHTTP');
      try {
          httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
          }
      catch (e) {
          try {
              httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
          }
          catch (e) {}
      }
  }
  if (!httpRequest) {
      alert('Giving up :( Cannot create an XMLHTTP instance');
      return false;
  }
  var check = document.getElementById("quiz").innerHTML;
  if(check)
  {
  	  document.getElementById("quiz").innerHTML = "";
  }
  else{
	  httpRequest.onreadystatechange = function() {
	    if (httpRequest.readyState == 4 && httpRequest.status == 200) {
	      document.getElementById("quiz").innerHTML = httpRequest.responseText;
	    }
	  }

  }

  var data;
  //load quiz question
  if (type == 1)
  {
      var category_id = arguments[1];
      var question_id = arguments[2];

      data = 'type=' + type + '&category_id=' + category_id + '&question_id=' + question_id;
  }
  //load quiz answers
  else if (type == 2)
  {
      var category_id = arguments[1];
      var question_id = arguments[2];
      var selected = arguments[3];
      data = 'type=' + type + '&category_id=' + category_id + '&question_id=' + question_id + '&selected=' + selected;
  }
  //calculate averages
  else
  {

  }

  //since it's a POST method, I have to set content type header
  httpRequest.open("POST", "tabulate.php", true);
  httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  if (type == 1)
        httpRequest.onreadystatechange = function() { showQuestion(httpRequest, category_id, question_id); };
  if (type == 2)
        httpRequest.onreadystatechange = function() { showQuestion(httpRequest, category_id, question_id, selected); };

  httpRequest.send(data);
}

function showQuestion(httpRequest, category_id, question_id)
{
  if (httpRequest.readyState == 4)
  {
     if (httpRequest.status == 200)
     {
       var quizform = document.getElementById("quiz");
       var responseString = httpRequest.responseText;
       var possibleDone = responseString.split("@");
       if (possibleDone[0] == "Done") {
         alert("Your average for this quiz is " + possibleDone[1] + " out of " + possibleDone[2]);
         quizform.innerHTML = "You have completed the quiz!";
       }
       else {
         var qnancorra = responseString.split("$");

         //the question, answer and the correct answer split
         var q = qnancorra[0];
         var a = qnancorra[1];
         var corra = qnancorra[2];


         var htmlstring = q + "</br>";
         quizform.innerHTML = htmlstring;
         //answers
         var fullanswers = a.split("@");
         for (var i = 0; i < fullanswers.length; i++) {
           label = makeRadioButton("a", fullanswers[i], fullanswers[i]);
           //append child is necessary
           quizform.appendChild(label);
           quizform.innerHTML += "</br>";
         }

         var submit = document.createElement('input');
         submit.type = "button";
         submit.value = "Enter";
         submit.onclick = function() { submitAnswer(httpRequest, category_id, question_id); };
         quizform.appendChild(submit);
      }

     }
   }
}

function makeRadioButton(name, value, text) {
  //make a special element for the radio button
  var label = document.createElement("label");
  //making the radio button
  var radio = document.createElement("input");
  radio.type = "radio";
  radio.name = name;
  radio.value = value;

  //actually adding the elements onto radio button
  label.appendChild(radio);

  label.appendChild(document.createTextNode(text));
  return label;
}

//take the submitted answer and call the loadquiz thing
function submitAnswer(httpRequest, category_id, question_id) {
  var selected = document.qform.a.value;
  loadQuiz(2, category_id, question_id+1, selected);
}
</script>

<body>

<?php
//initialize database
$db = new mysqli("localhost", "root", "", "hw3");

if(mysqli_connect_errno()) {
  echo "sorry did not connect";
}

//echo $fulldate;
$query = "SELECT * FROM Category";
$result = $db->query($query);
$category = "";
$totalquestions = 0;
while($row = $result->fetch_assoc()) {
      if ($row["Category_id"] == $category_id) {
        $category = $row["Category_name"];
        $totalquestions = $row["No_questions"];
      }
}
//the full question count is seeded based on date
$q_ct = $fulldate % $totalquestions + 1;
$_SESSION["total_questions"] = $q_ct;
$_SESSION["individual_score"] = 0;

echo "<h1>" . $category . "</h1>";
$btn = "<button type=\"button\" onclick=\"loadQuiz(1, $category_id, 0)\">Toggle Quiz Data</button>";
echo $btn;
 ?>
 <form method ="POST"
        name = "qform">
 <div id="quiz">
 </div>
 </form>
 <?php }?>
</body>
</html>
