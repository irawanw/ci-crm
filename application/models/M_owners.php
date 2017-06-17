<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_owners extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('owner_id', 'ref', "id#", 'owner_id', 'owner_id', 'owner_id'),
                array('nom', 'text', "Nom", 'nom'),
                array('email', 'text', "Email", 'email'),
                array('telephone', 'text', "Téléphone", 'telephone'),
                array('adresse', 'text', "Adresse", 'adresse'),
                array('contact', 'text', "Contact", 'contact'),             
            ),
            'write' => array(
                'nom' => array("Nom", 'text', 'nom', false),
                'email' => array("Email", 'text', 'email', false),
                'telephone' => array("Téléphone", 'text', 'telephone', false),
                'adresse' => array("Adresse", 'text', 'adresse', false),
                'contact' => array("Contact", 'text', 'contact', false),                   
            )
        );

        return $champs[$type];
    }


    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_owners';
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*,owner_id as RowID, owner_id as checkbox");

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
         $this->db->where('owner_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->owner_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'owner_id' => 'int',
            'nom' => 'char',
            'email' => 'char',
            'telephone' => 'char',
            'adresse' => 'char',
            'contact' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_owners table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_owners', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
		$this->db->select("*");
		$this->db->where('owner_id = "'.$id.'"');
		$q = $this->db->get('t_owners');
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
        return $this->_update('t_owners',$data,$id,'owner_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_owners',$id,'owner_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_owners',$id,'owner_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_owners',$data, $id,'owner_id');
    }

    /**
     * list for option dropdown
     * @return [type] [description]
     */
    public function liste_option($with_ajouter = false)
    {
        $value = "CONCAT(nom, ' (', email ,' -- ', telephone , ' -- ', banque ,' )' ) as value";
        $query = $this->db->select("owner_id as id, $value")
							->where('inactive IS NULL AND deleted is NULL')
							->order_by('nom')
							->get('t_owners');
        
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
