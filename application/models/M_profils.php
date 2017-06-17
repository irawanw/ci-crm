<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_profils extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('prf_nom','text',"Nom"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'prf_nom' => array("Nom",'text','prf_nom',true)
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste des profils
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $prf_nom = formatte_sql_lien('profils/detail','prf_id','prf_nom');
        $this->db->select("prf_id AS RowID,prf_id,$prf_nom,",false);
        $this->db->where('prf_inactif is null');
        //$this->db->order_by("prf_nom asc");
        
        $id = intval($void);
        if ($id > 0) {
         $this->db->where('prf_id', $id);
        }

        $this->db->stop_cache();

        $table = 't_profils';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'prf_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau profil
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_profils', $data);
        return $id;
    }

    /******************************
    * Détail d'un profil
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("prf_id,prf_nom",false);
        $this->db->where('prf_id',$id);
        $this->db->where('prf_inactif is null');
        $q = $this->db->get('t_profils');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un profil
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('prf_id',$id)->get('t_profils');
        $res =  $this->_update('t_profils',$data,$id,'prf_id');
        return $res;
    }

/******************************
    * Suppression d'un profil
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('prf_id',$id)->get('t_profils');

            $res = $this->_delete('t_profils',$id,'prf_id','prf_inactif');
        return $res;
    }

}
// EOF
