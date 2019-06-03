<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["role"]) || empty($_GET["role"])) {
	header("Location: index.php");
	exit();
}
$role = get_role($_GET["role"]);
if ($role === false) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($role["forum"], true);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (userdata("id", $_GET["user"]) === false) {
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
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > <a href="editroles.php?id=<?=$forum["codename"]?>">Roles</a> > <a href="assignrole.php?forum=<?=$forum["codename"]?>&role=<?=$role["id"]?>">Asignar rol</a> > Quitar el rol
				</div>
				<h2>Quitar el rol</h2>
				<p>Estás a punto de quitarle el rol <b><?=$role["name"]?></b> a <b><?=userdata("username", $_GET["user"])?></b>. ¿Estás seguro de que quieres continuar? Esta acción es irreversible.</p>
				<form action="processdeallocaterole.php" method="POST">
					<input type="hidden" name="role" value="<?=$role["id"]?>">
					<input type="hidden" name="user" value="<?=userdata("id", $_GET["user"])?>">
					<p><a href="assignrole.php?forum=<?=$forum["codename"]?>&role=<?=$role["id"]?>" class="donotdelete">¡No!</a> | <input type="submit" value="Sí"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>