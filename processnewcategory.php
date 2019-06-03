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

if (!isset($_POST["name"]) || !isset($_POST["codename"]) || empty($_POST["name"]) || empty($_POST["codename"])) {
	header("Location: index.php");
	exit();
}

if (codename_is_good($_POST["codename"]) === false) {
	header("Location: newcategory.php?id=".urlencode($forum["codename"])."&msg=characters");
	exit();
}

$name = htmlspecialchars(mysqli_real_escape_string($con, $_POST["name"]));
$codename = htmlspecialchars(mysqli_real_escape_string($con, $_POST["codename"]));

if (isset($_POST["description"]) && !empty($_POST["description"])) {
	$description = htmlspecialchars(mysqli_real_escape_string($con, $_POST["description"]));
} else {
	$description = "";
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM categories WHERE forum = ".$forum["id"]." AND codename = '".$codename."'"))) {
	header("Location: newcategory.php?id=".urlencode($forum["codename"])."&msg=nameunique");
}

$num = mysqli_num_rows(mysqli_query($con, "SELECT * FROM categories WHERE forum = ".$forum["id"])) + 1;

if (mysqli_query($con, "INSERT INTO categories (forum, codename, name, description, num) VALUES (".$forum["id"].", '".$codename."', '".$name."', '".$description."', ".$num.")")) {
	header("Location: editcategories.php?id=".urlencode($forum["codename"])."&msg=added");
} else {
	echo "No se ha podido crear la categoría debido a un error :-/";
}