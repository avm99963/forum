<?php
require_once("../core.php");

// Required: anonymous function reference number as explained above.
$funcNum = $_GET['CKEditorFuncNum'];
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = $_GET['CKEditor'];

$image = upload_image($_FILES["upload"], null, true);

if ($image < 0) {
	$url = "";
	$message = "El archivo no se ha podido subir correctamente";
} else {
	$url = "uploaded_img/".$image;
	$message = "";
}
 
echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";