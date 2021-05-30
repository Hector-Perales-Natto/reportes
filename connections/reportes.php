 <?php
$dsn = "DRIVER={SQL Server};SERVER=10.1.11.4;DATABASE=SIAF_DB";
$usuario = "caem";
$clave="caem0206";

$cid = odbc_connect($dsn, $usuario, $clave);

if (!$cid){
	echo "<script>alert('No se pudo realizar la conexión a la base de datos, consulte a su administrador de sistemas');</script>";
}
?>