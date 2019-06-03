<?php
require_once("core.php");
if (!isset($_POST["forum"]) || empty($_POST["forum"]) || !isset($_POST["category"]) || empty($_POST["category"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_POST["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
$category = get_category($_POST["category"], false, $forum["id"]);
if ($category === false) {
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
	header("Location: editcategory.php?forum=".urlencode($forum["codename"])."&category=".urlencode($category["codename"])."&msg=characters");
	exit();
}

$name = htmlspecialchars(mysqli_real_escape_string($con, $_POST["name"]));
$codename = htmlspecialchars(mysqli_real_escape_string($con, $_POST["codename"]));

if (isset($_POST["description"]) && !empty($_POST["description"])) {
	$description = htmlspecialchars(mysqli_real_escape_string($con, $_POST["description"]));
} else {
	$description = "";
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM categories WHERE forum = ".$forum["id"]." AND codename = '".$codename."' AND id != ".$category["id"]))) {
	header("Location: editcategory.php?forum=".urlencode($forum["codename"])."&category=".urlencode($category["codename"])."&msg=nameunique");
}

if (mysqli_query($con, "UPDATE categories set codename = '".$codename."', name = '".$name."', description = '".$description."' WHERE id = ".$category["id"])) {
	header("Location: editcategories.php?id=".urlencode($forum["codename"])."&msg=edit");
} else {
	echo "No se ha podido editar la categoría debido a un error :-/";
}