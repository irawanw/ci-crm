<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Pages_jaunes extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Ajouter un e-mailing"       => array('pages_jaunes/nouveau', 'plus', true, 'pages_jaunes_nouveau', null, array('form')),
            "Ajouter un e-mailing child" => array('pages_jaunes/nouveau_child', 'plus', true, 'pages_jaunes_nouveau_child', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('pages_jaunes/modification', 'pencil', false, 'pages_jaunes_modification'),
            "Archiver"           => array('pages_jaunes/archive', 'folder-close', false, 'pages_jaunes_archiver', "Veuillez confirmer la archive du Pages Jaune"),
            "Supprimer"          => array('pages_jaunes/remove', 'trash', false, 'pages_jaunes_supprimer', "Veuillez confirmer la suppression du page jaunes"),
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
        $this->load->model('m_pages_jaunes');
        $this->load->model('m_openemm');
        $this->load->model('m_production_mails');
        $this->load->model('m_message_list');
        $this->load->model('m_segments');
    }    

    /******************************
     * List of Pages_jaunes Data
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
            // array("Ajouter un e-mailing pages jaunes","pages_jaunes/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'pages_jaunes/index',
            'detail'             => array('pages_jaunes/detail', 'pages_jaunes_id', 'description'),
            'archive'            => array('pages_jaunes/archive', 'pages_jaunes_id', 'archive'),
            'champs'             => $this->m_pages_jaunes->get_champs('read','parent'),
            'filterable_columns' => $this->m_pages_jaunes->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: pages_jaunes/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'pages_jaunes/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'pages_jaunes/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'pages_jaunes/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'pages_jaunes',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);
        $scripts[] = $this->load->view("pages_jaunes/liste-js", array(), true);
        $scripts[] = $this->load->view('pages_jaunes/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('pages_jaunes', $this->session->id);
        $data = array(
            'title'        => "Envois Pages Jaunes",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Pages Jaunes",
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

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_pages_jaunes->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'pages_jaunes_id' => 't_pages_jaunes',
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

                $resultat = $this->m_pages_jaunes->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_pages_jaunes->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_pages_jaunes->get_champs('read','parent');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Pages_jaunes',
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
            $resultat = $this->m_pages_jaunes->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'pages_jaunes_child_id' => 't_pages_jaunes_child',
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

                $resultat = $this->m_pages_jaunes->liste_child($id, $parent_id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_pages_jaunes->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_pages_jaunes->get_champs('read','parent');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Pages_jaunes_child',
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
     * New Data
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(           
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim'),
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
                'software'       => 4,
                'client'         => $this->input->post('client'),
                'commande'       => $this->input->post('commande'),
                'message'        => $this->input->post('message'),
                'segment_numero' => $this->input->post('segment_numero'),
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),    
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );

            $resultat = $this->m_pages_jaunes->nouveau($valeurs);

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
                $this->my_set_action_response($ajax, true, "Pages Jaune a été enregistré avec succès", 'info', $ajaxData);
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
            $valeurs                   = new stdClass();
            $listes_valeurs            = new stdClass();
            $valeurs->software         = 4;
            $valeurs->client           = $this->input->post('client');
            $valeurs->commande         = $this->input->post('commande');
            $valeurs->message          = $this->input->post('message');
            $valeurs->segment_numero   = $this->input->post('segment_numero');
            $valeurs->segment_criteria = $this->input->post('segment_criteria');
            $valeurs->date_limite_de_fin   = formatte_date_to_bd($this->input->post('date_limite_de_fin'));
            $valeurs->quantite_envoyer    = $this->input->post('quantite_envoyer');

            //get commandes that belongs to client
            $commande                  = $this->m_pages_jaunes->commande(0);
            $new_object                = new stdClass;
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);
         
            $listes_valeurs->client         = $this->m_pages_jaunes->client_option();
            $listes_valeurs->commande       = $commande;
            $listes_valeurs->message        = $this->m_message_list->simple_list();
            $listes_valeurs->segment_numero = $this->m_segments->liste_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_pages_jaunes->get_champs('write','parent'),
                'onglets' => array(                   
                    array("Info Facturation", array("client", "commande")),
                    array("Message", array("message")),
                    array("Segment", array("segment_numero","segment_criteria")),
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")), 
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Pages Jaune",
                'page'           => "templates/form",
                'menu'           => "Agenda|Nouveau Pages Jaunes",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'pages_jaunes',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    public function nouveau_child($parent_id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'trim|required'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),
            array('field' => 'segment_part', 'label' => "Segment Number", 'rules' => 'trim'),
            array('field' => 'operateur_qui_envoie', 'label' => "Opérateur qui envoie", 'rules' => 'trim'),
            array('field' => 'verification_number', 'label' => "Validation number by manager", 'rules' => 'trim'),
            array('field' => 'copy_mail', 'label' => "Copy Mail", 'rules' => 'trim'),
            array('field' => 'number_sent_through', 'Number sent through' => "Client", 'rules' => 'trim'),
            array('field' => 'number_sent_mail', 'label' => "Number sent through mails", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité à envoyee", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'parent_id'            => $this->input->post('parent_id'),
                'date_envoi'               => formatte_date_to_bd($this->input->post('date_envoi')),
                'segment_part'         => $this->input->post('segment_part'),
                'operateur_qui_envoie' => $this->input->post('operateur_qui_envoie'),
                'number_sent_through'  => $this->input->post('number_sent_through'),
                'number_sent_mail'     => $this->input->post('number_sent_mail'),
                'copy_mail'            => $this->input->post('copy_mail'),
                'verification_number'  => $this->input->post('verification_number'),
                'quantite_envoyee'     => $this->input->post('quantite_envoyee'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
            );

            $resultat = $this->m_pages_jaunes->nouveau_child($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $resultat,
                        'timeStamp'  => round(microtime(true) * 1000),
                        'isChild'    => true,
                        'parentId'   => $parent_id
                    ),
                );
                $this->my_set_action_response($ajax, true, "Pages Jaune child a été enregistré avec succès", 'info', $ajaxData);
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

            $valeurs->parent_id            = $parent_id;
            $valeurs->date_envoi           = formatte_date_to_bd($this->input->post('date_envoi'));
            $valeurs->segment_part         = $this->input->post('segment_part');
            $valeurs->operateur_qui_envoie = $this->input->post('operateur_qui_envoie');
            $valeurs->number_sent_through  = $this->input->post('number_sent_through');
            $valeurs->number_sent_mail     = $this->input->post('number_sent_mail');
            $valeurs->copy_mail            = $this->input->post('copy_mail');
            $valeurs->verification_number  = $this->input->post('verification_number');
            $valeurs->quantite_envoyee     = $this->input->post('quantite_envoyee');
            $valeurs->open                     = $this->input->post('open');
            $valeurs->open_pourcentage         = $this->input->post('open_pourcentage');

            $this->db->order_by('emp_nom', 'ASC');
            $q = $this->db->get('t_employes');

            $listes_valeurs->parent_id            = $this->m_pages_jaunes->parent_option();
            $listes_valeurs->segment_part         = $this->m_pages_jaunes->segment_part_option();
            $listes_valeurs->operateur_qui_envoie = $q->result();
            $listes_valeurs->verification_number  = $this->m_pages_jaunes->verification_number_option();
            $listes_valeurs->copy_mail            = $this->m_production_mails->simple_list();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_pages_jaunes->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array("parent_id")),
                    array("Segment", array("segment_part")),
                    array("Suivi de l'Envoi", array('date_envoi', "verification_number", "number_sent_through", "number_sent_mail", "quantite_envoyee","open","open_pourcentage")),
                    array("Technical", array("operateur_qui_envoie", "copy_mail")),
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Pages Jaune Child",
                'page'           => "templates/form",
                'menu'           => "Agenda|Nouveau Pages Jaune Child",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'pages_jaunes',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
     ******************************/
    public function detail($id)
    {

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
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim'),
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
                'client'         => $this->input->post('client'),
                'commande'       => $this->input->post('commande'),
                'message'        => $this->input->post('message'),
                'segment_numero' => $this->input->post('segment_numero'),
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),  
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );

            $resultat    = $this->m_pages_jaunes->maj($valeurs, $id);
            $redirection = 'pages_jaunes/detail/' . $id;

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Pages Jaune a été modifié";
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
            $valeurs        = $this->m_pages_jaunes->detail_for_form($id);
            $valeurs->segment_criteria = "";
            $listes_valeurs = new stdClass();
            //get commandes that belongs to client
            $commande                  = $this->m_pages_jaunes->commande($id);
            $new_object                = new stdClass;
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);

            $this->db->order_by('emp_nom', 'ASC');
            $q = $this->db->get('t_employes');
          
            $listes_valeurs->client         = $this->m_pages_jaunes->client_option();
            $listes_valeurs->commande       = $commande;
            $listes_valeurs->message        = $this->m_message_list->simple_list();
            $listes_valeurs->segment_numero = $this->m_segments->liste_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_pages_jaunes->get_champs('write','parent'),
                'onglets' => array(                  
                    array("Info Facturation", array("client", "commande")),
                    array("Message", array("message")),
                    array("Segment", array("segment_numero","segment_criteria")),
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")), 
                ),
            );

            $data = array(
                'title'          => "Modifier Pages Jaune",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Pages Jaunes",
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'pages_jaunes',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    public function modification_child($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'trim|required'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),
            array('field' => 'segment_part', 'label' => "Segment Number", 'rules' => 'trim'),
            array('field' => 'operateur_qui_envoie', 'label' => "Opérateur qui envoie", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité envoyee", 'rules' => 'trim'),
            array('field' => 'verification_number', 'label' => "Validation number by manager", 'rules' => 'trim'),
            array('field' => 'copy_mail', 'label' => "Copy Mail", 'rules' => 'trim'),
            array('field' => 'number_sent_through', 'Number sent through' => "Client", 'rules' => 'trim'),
            array('field' => 'number_sent_mail', 'label' => "Number sent through mails", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'parent_id'            => $this->input->post('parent_id'),
                'segment_part'         => $this->input->post('segment_part'),
                'operateur_qui_envoie' => $this->input->post('operateur_qui_envoie'),
                'date_envoi'           => formatte_date_to_bd($this->input->post('date_envoi')),
                'number_sent_through'  => $this->input->post('number_sent_through'),
                'number_sent_mail'     => $this->input->post('number_sent_mail'),
                'copy_mail'            => $this->input->post('copy_mail'),
                'verification_number'  => $this->input->post('verification_number'),
                'quantite_envoyee'     => $this->input->post('quantite_envoyee'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
            );

            $resultat    = $this->m_pages_jaunes->maj_child($valeurs, $id);
            $redirection = 'pages_jaunes/detail/' . $id;

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Pages Jaune child a été modifié";
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
            $valeurs        = $this->m_pages_jaunes->detail_for_form_child($id);
            $listes_valeurs = new stdClass();
            $this->db->order_by('emp_nom', 'ASC');
            $q = $this->db->get('t_employes');

            $listes_valeurs->parent_id            = $this->m_pages_jaunes->parent_option();
            $listes_valeurs->segment_part         = $this->m_pages_jaunes->segment_part_option();
            $listes_valeurs->operateur_qui_envoie = $q->result();
            $listes_valeurs->verification_number  = $this->m_pages_jaunes->verification_number_option();
            $listes_valeurs->copy_mail            = $this->m_production_mails->simple_list();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_pages_jaunes->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array("parent_id")),
                    array("Segment", array("segment_part")),
                    array("Suivi de l'Envoi", array("date_envoi", "verification_number", "number_sent_through", "number_sent_mail", "quantite_envoyer", "quantite_envoyee")),
                    array("Technical", array("operateur_qui_envoie", "copy_mail")),
                ),
            );

            $data = array(
                'title'          => "Modifier Pages Jaune",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Pages Jaunes",
                'id'             => $id,
                'form_id'        => "form-pages_jaunes-modification_child-1",
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'pages_jaunes',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_pages_jaunes->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Passe de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    public function facture_option($id = 0)
    {
        $resultat = $this->m_pages_jaunes->get_facture($id);
        echo json_encode($resultat);
    }

    public function message_option($id = 0)
    {
        $data = $this->m_message_list->detail($id);
        echo json_encode($data);
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_pages_jaunes->archive($id);

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
            $this->my_set_action_response($ajax, true, "Pages Jaune a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function archive_child($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_pages_jaunes->archive_child($id);

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
            $this->my_set_action_response($ajax, true, "Pages Jaune child a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    /******************************
     * Delete Pages_jaunes Data
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

        $resultat = $this->m_pages_jaunes->remove($id);

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
            $this->my_set_action_response($ajax, true, "Pages Jaune a été supprimé", 'info', $ajaxData);
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

        $resultat = $this->m_pages_jaunes->remove_child($id);

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
            $this->my_set_action_response($ajax, true, "Pages Jaune child a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_pages_jaunes->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_pages_jaunes->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_pages_jaunes->unremove($id);
        }
    }

    public function doupload()
    {
        $upPath = FCPATH . '/fichiers/pages_jaunes/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "*",
            'overwrite'     => false,
            'max_size'      => "20480000",
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('message_sent')) {
            $data['fileName']   = '';
            $data['imageError'] = $this->upload->display_errors();
            $this->session->set_flashdata('warning', $data['imageError']);
        } else {
            $imageDetailArray   = $this->upload->data();
            $data['fileName']   = $imageDetailArray['file_name'];
            $data['imageError'] = '';
        }
        return $data;
    }

    public function upload_message_sent()
    {
        $imagefile = $this->doupload();
        if ($imagefile['imageError'] == '') {
            $valeurs = array('message_sent' => $imagefile['fileName']);
            $this->m_pages_jaunes->maj($valeurs, $this->input->post('upload_id'));
        }
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }

    public function update_value()
    {
        foreach ($_POST as $key => $value) {
            if ($key != 'id') {
                $valeurs[$key] = $value;
            }

        }
        $this->m_pages_jaunes->maj($valeurs, $this->input->post('id'));
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }
}
// EOF
