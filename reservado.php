<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/sweetalert2.min.css">    
	<title>Reservado</title>	

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
	</style>
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<form name="registrar" id="registrar" class="form" method="POST" action="reservado.php" enctype="multipart/form-data">
				<table class="table table-sm">
					<tr><h5>PRESUPUESTO RESERVADO</h5></tr>
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
                            <button type="submit" class="btn btn-primary" name="busca" id="busca" value="&rarr;" onclick="document.getElementById('loading_oculto').style.visibility='visible';" disabled>Genera reporte</button>
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

    echo '<div id="loading_oculto"><img src="img/ajax-loader.gif" /></div>';

    $ano = $_POST['ano'];
    $mes = $_POST['mes'];

	$sql="Select Cve_Ppto = Cve_Region + Cve_Funcion + Cve_SubFuncion + Cve_Programa +
    Cve_SubPrograma + Cve_Proyecto + Cve_FFinanciamiento + Cve_UResponsable +
    Cve_UEjecutora_ext + Cve_UDesagrega_ext + Cve_Partida,Num_Mes,
    CRP.Id_Ppto,Imp_Reservado
    from Ctrl_Ppto CRP
    inner join Cat_Ppto A on A.Id_Ppto = CRP.Id_Ppto
    inner join Cat_Region B(Nolock) on B.Id_Region = A.Id_Region
    inner join Cat_Proyecto C(Nolock) on C.Id_Proyecto = A.Id_Proyecto
    inner join Cat_SubPrograma D(Nolock) on D.Id_SubPrograma = C.Id_SubPrograma
    inner join Cat_Programa E(Nolock) on E.Id_Programa = D.Id_Programa
    inner join Cat_SubFuncion F(Nolock) on F.Id_SubFuncion = E.Id_SubFuncion
    inner join Cat_Funcion G(Nolock) on G.Id_Funcion = F.Id_Funcion
    inner join Cat_UDes H(Nolock) on H.Id_UDes = A.Id_UDes
    inner join Cat_UEjec I(Nolock) on I.Id_UEjecutora = H.Id_UEjecutora
    inner join Cat_UResp J(Nolock) on J.Id_UResponsable = I.Id_UResponsable
    inner join Cat_FFinanciamiento K(Nolock) on K.Id_FFinan = A.Id_FFinan
    inner join Cat_Partida L(Nolock) on L.Id_Partida = A.Id_Partida
    where Num_Mes <= $mes and A.Efisc = $ano and Imp_Reservado <> 0
    order by Cve_ppto,Num_mes";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));       

    if (odbc_num_rows($result) == 0) {
        echo '<script type="text/javascript">
					swal({
						position: "top",
				 		text: "No existen datos para el periodo seleccionado!",
				 		confirmButtonColor: "#DD6B55"
			 		}).then(function() {
						window.location.href = "index.php?pagina=reservado.php";
					});
	    			</script>';
    } else {
        echo '<div class="container">';
        echo '<div class="row justify-content-center">';
        echo '<a style="margin: 10px; color: #FFF" href="excelReservado.php?recordID='.$ano.$mes.'" class="btn btn-primary" name="busca" id="busca">Exportar a excel</a>';
        echo '<table style="width:90%" border=1>';
        echo '<tr>';
        echo '<th>CVE. PRESUPUESTO</th>';
        echo '<th>MES</th>';
        echo '<th>ID. PRESUPUESTO</th>';
        echo '<th>IMPORTE</th>';
        echo '</tr>';

        while($datos = odbc_fetch_array($result)) {    
            echo '<tr>';
            echo '<td align="center">'.$datos["Cve_Ppto"].'</td>';
            echo '<td>'.$datos["Num_Mes"].'</td>';
            echo '<td>'.$datos["Id_Ppto"].'</td>';
            echo '<td>'.$datos["Imp_Reservado"].'</td>';
            echo '</tr>';        
        }

        echo '</table>';                
        echo '</div>';
        echo '</div>';
    }
}
?>