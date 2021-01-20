<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if(! isset($_SESSION['user_id'])){
  die('ACCESS DENIED');
  return;
}

if(isset($_POST['cancel'])){
  header('Location: index.php');
  return;
}
if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline'])
&& isset($_POST['summary'])){
  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }
  //validate education
    $msg = validateEdu();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id" .$_REQUEST["profile_id"]);
      return;
    }

  $stmt = $pdo->prepare('INSERT INTO profile (user_id,first_name,last_name,email,headline,summary)
  VALUES (:id,:fname,:lname,:em,:hline,:summary)');
  $stmt->execute(array(
    ":id" => $_SESSION['user_id'],
    ":fname" => $_POST['first_name'],
    ":lname" => $_POST['last_name'],
    ":em" => $_POST['email'],
    ":hline" => $_POST['headline'],
    ":summary" => $_POST['summary']
  ));
  $profile_id = $pdo->lastInsertId(); //get the primary key of the profile inserted, function built in pdo

insertPos($pdo,$profile_id);
insertEdu($pdo, $profile_id);

  $_SESSION['success'] = "Profile added";
  header("Location: index.php");
  return;
}
 ?>
 <!DOCTYPE html>
 <html>
 <head>
 <title>Salma's Add</title>

 <?php require_once "bootstrap.php"; ?>
 </head>
 <body>
 <div class="container">
<h1>Adding Profile for <?= htmlentities($_SESSION['name']);?></h1>

 <?php
 flashMessages();
  ?>
 <form method="post">
 <p>First Name:
   <input type="text" name="first_name" size="40"></p>
 <p>Last Name:
   <input type="text" name="last_name" size="40"></p>
 <p>Email:
 <input type="text" name="email" size="20"></p>
 <p>Headline:
 <input type="text" name="headline" size="20"></p>
<p> Summary:</br>
<textarea name="summary" rows="8" cols="80"></textarea></p>

<p>Education: <input type="button" id = "addEdu" value="+" >
<div id = "edu_fileds"></div>
</p>

<p>Position: <input type="button" id = "addPos" value="+" >
<div id = "position_fileds"></div>
</p>

<p>
 <input type="submit" value="Add">
 <input type="submit" value="Cancel" name="cancel">
</p>
 </form>
 <script>


countPos = 0; //global by default in js
countEdu = 0;

$(document).ready(function(){
  window.console && console.log('Document ready called');
  $('#addPos').click(function(event){ //let's register an event
    event.preventDefault(); //just like return false in old style js
    if(countPos >= 9){
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countPos++;
    window.console && console.log('Adding position'+ countPos);
    $('#position_fileds').append(
      "<div id = 'position"+countPos+"'>"+
      "<p>Year: <input type = 'text' name = 'year"+countPos+"' value=''>"+
      "<input type = 'button' value ='-'"+ 'onclick ="$(\'#position'+countPos+'\').remove(); return false;">'+
      "</p>"+
      "<textarea name = 'desc"+countPos+"' rows = '8' cols = '80'>"+
      "</textarea>"+

      "</div>"
    );
  }

);

$('#addEdu').click(function(event){ //let's register an event
  event.preventDefault(); //just like return false in old style js
  if(countEdu >= 9){
    alert("Maximum of nine Education entries exceeded");
    return;
  }
  countEdu++;
  window.console && console.log('Adding education'+ countEdu);
  //GRAP some HTML with hot spots and insert into the dom
  var source = $('#edu_template').html();
  $('#edu_fileds').append(source.replace(/@COUNT@/g,countEdu));
  //add the even handler to the new ones
  $('.school').autocomplete({
    source : "school.php"
  });

});
//?????????????????? for old ones
$('.school').autocomplete({
  source : "school.php"
});

});
 </script>
 <!-- HTML with subsitution hot spots -->
 <!-- HTML coming from js not php like position -->
 <script id= "edu_template" type="text">
 <div id = "edu@COUNT@">
 <p>Year: <input type = "text" name = "edu_year@COUNT@" value = ""/>
 <input type = "button" value = "-" onclick = "$('#edu@COUNT@').remove();return false;"></p><br>
 <p>School: <input type = "text" name = "edu_school@COUNT@" size = "80" class = "school" value = ""/>
 </p>
 </div>
 </script>
 </body>
</div>
