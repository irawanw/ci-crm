<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_rbl_list m_rbl_list
 */
class Rbl_list extends MY_Controller
{
    private $profil;
    private $barre_action = array(
		array(
			"Nouveau" => array('rbl_list/nouveau', 'plus', true, 'rbl_list_nouveau', null, array('form')),
		),
		array(
			"Modifier"  => array('rbl_list/modification', 'pencil', false, 'rbl_list_modification', null, array('form')),
			"Supprimer" => array('rbl_list/suppression', 'trash', false, 'rbl_list_supprimer', "Veuillez confirmer la suppression du RBL", array('confirm-delete' => array('rbl_list/index'))),
		),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_rbl_list','m_providers'));
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
            'datasource'         => 'rbl_list/index',
            'detail'             => array('rbl_list/detail', 'rbl_id','description'),
			'archive'            => array('rbl_list/archive', 'rbl_id', 'archive'),
            'champs'             => $this->m_rbl_list->get_champs('read'),
            'filterable_columns' => $this->m_rbl_list->liste_filterable_columns(),
        );

        switch ($mode) {
			case 'archiver':
                $descripteur['datasource'] = 'rbl_list/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'rbl_list/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'rbl_list/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'rbl_list',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);

		//$scripts[] = $this->load->view('rbl_list/form-js', array(), true);
        //$scripts[] = $this->load->view('rbl_list/liste-js', array(), true);
        $vues      = $this->m_vues->vues_ctrl('rbl_list', $this->session->id);
        $data      = array(
            'title'        => "Liste De RBL",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Liste De RBL",
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
		
        if (empty($order) || empty($columns)) 
		{
            $resultat = $this->m_rbl_list->liste($id, $pagelength, $pagestart, $filters);
        } 
		else 
		{
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
				'rbl_id' => 't_rbl_liste'
			);

            if ($order_col_id >= 0 && $order_col_id <= count($columns)) 
			{
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

                $resultat = $this->m_rbl_list->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_rbl_list->liste($id, $pagelength, $pagestart, $filters);
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
     * Nouveau
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');
        $config = array(
                array('field' => 'rbl_nom', 'label' => "Nom", 'rules' => 'trim'),
                array('field' => 'rbl_url', 'label' => "Url", 'rules' => 'trim'),
                array('field' => 'rbl_abuse_mail', 'label' => "Abuse mail", 'rules' => 'trim'),
				array('field' => 'rbl_delistable', 'label' => "Delistable", 'rules' => 'trim'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) 
		{
            $rbl_providers_array = $this->input->post('rbl_providers', true) ? $this->input->post('rbl_providers', true) : array();
            $rbl_providers = implode(",", $rbl_providers_array);

            $valeurs = array(
                'rbl_nom'   => $this->input->post('rbl_nom'),
                'rbl_url'   => $this->input->post('rbl_url'),
                'rbl_abuse_mail'   => $this->input->post('rbl_abuse_mail'),
                'rbl_delistable'	=> $this->input->post('rbl_delistable'),
                'rbl_providers' => $rbl_providers
            );
            $resultat = $this->m_rbl_list->nouveau($valeurs);
            if ($resultat === false) 
			{
                $this->my_set_action_response($ajax, false);
            } 
			else 
			{
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $resultat,
                        'timeStamp'  => round(microtime(true) * 1000),
                    ),
                );
                $this->my_set_action_response($ajax, true, "RBL a été enregistré avec succès", 'info', $ajaxData);
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
		else 
		{
            $valeurs			= new stdClass();
            $listes_valeurs		= new stdClass();
            $valeurs->rbl_nom   = $this->input->post('rbl_nom');
            $valeurs->rbl_url   = $this->input->post('rbl_url');
            $valeurs->rbl_abuse_mail   = $this->input->post('rbl_abuse_mail');
            $valeurs->rbl_delistable   = $this->input->post('rbl_delistable');
            $valeurs->rbl_providers   = $this->input->post('rbl_providers');
            $listes_valeurs->rbl_delistable = $this->m_rbl_list->delistable_liste_option();
            $listes_valeurs->rbl_providers = $this->m_providers->liste_option();

            $descripteur = array(
                'champs'  => $this->m_rbl_list->get_champs('write'),
                'onglets' => array(),
            );
            $data = array(
                'title'          => "Nouveau Liste De RBL",
                'page'           => "templates/form",
                'menu'           => "Extra|Nouveau Liste De RBL",
                'values'         => $valeurs,
                'action'         => "create",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'rbl_list',
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
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        $config = array(
            array('field' => 'rbl_nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'rbl_url', 'label' => "Url", 'rules' => 'trim'),
            array('field' => 'rbl_abuse_mail', 'label' => "Abuse mail", 'rules' => 'trim'),
            array('field' => 'rbl_delistable', 'label' => "Delistable", 'rules' => 'trim'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $rbl_providers_array = $this->input->post('rbl_providers', true) ? $this->input->post('rbl_providers', true) : array();
            $rbl_providers = implode(",", $rbl_providers_array);

            $valeurs = array(
                'rbl_nom'	=> $this->input->post('rbl_nom'),
                'rbl_url'   => $this->input->post('rbl_url'),
                'rbl_abuse_mail'   => $this->input->post('rbl_abuse_mail'),
                'rbl_delistable'    => $this->input->post('rbl_delistable'),
                'rbl_providers'  => $rbl_providers
            );

            $resultat = $this->m_rbl_list->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification RBL";
                    $ajaxData = null;
                } else {
                    $message  = "RBL a été modifiée";
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordchange',
                            'id'         => $resultat,
                            'timeStamp'  => round(microtime(true) * 1000),
                        ),
                    );
                }

                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }
            $redirection = 'rbl_list/detail/'.$id;
            redirect($redirection);
        } else {
            $valeurs        = $this->m_rbl_list->detail($id);
            $listes_valeurs = new stdClass();
            $listes_valeurs->rbl_delistable = $this->m_rbl_list->delistable_liste_option();
            $listes_valeurs->rbl_providers = $this->m_providers->liste_option();

            $valeur = $this->input->post('rbl_nom');
            if (isset($valeur)) {
                $valeurs->rbl_nom = $valeur;
            }
            $scripts = array();

            $descripteur = array(
                'champs'  => $this->m_rbl_list->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier RBL Liste",
                'page'           => "templates/form",
                'menu'           => "Extra|Modifier RBL Liste",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'rbl_list',
                'methode'        => __FUNCTION__,
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
            $valeurs = $this->m_rbl_list->detail($id);
            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'rbl_nom'   => array("Nom", 'VARCHAR 50', 'text', 'rbl_nom'),
                    'rbl_url'   => array("Url", 'VARCHAR 255', 'text', 'rbl_url'),
                    'rbl_abuse_mail'   => array("Abuse mail", 'VARCHAR 255', 'text', 'rbl_abuse_mail'),
                    'rbl_delistable'   => array("Delistable", 'VARCHAR 255', 'text', 'rbl_delistable'),
                ),
                'onglets' => array(),
            );
            $data = array(
                'title'        => "Détail RBL Liste",
                'page'         => "templates/detail",
                'menu'         => "Extra|Détail RBL Liste",
                'barre_action' => $this->barre_action,
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'rbl_list',
                'methode'      => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
                'id_parent'    => 'rbl_id',
            );
            $this->my_set_display_response($ajax, $data);
        }
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
        $resultat = $this->m_rbl_list->remove($id);

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
            $this->my_set_action_response($ajax, true, "RBL a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

	public function mass_archiver()
    {
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id)
		{
			$resultat = $this->m_rbl_list->archive($id);
		}
	}

	public function mass_remove()
    {
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id)
		{
			$resultat = $this->m_rbl_list->remove($id);
		}
	}

	public function mass_unremove()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id)
		{
			$resultat = $this->m_rbl_list->unremove($id);
		}
	}
}
// EOF