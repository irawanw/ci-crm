<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Document_templates extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('document_templates/nouveau', 'plus', true, 'document_templates_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier"  => array('document_templates/modification', 'pencil', false, 'document_templates_modification',null, array('form')),
            "Dupliquer"          => array('document_templates/dupliquer', 'duplicate', false, 'document_templates__dupliquer', "Veuillez confirmer la duplication du document templates", array('confirm-delete' => array('document_templates/index'))),
            "Supprimer" => array('document_templates/remove', 'trash', false, 'document_templates_supprimer',"Veuillez confirmer la suppression du template", array('confirm-delete' => array('document_templates/index'))),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_document_templates');
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
        $cmd_globales = array();

        // toolbar
        $toolbar = '';
		
        // descripteur
        $descripteur = array(
            'datasource'         => 'document_templates/index',
            'detail'             => array('document_templates/detail', 'tpl_id', 'description'),
            'archive'            => array('document_templates/archive', 'tpl_id', 'archive'),
            'champs'             => $this->m_document_templates->get_champs('read'),
            'filterable_columns' => $this->m_document_templates->liste_filterable_columns(),
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'document_templates/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'document_templates/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'document_templates/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'document_templates',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("document_templates/form-js", array(), true);
		//$scripts[] = $this->load->view("document_templates/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('document_templates', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Document Templates",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Document Templates",
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
     * Ajax call for List
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
            $resultat = $this->m_document_templates->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tpl_id' => 't_document_templates',
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

                $resultat = $this->m_document_templates->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_document_templates->liste($id, $pagelength, $pagestart, $filters);
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
     * New
     ******************************/
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        $config = array(
			array('field' => 'tpl_nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'tpl_content', 'label' => "Content", 'rules' => 'trim'),
        );

        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) 
		{
            $valeurs = array(
                'tpl_nom'			=> $this->input->post('tpl_nom'),
                'tpl_content'		=> $this->input->post('tpl_content'),
				'tpl_created_date'	=> date("Y-m-d"),
            );

            $resultat = $this->m_document_templates->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Template a été enregistré avec succès",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
           
        } else {
            $valeurs               	= new stdClass();
            $listes_valeurs         = new stdClass();
            $valeurs->tpl_nom		= $this->input->post('tpl_nom');
            $valeurs->tpl_content	= $this->input->post('tpl_content');
            $scripts = array();

            $descripteur = array(
				'champs'  => $this->m_document_templates->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Nouveau Document Template",
                'page' => "templates/form",
                'menu' => "Extra|Document Templates",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'document_templates',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Edit function for Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
			array('field' => 'tpl_nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'tpl_content', 'label' => "Content", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
             $valeurs = array(
                'tpl_nom'			=> $this->input->post('tpl_nom'),
                'tpl_content'		=> $this->input->post('tpl_content'),
            );

            $resultat = $this->m_document_templates->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "Carte a été modifiée";
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
			$redirection = 'document_templates/detail/'.$id;
            redirect($redirection);          
        } 
		else 
		{
            $valeurs		= $this->m_document_templates->detail($id);
            $listes_valeurs	= new stdClass();
			$valeur         = $this->input->post('tpl_nom');
			if (isset($valeur)) {
				$valeurs->tpl_nom	= $valeur;
            }
            $valeurs->tpl_content	= $this->input->post('tpl_content');

            $descripteur = array(
				'champs'  => $this->m_document_templates->get_champs('write'),
                'onglets' => array(),
            );
			
            $data = array(
                'title' 	 	 => "Modifier Template",
                'page' 			 => "templates/form",
                'menu' 			 => "Extra|Edit Document Template",
                'id' 			 => $id,
                'values' 		 => $valeurs,
                'action' 		 => "modif",
                'multipart' 	 => false,
                'confirmation' 	 => 'Enregistrer',
                'controleur' 	 => 'document_templates',
                'methode' 		 => 'modification',
                'descripteur' 	 => $descripteur,
				'listes_valeurs' => $listes_valeurs,				
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Detail
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
            $valeurs = $this->m_document_templates->detail($id);
            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'document_templates/modification', 'primary'),
                array("Archiver", 'document_templates/archive', 'warning'),
                array("Supprimer", 'document_templates/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
					'tpl_nom' 		=> array("Nom", 'VARCHAR 50', 'text', 'tpl_nom'),
					'tpl_content'	=> array("Content", 'TEXT', 'textarea', 'tpl_content'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Document Template",
                'page'         => "templates/detail",
                'menu'         => "Extra|Document Templates",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'document_templates',
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
		if ($this->input->method() != 'post') {
            die;
        }
		$redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_document_templates->archive($id);
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
            $this->my_set_action_response($ajax, true, "Template a été supprimé", 'info',$ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection); 
    }

    /******************************
     * Delete Owners Data
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
        $resultat = $this->m_document_templates->remove($id);

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
            $this->my_set_action_response($ajax, true, "Template a été supprimé", 'info',$ajaxData);
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
            $resultat = $this->m_document_templates->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_document_templates->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_document_templates->unremove($id);
        }
    }
	
	function getTemplate()
	{
		$template_id 	= $this->input->get('template_id');
		$template 		= $this->m_document_templates->detail($template_id);
		echo json_encode($template->tpl_content);
	}

    /******************************
     * Dupliquer Data
     ******************************/
    public function dupliquer($id, $ajax = false)
    {
        $resultat = $this->m_document_templates->dupliquer($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recordadd',
                    'id'         => $resultat,
                    'timeStamp'  => round(microtime(true) * 1000),
                ),
            );
            $this->my_set_action_response($ajax,true,"Document templates a été dupliquer", 'info', $ajaxData);
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }
}
// EOF