<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_articles_distribution_base_price extends MY_Model {
	public function __construct()
    {
        parent::__construct();
    }
	
	public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
                array('adb_id', 'ref', "id#", 'adb_id', 'adb_id', 'adb_id'),
                array('adb_secteur', 'text', "Secteur", 'adb_secteur'),
                array('adb_baseprice', 'text', "Baseprice", 'adb_baseprice')
            ),            
            'write' => array(
               'adb_secteur' => array("Secteur", 'select', array('adb_secteur', 'id', 'value'), false),
               'adb_baseprice' => array("Baseprice",'text','adb_baseprice',false)
            )
        );

        return $champs[$type];
    }

	public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc") {

        // première partie du select, mis en cache
        $table = 't_articles_distribution_base_price';
        $adb_id = $table . ".adb_id as RowID";
        $checkbox   = $table . ".adb_id as checkbox";
        $this->db->start_cache();

        $this->db->select($table . ".*,$adb_id,$checkbox,adb_baseprice,vts.vts_type as adb_secteur",false);
        $this->db->join('v_types_secteurs as vts', 'vts.vts_id = t_articles_distribution_base_price.adb_secteur', 'left');
        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where($table . '.deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table . '.inactive is NULL');
                $this->db->where($table . '.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where($table . '.adb_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->adb_id.'">';
        }  
        return $resultat;
    }

    public function liste_filterable_columns() {
    $filterable_columns = array(
            'adb_secteur'=>'char',
            'adb_baseprice'=>'char'
        );
        return $filterable_columns;
    }

    public function nouveau($data) {
        $id = $this->_insert('t_articles_distribution_base_price', $data);
        return $id;
    }

    public function detail($id) {
        $this->db->select("adb_id,adb_baseprice,adb_secteur",false);
        $this->db->where('adb_id',$id);
        $q = $this->db->get('t_articles_distribution_base_price');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    public function maj($data,$id) {
        return $this->_update('t_articles_distribution_base_price', $data, $id, 'adb_id');
    }

    public function archive($id)
    {
        return $this->_delete('t_articles_distribution_base_price', $id, 'adb_id', 'inactive');
    }

    public function remove($id)
    {
        return $this->_delete('t_articles_distribution_base_price', $id, 'adb_id', 'deleted');
    }

    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_articles_distribution_base_price', $data, $id, 'adb_id');
    }
	
	public function secteurs_option()
    {
        $this->db->select('vts_id as id, vts_type as value');
        $q = $this->db->get('v_types_secteurs');
        return $q->result();
    }

}

/* End of file M_articles_distribution_base_price.php */
/* Location: .//tmp/fz3temp-1/M_articles_distribution_base_price.php */