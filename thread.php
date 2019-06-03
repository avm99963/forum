<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["id"]) || empty($_GET["id"])) {
	header("Location: index.php");
	exit();
}
$post = get_post($_GET["id"]);
if ($post === false) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($post["forum"], true);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("view", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
$category = get_category($post["category"], true, $forum["id"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
$op_role = get_display_roles($forum["id"], userdata("id", $post["op"]));
$replies = get_replies($post["id"]);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?=$post["title"]?> – <?=$forum["name"]?> – <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<link rel="stylesheet" href="css/thread.css">
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
			<?php print_aside("forum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="discussions.php?forum=<?=$forum["codename"]?>&category=<?=$category["codename"]?>"><?=$category["name"]?></a> > <?=$post["title"]?>
				</div>
				<h2 class="title"><?=$post["title"]?></h2>
				<?php
				$attachments = json_decode($post["attachments"], true);
				print_message($post, $op_role, $attachments, "post");

				if (count($replies["ba"])) {
					?>
					<h3 class="section">Mejores respuestas</h3>
					<?php
				}
				?>
				<h3 class="section">Respuestas (<?=count($replies["all"])?>)</h3>
				<?php
				if (count($replies["all"])) {
					?>
					<div class="replies">
					<?php
					foreach ($replies["all"] as $reply) {
						$role = get_display_roles($forum["id"], userdata("id", $reply["p"]));
						$attachments = json_decode($reply["attachments"], true);
						print_message($reply, $role, $attachments, "reply");
					}
					?>
					</div>
					<?php
				} else {
					?>
					<p>Este debate no tiene ninguna respuesta. <?php if (get_permission("post", $forum["id"]) && $post["locked"] != true) { ?>¡Únete a él!<?php } ?></p>
					<?php
				}
				$permuser = json_decode($forum["user_permissions"], true);
				if (get_permission("post", $forum["id"])) {
					?>
					<h3 class="section">Publicar una respuesta</h3>
					<?php
					if (isset($_GET["msg"])) {
						if ($_GET["msg"] == "emptyreply")
							echo "<p class='alert warning'>Has enviado una respuesta vacía.</p>";
					}
					if ($post["locked"] == true) {
						?>
						<p>Este debate está cerrado y ya no acepta más respuestas.</p>
						<?php
					} else {
						?>
						<form action="processreply.php" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="post" value="<?=$post["id"]?>">
							<p><textarea class="large" name="message" id="message"></textarea></p>
							<?php
							if (get_permission("attach", $forum["id"])) {
								?>
								<div id="attachments">
									<p><span id="attach_file"><img src="img/attach.png"> <span id="attach_text">Adjuntar un archivo</span></span></p>
									<div id="attachments"></div>
								</div>
								<?php
							}
							?>
							<p><input type="submit" value="Publicar"></p>
						</form>
						<?php
					}
				} elseif (!loggedin() && $permuser["post"] == true && $post["locked"] !== true) {
					?>
					<h3 class="section">Publicar una respuesta</h3>
					<p><a href="signin.php<?="?continue=".urlencode(get_current_url())?>">Accede</a> para responder a esta pregunta.</p>
					<?php
				}
				?>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>
