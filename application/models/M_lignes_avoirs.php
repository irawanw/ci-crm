<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_lignes_avoirs extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des lignes d'un avoir
    ******************************/
    public function liste($pere) {

        // lecture des informations
        $this->db->select("lia_code,lia_prix,lia_quantite,lia_description,lia_avoir,avr_reference",false);
        $this->db->join('t_avoirs','avr_id=lia_avoir','left');
        $this->db->where("lia_avoir",$pere);
        $this->db->where('lia_inactif is null');
        $this->db->order_by("lia_code asc");
            $q = $this->db->get('t_lignes_avoirs');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
     * Lecture des catalogues
     ******************************/
    public function lecture_catalogue($code) {
        $famille = 'Famille_'.$code;
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library($famille,NULL,'famille');
        return $this->famille->catalogue();
    }

    /******************************
     * Constitution des avoirs
     ******************************/
    public function constitution($id,$commande) {
        $this->load->model('m_avoirs');
        switch ($commande) {
            case 'create':
                $data = $this->input->get('models');
                foreach ($data as &$d) {
                    $nouveau = $d;
                    $nouveau['lia_avoir'] = $id;
                    $res = $this->_insert('t_lignes_avoirs',$nouveau);
                    if ($res !== false) {
                        $this->m_avoirs->trigger_lignes_avoirs($id);
                        $d['lia_id'] = $res;
                    }
                }
                return $data;
                break;
            case 'destroy':
                if (!$this->m_avoirs->can_delete($id)) {
                    throw new MY_Exceptions_OperationNotAllowed('Not allowed to delete record given its current state');
                }
                $data = $this->input->get('models');
                $ops = 0;
                foreach ($data as $d) {
                    $res = $this->_delete('t_lignes_avoirs',$d['lia_id'],'lia_id','lia_inactif');
                    if ($res !== false) {
                        $this->m_avoirs->trigger_lignes_avoirs($id);
                        $ops++;
                    }
                }
                return $ops;
                break;
            case 'get':
                $this->db->select("lia_id,lia_code,lia_description,lia_prix,lia_quantite,lia_remise_taux,lia_remise_ht,lia_remise_ttc");
                $this->db->where("lia_avoir",$id);
                $this->db->where('lia_inactif is null');
                $this->db->order_by("lia_code asc");
                $q = $this->db->get('t_lignes_avoirs');
                if ($q->num_rows() > 0) {
                    $result = $q->result();
                    return $result;
                }
                else {
                    return array();
                }
                break;
            case 'update':
                $data = $this->input->get('models');
                $ops = 0;
                foreach ($data as $d) {
                    $d['lia_avoir'] = $id;
                    $res = $this->_update('t_lignes_avoirs',$d,$d['lia_id'],'lia_id');
                    if ($res !== false) {
                        $this->m_avoirs->trigger_lignes_avoirs($id);
                        $ops++;
                    }
                }
                return $ops;
                break;
            default: return false;
        }
    }
}

// EOF