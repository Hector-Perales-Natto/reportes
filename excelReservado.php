<?php
include_once("Connections/reportes.php");
require 'vendor/autoload.php';

$ano = substr($_GET['recordID'], 0, 4); 
$mes = substr($_GET['recordID'], 4, 5);
$col = 0;
$ren = 0;

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
    ->setKeywords('Reservado')
    ->setCategory('Reporte de Presupuesto Reservado');

$hoja = $documento->getActiveSheet();
$hoja->getStyle('A:E')->getAlignment()->setHorizontal('center');
$hoja->setTitle('PRESUPUESTO RESERVADO');
$hoja->mergeCells("A1:E1");
$hoja->setCellValue("A1", "PRESUPUESTO RESERVADO");
$hoja->getColumnDimension('A')->setAutoSize(true);
$hoja->setCellValue("A2", "CVE. PRESUPUESTO");
$hoja->getColumnDimension('B')->setAutoSize(true);
$hoja->setCellValue("B2", "MES");
$hoja->getColumnDimension('C')->setAutoSize(true);
$hoja->setCellValue("C2", "ID. PRESUPUESTO");
$hoja->getColumnDimension('D')->setAutoSize(true);
$hoja->setCellValue("D2", "IMPORTE");

$col = 1;
$ren = 3;

while($datos = odbc_fetch_array($result)) { 
    $hoja->setCellValueByColumnAndRow($col,$ren, $datos["Cve_Ppto"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Num_Mes"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Id_Ppto"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["Imp_Reservado"]);    

    $col = 1;
    $ren = $ren + 1;
}

$nombreDelDocumento = "reservado.xlsx";
 
$writer = IOFactory::createWriter($documento, 'Xlsx');
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0'); 

$writer->save('php://output');
exit;
?>