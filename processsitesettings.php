<?php
require_once("core.php");

require_once("lib/htmlpurifier/HTMLPurifier.auto.php");
$purifier = new HTMLPurifier();

if (!isadmin()) {
	exit();
}

if (isset($_POST["aside"]) && !empty($_POST["aside"])) {
	$aside = mysqli_real_escape_string($con, $purifier->purify($_POST["aside"]));
} else {
	$aside = "";
}

if (isset($_POST["footer"]) && !empty($_POST["footer"])) {
	$footer = mysqli_real_escape_string($con, $purifier->purify($_POST["footer"]));
} else {
	$footer = "";
}

mysqli_query($con, "UPDATE site_settings SET aside = '".$aside."', footer = '".$footer."' LIMIT 1") or die(mysqli_error($con));

header("Location: sitesettings.php?msg=saved");