<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["forum"]) || empty($_GET["forum"]) || !isset($_GET["category"]) || empty($_GET["category"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_GET["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
$category = get_category($_GET["category"], false, $forum["id"]);
if ($category === false) {
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
			<?php print_header("editcategory.php?id=".$_GET["forum"], $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > <a href="editcategories.php?id=<?=$forum["codename"]?>">Categorías</a> > Editar categoría
				</div>
				<h2>Editar categoría</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe una categoría con ese nombre código.</div>";
					if ($_GET["msg"] == "characters")
						echo "<div class='alert danger'>El nombre código solo puede contener letras minúsculas, espacios, guiones y guiones bajos.</div>";
				}
				?>
				<form action="processeditcategory.php" method="POST">
					<input type="hidden" name="forum" value="<?=$_GET["forum"]?>">
					<input type="hidden" name="category" value="<?=$_GET["category"]?>">
					<p><label for="forum_name">Nombre:</label> <input type="text" name="name" id="forum_name" value="<?=$category["name"]?>" required></p>
					<p><label for="codename">Nombre código:</label> <input type="text" name="codename" id="codename" value="<?=$category["codename"]?>" required> <span class="tooltip" data-title="El nombre código es una cadena de caracteres que se mostrará en las URLs pertenecientes a esta categoría. Puede incluir letras minúsculas, números, espacios, guiones y guiones bajos."><img src="img/help.png"></span></p>
					<p><label for="description">Descripción:</label><br><textarea name="description" id="description"><?=$category["description"]?></textarea></p>
					<p><input type="submit" value="Editar"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>