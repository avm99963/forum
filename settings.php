<?php
require_once("core.php");
require_once("includes.php");
if (!loggedin()) {
	header("Location: signin.php?continue=settings.php");
}

$avatar = userdata("avatar");

$url = get_current_path();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Configuración - <?=$CONF["appname"]?></title>
		<link rel="stylesheet" href="css/forum.css">
		<?php
		if (!empty($avatar) && $CONF["aviary"]["use"] === true) {
		?>
		<script type="text/javascript" src="http://feather.aviary.com/imaging/v2/editor.js"></script>
		<script>
		var featherEditor = new Aviary.Feather({
			apiKey: '<?=$CONF["aviary"]["clientId"]?>',
			theme: 'dark', // Check out our new 'light' and 'dark' themes!
			tools: 'all',
			appendTo: '',
			onSave: function(imageID, newURL) {
				var img = document.getElementById(imageID);
				img.src = newURL;
			},
			onError: function(errorObj) {
				alert(errorObj.message);
			}
		});
		function launchEditor() {
			featherEditor.launch({
				image: "avatar_img",
				url: "<?="uploaded_img/".$avatar?>"
			});
			return false;
		}
	    window.addEventListener("load", function() {
	    	document.querySelector("#edit_avatar").addEventListener("click", launchEditor);
	    });
		</script>
		<style>
		#edit_avatar {
			vertical-align: middle;
		}
		</style>
		<?php
		}
		?>
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header("index.php", "Configuración", "all"); ?>
		</header>
		<div id="container">
			<?php print_aside(); ?>
			<section class="alone">
				<h2>Configuración</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "saved")
						echo "<div class='alert success'>Se ha guardado la configuración correctamente.</div>";
					if ($_GET["msg"] == "emailincorrect")
						echo "<div class='alert danger'>Introduce una dirección de correo electrónico válido.</div>";
					if ($_GET["msg"] == "password")
						echo "<div class='alert danger'>La contraseña debe contener como mínimo 6 caracteres.</div>";
					if ($_GET["msg"] == "usernametaken")
						echo "<div class='alert danger'>Ese usuario ya está siendo usado por algún usuario.</div>";
					if ($_GET["msg"] == "emailregistered")
						echo "<div class='alert danger'>Esta dirección de correo electrónico ya está siendo usada por algún usuario.</div>";
					if ($_GET["msg"] == "wrongpassword")
						echo "<div class='alert danger'>La contraseña antigua no es correcta.</div>";
				}

				$query = mysqli_query($con, "SELECT recentsearches FROM settings WHERE user = '".userdata("id")."'");

				if (mysqli_num_rows($query)) {
					$row = mysqli_fetch_assoc($query);
					if ($row["recentsearches"] == true) {
						$recentsearches = " checked";
					} else {
						$recentsearches = "";
					}
				} else {
					$recentsearches = "";
				}

				$query2 = mysqli_query($con, "SELECT * FROM users WHERE id = ".userdata("id"));

				$row2 = mysqli_fetch_assoc($query2);
				?>
				<form action="processsettings.php" method="POST" enctype="multipart/form-data">
					<p><label for="username">Usuario:</label> <input type="text" name="username" id="username" value="<?=$row2["username"]?>"></p>
					<p><label for="fullname">Nombre completo:</label> <input type="text" name="fullname" id="fullname" value="<?=$row2["fullname"]?>"></p>
					<p><label for="email">Correo electrónico:</label> <input type="email" name="email" id="email" value="<?=$row2["email"]?>"></p>
					<?php
					function formatOffset($offset) {
					    $hours = $offset / 3600;
					    $remainder = $offset % 3600;
					    $sign = $hours > 0 ? '+' : '-';
					    $hour = (int) abs($hours);
					    $minutes = (int) abs($remainder / 60);

					    if ($hour == 0 AND $minutes == 0) {
					        $sign = ' ';
					    }
					    return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

					}

					$utc = new DateTimeZone('UTC');
					$dt = new DateTime('now', $utc);

					echo '<p><label for="timezone">Zona horaria:</label> <select id="timezone" name="timezone">';
					$timezone = get_setting("timezone");
					foreach(DateTimeZone::listIdentifiers() as $tz) {
					    $current_tz = new DateTimeZone($tz);
					    $offset =  $current_tz->getOffset($dt);
					    $transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
					    $abbr = $transition[0]['abbr'];

					    if ($tz == $timezone) {
					    	$selected = " selected";
					    } else {
					    	$selected = "";
					    }

					    echo '<option value="' .$tz. '"'.$selected.'>' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
					}
					echo '</select></p>';
					?>
					<p><label for="avatar">Foto de perfil:</label> <input type="file" name="avatar" id="avatar" accept=".gif,.jpg,.jpeg,.png"></p>
					<p><?php if (!empty($avatar)) { if ($CONF["aviary"]["use"] === true) { ?><img id="avatar_img" src="<?="uploaded_img/".$avatar?>"> <input id="edit_avatar" type='image' src='http://images.aviary.com/images/edit-photo.png' value='Editar'> | <?php } ?><a href="deleteavatar.php">Eliminar foto de perfil</a><div id='injection_site'></div><?php } ?></p> <hr>
					<p style="color: gray;">Si quieres cambiar la contraseña:</p>
					<p><label for="oldpassword">Antigua contraseña:</label> <input type="password" name="oldpassword" id="oldpassword"></p>
					<p><label for="password">Contraseña:</label> <input type="password" name="password" id="password"></p>
					<hr>
					<p><input type="checkbox" name="recentsearches" id="form_recentsearches"<?=$recentsearches?>> <label for="form_recentsearches">Guardar búsquedas recientes</label></p>
					<p><input type="submit" value="Guardar"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>
