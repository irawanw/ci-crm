<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Disques_archivage extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('disques_archivage/nouveau','plus',true,'disques_archivage_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*disques_archivage/detail','eye-open',false,'disques_archivage_detail',null,array('view')),
                    "Modifier" => array('disques_archivage/modification','pencil',false,'disques_archivage_modification',null,array('form')),
                    "Supprimer" => array('disques_archivage/remove','trash',false,'disques_archivage_remove',"Veuillez confirmer la suppression du disque d'archivage",array('confirm-modify')),
            ),
            array(
                    "Export XLS" => array('#','list-alt',true,'export_xls'),
					"Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('disques_archivage/nouveau','plus',true,'disques_archivage_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('disques_archivage/detail','eye-open',true,'boites_archive_detail',null,array('view')),
                    "Modifier" => array('disques_archivage/modification','pencil',true,'disques_archivage_modification',null,array('form')),
                    "Supprimer" => array('disques_archivage/remove','trash',true,'disques_archivage_remove',"Veuillez confirmer la suppression du disque d'archivage",array('confirm-modify')),
            ),
            array(
					"Export XLS" => array('#','list-alt',true,'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_disques_archivage');
    }    

    /******************************
    * Liste des disques d'archivage
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'disques_archivage/index',
            'detail' => array('disques_archivage/detail','dsq_id','dsq_nom'),
            'champs' => $this->m_disques_archivage->get_champs('read'),
            'filterable_columns' => $this->m_disques_archivage->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'disques_archivage',
                'methode' => 'index'
            ),true);
		*/
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'disques_archivage',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'external_toolbar_data' => array(
				'controleur' => 'disques_archivage',
                ),
            ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('disques_archivage',$this->session->id);
        $data = array(
            'title' => "Liste des disques d'archivage",
            'page' => "templates/datatables",
            'menu' => "GED|Disques d'archivage",
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

    /******************************
    * Liste des disques d'archivage (datasource)
    ******************************/
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_disques_archivage->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_disques_archivage->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_disques_archivage->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_disques_archivage->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Disques_archivages'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Nouveau disque d'archivage
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dsq_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'dsq_commentaire','label'=>"Commentaire",'rules'=>'trim'),
            array('field'=>'dsq_chemin','label'=>"Chemin d'accès",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dsq_nom' => $this->input->post('dsq_nom'),
                'dsq_commentaire' => $this->input->post('dsq_commentaire'),
                'dsq_chemin' => $this->input->post('dsq_chemin')
            );
            $resultat = $this->m_disques_archivage->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Le disque d'archivage a été créé",'info', $ajaxData);
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
            $valeurs->dsq_nom = $this->input->post('dsq_nom');
            $valeurs->dsq_commentaire = $this->input->post('dsq_commentaire');
            $valeurs->dsq_chemin = $this->input->post('dsq_chemin');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_disques_archivage->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau disque d'archivage",
                'page' => "templates/form",
                'menu' => "GED|Nouveau disque d'archivage",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'disques_archivage',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un disque d'archivage
    * support AJAX
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_disques_archivage->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'dsq_nom' => array("Nom",'VARCHAR 30','text','dsq_nom'),
                   'dsq_commentaire' => array("Commentaire",'VARCHAR 100','textarea','dsq_commentaire'),
                   'dsq_chemin' => array("Chemin d'accès",'VARCHAR 200','text','dsq_chemin')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un disque d'archivage",
                'page' => "templates/detail",
                'menu' => "GED|Disque d'archivage",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'disques_archivage',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un disque d'archivage
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dsq_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'dsq_commentaire','label'=>"Commentaire",'rules'=>'trim'),
            array('field'=>'dsq_chemin','label'=>"Chemin d'accès",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dsq_nom' => $this->input->post('dsq_nom'),
                'dsq_commentaire' => $this->input->post('dsq_commentaire'),
                'dsq_chemin' => $this->input->post('dsq_chemin')
            );
            $resultat = $this->m_disques_archivage->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "Le disque d'archivage a été modifié";
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
            redirect('disques_archivage/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_disques_archivage->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('dsq_nom');
            if (isset($valeur)) {
                $valeurs->dsq_nom = $valeur;
            }
            $valeur = $this->input->post('dsq_commentaire');
            if (isset($valeur)) {
                $valeurs->dsq_commentaire = $valeur;
            }
            $valeur = $this->input->post('dsq_chemin');
            if (isset($valeur)) {
                $valeurs->dsq_chemin = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_disques_archivage->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un disque d'archivage",
                'page' => "templates/form",
                'menu' => "GED|Mise à jour de disque d'archivage",
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'disques_archivage',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un disque d'archivage
    * support AJAX
    ******************************/
    public function remove($id,$ajax=false) {
		 if ($this->input->method() != 'post') {
            die;
        }
		$redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_disques_archivage->remove($id);
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
            $this->my_set_action_response($ajax, true, "Le disque d'archivage a été supprimé", 'info',$ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }
}

// EOF