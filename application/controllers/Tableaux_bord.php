<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Tableaux_bord extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_tableaux_bord');
    }

    /******************************
    * Non disponible
    ******************************/
    public function index() {
        $data = array(
            'title' => "Non disponible",
            'page' => "non_implemente",
            'menu' => "Ventes|Mise à jour d'enseigne",
            'values' => array(
                'message' => "Cet écran permet d'afficher les tableaux de bord."
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}
// EOF
