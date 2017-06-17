<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_document_table extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="desc")
    {
        $table = 't_document_table';
        $template = "tpl_nom";
        $template_name = $template." AS template_name";
        $client = "ctc_nom";
        $client_name = $client." AS client_name";

        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select("id as RowID,
            id as checkbox,
            filename,
            $template_name,
            $client_name,
            content,
            date_generate
            ");

        $this->db->join('t_document_templates','tpl_id=template','left');
        $this->db->join('t_contacts','ctc_id=client_id','left');

        switch($void){
            case 'archived':
                $this->db->where('inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where('deleted IS NOT NULL');
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
         $this->db->where('id', $id);
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(
            'template_name' => $template,
            'client_name' => $client
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
            $resultat['data'][$i]->filename = '<a href="#" class="download-file" data-id="'.$resultat['data'][$i]->RowID.'" >'.$resultat['data'][$i]->filename.'</a>';
        }  

        return $resultat;
    }

     /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'filename' => 'char',
            'template_name' => 'char',
            'client_name' => 'char',
            'date_generate' => 'date'
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_document_table table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_document_table', $data);
    }

    /******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_document_table',$id,'id','inactive');
    }

    /******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_document_table',$id,'id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_document_table',$data, $id,'id');
    }

    public function download($id)
    {
        $this->config->load('export');
        $path_save = $this->config->item('path_save_contact_document');
        $path_download = $this->config->item('path_download_document');

        $filename = $this->db->select('filename')->get_where('t_document_table', array('id' => $id))->row()->filename;

        if(!$filename || $filename == "") {
            throw new MY_Exceptions_NoSuchRecord('Pas de contact document file '.$id);
        }

        if(!file_exists($path_save.$filename)) {
            throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
        } else {
            $fileUrl = base_url().$path_download.$filename;
            $message = "Document success downloaded";

            $result = array(
                'fileUrl' => $fileUrl,
                'message' => $message
            );

            return $result;
        }

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
