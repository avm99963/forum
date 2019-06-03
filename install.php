<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Instalar los Foros</title>
		<link rel="stylesheet" href="css/form_design.css">
	</head>
	<body>
	<?php
	if (file_exists("config.php")) {
	?>
		<main>
			<h1>Los Foros ya están instalados :-D</h1>
		</main>
	<?php
	} else {
	if (!isset($_POST["step"])) {
	?>
		<main>
			<h1>Instalar los Foros</h1>
			<p>¡Hola! Bienvenido a la página de instalación de los Foros. Para instalar los Foros, pulsa el siguiente botón:</p>
			<p><form method="POST"><input type="hidden" name="step" value="1"><input type="submit" value="Siguiente >>"></form></p>
		</main>
	<?php
	} elseif (isset($_POST["step"]) && $_POST["step"] == "1") {
	?>
		<main>
			<h1>Instalar los Foros</h1>
			<form method="POST">
				<input type="hidden" name="step" value="2">
				<h2>Paso 1. Información básica</h2>
				<p class="description">Por favor, rellena el siguiente formulario con información básica sobre los Foros:</p>
				<p><label for="appname">Nombre de los Foros:</label> <input type="text" name="appname" id="appname" required></p>
				<p><input type="checkbox" name="register_filter" id="register_filter"> <label for="register_filter">Permitir a cualquier persona registrarse</label></p>
				<h2>Paso 2. Base de datos</h2>
				<p class="description">Necesitamos acceso a una base de datos MySQL para poder almacenar toda la información de los Foros. Proporciona los detalles aquí abajo:</p>
				<p><label for="db_server">Servidor:</label> <input type="text" name="db_server" id="db_server" required></p>
				<p><label for="db_database">Base de datos:</label> <input type="text" name="db_database" id="db_database" required></p>
				<p><label for="db_username">Usuario:</label> <input type="text" name="db_username" id="db_username" required></p>
				<p><label for="db_password">Contraseña:</label> <input type="password" name="db_password" id="db_password" required></p>
				<h2>Paso 3. Usuario administrador</h2>
				<p class="description">En este paso se creará el usuario administrador. Introduce los datos de inicio de sesión para el administrador:</p>
				<p><label for="admin_username">Usuario:</label> <input type="text" name="admin_username" id="admin_username" required></p>
				<p><label for="admin_password">Contraseña:</label> <input type="password" name="admin_password" id="admin_password" required></p>
				<p><label for="admin_email">Correo electrónico:</label> <input type="email" name="admin_email" id="admin_email" required></p>
				<p><input type="submit" value="Finalizar"></p>
			</form>
		</main>
	<?php
	} elseif (isset($_POST["step"]) && $_POST["step"] == "2") {
	?>
		<main>
			<?php
			require_once("lib/password_compat/password.php");
			
			$required_fields = array("appname", "register_filter", "db_server", "db_database", "db_username", "db_password", "admin_username", "admin_password", "admin_email");
			$config_fields = array("appname", "register_filter", "db_server", "db_database", "db_username", "db_password");
			$data = array();
			foreach ($required_fields as $field) {
				if (!isset($_POST[$field]) || empty($_POST[$field])) {
					if ($field != "register_filter") {
						die("<div class='alert danger'>Asegúrate de rellenar todo el formulario. (".$field.")</div>");
					}
				}
				if ($field == "admin_email") {
					if(!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
						die("<div class='alert danger'>La dirección de correo electrónico que has rellenado no es válida.</div>");
					}
				}
			}

			$config_file = file_get_contents("config.template");

			foreach ($config_fields as $field) {
				if ($field == "register_filter") {
					$value = (isset($_POST["register_filter"])) ? "true" : "false";
				} else {
					$value = addslashes(htmlentities($_POST[$field]));
				}
				$config_file = str_replace("{{".$field."}}", $value, $config_file);
			}

			if (!file_exists("uploaded_img")) {
				if(!@mkdir("uploaded_img")) {
					die("<div class='alert danger'>No se ha podido crear la carpeta uploaded_img. Créala tú mismo con los permisos necesarios, y vuelve a rellenar el formulario de instalación.</div>");
				}
			}

			$con = @mysqli_connect($_POST["db_server"], $_POST["db_username"], $_POST["db_password"], $_POST["db_database"]) or die("<div class='alert danger'>No se ha podido conectar con la base de datos.</div>");

			$sql = array();

			$sql["users"] = "CREATE TABLE users
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			username VARCHAR(50),
			password VARCHAR(255),
			email VARCHAR(100),
			fullname VARCHAR(100),
			avatar VARCHAR(100),
			joined INT(15),
			role INT(2)
			)";

			$sql["forums"] = "CREATE TABLE forums
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			codename VARCHAR(50),
			name VARCHAR(100),
			description TEXT,
			welcome TEXT,
			logo VARCHAR(100),
			parent INT(13),
			levels BOOLEAN,
			plain_template TEXT,
			rich_template TEXT,
			guest_permissions TEXT,
			user_permissions TEXT
			)";

			$sql["categories"] = "CREATE TABLE categories
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			forum INT(13),
			codename VARCHAR(50),
			name VARCHAR(100),
			description TEXT,
			num INT(13)
			)";

			$sql["posts"] = "CREATE TABLE posts
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			title VARCHAR(250),
			message TEXT,
			op INT(13),
			time INT(15),
			type VARCHAR(20),
			forum INT(13),
			category INT(13),
			pinned BOOLEAN,
			locked BOOLEAN,
			assigned INT(13),
			duplicated INT(13),
			deleted BOOLEAN,
			lastmodified INT(15),
			attachments TEXT
			)";

			$sql["replies"] = "CREATE TABLE replies
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			post INT(13),
			message TEXT,
			p INT(13),
			time INT(15),
			ba BOOLEAN,
			deleted BOOLEAN,
			attachments TEXT
			)";

			$sql["votes"] = "CREATE TABLE votes
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			type VARCHAR(20),
			message INT(13),
			voter INT(13),
			time INT(15)
			)";

			$sql["moves"] = "CREATE TABLE moves
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			origin INT(13),
			destination INT(13),
			mover INT(13),
			time INT(15)
			)";

			$sql["searches"] = "CREATE TABLE searches
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			searchterms VARCHAR(250),
			searcher INT(13),
			time INT(15)
			)";

			$sql["settings"] = "CREATE TABLE settings
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			user INT(13),
			recentsearches BOOLEAN,
			timezone VARCHAR(35)
			)";

			$sql["points"] = "CREATE TABLE points
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			points INT(16),
			holder INT(13),
			forum INT(13)
			)";

			$sql["roles"] = "CREATE TABLE roles
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			name VARCHAR(50),
			description TEXT,
			forum INT(13),
			badge VARCHAR(100),
			permissions TEXT,
			level INT(3),
			expert BOOLEAN
			)";

			$sql["assigned_roles"] = "CREATE TABLE assigned_roles
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			user INT(13),
			role INT(13),
			forum INT(13)
			)";

			$sql["site_settings"] = "CREATE TABLE site_settings 
			(
			aside TEXT,
			footer TEXT
			)";

			$sql["metoo"] = "CREATE TABLE metoo
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			user INT(13),
			post INT(13)
			)";

			$sql["search_index"] = "CREATE TABLE search_index
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			message TEXT,
			type VARCHAR(13),
			data INT(13),
			forum INT(13),
			category INT(13),
			FULLTEXT (message)
			)";

			$sql["marked_abuse"] = "CREATE TABLE marked_abuse
			(
			id INT(13) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			data INT(13),
			type VARCHAR(13),
			reporter INT(13),
			reason VARCHAR(13),
			action VARCHAR(13)
			)";

			foreach ($sql as $key => $query) {
				if (!mysqli_query($con, $query)) {
				  die("<div class='alert danger'>Ha ocurrido un error inesperado al crear la tabla ".$key.": ".mysqli_error($con).".</div>");
				}
			}

			$username = htmlspecialchars(mysqli_real_escape_string($con, $_POST['admin_username']));
			$password = password_hash(mysqli_real_escape_string($con, $_POST['admin_password']), PASSWORD_DEFAULT);
			$email = mysqli_real_escape_string($con, $_POST['admin_email']);
			$sql6 = "INSERT INTO users (username, password, email, role, joined) VALUES ('".$username."', '".$password."', '".$email."', 1, ".time().")";
			if (!mysqli_query($con,$sql6)) {
				die("<div class='alert danger'>Ha ocurrido un error inesperado al crear el usuario administrador: ".mysqli_error($con).".</div>");
			}

			$sql7 = "INSERT INTO site_settings (aside, footer) VALUES ('', '<p>Desarrollado por Adrià Vilanova Martínez</p>')";
			if (!mysqli_query($con,$sql7)) {
				die("<div class='alert danger'>Ha ocurrido un error inesperado al definir la configuración por defecto: ".mysqli_error($con).".</div>");
			}

			file_put_contents("config.php", $config_file);
			?>
			<h1>¡Instalado!</h1>
			<p>Perfecto, los Foros se han instalado correctamente. Ahora ya puedes visitarlos <a href="index.php">aquí</a>.</p>
		</main>
	<?php
	} else {
	?>
		<main>
			<h1>Woohoo!</h1>
			<p><iframe width="420" height="315" src="https://www.youtube.com/embed/otCpCn0l4Wo" frameborder="0" allowfullscreen></iframe></p>
		</main>
	<?php
	}
	}	
	?>
	</body>
</html>