<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Newtournee extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('newtournee/create','plus',true,'tournee_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('newtournee/detail','eye-open',false,'tournee_detail',null,array('view')),
                    "Modifier" => array('newtournee/modification','pencil',false,'tournee_modification',null,array('form')),
                    "Supprimer" => array('newtournee/suppression','trash',false,'tournee_supprimer','Confirmez la suppression de la tournée', array('confirm-delete')),
            ),
            /*array(
                    "Export Excel" => array('#','list-alt',false,'export_excel'),
					"Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),	*/
            array(
                "VIGIK" => array('newvigik/index[]','hand-right',true,'newvigik_detail'),
                "Bornes" => array('newbornes/index[]','book',true,'newbornes_detail'),
            ),
            array(
                "Adresses de livraison" => array('newadresse/index[]','home',true,'adresse_detail'),
                "Tournées" => array('newtournee/index[]','map-marker',true,'tournee_detail'),
                "Tournées journalières" => array('newtournee_journalieres/index[]','calendar',true,'tourneejourn_detail'),
            ),
        ),
        "Element" => array(
            array(
                    "Consulter" => array('newtournee/detail','eye-open',true,'tournee_detail',null,array('view')),
                    "Modifier" => array('newtournee/modification','pencil',true,'tournee_modification',null,array('form')),
                    "Supprimer" => array('newtournee/suppression','trash',true,'tournee_supprimer','Confirmez la suppression de la tournée', array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_newtournee');
    }

    /******************************
     * List of owners Data
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
    * Liste des newtournee
    ******************************/
    public function liste($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
           // array("Ajouter une tournee","newtournee/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newtournee/index',
            'detail' => array('newtournee/detail','tournee_id','tournee_numero'),
            'champs' => array(
                array('sno','text',"S.No"),
                array('tournee_numero','text',"Numero"),
                array('tournee_nom','text',"Nom de la tournée"),
				array('emp_nom','ref',"livreur",'t_employes'),
				array('remarques','text',"Remarques")
				
            ),
            'filterable_columns' => $this->m_newtournee->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'newtournee',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'newtournee',
                ),
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newtournee',$this->session->id);
		
        $data = array(
            'title' => "Liste des Tournee",
            'page' => "templates/datatables-wtoolbar",
            'menu' => "Vigik|Tournee",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
		
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
		
    }
	  public function index_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_newtournee->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tournee_numero' => 't_tourneevigik'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newtournee->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newtournee->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }


	
	public function create($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(

            array('field'=>'trn_numero','label'=>"Numero",'rules'=>'trim|required'),
            array('field'=>'trn_nom','label'=>"Nom de la tournée",'rules'=>'trim|required'),
            array('field'=>'trn_livreur','label'=>"Livreur",'rules'=>'trim|required'),
            array('field'=>'trn_remarques','label'=>"Remarques",'rules'=>'trim|required')
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {
            // validation réussie
            $valeurs = array(
                'tournee_numero' => $this->input->post('trn_numero'),
				
                'tournee_nom' => $this->input->post('trn_nom'),

                'livreur' => $this->input->post('trn_livreur'),

                'remarques' => $this->input->post('trn_remarques')
            );

            $id = $this->m_newtournee->form($valeurs);

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
                $this->my_set_action_response($ajax, true, "La tournée a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection); 
        }

        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();

            $valeurs->trn_numero = $this->input->post('trn_numero');
            $valeurs->trn_nom = $this->input->post('trn_nom');
            $valeurs->trn_livreur = $this->input->post('trn_livreur');
            $valeurs->trn_remarques = $this->input->post('trn_remarques');

            $listes_valeurs->trn_livreur = $this->m_newtournee->employe_list($valeurs->trn_livreur);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'trn_numero' => array("Numero De La Tournee", 'text', 'trn_numero', false),
                    'trn_nom' => array("Nom De La Tournee", 'text', 'trn_nom', false),
                    'trn_livreur' => array("Livreur", 'select', array('trn_livreur','emp_id','emp_nom'), false),
                    'trn_remarques' => array("Remarques", 'textarea', 'trn_remarques', false),              
                ),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Ajouter un nouveau Tournee",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Tournee",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newtournee',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax,$data);
		}

    }

    /******************************
    * Détail d'un tournee
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_newtournee->detail($id);

            // commandes globales
            $cmd_globales = array(
            //    array("Articles",'articles/articles_cat','default'),
            //    array("Import articles",'newtournee/importation','default'),
            //    array("Export articles",'newtournee/exportation','default')
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'newtournee/modification','primary'),
            //    array("Supprimer",'newtournee/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tournee_numero' => array("Numero",'VARCHAR 30','text','tournee_numero'),
                   'tournee_nom' => array("Nom de la tournée",'VARCHAR 30','text','tournee_nom'),
                   'livreur' => array("Employee",'VARCHAR 30','text','emp_nom'),
                   'remarques' => array("Remarques",'VARCHAR 30','text','remarques')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un Tournee",
                'page' => "templates/detail",
                'menu' => "Vigik|Tournee",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newtournee',
                'methode' => 'detail',
               // 'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }
	 public function employe_detail($id) {
		
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
			
            $valeurs = $this->m_newtournee->employe_detail($id);

            // commandes globales
           /* $cmd_globales = array(
                array("Articles",'articles/articles_cat','default'),
                array("Import articles",'newtournee/importation','default'),
                array("Export articles",'newtournee/exportation','default')
            );*/

            // commandes locales
            

            // descripteur
            

            $data = array(
                'title' => "Détail d'un Tournee",
                'page' => "templates/detail",
                'menu' => "Vigik|Tournee",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newtournee',
                'methode' => 'employe_detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $layout="layouts/standard";
            $this->load->view($layout,$data);
        }
    }

    /******************************
    * Mise à jour d'un tournee
    ******************************/
    public function modification($id=0,$ajax=false) {
        
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
			  array('field'=>'trn_numero','label'=>"Numero",'rules'=>'trim|required'),
			  array('field'=>'trn_nom','label'=>"Nom de la tournée",'rules'=>'trim|required'),
              array('field'=>'trn_livreur','label'=>"Livreur",'rules'=>'trim|required'),
              array('field'=>'trn_remarques','label'=>"Remarques",'rules'=>'trim|required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(		
                'tournee_numero' => $this->input->post('trn_numero'),
                'tournee_nom' => $this->input->post('trn_nom'),
                'livreur' => $this->input->post('trn_livreur'),
                'remarques' => $this->input->post('trn_remarques')

            );

            $resultat = $this->m_newtournee->editform($valeurs,$id);

            $redirection = 'newtournee/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "La Tournée a été modifié";
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
        }
        else {

            $valeurs = $this->m_newtournee->edit_detail($id);
            $listes_valeurs = new stdClass();
			
			if($this->input->post('trn_numero')!=""){
                $valeurs->trn_numero = $this->input->post('trn_numero');
			}
			else{
			     $valeurs->trn_numero = $valeurs->tournee_numero;
			}
			if($this->input->post('trn_nom')!=""){
                $valeurs->trn_nom = $this->input->post('trn_nom');
			}
			else{
			     $valeurs->trn_nom = $valeurs->tournee_nom;
			}
			
			if($this->input->post('trn_livreur')!=""){
                $valeurs->trn_livreur = $this->input->post('trn_livreur');
			}
			else{
			     $valeurs->trn_livreur = $valeurs->livreur;
			}
			
			if($this->input->post('trn_remarques')!=""){
                $valeurs->trn_remarques = $this->input->post('trn_remarques');
			}
			else{
			     $valeurs->trn_remarques = $valeurs->remarques;
			}
			
		
			$listes_valeurs->trn_livreur = $this->m_newtournee->employe_list($valeurs->trn_livreur);

            
            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'trn_numero' => array("Numero De La Tournee", 'text', 'trn_numero', false),
                    'trn_nom' => array("Nom De La Tournee", 'text', 'trn_nom', false),
                    'trn_livreur' => array("Livreur", 'select', array('trn_livreur','emp_id','emp_nom'), false),
                    'trn_remarques' => array("Remarques", 'textarea', 'trn_remarques', false),              
                ),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Modifier Tournee",
                'page' => "templates/form",
                'menu' => "Extra|Edit Tournee",
                'id' => $id,
                'values' => $valeurs,
                'listes_valeurs' => $listes_valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newtournee',
                'methode' => 'modification',
                'descripteur' => $descripteur,                
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un tournee
    ******************************/
    public function suppression($id,$ajax=false) {

        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_newtournee->suppression($id);

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
            $this->my_set_action_response($ajax, true, "La tournée a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    
}

// EOF
