<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_objectifs extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des objectifs d'un employé
    ******************************/
    public function liste_par_employe($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $obj_date = formatte_sql_lien('objectifs/detail','obj_id','obj_date');
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $this->db->select("obj_id AS RowID,obj_id,$obj_date,obj_id,vcr_critere,$emp_nom,obj_prevu,obj_realise",false);
        $this->db->join('v_criteres','vcr_id=obj_critere','left');
        $this->db->join('t_employes','emp_id=obj_employe','left');
        $this->db->where("obj_employe",$pere);
        $this->db->where('obj_inactif is null');
        //$this->db->order_by("obj_critere asc");
        //$this->db->order_by("obj_date asc");
        $this->db->stop_cache();

        $table = 't_objectifs';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_employe_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_employe_filterable_columns() {
    $filterable_columns = array(
            'obj_id'=>'int',
            'vcr_critere'=>'char',
            'emp_nom'=>'char',
            'obj_date'=>'date',
            'obj_prevu'=>'int',
            'obj_realise'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvel objectif
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_objectifs', $data);
        return $id;
    }

    /******************************
    * Détail d'un objectif
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("obj_id,obj_critere,vcr_critere,obj_employe,emp_nom,obj_date,obj_prevu,obj_realise",false);
        $this->db->join('v_criteres','vcr_id=obj_critere','left');
        $this->db->join('t_employes','emp_id=obj_employe','left');
        $this->db->where('obj_id',$id);
        $this->db->where('obj_inactif is null');
        $q = $this->db->get('t_objectifs');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un objectif
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('obj_id',$id)->get('t_objectifs');
        $res =  $this->_update('t_objectifs',$data,$id,'obj_id');
        return $res;
    }

/******************************
    * Suppression d'un objectif
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('obj_id',$id)->get('t_objectifs');

            $res = $this->_delete('t_objectifs',$id,'obj_id','obj_inactif');
        return $res;
    }

}
// EOF
