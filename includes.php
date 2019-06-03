<?php
require_once("core.php");
function print_header($page=null, $header=null, $search_type="all", $search_forum=null, $search_category=null, $logo="logo.svg", $searchquery=null) {
	$continue = "?continue=".urlencode(get_current_url());

	if ($search_forum == null) {
		$url = "index.php";
	} else {
		$url = "forum.php?id=".urlencode($search_forum);
	}
?>
<div id="toolbar">
<?php
if (loggedin()) {
?>
	<b><?=userdata("username")?></b> | <a href="settings.php">Configuración</a> | <a href="logout.php<?=$continue?>">Cerrar sesión</a>
<?php
} else {
?>
	<a href="signin.php<?=$continue?>">Iniciar sesión</a>
<?php
}
?>
</div>
<div id="searchbox">
	<a href="<?=$url?>"><img src="<?=$logo?>"></a>
	<form method="GET" action="search.php">
		<input type="text" name="q" maxlength="250" value="<?=$searchquery?>">
		<input type="hidden" name="type" value="<?=$search_type?>">
		<?php
		if ($search_type == "forum") {
		?>
		<input type="hidden" name="forum" value="<?=$search_forum?>">
		<?php
		} elseif ($search_type == "category") {
		?>
		<input type="hidden" name="forum" value="<?=$search_forum?>">
		<input type="hidden" name="category" value="<?=$search_category?>">
		<?php
		}
		?>
		<input type="submit" value="Buscar">
	</form>
</div>
<h1 id="name"><?=$header?></h1>
<?php
}

function print_footer($here=false) {
?>
<footer>
	<?php
	if ($here === true) {
		?>
		<div class="here">Texto personalizado del pie de página</div>
		<?php
	} else {
		echo get_site_setting("footer");
	}
	?>
	<!--<p><a href="/web/20111029121105/http://www.google.com/support?hl=es">Ayuda con otros productos de Google</a> - <a href="/web/20111029121105/http://www.google.com/support/faqs/bin/static.py?page=guide.cs&amp;guide=19559&amp;hl=es">Guía de introducción</a></p>
	<p>© 2015 Google - <a href="/web/20111029121105/http://www.google.com/intl/es/privacy.html">Política de privacidad</a> - <a href="/web/20111029121105/http://www.google.com/support/forum/tos?hl=es">Condiciones del servicio</a></p>-->
</footer>
<?php
}

function print_aside($current=null, $forum=null, $category=null) {
?>
<aside id="left_sidebar">
	<ul>
		<?php
		if ($current == "forum" || $current == "adminforum" || $current == "post") {
			$forum_full = get_forum($forum);
			if ($current == "forum") {
				?>
				<li><b>Foro</b> <img src="img/forum.png"></li>
				<?php
			} else {
				?>
				<li><a href="forum.php?id=<?=urlencode($forum_full["codename"])?>">Foro</a> <img src="img/forum.png"></li>
				<?php
			}
			if (get_permission("post", $forum_full["id"])) {
				if ($current == "post") {
					?>
					<li class="padding40"><b>Publicar una pregunta</b> <img src="img/add_post.gif"></li>
					<?php
				} else {
					if ($category != null) {
						$category_add = "&category=".urlencode($category);
					} else {
						$category_add = "";
					}
					?>
					<li class="padding40"><a href="post.php?id=<?=urlencode($forum_full["codename"]).$category_add?>">Publicar una pregunta</a> <img src="img/add_post.gif"></li>
					<?php
				}
			}
			if (get_permission("admin", $forum_full["id"])) {
				if ($current == "adminforum") {
					?>
					<li class="padding40"><b>Administrar Foro</b></li>
					<?php
				} else {
					?>
					<li class="padding40"><a href="adminforum.php?id=<?=urlencode($forum_full["codename"])?>">Administrar Foro</a></li>
					<?php
				}
				?>
				<?php
			}
		} else {
			if ($current == "topiclist") {
				?>
				<li><b>Lista de Foros</b> <img src="img/forum.png"></li>
				<?php
			} else {
				?>
				<li><a href="index.php">Lista de Foros</a> <img src="img/forum.png"></li>
				<?php
			}
			if (isadmin()) {
				if ($current == "newforum") {
					?>
					<li class="padding40"><b>Crea un Foro</b> <img src="img/add_post.gif"></li>
					<?php
				} else {
					?>
					<li class="padding40"><a href="newforum.php">Crea un Foro</a> <img src="img/add_post.gif"></li>
					<?php
				}
				if ($current == "sitesettings") {
					?>
					<li class="padding40"><b>Configura los Foros</b></li>
					<?php
				} else {
					?>
					<li class="padding40"><a href="sitesettings.php">Configura los Foros</a></li>
					<?php
				}
			}
		}
		if (get_setting("recentsearches") == true) {
			if ($current == "recentsearches") {
			?>
			<li><b>Búsquedas recientes</b></li>
			<?php
			} else {
			?>
			<li><a href="recentsearches.php">Búsquedas recientes</a></li>
			<?php
			}
		}
		?>
		<!--<li><a href="https://support.google.com" target="_BLANK">Artículos de ayuda</a></li>-->
	</ul>
	<?php
	if ($current == "sitesettings") {
		echo "<div class='here'>Texto personalizado de la barra lateral</div>";
	} else {
		echo get_site_setting("aside");
	}
	?>
</aside>
<?php
}

