<?php
require_once("core.php");
require_once("includes.php");
if (!isadmin()) {
	exit();
}

if (!isset($_POST["name"]) || !isset($_POST["codename"]) || empty($_POST["name"]) || empty($_POST["codename"])) {
	header("Location: index.php");
	exit();
}

if (codename_is_good($_POST["codename"]) === false) {
	header("Location: newforum.php?msg=characters");
	exit();
}

$name = htmlspecialchars(mysqli_real_escape_string($con, $_POST["name"]));
$codename = mysqli_real_escape_string($con, $_POST["codename"]);

if (isset($_POST["description"]) && !empty($_POST["description"])) {
	$description = htmlspecialchars(mysqli_real_escape_string($con, $_POST["description"]));
} else {
	$description = "";
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM forums WHERE codename = '".$codename."'"))) {
	header("Location: newforum.php?msg=nameunique");
}

$permissions = mysqli_real_escape_string($con, json_encode($MASTER["default_permissions"]));

if (mysqli_query($con, "INSERT INTO forums (codename, name, description, guest_permissions, user_permissions) VALUES ('".$codename."', '".$name."', '".$description."', '".$permissions."', '".$permissions."')")) {
	header("Location: index.php");
} else {
	echo "No se ha podido crear el Foro debido a un error :-/";
}