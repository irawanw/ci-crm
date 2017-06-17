<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_articles_devis extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des articles d'un devis
    ******************************/
    public function liste($pere) {

        // lecture des informations
        $this->db->select("ard_id,ard_code,ard_article,art_description,ard_description,ard_info,ard_prix,ard_quantite,art_catalogue,cat_version",false);
        $this->db->join('t_articles','art_id=ard_article','left');
        $this->db->join('t_catalogues','cat_id=art_catalogue','left');
        $this->db->where("ard_devis",$pere);
        $this->db->where('ard_inactif is null');
        $this->db->order_by("ard_code asc");
            $q = $this->db->get('t_articles_devis');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Détail d'un article de devis
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("ard_id,ard_code,ard_article,art_description,ard_description,ard_info,ard_prix,ard_quantite,art_catalogue,cat_version,ard_devis,dvi_reference,ard_remise_taux,ard_remise_ht,ard_remise_ttc",false);
        $this->db->join('t_articles','art_id=ard_article','left');
        $this->db->join('t_catalogues','cat_id=art_catalogue','left');
        $this->db->join('t_devis','dvi_id=ard_devis','left');
        $this->db->where('ard_id',$id);
        $this->db->where('ard_inactif is null');
        $q = $this->db->get('t_articles_devis');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

}
// EOF
