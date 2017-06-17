<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailchimp extends MY_Controller {

	private $profil;
    private $_SOFTWARE_MAILCHIMP_ID = 8;
    private $barre_action         = array(
        array(
            "Nouveau"       => array('mailchimp/nouveau', 'plus', true, 'mailchimp_nouveau', null, array('form')),
            "Nouveau child" => array('mailchimp/nouveau_child', 'plus', true, 'mailchimp_nouveau_child', null, array('form')),
        ),
        array(
            //"Consulter" => array('*mailchimp/detail','eye-open',false,'mailchimp_detail'),
            "Consulter/Modifier" => array('mailchimp/modification', 'pencil', false, 'mailchimp_modification'),
            "Archiver"           => array('mailchimp/archive', 'folder-close', false, 'mailchimp_archiver', "Veuillez confirmer la archive du mail chimp"),
            "Supprimer"          => array('mailchimp/remove', 'trash', false, 'mailchimp_supprimer', "Veuillez confirmer la suppression du mail chimp"),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
        ),
        array(
            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_mailchimp', 'm_message_list', 'm_segments'));
    }

    /******************************
     * List of Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archiver');
    }

    public function supprimees()
    {
        $this->liste($id = 0, 'supprimees');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            //array("Nouvelle livraison","mailchimp/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'mailchimp/index',
            'detail'             => array('mailchimp/detail', 'mailchimp_id', 'description'),
            'archive'            => array('mailchimp/archive', 'mailchimp_id', 'archive'),
            'champs'             => $this->m_mailchimp->get_champs('read','parent'),
            'filterable_columns' => $this->m_mailchimp->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: mailchimp/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'mailchimp/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'mailchimp/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'mailchimp/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'mailchimp',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);
        $scripts[] = $this->load->view("mailchimp/liste-js", array(), true);
        $scripts[] = $this->load->view('mailchimp/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('mailchimp', $this->session->id);
        $data = array(
            'title'        => "Envois Mail chimp",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Mail chimp",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action, //enable sage bar action
            'values'       => array(
                'id'           => $id,
                'vues'         => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'      => $toolbar,
                'descripteur'  => $descripteur,
            ),
        );
        $layout = "layouts/datatables";
        $this->load->view($layout, $data);
    }

    /******************************
     * Ajax call for Livraison List
     ******************************/
    public function index_json($id = 0)
    {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_mailchimp->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'mailchimp_id' => 't_mailchimp',
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

                $resultat = $this->m_mailchimp->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_mailchimp->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_mailchimp->get_champs('read','parent');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Mail chimp',
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
    }

    public function index_child_json($id = 0)
    {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order     = $this->input->post('order');
        $columns   = $this->input->post('columns');
        $filters   = $this->input->post('filters');
        $parent_id = $this->input->post('parentId');

        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_mailchimp->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'mailchimp_child_id' => 't_mailchimp_child',
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

                $resultat = $this->m_mailchimp->liste_child($id, $parent_id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_mailchimp->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Mail Chimp Child',
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
    }

    public function archived_json($id = 0)
    {
        $this->index_json('archived');
    }

    public function deleted_json($id = 0)
    {
        $this->index_json('deleted');
    }

    public function all_json($id = 0)
    {
        $this->index_json('all');
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(           
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'segment_numero', 'label' => "Segment Numéro", 'rules' => 'trim'),            
            array('field' => 'date_limite_de_fin', 'label' => "Date Limite de Fin", 'rules' => 'trim'),
            array('field' => 'quantite_envoyer', 'label' => "Quantité à envoyer", 'rules' => 'trim'), 
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'software'       => $this->_SOFTWARE_MAILCHIMP_ID,
                'client'         => $this->input->post('client'),
                'commande'       => $this->input->post('commande'),
                'message'        => $this->input->post('message'),
                'segment_numero' => $this->input->post('segment_numero'),
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),    
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );

            $resultat = $this->m_mailchimp->nouveau($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $resultat,
                        'timeStamp'  => round(microtime(true) * 1000),
                    ),
                );
                $this->my_set_action_response($ajax, true, "Mailchimp a été enregistré avec succès", 'info', $ajaxData);
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

            $valeurs->software         = $this->_SOFTWARE_MAILCHIMP_ID;
            $valeurs->client           = $this->input->post('client');
            $valeurs->commande         = $this->input->post('commande');
            $valeurs->message          = $this->input->post('message');
            $valeurs->segment_numero   = $this->input->post('segment_numero');
            $valeurs->segment_criteria = $this->input->post('segment_criteria');            
            $valeurs->date_limite_de_fin   = formatte_date_to_bd($this->input->post('date_limite_de_fin'));
            $valeurs->quantite_envoyer    = $this->input->post('quantite_envoyer');
           
            $listes_valeurs->client         = $this->m_mailchimp->client_option();
            $listes_valeurs->commande       = $this->m_mailchimp->commande(0);
            $listes_valeurs->message        = $this->m_message_list->simple_list();
            $listes_valeurs->segment_numero = $this->m_segments->liste_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_mailchimp->get_champs('write','parent'),
                'onglets' => array(                    
                    array("Info Facturation", array('client', 'commande')),
                    array("Message", array("message")),
                    array("Segment", array('segment_numero','segment_criteria')),
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")), 
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Mail chimp",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Mail chimp",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'mailchimp',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    public function nouveau_child($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'trim|required'),
            array('field' => 'stats', 'label' => "Stats", 'rules' => 'trim'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),
            array('field' => 'segment_part', 'label' => "Segment number", 'rules' => 'trim'),
            array('field' => 'number_sent', 'label' => "Number sent simultaneaously", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_orange', 'label' => "Orange", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_free', 'label' => "Free", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_sfr', 'label' => "SFR", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_gmail', 'label' => "Gmail", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_yahoo', 'label' => "Yahoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_microsoft', 'label' => "Microsoft", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_ovh', 'label' => "OVH", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_oneandone', 'label' => "1and1", 'rules' => 'trim'),
            array('field' => 'physical_server', 'label' => "Physical server", 'rules' => 'trim'),
            array('field' => 'smtp', 'label' => "SMTP", 'rules' => 'trim'),
            array('field' => 'rotation', 'label' => "Rotation", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité envoyee", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'parent_id'                => $this->input->post('parent_id'),                
                'date_envoi'               => formatte_date_to_bd($this->input->post('date_envoi')),
                'operateur_qui_envoie'     => $this->input->post('operateur_qui_envoie'),
                'stats'                    => $this->input->post('stats'),
                'segment_part'             => $this->input->post('segment_part'),
                'deliv_sur_test_orange'    => $this->input->post('deliv_sur_test_orange'),
                'deliv_sur_test_free'      => $this->input->post('deliv_sur_test_free'),
                'deliv_sur_test_sfr'       => $this->input->post('deliv_sur_test_sfr'),
                'deliv_sur_test_gmail'     => $this->input->post('deliv_sur_test_gmail'),
                'deliv_sur_test_yahoo'     => $this->input->post('deliv_sur_test_yahoo'),
                'deliv_sur_test_microsoft' => $this->input->post('deliv_sur_test_microsoft'),
                'deliv_sur_test_ovh'       => $this->input->post('deliv_sur_test_ovh'),
                'deliv_sur_test_oneandone' => $this->input->post('deliv_sur_test_oneandone'),
                'physical_server'          => $this->input->post('physical_server'),
                'smtp'                     => $this->input->post('smtp'),
                'rotation'                 => $this->input->post('rotation'),
                'quantite_envoyee'         => $this->input->post('quantite_envoyee'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
            );

            $resultat = $this->m_mailchimp->nouveau_child($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $resultat,
                        'timeStamp'  => round(microtime(true) * 1000),
                        'parentId'   => $valeurs['parent_id']
                    ),
                );
                $this->my_set_action_response($ajax, true, "Mail chimp child a été enregistré avec succès", 'info', $ajaxData);
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

            $valeurs->parent_id                = $this->input->post('parent_id');
            $valeurs->date_envoi           = formatte_date_to_bd($this->input->post('date_envoi'));
            $valeurs->operateur_qui_envoie     = $this->input->post('operateur_qui_envoie');
            $valeurs->stats                    = $this->input->post('stats');
            $valeurs->segment_part             = $this->input->post('segment_part');
            $valeurs->deliv_sur_test_orange    = $this->input->post('deliv_sur_test_orange');
            $valeurs->deliv_sur_test_free      = $this->input->post('deliv_sur_test_free');
            $valeurs->deliv_sur_test_sfr       = $this->input->post('deliv_sur_test_sfr');
            $valeurs->deliv_sur_test_gmail     = $this->input->post('deliv_sur_test_gmail');
            $valeurs->deliv_sur_test_yahoo     = $this->input->post('deliv_sur_test_yahoo');
            $valeurs->deliv_sur_test_microsoft = $this->input->post('deliv_sur_test_microsoft');
            $valeurs->deliv_sur_test_ovh       = $this->input->post('deliv_sur_test_ovh');
            $valeurs->deliv_sur_test_oneandone = $this->input->post('deliv_sur_test_oneandone');
            $valeurs->physical_server          = $this->input->post('physical_server');
            $valeurs->smtp                     = $this->input->post('smtp');
            $valeurs->rotation                 = $this->input->post('rotation');
            $valeurs->quantite_envoyee         = $this->input->post('quantite_envoyee');
            $valeurs->open                     = $this->input->post('open');
            $valeurs->open_pourcentage         = $this->input->post('open_pourcentage');

            $listes_valeurs->parent_id                = $this->m_mailchimp->parent_option();
            $listes_valeurs->stats                    = $this->m_mailchimp->stats_option();
            $listes_valeurs->operateur_qui_envoie     = $this->m_mailchimp->utilisateurs_option();
            $listes_valeurs->segment_part             = $this->m_mailchimp->segment_part_option();
            $delivrabilite_sur_test_option            = $this->m_mailchimp->delivrabilite_sur_test_option();
            $listes_valeurs->deliv_sur_test_orange    = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_free      = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_sfr       = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_gmail     = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_yahoo     = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_microsoft = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_ovh       = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_oneandone = $delivrabilite_sur_test_option;
            $listes_valeurs->physical_server          = $this->m_mailchimp->physical_server_option();
            $listes_valeurs->smtp                     = $this->m_mailchimp->smtp_option();
            $listes_valeurs->rotation                 = $this->m_mailchimp->rotation_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_mailchimp->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array('parent_id')),
                    array("Segment", array('segment_part')),
                    array("Suivi de l'Envoi", array('date_envoi','stats','quantite_envoyee',"open","open_pourcentage")),
                    array("Delivrabilite Sur Test", array('deliv_sur_test_orange', 'deliv_sur_test_free', 'deliv_sur_test_sfr', 'deliv_sur_test_gmail', 'deliv_sur_test_yahoo', 'deliv_sur_test_microsoft', 'deliv_sur_test_ovh', 'deliv_sur_test_oneandone')),
                    array("Technical", array('operateur_qui_envoie', 'physical_server', 'smtp', 'rotation')),
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Mail chimp",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Mail chimp",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'mailchimp',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Data
     ******************************/
    public function detail($id)
    {

    }

    /******************************
     * Edit function for Data
     ******************************/
    public function modification($id = 0, $ajax)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(            
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'segment_numero', 'label' => "Message", 'rules' => 'trim'),            
            array('field' => 'date_limite_de_fin', 'label' => "Date Limite de Fin", 'rules' => 'trim'),
            array('field' => 'quantite_envoyer', 'label' => "Quantité à envoyer", 'rules' => 'trim'),  
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {
            // validation réussie
            $valeurs = array(
                'client'         => $this->input->post('client'),
                'commande'       => $this->input->post('commande'),
                'message'        => $this->input->post('message'),
                'segment_numero' => $this->input->post('segment_numero'),                
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),  
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );
            $resultat    = $this->m_mailchimp->maj($valeurs, $id);
            $redirection = 'mailchimp/detail/' . $id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Mail chimp a été modifié";
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordchange',
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                        ),
                    );
                }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_mailchimp->detail_for_form($id);

            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('client');
            if (isset($valeur)) {
                $valeurs->client = $valeur;
            }
            $valeurs->segment_criteria = "";
           
            $listes_valeurs->client         = $this->m_mailchimp->client_option();
            $listes_valeurs->commande       = $this->m_mailchimp->commande($id);
            $listes_valeurs->message        = $this->m_message_list->simple_list();
            $listes_valeurs->segment_numero = $this->m_segments->liste_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_mailchimp->get_champs('write','parent'),
                'onglets' => array(                   
                    array("Info Facturation", array('client', 'commande')),
                    array("Message", array("message")),
                    array("Segment", array('segment_numero','segment_criteria')),
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")), 
                ),
            );

            $data = array(
                'title'          => "Modifier Mail chimp",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Mail chimp",
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'mailchimp',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    public function modification_child($id = 0, $ajax)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'trim|required'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),
            array('field' => 'operateur_qui_envoie', 'label' => "Opérateur qui envoie", 'rules' => 'trim'),
            array('field' => 'stats', 'label' => "Stats", 'rules' => 'trim'),
            array('field' => 'segment_part', 'label' => "Segment number", 'rules' => 'trim'),
            array('field' => 'number_sent', 'label' => "Number sent simultaneaously", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_orange', 'label' => "Orange", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_free', 'label' => "Free", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_sfr', 'label' => "SFR", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_gmail', 'label' => "Gmail", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_yahoo', 'label' => "Yahoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_microsoft', 'label' => "Microsoft", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_ovh', 'label' => "OVH", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_oneandone', 'label' => "1and1", 'rules' => 'trim'),
            array('field' => 'physical_server', 'label' => "Physical server", 'rules' => 'trim'),
            array('field' => 'smtp', 'label' => "SMTP", 'rules' => 'trim'),
            array('field' => 'rotation', 'label' => "Rotation", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité envoyee", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {
            // validation réussie
            $valeurs = array(
                'parent_id'                => $this->input->post('parent_id'),
                'date_envoi'               => formatte_date_to_bd($this->input->post('date_envoi')),
                'operateur_qui_envoie'     => $this->input->post('operateur_qui_envoie'),
                'stats'                    => $this->input->post('stats'),
                'segment_part'             => $this->input->post('segment_part'),
                'deliv_sur_test_orange'    => $this->input->post('deliv_sur_test_orange'),
                'deliv_sur_test_free'      => $this->input->post('deliv_sur_test_free'),
                'deliv_sur_test_sfr'       => $this->input->post('deliv_sur_test_sfr'),
                'deliv_sur_test_gmail'     => $this->input->post('deliv_sur_test_gmail'),
                'deliv_sur_test_yahoo'     => $this->input->post('deliv_sur_test_yahoo'),
                'deliv_sur_test_microsoft' => $this->input->post('deliv_sur_test_microsoft'),
                'deliv_sur_test_ovh'       => $this->input->post('deliv_sur_test_ovh'),
                'deliv_sur_test_oneandone' => $this->input->post('deliv_sur_test_oneandone'),
                'physical_server'          => $this->input->post('physical_server'),
                'smtp'                     => $this->input->post('smtp'),
                'rotation'                 => $this->input->post('rotation'),
                'quantite_envoyee'         => $this->input->post('quantite_envoyee'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
            );
            $resultat    = $this->m_mailchimp->maj_child($valeurs, $id);
            $redirection = 'mailchimp/detail/' . $id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Mail chimp child a été modifié";
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordchange',
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                            'isChild'    => true,
                        ),
                    );
                }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_mailchimp->detail_for_form_child($id);

            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('client');
            if (isset($valeur)) {
                $valeurs->client = $valeur;
            }

            $listes_valeurs->parent_id                = $this->m_mailchimp->parent_option();
            $listes_valeurs->stats                    = $this->m_mailchimp->stats_option();
            $listes_valeurs->operateur_qui_envoie     = $this->m_mailchimp->utilisateurs_option();
            $listes_valeurs->segment_part             = $this->m_mailchimp->segment_part_option();
            $delivrabilite_sur_test_option            = $this->m_mailchimp->delivrabilite_sur_test_option();
            $listes_valeurs->deliv_sur_test_orange    = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_free      = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_sfr       = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_gmail     = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_yahoo     = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_microsoft = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_ovh       = $delivrabilite_sur_test_option;
            $listes_valeurs->deliv_sur_test_oneandone = $delivrabilite_sur_test_option;
            $listes_valeurs->physical_server          = $this->m_mailchimp->physical_server_option();
            $listes_valeurs->smtp                     = $this->m_mailchimp->smtp_option();
            $listes_valeurs->rotation                 = $this->m_mailchimp->rotation_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_mailchimp->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array('parent_id')),
                    array("Segment", array('segment_part')),
                    array("Suivi de l'Envoi", array('date_envoi','stats','quantite_envoyee',"open","open_pourcentage")),
                    array("Delivrabilite Sur Test", array('deliv_sur_test_orange', 'deliv_sur_test_free', 'deliv_sur_test_sfr', 'deliv_sur_test_gmail', 'deliv_sur_test_yahoo', 'deliv_sur_test_microsoft', 'deliv_sur_test_ovh', 'deliv_sur_test_oneandone')),
                    array("Technical", array('operateur_qui_envoie', 'physical_server', 'smtp', 'rotation')),
                ),
            );

            $data = array(
                'title'          => "Modifier Mail chimp",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Mail chimp",
                'id'             => $id,
                'values'         => $valeurs,
                'form-id'        => "form-mailchimp-modification_child-1",
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'mailchimp',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id = 0, $ajax = false)
    {

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_mailchimp->archive($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Mail chimp a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function archive_child($id = 0, $ajax = false)
    {

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_mailchimp->archive_child($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                    'isChild'    => true,
                ),
            );
            $this->my_set_action_response($ajax, true, "Mail chimp child a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    /******************************
     * Delete Data
     ******************************/
    public function remove($id, $ajax = false)
    {

        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_mailchimp->remove($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Mail chimp a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function remove_child($id, $ajax = false)
    {

        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_mailchimp->remove_child($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                    'isChild'    => true,
                ),
            );
            $this->my_set_action_response($ajax, true, "Mail chimp child a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_mailchimp->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_mailchimp->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_mailchimp->unremove($id);
        }
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_mailchimp->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Pas de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    public function facture_option($id = 0)
    {
        $resultat = $this->m_mailchimp->facture_option($id);
        echo json_encode($resultat);
    }

    public function message_option($id = 0)
    {
        $data = $this->m_message_list->detail($id);
        echo json_encode($data);
    }

    /*public function mailchimp_option($id = 0)
    {
        $data = $this->m_mailchimp->get_mailing_detail($id);
        echo json_encode($data);
    }*/

}

/* End of file Mailchimp.php */
/* Location: .//tmp/fz3temp-1/Mailchimp.php */