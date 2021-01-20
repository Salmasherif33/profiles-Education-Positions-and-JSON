<?php
session_start();
require_once "pdo.php";
 ?>
 <!DOCTYPE html>
 <html>
 <head>
<title>Salma's profile view</title>
<?php require_once "bootstrap.php"; ?>
 </head>
 <body>
<div class="container">
<h1>Profile information</h1>
<?php
$user = $_SESSION['user_id'];
$stmt = $pdo->query("SELECT * FROM profile WHERE user_id = $user");

while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
  echo '<p>First Name: '.htmlentities($row['first_name'])."</p>\n";
    echo '<p>last Name: '.htmlentities($row['last_name'])."</p>\n";
      echo '<p>Email: '.htmlentities($row['email'])."</p>\n";
        echo '<p>Headline: <br>'.htmlentities($row['headline'])."</p>\n";
          echo '<p>Summary: <br>'.htmlentities($row['summary'])."</p>\n";
          $id = $row['profile_id'];
            $q = $pdo->query("SELECT * FROM position WHERE profile_id = $id");
            $r = $q->fetch(PDO::FETCH_ASSOC);
          if($r != false){
          $q = $pdo->query("SELECT * FROM position WHERE profile_id = $id");
          echo "positions";
          echo "<ul>";
          while ($r=$q->fetch(PDO::FETCH_ASSOC)){
            echo ("<li>".htmlentities($r['year']));
            echo (" : ".htmlentities($r['description'])."</li>");
          }
          echo "</ul>";
        }
          echo '<p><a href="index.php">Done</a></p>';
}
?>
</div>
 </body>
