<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["forum"]) || empty($_GET["forum"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_GET["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
$role = get_role($_GET["role"], $forum["id"]);
if ($role === false) {
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
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > <a href="editroles.php?id=<?=$forum["codename"]?>">Roles</a> > Eliminar rol
				</div>
				<h2>Eliminar rol</h2>
				<p>Estás a punto de eliminar el rol <b><?=$role["name"]?></b>. ¿Estás seguro de que quieres continuar? Esta acción es irreversible.</p>
				<form action="processdeleterole.php" method="POST">
					<input type="hidden" name="role" value="<?=$role["id"]?>">
					<p><a href="editroles.php?id=<?=$forum["codename"]?>" class="donotdelete">¡No!</a> | <input type="submit" value="Sí"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>