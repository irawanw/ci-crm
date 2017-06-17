<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Promotions extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_promotions');
    }

    /******************************
    * Non disponible
    ******************************/
    public function index() {
        $data = array(
            'title' => "Non disponible",
            'page' => "non_implemente",
            'menu' => "Personnel|Mise à jour de profil",
            'values' => array(
                'message' => "Cet écran permet de définir la promotion qui s'appliquera le mois suivant. Le fonctionnement est à préciser."
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}
// EOF
