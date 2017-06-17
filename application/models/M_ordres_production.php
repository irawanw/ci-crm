<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_ordres_production extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('opr_code_article','text',"Code article"),
                array('opr_code_prod','text',"Code production"),
                array('opr_description','text',"Description"),
                array('opr_prix','number',"PUHT"),
                array('opr_quantite','number',"Quantité"),
                array('vfm_famille','ref',"Famille",'v_familles'),
                array('cmd_reference','ref',"Commande associée",'commandes','opr_commande','cmd_reference'),
                array('ard_id','ref',"Article de devis associé",'articles_devis','opr_article_devis','ard_id'),
                array('vfm_famille','ref',"Famille",'v_etats_ordres_production'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste des ordres de production
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
        $table = 't_ordres_production';
        $this->db->start_cache();
        // lecture des informations
        $this->db->select("opr_id as RowID,opr_code_article,opr_code_prod,opr_description,opr_prix,opr_quantite,opr_famille,vfm_famille,opr_commande,cmd_reference,opr_article_devis,ard_id,opr_etat,vfm_famille",false);
        $this->db->join('v_familles','vfm_id=opr_famille','left');
        $this->db->join('t_commandes','cmd_id=opr_commande','left');
        $this->db->join('t_articles_devis','ard_id=opr_article_devis','left');
        $this->db->join('v_etats_ordres_production','vfm_id=opr_etat','left');
        $this->db->where("opr_etat < 4");
        //$this->db->where('opr_inactif is null');
        //$this->db->order_by("opr_commande asc");
        //$q = $this->db->get('t_ordres_production');
        // if ($q->num_rows() > 0) {
        //     $result = $q->result();
        //     return $result;
        // }
        // else {
        //     return array();
        // }
        switch($void){
            case 'archived':
                $this->db->where($table.'.opr_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.opr_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.opr_archiver is NULL');
                $this->db->where($table.'.opr_inactif is NULL');
                break;
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
        } 

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des ordres de production
    ******************************/
    public function liste_par_commande($pere) {

        // lecture des informations
        $this->db->select("opr_id,opr_code_article,opr_code_prod,opr_description,opr_prix,opr_quantite,opr_famille,vfm_famille,opr_commande,cmd_reference,opr_article_devis,ard_id,opr_etat,vfm_famille",false);
        $this->db->join('v_familles','vfm_id=opr_famille','left');
        $this->db->join('t_commandes','cmd_id=opr_commande','left');
        $this->db->join('t_article_devis','ard_id=opr_article_devis','left');
        $this->db->join('v_etats_ordres_production','vfm_id=opr_etat','left');
        $this->db->where("opr_commande",$pere);
        $this->db->where('opr_inactif is null');
        $this->db->order_by("opr_commande asc");
            $q = $this->db->get('t_ordres_production');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_ordres_production',$id,'opr_id','opr_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_ordres_production',$id,'opr_id','opr_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('opr_inactif' => null, 'opr_archiver' => null);
        return $this->_update('t_ordres_production',$data, $id,'opr_id');
    }

}
// EOF
