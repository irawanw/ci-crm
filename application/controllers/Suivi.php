<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Suivi extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_suivi');
    }

    /******************************
     * Accueil suivi de production
     ******************************/
    public function index() {
        $familles = $this->m_suivi->familles();
        $data = array(
            'title' => "Suivi de production",
            'page' => "suivi/suivi",
            'menu' => "Production|Suivi",
            'values' => array(
                'familles' => $familles
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Suivi de production selon la famille
     ******************************/
    public function suivi($famille) {
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library("Famille_$famille",NULL,'famille');
        $data = $this->famille->suivi();
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}
// EOF
