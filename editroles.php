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
			<?php print_header("editroles.php?id=".$_GET["id"], $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > Roles
				</div>
				<h2>Roles</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe una categoría con ese nombre código.</div>";
					if ($_GET["msg"] == "added")
						echo "<div class='alert success'>Se ha añadido el rol correctamente.</div>";
					if ($_GET["msg"] == "edit")
						echo "<div class='alert success'>Se ha editado el rol correctamente.</div>";
					if ($_GET["msg"] == "delete")
						echo "<div class='alert success'>Se ha eliminado el rol correctamente.</div>";
				}
				?>
				<p><i class="material-icons middle">add</i> <a href="newrole.php?id=<?=$forum["codename"]?>">Añadir rol</a></p>
				<?php
				$roles = get_roles($forum["id"]);
				if ($roles === false) {
				?>
				<p>No existe ningún rol.</p>
				<?php
				} else {
					foreach ($roles as $role) {
						if (empty($role["badge"])) {
							$badge = "";
						} else {
							$badge = "<img class='rolebadge' src='badge.php?id=".$role["id"]."'>";
						}
						?>
						<div class="category">
							<div class="title"><?=$role["name"]?> <?=$badge?> <a class="orange" href="editrole.php?forum=<?=$forum["codename"]?>&role=<?=$role["id"]?>"><i class="material-icons middle">edit</i></a> <a class="red" href="deleterole.php?forum=<?=$forum["codename"]?>&role=<?=$role["id"]?>"><i class="material-icons red middle">delete</i></a> <a class="blue" href="assignrole.php?forum=<?=$forum["codename"]?>&role=<?=$role["id"]?>"><i class="material-icons red middle">people</i></a></div>
							<?php if (!empty($role["description"])) { ?>
								<div class="description"><?=$role["description"]?></div>
							<?php } ?>
						</div>
						<?php
					}
				}
				?>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>