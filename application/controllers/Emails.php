<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_emails m_emails
*/
class Emails extends MY_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_emails');
    }

    /******************************
    * Non disponible
    * support AJAX
    ******************************/
    public function envoi_email_type($id=0,$ajax=false) {
        $data = array(
            'title' => "Non disponible",
            'page' => "non_implemente",
            'menu' => "Contacts|Emails types",
            'values' => array(
                'message' => "Cet écran permet d'envoyer des emails types à une liste."
            )
        );
        $this->my_set_display_response($ajax,$data);
    }

}

// EOF