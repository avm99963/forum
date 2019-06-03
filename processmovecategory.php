<?php
require_once("core.php");
if (!isset($_GET["forum"]) || empty($_GET["forum"]) || !isset($_GET["category"]) || empty($_GET["category"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_GET["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
$category = get_category($_GET["category"], false, $forum["id"]);
if ($category === false) {
	header("Location: index.php");
	exit();
}

if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

if (!isset($_GET["move"]) || !in_array($_GET["move"], array("up", "down"))) {
	header("Location: index.php");
	exit();
}

$categories = get_categories($forum["id"]);
$count = count($categories);

if ($_GET["move"] == "up") {
	// Going up
	$switch_with = $category["num"] + 1;
	if ($switch_with > $count) {
		die("¡Hasta el infinito y más allá!");
	}
} else {
	// Going down
	$switch_with = $category["num"] - 1;
	if ($switch_with == 0) {
		die("No querrás que nos adentremos en los números negativos... ¿verdad?");
	}
}

mysqli_query($con, "UPDATE categories set num = ".$category["num"]." WHERE num = ".$switch_with);

if (mysqli_query($con, "UPDATE categories set num = ".$switch_with." WHERE id = ".$category["id"])) {
	header("Location: editcategories.php?id=".urlencode($forum["codename"]));
} else {
	echo "No se ha podido mover la categoría debido a un error :-/";
}