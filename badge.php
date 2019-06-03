<?php
require_once("core.php");
if (!isset($_GET["id"]) || empty($_GET["id"])) {
	exit();
}
$role = get_role($_GET["id"]);
if ($role === false) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($role["forum"], true);
if ($forum === false) {
	exit();
}
if (get_permission("view", $forum["id"]) === false) {
	exit();
}

if (empty($role["badge"])) {
	die();
}

$url = "uploaded_img/".$role["badge"];
$header = _mime_content_type($url);

$result = file_get_contents($url);

header("Content-Type: ".$header);

echo $result;