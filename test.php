<?php
require_once("core.php");

/*for ($i = 0; $i < 1000; $i++) {
	$message = mysqli_real_escape_string($con, file_get_contents("http://loripsum.net/api/bq/ul/ol/link"));
	$title = substr(mysqli_real_escape_string($con, file_get_contents("http://loripsum.net/api/short/plaintext/1")), 100, 150);
	$search_index_message = $title."\n".str_replace(array("\\n", "\\r"), array(" ", " "), strip_tags($message));

	$time = time();

	if (mysqli_query($con, "INSERT INTO posts (title, message, op, time, type, forum, category, pinned, locked, deleted, lastmodified, attachments) VALUES ('".$title."', '".$message."', 1, ".$time.", 'debate', 7, 8, false, false, false, ".$time.", '[]')")) {
		$query = mysqli_query($con, "SELECT id FROM posts WHERE title = '".$title."' AND time = ".$time." AND op = 1");
		$row = mysqli_fetch_assoc($query);
		mysqli_query($con, "INSERT INTO search_index (message, type, data, forum, category) VALUES ('".$search_index_message."', 'post', ".$row["id"].", 7, 8)");
	} else {
		die(mysqli_error($con));
	}
}*/

//print_r(modify_points("postanswer", "chrome-es"));

print(IMAGETYPE_GIF." AND ".IMAGETYPE_JPEG." AND ".IMAGETYPE_PNG);

echo "OK";