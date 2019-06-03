<?php
require_once("core.php");
if (!isset($_POST["role"]) || empty($_POST["role"])) {
	header("Location: index.php");
	exit();
}

$role = get_role($_POST["role"], $forum["id"]);
if ($role === false) {
	header("Location: index.php");
	exit();
}

$forum = get_forum($role["forum"], true);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

if (mysqli_query($con, "DELETE FROM roles WHERE id = ".$role["id"]." LIMIT 1")) {
	header("Location: editroles.php?id=".urlencode($forum["codename"])."&msg=delete");
} else {
	echo "No se ha podido eliminar el rol debido a un error :-/";
}