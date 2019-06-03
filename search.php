<?php
require_once("core.php");
require_once("includes.php");

if (!in_array($_GET["type"], array("all", "forum", "category"))) {
	http_response_code(400);
	exit();
}

if (!isset($_GET["q"])) {
	http_response_code(400);
	exit();
}

$forum = ((isset($_GET["forum"])) ? $_GET["forum"] : "");
$category = ((isset($_GET["category"])) ? $_GET["category"] : "");

if (isset($_GET["page"])) {
	$page = (int)$_GET["page"];
	if ($page < 1) {
		header("Location: index.php");
		exit();
	}
} else {
	$page = 1;
}

$search = search_threads($_GET["q"], $page, $_GET["type"], $forum, $category);

if ($search === false) {
	header("Location: index.php");
	exit();
}

if ($_GET["type"] == "all") {
	$title = "Búsqueda de [".htmlspecialchars($_GET["q"])."] en ".$CONF["appname"];
	$logo = "logo.svg";
	$breadcrumb = "";
	$heading = $CONF["appname"];
	$url = "search.php?q=".urlencode($_GET["q"])."&type=all";
} elseif ($_GET["type"] == "forum") {
	$forum_full = get_forum($_GET["forum"]);
	$title = "Búsqueda de [".htmlspecialchars($_GET["q"])."] en ".$forum_full["name"];
	$logo = "forumlogo.php?id=".urlencode($forum_full["codename"]);
	$breadcrumb = " > <a href=\"forum.php?id=".urlencode($forum_full["codename"])."\">".$forum_full["name"]."</a>";
	$url = "search.php?q=".urlencode($_GET["q"])."&type=forum&forum=".urlencode($forum_full["codename"]);
} elseif ($_GET["type"] == "category") {
	$forum_full = get_forum($_GET["forum"]);
	$category_full = get_category($_GET["category"], false, $forum_full["id"]);
	$title = "Búsqueda de [".htmlspecialchars($_GET["q"])."] en ".$forum_full["name"];
	$logo = "forumlogo.php?id=".urlencode($forum_full["codename"]);
	$breadcrumb = " > <a href=\"forum.php?id=".urlencode($forum_full["codename"])."\">".$forum_full["name"]."</a> > <a href=\"discussions.php?forum=".urlencode($forum_full["codename"])."&category=".urlencode($category_full["codename"])."\">".$category_full["name"]."</a>";
	$url = "search.php?q=".urlencode($_GET["q"])."&type=category&forum=".urlencode($forum_full["codename"])."&category=".urlencode($category_full["codename"]);
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?=$title?> – <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<link rel="stylesheet" href="css/discussions.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, "Buscador", $_GET["type"], $forum, $category, $logo, htmlspecialchars($_GET["q"])); ?>
		</header>
		<div id="container">
			<?php
			if ($_GET["type"] == "all") {
				print_aside("topiclist");
				$logo = "logo.svg";
			} elseif ($_GET["type"] == "forum") {
				print_aside("forum", $forum_full["codename"]);
			} elseif ($_GET["type"] == "category") {
				print_aside("forum", $forum_full["codename"], $category_full["codename"]);
			}
			?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a><?=$breadcrumb?> > Buscador
				</div>
				<h2><?=$title?></h2>
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
				if (!count($search["posts"])) {
					?>
					<p>La búsqueda <b><?=htmlspecialchars($_GET["q"])?></b> no ha devuelto ningún resultado. He aquí algunas sugerencias:</p>
					<?php
					$query = mysqli_query($con, "show variables like 'ft_min%'") or die(trigger_error());
					$num = mysqli_fetch_row($query);
					$ft_min = $num[1];
					?>
					<ul>
						<li>Usa palabras con al menos <?=$ft_min?> caracteres. Las palabras con menos se ignoran.</li>
					</ul>
					<?php
				} else {
					?>
					<p><?=$search["count"]?> <?=(($search["count"] == 1) ?"resultado" : "resultados")?> devueltos en <?=(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])?> microsegundos.</p>
					<table>
						<thead>
							<tr>
								<th></th>
								<th>Tema</th>
								<th>Mensaje escrito por</th>
								<th>Respuestas</th>
								<th>Última contestación</th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($search["posts"] as $post) {
						if ($post["type"] == "reply") {
							$role = get_display_roles($post["post"]["forum"], userdata("id", $post["p"]))["badges"];
							?>
							<tr>
								<td><span style="color: gray; font-size: 10px;"><?=round($post["relevance"])?></span></td>
								<td><a href="thread.php?id=<?=$post["post"]["id"]?>#c<?=$post["id"]?>"><?=$post["post"]["title"]?></a> (respuesta #c<?=$post["id"]?>)</td>
								<td><div><?=better_time($post["time"])?></div><div class="userlink"><a href="user.php?id=<?=$post["p"]?>"><?=userdata("username", $post["p"])?></a> <?=$role?></div></td>
								<td><?=$post["post"]["answers"]?></td>
								<td><?=((isset($post["lastanswer"])) ? better_time($post["post"]["lastanswer"]["time"]) : "--")?></td>
							</tr>
							<?php
						} else {
							$role_op = get_display_roles($post["forum"], userdata("id", $post["op"]))["badges"];
							?>
							<tr>
								<td><span style="color: gray; font-size: 10px;"><?=round($post["relevance"])?></span></td>
								<td><a href="thread.php?id=<?=$post["id"]?>"><?=$post["title"]?></a></td>
								<td><div><?=better_time($post["time"])?></div><div class="userlink"><a href="user.php?id=<?=$post["op"]?>"><?=userdata("username", $post["op"])?></a> <?=$role_op?></div></td>
								<td><?=$post["answers"]?></td>
								<td><?=((isset($post["lastanswer"])) ? better_time($post["lastanswer"]["time"]) : "--")?></td>
							</tr>
							<?php
						}
					}
				?>
					</tbody>
				</table>
				<?php
				if ($search["count"] > $MASTER["page_length"]) {
					$pages = ceil($search["count"] / $MASTER["page_length"]);
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

					for ($i = (($page > 5) ? ($page - 5) : 1); ($i <= $pages); $i++) {
						$looptimes++;
						if ($i == $page) {
							echo "<span class='actual'>".$i."</span>"; 
						} else {
							echo "<a href='".$url."&page=".$i."'>".$i."</a>"; 
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
						<a href="<?=$url?>&page=<?=($page - 1)?>">&lt; Anterior</a>
						<?php
					}

					if ($page < $pages) {
						?>
						<a href="<?=$url?>&page=<?=($page + 1)?>">Siguiente &gt;</a>
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