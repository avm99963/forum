<?php
require_once("core.php");
require_once("includes.php");
if (!isadmin()) {
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Crear Foro - <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header("newforum.php", "Crear Foro", "all"); ?>
		</header>
		<div id="container">
			<?php print_aside("newforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > Crear Foro
				</div>
				<h2>Crear Foro</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe un Foro con ese nombre código.</div>";
					if ($_GET["msg"] == "characters")
						echo "<div class='alert danger'>El nombre código solo puede contener letras minúsculas, espacios, guiones y guiones bajos.</div>";
				}
				?>
				<form action="processnewforum.php" method="POST">
					<p><label for="forum_name">Nombre:</label> <input type="text" name="name" id="forum_name" required></p>
					<p><label for="codename">Nombre código:</label> <input type="text" name="codename" id="codename" required> <span class="tooltip" data-title="El nombre código es una cadena de caracteres que se mostrará en las URLs pertenecientes a este Foro. Puede incluir letras minúsculas, números, espacios, guiones y guiones bajos."><img src="img/help.png"></span></p>
					<p><label for="description">Descripción:</label><br><textarea name="description" id="description"></textarea></p>
					<p><input type="submit" value="Crear"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>