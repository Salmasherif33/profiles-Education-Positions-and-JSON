<?php
require_once "pdo.php";
require_once "util.php";

session_start();

if(!isset($_SESSION['user_id']) ){
  die("ACCESS DENIED");
}

if(isset($_POST['cancel'])){
  echo "hiiiiiiiiii";
  header('Location: index.php');
  return;
}

if (!isset($_GET['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

if( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline'])
&& isset($_POST['summary'])){
  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id" .$_REQUEST["profile_id"]);
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id" .$_REQUEST["profile_id"]);
    return;
  }
//validate education
  $msg = validateEdu();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id" .$_REQUEST["profile_id"]);
    return;
  }

$sql = "UPDATE profile SET first_name=:first_name, last_name=:last_name, email=:email, headline=:headline,
summary=:summary WHERE profile_id=:profile_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ":first_name" => $_POST['first_name'],
  ":last_name" => $_POST['last_name'],
  ":email" => $_POST['email'],
  "headline" => $_POST['headline'],
  ":summary" => $_POST['summary'],
  ":profile_id" => $_GET['profile_id']
));


//delete old data from position (this option give me flexability to add also another one )
$stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_REQUEST['profile_id']));
//insert again the position entries
insertPos($pdo,$_REQUEST['profile_id']);

$stmt = $pdo->prepare('DELETE FROM education WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_REQUEST['profile_id']));
//insert again the education entries
insertEdu($pdo,$_REQUEST['profile_id']);


$_SESSION['success'] = "Profile updated";
header("Location: index.php");
return;
}

$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id=:xyz AND user_id = :uid");
$stmt->execute(array(":xyz"=>$_GET['profile_id'],":uid" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row == false){
  $_SESSION['error'] = "Bad values for profile_id";
  header("Location: index.php");
  return;
}
$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$hline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

//load up the position rows
$positions = loadPos($pdo,$_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Salma's Profile edit</title>

<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?=htmlentities($_SESSION['name']);?></h1>

<?php
flashMessages();
 ?>
<form method="post">
<p>First Name:
  <input type="text" name="first_name" size="40" value="<?=$fname?>"></p>
<p>Last Name:
  <input type="text" name="last_name" size="40" value="<?=$lname?>"></p>
<p>Email:
<input type="text" name="email" size="20" value="<?=$email?>"></p>
<p>Headline:
<input type="text" name="headline" size="20" value="<?=$hline?>"></p>
<p>Summary:</br>
<textarea name="summary" rows="8" cols="80"><?=$summary?></textarea></p>

<?php
$edu = 0;
echo ('<p>Education: <input type="button" id = "addEdu" value="+" >'."\n");
echo ('<div id = "edu_fileds">'."\n");
if(count((is_countable($educations)?$educations:[]))){
foreach ($educations as $education) {
  $edu++;
  echo ('<div id = "edu'.$edu.'">'."\n");
  echo ('<p>Year: <input type = "text" name = "edu_year'.$edu.'"');
  echo ('value = "'.$education['year'].'"/>'."\n");
  echo ('<input type = "button" value ="-" onclick = "$(\'#edu'.$edu.'\').remove(); return false;">');
  echo ('</p>'."\n");
  echo ('<p>School: <input type ="text" size = "80" class = "school" name = "edu_school'.$edu.'"');
  echo ('value = "'.htmlentities($education['name']).'"/>'."\n");
  echo ('</p></div>'."\n");
}
}
echo ("</div></p>\n");


$pos = 0;
echo ('<p>Position: <input type="button" id = "addPos" value="+" >'."\n");
echo ('<div id = "position_fileds">'."\n");
foreach ($positions as $position) {
  $pos++;
  echo ('<div id = "position'.$pos.'">'."\n");
  echo ('<p>Year: <input type = "text" name = "year'.$pos.'"');
  echo ('value = "'.$position['year'].'"/>'."\n");
  echo ('<input type = "button" value ="-" onclick = "$(\'#position'.$pos.'\').remove(); return false;">');
  echo ('</p>'."\n");
  echo ('<textarea name = "desc'.$pos.'" rows = "8" cols = "80">'."\n");
echo (htmlentities($position['description'])."\n");
  echo ("\n".'</textarea>'."\n".'</div>');
}
echo ("</div></p>\n");
 ?>


<input type="hidden" name="profile_id" value="<?=$profile_id?>">
<input type="submit" value="Save" name ="save">
<input type="submit" value="Cancel" name="cancel">


</form>
<script>

countPos = <?= $pos?>; //global by default in js, take the number from phph generated code
countEdu = <?=$edu?>;
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
</div>
</body>
</html>
