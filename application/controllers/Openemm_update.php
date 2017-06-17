<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Openemm_update extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->model('m_openemm_update');
    }
	
	public function index(){
		$this->m_openemm_update->refresh();
	}
}