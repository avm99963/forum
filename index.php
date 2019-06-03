<?php
if (!file_exists("config.php")) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Instala los Foros antes</title>
		<style>
		body {
			font-family: 'Roboto', sans-serif;
		}
		#main {
			max-width: 400px;
			margin-left: auto;
			margin-right: auto;
			text-align: center;
		}
		</style>
	</head>
	<body>
		<div id="main">
			<h1>Instala los Foros</h1>
			<p>Antes de utilizar los Foros, tienes que instalar la aplicación en tu servidor. Haz clic en el siguiente enlace para empezar la instalación:</p>
			<p><a href="install.php">Instalar</a></p>
		</div>
	</body>
</html>
<?php
exit();
}
require_once("core.php");
require_once("includes.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, $CONF["appname"], "all"); ?>
		</header>
		<div id="container">
			<?php
			print_aside("topiclist");
			if (loggedin()) {
				print_meter();
				$alone = "";
			} else {
				$alone = " class='alone'";
			}
			?>
			<section<?=$alone?>>
				<h2><?=$CONF["appname"]?></h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "registered")
						echo "<div class='alert success'>Te has registrado satisfactoriamente. Ahora ya puedes iniciar sesión con tus credenciales.</div>";
				}
				$forums = get_forums();
				if (!count($forums) || $forums === false) {
				?>
				<p>Todavía no hay ningún Foro. <?php if (isadmin()) { ?><a href="newforum.php">Crea uno</a><?php } ?></p>
				<?php
				} else {
					foreach ($forums as $forum) {
						?>
						<div class="forum">
							<div class="title"><a href="forum.php?id=<?=urlencode($forum["codename"])?>"><?=$forum["name"]?></a></div>
							<?php if (!empty($forum["description"])) { ?>
								<div class="description"><?=$forum["description"]?></div>
							<?php } ?>
							<div class="discussions"><?=$forum["discussions"]?> <?=(($forum["discussions"] == 1) ? "debate" : "debates")?></div>
						</div>
						<?php
					}
					?>
					<!--<div class="all_discussions"><a href="discussions.php">Examinar todos los hilos »</a></div>-->
					<?php
				}
				?>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>