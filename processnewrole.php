<?php
require_once("core.php");
if (!isset($_POST["id"]) || empty($_POST["id"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_POST["id"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

if (!isset($_POST["name"]) || !isset($_POST["level"]) || empty($_POST["name"])) {
	header("Location: index.php");
	exit();
}

$name = htmlspecialchars(mysqli_real_escape_string($con, $_POST["name"]));
$level = (INT)$_POST["level"];

if ($level > 10 || $level < 0) {
	header("Location: index.php");
	exit();
}

if (isset($_POST["description"]) && !empty($_POST["description"])) {
	$description = htmlspecialchars(mysqli_real_escape_string($con, $_POST["description"]));
} else {
	$description = "";
}

if (isset($_POST["expert"])) {
	$expert = "true";
} else {
	$expert = "false";
}

$permissions = mysqli_real_escape_string($con, json_encode($MASTER["default_permissions"]));

if (mysqli_query($con, "INSERT INTO roles (forum, name, description, level, expert, permissions) VALUES (".$forum["id"].", '".$name."', '".$description."', '".$level."', ".$expert.", '".$permissions."')")) {
	header("Location: editroles.php?id=".urlencode($forum["codename"])."&msg=added");
} else {
	echo "No se ha podido crear el rol debido a un error :-/";
}