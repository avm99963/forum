<?php
require_once("core.php");
require_once("includes.php");
if (!loggedin()) {
	exit();
}

if (!isset($_POST["username"]) || !isset($_POST["fullname"]) || !isset($_POST["email"]) || empty($_POST["username"]) || empty($_POST["email"])) {
	header("Location: index.php");
	exit();
}

if (!in_array($_POST["timezone"], DateTimeZone::listIdentifiers())) {
	header("Location: index.php");
	exit();
}

$username = htmlspecialchars(mysqli_real_escape_string($con, $_POST['username']));
$fullname = htmlspecialchars(mysqli_real_escape_string($con, $_POST['fullname']));
$email = mysqli_real_escape_string($con, $_POST['email']);
$timezone = mysqli_real_escape_string($con, $_POST['timezone']);

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  header("Location: settings.php?msg=emailincorrect");
  exit();
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '".$username."' AND id != ".userdata("id")))) {
  header("Location: settings.php?msg=usernametaken");
  exit();
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '".$email."' AND id != ".userdata("id")))) {
  header("Location: settings.php?msg=emailregistered");
  exit();
}

if (isset($_POST["password"]) && !empty($_POST["password"])) {
	$password = $_POST['password'];
	$oldpassword = $_POST['oldpassword'];

	$hash = userdata("password");

	if (!password_verify($oldpassword, $hash)) {
		header("Location: settings.php?msg=wrongpassword");
  		exit();
	}

	if (strlen($password) < 6) {
	  header("Location: settings.php?msg=password");
	  exit();
	}

	$password_hash = password_hash(mysqli_real_escape_string($con, $password), PASSWORD_DEFAULT);

	mysqli_query($con, "UPDATE users SET password='".$password_hash."' WHERE id=".userdata("id")) or die("Mysql error");
}

if (isset($_POST["recentsearches"])) {
	$recentsearches = "true";
} else {
	$recentsearches = "false";
	mysqli_query($con, "DELETE FROM searches WHERE searcher = ".userdata("id")) or die(mysqli_error($con));
}

if ($_FILES["avatar"]["error"] != UPLOAD_ERR_NO_FILE && !empty($_FILES["avatar"]["name"])) {
	$file = upload_image($_FILES["avatar"], userdata("avatar"));
} else {
	$file = userdata("avatar");
}

mysqli_query($con, "UPDATE users SET username = '".$username."', fullname='".$fullname."', email='".$email."', avatar='".$file."' WHERE id=".userdata("id")) or die("Mysql error");

if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM settings WHERE user = ".userdata("id")))) {
	mysqli_query($con, "UPDATE settings SET recentsearches = ".$recentsearches.", timezone = '".$timezone."' WHERE user = ".userdata("id")) or die(mysqli_error($con));
	header("Location: settings.php?msg=saved");
} else {
	mysqli_query($con, "INSERT INTO settings (user, recentsearches, timezone) VALUES (".userdata("id").", ".$recentsearches.", '".$timezone."')") or die(mysqli_error($con));
	header("Location: settings.php?msg=saved");
}