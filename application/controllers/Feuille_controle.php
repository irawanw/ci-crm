<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * Property M_feuille_controle m_feuille_controle
 */
class Feuille_controle extends MY_Controller
{
    private $profil;
	private $default_profil = 'Administrateur';
    private $barre_action = array(
	    'Administrateur' => array(
			array(
				"Creer un<br>controle distribution"  => array('#', 'plus', true, 'create_controle_distribution'),
				"Valider<br>controle distribution"   => array('#', 'ok', true, 'valider_controle_distribution'),
				"Devalider<br>controle distribution" => array('#', 'remove', true, 'devalider_controle_distribution'),
			),
			array(
				"Liste Contrôle de Distributions<br>consulter ou modifier" => array('#', 'th-list', true, 'liste_controle_distribution'),
			),
			array(
				"Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
				"Export pdf"  => array('#', 'book', true, 'export_pdf'),
				"Imprimer"    => array('#', 'print', true, 'print_list'),
			),
		),
        'Client' => array(
			array(
				"Liste Contrôle de Distributions<br>consulter ou modifier" => array('#', 'th-list', true, 'liste_controle_distribution'),
			),
			array(
				"Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
				"Export pdf"  => array('#', 'book', true, 'export_pdf'),
				"Imprimer"    => array('#', 'print', true, 'print_list'),
			),
        ),
        'Group_nonvalid' => array(
            array(
                "Ajouter<br> un document" => array('feuille_controle/nouveau', 'plus', true, 'group_feuille_controle_ajouter', null, array('form')),
            ),
            array(
                "Consulter/Modifier<br> un document"  => array('feuille_controle/modification', 'pencil', false, 'group_feuille_controle_modification',null, array('form')),
            ),
            array(
                "Supprimer<br> un document" => array('feuille_controle/remove', 'trash', false, 'group_feuille_controle_supprimer',"Veuillez confirmer la suppression du contrôle distribution", array('confirm-delete' => array('feuille_controle/group'))),
            ),
            array(
                "Voir<br> la liste" => array('#', 'th-list', true, 'feuille_controle_voir_liste'),
            ),
        ),
        "Group_valid" => array(
            array(
                "Voir<br> la liste" => array('#', 'th-list', true, 'feuille_controle_voir_liste'),
            ),
        ),	
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_feuille_controle');
    }
	
    /**
     * get barre_action by profile user logged in
     * @return Array [barre acton toolbar]
     */
    public function get_barre_action()
    {
        //get profil user logged in
        $profil       = $this->session->profil;
        $barre_action = '';

        switch ($profil) {
            case 'Client':
                $barre_action = $this->barre_action[$profil];
                break;
            default:
                $barre_action = $this->barre_action[$this->default_profil];
                break;
        }

        return $barre_action;
    }	

