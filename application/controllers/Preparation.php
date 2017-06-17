<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Preparation extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_preparation');
    }

    /******************************
     * Accueil préparation
     ******************************/
    public function index() {
        $familles = $this->m_preparation->familles();
        $data = array(
            'title' => "Préparation",
            'page' => "preparation/preparation",
            'menu' => "Production|Préparation",
            'values' => array(
                'familles' => $familles
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Préparation selon la famille
     ******************************/
    public function preparation($famille) {
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library("Famille_$famille",NULL,'famille');
        $data = $this->famille->preparation();
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}
// EOF
