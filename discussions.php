<?php
require_once("core.php");
require_once("includes.php");
if (!isset($_GET["forum"]) || empty($_GET["forum"])) {
	header("Location: index.php");
	exit();
}
$forum = get_forum($_GET["forum"]);
if ($forum === false) {
	header("Location: index.php");
	exit();
}
if (get_permission("view", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
if (isset($_GET["category"])) {
	$category = get_category($_GET["category"], false, $forum["id"]);
	if ($category === false) {
		header("Location: index.php");
		exit();
	}
	$breadcrumb_name = $category["name"];
	$page_name = $category["name"];
} else {
	$breadcrumb_name = "Todos los debates";
	$page_name = $forum["name"];
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?=$forum["name"]?> – <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<link rel="stylesheet" href="css/discussions.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, $forum["name"], (isset($_GET["category"]) ? "category" : "forum"), $forum["codename"], (isset($_GET["category"]) ? $category["codename"] : null), "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php
			if (isset($_GET["category"])) {
				print_aside("forum", $forum["codename"], $category["codename"]);
			} else {
				print_aside("forum", $forum["codename"]);
			}
			?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=urlencode($forum["codename"])?>"><?=$forum["name"]?></a> > <?=$breadcrumb_name?>
				</div>
				<h2><?=$page_name?></h2>
				<?php
				if (isset($_GET["page"])) {
					$page = (int)$_GET["page"];
					if ($page < 1) {
						header("Location: index.php");
						exit();
					}
				} else {
					$page = 1;
				}
				if (isset($_GET["category"])) {
					$posts = get_posts($page, $forum["codename"], $category["codename"]);
				} else {
					$posts = get_posts($page, $forum["codename"]);
				}
				if (!count($posts["posts"])) {
					?>
					<p>Todavía no hay ningún debate aquí. <?php if (get_permission("post", $forum["id"]) === true) { ?>¡Inicia el primer debate!<?php } ?></p>
					<?php
				} else {
					?>
					<table>
						<thead>
							<tr>
								<th></th>
								<th>Tema</th>
								<th>Pregunta formulada por</th>
								<th>Respuestas</th>
								<th>Última contestación</th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($posts["posts"] as $post) {
						/*$roles = get_user_roles($forum["id"], $post["op"]);
						$role_op = "";
						foreach ($roles as $role) {
							$role_full = get_role($role);
							if (!empty($role_full["badge"])) {
								$role_op .= "<img src='badge.php?id=".$role."'>";
							}
						}*/
						$role_op = get_display_roles($forum["id"], userdata("id", $post["op"]))["badges"];
						?>
						<tr>
							<td><?php if ($post["pinned"] == true) { ?><img src="img/sticky.png"><?php } ?></td>
							<td><a href="thread.php?id=<?=$post["id"]?>"><?=$post["title"]?></a></td>
							<td><div><?=better_time($post["time"])?></div><div class="userlink"><a href="user.php?id=<?=$post["op"]?>"><?=userdata("username", $post["op"])?></a> <?=$role_op?></div></td>
							<td><?=$post["answers"]?></td>
							<td><?=((isset($post["lastanswer"])) ? better_time($post["lastanswer"]["time"]) : "--")?></td>
						</tr>
						<?php
					}
				?>
					</tbody>
				</table>
				<?php
				if ($posts["count"] > $MASTER["page_length"]) {
					$pages = ceil($posts["count"] / $MASTER["page_length"]);
					$looptimes = 0;
					$lastprinted = 0;
					?>
					<div class="pages">
					<?php
					if ($page > 6) {
						?>
						<span>...</span>
						<?php
					}

					if (isset($_GET["category"])) {
						$addcategory = "&category=".urlencode($category["codename"]);
					} else {
						$addcategory = "";
					}

					for ($i = (($page > 5) ? ($page - 5) : 1); ($i <= $pages); $i++) {
						$looptimes++;
						if ($i == $page) {
							echo "<span class='actual'>".$i."</span>";
						} else {
							echo "<a href='discussions.php?forum=".urlencode($forum["codename"]).$addcategory."&page=".$i."'>".$i."</a>";
						}
						$lastprinted = $i;
						if ($looptimes == 11) {
							break;
						}
					}

					if ($lastprinted != $pages) {
						?>
						<span>...</span>
						<?php
					}

					if ($page > 1) {
						?>
						<a href="discussions.php?forum=<?=urlencode($forum["codename"])?><?=$addcategory?>&page=<?=($page - 1)?>">&lt; Anterior</a>
						<?php
					}

					if ($page < $pages) {
						?>
						<a href="discussions.php?forum=<?=urlencode($forum["codename"])?><?=$addcategory?>&page=<?=($page + 1)?>">Siguiente &gt;</a>
						<?php
					}
					?>
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
