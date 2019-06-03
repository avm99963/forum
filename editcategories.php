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
		<style>
		.red:link, .red:visited {
			color: rgb(219, 68, 55);
		}

		.orange:link, .orange:visited {
			color: rgb(244, 180, 0);
		}
		</style>
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header("editcategories.php?id=".$_GET["id"], $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > Categorías
				</div>
				<h2>Categorías</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe una categoría con ese nombre código.</div>";
					if ($_GET["msg"] == "added")
						echo "<div class='alert success'>Se ha añadido la categoría correctamente.</div>";
					if ($_GET["msg"] == "edit")
						echo "<div class='alert success'>Se ha editado la categoría correctamente.</div>";
				}
				?>
				<p><i class="material-icons middle">add</i> <a href="newcategory.php?id=<?=$forum["codename"]?>">Añadir categoría</a></p>
				<?php
				$categories = get_categories($forum["id"]);
				if ($categories === false) {
				?>
				<p>No hay ninguna categoría.</p>
				<?php
				} else {
					$count = count($categories);
					foreach ($categories as $i => $category) {
						?>
						<div class="category">
							<div class="title"><?=$category["name"]?> <a class="orange" href="editcategory.php?forum=<?=$forum["codename"]?>&category=<?=$category["codename"]?>"><i class="material-icons middle">edit</i></a> <a class="red" href="deletecategory.php?forum=<?=$forum["codename"]?>&category=<?=$category["codename"]?>"><i class="material-icons middle">delete</i></a><?php if ($i != ($count - 1)) { ?><a class="blue" href="processmovecategory.php?forum=<?=$forum["codename"]?>&category=<?=$category["codename"]?>&move=up"><i class="material-icons middle">keyboard_arrow_down</i></a><?php } ?><?php if ($i != 0) { ?><a class="blue" href="processmovecategory.php?forum=<?=$forum["codename"]?>&category=<?=$category["codename"]?>&move=down"><i class="material-icons middle">keyboard_arrow_up</i></a><?php } ?></div>
							<?php if (!empty($category["description"])) { ?>
								<div class="description"><?=$category["description"]?></div>
							<?php } ?>
							<div class="discussions">Nombre código: <?=$category["codename"]?> | ID: <?=$category["id"]?></div>
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