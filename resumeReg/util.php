<?php

function flashMessages(){
  if(isset($_SESSION['success'])){
    echo ('<p style="color:green;">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
  }
  if(isset($_SESSION['error'])){
    echo ('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
  }
}

function validateProfile(){
  if(strlen($_POST['first_name']) < 1|| strlen($_POST['last_name']) <1 || strlen($_POST['headline']) <1||
  strlen($_POST['email'])<1 || strlen($_POST['summary'])<1){
    return "All fields are required";
  }
  if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    return "Email address must contain @";
  }
  return true;
}

function validatePos(){
  for($i=1; $i<=9; $i++){
    if(! isset($_POST['year'.$i]) ) continue;
    if(! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if(strlen($year) == 0 || strlen($desc) == 0){
      return "All fields are required";
    }
    if (! is_numeric($year)){
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu(){
  for($i=1; $i<=9; $i++){
    if(! isset($_POST['edu_year'.$i]) ) continue;
    if(! isset($_POST['edu_school'.$i]) ) continue;
    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];
    if(strlen($edu_year) == 0 || strlen($edu_school) == 0){
      return "All fields are required";
    }
    if (! is_numeric($edu_year)){
      return "Position year must be numeric";
    }
  }
  return true;
}
function loadPos($pdo, $profile_id){
  $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id));
  $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $positions;
}

function loadEdu($pdo, $profile_id){
  //many profile to one institution or many institution to many profiles (many to many relation ship)   do join here
  $stmt = $pdo->prepare('SELECT * FROM education JOIN institution ON  education.institution_id = institution.institution_id
  WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id));
  $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}

function insertPos($pdo, $profile_id){
  $rank = 1;
  for($i=1; $i<=9; $i++){
    if(! isset($_POST['year'.$i]) ) continue;
    if(! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
    VALUES (:pid, :rank, :year, :desc)');
    $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':desc' => $desc
    ));
    $rank++;
  }
}

function insertEdu($pdo, $profile_id){
  $rank = 1;
  for($i=1; $i<=9; $i++){
    if(! isset($_POST['edu_year'.$i]) ) continue;
    if(! isset($_POST['edu_school'.$i]) ) continue;
    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    $institution_id = false;
    $stmt = $pdo->prepare("SELECT institution_id FROM institution WHERE name = :name");
    $stmt->execute(array(":name" => $edu_school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row != false) $institution_id = $row['institution_id'];

//if the user type a school which is not in the institution
    if($institution_id === false){
      $stmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
      $stmt->execute(array(":name" => $edu_school));
      $institution_id = $pdo->lastInsertId();
    }
    $stmt = $pdo->prepare('INSERT INTO education (profile_id, rank, year, institution_id)
    VALUES (:pid, :rank, :year, :inst)');
    $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $edu_year,
      ':inst' => $institution_id //????
    ));
    $rank++;
  }
}
