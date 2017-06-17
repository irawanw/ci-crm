<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_cartes_blues extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('carte_id', 'ref', "id#", 'carte_id', 'carte_id', 'carte_id'),
                array('banque', 'text', "Banque", 'banque'),
                array('premiers_chiffres', 'text', "Numéro de compte 4 premiers chiffres", 'premiers_chiffres'),
                array('derniers_chiffres', 'text', "Numéro de compte 4 derniers chiffres", 'derniers_chiffres'),
                array('societe', 'text', "Societe", 'societe'),
                array('autre_que_societe', 'text', "Autre que société", 'autre_que_societe'),
            ),
            'write' => array(
                'banque' => array("Banque", 'text', 'banque', false),
                'premiers_chiffres' => array("Numéro de compte 4 premiers chiffres", 'text', 'premiers_chiffres', false),
                'derniers_chiffres' => array("Numéro de compte 4 derniers chiffres", 'text', 'derniers_chiffres', false),
                'societe' => array("Societe", 'select', array('societe', 'id', 'value'), false),
                'autre_que_societe' => array("Autre que société", 'text', 'autre_que_societe', false),
            )
        );

        return array_key_exists($type, $champs) ? $champs[$type] : array();
    }


    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_cartes_blues';
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*,carte_id as RowID, carte_id as checkbox");

        switch($void){
            case 'archived':
                $this->db->where('inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where('inactive is NULL');
                $this->db->where('deleted is NULL');
                break;
        }
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('carte_id', $id);
        }
		$this->db->stop_cache();
        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->carte_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'carte_id' => 'int',
            'banque' => 'char',
            'premiers_chiffres' => 'char',
            'derniers_chiffres' => 'char',
            'societe' => 'char',
            'autre_que_societe' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_cartes_blues table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_cartes_blues', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
		$this->db->select("*");
		$this->db->where('carte_id = "'.$id.'"');
		$q = $this->db->get('t_cartes_blues');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating test mails data
    ******************************/
    public function maj($data,$id) {
        return $this->_update('t_cartes_blues',$data,$id,'carte_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_cartes_blues',$id,'carte_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_cartes_blues',$id,'carte_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_cartes_blues',$data, $id,'carte_id');
    }

    /**
     * list for option dropdown
     * @return [type] [description]
     */
    public function liste_option($with_ajouter = false)
    {
        //$value = "CONCAT(banque, ' (', premiere ,' -- ', derniers , ' -- ', societe , ' -- ', )' ) as value";
        $value = "CONCAT( banque,  ' (', premiers_chiffres,  ' -- ', derniers_chiffres,  ' -- ', societe,  ')' ) AS value";
        $query = $this->db->select("carte_id as id, $value")
                          ->order_by('banque')
                          ->where('deleted is null') 
                          ->get('t_cartes_blues');
        
        if($with_ajouter) {
            $ajouter = new stdClass();
            $ajouter->id = "ajouter";
            $ajouter->value = "Ajouter";

            if($query->num_rows() > 0) {
                $data = $query->result();
                array_unshift($data, $ajouter);
            } else {
                $data[] = $ajouter;
            }
        } else {
            $data = $query->result();
        }

        return $data;
    }

	
    public function liste_societe()
    {
        return $this->db->select('scv_nom as id, scv_nom as value')->order_by('scv_nom', 'ASC')->get('t_societes_vendeuses')->result();
    }
	
    public function form_option($values, $inc_index = false)
    {
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();
            if ($inc_index) {
                $val->id = $i;
            } else {
                $val->id = $values[$i];
            }

            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }
}
// EOF
