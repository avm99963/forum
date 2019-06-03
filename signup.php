<?php
require_once("core.php");
require_once("includes.php");
if ($CONF["registerFilter"] === false) {
	header("Location: index.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Crear una cuenta</title>
		<link rel="stylesheet" href="css/form_design.css">
		<?php print_head(); ?>
	</head>
	<body>
		<main>
			<h1>Crear una cuenta</h1>
			<?php
			if (isset($_GET["msg"])) {
				if ($_GET["msg"] == "recaptcha")
					echo "<div class='alert danger'>Asegúrate de rellenar el recaptcha.</div>";
				if ($_GET["msg"] == "emailincorrect")
					echo "<div class='alert danger'>Introduce una dirección de correo electrónico válido.</div>";
				if ($_GET["msg"] == "password")
					echo "<div class='alert danger'>La contraseña debe contener como mínimo 6 caracteres.</div>";
				if ($_GET["msg"] == "usernametaken")
					echo "<div class='alert danger'>Ese usuario ya está siendo usado por algún usuario.</div>";
				if ($_GET["msg"] == "emailregistered")
					echo "<div class='alert danger'>Esta dirección de correo electrónico ya está siendo usada por algún usuario.</div>";
			}
			?>
			<form method="POST" action="register.php">
				<p><label for="username">Usuario:</label> <input type="text" name="username" id="username" required></p>
				<p><label for="password">Contraseña:</label> <input type="password" name="password" id="password" required></p>
				<p><label for="email">Correo electrónico:</label> <input type="email" name="email" id="email" required></p>
				<?php
				if ($CONF["recaptcha"]["use"] === true) {
				?>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
				<div class="g-recaptcha" data-sitekey="<?=$CONF["recaptcha"]["siteKey"]?>"></div>
				<?php
				}
				if (isset($_GET["continue"]) && !empty($_GET["continue"])) {
				?>
				<input type="hidden" name="continue" value="<?=$_GET["continue"]?>">
				<?php
				}
				?>
				<p><input type="submit" value="Regístrate"></p>
			</form>
		</main>
	</body>
</html>