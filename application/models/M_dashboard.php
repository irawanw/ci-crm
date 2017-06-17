<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_dashboard extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function liste_secteur_type()
    {
    	return $this->db->select('vts_id as id, vts_type as name')->get('v_types_secteurs')->result();
    }
}