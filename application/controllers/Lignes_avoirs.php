<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property M_lignes_avoirs $m_lignes_avoirs
 */
class Lignes_avoirs extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_lignes_avoirs');
    }

    /******************************
     * Manipulation des lignes de l'avoir
     * AJAX
     ******************************/
    public function manipulation($id,$commande) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_lignes_avoirs->constitution($id,$commande);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Lecture des catalogues
     ******************************/
    public function lecture_catalogue($code) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_lignes_avoirs->lecture_catalogue($code);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

}

// EOF