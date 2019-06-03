<?php
require_once('core.php');
$username = mysqli_real_escape_string($con, $_POST['username']);
$password = $_POST['password'];
if (empty($username) || empty($password)) {
	header("Location: signin.php");
	echo "Please fill in all form.";
} else {
	$query = mysqli_query($con, "SELECT * FROM users WHERE username='".$username."'");
	if (mysqli_num_rows($query)) {
		$row = mysqli_fetch_assoc($query) or die(mysqli_error($con));
		if (password_verify($password, $row["password"])) {
			$_SESSION['id'] = $row['id'];
			if (isset($_POST['continue']) && !empty($_POST['continue'])) {
				header("Location: ".$_POST['continue']);
			} else {
				header("Location: index.php");
			}
		} else {
			header("Location: signin.php?msg=loginwrong".((isset($_POST['continue']) && !empty($_POST['continue'])) ? "&continue=".$_POST['continue'] : ""));
			echo "User data incorrect :-(";
		}
	} else {
		header("Location: signin.php?msg=loginwrong".((isset($_POST['continue']) && !empty($_POST['continue'])) ? "&continue=".$_POST['continue'] : ""));
		echo "User data incorrect :-(";
	}
}