<?php
require_once("core.php");
if (!isset($_POST["role"]) || empty($_POST["role"])) {
	header("Location: index.php");
	exit();
}
$role = get_role($_POST["role"]);
if ($role === false) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($role["forum"], true);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (userdata("id", $_POST["user"]) === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

$query = mysqli_query($con, "SELECT id FROM assigned_roles WHERE user = ".(int)$_POST["user"]." AND role = ".(int)$_POST["role"]);

if (mysqli_num_rows($query)) {
	$row = mysqli_fetch_assoc($query);

	if (mysqli_query($con, "DELETE FROM assigned_roles WHERE id = ".$row["id"]." LIMIT 1")) {
		header("Location: assignrole.php?forum=".urlencode($forum["codename"])."&role=".urlencode($role["id"]));
	} else {
		die("¡No se han podido cumplir las órdenes, sargento!");
	}
}