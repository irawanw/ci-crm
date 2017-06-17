<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_suivi extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
     * Préparation
     ******************************/
    public function familles() {

        // recherche des familles de catalogue
        $q = $this->db->where('vfm_production',1)
            ->get('v_familles');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

}
// EOF
