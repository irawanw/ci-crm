<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_documents_factures m_documents_factures
*/
class Documents_factures extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('documents_factures/nouveau','plus',true,'documents_factures_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*documents_factures/detail','eye-open',false,'documents_factures_detail',null,array('view')),
                    "Modifier" => array('documents_factures/modification','pencil',false,'documents_factures_modification',null,array('form')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('documents_factures/nouveau','plus',true,'documents_factures_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('documents_factures/detail','eye-open',true,'documents_factures_detail',null,array('view')),
                    "Modifier" => array('documents_factures/modification','pencil',true,'documents_factures_modification',null,array('form')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_documents_factures');
    }

    /******************************
    * Liste des factures fournisseurs
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_factures/index',
            'detail' => array('documents_factures/detail','dof_id','dof_nom'),
            'champs' => $this->m_documents_factures->get_champs('read'),
            'filterable_columns' => $this->m_documents_factures->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_factures',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'documents_factures',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'external_toolbar_data' => array(
				'controleur' => 'documents_factures',
                ),
            ), true);
        
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_factures',$this->session->id);
        $data = array(
            'title' => "Liste des factures fournisseurs",
            'page' => "templates/datatables",
            'menu' => "GED|Factures fournisseurs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'documents_factures',
            'methode' => __FUNCTION__,
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
    * Liste des factures fournisseurs (datasource)
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
            $resultat = $this->m_documents_factures->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'dof_nom' => 't_documents_factures',
                'dof_date' => 't_documents_factures',
                'ctc_nom' => 't_contacts',
                'bar_code' => 't_boites_archive',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_factures->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_factures->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->dof_fichier = construit_lien_fichier($v->dsq_chemin,$v->dof_fichier);
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_documents_factures->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Documents_factures'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Nouvelle facture
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dof_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'dof_fournisseur','label'=>"Fournisseur",'rules'=>'required'),
            array('field'=>'dof_nom','label'=>"Référence",'rules'=>'trim|required'),
            array('field'=>'dof_montant','label'=>"Montant",'rules'=>'trim|numeric|required'),
            array('field'=>'dof_fichier','label'=>"Nom du fichier GED",'rules'=>'trim|required'),
            array('field'=>'dof_boite_archive','label'=>"Boîte archive",'rules'=>'required'),
            array('field'=>'dof_disque_archivage','label'=>"Disque d'archivage",'rules'=>'required'),
            //array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dof_date' => formatte_date_to_bd($this->input->post('dof_date')),
                'dof_fournisseur' => $this->input->post('dof_fournisseur'),
                'dof_nom' => $this->input->post('dof_nom'),
                'dof_montant' => $this->input->post('dof_montant'),
                'dof_fichier' => $this->input->post('dof_fichier'),
                'dof_boite_archive' => $this->input->post('dof_boite_archive'),
                'dof_disque_archivage' => $this->input->post('dof_disque_archivage')
            );
			
            $resultat = $this->m_documents_factures->nouveau($valeurs);
			
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
                $this->my_set_action_response($ajax, true, "La facture a été créée",'info', $ajaxData);
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
            $valeurs->dof_date = $this->input->post('dof_date');
            $valeurs->dof_fournisseur = $this->input->post('dof_fournisseur');
            $valeurs->dof_nom = $this->input->post('dof_nom');
            $valeurs->dof_montant = $this->input->post('dof_montant');
            $valeurs->dof_fichier = $this->input->post('dof_fichier');
            $valeurs->dof_boite_archive = $this->input->post('dof_boite_archive');
            $valeurs->dof_disque_archivage = $this->input->post('dof_disque_archivage');
            $this->db->where("ctc_fournisseur=1");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->dof_fournisseur = $q->result();
            $this->db->order_by('bar_code','ASC');
            $q = $this->db->get('t_boites_archive');
            $listes_valeurs->dof_boite_archive = $q->result();
            $this->db->order_by('dsq_nom','ASC');
            $q = $this->db->get('t_disques_archivage');
            $listes_valeurs->dof_disque_archivage = $q->result();
            $scripts = array();
       

            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_factures->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle facture",
                'page' => "templates/form",
                'menu' => "GED|Nouvelle facture fournisseur",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_factures',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une facture
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
            $valeurs = $this->m_documents_factures->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'dof_date' => array("Date",'DATE','date','dof_date'),
                   'dof_fournisseur' => array("Fournisseur",'REF','ref',array('contacts','dof_fournisseur','ctc_nom')),
                   'dof_nom' => array("Référence",'VARCHAR 50','text','dof_nom'),
                   'dof_montant' => array("Montant",'DECIMAL 8,2','number','dof_montant'),
                   'dof_fichier' => array("Nom du fichier GED",'FICHIER','text','dof_fichier','dsq_chemin'),
                   'dof_boite_archive' => array("Boîte archive",'REF','ref',array('boites_archive','dof_boite_archive','bar_code')),
                   'dof_disque_archivage' => array("Disque d'archivage",'REF','ref',array('disques_archivage','dof_disque_archivage','dsq_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une facture",
                'page' => "templates/detail",
                'menu' => "GED|Facture fournisseur",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'documents_factures',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une facture
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dof_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'dof_fournisseur','label'=>"Fournisseur",'rules'=>'required'),
            array('field'=>'dof_nom','label'=>"Référence",'rules'=>'trim|required'),
            array('field'=>'dof_montant','label'=>"Montant",'rules'=>'trim|numeric|required'),
            array('field'=>'dof_fichier','label'=>"Nom du fichier GED",'rules'=>'trim|required'),
            array('field'=>'dof_boite_archive','label'=>"Boîte archive",'rules'=>'required'),
            array('field'=>'dof_disque_archivage','label'=>"Disque d'archivage",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dof_date' => formatte_date_to_bd($this->input->post('dof_date')),
                'dof_fournisseur' => $this->input->post('dof_fournisseur'),
                'dof_nom' => $this->input->post('dof_nom'),
                'dof_montant' => $this->input->post('dof_montant'),
                'dof_fichier' => $this->input->post('dof_fichier'),
                'dof_boite_archive' => $this->input->post('dof_boite_archive'),
                'dof_disque_archivage' => $this->input->post('dof_disque_archivage')
            );
            $resultat = $this->m_documents_factures->maj($valeurs,$id);
			 if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "La facture a été modifiée";
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
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_documents_factures->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('dof_date');
            if (isset($valeur)) {
                $valeurs->dof_date = $valeur;
            }
            $valeur = $this->input->post('dof_fournisseur');
            if (isset($valeur)) {
                $valeurs->dof_fournisseur = $valeur;
            }
            $valeur = $this->input->post('dof_nom');
            if (isset($valeur)) {
                $valeurs->dof_nom = $valeur;
            }
            $valeur = $this->input->post('dof_montant');
            if (isset($valeur)) {
                $valeurs->dof_montant = $valeur;
            }
            $valeur = $this->input->post('dof_fichier');
            if (isset($valeur)) {
                $valeurs->dof_fichier = $valeur;
            }
            $valeur = $this->input->post('dof_boite_archive');
            if (isset($valeur)) {
                $valeurs->dof_boite_archive = $valeur;
            }
            $valeur = $this->input->post('dof_disque_archivage');
            if (isset($valeur)) {
                $valeurs->dof_disque_archivage = $valeur;
            }
            $this->db->where("ctc_fournisseur=1");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->dof_fournisseur = $q->result();
            $this->db->order_by('bar_code','ASC');
            $q = $this->db->get('t_boites_archive');
            $listes_valeurs->dof_boite_archive = $q->result();
            $this->db->order_by('dsq_nom','ASC');
            $q = $this->db->get('t_disques_archivage');
            $listes_valeurs->dof_disque_archivage = $q->result();
            $scripts = array();
    
            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_factures->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une facture",
                'page' => "templates/form",
                'menu' => "GED|Mise à jour de facture fournisseur",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_factures',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }
}

// EOF