<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_rbl_list extends MY_Model {
	
	public function __construct() {
		parent::__construct();
	}

	public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('rbl_id', 'text', "Id#"),
                array('rbl_nom', 'text', "Nom"),
                array('rbl_url', 'text', "Url"),
                array('rbl_abuse_mail', 'text', "Abuse_mail"),
                array('rbl_delistable', 'text', "Delistable"),
                array('rbl_provider_names', 'text', "Providers using this rbl"),
            ),
            'write' => array(
                'rbl_nom'   => array("Nom", 'text', 'rbl_nom', true),
                'rbl_url'   => array("Url", 'text', 'rbl_url', true),
                'rbl_abuse_mail'   => array("Abuse mail", 'text', 'rbl_abuse_mail', true),
                'rbl_delistable' => array("Delistable", 'select', array('rbl_delistable', 'id', 'value'), false),
                'rbl_providers' => array("Providers using this rbl", 'select-multiple', array('rbl_providers', 'id', 'value'), false),
            )
        );

        return $champs[$type];
    }

	/******************************
	 * Liste
	 ******************************/
	public function liste($void , $limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_rbl_liste';
		$rbl_providers = "(select GROUP_CONCAT(provider) from t_providers where find_in_set(providers_id,rbl_providers))";
		$rbl_provider_names = $rbl_providers." as rbl_provider_names";
		$this->db->start_cache();
		$this->db->select("rbl_id as RowID, rbl_id, rbl_id as checkbox, rbl_nom,rbl_url,rbl_abuse_mail,rbl_delistable,$rbl_provider_names");
		
		switch($void){
            case 'archived':
                $this->db->where('rbl_inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('rbl_deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where('rbl_inactive is NULL');
                $this->db->where('rbl_deleted is NULL');
                break;
        }
		
		$id = intval($void);
		if ($id) {
			$this->db->where('rbl_id',$id);
		}
		$this->db->stop_cache();

		$aliases = array(
			'rbl_provider_names' => $rbl_providers
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
				'rbl_id' 	=> 'int',
				'rbl_nom'	=> 'char',
				'rbl_url'	=> 'char',
				'rbl_abuse_mail'	=> 'char',
				'rbl_delistable'	=> 'char',
				'rbl_provider_names' => 'char'
		);
		return $filterable_columns;
	}
	
	
	/******************************
	 * Nouveau
	 ******************************/
	public function nouveau($data) {
		return $this->_insert('t_rbl_liste', $data);
	}
	
	/******************************
	 * Détail
	 ******************************/
	public function detail($id) 
	{
		$table = 't_rbl_liste';
		$q = $this->db->get_where($table,array('rbl_id' => $id));
		if ($q->num_rows() > 0) {
			$resultat = $q->row();
			return $resultat;
		}
		else {
			return null;
		}
	}

	public function maj($data,$id) 
	{
		return $this->_update('t_rbl_liste',$data,$id,'rbl_id');
	}
	
	public function remove($id) 
	{
		return $this->_delete('t_rbl_liste',$id,'rbl_id','rbl_deleted');
	}
	
	public function liste_option()
	{
		$sql = $this->db->get('t_rbl_liste');
		$result = $sql->result();
		return $result;
	}

	public function delistable_liste_option()
    {
        $options = array(
            'Oui',
            'Non',
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