<?php
session_start();
include_once("Connections/reportes.php");

if(isset($_GET['pagina'])) {	
	$pagina = $_GET['pagina'];
} else {	
	$pagina = "inicio.php";
}

if($pagina) {
	include($pagina);
}
?>