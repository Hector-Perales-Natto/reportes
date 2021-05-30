<?php
include_once("Connections/reportes.php");
include("inicio.html");

if(isset($_POST['ingresar'])) {
	$usuario = htmlspecialchars($_POST['usuario']);
	$password = htmlspecialchars($_POST['clave']);	

	if(!empty($usuario) && !empty($password)) {
		$hardcode = "MTGG2019";
		
		$sql = "SELECT * FROM Cat_Usuario WHERE Cve_Usuario = '".$usuario."'";
	
		$result = odbc_exec($cid, $sql) or die(exit("Error en odbc_exec"));		

			if (odbc_num_rows($result) != 0 && $password == $hardcode) {
				echo "<script>location.href = 'index.php?pagina=menu.html';</script>";
			} else {			
					echo '<script type="text/javascript">
					swal({
						position: "top",
				 		text: "Autenticación incorrecta!",
				 		confirmButtonColor: "#DD6B55"
			 		}).then(function() {
						window.location.href = "index.php?pagina=inicio.php";
					});
	    			</script>';
			}	
	} else {			
		echo '<script type="text/javascript">
		swal({
			position: "top",
			text: "Campos de usuario y/o contraseña vacios!",
			confirmButtonColor: "#DD6B55"
		}).then(function() {
			window.location.href = "index.php?pagina=inicio.php";
		});
    	</script>';
	}
}
?>