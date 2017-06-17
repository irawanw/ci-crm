<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Newbornes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('newbornes/create','plus',true,'bornes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('newbornes/detail','eye-open',false,'bornes_detail',null,array('view')),
                    "Modifier" => array('newbornes/modification','pencil',false,'bornes_modification',null,array('form')),
                    "Supprimer" => array('newbornes/suppression','trash',false,'bornes_supprimer','Confirmez la suppression de la borne', array('confirm-delete')),
            ),
           /* array(
                    "Export Excel" => array('#','remove',false,'export_excel'),
					"Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),*/
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
                    "Consulter" => array('newbornes/detail','eye-open',true,'bornes_detail',null,array('view')),
                    "Modifier" => array('newbornes/modification','pencil',true,'bornes_modification',null,array('form')),
                    "Supprimer" => array('newbornes/suppression','trash',true,'bornes_supprimer','Confirmez la suppression de la borne', array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_newbornes');
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
    * Liste des newbornes
    ******************************/
    public function liste($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une bornes","newbornes/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newbornes/index',
            'detail' => array('newbornes/detail','bornes_id','borne_numero'),
            'champs' => array(
                array('sno','text',"S.No"),
                array('borne_numero','text',"Numéro"),
				array('scv_nom','ref',"Société",'t_societes_vendeuses'),
                array('bornes_adresse','text',"Adresse")
				
            ),
            'filterable_columns' => $this->m_newbornes->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'newbornes',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newbornes',$this->session->id);
		
        $data = array(
            'title' => "Liste des Bornes",
            'page' => "templates/datatables-wtoolbar",
            'menu' => "Vigik|bornes",
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
            $resultat = $this->m_newbornes->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'borne_numero' => 't_bornes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newbornes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newbornes->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

	
	public function create($id=0,$ajax=false) {
      $cmd_globales = array(
        //    array("Nouvelle Bornes","newbornes/create",'default')
        );
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');


        // règles de validation

        $config = array(
            array('field'=>'brn_numero','label'=>"Numero",'rules'=>'trim|required'),

            array('field'=>'brn_societe','label'=>"Societe",'rules'=>'trim|required')
        );

        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {
            // validation réussie

            $valeurs = array(
                'borne_numero' => $this->input->post('brn_numero'),
                'societe' => $this->input->post('brn_societe'),
                'bornes_adresse' => $this->input->post('brn_adresse')
            );

            $id = $this->m_newbornes->bornes_form($valeurs);

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
                $this->my_set_action_response($ajax, true, "La borne a été enregistré avec succès",'info',$ajaxData);
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

            $valeurs->brn_numero = $this->input->post('brn_numero');
            $valeurs->brn_societe = $this->input->post('brn_societe');
            $valeurs->brn_adresse = $this->input->post('brn_adresse');
            $soc_id = $this->m_newbornes->society_list($valeurs->brn_societe);

            $listes_valeurs->brn_societe = $this->m_newbornes->society_list($valeurs->brn_societe);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'brn_numero' => array("Numero", 'text', 'brn_numero', false),
                    'brn_societe' => array("Societe", 'select', array('brn_societe','scv_id','scv_nom'), false),
                    'brn_adresse' => array("Adresse", 'textarea', 'brn_adresse', false),              
                ),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Ajouter un nouveau Borne",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Borne",              
                'values' => $valeurs,
                'listes_valeurs' => $listes_valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newbornes',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax,$data);

		}

    }

    /******************************
    * Détail d'un bornes
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_newbornes->detail($id);

            // commandes globales
          /*  $cmd_globales = array(
                array("Articles",'articles/articles_cat','default'),
                array("Import articles",'newbornes/importation','default'),
                array("Export articles",'newbornes/exportation','default')
            );
			*/

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'newbornes/modification','primary'),
            //    array("Supprimer",'newbornes/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'borne_numero' => array("Numero",'VARCHAR 30','text','borne_numero'),
                   'societe' => array("Societe",'VARCHAR 30','text','scv_nom'),
                   'bornes_adresse' => array("Adresse",'VARCHAR 30','text','bornes_adresse')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un bornes",
                'page' => "templates/detail",
                'menu' => "Produits|bornes",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newbornes',
                'methode' => 'detail',
               // 'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }
	 public function societe_detail($id) {
		
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
			
            $valeurs = $this->m_newbornes->societe_detail($id);

            // commandes globales
            $cmd_globales = array(
                array("Articles",'articles/articles_cat','default'),
                array("Import articles",'newbornes/importation','default'),
                array("Export articles",'newbornes/exportation','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier",'newbornes/modification','primary',($valeurs->cat_etat == 'futur')),
                array("Supprimer",'newbornes/suppression','danger',($valeurs->cat_etat == 'futur'))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'scv_nom' => array("Nom",'VARCHAR 30','text','Nom')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un Societe",
                'page' => "templates/detail",
                'menu' => "Produits|bornes",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newbornes',
                'methode' => 'societe_detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $layout="layouts/standard";
            $this->load->view($layout,$data);
        }
    }

    /******************************
    * Mise à jour d'un bornes
    ******************************/
    public function modification($id=0,$ajax=false) {
        
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
			  array('field'=>'brn_numero','label'=>"Numero",'rules'=>'trim|required'),
              array('field'=>'brn_societe','label'=>"Societe",'rules'=>'trim|required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(		
                'borne_numero' => $this->input->post('brn_numero'),
                'societe' => $this->input->post('brn_societe'),
                'bornes_adresse' => $this->input->post('brn_adresse')
            );

            $resultat = $this->m_newbornes->bornes_editform($valeurs,$id);

            $redirection = 'newbornes/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "La Borne a été modifié";
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

            $valeurs = $this->m_newbornes->edit_detail($id);
            $listes_valeurs = new stdClass();
			
			if($this->input->post('brn_numero')!=""){
                $valeurs->brn_numero = $this->input->post('brn_numero');
			}
			else{
			     $valeurs->brn_numero = $valeurs->borne_numero;
			}
			
			if($this->input->post('brn_societe')!=""){
                $valeurs->brn_societe = $this->input->post('brn_societe');
			}
			else{
			     $valeurs->brn_societe = $valeurs->societe;
			}
			
			if($this->input->post('brn_adresse')!=""){
                $valeurs->brn_adresse = $this->input->post('brn_adresse');
			}
			else{
			     $valeurs->brn_adresse = $valeurs->bornes_adresse;
			}
		
			$listes_valeurs->brn_societe = $this->m_newbornes->society_list($valeurs->brn_societe);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'brn_numero' => array("Numero", 'text', 'brn_numero', false),
                    'brn_societe' => array("Societe", 'select', array('brn_societe','scv_id','scv_nom'), false),
                    'brn_adresse' => array("Adresse", 'textarea', 'brn_adresse', false),              
                ),
                'onglets' => array(),
            );
           
            $data = array(
                'title' => "Modifier Borne",
                'page' => "templates/form",
                'menu' => "Extra|Edit Borne",
                'id' => $id,
                'values' => $valeurs,
                'listes_valeurs' => $listes_valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newbornes',
                'methode' => 'modification',
                'descripteur' => $descripteur,                
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un bornes
    ******************************/
    public function suppression($id,$ajax=false) {

        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_newbornes->suppression($id);

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
            $this->my_set_action_response($ajax, true, "La borne a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    /******************************
     * Exportation d'un bornes
     ******************************/
    public function exportation($id) {
        $export = $this->m_newbornes->exportation($id);
        $data = array(
            'title' => "Exportation d'un bornes",
            'page' => "newbornes/exportation",
            'menu' => "Produits|Exportation de bornes",
            'values' => array (
                'export' => $export
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Importation d'un bornes
     ******************************/
    public function importation($id) {
        $this->load->helper(array('form','ctrl'));
        $resultat = false;
        if (array_key_exists('bornes',$_FILES)){
            $f = $_FILES['bornes'];
            if ($f['error'] == 0) {
                $extension = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'].$extension;
                rename($f['tmp_name'],$nom_fichier);
                $resultat = $this->m_newbornes->importation($id,$nom_fichier);
                if ($resultat === false) {
                    $this->session->set_flashdata('danger','Un problème technique est survenu - veuillez reessayer ultérieurement');
                }
                elseif ($resultat === true) {
                    $this->session->set_flashdata('success','Le bornes a été chargé');
                    redirect('newbornes/detail/'.$id);
                }
            }
            else {
                switch($f['error']) {
                    case 1:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 2:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 3:
                        $erreur = 'Le fichier '.$f['name']." n'a été que partiellement téléchargé.";
                        break;
                    case 4:
                        if (file_exists($f['tmp_name'])) {
                            unlink($f['tmp_name']);
                            $erreur = "Un fichier de même nom existait. Veuillez recommencer.";
                        }
                        else {
                            $erreur = "Vous n'avez pas désigné le fichier";
                        }
                        break;
                    case 7:
                        $erreur = 'Le fichier '.$f['name']." n'a pu être enregistré.";
                        break;
                    default:
                        $erreur = 'Erreur lors du téléchargement du fichier '.$f['name'];
                }
                $this->session->set_flashdata('danger',$erreur);
            }
        }
        $data = array(
            'title' => "Importation de bornes",
            'page' => "newbornes/importation",
            'menu' => "Produits|Importation de bornes",
            'values' => array (
                'resultat' => $resultat
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}

// EOF
