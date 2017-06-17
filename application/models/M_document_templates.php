<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_document_templates extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('tpl_id', 'ref', "id#", 'tpl_id'),
                array('tpl_nom', 'text', "Nom", 'tpl_nom'),              
                array('tpl_content', 'text', "Content", 'tpl_content'),
                array('tpl_created_date', 'text', "Created Date", 'tpl_created_date'),
            ),
            'write' => array(
                'tpl_nom'       => array("Nom", 'text', 'tpl_nom', true),
                'tpl_content'   => array("Content", 'textarea', 'tpl_content', false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_document_templates';
        $this->db->start_cache();
		$this->db->select($table.".*,tpl_id as RowID, tpl_id as checkbox");

        switch($void){
            case 'archived':
                $this->db->where('tpl_inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('tpl_deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where('tpl_inactive is NULL');
                $this->db->where('tpl_deleted is NULL');
                break;
        }
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('tpl_id', $id);
        }
		$this->db->stop_cache();
        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->tpl_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'tpl_id' => 'int',
            'tpl_nom' => 'char',
            'tpl_content' => 'char',
			'tpl_created_date' => 'datetime',
            
        );

        return $filterable_columns;
    }

    /******************************
    * New
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_document_templates', $data);
    }

    /******************************
    * Detail
    ******************************/
    public function detail($id) 
	{
		$this->db->where('tpl_id = "'.$id.'"');
		$q = $this->db->get('t_document_templates');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating data
    ******************************/
    public function maj($data,$id) {
        return $this->_update('t_document_templates',$data,$id,'tpl_id');
    }

	/******************************
    * Archive data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_document_templates',$id,'tpl_id','tpl_inactive');
    }

	/******************************
    * Remove data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_document_templates',$id,'tpl_id','tpl_deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_document_templates',$data, $id,'tpl_id');
    }

    /******************************
     * Dupliquer telephones data
     ******************************/
    public function dupliquer($id)
    {
        $data = $this->db->get_where('t_document_templates', array('tpl_id' => $id))->row_array();

        if($data) {
            unset($data['tpl_id']);  

            return $this->_insert('t_document_templates', $data);
        }
    }
}
// EOF
