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
if (get_permission("admin", $forum["id"]) === false) {
	header("Location: index.php");
	exit();
}
$role = get_role($_GET["role"], $forum["id"]);
if ($role === false) {
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
		<script src="bower_components/jquery/dist/jquery.min.js"></script>
		<script>
		var assignees = [<?php if (count($role["members"]) > 0) { echo implode(", ", $role["members"]); } ?>], selectize, role = <?=$role["id"]?>;

		function updated() {
			if (document.querySelector("#assign_textbox").value == "") {
				document.querySelector("#assign_btn").disabled = true;
			} else {
				document.querySelector("#assign_btn").disabled = false;
			}
		}

		function done() {
			var people = document.querySelector("#assign_textbox").value;
			var http = new XMLHttpRequest();
			var url = "ajax/processassignrole.php";
			var params = "role="+role+"&people="+people;
			http.open("POST", url, true);
			http.onload = function() {
			    if(this.status == 200) {
			        var response = JSON.parse(this.responseText);
			        if (response.errorCode) {
			        	alert("Error: "+response.errorText);
			        } else {
			        	location.reload();
			        }
			    }
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(params);
		}

		function init() {
			selectize = $("#assign_textbox").selectize({
				delimiter: ',',
				valueField: 'id',
				labelField: 'username',
			    searchField: ['username', 'name', 'surname', 'email'],
			    create: false,
			    render: {
			    	item: function(item, escape) {
			    		return '<div><span class="username">'+escape(item.username)+'</span><span class="name">'+escape(item.email)+'</span></div>';
			    	},
			        option: function(item, escape) {
			        	return '<div><span class="label">'+escape(item.username)+'</span><span class="caption">'+escape(item.email)+'</span></div>';
			        }
			    },
			    load: function(query, callback) {
			        if (!query.length) return callback();
			        $.ajax({
			            url: 'ajax/people.php?q='+encodeURIComponent(query)+'&exclude='+encodeURIComponent(JSON.stringify(assignees)),
			            type: 'GET',
			            error: function() {
			                callback();
			            },
			            success: function(res) {
			            	callback(JSON.parse(res));
			            }
			        });
			    }
			});
			selectize.on('change', updated);
			document.querySelector("#assign_btn").addEventListener('click', done);
		}

		window.addEventListener('load', init);
		</script>
		<script src="bower_components/selectize/dist/js/standalone/selectize.min.js"></script>
		<link rel="stylesheet" type="text/css" href="bower_components/selectize/dist/css/selectize.default.css">
		<link rel="stylesheet" type="text/css" href="css/selectize_custom.css">
		<?php print_head(); ?>
	</head>
	<body>
		<header>
			<?php print_header(null, $forum["name"], "forum", $forum["codename"], null, "forumlogo.php?id=".urlencode($forum["codename"])); ?>
		</header>
		<div id="container">
			<?php print_aside("adminforum"); ?>
			<section class="alone">
				<div class="breadcrumb">
					<a href="index.php"><?=$CONF["appname"]?></a> > <a href="forum.php?id=<?=$forum["codename"]?>"><?=$forum["name"]?></a> > <a href="adminforum.php?id=<?=$forum["codename"]?>">Administrar el Foro</a> > <a href="editroles.php?id=<?=$forum["codename"]?>">Roles</a> > Asignar rol
				</div>
				<h2>Asignar rol</h2>
				<?php
				if (count($role["members"]) > 0) {
					foreach ($role["members"] as $member) {
						?>
						<div class="category">
							<div class="title"><?=userdata("username", $member)?> <a class="red" href="deallocaterole.php?role=<?=$role["id"]?>&user=<?=$member?>"><i class="material-icons red middle">delete</i></a></div>
							<div class="discussions"><?=userdata("email", $member)?></div>
						</div>
						<?php
					}
				} else {
					?>
					<p>No hay nadie asignado a este rol.</p>
					<?php
				}
				?>
				<p><input type="text" id="assign_textbox"></p>
				<p><button id="assign_btn" class="g-button g-button-submit" disabled>Asignar</button></p>
			</section>
		</div>
		<div class="clear"></div>
		<?php print_footer(); ?>
	</body>
</html>