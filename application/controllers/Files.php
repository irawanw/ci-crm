<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Date:
* Time:
*/
class Files extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('m_files');
    }

    public function remove($id) {
    	$row = $this->m_files->get($id);

    	if($row) {
    		if(file_exists($row->path)){    			
				unlink($row->path);
			}
    	}

    	$this->m_files->remove($id);

        echo json_encode(array('id' => $id));
    }
}