<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_manual_sending m_manual_sending
*/
class Manual_sending extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Ajouter un e-mailing" => array('manual_sending/nouveau', 'plus', true, 'manual_sending_nouveau', null, array('form')),
             "Ajouter un e-mailing child" => array('manual_sending/nouveau_child', 'plus', true, 'manual_sending_nouveau_child', null, array('form')),
        ),
        array(
            //"Consulter" => array('manual_sending/detail', 'eye-open', false, 'manual_sending_detail'),
            "Consulter/Modifier"  => array('manual_sending/modification', 'pencil', false, 'manual_sending_modification'),
            "Archiver" => array('manual_sending/archive', 'folder-close', false, 'manual_sending_archive',"Veuillez confirmer la archive du manual sending"),
            "Supprimer" => array('manual_sending/remove', 'trash', false, 'manual_sending_supprimer',"Veuillez confirmer la suppression du manual sending"),
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
        $this->load->model(array('m_manual_sending', 'm_message_list', 'm_production_mails', 'm_openemm','m_segments'));
    }

    /******************************
     * List of Manual_sending Data
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
            // array("Ajouter un e-mailing pages jaunes","manual_sending/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'manual_sending/index',
            'detail'             => array('manual_sending/detail', 'manual_sending_id', 'description'),
            'archive'            => array('manual_sending/archive', 'manual_sending_id', 'archive'),
            'champs'             => $this->m_manual_sending->get_champs('read','parent'),
            'filterable_columns' => $this->m_manual_sending->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: manual_sending/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'manual_sending/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'manual_sending/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'manual_sending/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'manual_sending',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);

        $scripts[] = $this->load->view("manual_sending/liste-js", array(), true);
        $scripts[] = $this->load->view('manual_sending/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('manual_sending', $this->session->id);
        $data = array(
            'title'        => "Envois manuels",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Manual Sending",
            'scripts'      => $scripts,
			'controleur' 	=> 'manual_sending',
			'methode' 		=> __FUNCTION__,
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_manual_sending->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'manual_sending_id' => 't_manual_sending',
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

                $resultat = $this->m_manual_sending->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_manual_sending->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_manual_sending->get_champs('read','parent');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Manual_sendings'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /** Ajax call for List
     ******************************/
    public function index_child_json($id = 0)
    {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order      = $this->input->post('order');
        $columns    = $this->input->post('columns');
        $filters    = $this->input->post('filters');
        $parent_id  = $this->input->post('parentId');

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
            $resultat = $this->m_manual_sending->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'manual_sending_child_id' => 't_manual_sending_child',
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

                $resultat = $this->m_manual_sending->liste_child($id, $parent_id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_manual_sending->liste_child($id, $parent_id, $pagelength, $pagestart, $filters);
            }
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
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(            
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'segment_numero', 'label' => "Segment Numero", 'rules' => 'trim'), 
            array('field' => 'date_limite_de_fin', 'label' => "Date Limite de Fin", 'rules' => 'trim'),
            array('field' => 'quantite_envoyer', 'label' => "Quantité à envoyer", 'rules' => 'trim'),    
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'software'                 => 3,
                'client'                   => $this->input->post('client'),
                'commande'                 => $this->input->post('commande'),
                'message'                  => $this->input->post('message'),
                'segment_numero'           => $this->input->post('segment_numero'),                
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),    
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );

            $resultat = $this->m_manual_sending->nouveau($valeurs);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Manual Sending a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
            
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();

            $valeurs->software             = 3;
            $valeurs->client               = $this->input->post('client');
            $valeurs->commande             = $this->input->post('commande');
            $valeurs->message              = $this->input->post('message');
            $valeurs->segment_numero       = $this->input->post('segment_numero');
            $valeurs->segment_criteria = $this->input->post('segment_criteria');
            $valeurs->date_limite_de_fin   = formatte_date_to_bd($this->input->post('date_limite_de_fin'));
            $valeurs->quantite_envoyer    = $this->input->post('quantite_envoyer');
           
            $listes_valeurs->client               = $this->m_manual_sending->client_option();
            $listes_valeurs->commande             = $this->m_manual_sending->commande(0);
            $listes_valeurs->message              = $this->m_message_list->simple_list();
            $listes_valeurs->segment_numero       = $this->m_segments->liste_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_manual_sending->get_champs('write','parent'),
                'onglets' => array(                   
                    array("Info Facturation", array('client', 'commande')),
                    array("Message", array("message")),
                    array("Segment", array('segment_numero','segment_criteria')),     
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")),     
                ),
            );

            $data = array(
                'title' => "Ajouter un nouveau Manual Sending",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Manual Sendings",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'manual_sending',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);           
        }
    }

    /******************************
     * Edit function for Manual_sending Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(           
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),  
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim'),
            array('field' => 'message', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'segment_numero', 'label' => "Segment Numero", 'rules' => 'trim'), 
            array('field' => 'date_limite_de_fin', 'label' => "Date Limite de Fin", 'rules' => 'trim'),
            array('field' => 'quantite_envoyer', 'label' => "Quantité à envoyer", 'rules' => 'trim'),    
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(                
                'client'                   => $this->input->post('client'),
                'commande'                 => $this->input->post('commande'),
                'message'                  => $this->input->post('message'),
                'segment_numero'           => $this->input->post('segment_numero'),                
                'date_limite_de_fin'       => formatte_date_to_bd($this->input->post('date_limite_de_fin')),  
                'quantite_envoyer'         => $this->input->post('quantite_envoyer'),
            );

            $resultat = $this->m_manual_sending->maj($valeurs, $id);
            
            $redirection = 'manual_sending/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Manual Sending a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
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
            $valeurs = $this->m_manual_sending->detail_for_form($id);
            $listes_valeurs = new stdClass;

            $valeur = $this->input->post('client');
            if (isset($valeur)) {
                $valeurs->client = $valeur;
            }            
            $valeurs->segment_criteria = "";
            
            $listes_valeurs->client               = $this->m_manual_sending->client_option();
            $listes_valeurs->commande             = $this->m_manual_sending->commande(0,$valeurs->client);
            $listes_valeurs->message              = $this->m_message_list->simple_list();    
            $listes_valeurs->segment_numero       = $this->m_segments->liste_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_manual_sending->get_champs('write','parent'),
                'onglets' => array(                    
                    array("Info Facturation", array('client', 'commande')),
                    array("Message", array("message")),
                    array("Segment", array('segment_numero','segment_criteria')),  
                    array("Suivi de l'Envoi", array('date_limite_de_fin', "quantite_envoyer")),                 
                ),
            );

            $data = array(
                'title' => "Modifier Manual Sending",
                'page' => "templates/form",
                'menu' => "Extra|Edit Manual Sendings",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'manual_sending',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);            
        }
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau_child($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(           
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'required|trim'),
            array('field' => 'operateur_qui_envoie', 'label' => "Opérateur Qui Envoie", 'rules' => 'trim'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),
            array('field' => 'segment_part', 'label' => "Segment number", 'rules' => 'trim'),
            array('field' => 'verification_number', 'label' => "Verification Number Sent By Manager", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité envoyee", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_orange', 'label' => "Orange/Wanadoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_free', 'label' => "Free", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_sfr', 'label' => "SFR", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_gmail', 'label' => "Gmail", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_microsoft', 'label' => "Microsoft", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_yahoo', 'label' => "Yahoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_ovh', 'label' => "OVH", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_oneandone', 'label' => "1 and 1", 'rules' => 'trim'),
            array('field' => 'manual_sender', 'label' => "Manual Sender", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(                
                'operateur_qui_envoie'     => $this->input->post('operateur_qui_envoie'),
                'date_envoi'               => formatte_date_to_bd($this->input->post('date_envoi')),
                'segment_part'             => $this->input->post('segment_part'),
                'verification_number'      => $this->input->post('verification_number'),
                'quantite_envoyee'         => $this->input->post('quantite_envoyee'),
                'deliv_sur_test_orange'    => $this->input->post('deliv_sur_test_orange'),
                'deliv_sur_test_free'      => $this->input->post('deliv_sur_test_free'),
                'deliv_sur_test_sfr'       => $this->input->post('deliv_sur_test_sfr'),
                'deliv_sur_test_gmail'     => $this->input->post('deliv_sur_test_gmail'),
                'deliv_sur_test_microsoft' => $this->input->post('deliv_sur_test_microsoft'),
                'deliv_sur_test_yahoo'     => $this->input->post('deliv_sur_test_yahoo'),
                'deliv_sur_test_ovh'       => $this->input->post('deliv_sur_test_ovh'),
                'deliv_sur_test_oneandone' => $this->input->post('deliv_sur_test_oneandone'),
                'manual_sender'            => $this->input->post('manual_sender'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
                'parent_id'                => $this->input->post('parent_id'),
            );

            $resultat = $this->m_manual_sending->nouveau_child($valeurs);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),       
                         'type'       => 'recordadd',                                 
                         'id'         => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                         'parentId'   => $valeurs['parent_id']
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Manual Sending Child a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
            
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();
           
            $valeurs->operateur_qui_envoie = $this->input->post('operateur_qui_envoie');                 
            $valeurs->date_envoi           = formatte_date_to_bd($this->input->post('date_envoi'));
            $valeurs->segment_part        = $this->input->post('segment_part');
            $valeurs->quantite_envoyee    = $this->input->post('quantite_envoyee');
            $valeurs->verification_number = $this->input->post('verification_number');
            $valeurs->deliv_sur_test_orange    = $this->input->post('deliv_sur_test_orange');
            $valeurs->deliv_sur_test_free      = $this->input->post('deliv_sur_test_free');
            $valeurs->deliv_sur_test_sfr       = $this->input->post('deliv_sur_test_sfr');
            $valeurs->deliv_sur_test_gmail     = $this->input->post('deliv_sur_test_gmail');
            $valeurs->deliv_sur_test_microsoft = $this->input->post('deliv_sur_test_microsoft');
            $valeurs->deliv_sur_test_yahoo     = $this->input->post('deliv_sur_test_yahoo');
            $valeurs->deliv_sur_test_ovh       = $this->input->post('deliv_sur_test_ovh');
            $valeurs->deliv_sur_test_oneandone = $this->input->post('deliv_sur_test_oneandone');
            $valeurs->manual_sender            = $this->input->post('manual_sender');
            $valeurs->parent_id                = $this->input->post('parent_id');
            $valeurs->open                     = $this->input->post('open');
            $valeurs->open_pourcentage         = $this->input->post('open_pourcentage');
            
            $listes_valeurs->operateur_qui_envoie = $this->m_manual_sending->operateur_qui_envoie_option();            
            $listes_valeurs->segment_part         = $this->m_manual_sending->segment_part_option();
            $listes_valeurs->verification_number  = $this->m_manual_sending->verification_number_option();

            $deliv_sur_test_option                    = $this->m_manual_sending->deliv_sur_test_option();
            $listes_valeurs->deliv_sur_test_orange    = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_free      = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_sfr       = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_gmail     = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_microsoft = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_yahoo     = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_ovh       = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_oneandone = $deliv_sur_test_option;
            $listes_valeurs->manual_sender            = $this->m_manual_sending->manual_sender_option();
            $listes_valeurs->parent_id                = $this->m_manual_sending->parent_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_manual_sending->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array('parent_id')),                    
                    array("Segment", array('segment_part')),
                    array("Suivi de l'Envoi", array('date_envoi',"verification_number", "quantite_envoyee","open","open_pourcentage")),
                    array("Delivrabilite Sur Test", array('deliv_sur_test_orange', 'deliv_sur_test_free', 'deliv_sur_test_sfr', 'deliv_sur_test_gmail', 'deliv_sur_test_yahoo', 'deliv_sur_test_microsoft', 'deliv_sur_test_ovh', 'deliv_sur_test_oneandone')),
                    array("Technical", array('operateur_qui_envoie', 'manual_sender')),
                ),
            );

            $data = array(
                'title' => "Ajouter un nouveau Manual Sending",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Manual Sendings",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'manual_sending',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);           
        }
    }

    /******************************
     * Modification Child
     ******************************/
    public function modification_child($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(           
            array('field' => 'parent_id', 'label' => "Parent Sending", 'rules' => 'required|trim'),
            array('field' => 'operateur_qui_envoie', 'label' => "Opérateur Qui Envoie", 'rules' => 'trim'),
            array('field' => 'date_envoi', 'label' => "Date Envoi", 'rules' => 'trim'),     
            array('field' => 'segment_part', 'label' => "Segment number", 'rules' => 'trim'),
            array('field' => 'verification_number', 'label' => "Verification Number Sent By Manager", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantité envoyee", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_orange', 'label' => "Orange/Wanadoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_free', 'label' => "Free", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_sfr', 'label' => "SFR", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_gmail', 'label' => "Gmail", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_microsoft', 'label' => "Microsoft", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_yahoo', 'label' => "Yahoo", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_ovh', 'label' => "OVH", 'rules' => 'trim'),
            array('field' => 'deliv_sur_test_oneandone', 'label' => "1 and 1", 'rules' => 'trim'),
            array('field' => 'manual_sender', 'label' => "Manual Sender", 'rules' => 'trim'),
            array('field' => 'open', 'label' => "Open", 'rules' => 'trim'),
            array('field' => 'open_pourcentage', 'label' => "Open %", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(                
                'operateur_qui_envoie'     => $this->input->post('operateur_qui_envoie'), 
                'date_envoi'               => formatte_date_to_bd($this->input->post('date_envoi')),             
                'segment_part'             => $this->input->post('segment_part'),
                'verification_number'      => $this->input->post('verification_number'),
                'quantite_envoyee'         => $this->input->post('quantite_envoyee'),
                'deliv_sur_test_orange'    => $this->input->post('deliv_sur_test_orange'),
                'deliv_sur_test_free'      => $this->input->post('deliv_sur_test_free'),
                'deliv_sur_test_sfr'       => $this->input->post('deliv_sur_test_sfr'),
                'deliv_sur_test_gmail'     => $this->input->post('deliv_sur_test_gmail'),
                'deliv_sur_test_microsoft' => $this->input->post('deliv_sur_test_microsoft'),
                'deliv_sur_test_yahoo'     => $this->input->post('deliv_sur_test_yahoo'),
                'deliv_sur_test_ovh'       => $this->input->post('deliv_sur_test_ovh'),
                'deliv_sur_test_oneandone' => $this->input->post('deliv_sur_test_oneandone'),
                'manual_sender'            => $this->input->post('manual_sender'),
                'parent_id'                => $this->input->post('parent_id'),
                'open'                     => $this->input->post('open'),
                'open_pourcentage'         => $this->input->post('open_pourcentage'),
            );

            $resultat = $this->m_manual_sending->maj_child($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Manual Sending Child a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                         'isChild' => true
                     ),
                     );
                }

                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
            
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = $this->m_manual_sending->detail_for_form_child($id);
            $listes_valeurs = new stdClass();
                      
            $listes_valeurs->operateur_qui_envoie = $this->m_manual_sending->operateur_qui_envoie_option();            
            $listes_valeurs->segment_part         = $this->m_manual_sending->segment_part_option();
            $listes_valeurs->verification_number  = $this->m_manual_sending->verification_number_option();

            $deliv_sur_test_option                    = $this->m_manual_sending->deliv_sur_test_option();
            $listes_valeurs->deliv_sur_test_orange    = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_free      = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_sfr       = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_gmail     = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_microsoft = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_yahoo     = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_ovh       = $deliv_sur_test_option;
            $listes_valeurs->deliv_sur_test_oneandone = $deliv_sur_test_option;
            $listes_valeurs->manual_sender            = $this->m_manual_sending->manual_sender_option();
            $listes_valeurs->parent_id                = $this->m_manual_sending->parent_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_manual_sending->get_champs('write','child'),
                'onglets' => array(
                    array("Parent", array('parent_id')),                    
                    array("Segment", array('segment_part')),
                    array("Suivi de l'Envoi", array('date_envoi', "verification_number", "quantite_envoyee","open","open_pourcentage")),
                    array("Delivrabilite Sur Test", array('deliv_sur_test_orange', 'deliv_sur_test_free', 'deliv_sur_test_sfr', 'deliv_sur_test_gmail', 'deliv_sur_test_yahoo', 'deliv_sur_test_microsoft', 'deliv_sur_test_ovh', 'deliv_sur_test_oneandone')),
                    array("Technical", array('operateur_qui_envoie', 'manual_sender')),
                ),
            );

            $data = array(
                'title' => "Modifier Manual Sending Child",
                'page' => "templates/form",
                'menu' => "Agenda|Edit Manual Sending Child",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'manual_sending',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);           
        }
    }

    /******************************
     * Detail of Manual_sending Data
     ******************************/
    public function detail($id)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_manual_sending->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'manual_sending/modification', 'primary'),
                array("Archiver", 'manual_sending/archive', 'warning'),
                array("Supprimer", 'manual_sending/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    //PARAMETRES
                    'date_envoi'                => array("Date envoi", 'DATE', 'text', 'date_envoi'),
                    'software'                  => array("Software", 'VARCHAR 50', 'text', 'software'),
                    'operateur_qui_envoie_name' => array("Opérateur qui envoie", 'VARCHAR 50', 'text', 'operateur_qui_envoie_name'),
                    'date_limite_de_fin'        => array("Date limite de fin", 'DATE', 'text', 'date_limite_de_fin'),
                    //INFO FACTURATION
                    'client'                    => array("Client", 'VARCHAR 50', 'text', 'client_name'),
                    'commande'                  => array("Commande", 'VARCHAR 50', 'text', 'commande_name'),
                    'facture'                   => array("Facture", 'VARCHAR 50', 'text', 'facture'),
                    'ht'                        => array("HT", 'VARCHAR 50', 'text', 'ht'),
                    //MESSAGE
                    'message_name'              => array("Nom", 'VARCHAR 50', 'text', 'message_name'),
                    'message_view'              => array("View Message", 'VARCHAR 50', 'text', 'message_view'),
                    'message_lien'              => array("Lien Pour Télécharger", 'VARCHAR 50', 'text', 'message_lien'),
                    'message_object'            => array("Objet Du Message", 'VARCHAR 50', 'text', 'message_object'),
                    'message_type'              => array("Type", 'VARCHAR 50', 'text', 'message_type'),
                    //CORPS DU MESSSAGE
                    'message_famille'           => array("famille d'articles", 'VARCHAR 50', 'text', 'message_famille'),
                    'message_societe'           => array("société", 'VARCHAR 50', 'text', 'message_societe'),
                    'message_commercial'        => array("Commercial", 'VARCHAR 50', 'text', 'message_commercial'), //
                    'message_email'             => array("e-mail du corps", 'VARCHAR 50', 'text', 'message_email'), //
                    'message_telephone'         => array("telephone", 'VARCHAR 50', 'text', 'message_telephone'),
                    //SEGMENT
                    'segment_numero'            => array("Numéro", 'VARCHAR 50', 'text', 'segment_numero'),
                    'segment_part'              => array("Segment number", 'VARCHAR 50', 'text', 'segment_part'),
                    'segment_nom'               => array("Nom", 'VARCHAR 50', 'text', 'manual_sending_id'), //
                    'critere_one'               => array("Critere 1", 'VARCHAR 50', 'text', 'manual_sending_id'), //
                    'critere_two'               => array("Critere 2", 'VARCHAR 50', 'text', 'manual_sending_id'), //
                    'many_criterias'            => array("as many criterias as necessary", 'VARCHAR 50', 'text', 'manual_sending_id'), //
                    //ENVOI
                    'quantite_envoyer'          => array("Quantité à envoyer", 'VARCHAR 50', 'text', 'quantite_envoyer'),
                    'quantite_envoyee'          => array("Quantité envoyée", 'VARCHAR 50', 'text', 'quantite_envoyee'),
                    'verification_number'       => array("Verification number sent by manager", 'VARCHAR 50', 'text', 'verification_number'),  
                    'open'                      => array("Open", 'VARCHAR 50', 'text', 'open'),
                    'open_pourcentage'          => array("Open %", 'VARCHAR 50', 'text', 'open_pourcentage'),       
                    //DELIVRABILITE SUR TEST
                    'deliv_sur_test_orange'     => array("Orange", 'VARCHAR 50', 'text', 'deliv_sur_test_orange'),
                    'deliv_sur_test_free'       => array("Free", 'VARCHAR 50', 'text', 'deliv_sur_test_free'),
                    'deliv_sur_test_sfr'        => array("SFR", 'VARCHAR 50', 'text', 'deliv_sur_test_sfr'),
                    'deliv_sur_test_gmail'      => array("Gmail", 'VARCHAR 50', 'text', 'deliv_sur_test_gmail'),
                    'deliv_sur_test_yahoo'      => array("Yahoo", 'VARCHAR 50', 'text', 'deliv_sur_test_yahoo'),
                    'deliv_sur_test_microsoft'  => array("Microsoft", 'VARCHAR 50', 'text', 'deliv_sur_test_microsoft'),
                    'deliv_sur_test_ovh'        => array("OVH", 'VARCHAR 50', 'text', 'deliv_sur_test_ovh'),
                    'deliv_sur_test_oneandone'  => array("1and1", 'VARCHAR 50', 'text', 'deliv_sur_test_oneandone'),
                    //DELIVRABILITE REELLE
                    'deliv_reelle_orange'       => array("Orange", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_free'         => array("Free", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_sfr'          => array("SFR", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_gmail'        => array("Gmail", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_yahoo'        => array("Yahoo", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_microsoft'    => array("Microsoft", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_ovh'          => array("OVH", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    'deliv_reelle_oneandone'    => array("1and1", 'VARCHAR 50', 'text', 'manual_sending_id'),
                    //MANUAL
                    'manual_sender_email'       => array("Manual Sender Email", 'VARCHAR 50', 'text', 'manual_sender_email'),
                    'manual_sender_domain'      => array("Manual Sender Domain", 'VARCHAR 50', 'text', 'manual_sender_domain'),
                ),
                'onglets' => array(
                    array("Parametres", array('software')),
                    array("Info Facturation", array('client', 'commande')),
                    array("Message", array("message_name")),
                    array("Segment", array('segment_numero', 'segment_part')),
                    array("Suivi de l'Envoi", array('date_envoi', 'date_limite_de_fin', "quantite_envoyer", "quantite_envoyee", "verification_number","open","open_pourcentage")),
                    array("Delivrabilite Sur Test", array('deliv_sur_test_orange', 'deliv_sur_test_free', 'deliv_sur_test_sfr', 'deliv_sur_test_gmail', 'deliv_sur_test_yahoo', 'deliv_sur_test_microsoft', 'deliv_sur_test_ovh', 'deliv_sur_test_oneandone')),
                    array("Technical", array('operateur_qui_envoie_name', 'manual_sender_email', 'manual_sender_domain')),
                ),
            );

            $data = array(
                'title'        => "Détail of Manual",
                'page'         => "templates/detail",
                'menu'         => "Extra|Manual Sending",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'manual_sending',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_manual_sending->archive($id);

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
            $this->my_set_action_response($ajax, true, "Manual Sending a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive_child($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_manual_sending->archive_child($id);

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
                    'isChild'    => true
                ),
            );
            $this->my_set_action_response($ajax, true, "Manual Sending child a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    /******************************
     * Delete Manual_sending Data
     ******************************/
    public function remove($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_manual_sending->remove($id);

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
            $this->my_set_action_response($ajax, true, "Manual Sending a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);        
    }

    /******************************
     * Delete Manual_sending Data
     ******************************/
    public function remove_child($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_manual_sending->remove_child($id);

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
                    'isChild'    => true
                ),
            );
            $this->my_set_action_response($ajax, true, "Manual Sending child a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_manual_sending->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_manual_sending->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_manual_sending->unremove($id);
        }
    }

    public function doupload()
    {
        $upPath = FCPATH . '/fichiers/manual_sending/';
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
            $this->m_manual_sending->maj($valeurs, $this->input->post('id'));
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
        //$this->m_manual_sending->maj($valeurs, $this->input->post('id'));
        $this->m_manual_sending->updateMessage($valeurs, $this->input->post('id'));
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_manual_sending->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Pas de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    public function facture_option($id = 0)
    {
        $resultat = $this->m_manual_sending->get_facture($id);
        echo json_encode($resultat);
    }

    public function message_option($id = 0)
    {
        $data = $this->m_message_list->detail($id);
        echo json_encode($data);
    }
}
// EOF
