<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_lignes_factures $m_lignes_factures
*/
class Lignes_factures extends CI_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_lignes_factures');
    }

    /******************************
    * Liste des lignes d'une facture
     * Support AJAX
    ******************************/
    public function lignes_facture($id=0, $ajax=false) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'lignes_factures/lignes_facture',
            'champs' => $this->m_lignes_factures->get_champs('read')
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("lignes_factures/liste-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'lignes_factures',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('lignes_factures',$this->session->id);
        $data = array(
            'title' => "Liste des lignes d'une facture",
            'page' => "lignes_factures/liste",
            'menu' => "Ventes|Lignes de factures",
            'scripts' => $scripts,
            'controleur' => 'lignes_factures',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );

         if ($ajax) {
            $html = $this->load->view("lignes_factures/liste", $data, true) ;
            foreach ($scripts as $s) {
                $html .= $s;
            }
            $this->output->set_content_type('application/json')->set_output(json_encode(array("success"=>true, "data"=>$html)));
         } else {
            $layout="layouts/standard";
            $this->load->view($layout,$data);
         }
    }

    /******************************
    * Liste des lignes d'une facture (datasource)
    ******************************/
    public function lignes_facture_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_lignes_factures->liste($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));

    }

    /******************************
     * Constitution d'une facture
     * Support AJAX
     ******************************/
    public function constitution($id, $ajax=false) {
        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_familles');
        $familles = $q->result();
        $tva = tva();
        $scripts = array();
        $scripts[] = $this->load->view('lignes_factures/constitution-js',array('id'=>$id,'tva'=>$tva,'familles' => $familles),true);
        foreach ($familles as $f) {
            $scripts[] = $this->load->view('_catalogues/'.$f->vfm_nom.'-js',array(),true);
        }
        $data = array(
            'title' => "Facture",
            'page' => "lignes_factures/constitution",
            'menu' => "Ventes|Nouvelle facture",
            'scripts' => $scripts,
            'values' => array(
                'id' => $id,
                'familles' => $familles
            )
        );
        if ($ajax) {
            $html = $this->load->view("lignes_factures/constitution", $data, true) ;
            foreach ($scripts as $s) {
                $html .= $s;
            }
            $this->output->set_content_type('application/json')->set_output(json_encode(array("success"=>true, "data"=>$html)));
        } else {
            $layout="layouts/standard";
            $this->load->view($layout,$data);
        }
    }

    /******************************
     * Manipulation des lignes de la facture
     * AJAX
     ******************************/
    public function manipulation($id,$commande) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_lignes_factures->constitution($id,$commande);
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
        $resultat = $this->m_lignes_factures->lecture_catalogue($code);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

}
// EOF