    /******************************
     * List of Group Controle Distribution Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste();
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archived');
    }

    public function deleted()
    {
        $this->liste($id = 0, 'deleted');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    public function view_group()
    {
        $this->liste_group();
    }

    public function group($group_name = null, $option = '')
    {
        //we will list valider so lets set mode to "valider"
        //option is used to view mode in current group for eg: 12-October-2016/deleted
        //means we listed 12-October-2016 which is already deleted
        $id = 0;
        if($option != '') {
            $id = $option;
        }
        $this->liste_group($id.'/'.urldecode($group_name), 'group');
    }

    public function liste($id = 0, $mode = "")
    {
        // commandes globales
        $cmd_globales = array(

        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'feuille_controle/view_group',
            'detail'             => array('feuille_controle/detail', 'feuille_controle_group_id', 'description'),
            'archive'            => array('feuille_controle/archive', 'feuille_controle_group_id', 'archive'),
            'champs'             => $this->m_feuille_controle->get_champs('read','parent'),
            'filterable_columns' => $this->m_feuille_controle->liste_group_filterable_columns(),
        );

        //for eg: feuille_controle/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archived':
                $descripteur['datasource'] = 'feuille_controle/view_group_archived';
                break;
            case 'deleted':
                $descripteur['datasource'] = 'feuille_controle/view_group_deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'feuille_controle/view_group_all';
                break;
            default:
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $external_toolbar_data = array(
            'is_liste_group' => TRUE
        );

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuille_controle',
                'methode'               => 'index',
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => $external_toolbar_data,
            ), true);

        $scripts[] = $this->load->view("feuille_controle/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuille_controle', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Contrôle de Distributions",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Amalgame",
            'scripts'      => $scripts,
            'barre_action' => $this->get_barre_action(), //enable sage bar action
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

    public function liste_group($id = 0, $mode = 0)
    {
        $this->load->model(array('m_utilisateurs', 'm_contacts'));
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","feuille_controle/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'feuille_controle/group',
            'detail'             => array('feuille_controle/detail', 'feuille_controle_id', 'description'),
            'archive'            => array('feuille_controle/archive', 'feuille_controle_id', 'archive'),
            'champs'             => $this->m_feuille_controle->get_champs('read','child'),
            'filterable_columns' => $this->m_feuille_controle->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: feuille_controle/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'feuille_controle/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'feuille_controle/deleted';
                break;
            case 'group':
                $descripteur['datasource'] = 'feuille_controle/group';
                break;
            case 'all':
                $descripteur['datasource'] = 'feuille_controle/all';
                break;
        }

        $is_group_valid = $this->m_feuille_controle->is_group_valid($this->uri->segment(3));
        $barre_action   =  $is_group_valid == 0 ? $this->barre_action['Group_nonvalid'] : $this->barre_action['Group_valid']; 

        $external_toolbar_data = array(
            'controleur'       => 'feuille_controle',
            'list_client'      => $this->m_feuille_controle->list_client(),
            'list_valides'     => $this->m_feuille_controle->get_valider_name(true),
            'list_non_valides' => $this->m_feuille_controle->get_valider_name(false),
            'group_valid'      => $is_group_valid,
            'barre_action'     => $barre_action
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuille_controle',
                'methode'               => 'index',
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => $external_toolbar_data,
            ), true);
        $scripts[] = $this->load->view("feuille_controle/liste-group-js", array(
            'group_valid' => $this->m_feuille_controle->is_group_valid($this->uri->segment(3))
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuille_controle', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Contrôle de Distributions",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Feuille_controle",
            'scripts'      => $scripts,
            'barre_action' => $this->get_barre_action(), //enable sage bar action
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

    public function view_group_json($id = 0, $group_name = '')
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
            $resultat = $this->m_feuille_controle->liste_group($id, $pagelength, $pagestart, $filters, $group_name);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'feuille_controle_id' => 't_feuille_controle_group',
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

                $resultat = $this->m_feuille_controle->liste_group($id, $pagelength, $pagestart, $filters, $group_name, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuille_controle->liste_group($id, $pagelength, $pagestart, $filters, $group_name);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_feuille_controle->get_champs('read','parent');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Feuille_controle_groups'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
     * Ajax call for Livraison List
     ******************************/
    public function index_json($id = 0, $group = '')
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
            $resultat = $this->m_feuille_controle->liste($id, $pagelength, $pagestart, $filters, $group);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'feuille_controle_id' => 't_feuille_controle',
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

