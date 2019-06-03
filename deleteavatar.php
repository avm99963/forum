<?php
require_once("core.php");
if (!loggedin()) {
	exit();
}

delete_image(userdata("avatar"));

if (mysqli_query($con, "UPDATE users SET avatar='' WHERE id=".userdata("id"))) {
	header("Location: settings.php?msg=saved");
} else {
	die("Mysql error");
}