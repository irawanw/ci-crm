<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_softwares extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('software_id', 'ref', "id#", 'software_id', 'software_id', 'software_id'),
                array('software_nom', 'text', "Software nom", 'software_nom'),
            ),
            'write' => array(
                'software_nom' => array("Software nom", 'text', 'software_nom', false),
            )
        );

        return $champs[$type];
    }

	public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_softwares';
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*,software_id as RowID, software_id as checkbox");

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
            $this->db->where('software_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->software_id.'">';
        }  

        return $resultat;
    }

     /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'software_id' => 'int',
            'software_nom' => 'char',
        );

        return $filterable_columns;
    }

    public function nouveau($data) {
        return $this->_insert('t_softwares', $data);
    }

    public function detail($id) {
		$this->db->select("*");
		$this->db->where('software_id = "'.$id.'"');
		$q = $this->db->get('t_softwares');
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
        return $this->_update('t_softwares',$data,$id,'software_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_softwares',$id,'software_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_softwares',$id,'software_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null);
        return $this->_update('t_softwares',$data, $id,'software_id');
    }


    public function liste_option() {
        $where = '(inactive IS NULL OR inactive = "0000-00-00 00:00:00") 
                            AND (deleted IS NULL OR deleted = "0000-00-00 00:00:00")';
        $query = $this->db->select('software_id as id, software_nom as value')
                          ->where($where)
                          ->get('t_softwares');

        $result = $query->result();

        return $result;
    }
}

/* End of file M_softwares.php */
/* Location: .//tmp/fz3temp-1/M_software.php */