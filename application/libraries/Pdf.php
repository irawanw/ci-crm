<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 06/10/15
 * Time: 16:50
 */
require 'application/third_party/mpdf60/mpdf.php';
class Pdf {
    protected $CI;

    public function __construct() {

        // Super-objet CodeIgniter
        $this->CI =& get_instance();
    }

    /******************************
     * Creation d'un pdf
     ******************************/
    public function creation($html,$chemin,$production) {
        $mpdf=new mPDF();
        if ($production == 0) {
            $mpdf->SetWatermarkText('DOCUMENT DE TEST NON CONTRACTUEL');
            $mpdf->showWatermarkText = true;
        }
        $mpdf->WriteHTML($html);
        $pdf = $mpdf->Output('','S');
        if (file_put_contents($chemin,$pdf) === false) {
            return false;
        }
        return true;
    }

}
// EOF