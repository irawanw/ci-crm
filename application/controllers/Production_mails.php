<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Production_mails extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        /*array(
            "Nouveau" => array('production_mails/nouveau', 'plus', true, 'production_mails_nouveau'),
        ),
        array(
            //"Consulter" => array('production_mails/detail', 'eye-open', false, 'production_mails_detail'),
            "Consulter/Modifier"  => array('production_mails/modification', 'pencil', false, 'production_mails_modification'),
            "Supprimer" => array('#', 'trash', false, 'production_mails_supprimer'),
        ),*/
        array(
            "Nouveau" => array('production_mails/nouveau', 'plus', true, 'production_mails_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('production_mails/modification', 'pencil', false, 'production_mails_modification', null, array('form')),
            "Supprimer"          => array('production_mails/remove', 'trash', false, 'production_mails_supprimer', "Veuillez confirmer la suppression de cette production mails", array('confirm-modify' => array('production_mails/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
		),
		array(
            "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"    => array('#', 'book', true, 'export_pdf'),
			"Imprimer"		=> array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_production_mails');
    }

    
    /******************************
     * List of production_mails Data
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
            // array("Ajouter un e-mailing pages jaunes","production_mails/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'production_mails/index',
            'detail'             => array('production_mails/detail', 'production_mails_id', 'description'),
            'archive'            => array('production_mails/archive', 'production_mails_id', 'archive'),
            'champs'             => $this->m_production_mails->get_champs('read'),
            'filterable_columns' => $this->m_production_mails->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: production_mails/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'production_mails/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'production_mails/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'production_mails/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'production_mails',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);

        $scripts[] = $this->load->view("production_mails/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('production_mails', $this->session->id);
        $data = array(
            'title'        => "Mails de production",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Production Mails",
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_production_mails->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'production_mails_id' => 't_production_mails',
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

                $resultat = $this->m_production_mails->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_production_mails->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_production_mails->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Production_mails'
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
    public function nouveau($id=0,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'mail', 'label' => "Mail", 'rules' => 'trim|required|valid_email'),
            array('field' => 'domain', 'label' => "Domain", 'rules' => 'trim|valid_domain'),
            array('field' => 'login_url', 'label' => "Login Url", 'rules' => 'trim|valid_url'),
            array('field' => 'login', 'label' => "Login", 'rules' => 'trim'),
            array('field' => 'pass', 'label' => "Pass", 'rules' => 'trim'),
            array('field' => 'provider', 'label' => "Provider", 'rules' => 'trim'),   
            array('field' => 'commentaries', 'label' => "Commentaries", 'rules' => 'trim'),
            array('field' => 'status', 'label' => "Status", 'rules' => 'trim'),
			array('field' => 'blacklist', 'label' => "Blacklist", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            // validation réussie
            $valeurs = array(
                'mail'            => $this->input->post('mail'),
                'domain'          => $this->input->post('domain'),
                'login_url'       => $this->input->post('login_url'),
                'login'           => $this->input->post('login'),
                'pass'            => $this->input->post('pass'),
                'provider'        => $this->input->post('provider'),
                // 'abuse_email'     => $this->input->post('abuse_email'),
                // 'abuse_telephone' => $this->input->post('abuse_telephone'),
                // 'abuse_url'       => $this->input->post('abuse_url'),                
                'commentaries'    => $this->input->post('commentaries'),
                'used_for'        => $this->input->post('used_for'),
                'status'          => $this->input->post('status'),
               // 'blacklist'       => implode(",", $this->input->post('blacklist')),
			    'blacklist'       => $this->input->post('blacklist'),
            );



            $id = $this->m_production_mails->nouveau($valeurs);
            /*if ($id === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            } else {
                $this->session->set_flashdata('success', "Test mails a été enregistré avec succès");
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            }*/
            if ($id === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );              
                $this->my_set_action_response($ajax, true, "Test mails a été enregistré avec succès",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection); 
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                  = new stdClass();
            $listes_valeurs           = new stdClass();
            $valeurs->mail            = $this->input->post('mail');
            $valeurs->domain          = $this->input->post('domain');
            $valeurs->login_url       = $this->input->post('login_url');
            $valeurs->login           = $this->input->post('login');
            $valeurs->pass            = $this->input->post('pass');
            $valeurs->provider        = $this->input->post('provider');
            $valeurs->abuse_email     = $this->input->post('abuse_email');
            $valeurs->abuse_telephone = $this->input->post('abuse_telephone');
            $valeurs->abuse_url       = $this->input->post('abuse_url');            
            $valeurs->commentaries    = $this->input->post('commentaries');
            $valeurs->used_for        = $this->input->post('used_for');
            $valeurs->status          = $this->input->post('status');
            $valeurs->blacklist       = $this->input->post('blacklist');

            $listes_valeurs->provider        = $this->m_production_mails->liste_providers();
            $listes_valeurs->used_for        = $this->m_production_mails->used_for_option();
            $listes_valeurs->status          = $this->m_production_mails->status_option();
            $listes_valeurs->blacklist       = $this->m_production_mails->liste_blacklist();
            $scripts                         = array();
            //$scripts[]                       = $this->load->view("production_mails/form-js",array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_production_mails->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Production Mails",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Production Mails",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'production_mails',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
     ******************************/
    public function detail($id,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_production_mails->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'production_mails/modification', 'primary'),
                array("Archiver", 'production_mails/archive', 'warning'),
                array("Supprimer", 'production_mails/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'mail'            => array("Mail", 'VARCHAR 50', 'text', 'mail'),
                    'domain'          => array("Domain", 'VARCHAR 50', 'text', 'domain'),
                    'login_url'       => array("Login Url", 'VARCHAR 50', 'text', 'login_url'),
                    'login'           => array("Login", 'VARCHAR 50', 'text', 'login'),
                    'pass'            => array("Pass", 'VARCHAR 50', 'text', 'pass'),
                    'provider_name'   => array("Provider", 'VARCHAR 50', 'text', 'provider_name'),
                    'abuse_email'     => array("Abuse Email", 'VARCHAR 50', 'text', 'provider_abuse_email'),
                    'abuse_telephone' => array("Abuse Telephone", 'VARCHAR 50', 'text', 'provider_abuse_telephone'),
                    'abuse_url'       => array("Abuse Url", 'VARCHAR 50', 'text', 'provider_abuse_url'),
                    'commentaries'    => array("Commentaries", 'VARCHAR 50', 'text', 'commentaries'),
                    'status'          => array("Status", 'VARCHAR 50', 'text', 'status'),
                    'blacklist'       => array("Blacklist", 'VARCHAR 50', 'text', 'blacklist'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Production Mail",
                'page'         => "templates/detail",
                'menu'         => "Extra|Production Mails",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'production_mails',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'mail', 'label' => "Mail", 'rules' => 'trim|required|valid_email'),
            array('field' => 'domain', 'label' => "Domain", 'rules' => 'trim|valid_domain'),
            array('field' => 'login_url', 'label' => "Login Url", 'rules' => 'trim|valid_url'),
            array('field' => 'login', 'label' => "Login", 'rules' => 'trim'),
            array('field' => 'pass', 'label' => "Pass", 'rules' => 'trim'),
            array('field' => 'provider', 'label' => "Provider", 'rules' => 'trim'),   
            array('field' => 'commentaries', 'label' => "Commentaries", 'rules' => 'trim'),
            array('field' => 'status', 'label' => "Status", 'rules' => 'trim'),
			array('field' => 'blacklist', 'label' => "Blacklist", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'mail'            => $this->input->post('mail'),
                'domain'          => $this->input->post('domain'),
                'login_url'       => $this->input->post('login_url'),
                'login'           => $this->input->post('login'),
                'pass'            => $this->input->post('pass'),
                'provider'        => $this->input->post('provider'),
                // 'abuse_email'     => $this->input->post('abuse_email'),
                // 'abuse_telephone' => $this->input->post('abuse_telephone'),
                // 'abuse_url'       => $this->input->post('abuse_url'),               
                'commentaries'    => $this->input->post('commentaries'),
                'used_for'        => $this->input->post('used_for'),
                'status'          => $this->input->post('status'),
                //'blacklist'       => implode(",", $this->input->post('blacklist')),
                'blacklist'       => $this->input->post('blacklist'),
            );

            $resultat = $this->m_production_mails->maj($valeurs, $id);
            /*if ($resultat === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('production_mails/detail/' . $id);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Test Mails a été modifié";
                }
                $this->session->set_flashdata('success', $message);
                redirect('production_mails');
            }*/
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Test Mails a été modifiée";
                    $ajaxData = array(
                       'event' => array(
                           'controleur' => $this->my_controleur_from_class(__CLASS__),
                           'type' => 'recordchange',
                           'id' => $id,
                           'timeStamp' => round(microtime(true) * 1000),
                           ),
                       );
                }
                $this->my_set_action_response($ajax,true,$message,'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'production_mails/detail/'.$id;
            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                         = $this->m_production_mails->detail($id);
            $listes_valeurs                  = new stdClass();
            $provider                        = $valeurs->provider ? $valeurs->provider : null;
            $listes_valeurs->provider        = $this->m_production_mails->liste_providers();
            $listes_valeurs->used_for        = $this->m_production_mails->used_for_option();
            $listes_valeurs->status          = $this->m_production_mails->status_option();
            //$listes_valeurs->blacklist       = $this->m_production_mails->liste_providers();
			$listes_valeurs->blacklist       = $this->m_production_mails->liste_blacklist();

            $scripts   = array();
            //$scripts[] = $this->load->view("production_mails/form-js",
                //array('valeurs' => $valeurs, 'liste_blacklist' => $this->m_production_mails->liste_providers()), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_production_mails->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier Production Mail",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Production Mails",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'production_mails',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        $resultat = $this->m_production_mails->archive($id);
        if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Test Mails a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Test Mails Data
     ******************************/
    public function remove($id,$ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_production_mails->remove($id);
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
            $this->session->set_flashdata('success', "Test Mails a été supprimé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }*/
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax,true,"Test Mails a été supprimée", 'info', $ajaxData);
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
            $resultat = $this->m_production_mails->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_production_mails->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_production_mails->unremove($id);
        }
    }

    public function get_provider_detail($id)
    {
        $detail = $this->m_production_mails->get_provider_detail($id);
        echo json_encode($detail);
    }

    public function test($id)
    {
        echo "<pre>";
        print_r($this->m_production_mails->detail($id));
        echo "<br>";
        echo "</pre>";
        
    }
}
// EOF
