<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property CI_DB_query_builder $db
*/
class M_societes_vendeuses extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('scv_nom','text',"Nom"),
                array('scv_adresse','text',"Adresse complète"),
                array('scv_telephone','text',"Téléphone"),
                array('scv_fax','text',"Fax"),
                array('scv_capital','text',"Capital"),
                array('scv_rcs','text',"RCS"),
                array('scv_siret','text',"SIRET"),
                array('scv_en_production','text',"En production"),
                array('scv_id_comptable','text',"Gestion des id comptables"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'scv_nom' => array("Nom",'text','scv_nom',true),
               'scv_adresse' => array("Adresse complète",'textarea','scv_adresse',false),
               'scv_telephone' => array("Téléphone",'number','scv_telephone',true),
               'scv_fax' => array("Fax",'number','scv_fax',false),
               'scv_email' => array("Email",'email','scv_email',false),
               'scv_capital' => array("Capital",'text','scv_capital',true),
               'scv_rcs' => array("RCS",'text','scv_rcs',true),
               'scv_siret' => array("SIRET",'text','scv_siret',true),
               'scv_taux_annuel' => array("Pénalités (taux ann.)",'number','scv_taux_annuel',true),
               'scv_taux_mensuel' => array("Pénalités (taux mens.)",'number','scv_taux_mensuel',true),
               'scv_no_devis' => array("Dernier n° devis",'number','scv_no_devis',true),
               'scv_no_facture' => array("Dernier n° facture",'number','scv_no_facture',true),
               'scv_no_avoir' => array("Dernier n° avoir",'number','scv_no_avoir',true),
               'scv_format_devis' => array("Format des n° de devis",'text','scv_format_devis',false),
               'scv_format_facture' => array("Format des n° de factures",'text','scv_format_facture',false),
               'scv_format_avoir' => array("Format des n° d'avoirs",'text','scv_format_avoir',false),
               'scv_modele_devis' => array("Modèle devis",'text','scv_modele_devis',true),
               'scv_modele_facture' => array("Modèle facture",'text','scv_modele_facture',true),
               'scv_modele_avoir' => array("Modèle avoir",'text','scv_modele_avoir',true),
               'scv_en_production' => array("En production",'checkbox','scv_en_production',false),
               'scv_id_comptable' => array("Gestion des id comptables",'checkbox','scv_id_comptable',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des enseignes
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_societes_vendeuses';
        $this->db->start_cache();

        // lecture des informations
        $scv_nom = formatte_sql_lien('societes_vendeuses/detail','scv_id','scv_nom');
        $this->db->select("scv_id AS RowID,scv_id,$scv_nom,scv_adresse,scv_telephone,scv_fax,scv_capital,scv_rcs,scv_siret,scv_en_production,scv_id_comptable",false);
        //$this->db->order_by("scv_nom asc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.scv_archiver is NOT NULL');
                break;
            // case 'deleted':
            //     $this->db->where($table.'.vil_inactif is NOT NULL');
            //     break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.scv_archiver is NULL');
                //$this->db->where($table.'.vil_inactif is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('scv_id', $id);
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
            'scv_nom'=>'char',
            'scv_adresse'=>'char',
            'scv_telephone'=>'char',
            'scv_fax'=>'char',
            'scv_capital'=>'char',
            'scv_rcs'=>'char',
            'scv_siret'=>'char',
            'scv_en_production'=>'char',
            'scv_id_comptable'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle enseigne
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_societes_vendeuses', $data);
        return $id;
    }

    /******************************
    * Détail d'une enseigne
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("scv_id,scv_nom,scv_adresse,scv_telephone,scv_fax,scv_email,scv_capital,scv_rcs,scv_siret,scv_taux_annuel,scv_taux_mensuel,scv_no_devis,scv_no_facture,scv_no_avoir,scv_format_devis,scv_format_facture,scv_format_avoir,scv_modele_devis,scv_modele_facture,scv_modele_avoir,scv_en_production,scv_id_comptable",false);
        $this->db->where('scv_id',$id);
        $q = $this->db->get('t_societes_vendeuses');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une enseigne
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('scv_id',$id)->get('t_societes_vendeuses');
        $res =  $this->_update('t_societes_vendeuses',$data,$id,'scv_id');
        return $res;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_societes_vendeuses',$id,'scv_id','scv_archiver');
    }
    
    /******************************
     * Liste Option
     ******************************/
    public function liste_option()
    {
    	$this->db->select('scv_id as id, scv_nom as value');
    	$sql = $this->db->get('t_societes_vendeuses');
    	if ($sql->num_rows() > 0) {
    		$resultat = $sql->result();
    		return $resultat;
    	}
    	return array();
    }

    public function liste_id_comptable($gestion = null)
    {
        $this->db->select('scv_id, scv_nom, scv_id_comptable');
        if ($gestion === false) {
            $this->db->where('scv_id_comptable', 0);
        } elseif ($gestion === true) {
            $this->db->where('scv_id_comptable', 1);
        }
        $sql = $this->db->get('t_societes_vendeuses');
        if ($sql->num_rows() > 0) {
            $resultat = $sql->result();
            return $resultat;
        }
        return array();
    }

}
// EOF
