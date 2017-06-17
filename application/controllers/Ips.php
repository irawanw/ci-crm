<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * @property M_ips m_ips
 */
class IPS extends MY_Controller
{
	private $profil;
	private $barre_action = array(
			array(
					"Nouveau" => array('ips/nouveau', 'plus', true, 'ips_nouveau',null,array('form')),
			),
			array(
					//"Consulter" => array('ips/detail', 'eye-open', false, 'ips_detail'),
					"Consulter/Modifier"  => array('ips/modification', 'pencil', false, 'ips_modification',null,array('form')),
					"Supprimer" => array('ips/remove', 'trash', false, 'ips_supprimer',"Veuillez confirmer la suppression du ip", array('confirm-delete' => array('ips/index'))),
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
		$this->load->model('m_ips');
	}

	
	/******************************
	 * List of ips Data
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
				// array("Ajouter un e-mailing pages jaunes","ips/nouveau",'default')
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
				'datasource'         => 'ips/index',
				'detail'             => array('ips/detail', 'ip_id', 'description'),
				'archive'            => array('ips/archive', 'ip_id', 'archive'),
				'champs'             => $this->m_ips->get_champs('read'),
				'filterable_columns' => $this->m_ips->liste_filterable_columns(),
		);

		//determine json script that will be loaded
		//for eg: ips/archived_json in kendo_grid-js
		switch ($mode) {
			case 'archiver':
				$descripteur['datasource'] = 'ips/archived';
				break;
			case 'supprimees':
				$descripteur['datasource'] = 'ips/deleted';
				break;
			case 'all':
				$descripteur['datasource'] = 'ips/all';
				break;
		}

		$this->session->set_userdata('_url_retour', current_url());
		$scripts = array();

		$scripts[] = $this->load->view("templates/datatables-js",
				array(
						'id'                    => $id,
						'descripteur'           => $descripteur,
						'toolbar'               => $toolbar,
						'controleur'            => 'ips',
						'methode'               => 'index',
						'mass_action_toolbar'   => true,
						'view_toolbar'          => true,
				), true);
		$scripts[] = $this->load->view("ips/liste-js", array(), true);
		$scripts[] = $this->load->view('ips/form-js', array(), true);
		// listes personnelles
		$vues = $this->m_vues->vues_ctrl('ips', $this->session->id);
		$data = array(
				'title'        => "Liste suivi des IPS",
				'page'         => "templates/datatables",
				'menu'         => "Extra|IPS",
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

		if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

		if (empty($order) || empty($columns)) {

			//list with default ordering
			$resultat = $this->m_ips->liste($id, $pagelength, $pagestart, $filters);
		} else {

			//list with requested ordering
			$order_col_id = $order[0]['column'];
			$ordering     = $order[0]['dir'];

			// tables for LINK columns
			$tables = array(
					'ip_id' => 't_ips',
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

				$resultat = $this->m_ips->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
			} else {
				$resultat = $this->m_ips->liste($id, $pagelength, $pagestart, $filters);
			}
		}
		
		if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_ips->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'IPS'
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
	public function nouveau($serverid=0,$ajax=false)
	{
		$this->load->model(array('m_hosts','m_servers','m_owners'));
		$this->load->helper(array('form', 'ctrl'));
		$this->load->library('form_validation');

		// règles de validation
		$config = array(
				array('field' => 'numero', 'label' => "Numéro", 'rules' => 'trim|required'),
				array('field' => 'serveur', 'label' => "Serveur", 'rules' => 'trim'),
				array('field' => 'host', 'label' => "Hébergeur ", 'rules' => 'trim'),
				array('field' => 'etat', 'label' => "État ", 'rules' => 'trim'),
				array('field' => 'utilisation', 'label' => "Utilisation", 'rules' => 'trim'),
		);

		// validation des fichiers chargés
		$validation = true;

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() and $validation) {

			// validation réussie
			$valeurs = array(
					'numero'        => $this->input->post('numero'),
					'serveur'       => $this->input->post('serveur'),
					'host'          => $this->input->post('host'),
					'etat'          => $this->input->post('etat'),
					'utilisation'   => $this->input->post('utilisation'),
			);

			$resultat = $this->m_ips->nouveau($valeurs);

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
				$this->my_set_action_response($ajax, true, "IP a été enregistré avec succès",'info', $ajaxData);
			}
			if ($ajax) {
				return;
			}

			$redirection = $this->session->userdata('_url_retour');
			if (! $redirection) $redirection = '';
			redirect($redirection);

		} else {
			// validation en échec ou premier appel : affichage du formulaire
				
			$valeurs                = new stdClass();
			$listes_valeurs         = new stdClass();
			$valeurs->numero        = $this->input->post('numero');

			if ($serverid != 0)
			{
				$valeurs->serveur	= $serverid;
				$serveur = $this->db->get_where('t_servers',array('server_id' => $serverid))->row();
				//$ownerid = $serveur->owner;
				$valeurs->owner = $serveur->owner;
				$valeurs->host	= $serveur->host;
			}
			else{
				$valeurs->serveur       = $this->input->post('serveur');
				$valeurs->host          = $this->input->post('host');
				$valeurs->owner         = $this->input->post('owner');
			}
				
			$valeurs->etat          = $this->input->post('etat');
			$valeurs->utilisation   = $this->input->post('utilisation');

			$listes_valeurs->host   = $this->m_hosts->liste_option();
			$listes_valeurs->owner   = $this->m_owners->liste_option();
			$listes_valeurs->serveur = $this->m_servers->liste_option();
			$listes_valeurs->etat   = $this->m_ips->etat_liste_option();
			$listes_valeurs->utilisation = $this->m_ips->utilisation_liste_option();
				
			$scripts = array();
			// descripteur
			$descripteur = array(
					'champs'  => $this->m_ips->get_champs('write'),
					'onglets' => array(),
			);

			$data = array(
					'title' => "Ajouter un nouveau IP",
					'page' => "templates/form",
					'menu' => "Agenda|Nouveau IPS",
					'values' => $valeurs,
					'action' => "création",
					'multipart' => false,
					'confirmation' => 'Enregistrer',
					'controleur' => 'ips',
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
	public function modification($id = 0,$ajax=false)
	{
		$this->load->model(array('m_hosts','m_servers','m_owners'));
		$this->load->helper(array('form', 'ctrl'));
		$this->load->library('form_validation');

		// règles de validation
		$config = array(
				array('field' => 'numero', 'label' => "Numéro", 'rules' => 'trim|required'),
				array('field' => 'serveur', 'label' => "Serveur", 'rules' => 'trim'),
				array('field' => 'host', 'label' => "Hébergeur ", 'rules' => 'trim'),
				array('field' => 'etat', 'label' => "État ", 'rules' => 'trim'),
				array('field' => 'utilisation', 'label' => "Utilisation", 'rules' => 'trim'),
		);

		// validation des fichiers chargés
		$validation = true;

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() and $validation) {

			// validation réussie
			$valeurs = array(
					'numero'        => $this->input->post('numero'),
					'serveur'       => $this->input->post('serveur'),
					'host'          => $this->input->post('host'),
					'etat'          => $this->input->post('etat'),
					'utilisation'   => $this->input->post('utilisation'),
			);

			$resultat = $this->m_ips->maj($valeurs, $id);

			if ($resultat === false) {
				$this->my_set_action_response($ajax, false);
			}
			else {
				if ($resultat == 0) {
					$message = "Pas de modification demandée";
					$ajaxData = null;
				}
				else {
					$message = "IP a été modifié";
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

			$redirection = 'ips/detail/'.$id;
			redirect($redirection);

		} else {

			// validation en échec ou premier appel : affichage du formulaire
			$valeurs = $this->m_ips->detail($id);
			$listes_valeurs = new stdClass();

			$listes_valeurs->serveur = $this->m_servers->liste_option();
			$listes_valeurs->host = $this->m_hosts->liste_option();
			$listes_valeurs->owner = $this->m_owners->liste_option();
			$listes_valeurs->etat   = $this->m_ips->etat_liste_option();
			$listes_valeurs->utilisation = $this->m_ips->utilisation_liste_option();

			$scripts = array();

			// descripteur
			$descripteur = array(
					'champs'  => $this->m_ips->get_champs('write'),
					'onglets' => array(),
			);

			$data = array(
					'title' => "Modifier IP",
					'page' => "templates/form",
					'menu' => "Extra|Edit IPS",
					'scripts' => $scripts,
					'id' => $id,
					'values' => $valeurs,
					'action' => "modif",
					'multipart' => false,
					'confirmation' => 'Enregistrer',
					'controleur' => 'ips',
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
			$valeurs = $this->m_ips->detail($id);

			$cmd_globales = array();

			// commandes locales
			$cmd_locales = array(
					array("Modifier", 'ips/modification', 'primary'),
					array("Archiver", 'ips/archive', 'warning'),
					array("Supprimer", 'ips/remove', 'danger'),
			);

			// descripteur
			$descripteur = array(
					'champs'  => array(
							'numero' => array("Numéro", 'VARCHAR 50', 'text', 'numero'),
							'serveur_name' => array("Serveur", 'VARCHAR 50', 'text', 'serveur_name'),
							'host_name' => array("Hébergeur", 'VARCHAR 50', 'text', 'host_name'),
							'utilisation' => array("Utilisation", 'VARCHAR 50', 'text', 'utilisation'),
							'etat' => array("État", 'VARCHAR 50', 'text', 'etat'),
					),
					'onglets' => array(),
			);

			$data = array(
					'title'        => "Détail of IP",
					'page'         => "templates/detail",
					'menu'         => "Extra|IPS",
					'id'           => $id,
					'values'       => $valeurs,
					'controleur'   => 'ips',
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
		$resultat = $this->m_ips->archive($id);
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
			$this->session->set_flashdata('success', "IPS a été archivé");
			$redirection = $this->session->userdata('_url_retour');
			if (!$redirection) {
				$redirection = '';
			}

			redirect($redirection);
		}
	}

	/******************************
	 * Delete IPS Data
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

		$resultat = $this->m_ips->remove($id);

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
			$this->my_set_action_response($ajax, true, "IP a été supprimé", 'info', $ajaxData);
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
			$resultat = $this->m_ips->archive($id);
		}
	}

	public function mass_remove()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id) {
			$resultat = $this->m_ips->remove($id);
		}
	}

	public function mass_unremove()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id) {
			$resultat = $this->m_ips->unremove($id);
		}
	}

	public function get_host($serveurId)
	{
		$result = $this->m_ips->get_host($serveurId);

		echo json_encode($result);

	}

	public function get_owner($serveurId)
	{
		$result = $this->m_ips->get_owner($serveurId);

		echo json_encode($result);

	}


}
// EOF
