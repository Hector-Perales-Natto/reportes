<?php
include_once("Connections/reportes.php");
require 'vendor/autoload.php';

$ano = substr($_GET['recordID'], 0, 4); 
$mes = substr($_GET['recordID'], 4, 5);
$col = 0;
$ren = 0;

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
$hoja->getStyle('A:E')->getAlignment()->setHorizontal('center');
$hoja->setTitle('POLIZA DE DIARIO');
$hoja->mergeCells("A1:E1");
$hoja->setCellValue("A1", "POLIZA DE DIARIO");
$hoja->getColumnDimension('A')->setAutoSize(true);
$hoja->setCellValue("A2", "NUM. DE POLIZA");
$hoja->getColumnDimension('B')->setAutoSize(true);
$hoja->setCellValue("B2", "FECHA POLIZA");
$hoja->getColumnDimension('C')->setAutoSize(true);
$hoja->setCellValue("C2", "IMP. TOTAL");
$hoja->getColumnDimension('D')->setAutoSize(true);
$hoja->setCellValue("D2", "BENEFICIARIO");
$hoja->getColumnDimension('E')->setAutoSize(true);
$hoja->setCellValue("E2", "CVE. CONTABLE");

$col = 1;
$ren = 3;

while($datos = odbc_fetch_array($result)) { 
    $hoja->setCellValueByColumnAndRow($col,$ren, $datos["NUM_POLIZA"]);
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, date("d-m-Y", strtotime($datos["FEC_POLIZA"])));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, number_format($datos["IMP_TOTAL_POLIZA"], 2, '.', ''));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, utf8_encode($datos["txtctacontable"]));
    $hoja->setCellValueByColumnAndRow($col = $col+1,$ren, $datos["CVE_CTA_CONTABLE"]);    

    $col = 1;
    $ren = $ren + 1;
}

// $writer = new Xlsx($documento);
// $writer->save('C:\Users\user\Downloads\poliza.xlsx');

$nombreDelDocumento = "poliza.xlsx";
 
$writer = IOFactory::createWriter($documento, 'Xlsx');
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0'); 

$writer->save('php://output');

// echo "<script>alert('Reporte de polizas en excel generado con exito!');</script>";
// echo "<script>location.href = 'index.php?pagina=poliza.php';</script>";
exit;
?>