<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_villes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('vil_nom','text',"Nom"),
                array('vil_cp','text',"Code postal"),
                array('vil_nb_secteurs','text',"Nombre de secteurs"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'vil_nom' => array("Nom",'text','vil_nom',true),
               'vil_cp' => array("Code postal",'number','vil_cp',false)
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste des villes
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_villes';
        $this->db->start_cache();

        // lecture des informations
        $vil_nom = formatte_sql_lien('villes/detail','vil_id','vil_nom');
        $vil_nb_secteurs = "(SELECT COUNT(*) FROM t_secteurs A WHERE A.sec_ville=t_villes.vil_id)";
        $vil_nb_secteurs2 = $vil_nb_secteurs ." AS vil_nb_secteurs";
        $this->db->select("vil_id AS RowID,vil_id as id,vil_id,$vil_nom,vil_cp,$vil_nb_secteurs2",false);
        //$this->db->where('vil_inactif is null');
        //$this->db->order_by("vil_nom asc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.vil_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.vil_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.vil_archiver is NULL');
                $this->db->where($table.'.vil_inactif is NULL');
                break;
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'vil_nb_secteurs'=>$vil_nb_secteurs
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
            'vil_nom'=>'char',
            'vil_cp'=>'char',
            'vil_nb_secteurs'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle ville
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_villes', $data);
        return $id;
    }

    /******************************
    * Détail d'une ville
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("vil_id,vil_nom,vil_cp,(SELECT COUNT(*) FROM t_secteurs A WHERE A.sec_ville=t_villes.vil_id) AS vil_nb_secteurs",false);
        $this->db->where('vil_id',$id);
        $this->db->where('vil_inactif is null');
        $q = $this->db->get('t_villes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une ville
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('vil_id',$id)->get('t_villes');
        $res =  $this->_update('t_villes',$data,$id,'vil_id');
        return $res;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_villes',$id,'vil_id','vil_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_villes',$id,'vil_id','vil_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('vil_inactif' => null, 'vil_archiver' => null);
        return $this->_update('t_villes',$data, $id,'vil_id');
    }

}
// EOF
