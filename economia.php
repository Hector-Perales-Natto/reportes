<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/sweetalert2.min.css">                
	<title>Poliza</title>	

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
			<form name="registrar" id="registrar" class="form" method="POST" action="economia.php" enctype="multipart/form-data">
				<table class="table table-sm">
					<tr><h5>ECONOMIAS</h5></tr>
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
    $msi = $mes+1;    

	$sql="select NUM_EFISC,MES_AFECTACION,ppto = b.cve_region + '-' + g.cve_funcion + f.Cve_SubFuncion + e.Cve_Programa + d.Cve_SubPrograma + c.Cve_Proyecto + '-' +
    k.Cve_FFinanciamiento  +'-' + j.Cve_UResponsable + i.Cve_UEjecutora_ext + h.Cve_UDesagrega_ext + '-' + L.Cve_Partida 
    ,MES_DEDUCCION,MES_ASIGNACION,IMP_ECONOMIA,id_folio_economia,y.id_status 
    from ctrl_ppto_economia Y
    JOIN ctrl_ppto_economia_detalle Z ON Y.ID_ECONOMIA = Z.ID_ECONOMIA
    INNER JOIN Cat_Ppto A(nolock) ON Z.ID_PPTO = A.ID_PPTO 
    Inner Join Cat_Region B(nolock) on B.Id_Region = A.Id_Region
    Inner Join Cat_Proyecto C(nolock) on C.Id_Proyecto = A.Id_Proyecto 
    Inner Join Cat_SubPrograma D(nolock) on D.Id_SubPrograma = C.Id_SubPrograma 
    Inner Join Cat_Programa E(nolock) on E.Id_Programa = D.Id_Programa
    Inner Join Cat_SubFuncion F(nolock) on F.Id_SubFuncion = E.Id_SubFuncion 
    Inner Join Cat_Funcion G(nolock) on G.Id_Funcion = F.Id_Funcion
    Inner Join Cat_UDes H(nolock) on H.Id_UDes = A.Id_UDes 
    Inner Join Cat_UEjec I(nolock) on I.Id_UEjecutora = H.Id_UEjecutora
    Inner Join Cat_UResp J(nolock) on J.Id_UResponsable = I.Id_UResponsable
    Inner Join Cat_FFinanciamiento K(nolock) on K.Id_FFinan = A.Id_FFinan
    Inner Join Cat_Partida L(nolock) on L.Id_Partida = A.Id_Partida
    Left Join Cat_Cta_Contable M(nolock) on L.Id_Cta_Contable_Gasto = M.Id_Cta_Contable
    WHERE NUM_EFISC = $ano and  mes_afectacion BETWEEN $mes and $msi and y.id_status in(7,10,11)    
    order by mes_afectacion, b.cve_region,g.cve_funcion,f.Cve_SubFuncion,e.Cve_Programa,d.Cve_SubPrograma,c.Cve_Proyecto,
    k.Cve_FFinanciamiento,j.Cve_UResponsable,i.Cve_UEjecutora,h.Cve_UDesagrega,L.Cve_Partida";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));    

    if (odbc_num_rows($result) == 0) {
        echo '<script type="text/javascript">
					swal({
						position: "top",
				 		text: "No existen datos para el periodo seleccionado!",
				 		confirmButtonColor: "#DD6B55"
			 		}).then(function() {
						window.location.href = "index.php?pagina=economia.php";
					});
	    			</script>';
    } else {
        echo '<div class="container">';
        echo '<div class="row justify-content-center">';
        echo '<a style="margin: 10px; color: #FFF" href="excelEconomia.php?recordID='.$ano.$mes.'" class="btn btn-primary" name="busca" id="busca">Exportar a excel</a>';
        echo '<table style="width:90%" border=1>';
        echo '<tr>';
        echo '<th>EJERCICIO FISCAL</th>';
        echo '<th>MES DE AFECTACION</th>';
        echo '<th>CVE. PRESUPUESTAL</th>';
        echo '<th>MES DE DEDUCCION</th>';
        echo '<th>MES DE ASIGNACION</th>';
        echo '<th>TOTAL</th>';
        echo '<th>FOLIO</th>';
        echo '<th>ESTATUS</th>';
        echo '</tr>';

        while($datos = odbc_fetch_array($result)) {    
            echo '<tr>';
            echo '<td align="center">'.$datos["NUM_EFISC"].'</td>';
            echo '<td align="center">'.$datos["MES_AFECTACION"].'</td>';
            echo '<td>'.$datos["ppto"].'</td>';
            echo '<td align="center">'.$datos["MES_DEDUCCION"].'</td>';
            echo '<td align="center">'.$datos["MES_ASIGNACION"].'</td>';
            echo '<td>'.number_format($datos["IMP_ECONOMIA"], 2, '.', '').'</td>';
            echo '<td align="center">'.$datos["id_folio_economia"].'</td>';
            echo '<td align="center">'.$datos["id_status"].'</td>';
            echo '</tr>';        
        }

        echo '</table>';                
        echo '</div>';
        echo '</div>';
    }
}
?>