<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/sweetalert2.min.css">    
	<title>Traspasos</title>	

    <link rel="shortcut icon" href="img/favicon.ico">

	<script language="javascript">
        function valida() {
            if(document.getElementById('ano').value != 0 && document.getElementById('mes').value != 0) {
                document.getElementById('busca').disabled = false;
            }
        }  		
	</script>

	<style>
		th {
			font-size: 12px;
		}

		label {
			font-size: 10px;
        }
        
        #loading_oculto{
            z-index: 99;
            position: absolute;            
            margin-left: 55px;
            margin-top: 10px;
            visibility: hidden;
        }

        .container {
            height: 500px;
        }    
	</style>
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<form name="registrar" id="registrar" class="form" method="POST" action="traspasos.php" enctype="multipart/form-data">
				<table class="table table-sm">
					<tr><h5>TRASPASOS</h5></tr>
					<tr>
						<th>AÑO:</th>
						<th>MES:</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<td class="form-group">
							<select name="ano" id="ano" class="form-control" onchange="valida()">
								<option value=0 disabled selected>--Seleccione una Opción--</option>							
                                <option value="<?php echo (date("Y")-1); ?>"><?php echo (date("Y")-1); ?></option>
                                <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
							</select>
                        </td>
                        <td class="form-group">
							<select name="mes" id="mes" class="form-control" onchange="valida()">
								<option value=0 disabled selected>--Seleccione una Opción--</option>								
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
							</select>
                        </td>
                        <td class="form-group">
                            <button type="submit" class="btn btn-primary" name="busca" id="busca" value="&rarr;" onclick="document.getElementById('loading_oculto').style.visibility='visible';" disabled>Ejecuta proceso</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">&nbsp;</td>
                        <td class="form-group">
                            <div id="loading_oculto"><img src="img/ajax-loader.gif" /></div>            
                        </td>
                    </tr>                                        					
				</table>
            </form>            
		</div>	
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>	
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/sweetalert2.min.js"></script>
</body>
</html>
<?php
if(isset($_POST['busca'])) {
    include_once("Connections/reportes.php");

    $ano = $_POST['ano'];
    $mes = $_POST['mes'];

    $sql="exec PO_Actualiza_Ctrl_Ppto_Asig_Dedu $ano, $mes";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));     

    if (odbc_num_rows($result) == 0) {
        echo '<script type="text/javascript">
                    swal({
                        position: "top",
                         text: "No se actualizo bien el traspaso!",
                         confirmButtonColor: "#DD6B55"
                     }).then(function() {
                        window.location.href = "index.php?pagina=traspasos.php";
                    });
               </script>';
    } else {
        $hoy = date("F j, Y, g:i a");
        $file = fopen("bitacora.txt", "a");
        fwrite($file, "Proceso de traspaso generado: ".$hoy . PHP_EOL);
        fclose($file);
        echo '<script type="text/javascript">
                    swal({
                        position: "top",
                         text: "El traspaso se realizo correctamente!",
                         confirmButtonColor: "#DD6B55"
                    }).then(function() {
                        window.location.href = "index.php?pagina=traspasos.php";
                    });
               </script>';
    }
} 
?>