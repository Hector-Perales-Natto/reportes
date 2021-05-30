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
			<form name="registrar" id="registrar" class="form" method="POST" action="poliza.php" enctype="multipart/form-data">
				<table class="table table-sm">
					<tr><h5>POLIZA DE DIARIO</h5></tr>
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

	$sql="select DISTINCT NUM_POLIZA,FEC_POLIZA,IMP_TOTAL_POLIZA,
     txtctacontable = case when substring(CAT1.txt_cta_contable,1,9) = 'FINIQUITO' THEN   TXT_CONCEPTO_POLIZA 
                           WHEN SUBSTRING(CAT1.TXT_CTA_CONTABLE,1,17) = 'SUELDOS POR PAGAR' THEN   TXT_CUSTODIA
                           WHEN SUBSTRING(CAT1.TXT_CTA_CONTABLE,1,8) = 'DERECHOS' THEN   TXT_CUSTODIA  
ELSE
     CAT1.TXT_CTA_CONTABLE END,
     CAT1.CVE_CTA_CONTABLE,CAT1.CVE_NIVEL2,CAT1.CVE_NIVEL3,CTA4 = ISNULL(CAT1.CVE_NIVEL4, ''),CTA5 = ISNULL(CAT1.CVE_NIVEL5,'') 

    from ctrl_poliza a (nolock)
    join ctrl_poliza_detalle c (nolock) on a.id_poliza = c.id_poliza
    join cat_cta_contable d (nolock) on c.id_cta_contable = d.id_cta_contable
    join cat_tipo_poliza b (nolock) on a.id_tipo_poliza = b.id_tipo_poliza
    LEFT JOIN CTRL_CUENTAS_PPAGAR CPP (nolock) ON A.ID_POLIZA = CPP.ID_POLIZA
    JOIN CAT_CTA_CONTABLE CAT1 (nolock) ON CAT1.ID_CTA_CONTABLE = CPP.ID_CTA_CONTABLE_PROV
    WHERE  a.efisc  = $ano and month(fec_poliza) = $mes and txt_clase_poliza = 3
    AND  SUBSTRING(D.CVE_CTA_CONTABLE,1,1) = 8  
    
UNION ALL
    
    select DISTINCT NUM_POLIZA,FEC_POLIZA,IMP_TOTAL_POLIZA,CAT1.TXT_CTA_CONTABLE,CAT1.CVE_CTA_CONTABLE,CAT1.CVE_NIVEL2,CAT1.CVE_NIVEL3,CAT1.CVE_NIVEL4,CAT1.CVE_NIVEL5 

    from ctrl_poliza a (nolock)
    join ctrl_poliza_detalle c (nolock) on a.id_poliza = c.id_poliza
    join cat_cta_contable d (nolock) on c.id_cta_contable = d.id_cta_contable
    join cat_tipo_poliza b (nolock) on a.id_tipo_poliza = b.id_tipo_poliza
    LEFT JOIN CTRL_COMPRAS CPP (nolock) ON A.ID_POLIZA = CPP.ID_POLIZA
    JOIN CAT_CTA_CONTABLE CAT1 (nolock) ON CAT1.ID_CTA_CONTABLE = CPP.ID_CTA_CONTABLE_PROV
    WHERE  a.efisc  = $ano and month(fec_poliza) = $mes and txt_clase_poliza = 3
    AND  SUBSTRING(D.CVE_CTA_CONTABLE,1,1) = 8 
    
UNION ALL

 select DISTINCT NUM_POLIZA,FEC_POLIZA,IMP_TOTAL_POLIZA,CAT1.TXT_CTA_CONTABLE,CAT1.CVE_CTA_CONTABLE,CAT1.CVE_NIVEL2,CAT1.CVE_NIVEL3,CAT1.CVE_NIVEL4,CAT1.CVE_NIVEL5 

    from ctrl_poliza a (nolock)
    join ctrl_poliza_detalle c (nolock) on a.id_poliza = c.id_poliza
    join cat_cta_contable d (nolock) on c.id_cta_contable = d.id_cta_contable
    join cat_tipo_poliza b (nolock) on a.id_tipo_poliza = b.id_tipo_poliza
    LEFT JOIN CTRL_SERVICIOS CPP (nolock) ON A.ID_POLIZA = CPP.ID_POLIZA
    JOIN CAT_CTA_CONTABLE CAT1 (nolock) ON CAT1.ID_CTA_CONTABLE = CPP.ID_CTA_CONTABLE_PROV
    WHERE  a.efisc  = $ano and month(fec_poliza) = $mes and txt_clase_poliza = 3
	AND  SUBSTRING(D.CVE_CTA_CONTABLE,1,1) = 8
	
