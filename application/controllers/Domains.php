<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * @property M_domains m_domains
 */
class Domains extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('domains/nouveau', 'plus', true, 'domains_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('domains/detail', 'eye-open', false, 'domains_detail'),
            "Consulter/Modifier" => array('domains/modification', 'pencil', false, 'domains_modification', null, array('form')),
            "Supprimer"          => array('domains/remove', 'trash', false, 'domains_supprimer', "Veuillez confirmer la suppression du domain", array('confirm-modify' => array('domains/index'))),
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
        $this->load->model('m_domains');
    }

    /******************************
     * List of domains Data
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
            //array()
        );

        $menu_extra = array(
            array(
                'name' => "Serveurs",
                'url'  => site_url('servers'),
            ),
            array(
                'name' => "Hebergeur",
                'url'  => site_url('hosts'),
            ),
            array(
                'name' => "Propriétaire",
                'url'  => site_url('owners'),
            ),
            array(
                'name' => "Domains",
                'url'  => site_url('domains'),
            ),
            array(
                'name' => "IPS",
                'url'  => site_url('ips'),
            ),
            array(
                'name' => "Cartes Bleues",
                'url'  => site_url('cartes_blues'),
            ),
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'domains/index',
            'detail'             => array('domains/detail', 'domain_id', 'description'),
            'archive'            => array('domains/archive', 'domain_id', 'archive'),
            'champs'             => $this->m_domains->get_champs('read'),
            'filterable_columns' => $this->m_domains->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: domains/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'domains/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'domains/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'domains/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'domains',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
				
            ), true);
        $scripts[] = $this->load->view('domains/form-js', array(), true);
		$scripts[] = $this->load->view("domains/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('domains', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Domains",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Domains",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
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
		
        if (empty($order) || empty($columns)) 
		{
            //list with default ordering
            $resultat = $this->m_domains->liste($id, $pagelength, $pagestart, $filters);
        } 
		else 
		{
            //list with requested ordering
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

                $resultat = $this->m_domains->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_domains->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
			$pagelength = false;
            $pagestart = 0;
            //action export data xls
            $champs = $this->m_domains->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Domains'
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
    public function nouveau($serverid = 0, $ajax = false)
    {
        $this->load->model(array('m_servers', 'm_owners', 'm_hosts', 'm_champs'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim|valid_domain|required'),
            array('field' => 'server', 'label' => "Serveurs", 'rules' => 'trim'),
            array('field' => 'owner', 'label' => "Propriétaire", 'rules' => 'trim'),
            array('field' => 'host', 'label' => "Hébergeur", 'rules' => 'trim'),
            array('field' => 'compte', 'label' => "Compte", 'rules' => 'trim'),
            array('field' => 'contrat', 'label' => "Contrat", 'contrat' => 'trim'),
            //array('field' => 'identifiant', 'label' => "Identifiant", 'rules' => 'trim'),
            array('field' => 'site', 'label' => "Site", 'rules' => 'trim|valid_url'),
            //array('field' => 'utilisation', 'label' => "Utilisation", 'rules' => 'trim'),
			array('field' => 'etat', 'label' => "État", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            $utilisation_array = $this->input->post('utilisation', true) ? $this->input->post('utilisation', true) : array();
            $utilisation = implode(",", $utilisation_array);

            // validation réussie
            $valeurs = array(
                'nom'         		=> $this->input->post('nom'),
                'server'      		=> $this->input->post('server'),
                'owner'       		=> $this->input->post('owner'),
                'host'        		=> $this->input->post('host'),
                'compte'     		=> $this->input->post('compte'),
                'contrat'     		=> $this->input->post('contrat'),
                //'identifiant'   	=> $this->input->post('identifiant'),
                'site'        		=> $this->input->post('site'),
                'utilisation' 		=> $utilisation,
				'etat'				=> $this->input->post('etat'),
            );

            $resultat = $this->m_domains->nouveau($valeurs);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
            	$ajaxData = array(
					'event' => array(
						'controleur' => $this->my_controleur_from_class(__CLASS__),
						'type' 		 => 'recordadd',
						'id' 		 => $resultat,
						'timeStamp'  => round(microtime(true) * 1000),
           			),
            	);
                $this->my_set_action_response($ajax, true, "Domain a été enregistré avec succès", 'info', $ajaxData);
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
            $valeurs->nom   = $this->input->post('nom');
            if ($serverid != 0) {
                $valeurs->server  = $serverid;
                $serveur          = $this->db->get_where('t_servers', array('server_id' => $serverid))->row();
                $valeurs->host    = $serveur->host;
                $valeurs->owner   = $serveur->owner;
                $valeurs->compte  = $serveur->numero_de_client;
                $valeurs->contrat = $serveur->contrat;

                $listes_valeurs->contrat = $this->m_domains->contrat_liste_option_by_server($serverid);
                $listes_valeurs->compte  = $this->m_domains->compte_liste_option_by_server($serverid);
            } else {
                $valeurs->server  = $this->input->post('server');
                $valeurs->host    = $this->input->post('host');
                $valeurs->owner   = $this->input->post('owner');
                $valeurs->compte  = $this->input->post('compte');
                $valeurs->contrat = $this->input->post('contrat');

                $listes_valeurs->compte  = $this->m_domains->compte_liste_option_by_host(0);
                $listes_valeurs->contrat = $this->m_domains->contrat_liste_option(0, 0, 0);
            }

            //$valeurs->identifiant   			 = $this->input->post('identifiant');
            $valeurs->site        				 = $this->input->post('site');
            $valeurs->utilisation 				 = $this->input->post('utilisation');
            $valeurs->acces_contrat_url          = $this->input->post('acces_contrat_url');
            $valeurs->acces_contrat_login        = $this->input->post('acces_contrat_login');
            $valeurs->acces_contrat_pass         = $this->input->post('acces_contrat_pass');
            $valeurs->acces_contrat_utilisateurs = $this->input->post('acces_contrat_utilisateurs');
			$valeurs->etat			       		 = $this->input->post('etat');
			
            $listes_valeurs->server 		= $this->m_servers->liste_option();
            $listes_valeurs->owner  		= $this->m_owners->liste_option();
            $listes_valeurs->host   		= $this->m_hosts->liste_option();
            $listes_valeurs->utilisation 	= $this->m_domains->utilisation_liste_option();
			$listes_valeurs->etat			= $this->m_servers->eta_du_serveur_liste_option();
			
            $scripts = array();

            //add button to add new champs
            $liste_ajouter = array(
                array(
                    'id'    => 'host',
                    'ref'   => site_url('hosts/nouveau/0'),
                    'champ' => 'domain_host',
                ),
                array(
                    'id'    => 'owner',
                    'ref'   => site_url('owners/nouveau/0'),
                    'champ' => 'domain_owner',
                ),
            );

            $scripts[] = $this->load->view('champs/form-ajouter', array('liste_ajouter' => $liste_ajouter), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_domains->get_champs('write'),
                'onglets' => array(
                    array('Information generales', array('nom', 'server', 'owner', 'host', 'compte', 'contrat', 'site', 'utilisation')),
                    array('Liens et accès', array(
                        'acces_contrat_url',
                        'acces_contrat_login',
                        'acces_contrat_pass',
                        'acces_contrat_utilisateurs',
                    )),
					array('État', array(
                        'etat',
                    )),
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Domain",
                'page'           => "templates/form",
                'menu'           => "Agenda|Nouveau Domains",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'domains',
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
        $this->load->model(array('m_servers','m_owners', 'm_hosts', 'm_champs'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim|valid_domain|required'),
        	array('field' => 'server', 'label' => "Serveurs", 'rules' => 'trim'),
            array('field' => 'owner', 'label' => "Propriétaire", 'rules' => 'trim'),
            array('field' => 'host', 'label' => "Hébergeur", 'rules' => 'trim'),
            array('field' => 'compte', 'label' => "Compte", 'rules' => 'trim'),
            array('field' => 'contrat', 'label' => "Contrat", 'rules' => 'trim'),
            //array('field' => 'identifiant', 'label' => "Identifiant", 'rules' => 'trim'),
            array('field' => 'site', 'label' => "Site", 'rules' => 'trim|valid_url'),
            array('field' => 'utilisation', 'label' => "Utilisation"),
			array('field' => 'etat', 'label' => "État", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $utilisation_array = $this->input->post('utilisation', true) ? $this->input->post('utilisation', true) : array();
            $utilisation = implode(",", $utilisation_array);
            // validation réussie
            $valeurs = array(
                'nom'         		=> $this->input->post('nom'),
            	'server'       		=> $this->input->post('server'),
            	'owner'       		=> $this->input->post('owner'),
                'host'        		=> $this->input->post('host'),
                'compte'      		=> $this->input->post('compte'),
                'contrat'     		=> $this->input->post('contrat'),
                //'identifiant'   	=> $this->input->post('identifiant'),
                'site'        		=> $this->input->post('site'),
                'utilisation' 		=> $utilisation,
				'etat'				=> $this->input->post('etat'),
            );

            $resultat = $this->m_domains->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Domain a été modifié";
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

            $redirection = 'domains/detail/' . $id;
            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_domains->detail($id);	
            $listes_valeurs              	= new stdClass;
				
            $listes_valeurs->server      	= $this->m_servers->liste_option();
            $listes_valeurs->owner       	= $this->m_owners->liste_option();
            $listes_valeurs->host        	= $this->m_hosts->liste_option();
            $listes_valeurs->compte      	= $this->m_domains->compte_liste_option_by_host($valeurs->host);
            $listes_valeurs->contrat     	= $this->m_domains->contrat_liste_option($valeurs->owner, $valeurs->host, $valeurs->compte);
            $listes_valeurs->utilisation 	= $this->m_domains->utilisation_liste_option();
			$listes_valeurs->etat			= $this->m_servers->eta_du_serveur_liste_option();
			
			$valeur = $this->input->post('server');
            if(isset($valeur)) {
                $valeurs->server = $valeur;
            }
			$valeur = $this->input->post('owner');
            if(isset($valeur)) {
                $valeurs->owner = $valeur;
            }
			$valeur = $this->input->post('host');
            if(isset($valeur)) {
                $valeurs->host = $valeur;
            }
			$valeur = $this->input->post('compte');
            if(isset($valeur)) {
                $valeurs->compte = $valeur;
            }
			$valeur = $this->input->post('contrat');
            if(isset($valeur)) {
                $valeurs->contrat = $valeur;
            }
			$valeur = $this->input->post('utilisation');
            if(isset($valeur)) {
                $valeurs->utilisation = $valeur;
            }
			$valeur = $this->input->post('etat');
            if(isset($valeur)) {
                $valeurs->etat = $valeur;
            }

            $server_id 	= $valeurs->server;
            $owner_id 	= $valeurs->owner;
            $host_id  	= $valeurs->host;
            $compte   	= $valeurs->compte;
            $contrat  	= $valeurs->contrat;
			$utilisation= $valeurs->utilisation;
			$etat  		= $valeurs->etat;

            $resultat_contrat = $this->m_domains->get_contrat_detail($owner_id, $host_id, $compte, $contrat);

            $valeurs->acces_contrat_url          = $resultat_contrat['url'];
            $valeurs->acces_contrat_login        = $resultat_contrat['login'];
            $valeurs->acces_contrat_pass         = $resultat_contrat['pass'];
            $valeurs->acces_contrat_utilisateurs = $resultat_contrat['utilisateurs'];

            $scripts = array();
            //add button to add new champs
            $liste_ajouter = array(
                array(
                    'id'    => 'host',
                    'ref'   => site_url('hosts/nouveau/0'),
                    'champ' => 'domain_host',
                ),
                array(
                    'id'    => 'owner',
                    'ref'   => site_url('owners/nouveau/0'),
                    'champ' => 'domain_owner',
                ),
            );

            $scripts[] = $this->load->view('champs/form-ajouter', array('liste_ajouter' => $liste_ajouter), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_domains->get_champs('write'),
                'onglets' => array(
                    array('Information générales', array('nom', 'server','owner', 'host', 'compte', 'contrat', 'site', 'utilisation')),
                    array('Liens et accès', array(
                        'acces_contrat_url',
                        'acces_contrat_login',
                        'acces_contrat_pass',
                        'acces_contrat_utilisateurs',
                    )),
					array('État', array(
                        'etat',
                    )),
                ),
            );

            $data = array(
                'title'          => "Modifier Domain",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Domains",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'domains',
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
            $valeurs = $this->m_domains->detail($id);

            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'domains/modification', 'primary'),
                array("Archiver", 'domains/archive', 'warning'),
                array("Supprimer", 'domains/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'nom'                             => array("Nom", 'VARCHAR 50', 'text', 'nom'),
                    'owner_name'                      => array("Propriétaire", 'VARCHAR 50', 'text', 'owner_name'),
                    'host_name'                       => array("Hébergeur", 'VARCHAR 50', 'text', 'host_name'),
                    'compte'                          => array("Compte", 'VARCHAR 50', 'text', 'compte'),
                    'contrat'                         => array("Contrat", 'VARCHAR 50', 'text', 'contrat'),
                    //'identifiant' 				  => array("Identifiant", 'VARCHAR 50', 'text', 'identifiant'),
                    'site'                            => array("Site", 'VARCHAR 50', 'text', 'site'),
                    'utilisation'                     => array("Utilisation ", 'VARCHAR 50', 'text', 'utilisation'),
                    'acces_contrat_url'               => array("Contrat url", 'VARCHAR 50', 'text', 'acces_contrat_url'),
                    'acces_contrat_login'             => array("Contrat login", 'VARCHAR 50', 'text', 'acces_contrat_login'),
                    'acces_contrat_pass'              => array("Contrat pass", 'VARCHAR 50', 'text', 'acces_contrat_pass'),
                    'acces_contrat_utilisateurs_name' => array("Contrat utilisateurs_name", 'VARCHAR 50', 'text', 'acces_contrat_utilisateurs_name'),
					'etat'			                  => array("État", 'VARCHAR 50', 'text','etat'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Domain",
                'page'         => "templates/detail",
                'menu'         => "Extra|Domains",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'domains',
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
    public function archive($id)
    {
        $resultat = $this->m_domains->archive($id);
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
            $this->session->set_flashdata('success', "Domains a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Domains Data
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

        $resultat = $this->m_domains->remove($id);
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
            $this->my_set_action_response($ajax, true, "Domain a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_domains->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_domains->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_domains->unremove($id);
        }
    }

    /**
     * get liste host only which related with server chosen
     * @param  int $server server id
     * @return [type]    html
     */
    public function host_option($server_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $resultat = $this->m_domains->host_liste_option_by_server($server_id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='0'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "' selected='selected'>" . $row['value'] . "</option>";
        }
    }

    public function owner_option($server_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $resultat = $this->m_domains->owner_liste_option_by_server($server_id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='0'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "' selected='selected'>" . $row['value'] . "</option>";
        }
    }

    /**
     * get liste compte only which related with host chosen
     * @param  int $host host id
     * @return [type]        html
     */
    public function compte_option($server_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $resultat = $this->m_domains->compte_liste_option_by_server($server_id);
        //$resultat = $this->m_domains->compte_liste_option_by_host($host_id);
        $results = json_decode(json_encode($resultat), true);

        echo "<option value='0'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "' selected='selected'>" . $row['value'] . "</option>";
        }
    }

    /**
     * get liste contrat only which related with owner,host, and contrat chosen
     * @return [type]        html
     */
    public function contrat_option($server_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }
        $resultat = $this->m_domains->contrat_liste_option_by_server($server_id);
        //$resultat = $this->m_domains->contrat_liste_option($owner_id, $host_id, $compte);
        $results = json_decode(json_encode($resultat), true);

        echo "<option value='0'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "' selected='selected'>" . $row['value'] . "</option>";
        }
    }

    public function get_contrat_detail()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $owner_id = $this->input->post('owner_id', true);
        $host_id  = $this->input->post('host_id', true);
        $compte   = $this->input->post('compte', true);
        $contrat  = $this->input->post('contrat', true);

        $resultat = $this->m_domains->get_contrat_detail($owner_id, $host_id, $compte, $contrat);
        echo json_encode($resultat);
    }
}
// EOF
