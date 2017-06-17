<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_lignes_factures extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('lif_code','text',"Code article"),
                array('lif_prix','number',"PUHT"),
                array('lif_quantite','number',"Quantité"),
                array('lif_description','text',"Description"),
                array('fac_reference','ref',"Facture",'factures','lif_facture','fac_reference')
            ),
            'write' => array(
               
            )
        );

        return $champs[$type];
    }
    
    /******************************
    * Liste des lignes d'une facture
    ******************************/
    public function liste($pere) {

        // lecture des informations
        $this->db->select("lif_code,lif_prix,lif_quantite,lif_description,lif_facture,fac_reference",false);
        $this->db->join('t_factures','fac_id=lif_facture','left');
        $this->db->where("lif_facture",$pere);
        $this->db->where('lif_inactif is null');
        $this->db->order_by("lif_code asc");
            $q = $this->db->get('t_lignes_factures');
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
     * Constitution des factures
     ******************************/
    public function constitution($id,$commande) {
        $this->load->model('m_factures');
        switch ($commande) {
            case 'create':
                $data = $this->input->get('models');
                foreach ($data as &$d) {
                    $nouveau = $d;
                    $nouveau['lif_facture'] = $id;
                    $res = $this->_insert('t_lignes_factures',$nouveau);
                    if ($res !== false) {
                        $this->m_factures->trigger_lignes_factures($id);
                        $d['lif_id'] = $res;
                    }
                }
                return $data;
                break;
            case 'destroy':
                $data = $this->input->get('models');
                $ops = 0;
                foreach ($data as $d) {
                    $res = $this->_delete('t_lignes_factures',$d['lif_id'],'lif_id','lif_inactif');
                    if ($res !== false) {
                        $this->m_factures->trigger_lignes_factures($id);
                        $ops++;
                    }
                }
                return $ops;
                break;
            case 'get':
                $this->db->select("lif_id,lif_code,lif_description,lif_prix,lif_quantite,lif_remise_taux,lif_remise_ht,lif_remise_ttc");
                $this->db->where("lif_facture",$id);
                $this->db->where('lif_inactif is null');
                $this->db->order_by("lif_code asc");
                $q = $this->db->get('t_lignes_factures');
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
                    $d['lif_facture'] = $id;
                    $res = $this->_update('t_lignes_factures',$d,$d['lif_id'],'lif_id');
                    if ($res !== false) {
                        $this->m_factures->trigger_lignes_factures($id);
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
