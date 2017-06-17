<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_secteurs extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('sec_nom','text',"Nom"),
                array('vil_nom','ref',"Ville",'villes','sec_ville','vil_nom'),
                array('vts_type','ref',"Type de secteur",'v_types_secteurs'),
                array('sec_hlm','number',"Nombre de BAL HLM"),
                array('sec_hlm_stop','number',"Nombre de BAL HLM Stop Pub"),
                array('sec_res','number',"Nombre de BAL RES"),
                array('sec_res_stop','number',"Nombre de BAL RES Stop Pub"),
                array('sec_pav','number',"Nombre de BAL PAV"),
                array('sec_pav_stop','number',"Nombre de BAL PAV Stop Pub"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'sec_nom' => array("Nom",'text','sec_nom',true),
               'sec_type' => array("Type de secteur",'select',array('sec_type','vts_id','vts_type'),false),
               'sec_hlm' => array("Nombre de BAL HLM",'number','sec_hlm',true),
               'sec_hlm_stop' => array("Nombre de BAL HLM Stop Pub",'number','sec_hlm_stop',true),
               'sec_res' => array("Nombre de BAL RES",'number','sec_res',true),
               'sec_res_stop' => array("Nombre de BAL RES Stop Pub",'number','sec_res_stop',true),
               'sec_pav' => array("Nombre de BAL PAV",'number','sec_pav',true),
               'sec_pav_stop' => array("Nombre de BAL PAV Stop Pub",'number','sec_pav_stop',true)
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste des secteurs
    ******************************/
    public function liste_par_ville($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $sec_nom = formatte_sql_lien('secteurs/detail','sec_id','sec_nom');
        $vil_nom = formatte_sql_lien('villes/detail','vil_id','vil_nom');
        $this->db->select("sec_id AS RowID,sec_id,$sec_nom,$vil_nom,vts_type,sec_hlm,sec_hlm_stop,sec_res,sec_res_stop,sec_pav,sec_pav_stop",false);
        $this->db->join('t_villes','vil_id=sec_ville','left');
        $this->db->join('v_types_secteurs','vts_id=sec_type','left');
        $this->db->where("sec_ville",$pere);
        $this->db->where('sec_inactif is null');
        //$this->db->order_by("sec_nom asc");
        $this->db->stop_cache();

        $table = 't_secteurs';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_ville_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_ville_filterable_columns() {
    $filterable_columns = array(
            'sec_nom'=>'char',
            'vil_nom'=>'char',
            'vts_type'=>'char',
            'sec_hlm'=>'int',
            'sec_hlm_stop'=>'int',
            'sec_res'=>'int',
            'sec_res_stop'=>'int',
            'sec_pav'=>'int',
            'sec_pav_stop'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau secteur
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_secteurs', $data);
        return $id;
    }

    /******************************
    * Détail d'un secteur
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("sec_id,sec_nom,sec_ville,vil_nom,sec_type,vts_type,sec_hlm,sec_hlm_stop,sec_res,sec_res_stop,sec_pav,sec_pav_stop",false);
        $this->db->join('t_villes','vil_id=sec_ville','left');
        $this->db->join('v_types_secteurs','vts_id=sec_type','left');
        $this->db->where('sec_id',$id);
        $this->db->where('sec_inactif is null');
        $q = $this->db->get('t_secteurs');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un secteur
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('sec_id',$id)->get('t_secteurs');
        $res =  $this->_update('t_secteurs',$data,$id,'sec_id');
        return $res;
    }

}
// EOF
