<?php
require_once("core.php");
if (!isset($_GET["type"]) || !in_array($_GET["type"], array("post", "reply")) || !isset($_GET["post"]) || empty($_GET["post"]) || !isset($_GET["file"])) {
	exit();
}
$file = (int)$_GET["file"];
if ($_GET["type"] == "reply") {
	$message = get_reply($_GET["post"]);
	$post = get_post($message["post"]);
} else {
	$message = get_post($_GET["post"]);
	$post = $message;
}
if ($message === false) {
	exit();
}
if (get_permission("view", $post["forum"]) === false) {
	exit();
}

$files = json_decode($message["attachments"], true);

if (!isset($files[$file])) {
	exit();
}

$url = "uploaded_img/".$files[$file];

$header = _mime_content_type($url);

$result = file_get_contents($url);

if (explode("/", $header)[0] != "image" && $header != "application/pdf") {
	$extension = explode(".", $files[$file]);
	$extension = end($extension);
	header("Content-Disposition: attachment; filename=\"attachment_".$file.".".$extension."\"");
}

header("Content-Type: ".$header);
	echo $result;
