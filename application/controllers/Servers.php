<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Servers extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Servers" => array(
            array(
                "Nouveau" => array('servers/nouveau', 'plus', true, 'servers_nouveau', null, array('form')),
            ),
            array(
                "Consulter/Modifier" => array('servers/modification', 'pencil', false, 'servers_modification', null, array('form')),
				"Archiver" 			 => array('servers/archive', 'folder-close', false, 'servers_archiver', "Veuillez confirmer la archive du Server",array('confirm-modify'=> array('servers/index'))),
                "Supprimer"          => array('servers/remove', 'trash', false, 'servers_supprimer', "Veuillez confirmer la suppression du server", array('confirm-delete' => array('servers/index'))),
            ),
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
            ),
            array(
                "Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"  => array('#', 'book', true, 'export_pdf'),
                "Imprimer"    => array('#', 'print', true, 'print_list'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_servers');
    }

    
    /******************************
     * List of servers Data
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
            // array("Ajouter un e-mailing pages jaunes","servers/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        $menu_extra = array(
            array(
                'name' => "Serveurs",
                'url' => site_url('servers')
            ),
            array(
                'name' => "Hebergeur",
                'url' => site_url('hosts')
            ),
            array(
                'name' => "Propriétaire",
                'url' => site_url('owners')
            ),
            array(
                'name' => "Domains",
                'url' => site_url('domains')
            ),
            array(
                'name' => "IPS",
                'url' => site_url('ips')
            ),
            array(
                'name' => "Cartes Bleues",
                'url' => site_url('cartes_blues')
            ),
        );

        // descripteur
        $descripteur = array(
            'datasource'         => 'servers/index',
            'detail'             => array('servers/detail', 'server_id', 'description'),
            'archive'            => array('servers/archive', 'server_id', 'archive'),
            'champs'             => $this->m_servers->get_champs('read'),
            'filterable_columns' => $this->m_servers->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: servers/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'servers/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'servers/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'servers/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'servers',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => null,
                'external_toolbar_data' => array(
                    'controleur' => 'servers',
                ),
            ), true);
        $scripts[] = $this->load->view("servers/liste-js", array(), true);
        $scripts[] = $this->load->view('servers/form-js', array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('servers', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Serveurs",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Servers",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action["Servers"],
            'menu_extra'   => $menu_extra,
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
            $resultat = $this->m_servers->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'server_id' => 't_servers',
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

                $resultat = $this->m_servers->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_servers->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_servers->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Servers'
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
    public function nouveau($serverid=0, $ajax = false)
    {
        $this->load->model(array('m_hosts', 'm_owners', 'm_domains', 'm_ips', 'm_utilisateurs','m_cartes_blues'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'host', 'label' => "Hébergeur", 'rules' => 'trim'),
            array('field' => 'nom_interne', 'label' => "Nom Interne", 'rules' => 'trim'),
            array('field' => 'numero_de_client', 'label' => "Numéro de client ou compte", 'rules' => 'trim'),
            array('field' => 'owner', 'label' => "Propriétaire", 'rules' => 'trim'),
            array('field' => 'contrat', 'label' => "Contrat", 'rules' => 'trim'),
            array('field' => 'options', 'label' => "Options", 'rules' => 'trim'),
            array('field' => 'system_exploration', 'label' => "Système d'exploitation", 'rules' => 'trim'),
            array('field' => 'type_serveur', 'label' => "Type Serveur", 'rules' => 'trim'),
            array('field' => 'utilisation', 'label' => "Utilisation"),
            array('field' => 'remarques', 'label' => "Remarques", 'rules' => 'trim'),

            array('field' => 'acces_compte_client_url', 'label' => "compte client url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_compte_client_login', 'label' => "compte client login", 'rules' => 'trim'),
            array('field' => 'acces_compte_client_pass', 'label' => "compte client pass", 'rules' => 'trim'),
            array('field' => 'acces_compte_client_utilisateurs', 'label' => "compte client utilisateurs", 'rules' => 'trim'),

            array('field' => 'acces_plesk_url', 'label' => "plesk Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_plesk_login', 'label' => "plesk Login", 'rules' => 'trim'),
            array('field' => 'acces_plesk_pass', 'label' => "plesk Pass", 'rules' => 'trim'),
            array('field' => 'acces_plesk_utilisateurs', 'label' => "plesk utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'acces_contrat_url', 'label' => "contrat Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_contrat_login', 'label' => "contrat Login", 'rules' => 'trim'),
            array('field' => 'acces_contrat_pass', 'label' => "contrat Pass", 'rules' => 'trim'),
            array('field' => 'acces_contrat_utilisateurs', 'label' => "contrat utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'acces_root_url', 'label' => "root Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_root_login', 'label' => "root Login", 'rules' => 'trim'),
            array('field' => 'acces_root_pass', 'label' => "root Pass", 'rules' => 'trim'),
            array('field' => 'acces_root_utilisateurs', 'label' => "root utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'prix', 'label' => "Prix", 'rules' => 'trim'),
            array('field' => 'type_de_paiement', 'label' => "Type de paiement", 'rules' => 'trim'),
            array('field' => 'echeance_du_paiement', 'label' => "Echéance du paiement", 'rules' => 'trim'),
            array('field' => 'date_de_resiliation', 'label' => "Date de résiliation", 'rules' => 'trim'),
            array('field' => 'moyen_de_paiement', 'label' => "Moyen De Paiement", 'rules' => 'trim'),
            array('field' => 'compte_paypal_utilise', 'label' => "Compte paypal utilisé", 'rules' => 'trim'),
            array('field' => 'cb_utilsée', 'label' => "CB Utilsée", 'rules' => 'trim'),
            array('field' => 'ips', 'label' => "Ips"),
            array('field' => 'domaines', 'label' => "Domaines"),
            array('field' => 'ajouter_des_sites_hébergés', 'label' => "Ajouter des Sites Hébergés ", 'rules' => 'trim'),
            array('field' => 'eta_du_serveur', 'label' => "Eta du Serveur", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) 
		{
			$svrid = $this->input->post('svrid');
            $ips_array = $this->input->post('ips', true) ? $this->input->post('ips', true) : array();
            $ips = implode(",", $ips_array);
            $domaines_array = $this->input->post('domaines', true) ? $this->input->post('domaines', true) : array();
            $domaines = implode(",", $domaines_array);
            $utilisation_array = $this->input->post('utilisation', true) ? $this->input->post('utilisation', true) : array();
            $utilisation = implode(",", $utilisation_array);

            // validation réussie
            $valeurs = array(
				'host'                             => $this->input->post('host'),
                'nom_interne'                      => $this->input->post('nom_interne'),
                'numero_de_client'                 => $this->input->post('numero_de_client'),
                'owner'                            => $this->input->post('owner'),
                'contrat'                          => $this->input->post('contrat'),
                'options'                          => $this->input->post('options'),
                'system_exploration'               => $this->input->post('system_exploration'),
                'type_serveur'                     => $this->input->post('type_serveur'),
                'utilisation'                      => $utilisation,
                'remarques'                        => $this->input->post('remarques'),

                'acces_compte_client_url'          => $this->input->post('acces_compte_client_url'),
                'acces_compte_client_login'        => $this->input->post('acces_compte_client_login'),
                'acces_compte_client_pass'         => $this->input->post('acces_compte_client_pass'),
                'acces_compte_client_utilisateurs' => $this->input->post('acces_compte_client_utilisateurs'),

                'acces_plesk_url'                  => $this->input->post('acces_plesk_url'),
                'acces_plesk_login'                => $this->input->post('acces_plesk_login'),
                'acces_plesk_pass'                 => $this->input->post('acces_plesk_pass'),
                'acces_plesk_utilisateurs'         => $this->input->post('acces_plesk_utilisateurs'),

                'acces_contrat_url'                => $this->input->post('acces_contrat_url'),
                'acces_contrat_login'              => $this->input->post('acces_contrat_login'),
                'acces_contrat_pass'               => $this->input->post('acces_contrat_pass'),
                'acces_contrat_utilisateurs'       => $this->input->post('acces_contrat_utilisateurs'),

                'acces_root_url'                   => $this->input->post('acces_root_url'),
                'acces_root_login'                 => $this->input->post('acces_root_login'),
                'acces_root_pass'                  => $this->input->post('acces_root_pass'),
                'acces_root_utilisateurs'          => $this->input->post('acces_root_utilisateurs'),

                'prix'                             => $this->input->post('prix'),
                'type_de_paiement'                 => $this->input->post('type_de_paiement'),
                'echeance_du_paiement'             => formatte_date_to_bd($this->input->post('echeance_du_paiement')),
                'date_de_resiliation'              => formatte_date_to_bd($this->input->post('date_de_resiliation')),
				'pas_engage'					   => $this->input->post('pas_engage') == "" ? 0 : $this->input->post('pas_engage') == "",
                'moyen_de_paiement'                => $this->input->post('moyen_de_paiement'),
                'compte_paypal_utilise'            => $this->input->post('compte_paypal_utilise'),
                'cb_utilsée'                       => $this->input->post('cb_utilsée'),
                'ips'                              => $ips,
                'domaines'                         => $domaines,
                'ajouter_des_sites_hébergés'       => $this->input->post('ajouter_des_sites_hébergés'),
                'eta_du_serveur'                   => $this->input->post('eta_du_serveur'),
            );
			
			if($svrid == '')
			{
				$resultat = $this->m_servers->nouveau($valeurs);
			}
			else
			{
				$resultat = $this->m_servers->maj($valeurs,$svrid);
			}
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
				$this->my_set_action_response($ajax, true, "Serveur a été enregistré avec succès",'info',$ajaxData);			
			}
			
			if ($ajax) {
				return;
			}

			$redirection = $this->session->userdata('_url_retour');
			if (! $redirection) $redirection = '';
			redirect($redirection); 
            
        } 
		else 
		{
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();
			$valeurs->svrid 			 = $this->input->post('svrid');
			$valeurs->host               = $this->input->post('host');
            $valeurs->nom_interne        = $this->input->post('nom_interne');
            $valeurs->numero_de_client   = $this->input->post('numero_de_client');
            $valeurs->owner              = $this->input->post('owner');
            $valeurs->contrat            = $this->input->post('contrat');
            $valeurs->options            = $this->input->post('options');
            $valeurs->system_exploration = $this->input->post('system_exploration');
            $valeurs->type_serveur       = $this->input->post('type_serveur');
            $valeurs->utilisation        = $this->input->post('utilisation');
            $valeurs->remarques          = $this->input->post('remarques');

            $valeurs->acces_compte_client_url          = $this->input->post('acces_compte_client_url');
            $valeurs->acces_compte_client_login        = $this->input->post('acces_compte_client_login');
            $valeurs->acces_compte_client_pass         = $this->input->post('acces_compte_client_pass');
            $valeurs->acces_compte_client_utilisateurs = $this->input->post('acces_compte_client_utilisateurs');

            $valeurs->acces_plesk_url          = $this->input->post('acces_plesk_url');
            $valeurs->acces_plesk_login        = $this->input->post('acces_plesk_login');
            $valeurs->acces_plesk_pass         = $this->input->post('acces_plesk_pass');
            $valeurs->acces_plesk_utilisateurs = $this->input->post('acces_plesk_utilisateurs');

            $valeurs->acces_contrat_url          = $this->input->post('acces_contrat_url');
            $valeurs->acces_contrat_login        = $this->input->post('acces_contrat_login');
            $valeurs->acces_contrat_pass         = $this->input->post('acces_contrat_pass');
            $valeurs->acces_contrat_utilisateurs = $this->input->post('acces_contrat_utilisateurs');

            $valeurs->acces_root_url          = $this->input->post('acces_root_url');
            $valeurs->acces_root_login        = $this->input->post('acces_root_login');
            $valeurs->acces_root_pass         = $this->input->post('acces_root_pass');
            $valeurs->acces_root_utilisateurs = $this->input->post('acces_root_utilisateurs');

            $valeurs->prix                 = $this->input->post('prix');
            $valeurs->type_de_paiement     = $this->input->post('type_de_paiement');
            $valeurs->echeance_du_paiement = $this->input->post('echeance_du_paiement');
            $valeurs->date_de_resiliation  = $this->input->post('date_de_resiliation');
            $valeurs->pas_engage           = $this->input->post('pas_engage');
			$valeurs->moyen_de_paiement    = $this->input->post('moyen_de_paiement');
            $valeurs->compte_paypal_utilise = $this->input->post('compte_paypal_utilise');
            $valeurs->cb_utilsée           = $this->input->post('cb_utilsée');
            $valeurs->ips                  = $this->input->post('ips');
            $valeurs->domaines             = $this->input->post('domaines');
            $valeurs->ajouter_des_sites_hébergés             = $this->input->post('ajouter_des_sites_hébergés');
            $valeurs->eta_du_serveur       = $this->input->post('eta_du_serveur');
			
            //liste options avoid show ajouter while referer from ips
			if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != 'http://svr.bonenvoi.com/crm-dev/index.php/ips')
			{
				$listes_valeurs->host                             = $this->m_hosts->liste_option(true);
				$listes_valeurs->owner                            = $this->m_owners->liste_option(true);
				$listes_valeurs->ips                              = $this->m_ips->liste_option(true);
                $listes_valeurs->domaines                         = $this->m_domains->liste_option(true);
				$listes_valeurs->cb_utilsée                     = $this->m_cartes_blues->liste_option(true);
			}
			else
			{
				$listes_valeurs->host                             = $this->m_hosts->liste_option(false);
				$listes_valeurs->owner                            = $this->m_owners->liste_option(false);
				$listes_valeurs->ips                              = $this->m_ips->liste_option(false);
				$listes_valeurs->domaines                         = $this->m_domains->liste_option(false);
                $listes_valeurs->cb_utilsée                     = $this->m_cartes_blues->liste_option(false);
			}
            $listes_valeurs->utilisation                      = $this->m_servers->utilisation_liste();
            $listes_valeurs->system_exploration               = $this->m_servers->system_exploration_liste_option();
            $listes_valeurs->moyen_de_paiement                = $this->m_servers->moyen_de_paiement_liste_option();
            $listes_valeurs->type_de_paiement                 = $this->m_servers->type_de_paiement_liste_option();        
            $listes_valeurs->eta_du_serveur                   = $this->m_servers->eta_du_serveur_liste_option();
            $listes_valeurs->acces_compte_client_utilisateurs = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_contrat_utilisateurs       = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_root_utilisateurs          = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_plesk_utilisateurs         = $this->m_utilisateurs->liste_option();

            $scripts   = array();


            // descripteur
            $descripteur = array(
                'champs'  => $this->m_servers->get_champs('write'),
                'onglets' => array(
                    array('Information générales', array(
						'svrid',
                        'host',
                        'nom_interne',
                        'owner',
                        'numero_de_client',
                        'contrat',
                        'options',
                        'system_exploration',
                        'type_serveur',
                        'utilisation',
                        'remarques',
                    )),
                    array('Liens et accès', array(
                        'acces_plesk_url',
                        'acces_plesk_login',
                        'acces_plesk_pass',
                        'acces_plesk_utilisateurs',

                        'acces_compte_client_url',
                        'acces_compte_client_login',
                        'acces_compte_client_pass',
                        'acces_compte_client_utilisateurs',

                        'acces_contrat_url',
                        'acces_contrat_login',
                        'acces_contrat_pass',
                        'acces_contrat_utilisateurs',

                        'acces_root_url',
                        'acces_root_login',
                        'acces_root_pass',
                        'acces_root_utilisateurs',
                    )),
                    array('Paiement', array(
                        'prix',
                        'type_de_paiement',
                        'echeance_du_paiement',
                        'date_de_resiliation',
						'pas_engage',
                        'moyen_de_paiement',
                        'cb_utilsée',
                        'compte_paypal_utilise'
                    )),
                    array('IPS Domaines Sites', array(
                        'ips',
                        'domaines',
                        'ajouter_des_sites_hébergés',
                    )),
                    array('État du serveur', array(
                        'eta_du_serveur',
                    )),
                ),
            );           

            $data = array(
                'title' => "Ajouter un nouveau Serveur",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Servers",
                'barre_action' => $this->barre_action["Servers"],
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'servers',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0, $ajax = false)
    {
        $this->load->model(array('m_hosts', 'm_owners', 'm_domains', 'm_ips', 'm_utilisateurs','m_cartes_blues'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');
		/*reset id*/
		$this->session->set_userdata('isSave',0);
        // règles de validation
        $config = array(
            array('field' => 'host', 'label' => "Hébergeur", 'rules' => 'trim|required'),
            array('field' => 'nom_interne', 'label' => "Nom Interne", 'rules' => 'trim'),
            array('field' => 'numero_de_client', 'label' => "Numéro de client ou compte", 'rules' => 'trim'),
            array('field' => 'owner', 'label' => "Propriétaire", 'rules' => 'trim'),
            array('field' => 'contrat', 'label' => "Contrat", 'rules' => 'trim'),
            array('field' => 'options', 'label' => "Options", 'rules' => 'trim'),
            array('field' => 'system_exploration', 'label' => "Système d'exploitation", 'rules' => 'trim'),
            array('field' => 'type_serveur', 'label' => "Type Serveur", 'rules' => 'trim'),
            array('field' => 'utilisation', 'label' => "Utilisation"),
            array('field' => 'remarques', 'label' => "Remarques", 'rules' => 'trim'),

            array('field' => 'acces_compte_client_url', 'label' => "compte client url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_compte_client_login', 'label' => "compte client login", 'rules' => 'trim'),
            array('field' => 'acces_compte_client_pass', 'label' => "compte client pass", 'rules' => 'trim'),
            array('field' => 'acces_compte_client_utilisateurs', 'label' => "compte client utilisateurs", 'rules' => 'trim'),

            array('field' => 'acces_plesk_url', 'label' => "plesk Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_plesk_login', 'label' => "plesk Login", 'rules' => 'trim'),
            array('field' => 'acces_plesk_pass', 'label' => "plesk Pass", 'rules' => 'trim'),
            array('field' => 'acces_plesk_utilisateurs', 'label' => "plesk utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'acces_contrat_url', 'label' => "contrat Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_contrat_login', 'label' => "contrat Login", 'rules' => 'trim'),
            array('field' => 'acces_contrat_pass', 'label' => "contrat Pass", 'rules' => 'trim'),
            array('field' => 'acces_contrat_utilisateurs', 'label' => "contrat utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'acces_root_url', 'label' => "root Url", 'rules' => 'trim|valid_url'),
            array('field' => 'acces_root_login', 'label' => "root Login", 'rules' => 'trim'),
            array('field' => 'acces_root_pass', 'label' => "root Pass", 'rules' => 'trim'),
            array('field' => 'acces_root_utilisateurs', 'label' => "root utilisateurs agrees ", 'rules' => 'trim'),

            array('field' => 'prix', 'label' => "Prix", 'rules' => 'trim'),
            array('field' => 'type_de_paiement', 'label' => "Type de paiement", 'rules' => 'trim'),
            array('field' => 'echeance_du_paiement', 'label' => "Echéance du paiement", 'rules' => 'trim'),
            array('field' => 'date_de_resiliation', 'label' => "Date de résiliation", 'rules' => 'trim'),
            array('field' => 'moyen_de_paiement', 'label' => "Moyen De Paiement", 'rules' => 'trim'),
            array('field' => 'compte_paypal_utilise', 'label' => "Compte paypal utilisé", 'rules' => 'trim'),
            array('field' => 'cb_utilsée', 'label' => "CB Utilsée", 'rules' => 'trim'),
            array('field' => 'ips', 'label' => "Ips", 'rules' => ''),
            array('field' => 'domaines', 'label' => "Domaines ", 'rules' => ''),
            array('field' => 'ajouter_des_sites_hébergés', 'label' => "Ajouter des Sites Hébergés", 'rules' => 'trim'),
            array('field' => 'eta_du_serveur', 'label' => "Eta du Serveur", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $ips_array = $this->input->post('ips', true) ? $this->input->post('ips', true) : array();
            $ips = implode(",", $ips_array);
            $domaines_array = $this->input->post('domaines', true) ? $this->input->post('domaines', true) : array();
            $domaines = implode(",", $domaines_array);
            $utilisation_array = $this->input->post('utilisation', true) ? $this->input->post('utilisation', true) : array();
            $utilisation = implode(",", $utilisation_array);
            // validation réussie
            $valeurs = array(
                'host'                             => $this->input->post('host'),
                'nom_interne'                      => $this->input->post('nom_interne'),
                'numero_de_client'                 => $this->input->post('numero_de_client'),
                'owner'                            => $this->input->post('owner'),
                'contrat'                          => $this->input->post('contrat'),
                'options'                          => $this->input->post('options'),
                'system_exploration'               => $this->input->post('system_exploration'),
                'type_serveur'                     => $this->input->post('type_serveur'),
                'utilisation'                      => $utilisation,
                'remarques'                        => $this->input->post('remarques'),

                'acces_compte_client_url'          => $this->input->post('acces_compte_client_url'),
                'acces_compte_client_login'        => $this->input->post('acces_compte_client_login'),
                'acces_compte_client_pass'         => $this->input->post('acces_compte_client_pass'),
                'acces_compte_client_utilisateurs' => $this->input->post('acces_compte_client_utilisateurs'),

                'acces_plesk_url'                  => $this->input->post('acces_plesk_url'),
                'acces_plesk_login'                => $this->input->post('acces_plesk_login'),
                'acces_plesk_pass'                 => $this->input->post('acces_plesk_pass'),
                'acces_plesk_utilisateurs'         => $this->input->post('acces_plesk_utilisateurs'),

                'acces_contrat_url'                => $this->input->post('acces_contrat_url'),
                'acces_contrat_login'              => $this->input->post('acces_contrat_login'),
                'acces_contrat_pass'               => $this->input->post('acces_contrat_pass'),
                'acces_contrat_utilisateurs'       => $this->input->post('acces_contrat_utilisateurs'),

                'acces_root_url'                   => $this->input->post('acces_root_url'),
                'acces_root_login'                 => $this->input->post('acces_root_login'),
                'acces_root_pass'                  => $this->input->post('acces_root_pass'),
                'acces_root_utilisateurs'          => $this->input->post('acces_root_utilisateurs'),

                'prix'                             => $this->input->post('prix'),
                'type_de_paiement'                 => $this->input->post('type_de_paiement'),
                'echeance_du_paiement'             => formatte_date_to_bd($this->input->post('echeance_du_paiement')),
                'date_de_resiliation'              => formatte_date_to_bd($this->input->post('date_de_resiliation')),
                'pas_engage'                	   => $this->input->post('pas_engage'),
				'moyen_de_paiement'                => $this->input->post('moyen_de_paiement'),
                'compte_paypal_utilise'            => $this->input->post('compte_paypal_utilise'),
                'cb_utilsée'                       => $this->input->post('cb_utilsée'),
                'ips'                              => $ips,
                'domaines'                         => $domaines,
                'ajouter_des_sites_hébergés'       => $this->input->post('ajouter_des_sites_hébergés'),
                'eta_du_serveur'                   => $this->input->post('eta_du_serveur'),
				
            );

            $resultat = $this->m_servers->maj($valeurs, $id);
            $redirection = 'servers/detail/'.$id;

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Serveur a été modifié";
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
            $valeurs = $this->m_servers->detail($id);
            //liste options
            $listes_valeurs                                   = new stdClass();
            $listes_valeurs->host                             = $this->m_hosts->liste_option(true);
            $listes_valeurs->owner                            = $this->m_owners->liste_option(true);
            $listes_valeurs->utilisation                      = $this->m_servers->utilisation_liste();
            $listes_valeurs->system_exploration               = $this->m_servers->system_exploration_liste_option();
            $listes_valeurs->moyen_de_paiement                = $this->m_servers->moyen_de_paiement_liste_option();
            $listes_valeurs->type_de_paiement                 = $this->m_servers->type_de_paiement_liste_option();
            $listes_valeurs->ips                              = $this->m_ips->liste_option(true);
            $listes_valeurs->cb_utilsée                     = $this->m_cartes_blues->liste_option(true);
            $listes_valeurs->domaines                         = $this->m_domains->liste_option(true);
            $listes_valeurs->eta_du_serveur                   = $this->m_servers->eta_du_serveur_liste_option();
            $listes_valeurs->acces_compte_client_utilisateurs = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_contrat_utilisateurs       = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_root_utilisateurs          = $this->m_utilisateurs->liste_option();
            $listes_valeurs->acces_plesk_utilisateurs         = $this->m_utilisateurs->liste_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'host'                             => array("Hébergeur", 'select', array('host', 'id', 'value'), false),
                    'nom_interne'                      => array("Nom Interne", 'text', 'nom_interne', false),
                    'numero_de_client'                 => array("Numéro de client ou compte", 'text', 'numero_de_client', false),
                    'owner'                            => array("Propriétaire", 'select', array('owner', 'id', 'value'), false),
                    'contrat'                          => array("Contrat", 'text', 'contrat', false),
                    'options'                          => array("Options", 'text', 'options', false),
                    'system_exploration'               => array("Système d'exploitation", 'select', array('system_exploration', 'id', 'value'), false),
                    'type_serveur'                     => array("Type Serveur", 'text', 'type_serveur', false),
                    'utilisation'                      => array("Utilisation", 'select-multiple', array('utilisation','id','value'), false),
                    'remarques'                        => array("Remarques", 'textarea', 'remarques', false),

                    'acces_plesk_url'                  => array("Url", 'text', 'acces_plesk_url', false),
                    'acces_plesk_login'                => array("Login", 'text', 'acces_plesk_login', false),
                    'acces_plesk_pass'                 => array("Pass", 'text', 'acces_plesk_pass', false),
                    'acces_plesk_utilisateurs'         => array("utilisateurs agrees ", 'select', array('acces_plesk_utilisateurs', 'utl_id', 'emp_nom'), false),

                    'acces_compte_client_url'          => array("Url", 'text', 'acces_compte_client_url', false),
                    'acces_compte_client_login'        => array("Login", 'text', 'acces_compte_client_login', false),
                    'acces_compte_client_pass'         => array("Pass", 'text', 'acces_compte_client_pass', false),
                    'acces_compte_client_utilisateurs' => array("utilisateurs agrees ", 'select', array('acces_compte_client_utilisateurs', 'utl_id', 'emp_nom'), false),

                    'acces_contrat_url'                => array("Url", 'text', 'acces_contrat_url', false),
                    'acces_contrat_login'              => array("Login", 'text', 'acces_contrat_login', false),
                    'acces_contrat_pass'               => array("Pass", 'text', 'acces_contrat_pass', false),
                    'acces_contrat_utilisateurs'       => array("utilisateurs agrees ", 'select', array('acces_contrat_utilisateurs', 'utl_id', 'emp_nom'), false),

                    'acces_root_url'                   => array("Url", 'text', 'acces_root_url', false),
                    'acces_root_login'                 => array("Login", 'text', 'acces_root_login', false),
                    'acces_root_pass'                  => array("Pass", 'text', 'acces_root_pass', false),
                    'acces_root_utilisateurs'          => array("utilisateurs agrees ", 'select', array('acces_root_utilisateurs', 'utl_id', 'emp_nom'), false),

                    'prix'                             => array("Prix", 'text', 'prix', false),
                    'type_de_paiement'                 => array("Type de paiement", 'select', array('type_de_paiement', 'id', 'value'), false),
                    'echeance_du_paiement'             => array("Echéance du paiement", 'date', 'echeance_du_paiement', false),
                    'date_de_resiliation'              => array("Date de résiliation", 'date', 'date_de_resiliation', false),
                    'pas_engage'                       => array("Pas engagé", 'checkbox-h', 'pas_engage', false),
                    'moyen_de_paiement'                => array("Moyen De Paiement", 'select', array('moyen_de_paiement', 'id', 'value'), false),
                    'compte_paypal_utilise'            => array("Compte paypal utilisé", 'text', 'compte_paypal_utilise', false),
                    'cb_utilsée'                       => array("CB Utilsée", 'select', array('cb_utilsée', 'id', 'value'), false),
                    'ips'                              => array("Ajouter des IPs", 'select-multiple', array('ips', 'id', 'value'), false),
                    'domaines'                         => array("Ajouter des domaines ", 'select-multiple', array('domaines', 'id', 'value'), false),
                    'ajouter_des_sites_hébergés'       => array("Ajouter des Sites Hébergés", 'text', 'ajouter_des_sites_hébergés', false),
                    'eta_du_serveur'                   => array("État du serveur", 'select', array('eta_du_serveur', 'id', 'value'), false),
                ),
                'onglets' => array(
                    array('Information générales', array(
                        'host',
                        'nom_interne',
                        'owner',
                        'numero_de_client',
                        'contrat',
                        'options',
                        'system_exploration',
                        'type_serveur',
                        'utilisation',
                        'remarques',
                    )),
                    array('Liens et accès', array(
                        'acces_plesk_url',
                        'acces_plesk_login',
                        'acces_plesk_pass',
                        'acces_plesk_utilisateurs',

                        'acces_compte_client_url',
                        'acces_compte_client_login',
                        'acces_compte_client_pass',
                        'acces_compte_client_utilisateurs',

                        'acces_contrat_url',
                        'acces_contrat_login',
                        'acces_contrat_pass',
                        'acces_contrat_utilisateurs',

                        'acces_root_url',
                        'acces_root_login',
                        'acces_root_pass',
                        'acces_root_utilisateurs',
                    )),
                    array('Paiement', array(
                        'prix',
                        'type_de_paiement',
                        'echeance_du_paiement',
                        'date_de_resiliation',
						'pas_engage',
                        'moyen_de_paiement',
                        'cb_utilsée',
                        'compte_paypal_utilise'
                    )),
                    array('IPS Domaines Sites', array(
                        'ips',
                        'domaines',
                        'ajouter_des_sites_hébergés',
                    )),
                    array('État du serveur', array(
                        'eta_du_serveur',
                    )),
                ),
            );
        
            $data = array(
                'title' => "Modifier Serveur",
                'page' => "templates/form",
                'menu' => "Extra|Edit Servers",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'servers',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
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
            $valeurs = $this->m_servers->detail($id);

            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'servers/modification', 'primary'),
                array("Archiver", 'servers/archive', 'warning'),
                array("Supprimer", 'servers/remove', 'danger'),
            );
            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'host_name'                             => array("Hébergeur", 'VARCHAR 50', 'text', 'host_name'),
                    'nom_interne'                           => array("Nom Interne", 'VARCHAR 50', 'text', 'nom_interne'),
                    'numero_de_client'                      => array("Numéro de client ou compte", 'VARCHAR 50', 'text', 'numero_de_client'),
                    'owner_name'                            => array("Propriétaire", 'VARCHAR 50', 'text', 'owner_name'),
                    'contrat'                               => array("Contrat", 'VARCHAR 50', 'text', 'contrat'),
                    'options'                               => array("Options", 'VARCHAR 50', 'text', 'options'),
                    'system_exploration'                    => array("Système d'exploitation", 'VARCHAR 50', 'text', 'system_exploration'),
                    'type_serveur'                          => array("Type Serveur", 'VARCHAR 50', 'text', 'type_serveur'),
                    'utilisation'                           => array("Utilisation", 'VARCHAR 50', 'text', 'utilisation'),
                    'remarques'                             => array("Remarques", 'VARCHAR 50', 'text', 'remarques'),

                    'acces_plesk_url'                       => array("plesk url", 'VARCHAR 50', 'text', 'acces_plesk_url'),
                    'acces_plesk_login'                     => array("plesk login", 'VARCHAR 50', 'text', 'acces_plesk_login'),
                    'acces_plesk_pass'                      => array("plesk pass", 'VARCHAR 50', 'text', 'acces_plesk_pass'),
                    'acces_plesk_utilisateurs_name'         => array("plesk utilisateurs", 'VARCHAR 50', 'text', 'acces_plesk_utilisateurs_name'),

                    'acces_compte_client_url'               => array("compte client url", 'VARCHAR 50', 'text', 'acces_compte_client_url'),
                    'acces_compte_client_login'             => array("compte client login", 'VARCHAR 50', 'text', 'acces_compte_client_login'),
                    'acces_compte_client_pass'              => array("compte client pass", 'VARCHAR 50', 'text', 'acces_compte_client_pass'),
                    'acces_compte_client_utilisateurs_name' => array("compte client utilisateurs", 'VARCHAR 50', 'text', 'acces_compte_client_utilisateurs_name'),

                    'acces_contrat_url'                     => array("contrat url", 'VARCHAR 50', 'text', 'acces_contrat_url'),
                    'acces_contrat_login'                   => array("contrat login", 'VARCHAR 50', 'text', 'acces_contrat_login'),
                    'acces_contrat_pass'                    => array("contrat pass", 'VARCHAR 50', 'text', 'acces_contrat_pass'),
                    'acces_contrat_utilisateurs_name'       => array("contrat utilisateurs", 'VARCHAR 50', 'text', 'acces_contrat_utilisateurs_name'),

                    'acces_root_url'                        => array("root url", 'VARCHAR 50', 'text', 'acces_root_url'),
                    'acces_root_login'                      => array("root login", 'VARCHAR 50', 'text', 'acces_root_login'),
                    'acces_root_pass'                       => array("root pass", 'VARCHAR 50', 'text', 'acces_root_pass'),
                    'acces_root_utilisateurs_name'          => array("root utilisateurs", 'VARCHAR 50', 'text', 'acces_root_utilisateurs_name'),

                    'prix'                                  => array("Prix", 'VARCHAR 50', 'text', 'prix'),
                    'type_de_paiement'                      => array("Type de paiement", 'VARCHAR 50', 'text', 'type_de_paiement'),
                    'echeance_du_paiement'                  => array("Echéance du paiement", 'DATE', 'date', 'echeance_du_paiement'),
                    'date_de_resiliation'                   => array("Date de résiliation", 'DATE', 'date', 'date_de_resiliation'),
                    'moyen_de_paiement'                     => array("Moyen De Paiement", 'VARCHAR 50', 'text', 'moyen_de_paiement'),
                    'cb_utilsée'                            => array("CB Utilsée", 'VARCHAR 50', 'text', 'cb_utilsée'),
                    'ips_numero'                            => array("Ips", 'VARCHAR 50', 'text', 'ips_numero'),
                    'domaines_name'                         => array("Domaines ", 'VARCHAR 50', 'text', 'domaines_name'),
                    'ajouter_des_sites_hébergés'            => array("Ajouter des Sites Hébergés", 'VARCHAR 50', 'text', 'ajouter_des_sites_hébergés'),
                    'eta_du_serveur'                        => array("Eta du Serveur", 'VARCHAR 50', 'text', 'eta_du_serveur'),
                ),
                'onglets' => array(
                    array('Information générales', array(
                        'host_name',
                        'nom_interne',
                        'owner_name',
                        'numero_de_client',
                        'contrat',
                        'options',
                        'system_exploration',
                        'type_serveur',
                        'utilisation',
                        'remarques',
                    )),
                    array('Liens et accès', array(
                        'acces_plesk_url',
                        'acces_plesk_login',
                        'acces_plesk_pass',
                        'acces_plesk_utilisateurs_name',

                        'acces_compte_client_url',
                        'acces_compte_client_login',
                        'acces_compte_client_pass',
                        'acces_compte_client_utilisateurs_name',

                        'acces_contrat_url',
                        'acces_contrat_login',
                        'acces_contrat_pass',
                        'acces_contrat_utilisateurs_name',

                        'acces_root_url',
                        'acces_root_login',
                        'acces_root_pass',
                        'acces_root_utilisateurs_name',
                    )),
                    array('Paiement', array(
                        'prix',
                        'type_de_paiement',
                        'echeance_du_paiement',
                        'date_de_resiliation',
                        'moyen_de_paiement',
                        'cb_utilsée',
                    )),
                    array('IPS Domaines Sites', array(
                        'ips_numero',
                        'domaines_name',
                        'ajouter_des_sites_hébergés',
                    )),
                    array('État du serveur', array(
                        'eta_du_serveur',
                    )),
                ),
            );

            $data = array(
                'title'        => "Détail of Serveur",
                'page'         => "servers/detail",
                'menu'         => "Extra|Servers",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'servers',
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
    public function archive($id,$ajax=false)
    {
		if ($this->input->method() != 'post') {
            die;
        }
		$redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
		
        $resultat = $this->m_servers->archive($id);
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
            $this->my_set_action_response($ajax, true, "Serveur a été archivé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    /******************************
     * Delete Servers Data
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

        $resultat = $this->m_servers->remove($id);

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
            $this->my_set_action_response($ajax, true, "Serveur a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_servers->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_servers->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_servers->unremove($id);
        }
    }

    public function get_list_domain($selectedId)
    {
        $domaines  = $this->m_domains->liste_option(true);

        $options = "<option value=0>(choisissez)</option>";

        foreach($domaines as $row) {
            $selected = $row->id == $selectedId ? "selected" : "";
            $options += '<option value="'.$row->id.'" '.$selected.'>'.$row->value.'</option>';
        }

        echo $options;
    }
    
    public function get_list_IPS()
    {
    	
    	//$row = $this->m_ips->liste_option(true);
    	/*
    	$tags = array();
    	foreach($result as $row) 
    	{
    		$tag = explode(",", strtolower($row['tag']));
    		$tags = array_merge($tags, $tag);
    	}
    	$tags = array_unique($tags);
    	foreach($tags as $val) 
    	{
    		$data[] = array("label" => $val , "title" => $val , "value" => $val);
    	}
    	$data = array( "label" => 0 , "title" => "No Tags", "value" => "");
    	
    	foreach($row as $row)
    	{
    		$data[] = array("label" => $row->value, "title" => $row->value , "value" => $val);
    	}
    	*/
    	echo json_encode('a');
    }
    
}
// EOF
