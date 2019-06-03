<?php
require_once("core.php");
if (!isset($_POST["forum"]) || empty($_POST["forum"]) || !isset($_POST["role"]) || empty($_POST["role"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_POST["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
$role = get_role($_POST["role"], $forum["id"]);
if ($role === false) {
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

if ($_FILES["badge"]["error"] != UPLOAD_ERR_NO_FILE) {
	$file = upload_image($_FILES["badge"], $role["badge"]);
} else {
	$file = $role["badge"];
}

if (mysqli_query($con, "UPDATE roles SET forum = ".$forum["id"].", name = '".$name."', description = '".$description."', level = '".$level."', expert = ".$expert.", badge = '".$file."' WHERE id = ".$role["id"])) {
	header("Location: editroles.php?id=".urlencode($forum["codename"])."&msg=edit");
} else {
	echo "No se ha podido editar el rol debido a un error :-/";
}