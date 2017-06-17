<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 03/08/15
 * Time: 16:11
 */
class Famille_catalogue{
    protected $CI;

    public function __construct() {

        // Super-objet CodeIgniter
        $this->CI =& get_instance();
    }

    /******************************
     * Renvoie le catalogue en service pour la famille du catalogue indiqué
     ******************************/
    protected function catalogue_en_service($code) {

        // recherche de la famille du catalogue indiqué
        $q = $this->CI->db->select('vfm_id')
            ->where('vfm_code',$code)
            ->get('v_familles');
        if ($q->num_rows() == 0) {
            return false;
        }
        $famille = $q->row()->vfm_id;

        $q = $this->CI->db->select('cat_id')
            ->where('cat_date=(SELECT max(A.cat_date) FROM t_catalogues A where A.cat_date <= CURDATE() AND A.cat_famille='.$famille.')',NULL,false)
            ->where('cat_famille',$famille)
            ->get('t_catalogues');
        if ($q->num_rows() == 0) {
            return false;
        }
        $catalogue = $q->row()->cat_id;

        // récupération du catalogue
        $q = $this->CI->db->where('art_catalogue',$catalogue)
            ->where('art_selection',1)
            ->get('t_articles');
        if ($q->num_rows() == 0) {
            return false;
        }
        return $q->result();
    }


    /******************************
     * En-tête pour l'exportation
     ******************************/
    protected function en_tete() {
        return array('Code article','Désignation','Libellé','Prix','Sélectionnable','Code production');
    }

}
// EOF