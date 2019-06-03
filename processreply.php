<?php
require_once("core.php");

require_once("lib/htmlpurifier/HTMLPurifier.auto.php");
$purifier = new HTMLPurifier();

if (!isset($_POST["post"]) || empty($_POST["post"])) {
	header("Location: index.php");
	exit();
}

$post = get_post($_POST["post"]);
if ($post === false) {
	header("Location: index.php");
	exit();
}

$forum = get_forum($post["forum"], true);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("post", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

if (get_permission("rich", $forum["id"]) === true) {
	$message = mysqli_real_escape_string($con, $purifier->purify($_POST["message"]));
	if (empty($message)) {
		header("Location: thread.php?id=".urlencode($post["id"])."&msg=emptyreply");
		exit();
	}
} else {
	if (empty($_POST["message"])) {
		header("Location: thread.php?id=".urlencode($post["id"])."&msg=emptyreply");
		exit();
	}
	$message = mysqli_real_escape_string($con, nl2br(htmlspecialchars($_POST["message"]), false));
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

$search_index_message = str_replace(array("\\n", "\\r"), array(" ", " "), strip_tags($message));

$time = time();

if (mysqli_query($con, "INSERT INTO replies (message, p, time, post, deleted, attachments) VALUES ('".$message."', ".userdata("id").", ".$time.", ".$post["id"].", false, '".$files_json."')")) {
	$query = mysqli_query($con, "SELECT id FROM replies WHERE message = '".$message."' AND time = ".$time." AND p = ".userdata("id"));
	$row = mysqli_fetch_assoc($query);
	mysqli_query($con, "INSERT INTO search_index (message, type, data, forum, category) VALUES ('".$search_index_message."', 'reply', ".$row["id"].", ".$post["forum"].", ".$post["category"].")");
	mysqli_query($con, "UPDATE posts SET lastmodified = ".$time." WHERE id = ".$post["id"]);
	modify_points("postanswer", $forum["codename"]);
	header("Location: thread.php?id=".$post["id"]);
} else {
	die("No se ha podido publicar la respuesta. Vuelve a intentar de nuevo en unos minutos.");
}
