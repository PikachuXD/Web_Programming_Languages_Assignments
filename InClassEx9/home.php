<!DOCTYPE html>
<html>
<script type = "text/javascript">

function requestWord() {
  var htmlRequest = new XMLHttpRequest();

	htmlRequest.onreadystatechange = function() {
		if (htmlRequest.readyState == 4 && htmlRequest.status == 200) {
			var output = htmlRequest.responseXML;
			var word = output.getElementsByTagName("Word");
      var str = word[0].childNodes[0].nodeValue;
      document.getElementById("d").innerHTML = str;
      /*
      var problem = output.getElementsByTagName("problem");
			for (var i = 0; i < questions.length; i++) {
				str += questions[i].childNodes[0].nodeValue;
				var choices = problem[i].getElementsByTagName("answer");
				str += "<select>";
				for (var j = 0; j < choices.length; j++) {
					str += "<option>";
					str += choices[j].childNodes[0].nodeValue;
					str += "</option>";
				}

				str += "</select>";
				str += "<br/>";

				//alert(str);
				document.getElementById("di").innerHTML = str;
			}
      */
		}
	}
	htmlRequest.open('GET', 'getword.php', true);
	htmlRequest.send();
}

</script>
 <head>
  <title>In Class Exercise 9</title>
 </head>
 <body>
   <form name = "pollForm">
   <table id = "theTable" border = "1">
       <caption><b>Table of Words and Counts</b></caption>
       <tr align = center>
           <th>Word</th><th>Count</th>
       </tr>

   </table>
<div id = "d"> </div>
<button type= "button" onclick= "requestWord()">Add a Word</button>
</form>
 </body>
</html>
