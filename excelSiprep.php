<?php
include_once("Connections/reportes.php");
require 'vendor/autoload.php';

$ano = substr($_GET['recordID'], 0, 4); 
$mes = substr($_GET['recordID'], 4, 5);
$col = 0;
$ren = 0;

$sql="select L.id_Poliza, L.Efisc, Num_Poliza, Fec_Poliza = convert(varchar(10),Fec_Poliza,103), Txt_Tipo_poliza, 
Clase_Poliza = Case txt_clase_poliza When '1' Then 'EGRESOS'  When '2' Then 'NOMINA'  When '3' Then 'DIARIO' 
      When '4' 
      Then 'INGRESOS' When '5' Then 'CUENTA POR PAGAR' When '6' Then 'DIARIO-PPTO' END,  
Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza = case when txt_concepto_poliza = '- POLIZA DE MIGRACION DE DATOS AL SIAF -' then
                              txt_observacion_poliza else txt_concepto_poliza end , 

Txt_Clave_SIPREP, Cve_Partida,
imp_cargo = sum(isnull(imp_cargo,0)) , 
imp_abono = sum(isnull(imp_abono,0))
--select * from ctrl_poliza
--select * from ctrl_poliza_detalle
--select * from cat_cta_contable 
--select * from Cat_Matriz_Validacion
            
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

-- 2012	
--where L.id_status = 29 and month (fec_poliza) = 12 and year (fec_poliza) = 2015
      --and cve_cta_contable in ('8251', '8252', '8255', '8256', '8254', '8241','8242', '8245', '8246','8244', '8231', '8232','8235', '8236', '8234') and num_poliza not in ('01714', '01715')
--2013 y 2014 y 2015 y 2016
where L.id_status = 29 and month (fec_poliza) = $mes and year (fec_poliza) = $ano
and cve_cta_contable in ('8271', '8272', '8276', '8275', '8274', '8251','8252', '8256', '8255','8254', '8241', '8242','8246', '8245', '8244') and num_poliza not in ('01714', '01715')

group by  L.id_Poliza,Txt_Clave_SIPREP, Cve_Partida, L.Efisc, Num_Poliza, Fec_Poliza , Txt_Tipo_poliza, txt_clase_poliza,  
      Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza ,  txt_observacion_poliza   
ORDER by  L.id_Poliza,Txt_Clave_SIPREP, Cve_Partida, L.Efisc, Num_Poliza, Fec_Poliza , Txt_Tipo_poliza, txt_clase_poliza,  
      Txt_Tipo_Doc, num_referencia,txt_Concepto_poliza ,  txt_observacion_poliza";

$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator('Hector Perales Natto')
    ->setLastModifiedBy('Hector Perales Natto')
    ->setTitle('Documento creado con phpSpreadsheet')
    ->setSubject('Archivo de excel')
    ->setDescription('Este documento fue creado para CAEM')
    ->setKeywords('Poliza')
    ->setCategory('Reporte financiero');

$hoja = $documento->getActiveSheet();
$hoja->getStyle('A:M')->getAlignment()->setHorizontal('center');
$hoja->setTitle('SIPREP');
$hoja->mergeCells("A1:M1");
$hoja->setCellValue("A1", "SIPREP");
$hoja->getColumnDimension('A')->setAutoSize(true);
$hoja->setCellValue("A2", "ID. DE POLIZA");
$hoja->getColumnDimension('B')->setAutoSize(true);
$hoja->setCellValue("B2", "E. FISC.");
$hoja->getColumnDimension('C')->setAutoSize(true);
$hoja->setCellValue("C2", "NÚM. DE POLIZA");
$hoja->getColumnDimension('D')->setAutoSize(true);
$hoja->setCellValue("D2", "FECHA POLIZA");
$hoja->getColumnDimension('E')->setAutoSize(true);
$hoja->setCellValue("E2", "TIPO POLIZA");
$hoja->getColumnDimension('F')->setAutoSize(true);
$hoja->setCellValue("F2", "CLASE POLIZA");
$hoja->getColumnDimension('G')->setAutoSize(true);
$hoja->setCellValue("G2", "TIPO DOC.");
$hoja->getColumnDimension('H')->setAutoSize(true);
$hoja->setCellValue("H2", "NÚM. REFERENCIA");
$hoja->getColumnDimension('I')->setAutoSize(true);
$hoja->setCellValue("I2", "CON. POLIZA");
$hoja->getColumnDimension('J')->setAutoSize(true);
$hoja->setCellValue("J2", "CLAVE SIPREP");
$hoja->getColumnDimension('K')->setAutoSize(true);
$hoja->setCellValue("K2", "CLAVE PARTIDA");
$hoja->getColumnDimension('L')->setAutoSize(true);
$hoja->setCellValue("L2", "IMP. CARGO");
$hoja->getColumnDimension('M')->setAutoSize(true);
$hoja->setCellValue("M2", "IMP. ABONO");

$col = 1;
$ren = 3;

while($datos = odbc_fetch_array($result)) { 
    $hoja->setCellValueByColumnAndRow($col,$ren, $datos["id_Poliza"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Efisc"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Num_Poliza"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Fec_Poliza"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, utf8_encode($datos["Txt_Tipo_poliza"]));    
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Clase_Poliza"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Txt_Tipo_Doc"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["num_referencia"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, utf8_encode($datos["txt_Concepto_poliza"]));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Txt_Clave_SIPREP"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Cve_Partida"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, number_format($datos["imp_cargo"], 2, '.', ''));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, number_format($datos["imp_abono"], 2, '.', ''));

    $col = 1;
    $ren = $ren + 1;
}

// $writer = new Xlsx($documento);
// $writer->save('C:\Users\user\Downloads\siprep.xlsx');

$nombreDelDocumento = "siprep.xlsx";
 
$writer = IOFactory::createWriter($documento, 'Xlsx');
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0'); 

$writer->save('php://output');

// echo "<script>alert('Reporte de SIPREP en excel generado con exito!');</script>";
// echo "<script>location.href = 'index.php?pagina=siprep.php';</script>";
exit;
?>