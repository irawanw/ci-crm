<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Imputations extends MY_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_imputations');
    }

    /******************************
    * Liste des imputations
    ******************************/
    public function imputations_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'imputations/imputations_client',
            'detail' => array('imputations/detail','ipu_id','ipu_montant'),
            'champs' => $this->m_imputations->get_champs('read'),
            'filterable_columns' => $this->m_imputations->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'imputations',
                'methode' => 'imputations_client'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('imputations',$this->session->id);
        $data = array(
            'title' => "Liste des imputations",
            'page' => "templates/datatables",
            'menu' => "Ventes|Imputations de règlement",
            'scripts' => $scripts,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des imputations (datasource)
    ******************************/
    public function imputations_client_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_imputations->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ipu_montant' => 't_imputations',
                'rgl_reference' => 't_reglements',
                'ctc_nom' => 't_contacts',
                'fac_reference' => 't_factures',
                'avr_reference' => 't_avoirs',
                'pep_id' => 't_profits_et_pertes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_imputations->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_imputations->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Détail d'une imputation
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_imputations->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'ipu_reglement' => array("Règlement",'REF','ref',array('reglements','ipu_reglement','rgl_reference')),
                   'rgl_client' => array("Client",'REF','ref',array('contacts','rgl_client','ctc_nom')),
                   'ipu_montant' => array("Montant",'DECIMAL 8,2','number','ipu_montant'),
                   'ipu_facture' => array("Facture",'REF','ref',array('factures','ipu_facture','fac_reference')),
                   'ipu_avoir' => array("Avoir",'REF','ref',array('avoirs','ipu_avoir','avr_reference')),
                   'ipu_profits' => array("Profits et pertes",'REF','ref',array('profits_et_pertes','ipu_profits','pep_id'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une imputation",
                'page' => "templates/detail",
                'menu' => "Ventes|Imputations de règlement",
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'imputations',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

}
// EOF
