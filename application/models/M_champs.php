<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_champs extends CI_Model {


	private $_table = "t_champs";

	public function insert($data)
	{
		$check_data = $this->_check_exist($data);

		if($check_data) {
			$this->db->insert($this->_table, $check_data);
			$id = $this->db->insert_id();

			return $id;
		} else {
			return FALSE;
		}
	}

	/**
	 * [_check_exist description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function _check_exist($data)
	{
		$name = $data['champ_name'];
		$key = strtolower($data['champ_value']);
		$key = str_replace(" ","_", $key);

		$query = $this->db->where('champ_name', $name)
						  ->where('champ_key', $key)
						  ->get($this->_table);
		if($query->num_rows() > 0)
		{
			return FALSE;
		} else {
			$data['champ_key'] = $key;
			return $data;
		}
	}

	public function liste_option($name)
	{
		$query = $this->db->select('champ_value as id, champ_value as value')
						  ->where('champ_name', $name)
						  ->get($this->_table);

		return $query->result();
	}
}