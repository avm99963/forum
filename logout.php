<?php
    require_once('core.php'); // incluimos los datos de acceso a la BD 
    // comprobamos que se haya iniciado la sesión 
    if (isset($_SESSION['id'])) { 
        session_destroy();
        if (isset($_GET['continue']) && !empty($_GET['continue'])) {
			header("Location: ".$_GET['continue']);
		} else {
			header("Location: index.php");
		}
    } else { 
        echo "Operación incorrecta."; 
    }
?>