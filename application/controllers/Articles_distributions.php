<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_articles_devis m_articles_devis
*/
class Articles_distributions extends MY_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_articles_distributions');
    }
	
	public function catalogues_distribution_json($id = 0)
    {
        $code       = "D";
        $pagelength = 100;
        $pagestart  = 0 + $this->input->post('start');
        if ($pagestart < 2) {
            $pagelength = 50;
        }

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $quantites = $this->input->post('quantites');

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_articles_distributions->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'art_id' => 't_articles',
            );
            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_articles_distributions->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_articles_distributions->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters);
            }
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function commande_distribution($id = 0, $ajax = false)
    {
        $check = $this->m_articles_distributions->check_is_type_distribution($id);

        if (!$check) {
            $this->my_set_action_response($ajax, false, "Sorry, please select devis with type distribution", 'warning');

            if ($ajax) {
                return;
            }
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'cdb_date_commande', 'label' => "Date Commande", 'rules' => 'trim|required'),
            array('field' => 'cdb_date_limite', 'label' => "Date Limite", 'rules' => 'trim'),
            array('field' => 'cdb_date_activation', 'label' => "Date d'activation", 'rules' => 'trim'),
            array('field' => 'cdb_nom_commande', 'label' => "Nom commande", 'rules' => 'trim'),
            array('field' => 'cdb_type_commande', 'label' => "Type de commande", 'rules' => 'trim|required'),
            array('field' => 'cdb_envelement_demand', 'label' => "ENLEVEMENT DEMAND", 'rules' => 'trim'),
            array('field' => 'cdb_etat', 'label' => "état", 'rules' => 'trim'),
            array('field' => 'cdb_stock', 'label' => "stock", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            // validation réussie
            $valeurs = array(
                'cdb_date_commande'     => formatte_date_to_bd($this->input->post('cdb_date_commande')),
                'cdb_date_limite'       => formatte_date_to_bd($this->input->post('cdb_date_limite')),
                'cdb_date_activation'   => formatte_date_to_bd($this->input->post('cdb_date_activation')),
                'cdb_nom_commande'      => $this->input->post('cdb_nom_commande'),
                'cdb_type_commande'     => $this->input->post('cdb_type_commande'),
                'cdb_envelement_demand' => $this->input->post('cdb_envelement_demand'),
                'cdb_etat'              => $this->input->post('cdb_etat'),
                'cdb_stock'             => $this->input->post('cdb_stock'),
            );

            $resultat = $this->m_articles_distributions->commande_distribution($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {                
                $this->my_set_action_response($ajax, true, "Commande distribution a été enregistré avec succès", 'info');
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);

        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();

            $devis = $this->m_articles_distributions->detail_for_form_commande_distribution($id);

            $valeurs->cdb_date_commande     = $this->input->post('cdb_date_commande') ? $this->input->post('cdb_date_commande') : date("Y-m-d");
            $valeurs->cdb_date_limite       = $this->input->post('cdb_date_limite');
            $valeurs->cdb_date_activation   = $this->input->post('cdb_date_activation');
            $valeurs->cdb_nom_commande      = $this->input->post('cdb_nom_commande');
            $valeurs->cdb_client            = $devis->client;
            $valeurs->cdb_devis             = $devis->devis;
            $valeurs->cdb_facture           = $this->input->post('cdb_facture');
            $valeurs->cdb_montant_ht        = $devis->montant_ht;
            $valeurs->cdb_type_commande     = $this->input->post('cdb_type_commande');
            $valeurs->cdb_envelement_demand = $this->input->post('cdb_envelement_demand');
            $valeurs->cdb_etat              = $this->input->post('cdb_etat');
            $valeurs->cdb_stock             = $this->input->post('cdb_stock');
            $valeurs->cdb_secteurs          = $this->input->post('cdb_secteurs');
            $valeurs->cdb_articles          = $this->input->post('cdb_articles');

            $listes_valeurs->cdb_type_commande     = $this->m_articles_distributions->cdb_type_commande_option();
            $listes_valeurs->cdb_envelement_demand = $this->m_articles_distributions->cdb_envelement_demand_option();
            $listes_valeurs->cdb_etat              = $this->m_articles_distributions->cdb_etat_option();

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'cdb_secteurs'          => array("Secteurs", 'hidden', 'cdb_secteurs', false),
                    'cdb_articles'          => array("Articles", 'hidden', 'cdb_articles', false),
                    'cdb_date_commande'     => array("Date Commande", 'date', 'cdb_date_commande', false),
                    'cdb_date_limite'       => array("Date Limite", 'date', 'cdb_date_limite', false),
                    'cdb_date_activation'   => array("Date d'activation", 'date', 'cdb_date_activation', false),
                    'cdb_nom_commande'      => array("Nom commande", 'text', 'cdb_nom_commande', false),
                    'cdb_client'            => array("Client", 'text', 'cdb_client', false),
                    'cdb_devis'             => array("Devis", 'text', 'cdb_devis', false),
                    'cdb_facture'           => array("Facture", 'text', 'cdb_facture', false),
                    'cdb_montant_ht'        => array("Montant HT", 'text', 'cdb_montant_ht', false),
                    'cdb_type_commande'     => array("Type de commande", 'select', array('cdb_type_commande', 'id', 'value'), false),
                    'cdb_envelement_demand' => array("ENLEVEMENT DEMANDE", 'select', array('cdb_envelement_demand', 'id', 'value'), false),
                    'cdb_etat'              => array("État", 'select', array('cdb_etat', 'id', 'value'), false),
                    'cdb_stock'             => array("Stock", 'text', 'cdb_stock', false),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Transformer en Commande Distribution",
                'page'           => "templates/form",
                'menu'           => "",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'devis',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }

    }

    public function get_commande_distribution_ard_data($devis_id)
    {
        $resultat = $this->m_articles_distributions->get_commande_distribution_ard_data($devis_id);

        echo json_encode($resultat);
    }
}
