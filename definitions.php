<?php
$MASTER = array();

$MASTER["permissions"] = array("view", "post", "rich", "attach", "references", "assign", "metoo", "ba", "edit", "privatereply", "duplicate", "lock", "move", "pin", "category", "othersba", "othersedit", "announce", "insights", "admin");

$MASTER["translate_permissions"] = array(
	"view" => "Ver y leer el Foro",
	"post" => "Publicar preguntas y respuestas en el Foro",
	"rich" => "Escribir con formato enriquecido",
	"attach" => "Adjuntar archivos",
	"references" => "Añadir referencias",
	"assign" => "Asignar debates",
	"metoo" => "Decir \"¡Yo también!\"",
	"ba" => "Marcar mejor respuesta en debate propio",
	"edit" => "Editar o eliminar pregunta o respuesta propia",
	"privatereply" => "Responder al autor",
	"duplicate" => "Duplicar un debate",
	"lock" => "Bloquear un debate",
	"move" => "Mover un debate a otro Foro",
	"pin" => "Poner chincheta a un debate",
	"category" => "Editar la categoría de un debate",
	"othersba" => "Marcar mejor respuesta en el debate de otro",
	"othersedit" => "Editar o eliminar la pregunta o respuesta de otro",
	"announce" => "Publicar anuncios",
	"insights" => "Ver estadísticas sobre el Foro",
	"admin" => "Administrar el Foro"
);

$MASTER["default_permissions"] = array(
	"view" => 0,
	"post" => 0,
	"rich" => 0,
	"attach" => 0,
	"references" => 0,
	"assign" => 0,
	"metoo" => 0,
	"ba" => 0,
	"edit" => 0,
	"privatereply" => 0,
	"duplicate" => 0,
	"lock" => 0,
	"move" => 0,
	"pin" => 0,
	"category" => 0,
	"othersba" => 0,
	"othersedit" => 0,
	"announce" => 0,
	"insights" => 0,
	"admin" => 0
);

$MASTER["guest_allowed_permissions"] = array("view", "insights");

$MASTER["points"] = array(
	"postanswer" => 5,
	"helpful" => 10,
	"ba" => 15,
	"rating" => 1,
	"markingba" => 2,
	"abuse" => -100,
	"correctflag" => 15
);

$MASTER["levels"] = array(
	20 => 18500,
	19 => 16600,
	18 => 14800,
	17 => 13100,
	16 => 11500,
	15 => 10000,
	14 => 8600,
	13 => 7300,
	12 => 6100,
	11 => 5000,
	10 => 4000,
	9 => 3000,
	8 => 2000,
	7 => 1300,
	6 => 800,
	5 => 400,
	4 => 200,
	3 => 100,
	2 => 50,
	1 => 0
);

$MASTER["page_length"] = 25;