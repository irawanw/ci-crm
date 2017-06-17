<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Message_list extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('message_list/nouveau', 'plus', true, 'message_list_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('message_list/modification', 'pencil', false, 'message_list_modification', null, array('form')),
            "Supprimer"          => array('message_list/remove', 'trash', false, 'message_list_supprimer', "Veuillez confirmer la suppression de cette Message List", array('confirm-modify' => array('message_list/index'))),
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
        $this->load->model(array('m_message_list', 'm_catalogues', 'm_societes_vendeuses', 'm_utilisateurs', 'm_contacts'));
    }

    /******************************
     * List of message_list Data
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
            // array("Ajouter un e-mailing pages jaunes","message_list/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'message_list/index',
            'detail'             => array('message_list/detail', 'message_list_id', 'description'),
            'archive'            => array('message_list/archive', 'message_list_id', 'archive'),
            'champs'             => $this->m_message_list->get_champs('read'),
            'filterable_columns' => $this->m_message_list->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: message_list/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'message_list/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'message_list/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'message_list/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'message_list',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);
        $scripts[] = $this->load->view("message_list/liste-js", array(), true);
        $scripts[] = $this->load->view('message_list/form-js', array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('message_list', $this->session->id);
        $data = array(
            'title'        => "Liste des messages",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Message_list",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_message_list->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'message_list_id' => 't_message_list',
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

                $resultat = $this->m_message_list->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_message_list->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
            //action export data xls
            $champs = $this->m_message_list->get_champs('read');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Message_list',
            );
            $this->_export_xls($params);
        } else {

        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
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
            array('field' => 'name', 'label' => "Name", 'rules' => 'trim|required'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'object', 'label' => "Object", 'rules' => 'trim'),
            array('field' => 'lien_pour_telecharger', 'label' => "Lien Pour Telecharger", 'rules' => 'trim|valid_url'),
            array('field' => 'famille_darticles', 'label' => "Famille D'Articles", 'rules' => 'trim'),
            array('field' => 'societe', 'label' => "Societe", 'rules' => 'trim'),
            array('field' => 'salesman', 'label' => "Salesman", 'rules' => 'trim'),
            array('field' => 'telephone', 'label' => "Telephone", 'rules' => 'trim'),
            array('field' => 'email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'segment_name', 'label' => "Segment Name", 'rules' => 'trim'),
            array('field' => 'segment_number', 'label' => "Segment Number", 'rules' => 'trim'),
            array('field' => 'department', 'label' => "Department", 'rules' => 'trim'),
            array('field' => 'region', 'label' => "Region", 'rules' => 'trim'),
            array('field' => 'country', 'label' => "Country", 'rules' => 'trim'),
            array('field' => 'activities', 'label' => "Activities", 'rules' => 'trim'),
            array('field' => 'origin', 'label' => "Origin", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'software', 'label' => "Software", 'rules' => 'trim'),
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim'),
            array('field' => 'produit_vendu', 'label' => "Produit Vendu", 'rules' => 'trim'),
            array('field' => 'database', 'label' => "Database", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            // validation réussie
            $valeurs = array(
                'name'                  => $this->input->post('name'),
                'department'            => $this->input->post('department'),
                'region'                => $this->input->post('region'),
                'country'               => $this->input->post('country'),
                'activities'            => $this->input->post('activities'),
                'famille_darticles'     => $this->input->post('famille_darticles'),
                'societe'               => $this->input->post('societe'),
                'salesman'              => $this->input->post('salesman'),
                'telephone'             => $this->input->post('telephone'),
                'email'                 => $this->input->post('email'),
                'segment_name'          => $this->input->post('segment_name'),
                'segment_number'        => $this->input->post('segment_number'),
                'message'               => $this->input->post('message'),
                'object'                => $this->input->post('object'),
                'lien_pour_telecharger' => $this->input->post('lien_pour_telecharger'),
                'origin'                => $this->input->post('origin'),
                'type'                  => $this->input->post('type'),
                'software'              => $this->input->post('software'),
                'client'                => $this->input->post('client'),
                'produit_vendu'         => $this->input->post('produit_vendu'),
                'database'              => $this->input->post('database'),
            );

            $resultat = $this->m_message_list->nouveau($valeurs);

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
                $this->my_set_action_response($ajax, true, "Message list a été enregistré avec succès", 'info', $ajaxData);
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
            $valeurs                        = new stdClass();
            $listes_valeurs                 = new stdClass();
            $valeurs->name                  = $this->input->post('name');
            $valeurs->department            = $this->input->post('department');
            $valeurs->region                = $this->input->post('region');
            $valeurs->country               = $this->input->post('country');
            $valeurs->activities            = $this->input->post('activities');
            $valeurs->famille_darticles     = $this->input->post('famille_darticles');
            $valeurs->societe               = $this->input->post('societe');
            $valeurs->salesman              = $this->input->post('salesman');
            $valeurs->telephone             = $this->input->post('telephone');
            $valeurs->email                 = $this->input->post('email');
            $valeurs->segment_name          = $this->input->post('segment_name');
            $valeurs->segment_number        = $this->input->post('segment_number');
            $valeurs->message               = $this->input->post('message');
            $valeurs->object                = $this->input->post('object');
            $valeurs->lien_pour_telecharger = $this->input->post('lien_pour_telecharger');
            $valeurs->origin                = $this->input->post('origin');
            $valeurs->type                  = $this->input->post('type');
            $valeurs->software              = $this->input->post('software');
            $valeurs->client                = $this->input->post('client');
            $valeurs->produit_vendu         = $this->input->post('produit_vendu');
            $valeurs->database              = $this->input->post('database');

            $famille_darticles = $this->m_catalogues->liste(0);
            for ($i = 0; $i < count($famille_darticles['data']); $i++) {
                if ($famille_darticles['data'][$i]->cat_etat != 'en service') {
                    unset($famille_darticles['data'][$i]);
                }
            }

            $listes_valeurs->famille_darticles = $famille_darticles['data'];
            $listes_valeurs->societe           = $this->m_message_list->societe_option();
            $listes_valeurs->region            = $this->m_message_list->region_liste_option();
            $listes_valeurs->software          = $this->m_message_list->software_option();
            $listes_valeurs->salesman          = $this->m_utilisateurs->liste_empty_params();
            $listes_valeurs->type              = $this->m_message_list->type_option();
            $listes_valeurs->client            = $this->m_contacts->liste_option();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_message_list->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Message List",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Message List",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'message_list',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
        
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'name', 'label' => "Name", 'rules' => 'trim|required'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'object', 'label' => "Object", 'rules' => 'trim'),
            array('field' => 'lien_pour_telecharger', 'Lien Pour Telecharger' => "Name", 'rules' => 'trim|valid_url'),
            array('field' => 'famille_darticles', 'label' => "Famille D'Articles", 'rules' => 'trim'),
            array('field' => 'societe', 'label' => "Societe", 'rules' => 'trim'),
            array('field' => 'salesman', 'label' => "Salesman", 'rules' => 'trim'),
            array('field' => 'telephone', 'label' => "Telephone", 'rules' => 'trim'),
            array('field' => 'email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'segment_name', 'label' => "Segment Name", 'rules' => 'trim'),
            array('field' => 'segment_number', 'label' => "Segment Number", 'rules' => 'trim'),
            array('field' => 'department', 'label' => "Department", 'rules' => 'trim'),
            array('field' => 'region', 'label' => "Region", 'rules' => 'trim'),
            array('field' => 'country', 'label' => "Country", 'rules' => 'trim'),
            array('field' => 'activities', 'label' => "Activities", 'rules' => 'trim'),
            array('field' => 'origin', 'label' => "Origin", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'software', 'label' => "Software", 'rules' => 'trim'),
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim'),
            array('field' => 'produit_vendu', 'label' => "Produit Vendu", 'rules' => 'trim'),
            array('field' => 'database', 'label' => "Database", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'name'                  => $this->input->post('name'),
                'department'            => $this->input->post('department'),
                'region'                => $this->input->post('region'),
                'country'               => $this->input->post('country'),
                'activities'            => $this->input->post('activities'),
                'famille_darticles'     => $this->input->post('famille_darticles'),
                'societe'               => $this->input->post('societe'),
                'salesman'              => $this->input->post('salesman'),
                'telephone'             => $this->input->post('telephone'),
                'email'                 => $this->input->post('email'),
                'segment_name'          => $this->input->post('segment_name'),
                'segment_number'        => $this->input->post('segment_number'),
                'message'               => $this->input->post('message'),
                'object'                => $this->input->post('object'),
                'lien_pour_telecharger' => $this->input->post('lien_pour_telecharger'),
                'origin'                => $this->input->post('origin'),
                'type'                  => $this->input->post('type'),
                'software'              => $this->input->post('software'),
                'client'                => $this->input->post('client'),
                'produit_vendu'         => $this->input->post('produit_vendu'),
                'database'              => $this->input->post('database'),
            );

            $resultat = $this->m_message_list->maj($valeurs, $id);
           
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Message List a été modifié";
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
            $redirection = 'message_list/detail/' . $id;
            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs           = $this->m_message_list->detail($id);
            $scripts           = array();
            $listes_valeurs    = new stdClass();
            $famille_darticles = $this->m_catalogues->liste(0);
            for ($i = 0; $i < count($famille_darticles['data']); $i++) {
                if ($famille_darticles['data'][$i]->cat_etat != 'en service') {
                    unset($famille_darticles['data'][$i]);
                }
            }

            $listes_valeurs->famille_darticles = $famille_darticles['data'];
            $listes_valeurs->societe           = $this->m_message_list->societe_option();
            $listes_valeurs->salesman          = $this->m_utilisateurs->liste_empty_params();
            $listes_valeurs->type              = $this->m_message_list->type_option();
            $listes_valeurs->client            = $this->m_contacts->liste_option();
            $listes_valeurs->region            = $this->m_message_list->region_liste_option();
            $listes_valeurs->software          = $this->m_message_list->software_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_message_list->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier Message List",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Message List",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'message_list',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
        
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
     ******************************/
    public function detail($id, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_message_list->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'message_list/modification', 'primary'),
                array("Archiver", 'message_list/archive', 'warning'),
                array("Supprimer", 'message_list/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'name'                  => array("Name", 'VARCHAR 50', 'text', 'name'),
                    'type'                  => array("Type", 'VARCHAR 50', 'text', 'type'),
                    'message'               => array("Message", 'VARCHAR 50', 'text', 'message'),
                    'object'                => array("Object", 'VARCHAR 50', 'text', 'object'),
                    'lien_pour_telecharger' => array("Lien Pour Telecharger", 'VARCHAR 50', 'text', 'lien_pour_telecharger'),
                    'famille_darticles'     => array("Famille D'Articles", 'VARCHAR 50', 'text', 'famille_darticles_name'),
                    'societe'               => array("Societe", 'VARCHAR 50', 'text', 'societe_name'),
                    'salesman'              => array("Salesman", 'VARCHAR 50', 'text', 'commercial_name'),
                    'telephone'             => array("Telephone", 'VARCHAR 50', 'text', 'telephone'),
                    'email'                 => array("Email", 'VARCHAR 50', 'text', 'email'),
                    'segment_name'          => array("Segment Name", 'VARCHAR 50', 'text', 'segment_name'),
                    'segment_number'        => array("Segment Number", 'VARCHAR 50', 'text', 'segment_number'),
                    'department'            => array("Department", 'VARCHAR 50', 'text', 'department'),
                    'region'                => array("Region", 'VARCHAR 50', 'text', 'region'),
                    'country'               => array("Country", 'VARCHAR 50', 'country', 'origin'),
                    'activities'            => array("Activities", 'VARCHAR 50', 'text', 'activities'),
                    'origin'                => array("Origin", 'VARCHAR 50', 'text', 'origin'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Message List",
                'page'         => "templates/detail",
                'menu'         => "Extra|Message List",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'message_list',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_display_response($ajax, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id, $ajax = false)
    {
        $resultat = $this->m_message_list->archive($id);
        /*if ($resultat === false) {
        if (null === $this->session->flashdata('danger')) {
        $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
        }
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
        $redirection = '';
        }

        redirect($redirection);
        } else {
        $this->session->set_flashdata('success', "Message List a été archivé");
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
        $redirection = '';
        }

        redirect($redirection);
        }*/
        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $this->my_set_action_response($ajax, true, "Message List a été archivé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        redirect($redirection);
    }

    /******************************
     * Delete Message List Data
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
        $resultat = $this->m_message_list->remove($id);
        /*if ($resultat === false) {
        if (null === $this->session->flashdata('danger')) {
        $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
        }
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
        $redirection = '';
        }

        redirect($redirection);
        } else {
        $this->session->set_flashdata('success', "Message List a été supprimé");
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
        $redirection = '';
        }

        redirect($redirection);
        }*/
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
            $this->my_set_action_response($ajax, true, "Message List a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_message_list->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_message_list->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_message_list->unremove($id);
        }
    }

    public function update_value()
    {
        foreach ($_POST as $key => $value) {
            if ($key != 'id') {
                $valeurs[$key] = $value;
            }

        }
        $this->m_message_list->maj($valeurs, $this->input->post('id'));
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }

}
// EOF
