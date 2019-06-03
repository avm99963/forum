<?php
require("core.php");

if (!isset($_GET["id"])) {
	exit();
}

if ($_GET["id"] < 1) {
	exit();
}

// File and new size
if (!empty(userdata("avatar", $_GET["id"]))) {
	$filename = "uploaded_img/".userdata("avatar", $_GET["id"]);
} else {
	$id = (int)$_GET["id"];
	$image = $id % 19;
	$filename = "img/profile/default".$image.".jpg";
}

$header = _mime_content_type($filename);

$result = file_get_contents($filename);

header("Content-Type: ".$header);

echo $result;