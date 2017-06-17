<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_courriers m_courriers
*/
class Courriers extends MY_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_courriers');
    }

    /******************************
    * Non disponible
    * support AJAX
    ******************************/
    public function envoi_courrier_type($id=0,$ajax=false) {
        $data = array(
            'title' => "Non disponible",
            'page' => "non_implemente",
            'menu' => "Espace client|Mon compte",
            'values' => array(
                'message' => "Cet écran permet d'envoyer des courriers types à une liste."
            )
        );
        $this->my_set_display_response($ajax,$data);
    }

}

// EOF