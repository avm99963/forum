<?php
require_once("../core.php");
if (!isset($_POST["role"]) || empty($_POST["role"]) || !isset($_POST["people"]) || empty($_POST["people"])) {
	$return["errorCode"] = 1;
	$return["errorText"] = "No se pasaron todos los atributos";
	echo json_encode($return);
	exit();
}

$role = get_role($_POST["role"]);
if ($role === false) {
	$return["errorCode"] = 1;
	$return["errorText"] = "No existe este rol";
	echo json_encode($return);
	exit();
}

$forum = get_forum($role["forum"], true);
if ($forum === false) {
	$return["errorCode"] = 3;
	$return["errorText"] = "Hakuna Matata! Qué bonito es vivir...";
	echo json_encode($return);
	exit();
}

if (get_permission("admin", $forum["id"]) === false) {
	$return["errorCode"] = 4;
	$return["errorText"] = "No tienes permisos para realizar esta acción";
	echo json_encode($return);
	exit();
}

$people = explode(",", mysqli_real_escape_string($con, $_POST["people"]));

$query = mysqli_query($con, "SELECT * FROM assigned_roles WHERE role = ".$role["id"]);

$already_invited = array();

if (mysqli_num_rows($query)) {
	for ($i = 0; $i < mysqli_num_rows($query); $i++) {
		$row = mysqli_fetch_assoc($query);
		$already_invited[] = $row["user"];
	}
}

$rows = mysqli_num_rows($query);

foreach ($people as $person) {
	if (!in_array($person, $already_invited)) {
		if ($person == userdata("id", $person)) {
			if (!mysqli_query($con, "INSERT INTO assigned_roles (user, role, forum) VALUES (".$person.", ".$role["id"].", ".$forum["id"].")")) {
				$return["errorCode"] = 2;
				$return["errorText"] = "No se pudo asignar al rol a uno o más foreros";
				echo json_encode($return);
				exit();
			}
		}
	}
}

$return["status"] = "assigned";

echo json_encode($return);