                $resultat = $this->m_feuille_controle->liste($id, $pagelength, $pagestart, $filters, $group, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuille_controle->liste($id, $pagelength, $pagestart, $filters, $group);
            }
        }
        if($this->input->post('export')) {
            //action export data xls
            $group_name = $this->uri->segment(4);
            $group_name = str_replace("_", " ", $group_name);
            $champs = $this->m_feuille_controle->get_champs('read','child');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Feuille_controles',
                'headers' => array(
                    array('text' => 'Liste des Contrôle distribution'),
                    array('text' => $group_name),
                )
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

    public function group_json($id = 0, $group = null)
    {
        $this->index_json($id, $group);
    }

    public function view_group_archived_json($id = 0)
    {
        $this->view_group_json('archived');
    }

    public function view_group_deleted_json($id = 0)
    {
        $this->view_group_json('deleted');
    }

    public function view_group_all_json($id = 0)
    {
        $this->view_group_json('all');
    }
    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id=0,$group_name = null, $ajax=false)
    {
        if ($group_name == null) {
            $this->session->set_flashdata('warning', "No Selected Contrôle De Distribution");
            redirect('feuille_controle');
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
			// array('field' => 'heure_de_debut', 'label' => "Heure de début", 'rules' => 'trim'),
			// array('field' => 'heure_de_fin', 'label' => "Heure de fin", 'rules' => 'trim'),
            array('field' => 'numero', 'label' => "Numero", 'rules' => 'trim|required'),
            array('field' => 'rue', 'label' => "Rue", 'rules' => 'trim'),
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'resultat', 'label' => "Resultat", 'rules' => 'trim'),
            array('field' => 'commentaire', 'label' => "Commentaires", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $resultat_group = $this->m_feuille_controle->get_group(array('name' => $group_name));

        if (!$resultat_group) {
            $this->session->set_flashdata('warning', "No Found Contrôle De Distribution");
            redirect('feuille_controle');
        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            // validation réussie
            $valeurs = array(
				// 'heure_de_debut'         	=> $this->input->post('heure_de_debut'),
				// 'heure_de_fin'             	=> $this->input->post('heure_de_fin'),
                'numero'                    => $this->input->post('numero'),
                'rue'                       => $this->input->post('rue'),
                'ville'                     => $this->input->post('ville'),
                'resultat'                  => $this->input->post('resultat'),
                'commentaire'               => $this->input->post('commentaire'),
                'feuille_controle_group_id' => $resultat_group->feuille_controle_group_id,
            );

            $id = $this->m_feuille_controle->nouveau($valeurs);

            if($id === false) {
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
                $this->my_set_action_response($ajax, true, "Contrôle de Distribution a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);

            // if ($id === false) {
            //     if (null === $this->session->flashdata('danger')) {
            //         $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            //     }
            //     $redirection = $this->session->userdata('_url_retour');
            //     if (!$redirection) {
            //         $redirection = '';
            //     }

            //     redirect($redirection);
            // } else {
            //     $this->session->set_flashdata('success', "Contrôle de Distribution a été enregistré avec succès");
            //     $redirection = $this->session->userdata('_url_retour');
            //     if (!$redirection) {
            //         $redirection = '';
            //     }
                
            //     redirect($redirection);               
            // }
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
			// $valeurs->heure_de_debut    = $this->input->post('heure_de_debut');
			// $valeurs->heure_de_fin      = $this->input->post('heure_de_fin');
            $valeurs->numero      		= $this->input->post('numero');
            $valeurs->rue         		= $this->input->post('rue');
            $valeurs->ville       		= $this->input->post('ville');
            $valeurs->resultat    		= $this->input->post('resultat');
            $valeurs->commentaire 		= $this->input->post('commentaire');

            //liste option
            $listes_valeurs           = new stdClass();
            $listes_valeurs->resultat = $this->m_feuille_controle->resultat_liste_option();

            $scripts   = array();
            $scripts[] = $this->load->view('feuille_controle/form-js', array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_feuille_controle->get_champs('write','child'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Contrôle de Distribution",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Feuille_controle",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'feuille_controle',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);

            // $layout = "layouts/standard";
            // $this->load->view($layout, $data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0, $ajax)
    {
        $addressline = $this->m_feuille_controle->detail($id);
        if ($this->m_feuille_controle->is_valides($id)) {

            $this->session->set_flashdata('danger', "Attention pour modifier ce controle vouz devez le devalider");
            if ($addressline->group_name != '') {
                $mode       = 'group';
                $group_name = $addressline->group_name;
            }

            redirect('feuille_controle/' . $mode . '/' .$group_name);
        }

        $this->load->model(array('m_contacts', 'm_utilisateurs'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
			// array('field' => 'heure_de_debut', 'label' => "Numero", 'rules' => 'trim'),
			// array('field' => 'heure_de_fin', 'label' => "Numero", 'rules' => 'trim'),
            array('field' => 'numero', 'label' => "Numero", 'rules' => 'trim|required'),
            array('field' => 'rue', 'label' => "Rue", 'rules' => 'trim'),
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'resultat', 'label' => "Resultat", 'rules' => 'trim'),
            array('field' => 'commentaire', 'label' => "Commentaires", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
				// 'heure_de_debut' => $this->input->post('heure_de_debut'),
				// 'heure_de_fin' => $this->input->post('heure_de_fin'),
                'numero'      => $this->input->post('numero'),
                'rue'         => $this->input->post('rue'),
                'ville'       => $this->input->post('ville'),
                'resultat'    => $this->input->post('resultat'),
                'commentaire' => $this->input->post('commentaire'),
            );

            $resultat = $this->m_feuille_controle->maj($valeurs, $id);

            $redirection = 'feuille_controle/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Contrôle de Distribution a été modifié";
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
            
            // if ($resultat === false) {
            //     if (null === $this->session->flashdata('danger')) {
            //         $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            //     }
            //     redirect('feuille_controle/detail/' . $id);
            // } else {
            //     if ($resultat == 0) {
            //         $message = "Pas de modification demandée";
            //     } else {
            //         $message = "Contrôle de Distribution a été modifié";
            //     }

            //     $this->session->set_flashdata('success', $message);

            //     if ($addressline->feuille_controle_group_id != '') {
            //         redirect('feuille_controle/group/' . $addressline->group_name);
            //     }

            //     redirect('feuille_controle');
            // }
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_feuille_controle->data_for_form($id);

            //liste option
            $listes_valeurs           = new stdClass();
            $listes_valeurs->resultat = $this->m_feuille_controle->resultat_liste_option();

            $scripts   = array();
            $scripts[] = $this->load->view('feuille_controle/form-js', array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_feuille_controle->get_champs('write','child'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier Contrôle de Distribution",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Feuille_controle",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'feuille_controle',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax,$data);
            // $layout = "layouts/standard";
            // $this->load->view($layout, $data);
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
            $valeurs      = $this->m_feuille_controle->detail($id);
            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'feuille_controle/modification', 'primary'),
                array("Archiver", 'feuille_controle/archive', 'warning'),
                array("Supprimer", 'feuille_controle/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'date_du_controle' => array("Date du Controle", 'VARCHAR 50', 'text', 'date_du_controle'),
                    'group_name'       => array("Contrôle de Distribution Nom", 'VARCHAR 50', 'text', 'group_name'),
                    'controleur_name'  => array("Controleur", 'VARCHAR 50', 'text', 'controleur_name'),
                    'client_name'      => array("Client", 'VARCHAR 50', 'text', 'client_name'),
                    'devis_name'       => array("Devis", 'VARCHAR 50', 'text', 'devis_name'),
                    'facture_name'     => array("Facture", 'VARCHAR 50', 'text', 'facture_name'),
                    'numero'           => array("Numero", 'VARCHAR 50', 'text', 'numero'),
                    'rue'              => array("Rue", 'VARCHAR 50', 'text', 'rue'),
                    'ville'            => array("Ville", 'VARCHAR 50', 'text', 'ville'),
                    'resultat'         => array("Resultat", 'VARCHAR 50', 'text', 'resultat'),
                    'commentaire'      => array("Commentaires", 'VARCHAR 50', 'text', 'commentaire'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Contrôle de Distribution",
                'page'         => "templates/detail",
                'menu'         => "Extra|Feuille_controle",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'feuille_controle',
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
        $resultat = $this->m_feuille_controle->archive($id);
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
            $this->session->set_flashdata('success', "Contrôle de Distribution a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Feuille_controle Data
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

        $resultat = $this->m_feuille_controle->remove($id);

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
            $this->my_set_action_response($ajax, true, "Contrôle de Distribution a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_feuille_controle->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuille_controle->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuille_controle->unremove($id);
        }
    }

    /**
     * get controleur list for options
     * @return [type] [description]
     */
    public function controleur_option()
    {
        $this->load->model('m_utilisateurs');
        $resultat = $this->m_utilisateurs->liste_option();
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='' selected='selected'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['utl_id'] . "'>" . $row['utl_nom'] . "</option>";
        }
    }

    /**
     * get client list for options
     * @return [type] [description]
     */
    public function client_option()
    {
        $this->load->model('m_contacts');
        $resultat = $this->m_contacts->liste_option();
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='' selected='selected'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    /**
     * get devis list for options
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function devis_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_feuille_controle->liste_option_devis_by_client($id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='' selected='selected'>(choisissez)</option>";
        echo "<option value=-1 >aucun</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    /**
     * get facture list for options
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function factures_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_feuille_controle->liste_option_factures_by_devis($id);
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='' selected='selected'>(choisissez)</option>";
        echo "<option value=-1 >aucune</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    /**
     * get client list for options
     * @return [type] [description]
     */
    public function resultat_option()
    {
        $resultat = $this->m_feuille_controle->resultat_liste_option();
        $results  = json_decode(json_encode($resultat), true);

        echo "<option value='' selected='selected'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    /**
     * set new controle distribution
     */
    public function set_controle_distribution()
    {
        $controle_distribution = $this->input->post('controle_distribution_name');        
        $this->m_feuille_controle->set_controle_distribution();
        $this->session->set_flashdata('success', "Feuille Controles have been successfully saved as " . $controle_distribution);
        redirect('feuille_controle/group/' . $controle_distribution);
    }

    /**
     * get controle distribution list
     * @param  [type] $client [description]
     * @param  [type] $mode   [description]
     * @return [type]         [description]
     */
    public function get_group($client, $mode)
    {
        if ($mode == 'valides') {
            $criteria = array('client' => $client, 'valid' => true);
            $res = $this->m_feuille_controle->list_option_group($criteria);
        } else {
            $criteria = array('client' => $client, 'valid' => false);
            $res = $this->m_feuille_controle->list_option_group($criteria);
        }

        $option = '<option value="">(choisissez)</option>';

        if (is_array($res)) {
            foreach ($res as $row) {
                if ($row != '') {
                    $option .= '<option value="' . site_url('feuille_controle') . '/group/' . $row->name . '">' . $row->name . '</option>';
                }

            };
        }

        echo $option;
    }

    /******************************
     * Valider-Devalider Contrôle de Distribution
     ******************************/
    public function set_valider($valider_name = null)
    {        
        if($valider_name == null) {
            redirect('feuille_controle');
        } else {
            $valider_name = urldecode($valider_name);
            $data = array('name' => $valider_name);            
            $resultat = $this->m_feuille_controle->valider($data);

            if($resultat)
                $this->session->set_flashdata('success', "Contrôle de Distribution ont été validées avec succès comme " . $valider_name);
            
            redirect('feuille_controle/group/'.$valider_name);
        }
    }
    public function unset_valider($valider_name = null)
    {
        if($valider_name == null) {
            redirect('feuille_controle');
        } else {
            $valider_name = urldecode($valider_name);
            $data = array('name' => $valider_name);

            $resultat = $this->m_feuille_controle->revalider($data);
            
            if($resultat)
                $this->session->set_flashdata('success', "Contrôle de Distribution n'ont pas été validées comme as " . $valider_name);

            redirect('feuille_controle/group/'.$valider_name);
        }
    }
    /******************************
     * Mass Archiver Group Data
     ******************************/
    public function mass_archiver_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuille_controle->archive_group($id);
        }
    }
    /**
     * Mass Action Remove Group
     */
    public function mass_remove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuille_controle->remove_group($id);
        }
    }
    /**
     * Mass Action Unremove Group
     */
    public function mass_unremove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuille_controle->unremove_group($id);
        }
    }
}
// EOF
