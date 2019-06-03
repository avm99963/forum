<?php
require_once("core.php");

if ($CONF["registerFilter"] === false) {
  header("Location: index.php");
  exit();
}

$username = htmlspecialchars(mysqli_real_escape_string($con, $_POST['username']));
$password = $_POST['password'];
$email = mysqli_real_escape_string($con, $_POST['email']);
if ($CONF["recaptcha"]["use"] === true) {
  $recaptcha = $_POST['g-recaptcha-response'];

  $response_recaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$CONF["recaptcha"]["secretKey"]."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

  if ($response_recaptcha["success"] === false) {
    header("Location: signup.php?msg=recaptcha");
    exit();
  }
}

if (empty($username) || empty($email) || empty($password)) {
  header("Location: index.php");
  exit();
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  header("Location: signup.php?msg=emailincorrect");
  exit();
}

if (strlen($password) < 6) {
  header("Location: signup.php?msg=password");
  exit();
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '".$username."'"))) {
  header("Location: signup.php?msg=usernametaken");
  exit();
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '".$email."'"))) {
  header("Location: signup.php?msg=emailregistered");
  exit();
}

$password_hash = password_hash(mysqli_real_escape_string($con, $password), PASSWORD_DEFAULT);

if (mysqli_query($con, "INSERT INTO users (username, email, role, password, joined) VALUES ('".$username."', '".$email."', 0, '".$password_hash."', ".time().")")) {
  header("Location: index.php?msg=registered");
} else {
  die("Ha ocurrido un error :-/");
}