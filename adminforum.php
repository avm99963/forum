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
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=urlencode($forum["codename"])?>"><?=$forum["name"]?></a> > Administrar el Foro
				</div>
				<h2>Administrar el Foro</h2>
				<ul>
					<li><a href="editinfo.php?id=<?=urlencode($forum["codename"])?>">Información general</a></li>
					<li><a href="editcategories.php?id=<?=urlencode($forum["codename"])?>">Categorías</a></li>
					<li><a href="editroles.php?id=<?=urlencode($forum["codename"])?>">Roles</a></li>
					<li><a href="editpermissions.php?id=<?=urlencode($forum["codename"])?>">Permisos</a></li>
				</ul>
				<p style="color: gray; font-size: 12px;">ID: <?=$forum["id"]?></p>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>