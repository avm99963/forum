<?php
require_once("core.php");
if (!isset($_POST["id"]) || empty($_POST["id"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_POST["id"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}

$default_roles = array("guest", "user");
$newpermissions = array();

foreach ($default_roles as $role) {
	$newpermissions[$role] = $MASTER["default_permissions"];
	if ($role == "guest") {
		$pseudo_master = $MASTER["guest_allowed_permissions"];
	} else {
		$pseudo_master = $MASTER["permissions"];
	}
	foreach ($pseudo_master as $permission) {
		if (isset($_POST[$role.",".$permission])) {
			$newpermissions[$role][$permission] = 1;
		}
	}
}

$newrolepermissions = array();

$roles = get_roles($forum["id"]);

foreach ($roles as $role) {
	$newrolepermissions[$role["id"]] = $MASTER["default_permissions"];
	foreach ($MASTER["permissions"] as $permission) {
		if (isset($_POST[$role["id"].",".$permission])) {
			$newrolepermissions[$role["id"]][$permission] = 1;
		}
	}
}

$guestpermissions = mysqli_real_escape_string($con, json_encode($newpermissions["guest"]));
$userpermissions = mysqli_real_escape_string($con, json_encode($newpermissions["user"]));

if (mysqli_query($con, "UPDATE forums SET guest_permissions = '".$guestpermissions."', user_permissions='".$userpermissions."' WHERE id=".$forum["id"])) {
	foreach ($newrolepermissions as $id => $newrolepermission) {
		if (!mysqli_query($con, "UPDATE roles SET permissions='".mysqli_real_escape_string($con, json_encode($newrolepermission))."' WHERE id=".$id)) {
			die("¡No se han podido cumplir las órdenes, sargento!");
		}
	}
	header("Location: editpermissions.php?id=".urlencode($forum["codename"])."&msg=success");
} else {
	die("¡No se han podido cumplir las órdenes, sargento!");
}