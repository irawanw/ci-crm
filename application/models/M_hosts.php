<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_hosts extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('host_id', 'ref', "id#", 'host_id', 'host_id', 'host_id'),
                array('nom', 'text', "Nom", 'nom'),
                array('pays', 'text', 'Pays', 'pays'),
                array('type', 'text', 'Type', 'type'),
                array('lien', 'text', 'Lien', 'lien'),
                array('tel_support_technique', 'text', 'Tel Support technique', 'tel_support_technique'),
                array('mail_support_technique', 'text', 'Mail Support technique', 'mail_support_technique'),
                array('tel_service_commercial', 'text', 'Tel Service Commercial', 'tel_service_commercial'),
            ),
            'write' => array(
                'nom' => array("Nom", 'text', 'nom', false),
                'pays' => array("Pays", 'text', 'pays', false),
                'type' => array('type', 'select', array('type', 'id', 'value'), false),
                'lien' => array("Lien", 'text', 'lien', false),
                'tel_support_technique' => array("Tel Support technique", 'text', 'tel_support_technique', false),
                'mail_support_technique' => array("Mail Support technique", 'text', 'mail_support_technique', false),
                'tel_service_commercial' => array("Tel Service Commercial", 'text', 'tel_service_commercial', false),
            )
        );

        return $champs[$type];
    }



    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_hosts';
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*,host_id as RowID, host_id as checkbox");

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
            $this->db->where('host_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->host_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'host_id' => 'int',
            'nom' => 'char',
            'pays' => 'char',
            'type' => 'char',
            'lien' => 'char',
            'tel_support_technique' => 'char',
            'mail_support_technique' => 'char',
            'tel_service_commercial' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_hosts table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_hosts', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
		$this->db->select("*");
		$this->db->where('host_id = "'.$id.'"');
		$q = $this->db->get('t_hosts');
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
        return $this->_update('t_hosts',$data,$id,'host_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_hosts',$id,'host_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_hosts',$id,'host_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null);
        return $this->_update('t_hosts',$data, $id,'host_id');
    }

    /**
     * list for option dropdown
     * @return [type] [description]
     */
    public function liste_option($with_ajouter = false)
    {
        $value = "CONCAT(nom, ' (', pays , ' -- ', type ,')') as value";
        $query = $this->db->select("host_id as id, $value")
							->where('inactive IS NULL AND deleted is NULL')
							->order_by('nom')
							->get('t_hosts');
        
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
	
    public function type_option()
    {
        $options = array(
            'Registrar+hébergeur',
            'Registrar pur',
            'Hébergeur pur',
            'FAI',
            'Plateforme e-mailing',
            'autre',
        );

        return $this->form_option($options);
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
