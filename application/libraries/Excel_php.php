<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/Excel/Classes/PHPExcel.php";
class Excel extends PHPExcel {

	private $path_save = "";
	private $path_download = "";
    
    public function __construct() {
        parent::__construct();
        $CI =& get_instance();
        $CI->config->load('export');
        $this->path_save = $CI->config->item('path_save');
        $this->path_download = $CI->config->item('path_download');
    }

    public function export($params){
    	$alphas = range("A", "Z");
    	$objPHPExcel = new PHPExcel();
    	$sNAMESS = "";

    	foreach ($params as $key => $value) {
    		${$key}=$value;
    	}

		if(!isset($columns)){
			echo "Column definition not available";
			die();
		}else{
			$totalColumn = count($columns);
			if($totalColumn==0){
				echo "Column definition not available!";
				die();
			}else{
				if($totalColumn>26){
					$alphas = $this->createColumnsArray($totalColumn, array(), 0);
				}
			}
		}
			
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($sNAMESS);
		//$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(55);
		$loop = 1;
		$arrColumns =0;

		//if header exist, set header first
		if(isset($headers)) {
			foreach($headers as $header) {
				$objPHPExcel->getActiveSheet()->setCellValue($alphas[$arrColumns].$loop, $header['text']);

				if(array_key_exists("fontSize", $header)) {
					$objPHPExcel->getActiveSheet()->getStyle($alphas[$arrColumns].$loop)->getFont()->setSize($header['fontSize']);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle($alphas[$arrColumns].$loop)->getFont()->setBold(true);
				$loop++;
			}
		}

		if(isset($columns)) {
			foreach ($columns as $colvalue) {
				foreach ($colvalue as $keycol => $valuecol) {
					${$keycol}=$valuecol;
				}

				if(isset($title)){
					if($title!=""){
						$objPHPExcel->getActiveSheet()->setCellValue($alphas[$arrColumns].$loop, $title);
					}
				}

				if(isset($fontsize)){
					if($fontsize!=0){
						$objPHPExcel->getActiveSheet()->getStyle($alphas[$arrColumns].$loop)->getFont()->setSize($fontsize);
					}
				}

				$objPHPExcel->getActiveSheet()->getStyle($alphas[$arrColumns].$loop)->getFont()->setBold(true);
				$arrColumns++;
			}

			$loop++;
		}

			if(isset($records)){
				$nomor = 1;
				$rowloc = $loop;
				// echo "
				foreach ($records as $key => $value) {
					$colloc=0;
					$arrColumns =0;
					foreach ($columns as $colvalue) {
						if(array_key_exists("name", $colvalue)){
							if($colvalue['name'] =="nomor"){
								$valueval = $nomor;	
							}else{
								$field = $colvalue['name'];
								$valueval = $value->$field;	
								$valueval = strip_tags($valueval);
								$valueval = str_replace("&nbsp;", "", $valueval);

								//format date
								if($colvalue['format'] == "date") {
									$valueval = $valueval != "0000-00-00" ? formatte_date($valueval) : "";
								}

								if($colvalue['format'] == "datetime") {
									$valueval = ($valueval != "0000-00-00 00:00:00") ? formatte_dateheure($valueval) : "";
								}
							}

							$objPHPExcel->getActiveSheet()->setCellValue($alphas[$arrColumns].$rowloc, $valueval);
							$objPHPExcel->getActiveSheet()->getColumnDimension($alphas[$arrColumns])->setAutoSize(true);
							$found = true;
						}

						if(array_key_exists("format", $colvalue)){
							switch ($colvalue['format']) {
								case 'text':
								case 'number':
									break;
								case 'decimal':
									$objPHPExcel->getActiveSheet()->getStyle($alphas[$arrColumns].$rowloc)->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
									break;			
							}
						}

						$arrColumns++;			
					}

					$rowloc++;
					$nomor++;
				}
			}
			
			//header('Content-Type: application/vnd.ms-excel'); //mime type
			//header('Content-Disposition: attachment;filename="'.$sFILNAM.'.xls"'); 
			//header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			//$objWriter->save('php://output');    	
			$objWriter->save($this->path_save.$sFILNAM.'.xls');
			$urlFile = base_url().$this->path_download.$sFILNAM.'.xls';
			return $urlFile;
			
    }

    protected function createColumnsArray($totalColumn, $columns, $endColumn){
      $letters = range('A', 'Z');
 	  $maxLetters = 26;
 	  $loop = ($totalColumn > $maxLetters) ? 26 : $totalColumn;
 	  $prefix = $endColumn == 0 ? "" : $letters[$endColumn - 1];

 	  for ($i=0; $i < $loop; $i++) { 
 	  	$columns[] = $prefix.$letters[$i];
 	  }

 	  if($totalColumn > $maxLetters) {
 	  	$totalColumn = $totalColumn - $maxLetters;
 	  	$endColumn += 1;
 	  	$columns = $this->createColumnsArray($totalColumn, $columns, $endColumn);
 	  }

 	  return $columns; 
      
    }
}
