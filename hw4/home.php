<?php session_start(); ?>
<!DOCTYPE html>
<!-- the style sheet to use -->
<link rel = "stylesheet" type = "text/css" href = "wordStyle.css"/>
<html>
<head><h2> Test Your Anagram Skills </h2></head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js">
</script>
<script>

//main ajax stuff
$(document).ready(function(){
  //variables to keep in mind
  var array = Array();
  var guesses = Array();
  var count = -1;
  //if button is clicked, take next word
  $("button").click(function(){
      $.ajax({
        type: "POST",
        url: "getWord.php",
        dataType: "xml",
        success: function(result) {
          $(result).find("Word").each(function() {
            var name = $(this).find("value").text();
            if (array.indexOf(name) == -1) {
              array.push(name);
              guesses.push(Array());
              count += 1;
            }
            var str = tablehtml(array, guesses);
            $("table").html(str);
            // alert(str);
          })
        }
      })
  })

  //if enter pressed, check the guess
  $(document).keypress(function(e) {
    $.ajax({
      type: "POST",
      url: "checkWord.php",
      data: {guess: $('#guess').val()},
      success: function(result) {
        if(e.which == 13) {
          //alert(result);
          var guess = $('#guess').val();
          //is the guess already made?
          var chkAlreadyGuessed = guesses[count].indexOf(guess) > -1;
          //is the guess a valid word?
          //is the guess actually an anagram?
          var theWord = array[count].split("").sort();
          var guessedWord = guess.split("").sort();
          if (chkAlreadyGuessed) {
            alert(guess + " is already guessed!");
          }
          if (!chkAnagram(guessedWord, theWord)) {
            alert(guess + " is not contained in the word " + array[count]);
          }
          if (result == "false") {
            alert(guess + " is not in the dictionary");
          }
          if (!chkAlreadyGuessed && chkAnagram(guessedWord, theWord) && result == "true") {
            guesses[count].push(guess);
          }
          var str = tablehtml(array, guesses);
          $("table").html(str);
          $('#guess').val('');
        }
      }
    })
  });

//this is just called to simplify the code.
function tablehtml(array, guesses) {
  var str = "";
  str += "<tr><th colspan=\"2\">Anagram Finder Game</th></tr>";
  str += "<tr><th>Word</th><th>Anagram</th></tr>";
  for (var i = 0; i < array.length; i++) {
    str += "<tr><td>";
    str += array[i];
    str += "</td><td>";
    for (var j = 0; j < guesses[i].length; j++) {
      var tmp = j+1;
      str += tmp + ". " + guesses[i][j] + "<br/>";
    }
    str += "</td></tr>";
  }
  document.getElementById("guess").focus();
  return str;
}

function chkAnagram(guessed, word) {
  if (guessed.length > word.length) {
    return false;
  }
  var iw = 0;
  while (guessed.length > 0) {
    //base case of this thing
    if (iw == word.length) {
      return false;
    }
    if (guessed[0] == word[iw]) {
      guessed.splice(0, 1);
    }
    iw++;
  }
  return true;
}

})
</script>

<body>
  <table id = "tab" border="1px" align = "center"> <tr><th colspan="2">Anagram Finder Game</th></tr>
    <tr><th>Word</th><th>Anagram</th></tr>
  </table>
  <button> Try Another Word </button> <br/>
  Your Guess: <input type = "text" name = "guess" id="guess" value = ""><br />
</body>

</html>
