<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_plaintes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('pla_id','id',"Identifiant"),
                array('pla_date','date',"Date de la plainte"),
                array('pla_description','text',"Teneur de la plainte"),
                array('sec_nom','ref',"Secteur concerné",'secteurs','pla_secteur','sec_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'pla_date' => array("Date de la plainte",'date','pla_date',true),
               'pla_description' => array("Teneur de la plainte",'textarea','pla_description',true),
               'pla_secteur' => array("Secteur concerné",'select',array('pla_secteur','sec_id','sec_nom'),true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des plaintes
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_plaintes';
        $this->db->start_cache();

        // lecture des informations
        $pla_date = formatte_sql_lien('plaintes/detail','pla_id','pla_date');
        $sec_nom = formatte_sql_lien('secteurs/detail','sec_id','sec_nom');
        $this->db->select("pla_id AS RowID,pla_id,pla_id as id,$pla_date,pla_id,pla_description,$sec_nom",false);
        $this->db->join('t_secteurs','sec_id=pla_secteur','left');
        //$this->db->where('pla_inactif is null');
        //$this->db->order_by("pla_date desc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.pla_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.pla_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.pla_archiver is NULL');
                $this->db->where($table.'.pla_inactif is NULL');
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
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->id.'">';
        } 

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'pla_id'=>'int',
            'pla_date'=>'date',
            'pla_description'=>'char',
            'sec_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle plainte
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_plaintes', $data);
        return $id;
    }

    /******************************
    * Détail d'une plainte
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("pla_id,pla_date,pla_description,pla_secteur,sec_nom",false);
        $this->db->join('t_secteurs','sec_id=pla_secteur','left');
        $this->db->where('pla_id',$id);
        $this->db->where('pla_inactif is null');
        $q = $this->db->get('t_plaintes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une plainte
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('pla_id',$id)->get('t_plaintes');
        $res =  $this->_update('t_plaintes',$data,$id,'pla_id');
        return $res;
    }

/******************************
    * Suppression d'une plainte
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('pla_id',$id)->get('t_plaintes');

            $res = $this->_delete('t_plaintes',$id,'pla_id','pla_inactif');
        return $res;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_plaintes',$id,'pla_id','pla_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_plaintes',$id,'pla_id','pla_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('pla_inactif' => null, 'pla_archiver' => null);
        return $this->_update('t_plaintes',$data, $id,'pla_id');
    }

}
// EOF
