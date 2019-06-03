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
if (get_permission("post", $forum["id"]) === false) {
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
		<?php
		if (get_permission("rich", $forum["id"]) === true) {
			?>
			<script src="lib/ckeditor/ckeditor.js"></script>
			<script>
			window.addEventListener("load", function() {
				CKEDITOR.replace("message", {
					language: "es",
					width: 750,
					filebrowserImageUploadUrl: 'ajax/uploadimage.php'
				});
			});
	        </script>
	    	<?php
		}
		if (get_permission("attach", $forum["id"]) === true) {
			?>
		    <script>
		    var max_file_uploads = <?=ini_get("max_file_uploads")?>;
		    </script>
		    <script src="js/attachments.js"></script>
		    <link rel="stylesheet" href="css/attachments.css">
		    <?php
		}
	    ?>
	    <?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("post", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=urlencode($forum["codename"])?>"><?=$forum["name"]?></a> > Publicar una pregunta
				</div>
				<h2>Publicar una pregunta</h2>
				<?php
				$categories = get_categories($forum["id"]);
				if ($categories === false) {
				?>
				<p>No puedes publicar, ya que no existe ninguna categoría. <?php if (get_permission("admin", $forum["id"])) { ?><a href="newcategory.php?id=<?=urlencode($forum["codename"])?>">¡Crea una!</a><?php } ?></p>
				<?php
				} else {
					?>
					<form action="processpost.php" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="id" value="<?=$forum["codename"]?>">
						<p><label for="title">Título:</label> <input type="text" name="title" id="title" class="large" required></p>
						<p>
							<label for="category">Categoría:</label>
							<select name="category" id="category" required>
								<option value="">---</option>
								<?php
								if (isset($_GET["category"])) {
									$category_select = $_GET["category"];
								} else {
									$category_select = "";
								}
								foreach ($categories as $category) {
									if ($category["codename"] == $category_select) {
										$selected = " selected";
									} else {
										$selected = "";
									}
									?>
									<option value="<?=$category["codename"]?>"<?=$selected?>><?=$category["name"]?></option>
									<?php
								}
								?>
							</select>
						</p>
						<p>
							<label for="type">Tipo:</label>
							<select name="type" id="type" required>
								<option value="question">Pregunta</option>
								<option value="discussion">Debate</option>
								<?php
								if (get_permission("announce", $forum["id"])) {
									?>
									<option value="announcement">Anuncio</option>
									<?php
								}
								?>
							</select>
						</p>
						<p><label for="message">Mensaje:</label><br><textarea name="message" id="message" class="large"><?=$forum[((get_permission("rich", $forum["id"]) === true) ? "rich" : "plain")."_template"]?></textarea></p>
						<?php
						if (get_permission("attach", $forum["id"])) {
							?>
							<div id="attachments">
								<p><span id="attach_file"><img src="img/attach.png"> <span id="attach_text">Adjuntar un archivo</span></span></p>
								<div id="attachments"></div>
							</div>
							<?php
						}
						if (get_permission("lock", $forum["id"])) {
							?>
							<p><input type="checkbox" name="lock" id="lock"> <label for="lock">Bloquear</label></p>
							<?php
						}
						if (get_permission("pin", $forum["id"])) {
							?>
							<p><input type="checkbox" name="pinned" id="pinned"> <label for="pinned">Poner chincheta</label></p>
							<?php
						}
						?>
						<p><input type="submit" value="Publicar"></p>
					</form>
					<?php
				}
				?>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>