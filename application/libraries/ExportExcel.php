<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 04/04/2016
 * Time: 14:18
 */
require 'application/third_party/PHPExcel/IOFactory.php';
class ExportExcel {
    protected $CI;

    public function __construct() {

        // Super-objet CodeIgniter
        $this->CI =& get_instance();
    }


    /******************************
     * Creation de l'export factures
     ******************************/
    public function factures($factures) {
        PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        $PHPExcel = new PHPExcel();
        $sheet = $PHPExcel->getActiveSheet();

        $col = 0;
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Société');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'N°facture');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Jour');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Mois');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Année');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Client');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'ID comptable');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Montant HT');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'TVA');
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Montant TTC');
        $row = 2;
        foreach ($factures as $f) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->scv_nom);
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->fac_reference);
            $sheet->setCellValueByColumnAndRow($col++, $row, substr($f->fac_date,8,2));
            $sheet->setCellValueByColumnAndRow($col++, $row, substr($f->fac_date,5,2));
            $sheet->setCellValueByColumnAndRow($col++, $row, substr($f->fac_date,0,4));
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->ctc_nom);
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->ctc_id_comptable);
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->total_HT);
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->total_TTC-$f->total_HT);
            $sheet->setCellValueByColumnAndRow($col++, $row, $f->total_TTC);
            $row++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $fichier = 'tmp/export_factures.xlsx';
        $objWriter->save($fichier);
        return $fichier;
    }
}
// EOF