<?php
include_once("Connections/reportes.php");
require 'vendor/autoload.php';

$ano = substr($_GET['recordID'], 0, 4); 
$mes = substr($_GET['recordID'], 4, 5);
$msi = $mes+1;
$col = 0;
$ren = 0;

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
$hoja->getStyle('A:H')->getAlignment()->setHorizontal('center');
$hoja->setTitle('ECONOMIA');
$hoja->mergeCells("A1:H1");
$hoja->setCellValue("A1", "ECONOMIA");
$hoja->getColumnDimension('A')->setAutoSize(true);
$hoja->setCellValue("A2", "EJERCICIO FISCAL");
$hoja->getColumnDimension('B')->setAutoSize(true);
$hoja->setCellValue("B2", "MES DE AFECTACION");
$hoja->getColumnDimension('C')->setAutoSize(true);
$hoja->setCellValue("C2", "CVE. PRESUPUESTAL");
$hoja->getColumnDimension('D')->setAutoSize(true);
$hoja->setCellValue("D2", "MES DE DEDUCCION");
$hoja->getColumnDimension('E')->setAutoSize(true);
$hoja->setCellValue("E2", "MES DE ASIGNACION");
$hoja->getColumnDimension('F')->setAutoSize(true);
$hoja->setCellValue("F2", "TOTAL");
$hoja->getColumnDimension('G')->setAutoSize(true);
$hoja->setCellValue("G2", "FOLIO");
$hoja->getColumnDimension('H')->setAutoSize(true);
$hoja->setCellValue("H2", "ESTATUS");

$col = 1;
$ren = 3;

while($datos = odbc_fetch_array($result)) { 
    $hoja->setCellValueByColumnAndRow($col,$ren, $datos["NUM_EFISC"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["MES_AFECTACION"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["ppto"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["MES_DEDUCCION"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["MES_ASIGNACION"]);    
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, number_format($datos["IMP_ECONOMIA"], 2, '.', ''));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["id_folio_economia"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["id_status"]);    

    $col = 1;
    $ren = $ren + 1;
}

// $writer = new Xlsx($documento);
// $writer->save('C:\Users\user\Downloads\economia.xlsx');

$nombreDelDocumento = "economia.xlsx";
 
$writer = IOFactory::createWriter($documento, 'Xlsx');
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0'); 

$writer->save('php://output');

// echo "<script>alert('Reporte de economia en excel generado con exito!');</script>";
// echo "<script>location.href = 'index.php?pagina=economia.php';</script>";
exit;
?>