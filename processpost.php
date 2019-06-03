<?php
require_once("core.php");

require_once("lib/htmlpurifier/HTMLPurifier.auto.php");
$purifier = new HTMLPurifier();

if (!isset($_POST["id"]) || empty($_POST["id"]) || !isset($_POST["category"]) || empty($_POST["category"]) || !isset($_POST["title"]) || empty($_POST["title"]) || !isset($_POST["type"]) || !in_array($_POST["type"], array("question", "discussion", "announcement"))) {
	header("Location: index.php");
	exit();
}

$forum = get_forum($_POST["id"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("post", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

$category = get_category($_POST["category"], false, $forum["id"]);
if ($category === false) {
	header("Location: index.php");
	exit();
}

if (!isset($_POST["message"])) {
header("Location: index.php");
	exit();
}

$title = mysqli_real_escape_string($con, htmlspecialchars($_POST["title"]));
$type = mysqli_real_escape_string($con, $_POST["type"]);

if ($type == "announcement" && get_permission("announce", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

if (get_permission("rich", $forum["id"]) === true) {
	$message = mysqli_real_escape_string($con, $purifier->purify($_POST["message"]));
	if (empty($message)) {
		header("Location: index.php");
		exit();
	}
} else {
	if (empty($_POST["message"])) {
		header("Location: index.php");
		exit();
	}
	$message = mysqli_real_escape_string($con, nl2br(htmlspecialchars($_POST["message"]), false));
}

if (isset($_POST["lock"]) && get_permission("lock", $forum["id"]) === true) {
	$lock = "true";
} else {
	$lock = "false";
}

if (isset($_POST["pinned"]) && get_permission("pinned", $forum["id"]) === true) {
	$pinned = "true";
} else {
	$pinned = "false";
}

$files = array();

if (isset($_FILES["attachment"]) && get_permission("attach", $forum["id"]) === true) {
	$attachments = reArrayFiles($_FILES["attachment"]);
	if (count($attachments) > ini_get("max_file_uploads")) {
		die("Has adjuntado demasiados ficheros.");
	}
	foreach ($attachments as $attachment) {
		if ($attachment["error"] != 4) {
			$files[] = upload_image($attachment, "", false, false);
		}
	}
}

$files_json = json_encode($files);

$search_index_message = $title."\n".str_replace(array("\\n", "\\r"), array(" ", " "), strip_tags($message));

$time = time();

if (mysqli_query($con, "INSERT INTO posts (title, message, op, time, type, forum, category, pinned, locked, deleted, lastmodified, attachments) VALUES ('".$title."', '".$message."', ".userdata("id").", ".$time.", '".$type."', ".$forum["id"].", ".$category["id"].", ".$pinned.", ".$lock.", false, ".$time.", '".$files_json."')")) {
	$query = mysqli_query($con, "SELECT id FROM posts WHERE title = '".$title."' AND time = ".$time." AND op = ".userdata("id"));
	$row = mysqli_fetch_assoc($query);
	mysqli_query($con, "INSERT INTO search_index (message, type, data, forum, category) VALUES ('".$search_index_message."', 'post', ".$row["id"].", ".$forum["id"].", ".$category["id"].")");
	header("Location: thread.php?id=".$row["id"]);
} else {
	die("No se ha podido publicar el post. Vuelve a intentar de nuevo en unos minutos.");
}
