<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: jausions
 * Date: 25-Feb-17
 * Time: 16:38
 */
class Fichiers extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('download');
        $this->load->helper('path');
    }

    public function telecharger() {
        $file = $this->input->get('ref');
        $dir = FCPATH.'fichiers';

        $path = set_realpath(FCPATH.$file);

        if (!$file || !file_exists($path) || strncmp($path, $dir, strlen($dir)) != 0) {
            show_404();
        } else {
            force_download($path, null, false);
        }
    }

    public function pdf() {
        $file = $this->input->get('ref');
        $dir = FCPATH.'fichiers';

        $path = set_realpath(FCPATH.$file);

        if (!$file || !file_exists($path) || strncmp($path, $dir, strlen($dir)) != 0) {
            show_404();
        } else {
            $this->output->set_content_type('application/pdf')->_display();
            readfile($file);
        }
    }
}

// EOF