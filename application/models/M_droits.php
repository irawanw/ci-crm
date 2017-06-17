<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_droits extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('vto_type','ref',"Type de droit d'utilisation",'v_types_droits'),
                array('dro_visibilite','text',"Visibilite totale"),
                array('prf_nom','ref',"Profil",'profils','dro_profil','prf_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'dro_type_droit' => array("Type de droit d'utilisation",'select',array('dro_type_droit','vto_id','vto_type'),true),
               'dro_visibilite' => array("Visibilite totale",'checkbox','dro_visibilite',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des droits d'utilisation d'un profil
    ******************************/
    public function liste_par_profil($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $dro_type_droit = formatte_sql_lien('droits_utilisation/detail','dro_id','dro_type_droit');
        $prf_nom = formatte_sql_lien('profils/detail','prf_id','prf_nom');
        $this->db->select("dro_id AS RowID,dro_id,$dro_type_droit,vto_type,dro_visibilite,$prf_nom",false);
        $this->db->join('v_types_droits','vto_id=dro_type_droit','left');
        $this->db->join('t_profils','prf_id=dro_profil','left');
        $this->db->where("dro_profil",$pere);
        $this->db->where('dro_inactif is null');
        //$this->db->order_by("dro_type_droit asc");
        $this->db->stop_cache();

        $table = 't_droits';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_profil_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_profil_filterable_columns() {
    $filterable_columns = array(
            'vto_type'=>'char',
            'vto_type'=>'char',
            'dro_visibilite'=>'char',
            'prf_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau droit d'utilisation
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_droits', $data);
        return $id;
    }

    /******************************
    * Détail d'un droit d'utilisation
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("dro_id,dro_type_droit,vto_type,dro_visibilite,dro_profil,prf_nom",false);
        $this->db->join('v_types_droits','vto_id=dro_type_droit','left');
        $this->db->join('t_profils','prf_id=dro_profil','left');
        $this->db->where('dro_id',$id);
        $this->db->where('dro_inactif is null');
        $q = $this->db->get('t_droits');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un droit d'utilisation
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('dro_id',$id)->get('t_droits');
        $res =  $this->_update('t_droits',$data,$id,'dro_id');
        return $res;
    }

/******************************
    * Suppression d'un droit d'utilisation
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('dro_id',$id)->get('t_droits');

            $res = $this->_delete('t_droits',$id,'dro_id','dro_inactif');
        return $res;
    }

}
// EOF
