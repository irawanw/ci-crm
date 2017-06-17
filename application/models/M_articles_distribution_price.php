<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_articles_distribution_price extends MY_Model {
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
                array('adp_id', 'ref', "id#", 'adp_id', 'adp_id', 'adp_id'),
                array('adp_option', 'text', "Option", 'adp_option'),
                array('adp_value', 'text', "Value", 'adp_value'),
                array('adp_percentage', 'text', "Percentage", 'adp_percentage'),
            ),            
            'write' => array(
               'adp_option' => array("Option",'text','adp_option',false),
               'adp_value' => array("Value",'text','adp_value',false),
               'adp_percentage' => array("Percentage",'text','adp_percentage',false),
            )
        );

        return $champs[$type];
    }

	public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc") {

        // première partie du select, mis en cache
        $table = 't_articles_distribution_price';
        $adp_id = $table . ".adp_id as RowID";
        $checkbox   = $table . ".adp_id as checkbox";
        $this->db->start_cache();

        $this->db->select($table . ".*,$adp_id,$checkbox,adp_option,adp_value,adp_percentage",false);

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
            $this->db->where($table . '.adp_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->adp_id.'">';
        }  
        return $resultat;
    }

    public function liste_filterable_columns() {
    $filterable_columns = array(
            'adp_option'=>'char',
            'adp_value'=>'char',
            'adp_percentage'=>'char'
        );
        return $filterable_columns;
    }

    public function nouveau($data) {
        $id = $this->_insert('t_articles_distribution_price', $data);
        return $id;
    }

    public function detail($id) {
        $this->db->select("adp_id,adp_option,adp_value,adp_percentage",false);
        $this->db->where('adp_id',$id);
        $q = $this->db->get('t_articles_distribution_price');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    public function maj($data,$id) {
        return $this->_update('t_articles_distribution_price', $data, $id, 'adp_id');
    }

    public function archive($id)
    {
        return $this->_delete('t_articles_distribution_price', $id, 'adp_id', 'inactive');
    }

    public function remove($id)
    {
        return $this->_delete('t_articles_distribution_price', $id, 'adp_id', 'deleted');
    }

    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_articles_distribution_price', $data, $id, 'adp_id');
    }

}

/* End of file M_articles_distribution_price.php */
/* Location: .//tmp/fz3temp-1/M_articles_distribution_price.php */