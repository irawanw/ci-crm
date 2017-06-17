<?php


require_once('application/third_party/tcpdf/tcpdf.php');
require_once('application/third_party/fpdi/fpdi.php');
 
class PdfConcat extends \FPDI {
     var $files = array();
 
     function setFiles($files) {
          $this->files = $files;
     }
 
     function concat() {
          foreach($this->files AS $file) {
               $pagecount = $this->setSourceFile($file);
               for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage('P', array($s['w'], $s['h']));
                    $this->useTemplate($tplidx);
               }
          }
     }
}