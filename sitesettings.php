<?php
require_once("core.php");
require_once("includes.php");
if (!isadmin()) {
	exit();
}

$avatar = userdata("avatar");

$url = get_current_path();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Configuración de los Foros - <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<script src="lib/ckeditor/ckeditor.js"></script>
		<script>
		window.addEventListener("load", function() {
			["aside", "footer"].forEach(function(editor) {
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
			<?php print_header(null, "Configuración de los Foros", "all"); ?>
		</header>
		<div id="container">
			<?php print_aside("sitesettings"); ?>
			<section class="alone">
				<h2>Configuración de los Foros</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "saved")
						echo "<div class='alert success'>Se ha guardado la configuración correctamente.</div>";
				}
				?>
				<form action="processsitesettings.php" method="POST" enctype="multipart/form-data">
					<h3>Textos personalizados</h3>
					<p><label for="aside">Barra lateral:</label><br><textarea name="aside" id="aside"><?=get_site_setting("aside")?></textarea></p>
					<p><label for="footer">Pie de página:</label><br><textarea name="footer" id="footer"><?=get_site_setting("footer")?></textarea></p>
					<p><input type="submit" value="Guardar"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(true); ?>
	</body>
</html>