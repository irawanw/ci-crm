<?php
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 04/03/2017
 * Time: 19:04
 *
 * @property CI_Session $session
 * @property CI_Loader $load
 */
class Migration extends CI_Controller {

    public function index() {				
		if ($this->session->userdata('utl_login') == '') {
            die("Sorry, you are not allowed to run migration script. Please log in first.");
        }

        /**
         * @var CI_Migration $migration
         */
        $this->load->library('migration');
        $migration = $this->migration;

        if ($migration->current() === FALSE) {
            show_error($migration->error_string());
        }
        else {
            echo "Migration done";
        }
    }
}