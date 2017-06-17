<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_permissions extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_users_permissions');
	}

	private $profil;
    private $barre_action = array(
        array(
            "Ajouter" => array('users_permissions/nouveau', 'plus', true, 'users_permissions_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('users_permissions/detail', 'eye-open', false, 'users_permissions_detail'),
            "Consulter/Modifier"  => array('users_permissions/modification', 'pencil', false, 'users_permissions_modification'),
            "Dupliquer" => array('users_permissions/dupliquer', 'duplicate', false, 'users_permissions_dupliquer',"Veuillez confirmer la duplique du user permissions", array('confirm-modify' => array('users_permissions/index'))),
            "Archiver" => array('users_permissions/archive', 'folder-close', false, 'users_permissions_archive',"Veuillez confirmer la archive du users permissions"),
            "Supprimer" => array('users_permissions/remove', 'trash', false, 'users_permissions_supprimer',"Veuillez confirmer la suppression du users permissions"),
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
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'users_permissions/index',
            'detail'             => array('users_permissions/detail', 'usp_id', 'description'),
            'archive'            => array('users_permissions/archive', 'usp_id', 'archive'),
            'champs'             => $this->m_users_permissions->get_champs('read'),
            'filterable_columns' => $this->m_users_permissions->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: users_permissions/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'users_permissions/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'users_permissions/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'users_permissions/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'users_permissions',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);

        $scripts[] = $this->load->view("users_permissions/liste-js", array(), true);
        $scripts[] = $this->load->view('users_permissions/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('users_permissions', $this->session->id);
        $data = array(
            'title'        => "Users Permissions",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Users Permissions",
            'scripts'      => $scripts,
			'controleur' 	=> 'users_permissions',
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
            $resultat = $this->m_users_permissions->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'usp_id' => 't_users_permissions',
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

                $resultat = $this->m_users_permissions->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_users_permissions->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_users_permissions->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Users Permissionss'
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

    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'usp_utilisateurs', 'label' => "Utilisateurs", 'rules' => 'trim|required'),
            array('field' => 'usp_table', 'label' => "Module", 'rules' => 'trim'),
            array('field' => 'usp_type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'usp_fields', 'label' => "Fields"),    
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {          
            $fields_array = $this->input->post('usp_fields', true) ? $this->input->post('usp_fields', true) : array();
            $usp_fields = implode(",", $fields_array);
            // validation réussie
            $valeurs = array(
                'usp_utilisateurs'	=> $this->input->post('usp_utilisateurs'),
                'usp_table'         => $this->input->post('usp_table'),
                'usp_type'          => $this->input->post('usp_type'),
                'usp_fields'        => $usp_fields,
            );

            $resultat = $this->m_users_permissions->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Users Permissions a été enregistré avec succès",'info',$ajaxData);
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

            $valeurs->usp_utilisateurs	= $this->input->post('usp_utilisateurs');
            $valeurs->usp_table         = $this->input->post('usp_table');
            $valeurs->usp_type          = $this->input->post('usp_type');
            $valeurs->usp_fields        = $this->input->post('usp_fields');

            $listes_valeurs->usp_utilisateurs = $this->m_users_permissions->utilisateurs_liste_option();
            $listes_valeurs->usp_table        = $this->m_users_permissions->table_liste_option();
            $listes_valeurs->usp_type         = $this->m_users_permissions->type_option();
            $listes_valeurs->usp_fields       = array();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_users_permissions->get_champs('write'),
                'onglets' => array(    
                ),
            );

            $data = array(
                'title' => "Ajouter un nouveau Users Permissions",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Users Permissions",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'users_permissions',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);           
        }
    }

    /******************************
     * Edit function for users_permissions Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'usp_utilisateurs', 'label' => "Utilisateurs", 'rules' => 'trim|required'),
            array('field' => 'usp_table', 'label' => "Module", 'rules' => 'trim'),
            array('field' => 'usp_type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'usp_fields', 'label' => "Fields"),       
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {
            $fields_array = $this->input->post('usp_fields', true) ? $this->input->post('usp_fields', true) : array();
            $usp_fields = implode(",", $fields_array);
            // validation réussie
            $valeurs = array(
                'usp_utilisateurs'	=> $this->input->post('usp_utilisateurs'),
                'usp_table'         => $this->input->post('usp_table'),
                'usp_type'         => $this->input->post('usp_type'),
                'usp_fields'        => $usp_fields,
            );

            $resultat = $this->m_users_permissions->maj($valeurs, $id);
            
            $redirection = 'users_permissions/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Users Permissions a été modifié";
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
            $valeurs = $this->m_users_permissions->detail($id);
            $listes_valeurs = new stdClass;

            $valeur = $this->input->post('usp_table');
            if (isset($valeur)) {
                $valeurs->usp_table = $valeur;
            }            
            $listes_valeurs->usp_utilisateurs = $this->m_users_permissions->utilisateurs_liste_option();
            $listes_valeurs->usp_table        = $this->m_users_permissions->table_liste_option();
            $listes_valeurs->usp_type        = $this->m_users_permissions->type_option();
            $listes_valeurs->usp_fields       = $this->m_users_permissions->field_liste_option($valeurs->usp_table,$valeurs->usp_type);

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_users_permissions->get_champs('write'),
                'onglets' => array(    
                ),
            );

            $data = array(
                'title' => "Modifier Users Permissions",
                'page' => "templates/form",
                'menu' => "Extra|Edit Users Permissions",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'users_permissions',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);            
        }
    }

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
            $valeurs = $this->m_users_permissions->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'users_permissions/modification', 'primary'),
                array("Archiver", 'users_permissions/archive', 'warning'),
                array("Supprimer", 'users_permissions/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    //PARAMETRES
                    'usp_utilisateurs'	=> array("Utilisateurs", 'VARCHAR 50', 'text', 'usp_utilisateurs'),
                    'usp_table'         => array("Module", 'VARCHAR 50', 'text', 'usp_table'),
                    'usp_fields'        => array("Fields", 'VARCHAR 50', 'text', 'usp_fields'),
                    
                ),
                'onglets' => array(
                ),
            );

            $data = array(
                'title'        => "Détail of Users Permissions",
                'page'         => "templates/detail",
                'menu'         => "Extra|Users Permissions",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'users_permissions',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    public function archive($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_users_permissions->archive($id);

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
            $this->my_set_action_response($ajax, true, "Users Permissions a été archivee", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    public function remove($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_users_permissions->remove($id);

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
            $this->my_set_action_response($ajax, true, "Users Permissions a été supprimé", 'info', $ajaxData);
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
        $redirection = 'users_permissions/detail/'.$id;
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_users_permissions->dupliquer($id);
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
              $this->my_set_action_response($ajax, true, "User permissions a été dupliquer", 'info',$ajaxData);
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
            $resultat = $this->m_users_permissions->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_users_permissions->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_users_permissions->unremove($id);
        }
    }

    public function fields_option($id = '', $type='')
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_users_permissions->field_liste_option($id, $type);

        $options = array();

        foreach($resultat as $row) {
            $options[] = array(
                'label' => $row->id,
                'title' => $row->value,
                'value' => $row->value
            );
        }

        echo json_encode($options);

        // $results  = json_decode(json_encode($resultat), true);

        // echo "<option value='0' selected='selected'>(choisissez)</option>";
        // foreach ($results as $row) {
        //     echo "<option value='" . $row['value'] . "'>" . $row['value'] . "</option>";
        // }
    }
	
	public function settings(){
		$listes_valeurs = new stdClass;
		$listes_valeurs->usp_utilisateurs = $this->m_users_permissions->utilisateurs_liste_option();
        $listes_valeurs->usp_table        = $this->m_users_permissions->table_liste_option();
        $listes_valeurs->usp_type         = $this->m_users_permissions->type_option();
        $listes_valeurs->usp_fields       = array();
		
		$scripts = array();
		$scripts[] = $this->load->view('users_permissions/settings-form-js',array(),true);
		
		$data = array(
				'title' => "Users Permissions Setting",
				'page' => "users_permissions/settings-form",
				'menu' => "Ventes|Détail Statistiques Prospection",
				//'id' => $id,
				'scripts' => $scripts,
				'values' => $listes_valeurs,
				'controleur' => 'users_permissions',
		);
		
		$layout = "layouts/standard";
        //$layout = "users_permissions/settings-form";
        $this->load->view($layout, $data);
	}
	
	public function get_permissions($id){		
		$data = $this->m_users_permissions->get_permissions($id);
		$permissions = array();
		$permissions['usp_utilisateurs'] = $id;
		
		if(is_array($data))
		{
			foreach($data as $module => $type){
				$read_field = $this->m_users_permissions->field_liste_option($module, 'read');
				$write_field = $this->m_users_permissions->field_liste_option($module, 'write');
				
				//combine write and read field array
				$fields = array();
				foreach($read_field as $row){
					$fields[] = $row->id;
					$permissions['data'][$module]['read'][] = $row->id;
				}
				foreach($write_field as $row){
					$fields[] = $row->id;
					$permissions['data'][$module]['write'][] = $row->id;
				}
			
				$fields = array_unique($fields);						
				$permissions['data'][$module]['fields'] = $fields;			
				$permissions['data'][$module]['user_read'] = $type['read'];
				$permissions['data'][$module]['user_write'] = $type['write'];
				
			}
		}
		
		$layout = 'users_permissions/table-module';
		$this->load->view($layout, $permissions);
	}
	
	public function update_permissions($id){		
		$this->m_users_permissions->update_permissions($id);
	}
}

/* End of file Users_permissions.php */
/* Location: .//tmp/fz3temp-1/Users_permissions.php */