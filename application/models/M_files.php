<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * 
*/
class M_files extends CI_Model {
	protected $_table = "t_files";
	protected $_key = "file_id";

	public function __construct() {
        parent::__construct();
    }

    public function get($id) {
    	return $this->db->get_where($this->_table, array('file_id' => $id))->row();
    }

    public function nouveau($data) {
    	$this->db->insert($this->_table, $data);

    	$insert_id = $this->db->insert_id();

    	return $insert_id;
    }

    public function update_row($row_id, $file_ids) {

    	$this->db->where_in($this->_key, $file_ids);
    	$query = $this->db->update($this->_table, array('row_id' => $row_id));

    	return $this->db->affected_rows();
    }

    public function remove($id) {
    	$this->db->delete($this->_table, array($this->_key => $id));
    	return TRUE;
    }
}