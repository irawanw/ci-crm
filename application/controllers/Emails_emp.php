<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_emails_emp m_emails_emp
*/
class Emails_emp extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Contact" => array(
            array(
                "Fiche Employé(e)" => array('employes/detail','user',true,'employes_detail',null,array('view')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_emails_emp');
    }

    /******************************
    * Email
    * support AJAX
    ******************************/
    public function email($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'eme_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'eme_contenu','label'=>"Texte",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'eme_objet' => $this->input->post('eme_objet'),
                'eme_contenu' => $this->input->post('eme_contenu'),
                'eme_employe' => $pere
            );
            $id = $this->m_emails_emp->email($valeurs);
            if ($id === false) {
                $this->my_set_display_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"Le message a été envoyé");
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeurs->eme_objet = $this->input->post('eme_objet');
            $valeurs->eme_contenu = $this->input->post('eme_contenu');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_emails_emp->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = modifie_action_barre_action($this->barre_action['Contact'], 'employes/detail', 'employes/detail/'.$pere);

            $data = array(
                'title' => "Email",
                'page' => "templates/form",
                'menu' => "Personnel|Nouvel email",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Envoyer',
                'controleur' => 'emails_emp',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

}

// EOF