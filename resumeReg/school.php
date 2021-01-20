<?php
//JQUERY and JQUERY UI
if(!isset($_GET['term'])) die('Missing required parameter');
//Let's not start a session unless we already have one
//check if session-cookie set properly
if(!isset($_COOKIE[session_name()])){
  die("Must be logged in");
}
session_start();
if(!isset($_SESSION['user_id'])){
  die("ACCESS DENIED");
}
//don'it even make a connection to db until we are happy
require_once "pdo.php";

//sleep(3);
header('Content-Type: application/json; charset=utf-8');
$term = $_GET['term'];
error_log("Looking up typeahed term=".$term);
$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
