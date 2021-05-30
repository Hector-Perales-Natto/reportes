<!DOCTYPE html>
<html lang="es">
<head>
      <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="css/sweetalert2.min.css">                
	<title>Siprep</title>	

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
			<form name="registrar" id="registrar" class="form" method="POST" action="siprep.php" enctype="multipart/form-data">
				<table class="table table-sm">
					<tr><h5>SIPREP</h5></tr>
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

      $sql="select L.id_Poliza, L.Efisc, Num_Poliza, Fec_Poliza = convert(varchar(10),Fec_Poliza,103), Txt_Tipo_poliza, 
      Clase_Poliza = Case txt_clase_poliza When '1' Then 'EGRESOS'  When '2' Then 'NOMINA'  When '3' Then 'DIARIO' 
            When '4' 
            Then 'INGRESOS' When '5' Then 'CUENTA POR PAGAR' When '6' Then 'DIARIO-PPTO' END,  
      Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza = case when txt_concepto_poliza = '- POLIZA DE MIGRACION DE DATOS AL SIAF -' then
                                    txt_observacion_poliza else txt_concepto_poliza end , 

      Txt_Clave_SIPREP, Cve_Partida,
      imp_cargo = sum(isnull(imp_cargo,0)) , 
      imp_abono = sum(isnull(imp_abono,0))     
                  
      from ctrl_poliza               as L (nolock) 
      inner join ctrl_poliza_detalle as M (nolock) on L.id_poliza = M.id_poliza  
      inner join cat_cta_contable    as CC(nolock) on M.id_cta_contable = CC.id_cta_contable  and CC.Efisc = year(L.fec_Poliza)
      inner join cat_ppto            as CP(nolock) on M.id_ppto = CP.id_ppto  and CP.Efisc = year(L.fec_Poliza)
      inner join cat_tipo_poliza     as N (nolock) on L.id_tipo_Poliza = N.id_tipo_Poliza 
      inner join cat_tipo_doc        as O (nolock) on L.id_tipo_doc = O.id_tipo_Doc 

      inner join Cat_Region B(Nolock) on B.Id_Region = CP.Id_Region 
      inner join Cat_Proyecto C(Nolock) on C.Id_Proyecto = CP.Id_Proyecto
      inner join Cat_SubPrograma D(Nolock) on D.Id_SubPrograma = C.Id_SubPrograma
      inner join Cat_Programa E(Nolock) on E.Id_Programa = D.Id_Programa
      inner join Cat_SubFuncion F(Nolock) on F.Id_SubFuncion = E.Id_SubFuncion
      inner join Cat_Funcion G(Nolock) on G.Id_Funcion = F.Id_Funcion
      inner join Cat_UDes H(Nolock) on H.Id_UDes = CP.Id_UDes
      inner join Cat_UEjec I(Nolock) on I.Id_UEjecutora = H.Id_UEjecutora
      inner join Cat_UResp J(Nolock) on J.Id_UResponsable = I.Id_UResponsable
      inner join Cat_FFinanciamiento K(Nolock) on K.Id_FFinan = CP.Id_FFinan
      inner join Cat_Partida P(Nolock) on P.Id_Partida = CP.Id_Partida 
      INNER JOIN Cat_Matriz_Validacion CM (nolock)on CM.Id_Udes = CP.Id_Udes and CM.Id_Region = CP.Id_Region and CM.Id_Proyecto = CP.Id_Proyecto and CM.Id_FFinan = CP.Id_FFinan                        
      
      where L.id_status = 29 and month (fec_poliza) = $mes and year (fec_poliza) = $ano
      and cve_cta_contable in ('8271', '8272', '8276', '8275', '8274', '8251','8252', '8256', '8255','8254', '8241', '8242','8246', '8245', '8244') and num_poliza not in ('01714', '01715')

      group by  L.id_Poliza,Txt_Clave_SIPREP, Cve_Partida, L.Efisc, Num_Poliza, Fec_Poliza , Txt_Tipo_poliza, txt_clase_poliza,  
            Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza ,  txt_observacion_poliza   
      ORDER by  L.id_Poliza,Txt_Clave_SIPREP, Cve_Partida, L.Efisc, Num_Poliza, Fec_Poliza , Txt_Tipo_poliza, txt_clase_poliza,  
            Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza ,  txt_observacion_poliza";

      $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));      

      if (odbc_num_rows($result) == 0) {
            echo '<script type="text/javascript">
                        swal({
                              position: "top",
                              text: "No existen datos para el periodo seleccionado!",
                              confirmButtonColor: "#DD6B55"
                        }).then(function() {
                              window.location.href = "index.php?pagina=siprep.php";
                        });
                  </script>';
      } else {
            echo '<div class="container">';
            echo '<div class="row justify-content-center">';
            echo '<a style="margin: 10px; color: #FFF" href="excelSiprep.php?recordID='.$ano.$mes.'" class="btn btn-primary" name="busca" id="busca">Exportar a excel</a>';
            echo '<table style="width:90%" border=1>';
            echo '<tr>';
            echo '<th>ID. POLIZA</th>';
            echo '<th>EJERCICIO FISCAL</th>';
            echo '<th>NUM. DE POLIZA</th>';
            echo '<th>FECHA</th>';
            echo '<th>TIPO DE POLIZA</th>';
            echo '<th>CLASE DE POLIZA</th>';
            echo '<th>TIPO DOC.</th>';
            echo '<th>NÚM. REFERENCIA</th>';
            echo '<th>CONCEPTO POLIZA</th>';
            echo '<th>CLAVE SIPREP</th>';
            echo '<th>CLAVE PARTIDA</th>';
            echo '<th>IMP. CARGO</th>';
            echo '<th>IMP. ABONO</th>';
            echo '</tr>';
      
            while($datos = odbc_fetch_array($result)) {    
                  echo '<tr>';
                  echo '<td style="font-size: 80%;">'.$datos["id_Poliza"].'</td>';
                  echo '<td align="center" style="font-size: 80%;">'.$datos["Efisc"].'</td>';
                  echo '<td align="center" style="font-size: 80%;">'.$datos["Num_Poliza"].'</td>';
                  echo '<td style="font-size: 80%;">'.$datos["Fec_Poliza"].'</td>';
                  echo '<td style="font-size: 80%;">'.utf8_encode($datos["Txt_Tipo_poliza"]).'</td>';
                  echo '<td style="font-size: 80%;">'.$datos["Clase_Poliza"].'</td>';
                  echo '<td style="font-size: 80%;">'.$datos["Txt_Tipo_Doc"].'</td>';
                  echo '<td align="center" style="font-size: 80%;">'.$datos["num_referencia"].'</td>';
                  echo '<td style="font-size: 80%;">'.utf8_encode($datos["txt_Concepto_poliza"]).'</td>';
                  echo '<td style="font-size: 80%;">'.$datos["Txt_Clave_SIPREP"].'</td>';
                  echo '<td align="center" style="font-size: 80%;">'.$datos["Cve_Partida"].'</td>';
                  echo '<td style="font-size: 80%;">'.number_format($datos["imp_cargo"], 2, '.', '').'</td>';
                  echo '<td style="font-size: 80%;">'.number_format($datos["imp_abono"], 2, '.', '').'</td>';
                  echo '</tr>';        
            }
      
            echo '</table>';            
            echo '</div>';
            echo '</div>';
      }
}
?>