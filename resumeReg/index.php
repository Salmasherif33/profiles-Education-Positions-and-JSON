<?php
session_start();
require_once "pdo.php";
require_once "util.php";
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Salma's Index Page</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Salma's Resume Registry</h1>
<?php
if(! isset($_SESSION['name'])){
  echo '<p><a href="login.php">Please log in</a></p>'."\n";
  echo "<p>No Rows Found</p>";
}else {
  flashMessages();
  echo '<p><a href="logout.php">Logout</a></p>'."\n";
  $stmt = $pdo->query("SELECT * FROM profile");
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row == false){

    echo "<p>No Rows Found</p>";

  }else{

  echo ('<table border="1">'."\n");
  echo ('<thead><tr><th>Name</th>');
  echo ('<th>Headline</th>');
  echo ('<th>Action</th>');
$stmt = $pdo->query("SELECT * FROM profile");
  while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    echo ("<tr><td>");
    echo ('<a href = "view.php">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>');
    echo "</td><td>";
    echo (htmlentities($row['headline']));
    echo "</td><td>";
    echo ('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  ');
    echo ('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo "</td></tr>\n";
  }
  echo '</table>'."\n";

}
  echo '<p><a href="add.php">Add New Entry</a></p>'."\n";
}
 ?>
</div>
</body>
