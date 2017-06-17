<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/xlsxwriter.class.php";
class Excel{

	private $path_save = "";
	private $path_download = "";
	private $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('export');
        $this->path_save = $this->CI->config->item('path_save');
        $this->path_download = $this->CI->config->item('path_download');
    }

    public function export($params){
    	foreach ($params as $key => $value) {
    		${$key}=$value;
    	}   

    	$writer = new XLSXWriter();
    	$totalColumn = count($columns);
    	$style = array( 'font'=>'Arial','font-size'=>12,'font-style'=>'bold');

    	//if header exist, set header first
		if(isset($headers)) {
			foreach($headers as $header) {	
				$text = array();
				for($i=0; $i < $totalColumn; $i++) {
					if($i == 0) {
						$text[$i] = $header['text'];
					} else {
						$text[$i] = "";
					}
				}

				$writer->writeSheetRow('Sheet1', $text, $style);
			}
		}

    	$titles = array();
    	foreach($columns as $column)
    	{
    		$title = $column['title'];
    		$format = $column['format'];
    		$titles[] = $title;//$this->get_format_header($format);
    	}

		$writer->writeSheetRow('Sheet1', $titles, array('font-style' => 'bold','halign'=>'center'));
        foreach($records as $row)
        {
            $data = array();
            
            foreach($columns as $column)
            {
            	$field = $column['name'];
				$valueval = $row->$field;	
				$valueval = strip_tags($valueval);
				$valueval = str_replace("&nbsp;", "", $valueval);

				//format date
				if($column['format'] == "date") {
					$valueval = $valueval != "0000-00-00" ? formatte_date($valueval) : "";
				}

				if($column['format'] == "datetime") {
					$valueval = ($valueval != "0000-00-00 00:00:00") ? formatte_dateheure($valueval) : "";
				}

                $data[] = $valueval;    
            }
            
            $writer->writeSheetRow('Sheet1', $data);
        }

        $writer->writeToFile($this->path_save.$sFILNAM.'.xlsx');
        //echo '#'.floor((memory_get_peak_usage())/1024/1024)."MB"."\n";
        $urlFile = base_url().$this->path_download.$sFILNAM.'.xlsx';
		return $urlFile;
			
    }
}
