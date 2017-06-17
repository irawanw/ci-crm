<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Modeles_documents extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('*modeles_documents/nouveau','plus',true,'modeles_documents_nouveau', null, array('form')),
            ),
            array(
                    "Consulter" => array('*modeles_documents/detail','eye-open',false,'modeles_documents_detail',null,array('view')),
                    "Modifier" => array('*modeles_documents/modification[]','pencil',false,'modeles_documents_modification', null, array('form')),
                    "Supprimer" => array('modeles_documents/remove','trash',false,'modeles_documents_remove',"Veuillez confirmer la suppression du modèle de document",array('confirm-modify')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('modeles_documents/detail','eye-open',true,'modeles_documents_detail',null,array('view')),
                    "Supprimer" => array('modeles_documents/remove','trash',false,'modeles_documents_remove',"Veuillez confirmer la suppression du modèle de document",array('confirm-modify')),
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
        $this->load->model('m_modeles_documents');
    }    

    /******************************
    * Liste des modèles de documents
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'modeles_documents/index',
            'detail' => array('modeles_documents/detail','mod_id','mod_nom'),
            'champs' => $this->m_modeles_documents->get_champs('read'),
            'filterable_columns' => $this->m_modeles_documents->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'modeles_documents',
                'methode' => 'index'
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'modeles_documents',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'external_toolbar_data' => array(
				'controleur' => 'modeles_documents',
                ),
            ), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('modeles_documents',$this->session->id);
        $data = array(
            'title' => "Liste des modèles de documents",
            'page' => "templates/datatables",
            'menu' => "GED|Modèles de documents",
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
    * Liste des modèles de documents (datasource)
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
            $resultat = $this->m_modeles_documents->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'mod_nom' => 't_modeles_documents',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_modeles_documents->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_modeles_documents->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->mod_fichier = construit_lien_fichier($v->dsq_chemin,$v->mod_fichier);
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_modeles_documents->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Modeles_documents'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Nouveau modèle de documents
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'mod_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'mod_type','label'=>"Type de modèle",'rules'=>'required'),
            array('field'=>'mod_famille','label'=>"Famille de modèle",'rules'=>'required'),
            array('field'=>'mod_description','label'=>"Description",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $validation_mod_fichier = valide_chargement('mod_fichier');
        $validation = ($validation AND $validation_mod_fichier->valide);
        if (! $validation) {
            $this->session->set_flashdata('danger',"$validation_mod_fichier->erreur");
        }
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'mod_nom' => $this->input->post('mod_nom'),
                'mod_type' => $this->input->post('mod_type'),
                'mod_famille' => $this->input->post('mod_famille'),
                'mod_description' => $this->input->post('mod_description'),
                'mod_sujet' => $this->input->post('mod_sujet'),
                'mod_texte' => $this->input->post('mod_texte'),
                'mod_fichier' => $validation_mod_fichier
            );
            $resultat = $this->m_modeles_documents->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Le modèle de document a été créé",'info', $ajaxData);
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
            $valeurs->mod_nom = $this->input->post('mod_nom');
            $valeurs->mod_type = $this->input->post('mod_type');
            $valeurs->mod_famille = $this->input->post('mod_famille');
            $valeurs->mod_description = $this->input->post('mod_description');
            $valeurs->mod_sujet = $this->input->post('mod_sujet');
            $valeurs->mod_texte = $this->input->post('mod_texte');
            $valeurs->mod_fichier = $this->input->post('mod_fichier');
            $this->db->order_by('vtm_type_modele','ASC');
            $q = $this->db->get('v_types_modeles_documents');
            $listes_valeurs->mod_type = $q->result();
            $this->db->order_by('vfd_famille_modele','ASC');
            $q = $this->db->get('v_familles_modeles_documents');
            $listes_valeurs->mod_famille = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_modeles_documents->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau modèle de documents",
                'page' => "templates/form",
                'menu' => "GED|Nouveau modèle de document",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => true,
                'confirmation' => 'Enregistrer',
                'controleur' => 'modeles_documents',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un modèle de documents
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_modeles_documents->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'mod_nom' => array("Nom",'VARCHAR 60','text','mod_nom'),
                   'mod_type' => array("Type de modèle",'REF','text','vtm_type_modele'),
                   'mod_famille' => array("Famille de modèle",'REF','text','vfd_famille_modele'),
                   'mod_description' => array("Description",'VARCHAR 400','textarea','mod_description'),
                   'mod_sujet' => array("Sujet",'VARCHAR 100','text','mod_sujet'),
                   'mod_texte' => array("Texte",'VARCHAR 2048','textarea','mod_texte'),
                   'mod_fichier' => array("Fichier",'FICHIER','upload','mod_fichier','dsq_chemin')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un modèle de documents",
                'page' => "templates/detail",
                'menu' => "GED|Modèle de document",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'modeles_documents',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un modèle de documents
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'mod_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'mod_type','label'=>"Type de modèle",'rules'=>'required'),
            array('field'=>'mod_description','label'=>"Description",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $validation_mod_fichier = valide_chargement('mod_fichier');
        $validation = ($validation AND $validation_mod_fichier->valide);
        if (! $validation) {
            $this->session->set_flashdata('danger',"$validation_mod_fichier->erreur");
        }
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'mod_nom' => $this->input->post('mod_nom'),
                'mod_type' => $this->input->post('mod_type'),
                'mod_description' => $this->input->post('mod_description'),
                'mod_sujet' => $this->input->post('mod_sujet'),
                'mod_texte' => $this->input->post('mod_texte'),
                'mod_fichier' => $validation_mod_fichier
            );
            $resultat = $this->m_modeles_documents->maj($valeurs,$id);
			if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "Le modèle de documents a été modifié";
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
			$redirection = 'modeles_documents/detail/'.$id;
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_modeles_documents->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('mod_nom');
            if (isset($valeur)) {
                $valeurs->mod_nom = $valeur;
            }
            $valeur = $this->input->post('mod_type');
            if (isset($valeur)) {
                $valeurs->mod_type = $valeur;
            }
            $valeur = $this->input->post('mod_description');
            if (isset($valeur)) {
                $valeurs->mod_description = $valeur;
            }
            $valeur = $this->input->post('mod_sujet');
            if (isset($valeur)) {
                $valeurs->mod_sujet = $valeur;
            }
            $valeur = $this->input->post('mod_texte');
            if (isset($valeur)) {
                $valeurs->mod_texte = $valeur;
            }
            $valeur = $this->input->post('mod_fichier');
            if (isset($valeur)) {
                $valeurs->mod_fichier = $valeur;
            }
            $this->db->order_by('vtm_type_modele','ASC');
            $q = $this->db->get('v_types_modeles_documents');
            $listes_valeurs->mod_type = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'mod_nom' => array("Nom",'text','mod_nom',true),
                   'mod_type' => array("Type de modèle",'select',array('mod_type','vtm_id','vtm_type_modele'),true),
                   'mod_description' => array("Description",'textarea','mod_description',true),
                   'mod_sujet' => array("Sujet",'text','mod_sujet',false),
                   'mod_texte' => array("Texte",'textarea','mod_texte',false),
                   'mod_fichier' => array("Fichier",'upload','.doc,.docx',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un modèle de documents",
                'page' => "templates/form",
                'menu' => "GED|Mise à jour de modèle de document",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => true,
                'confirmation' => 'Enregistrer',
                'controleur' => 'modeles_documents',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Liste des champs de fusion
     ******************************/
    public function champs_fusion() {
        $data = array(
            'title' => "Liste des champs de fusion disponibles",
            'page' => "modeles_documents/champs_fusion",
            'menu' => "GED|Champs de fusion",
            'values' => array(
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Liste des documents générés
     ******************************/
    public function documents_generes() {
        $scripts = array();
        $scripts[] = $this->load->view('modeles_documents/documents_generes-js',array(),true);
        $data = array(
            'title' => "Liste des documents générés",
            'page' => "modeles_documents/documents_generes",
            'menu' => "GED|Documents générés",
            'scripts' => $scripts,
            'values' => array(
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Liste des documents générés (appelé en AJAX par le composant Grid)
     ******************************/
    public function liste($commande) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_modeles_documents->documents_generes($commande);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
	
	 /******************************
     * Delete Data
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
        $resultat = $this->m_modeles_documents->remove($id);

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
            $this->my_set_action_response($ajax, true, "Le modèle de documents a été supprimé", 'info',$ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);         
    }

}
// EOF
