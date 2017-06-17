<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_taux_tva extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('tva_taux','number',"Taux de TVA"),
                array('tva_date','date',"Date d'application"),
                array('tva_etat','text',"Etat"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'tva_taux' => array("Taux de TVA",'number','tva_taux',true),
               'tva_date' => array("Date d'application",'date','tva_date',true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des taux de TVA
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_taux_tva';
        $this->db->start_cache();

        // lecture des informations
        $tva_taux = formatte_sql_lien('taux_tva/detail','tva_id','tva_taux');
        $tva_date = formatte_sql_date('tva_date');
        $tva_etat = "IF (tva_date > CURDATE(),'futur',IF(tva_date=(SELECT max(tva_date) FROM t_taux_tva where tva_date <= CURDATE()),'en service','périmé'))";
        $tva_etat2 = $tva_etat ." AS tva_etat";
        $this->db->select("tva_id AS RowID,tva_id,$tva_taux,$tva_date,$tva_etat2",false);
        //$this->db->where('tva_inactif is null');
        //$this->db->order_by("tva_date asc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.tva_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.tva_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.tva_archiver is NULL');
                $this->db->where($table.'.tva_inactif is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('tva_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'tva_etat'=>$tva_etat
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
            'tva_taux'=>'decimal',
            'tva_date'=>'date',
            'tva_etat'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * TVA en service
    ******************************/
    public function tva_en_service() {

        // lecture des informations
        $this->db->select("tva_taux",false);
        $this->db->where("tva_date=(SELECT max(tva_date) FROM t_taux_tva where tva_date <= CURDATE())");
        $this->db->where('tva_inactif is null');
        $q = $this->db->get('t_taux_tva');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Nouveau taux de TVA
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_taux_tva', $data);
        return $id;
    }

    /******************************
    * Détail d'un taux de TVA
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("tva_id,tva_taux,tva_date,IF (tva_date > CURDATE(),'futur',IF(tva_date=(SELECT max(tva_date) FROM t_taux_tva where tva_date <= CURDATE()),'en service','périmé')) AS tva_etat",false);
        $this->db->where('tva_id',$id);
        $this->db->where('tva_inactif is null');
        $q = $this->db->get('t_taux_tva');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    public function taux_historiques()
    {
        $q = $this->db->select('tva_taux')
            ->where('`tva_date` <= CURDATE()', null, false)
            ->get('t_taux_tva');
        $taux = array();
        foreach ($q->result() as $row) {
            $taux[] = $row->tva_taux;
        }
        return $taux;
    }

    /******************************
    * Mise à jour d'un taux de TVA
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('tva_id',$id)->get('t_taux_tva');
        $res =  $this->_update('t_taux_tva',$data,$id,'tva_id');
        return $res;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_taux_tva',$id,'tva_id','tva_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_taux_tva',$id,'tva_id','tva_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('tva_inactif' => null, 'tva_archiver' => null);
        return $this->_update('t_taux_tva',$data, $id,'tva_id');
    }

}
// EOF
