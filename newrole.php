<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["id"]) || empty($_GET["id"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_GET["id"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?=$forum["name"]?> – <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header("newrole.php?id=".$_GET["id"], $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > <a href="editroles.php?id=<?=$forum["codename"]?>">Roles</a> > Crear rol
				</div>
				<h2>Crear rol</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe una rol con ese nombre código.</div>";
				}
				?>
				<form action="processnewrole.php" method="POST">
					<input type="hidden" name="id" value="<?=$_GET["id"]?>">
					<p><label for="forum_name">Nombre:</label> <input type="text" name="name" id="forum_name" required></p>
					<p><label for="description">Descripción:</label><br><textarea name="description" id="description"></textarea></p>
					<p><label for="level">Nivel:</label> <input type="number" name="level" id="level" min="0" max="10" value="0" required> <span class="tooltip" data-title="Los usuarios que tengan al menos este nivel se asignarán automáticamente a este rol. Si quieres asignar usuarios a este rol manualmente, deja este valor a 0."><img src="img/help.png"></span></p>
					<p><input type="checkbox" id="expert" name="expert"> <label for="expert">Experto</label></p>
					<p><input type="submit" value="Crear"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>