function print_meter() {
	$stats = get_stats();
?>
<aside id="right_sidebar">
	<div id="header"><?=userdata("username")?><?php if (userdata("fullname")) { echo " (".userdata("fullname").")"; } ?></div>
	<div id="infocard">
		<img class="avatar" src="avatar.php?id=<?=userdata("id")?>">
		<div class="data">
			<div id="joined">Te uniste el <?=date("d/m/Y", $stats["joined"])?></div>
			<div id="answered">Preguntas: <b><?=$stats["questions"]?></b> | Respuestas: <b><?=$stats["answers"]?></b></div>
		</div>
	</div>
	<?php
	foreach ($stats["meters"] as $meter) {
		?>
		<div class="meter"><span><a href="forum.php?id=<?=urlencode($meter["codename"])?>"><?=$meter["name"]?></a></span> <span>Nivel <?=$meter["level"]?></span> <?php if ($meter["level"] == 20) { ?><span>¡Felicidades!</span><?php } else { ?><meter class="level" max="1" value="<?=$meter["meter"]?>"></meter><?php } ?></div>
		<?php
	}
	?>
</aside>
<?php
}

function print_message($message, $role, $attachments, $type) {
	?>
	<div class="message_container" id="<?=(($type == "post") ? "op" : "c".$message["id"])?>">
		<div class="user">
			<img class="avatar" src="avatar.php?id=<?=(($type == "post") ? $message["op"] : $message["p"])?>">
			<div class="userinfo">
				<div class="username"><?=$role["user"]?></div>
				<div class="moreinfo"><?=$role["text"]?><br><span title="<?=date("r", $message["time"])?>"><?=better_time($message["time"])?></span></div>
			</div>
		</div>
		<div class="message">
			<div class="actionlinks">
			<?php
			if ((($type == "post") ? $message["op"] : $message["p"]) == userdata("id")) {
				$edit = "edit";
			} else {
				$edit = "othersedit";
			}
			if ($type == "post") {
				if (get_permission($edit, $message["forum"])) {
					?>
					<a href="editpost.php?id=<?=$message["id"]?>">editar</a> <a href="deletepost.php?id=<?=$message["id"]?>">eliminar</a>
					<?php
				}
				?>
				<a href="thread.php?id=<?=$message["id"]?>">permalink</a>
				<?php
			} else {
				$post = get_post($message["post"]);
				if (get_permission($edit, $post["forum"])) {
					?>
					<a href="editreply.php?id=<?=$post["id"]?>">editar</a> <a href="deletereply.php?id=<?=$post["id"]?>">eliminar</a>
					<?php
				}
				?>
				<a href="#c<?=$message["id"]?>">permalink</a>
				<?php
			}
			?>
			</div>
			<?=$message["message"]?>
			<?php
			$attachments = json_decode($message["attachments"], true);
			if (count($attachments)) {
				?>
				<hr>
				<p>Archivos adjuntos:</p>
				<ul>
				<?php
				foreach ($attachments as $i => $attachment) {
					$extension = explode(".", $attachment);
					$extension = end($extension);
					?>
					<li><a href="attachment.php?post=<?=$message["id"]?>&file=<?=$i?>&type=<?=$type?>">Archivo .<?=$extension?></a></li>
					<?php
				}
				?>
				</ul>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	}

function print_head() {
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
}