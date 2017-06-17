<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_demande des devis m_demande des devis
 */
class Tests_followup extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Followup" => array(
            array(
                "Nouveau" => array('tests_followup/nouveau', 'plus', true, 'tests_followup_nouveau', null, array('form')),
            ),
            array(
                // "Consulter" => array('tests_followup/detail', 'eye-open', false, 'tests_followup_detail', null, array('view', 'dblclick')),
                "Consulter/Modifier"  => array('tests_followup/modification', 'pencil', false, 'tests_followup_modification', null, array('form')),
                "Archiver" => array('tests_followup/archive', 'folder-close', false, 'tests_followup_archive',"Veuillez confirmer la archive du tests followup", array('confirm-modify' => array('tests_followup/index'))),
                "Dupliquer" => array('tests_followup/dupliquer', 'duplicate', false, 'list_utilisateurs_dupliquer',"Veuillez confirmer la duplique du test followup", array('confirm-modify' => array('tests_followup/index'))),
                "Supprimer" => array('tests_followup/suppression', 'trash', false, 'tests_followup_supprimer', "Veuillez confirmer la suppression du message", array('confirm-delete' => array('tests_followup/index'))),
            ),
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
            ),
			array(
				"Export xlsx" => array('#', 'list-alt', true, 'export_xlsx'),
				"Export pdf"  => array('#', 'book', true, 'export_pdf'),
				"Imprimer"    => array('#', 'print', true, 'print_list'),
			),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_tests_followup');
    }

    /******************************
     * List
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

    /******************************
     * Liste
     ******************************/
    public function liste($id = 0, $mode = 0)
    {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'tests_followup/index',
            'detail'             => array('tests_followup/detail', 'followup_id', 'message_name'),
            'champs'             => $this->m_tests_followup->get_champs('read'),
            'filterable_columns' => $this->m_tests_followup->liste_filterable_columns(),
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'tests_followup/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'tests_followup/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'tests_followup/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'tests_followup',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);

        $scripts[] = $this->load->view('tests_followup/liste-js', array(), true);
        $vues      = $this->m_vues->vues_ctrl('tests_followup', $this->session->id);
        $data      = array(
            'title'        => "Tests Followup",
            'page'         => "templates/datatables",
            'menu'         => "Extra|tests_followup",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action["Followup"],
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
     * Liste(datasource)
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
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {
            $resultat = $this->m_tests_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array();

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

                $resultat = $this->m_tests_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_tests_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }
/*
        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
            $champs     = $this->m_tests_followup->get_champs('read');
            $params     = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'TestsFollowup',
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
		*/
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
     * Nouveau
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->model(array('m_message_list', 'm_providers', 'm_production_mails','m_softwares'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');
        $config = array(
            array('field' => 'message_id', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'software', 'label' => "Software", 'rules' => 'trim'),
            array('field' => 'domain_used_send', 'label' => "Domain used sending", 'rules' => 'trim'),
            array('field' => 'domain_check_before_send', 'label' => "Domain check before sending", 'rules' => 'trim'),
            array('field' => 'domain_check_after_send', 'label' => "Domain check after sending", 'rules' => 'trim'),
            array('field' => 'rbl_blacklists', 'label' => "RBL blacklists", 'rules' => 'trim'),
            array('field' => 'lien_desabo', 'label' => "Lien desabo", 'rules' => 'trim'),
            array('field' => 'provider_id', 'label' => "Provider", 'rules' => 'trim'),
            array('field' => 'q_day', 'label' => "Q day", 'rules' => 'trim'),
            array('field' => 'q_hour', 'label' => "Q hour", 'rules' => 'trim'),
            array('field' => 'database_used', 'label' => "Database", 'rules' => 'trim'),
            array('field' => 'test_mail_used', 'label' => "Test mail used", 'rules' => 'trim'),
            array('field' => 'deliver_before_send', 'label' => "Deliver before sending", 'rules' => 'trim'),
            array('field' => 'deliver_after_send', 'label' => "Deliver after sending", 'rules' => 'trim'),
            array('field' => 'number_simultaneaous_mails', 'label' => "Number simultaneaous mails", 'rules' => 'trim'),
            array('field' => 'email_subject_changed', 'label' => "E-mail subject changed every x mails", 'rules' => 'trim'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $valeurs = array(
                'message_id'                 => $this->input->post('message_id'),
                'software'              	 => $this->input->post('software'),
                'domain_used_send'           => $this->input->post('domain_used_send'),
                'domain_check_before_send'   => $this->input->post('domain_check_before_send'),
                'domain_check_after_send'    => $this->input->post('domain_check_after_send'),
                'rbl_blacklists'             => $this->input->post('rbl_blacklists'),
                'lien_desabo'                => $this->input->post('lien_desabo'),
                'provider_id'                => $this->input->post('provider_id'),
                'q_day'                      => $this->input->post('q_day'),
                'q_hour'                     => $this->input->post('q_hour'),
                'database_used'              => $this->input->post('database_used'),
                'test_mail_used'             => $this->input->post('test_mail_used'),
                'deliver_before_send'        => $this->input->post('deliver_before_send'),
                'deliver_after_send'         => $this->input->post('deliver_after_send'),
                'number_simultaneaous_mails' => $this->input->post('number_simultaneaous_mails'),
                'email_subject_changed' 	 => $this->input->post('email_subject_changed'),
            );
            /*
            if (!isset($valeurs['ctc_fournisseur'])) {
            $valeurs['ctc_fournisseur'] = 0;
            }
             */
            //debug($valeurs,1);
            $resultat = $this->m_tests_followup->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Tests a été enregistré avec succès", 'info', $ajaxData);
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
            $valeurs                             = new stdClass();
            $listes_valeurs                      = new stdClass();
            $valeurs->message_id                 = $this->input->post('message_id');
            $valeurs->software              	 = $this->input->post('software');
            $valeurs->domain_used_send           = $this->input->post('domain_used_send');
            $valeurs->domain_check_before_send   = $this->input->post('domain_check_before_send');
            $valeurs->domain_check_after_send    = $this->input->post('domain_check_after_send');
            $valeurs->rbl_blacklists             = $this->input->post('rbl_blacklists');
            $valeurs->lien_desabo                = $this->input->post('lien_desabo');
            $valeurs->provider_id                = $this->input->post('provider_id');
            $valeurs->q_day                      = $this->input->post('q_day');
            $valeurs->q_hour                     = $this->input->post('q_hour');
            $valeurs->database_used              = $this->input->post('database_used');
            $valeurs->test_mail_used             = $this->input->post('test_mail_used');
            $valeurs->deliver_before_send        = $this->input->post('deliver_before_send');
            $valeurs->deliver_after_send         = $this->input->post('deliver_after_send');
            $valeurs->number_simultaneaous_mails = $this->input->post('number_simultaneaous_mails');
            $valeurs->email_subject_changed 	 = $this->input->post('email_subject_changed');

            $listes_valeurs->message_id               = $this->m_tests_followup->liste_messages();
            $listes_valeurs->software                 = $this->m_softwares->liste_option();
            $listes_valeurs->provider_id              = $this->m_tests_followup->liste_providers();
            $listes_valeurs->test_mail_used           = $this->m_tests_followup->liste_mails();
            $listes_valeurs->domain_check_before_send = $this->m_tests_followup->liste_check();
            $listes_valeurs->domain_check_after_send  = $this->m_tests_followup->liste_check();
            $listes_valeurs->lien_desabo              = $this->m_tests_followup->liste_lien();
            $listes_valeurs->deliver_before_send      = $this->m_tests_followup->liste_deliverance();
            $listes_valeurs->deliver_after_send       = $this->m_tests_followup->liste_deliverance();

            $descripteur = array(
                'champs'  => $this->m_tests_followup->get_champs('write'),
                'onglets' => array(
                    array("Generale", array('message_id', 'software', 'domain_used_send', 'domain_check_before_send', 'domain_check_after_send', 'rbl_blacklists', 'lien_desabo', 'provider_id', 'q_day', 'q_hour', 'database_used', 'test_mail_used', 'deliver_before_send', 'deliver_after_send', 'number_simultaneaous_mails','email_subject_changed')),
                ),
            );
            $data = array(
                'title'          => "Nouveau Tests Followup",
                'page'           => "templates/form",
                'menu'           => "Extra|Nouveau Tests Followup",
                'barre_action'   => $this->barre_action["Followup"],
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'tests_followup',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Mise àjour
     * support AJAX
     ******************************/
    public function modification($id = 0, $ajax = false)
    {
        $this->load->model('m_softwares');
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        $config = array(
            array('field' => 'message_id', 'label' => "Message", 'rules' => 'trim'),
            array('field' => 'software', 'label' => "Software", 'rules' => 'trim'),
            array('field' => 'domain_used_send', 'label' => "Domain used sending", 'rules' => 'trim'),
            array('field' => 'domain_check_before_send', 'label' => "Domain check before sending", 'rules' => 'trim'),
            array('field' => 'domain_check_after_send', 'label' => "Domain check after sending", 'rules' => 'trim'),
            array('field' => 'rbl_blacklists', 'label' => "RBL blacklists", 'rules' => 'trim'),
            array('field' => 'lien_desabo', 'label' => "Lien desabo", 'rules' => 'trim'),
            array('field' => 'provider_id', 'label' => "Provider", 'rules' => 'trim'),
            array('field' => 'q_day', 'label' => "Q day", 'rules' => 'trim'),
            array('field' => 'q_hour', 'label' => "Q hour", 'rules' => 'trim'),
            array('field' => 'database_used', 'label' => "Database", 'rules' => 'trim'),
            array('field' => 'test_mail_used', 'label' => "Test mail used", 'rules' => 'trim'),
            array('field' => 'deliver_before_send', 'label' => "Deliver before sending", 'rules' => 'trim'),
            array('field' => 'deliver_after_send', 'label' => "Deliver after sending", 'rules' => 'trim'),
            array('field' => 'number_simultaneaous_mails', 'label' => "Number simultaneaous mails", 'rules' => 'trim'),
            array('field' => 'email_subject_changed', 'label' => "E-mail subject changed every x mails", 'rules' => 'trim'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $valeurs = array(
                'message_id'                 => $this->input->post('message_id'),
                'software'                   => $this->input->post('software'),
                'domain_used_send'           => $this->input->post('domain_used_send'),
                'domain_check_before_send'   => $this->input->post('domain_check_before_send'),
                'domain_check_after_send'    => $this->input->post('domain_check_after_send'),
                'rbl_blacklists'             => $this->input->post('rbl_blacklists'),
                'lien_desabo'                => $this->input->post('lien_desabo'),
                'provider_id'                => $this->input->post('provider_id'),
                'q_day'                      => $this->input->post('q_day'),
                'q_hour'                     => $this->input->post('q_hour'),
                'database_used'              => $this->input->post('database_used'),
                'test_mail_used'             => $this->input->post('test_mail_used'),
                'deliver_before_send'        => $this->input->post('deliver_before_send'),
                'deliver_after_send'         => $this->input->post('deliver_after_send'),
                'number_simultaneaous_mails' => $this->input->post('number_simultaneaous_mails'),
                'email_subject_changed' 	 => $this->input->post('email_subject_changed'),
            );

            $resultat = $this->m_tests_followup->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Tests Followup a été modifiée";
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
            $redirection = 'tests_followup/detail/' . $id;
            redirect($redirection);
        } else {
            $valeurs        = $this->m_tests_followup->detail($id);
            $listes_valeurs = new stdClass();

            $valeur = $this->input->post('message_id');
            if (isset($valeur)) {
                $valeurs->message_id = $valeur;
            }
            $valeur = $this->input->post('software');
            if (isset($valeur)) {
                $valeurs->software = $valeur;
            }
            $valeur = $this->input->post('domain_used_send');
            if (isset($valeur)) {
                $valeurs->domain_used_send = $valeur;
            }
            $valeur = $this->input->post('domain_check_before_send');
            if (isset($valeur)) {
                $valeurs->domain_check_before_send = $valeur;
            }
            $valeur = $this->input->post('domain_check_after_send');
            if (isset($valeur)) {
                $valeurs->domain_check_after_send = $valeur;
            }
            $valeur = $this->input->post('rbl_blacklists');
            if (isset($valeur)) {
                $valeurs->rbl_blacklists = $valeur;
            }
            $valeur = $this->input->post('lien_desabo');
            if (isset($valeur)) {
                $valeurs->lien_desabo = $valeur;
            }
            $valeur = $this->input->post('provider_id');
            if (isset($valeur)) {
                $valeurs->provider_id = $valeur;
            }
            $valeur = $this->input->post('q_day');
            if (isset($valeur)) {
                $valeurs->q_day = $valeur;
            }
            $valeur = $this->input->post('q_hour');
            if (isset($valeur)) {
                $valeurs->q_hour = $valeur;
            }
            $valeur = $this->input->post('database_used');
            if (isset($valeur)) {
                $valeurs->database = $valeur;
            }
            $valeur = $this->input->post('test_mail_used');
            if (isset($valeur)) {
                $valeurs->test_mail_used = $valeur;
            }
            $valeur = $this->input->post('deliver_before_send');
            if (isset($valeur)) {
                $valeurs->deliver_before_send = $valeur;
            }
            $valeur = $this->input->post('deliver_after_send');
            if (isset($valeur)) {
                $valeurs->deliver_after_send = $valeur;
            }
            $valeur = $this->input->post('number_simultaneaous_mails');
            if (isset($valeur)) {
                $valeurs->number_simultaneaous_mails = $valeur;
            }

            $valeur = $this->input->post('email_subject_changed');
            if (isset($valeur)) {
                $valeurs->email_subject_changed = $valeur;
            }

            $listes_valeurs->message_id               = $this->m_tests_followup->liste_messages();
            $listes_valeurs->software                 = $this->m_softwares->liste_option();
            $listes_valeurs->provider_id              = $this->m_tests_followup->liste_providers();
            $listes_valeurs->test_mail_used           = $this->m_tests_followup->liste_mails();
            $listes_valeurs->domain_check_before_send = $this->m_tests_followup->liste_check();
            $listes_valeurs->domain_check_after_send  = $this->m_tests_followup->liste_check();
            $listes_valeurs->lien_desabo              = $this->m_tests_followup->liste_lien();
            $listes_valeurs->deliver_before_send      = $this->m_tests_followup->liste_deliverance();
            $listes_valeurs->deliver_after_send       = $this->m_tests_followup->liste_deliverance();

            $scripts = array();

            $descripteur = array(
                'champs'  => $this->m_tests_followup->get_champs('write'),
                'onglets' => array(
                    array("Generale", array('message_id', 'software', 'domain_used_send', 'domain_check_before_send', 'domain_check_after_send', 'rbl_blacklists', 'lien_desabo', 'provider_id', 'q_day', 'q_hour', 'database_used', 'test_mail_used', 'deliver_before_send', 'deliver_after_send','number_simultaneaous_mails','email_subject_changed')),
                ),
            );

            $data = array(
                'title'          => "Modifier Carte",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Tests Followup",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'tests_followup',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Détail
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
            $valeurs = $this->m_tests_followup->detail($id);
            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'message_id'               => array("Message", 'VARCHAR 100', 'text', 'message_name'),
                    'software'                 => array("Software", 'VARCHAR 100', 'text', 'software'),
                    'domain_used_send'         => array("Domain used sending", 'VARCHAR 100', 'text', 'domain_used_send'),
                    'domain_check_before_send' => array("Domain check before sending", 'VARCHAR 15', 'text', 'domain_check_before_send'),
                    'domain_check_after_send'  => array("Domain check after sending", 'VARCHAR 15', 'text', 'domain_check_after_send'),
                    'rbl_blacklists'           => array("Rbl Blacklists", 'VARCHAR 100', 'text', 'rbl_blacklists'),
                    'lien_desabo'              => array("Lien Desabo", 'VARCHAR 3', 'text', 'lien_desabo'),
                    'provider_id'              => array("Provider", 'VARCHAR 100', 'text', 'provider_name'),
                    'q_day'                    => array("Q day", 'VARCHAR 50', 'text', 'q_day'),
                    'q_hour'                   => array("Q hour", 'VARCHAR 50', 'text', 'q_hour'),
                    'database_used'            => array("Database", 'VARCHAR 100', 'text', 'database_used'),
                    'test_mail_used'           => array("Test mail used", 'VARCHAR 100', 'text', 'mail_name'),
                    'deliver_before_send'      => array("Deliverance before sending", 'VARCHAR 15', 'text', 'deliver_before_send'),
                    'deliver_after_send'       => array("Deliverance before sending", 'VARCHAR 15', 'text', 'deliver_after_send'),
                    'number_simultaneaous_mails'  => array("Number simultaneaous mails", 'VARCHAR 15', 'text', 'number_simultaneaous_mails'),
                    'email_subject_changed'       => array("E-mail subject changed every x mails", 'VARCHAR 15', 'text', 'email_subject_changed'),
                ),
                'onglets' => array(
                    array("Generale", array('message_id', 'software', 'domain_used_send', 'domain_check_before_send', 'domain_check_after_send', 'rbl_blacklists', 'lien_desabo', 'provider_id', 'q_day', 'q_hour', 'database_used', 'test_mail_used', 'deliver_before_send', 'deliver_after_send','number_simultaneaous_mails','email_subject_changed')),
                ),
            );

            $data = array(
                'title'        => "Détail",
                'page'         => "templates/detail",
                'menu'         => "Extra|Tests Followup",
                'barre_action' => $this->barre_action["Followup"],
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'tests_followup',
                'methode'      => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
                'id_parent'    => 'followup_id',
            );
            $this->my_set_display_response($ajax, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = 'tests_followup/detail/'.$id;
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_tests_followup->archive($id);
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
              $this->my_set_action_response($ajax, true, "Tests followup a été archive", 'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Suppression
     * support AJAX
     ******************************/
    public function suppression($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_tests_followup->remove($id);

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
            $this->my_set_action_response($ajax, true, "Tests Followup a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Dupliquer Data
     ******************************/
    public function dupliquer($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = 'tests_followup/detail/'.$id;
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_tests_followup->dupliquer($id);
        if ($resultat === false) {
           $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                 'event' => array(
                     'controleur' => $this->my_controleur_from_class(__CLASS__),
                     'type'       => 'recordadd',
                     'id'         => $resultat,
                     'timeStamp'  => round(microtime(true) * 1000),
                     'redirect'   => $redirection,
                 ),
             );
              $this->my_set_action_response($ajax, true, "Test followup a été dupliquer", 'info',$ajaxData);
            if ($ajax) {
            return;
        }
            redirect($redirection);
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_tests_followup->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_tests_followup->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_tests_followup->unremove($id);
        }
    }
}

// EOF
