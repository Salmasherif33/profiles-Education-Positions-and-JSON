<?php
require_once "pdo.php";
require_once "util.php";

session_start();

if(isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}

if(! isset($_GET['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header("Location: index.php");
  return;
}
if(isset($_POST['delete']) && isset($_POST['profile_id'])){
  $sql = "DELETE FROM profile WHERE profile_id=:xyz";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(":xyz" => $_POST['profile_id']));
  $_SESSION['success'] = "Profile deleted";
  header("Location: index.php");
  return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile WHERE profile_id=:profile_id");
$stmt->execute(array(":profile_id"=> $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row == false){
  $_SESSION['error'] = "Bad value for profile_id";
  header("Location:index.php");
  return;
}
?>
<html>
<head>
  <title>Salma's Deleting...</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <h1>Deleting profile</h1>
  <p>First Name: <?= htmlentities($row['first_name'])?></p>
  <p>Last Name: <?= htmlentities($row['last_name'])?></p>
<?php
//every thing from database entered by user , use htmlentities
// echo ('<p>Confirm: Deleting '.$row['name'].''."</p\n>");
?>
<form method="post">
<p><input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
 <input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
