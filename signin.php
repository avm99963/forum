<?php
require_once("core.php");
require_once("includes.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Iniciar sesión</title>
		<link rel="stylesheet" href="css/form_design.css">
		<script>
		window.addEventListener("load", function(){
			document.querySelector("#cookies").hidden = navigator.cookieEnabled;
		});
		</script>
		<?php print_head(); ?>
	</head>
	<body>
		<main>
			<h1>Iniciar sesión</h1>
			<?php
			if (isset($_GET["continue"]) && !empty($_GET["continue"])) {
				$continue_string = "?continue=".urlencode($_GET["continue"]);
			} else {
				$continue_string = "";
			}
			if (isset($_GET["msg"])) {
				if ($_GET["msg"] == "loginwrong")
					echo "<div class='alert danger'>Los datos de inicio de sesión no son correctos.</div>";
			}
			?>
			<div id="cookies" class="alert warning" hidden>No tienes las cookies activadas. Actívalas para poder iniciar sesión correctamente.</div>
			<form method="POST" action="login.php">
				<p><label for="username">Usuario:</label> <input type="text" name="username" id="username" required></p>
				<p><label for="password">Contraseña:</label> <input type="password" name="password" id="password" required></p>
				<?php
				if (isset($_GET["continue"]) && !empty($_GET["continue"])) {
				?>
				<input type="hidden" name="continue" value="<?=$_GET["continue"]?>">
				<?php
				}
				?>
				<p><input type="submit" value="Iniciar sesión"></p>
				<?php
				if ($CONF["registerFilter"] === true) {
				?>
				<p class="small_hint">¿Eres nuevo al Foro? <a href="signup.php<?=$continue_string?>">Regístrate</a></p>
				<?php
				}
				?>
			</form>
		</main>
	</body>
</html>