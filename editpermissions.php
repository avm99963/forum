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
if (get_permission("admin", $forum["id"]) === false) {
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
		<link rel="stylesheet" href="css/permissions.css">
		<script src="js/permissions.js"></script>
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum", $forum["codename"]); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > Permisos
				</div>
				<h2>Permisos</h2>
				<?php
				if (isset($_GET["msg"])) {
					if ($_GET["msg"] == "success")
						echo "<div class='alert success'>Se han modificado los permisos satisfactoriamente.</div>";
				}

				$allroles = array("guest", "user");
				$currentpermissions = array();

				$currentpermissions["guest"] = json_decode($forum["guest_permissions"], true);
				$currentpermissions["user"] = json_decode($forum["user_permissions"], true);

				$roles = get_roles($forum["id"]);

				if ($roles !== false) {
					foreach ($roles as $id => $role) {
						$allroles[] = "role_".$id;
						$currentpermissions["role_".$id] = json_decode($role["permissions"], true);
					}
				}

				function getcodenamerole_2($role) {
					global $roles;
					if ($role == "guest") {
						return "guest";
					} elseif ($role == "user") {
						return "user";
					} else {
						return explode("_", $role)[1];
					}
				}

				function getcodenamerole($role) {
					global $roles;
					if ($role == "guest") {
						return "guest";
					} elseif ($role == "user") {
						return "user";
					} else {
						return $roles[explode("_", $role)[1]]["id"];
					}
				}

				function translate($role) {
					global $roles;
					if ($role == "guest") {
						return "Invitado";
					} elseif ($role == "user") {
						return "Usuario registrado";
					} else {
						$role = explode("_", $role)[1];
						return $roles[$role]["name"].((!empty($roles[$role]["badge"])) ? " <img class='rolebadge' src='badge.php?id=".$roles[$role]["id"]."'>" : "");
					}
				}
				?>
				<form action="processeditpermissions.php" method="POST">
					<input type="hidden" name="id" value="<?=$forum["codename"]?>">
					<table>
						<thead>
							<tr>
								<th></th>
								<?php
								foreach ($allroles as $role) {
									?>
									<th><div><span><?=translate($role)?></span></div></th>
									<?php
								}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($MASTER["translate_permissions"] as $permission => $description) {
								?>
									<tr>
										<th><?=$description?></th>
										<?php
										foreach ($allroles as $role) {
											$codename = getcodenamerole($role);
											if ($codename == "guest" && !in_array($permission, $MASTER["guest_allowed_permissions"])) {
												$disabled = " disabled";
											} else {
												$disabled = "";
											}
											if ($currentpermissions[$role][$permission]) {
												$checked = " checked";
											} else {
												$checked = "";
											}
											?>
											<td><input type="checkbox" name="<?=$codename?>,<?=$permission?>" data-role="<?=$codename?>" data-permission="<?=$permission?>"<?=$disabled?><?=$checked?>></td>
											<?php
										}
										?>
									</tr>
								<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<?php
								foreach ($allroles as $role) {
									$codename = getcodenamerole($role);
									?>
									<td><span class="select" data-role="<?=$codename?>">+</span> <span class="deselect" data-role="<?=$codename?>">–</span></td>
									<?php
								}
								?>
							</tr>
						</tfoot>
					</table>
					<p><input type="submit" value="Guardar"></p>
				</form>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>