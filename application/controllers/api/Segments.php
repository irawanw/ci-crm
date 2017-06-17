<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Segments extends REST_Controller
{

    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('m_segments');
    }

    public function index_get()
    {
       
        $res = $this->db->get('t_segments')->result();
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        $data = array(
            'id'        => $this->post('id'),
            'userid'    => $this->post('userid'),
            'name'      => $this->post('name'),
            'filtering' => $this->post('filtering'),
        );

        $id = $this->m_segments->insert($data);
        if ($id) {
            $this->response($data, REST_Controller::HTTP_CREATED);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }


    public function index_put($id)
    {        
        $data = array();

        if($this->put('name')) {
            $data['name'] = $this->put('name');
        }

        if($this->put('userid')) {
            $data['userid'] = $this->put('userid');   
        }

        if($this->put('filtering')) {
            $data['filtering'] = $this->put('filtering');      
        }

        $result = $this->m_segments->update($id, $data);
        if ($result) {
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }


    public function index_delete($id)
    {        
        $result = $this->m_segments->delete($id);
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_CREATED);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

}
