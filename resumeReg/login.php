<?php // Do not put any HTML above this line
session_start();
require_once "pdo.php";

unset($_SESSION['name']); //to log the user out;
unset($_SESSION['user_id']);

if(isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}
$salt = 'XyZzy12*_';
// Pw is php123

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
  if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
      $_SESSION['error'] = "Email and password are required";
      header("Location: login.php");
      return;

  }

  $check = hash('md5', $salt.$_POST['pass']);
  $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
  $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ( $row !== false ) {

    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];

// Redirect the browser to index.php
  header("Location: index.php");
  return;
}else {
  echo "string";;
  $_SESSION['error'] = "Incorrect password";
  header("Location: login.php");
  return;
}
}

?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Salma's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>

<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION["error"]) ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION["error"]);
}
?>
<form method="POST" action="login.php">
<label for="email">Email:</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password:</label>
<input type="password" name="pass" id="id_1723"><br/>
<p>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" value="Cancel" name="cancel">
</p>
</form>
<script>
  function doValidate(){
    console.log('validating...');
    try{
      pw = document.getElementById('id_1723').value;
      email = document.getElementById('email').value;
      console.log("validating pw: "+pw +" email: "+ email);
      if(pw == null || pw == "" || email == null || email == ""){
        alert("Both fileds must be filled out");
        return false;
      }
      if(email.indexOf('@') == -1){
        alert("Invalid email address");
        return false;
      }
      return true;
    }catch(e){
      return false;
    }
    return false;
  }
</script>
</div>
</body>
