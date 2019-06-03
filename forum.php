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
if (get_permission("view", $forum["id"]) === false) {
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
			<?php print_aside("forum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <?=$forum["name"]?>
				</div>
				<?php
				if (!empty($forum["welcome"])) {
					echo "<div class='forum_welcome'>".$forum["welcome"]."</div>";
				}
				?>
				<h2>Categorías de debate</h2>
				<?php
				$categories = get_categories($forum["id"]);
				if ($categories === false) {
				?>
				<p>Todavía no existe ninguna categoría. <?php if (get_permission("admin", $forum["id"])) { ?><a href="adminforum.php?id=<?=urlencode($forum["codename"])?>">Administra el Foro</a><?php } ?></p>
				<?php
				} else {
					foreach ($categories as $category) {
						?>
						<div class="category">
							<div class="title"><a href="discussions.php?forum=<?=urlencode($forum["codename"])?>&category=<?=urlencode($category["codename"])?>"><?=$category["name"]?></a></div>
							<?php if (!empty($category["description"])) { ?>
								<div class="description"><?=$category["description"]?></div>
							<?php } ?>
							<div class="discussions"><?=$category["discussions"]?> <?=(($category["discussions"] == 1) ? "debate" : "debates")?></div>
						</div>
						<?php
					}
					?>
					<div class="all_discussions"><a href="discussions.php?forum=<?=urlencode($forum["codename"])?>">Examinar todos los debates »</a></div>
					<?php
				}
				?>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>