<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 */
class Profiler extends CI_Controller {

    public function index($id=0) {
        $this->load->library('session');
        $this->output->set_output($this->session->tempdata('ci_profiler_'.$id));
    }

}

// EOF