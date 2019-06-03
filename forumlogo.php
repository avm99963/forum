<?php
require_once("core.php");
if (!isset($_GET["id"]) || empty($_GET["id"])) {
	exit();
}
$forum = get_forum($_GET["id"]);
if ($forum === false) {
	exit();
}
if (get_permission("view", $forum["id"]) === false) {
	exit();
}

if (!empty($forum["logo"])) {
	$url = "uploaded_img/".$forum["logo"];
	$header = _mime_content_type($url);
} else {
	$url = "logo.svg";
	$header = "image/svg+xml";
}

$result = file_get_contents($url);

header("Content-Type: ".$header);

echo $result;