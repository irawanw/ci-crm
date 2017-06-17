<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Cartes_blues extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('cartes_blues/nouveau', 'plus', true, 'cartes_blues_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('cartes_blues/detail', 'eye-open', false, 'cartes_blues_detail'),
            "Consulter/Modifier"  => array('cartes_blues/modification', 'pencil', false, 'cartes_blues_modification',null, array('form')),
            "Supprimer" => array('cartes_blues/remove', 'trash', false, 'cartes_blues_supprimer',"Veuillez confirmer la suppression du carte", array('confirm-delete' => array('cartes_blues/index'))),
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
        $this->load->model('m_cartes_blues');
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
            'datasource'         => 'cartes_blues/index',
            'detail'             => array('cartes_blues/detail', 'carte_id', 'description'),
            'archive'            => array('cartes_blues/archive', 'carte_id', 'archive'),
            'champs'             => $this->m_cartes_blues->get_champs('read'),
            'filterable_columns' => $this->m_cartes_blues->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: owners/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'cartes_blues/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'cartes_blues/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'cartes_blues/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'cartes_blues',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("cartes_blues/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('cartes_blues', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Cartes Bleues",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Cartes Bleues",
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_cartes_blues->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'carte_id' => 't_cartes_blues',
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

                $resultat = $this->m_cartes_blues->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_cartes_blues->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_cartes_blues->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Cartes_blues'
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
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'banque', 'label' => "Banque", 'rules' => 'trim|required'),
            array('field' => 'premiers_chiffres', 'label' =>  "Numéro de compte 4 premiers chiffres", 'rules' => 'trim|min_length[4]|max_length[4]|numeric|required'),
            array('field' => 'derniers_chiffres', 'label' =>  "Numéro de compte 4 derniers chiffres", 'rules' => 'trim|min_length[4]|max_length[4]|numeric|required'),
            array('field' => 'societe', 'label' =>  "Societe", 'rules' => 'trim'),
            array('field' => 'autre_que_societe', 'label' =>  "Autre que société", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'banque'			=> $this->input->post('banque'),
                'premiers_chiffres'	=> $this->input->post('premiers_chiffres'),
                'derniers_chiffres'	=> $this->input->post('derniers_chiffres'),
                'societe'           => $this->input->post('societe'),
                'autre_que_societe'			=> $this->input->post('autre_que_societe'),
            );

            $resultat = $this->m_cartes_blues->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Carte a été enregistré avec succès",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
           
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs               		= new stdClass();
            $listes_valeurs         	= new stdClass();
            $valeurs->banque			= $this->input->post('banque');
            $valeurs->premiers_chiffres	= $this->input->post('premiers_chiffres');
            $valeurs->derniers_chiffres	= $this->input->post('derniers_chiffres');
            $valeurs->societe           = $this->input->post('societe');
            $valeurs->autre_que_societe	= $this->input->post('autre_que_societe');
			$listes_valeurs->societe	= $this->m_cartes_blues->liste_societe();
            $scripts = array();

            // descripteur
            $descripteur = array(
				'champs'  => $this->m_cartes_blues->get_champs('write'),
                'onglets' => array(),
            );

         //   echo print_r($descripteur); die();

            $data = array(
                'title' => "Ajouter un nouveau",
                'page' => "templates/form",
                'menu' => "Extra|Cartes Bleues",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'cartes_blues',
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
            array('field' => 'banque', 'label' => "Banque", 'rules' => 'trim|required'),
            array('field' => 'premiers_chiffres', 'label' =>  "premiers_chiffres", 'rules' => 'trim|min_length[4]|max_length[4]|required'),
            array('field' => 'derniers_chiffres', 'label' =>  "derniers_chiffres", 'rules' => 'trim|min_length[4]|max_length[4]|required'),
            array('field' => 'societe', 'label' =>  "Societe", 'rules' => 'trim'),
            array('field' => 'autre_que_societe', 'label' =>  "Autre que société", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'banque'			=> $this->input->post('banque'),
                'premiers_chiffres'	=> $this->input->post('premiers_chiffres'),
                'derniers_chiffres'	=> $this->input->post('derniers_chiffres'),
                'societe'           => $this->input->post('societe'),    
                'autre_que_societe'			=> $this->input->post('autre_que_societe'),    
            );

            $resultat = $this->m_cartes_blues->maj($valeurs, $id);
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
			$redirection = 'cartes_blues/detail/'.$id;
            redirect($redirection);          
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs 				 = $this->m_cartes_blues->detail($id);
			$listes_valeurs          = new stdClass();
			$societe				 = $valeurs->societe ? $valeurs->societe : null;
			$listes_valeurs->societe = $this->m_cartes_blues->liste_societe();
			
            $scripts = array();
			$scripts[] = $this->load->view("cartes_blues/form-js",
                array('valeurs' => $valeurs, 'liste_societe' => $this->m_cartes_blues->liste_societe()), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_cartes_blues->get_champs('write'),
                'onglets' => array(),
            );
			
            $data = array(
                'title' 	 	 => "Modifier Carte",
                'page' 			 => "templates/form",
                'menu' 			 => "Extra|Edit Cartes Bleues",
                'scripts' 		 => $scripts,
                'id' 			 => $id,
                'values' 		 => $valeurs,
                'action' 		 => "modif",
                'multipart' 	 => false,
                'confirmation' 	 => 'Enregistrer',
                'controleur' 	 => 'cartes_blues',
                'methode' 		 => 'modification',
                'descripteur' 	 => $descripteur,
				'listes_valeurs' => $listes_valeurs,				
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
            $valeurs = $this->m_cartes_blues->detail($id);
            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'cartes_blues/modification', 'primary'),
                array("Archiver", 'cartes_blues/archive', 'warning'),
                array("Supprimer", 'cartes_blues/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
					'banque' 			=> array("Banque", 'VARCHAR 50', 'text', 'banque'),
					'premiers_chiffres'	=> array("Premiers_chiffres", 'VARCHAR 50', 'text', 'premiers_chiffres'),
					'derniers_chiffres' => array("Derniers_chiffres", 'VARCHAR 50', 'text', 'derniers_chiffres'),
                    'societe'           => array("Societe", 'VARCHAR 50', 'text', 'societe'),
					'autre_que_societe' => array("Autre que société", 'VARCHAR 50', 'text', 'autre_que_societe'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Cartes Bleues",
                'page'         => "templates/detail",
                'menu'         => "Extra|Cartes Bleues",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'cartes_blues',
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
        $resultat = $this->m_cartes_blues->archive($id);
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
            $this->my_set_action_response($ajax, true, "Carte a été supprimé", 'info',$ajaxData);
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
        $resultat = $this->m_cartes_blues->remove($id);

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
            $this->my_set_action_response($ajax, true, "Carte a été supprimé", 'info',$ajaxData);
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
            $resultat = $this->m_cartes_blues->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_cartes_blues->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_cartes_blues->unremove($id);
        }
    }
}
// EOF