UNION ALL  

select DISTINCT NUM_POLIZA,FEC_POLIZA,IMP_TOTAL_POLIZA,CAT1.TXT_CTA_CONTABLE,CAT1.CVE_CTA_CONTABLE,CAT1.CVE_NIVEL2,CAT1.CVE_NIVEL3,CAT1.CVE_NIVEL4,CAT1.CVE_NIVEL5 

    from ctrl_poliza a (nolock)
    join ctrl_poliza_detalle c (nolock) on a.id_poliza = c.id_poliza
    join cat_cta_contable d (nolock) on c.id_cta_contable = d.id_cta_contable
    join cat_tipo_poliza b (nolock) on a.id_tipo_poliza = b.id_tipo_poliza
    LEFT JOIN CTRL_arrenDamiento_DETALLE CARD (nolock) ON A.ID_POLIZA = CARD.ID_POLIZA
    JOIN CTRL_ARRENDAMIENTO CAE (nolock) ON CARD.ID_ARRENDAMIENTO = CAE.ID_ARRENDAMIENTO
    JOIN CAT_INMUEBLE CI (nolock) ON CAE.ID_INMUEBLE = CI.ID_INMUEBLE
    JOIN CAT_CTA_CONTABLE CAT1 (nolock) ON CAT1.ID_CTA_CONTABLE = CI.ID_CTA_CONTABLE
    WHERE  a.efisc  = $ano and month(fec_poliza) = $mes and txt_clase_poliza = 3
    AND  SUBSTRING(D.CVE_CTA_CONTABLE,1,1) = 8 
    
UNION ALL

select DISTINCT NUM_POLIZA,FEC_POLIZA,IMP_TOTAL_POLIZA,CAT1.TXT_CTA_CONTABLE,CAT1.CVE_CTA_CONTABLE,CAT1.CVE_NIVEL2,CAT1.CVE_NIVEL3,CAT1.CVE_NIVEL4,CAT1.CVE_NIVEL5 

    from ctrl_poliza a (nolock)
    join ctrl_poliza_detalle c (nolock) on a.id_poliza = c.id_poliza
    join cat_cta_contable d (nolock) on c.id_cta_contable = d.id_cta_contable
    join cat_tipo_poliza b (nolock) on a.id_tipo_poliza = b.id_tipo_poliza
    LEFT JOIN CTRL_CONTRATO CARD ON A.ID_POLIZA = CARD.ID_POLIZA
    JOIN CAT_PROVEEDORes_cTA_CONTABLE CAE (nolock) ON CARD.ID_PROVEEDOR = CAE.ID_PROVEEDOR
    JOIN CAT_CTA_CONTABLE CAT1 (nolock) ON CAT1.ID_CTA_CONTABLE = CAE.ID_CTA_CONTABLE
    WHERE  a.efisc  = $ano and month(fec_poliza) = $mes and txt_clase_poliza = 3
    AND  SUBSTRING(D.CVE_CTA_CONTABLE,1,1) = 8 and cae.num_efisc = 2020

order by num_poliza";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));       

    if (odbc_num_rows($result) == 0) {
        echo '<script type="text/javascript">
					swal({
						position: "top",
				 		text: "No existen datos para el periodo seleccionado!",
				 		confirmButtonColor: "#DD6B55"
			 		}).then(function() {
						window.location.href = "index.php?pagina=poliza.php";
					});
	    			</script>';
    } else {
        echo '<div class="container">';
        echo '<div class="row justify-content-center">';
        echo '<a style="margin: 10px; color: #FFF" href="excelPoliza.php?recordID='.$ano.$mes.'" class="btn btn-primary" name="busca" id="busca">Exportar a excel</a>';
        echo '<table style="width:90%" border=1>';
        echo '<tr>';
        echo '<th>POLIZA</th>';
        echo '<th>FECHA</th>';
        echo '<th>TOTAL</th>';
        echo '<th>BENEFICIARIO</th>';
        echo '<th>CVE. CONTABLE</th>';
        echo '</tr>';

        while($datos = odbc_fetch_array($result)) {    
            echo '<tr>';
            echo '<td align="center">'.$datos["NUM_POLIZA"].'</td>';
            echo '<td>'.date("d-m-Y", strtotime($datos["FEC_POLIZA"])).'</td>';
            echo '<td>'.number_format($datos["IMP_TOTAL_POLIZA"], 2, '.', '').'</td>';
            echo '<td>'.utf8_encode($datos["txtctacontable"]).'</td>';
            echo '<td align="center">'.$datos["CVE_CTA_CONTABLE"].'</td>';
            echo '</tr>';        
        }

        echo '</table>';                
        echo '</div>';
        echo '</div>';
    }
}
?>