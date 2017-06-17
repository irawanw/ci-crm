<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_emails_emp extends MY_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_champs($type)
    {
        $champs = array(
            'read' => array(),
            'write' => array(
               'eme_objet' => array("Objet",'text','eme_objet',true),
               'eme_contenu' => array("Texte",'textarea','eme_contenu',false)
            )
        );

        return $champs[$type];
    }

    /******************************
     * Nouvel email pour un employé
     ******************************/
    public function email($data) {

        // récupération des informations de l'employé
        $this->load->model('m_employes');
        $employe = $this->m_employes->detail($data['eme_employe']);
        if ($employe === false) {
            return false;
        }
        if ($employe->emp_email == '') {
            $this->session->set_flashdata('danger',"L'employé n'a pas d'adresse email");
            return false;
        }

        // envoi de l'email
        $this->load->library('email');
        $resultat = $this->email->send_one($employe->emp_email,'',$data['eme_objet'],$data['eme_contenu']);
        if ($resultat === false) {
            $this->session->set_flashdata('danger',"Erreur technique lors de l'envoi de l'email");
            return false;
        }
    }

}
// EOF
