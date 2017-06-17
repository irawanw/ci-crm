<?php defined('BASEPATH') or exit('No direct script access allowed');

class Champs extends CI_Controller
{
    /**
     * [add_champs description]
     */
    public function nouveau()
    {
        if(!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $this->load->model(array('m_champs'));
        $data = $this->input->post(NULL, TRUE);

        $result = $this->m_champs->insert($data);

        if($result) {
            echo json_encode(array('status' => TRUE, 'id' => $result));
        } else {
            echo json_encode(array('status' => FALSE, 'error' => 'Cet élément existe avant'));
        }

        
    }
}