<?php
require_once("core.php");

require_once("lib/htmlpurifier/HTMLPurifier.auto.php");
$purifier = new HTMLPurifier();

if (!isset($_POST["id"]) || empty($_POST["id"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum(htmlspecialchars($_POST["id"]));
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
	header("Location: editinfo.php?id=".urlencode($_POST["id"])."&msg=characters");
	exit();
}

$name = htmlspecialchars(mysqli_real_escape_string($con, $_POST["name"]));
$codename = htmlspecialchars(mysqli_real_escape_string($con, $_POST["codename"]));

if (isset($_POST["description"]) && !empty($_POST["description"])) {
	$description = htmlspecialchars(mysqli_real_escape_string($con, $_POST["description"]));
} else {
	$description = "";
}

if (isset($_POST["welcome"]) && !empty($_POST["welcome"])) {
	$welcome = mysqli_real_escape_string($con, $purifier->purify($_POST["welcome"]));
} else {
	$welcome = "";
}

if (isset($_POST["plain_template"]) && !empty($_POST["plain_template"])) {
	$plain_template = mysqli_real_escape_string($con, $_POST["plain_template"]);
} else {
	$plain_template = "";
}

if (isset($_POST["rich_template"]) && !empty($_POST["rich_template"])) {
	$rich_template = mysqli_real_escape_string($con, $purifier->purify($_POST["rich_template"]));
} else {
	$rich_template = "";
}

if ($_FILES["logo"]["error"] != UPLOAD_ERR_NO_FILE) {
	$file = upload_image($_FILES["logo"], $forum["logo"]);
} else {
	$file = $forum["logo"];
}

if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM forums WHERE codename = '".$codename."' AND id != ".$forum["id"]))) {
	header("Location: editinfo.php?id=".urlencode($_POST["id"])."&msg=nameunique");
}

if (isset($_POST["levels"])) {
	$levels = "true";
} else {
	$levels = "false";
}

if (mysqli_query($con, "UPDATE forums set codename = '".$codename."', name = '".$name."', description = '".$description."', welcome = '".$welcome."', logo = '".$file."', levels = ".$levels.", plain_template = '".$plain_template."', rich_template = '".$rich_template."' WHERE id = ".$forum["id"])) {
	header("Location: editinfo.php?id=".urlencode($codename)."&msg=success");
} else {
	echo "No se ha podido editar el Foro debido a un error :-/";
}