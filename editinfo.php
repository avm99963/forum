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
		<script src="lib/ckeditor/ckeditor.js"></script>
		<script>
		window.addEventListener("load", function() {
			["welcome", "rich_template"].forEach(function(editor) {
				CKEDITOR.replace(editor, {
					language: "es",
					width: 750,
					filebrowserImageUploadUrl: 'ajax/uploadimage.php'
				});
			});
		});
        </script>
        <?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header("forum.php?id=".$_GET["id"], $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > Información general
				</div>
				<h2>Información general</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "nameunique")
						echo "<div class='alert danger'>Ya existe un Foro con ese nombre código.</div>";
					if ($_GET["msg"] == "success")
						echo "<div class='alert success'>La información del Foro se ha editado correctamente.</div>";
					if ($_GET["msg"] == "characters")
						echo "<div class='alert danger'>El nombre código solo puede contener letras minúsculas, espacios, guiones y guiones bajos.</div>";
				}
				?>
				<form action="processeditinfo.php" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?=$forum["codename"]?>">
					<p><label for="form_name">Nombre:</label> <input type="text" name="name" id="form_name" value="<?=$forum["name"]?>" required></p>
					<p><label for="codename">Nombre código:</label> <input type="text" name="codename" id="codename" value="<?=$forum["codename"]?>" required> <span class="tooltip" data-title="El nombre código es una cadena de caracteres que se mostrará en las URLs pertenecientes a este Foro. Puede incluir letras minúsculas, números, espacios, guiones y guiones bajos."><img src="img/help.png"></span></p>
					<p><label for="description">Descripción:</label><br><textarea name="description" id="description"><?=$forum["description"]?></textarea></p>
					<p><label for="welcome">Mensaje de bienvenida:</label><br><textarea name="welcome" id="welcome"><?=$forum["welcome"]?></textarea></p>
					<p><label for="plain_template">Plantilla editor texto plano:</label><br><textarea name="plain_template" id="plain_template" class="large"><?=$forum["plain_template"]?></textarea></p>
					<p><label for="rich_template">Plantilla editor formato enriquecido:</label><br><textarea name="rich_template" id="rich_template"><?=$forum["rich_template"]?></textarea></p>
					<p><input type="checkbox" id="levels" name="levels"<?=(($forum["levels"] == true) ? " checked" : "")?>> <label for="levels">Activar puntos y niveles</label></p>
					<p><label for="logo">Logo:</label> <input type="file" name="logo" id="logo" accept=".gif,.jpg,.jpeg,.png"></p>
					<p><input type="submit" value="Editar"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>