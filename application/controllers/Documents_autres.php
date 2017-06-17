<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Documents_autres extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('documents_autres/nouveau','plus',true,'documents_autres_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*documents_autres/detail','eye-open',false,'documents_autres_detail',null,array('view')),
                    "Modifier" => array('documents_autres/modification','pencil',false,'documents_autres_modification',null,array('form')),
                    "Supprimer" => array('documents_autres/remove','trash',false,'documents_autres_supprimer',"Veuillez confirmer la suppression du document",array('confirm-modify' => array('documents_autres/index'))),
            ),
            array(
                    "Export XLS" => array('#','list-alt',true,'export_xls'),
					"Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('documents_autres/nouveau','plus',true,'documents_autres_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('documents_autres/detail','eye-open',true,'documents_autres_detail',null,array('view')),
                    "Modifier" => array('documents_autres/modification','pencil',true,'documents_autres_modification',null,array('form')),
                    "Supprimer" => array('documents_autres/remove','trash',true,'documents_autres_supprimer',"Veuillez confirmer la suppression du document",array('confirm-modify' => array('documents_autres/index'))),
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
        $this->load->model('m_documents_autres');
    }    

    /******************************
    * Liste des documents
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_autres/index',
            'detail' => array('documents_autres/detail','doa_id','doa_nom'),
            'champs' => $this->m_documents_autres->get_champs('read'),
            'filterable_columns' => $this->m_documents_autres->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		 $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'documents_autres',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'external_toolbar_data' => array(
				'controleur' => 'documents_autres',
                ),
            ), true);
        
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_autres',
                'methode' => 'index'
            ),true);
		*/
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_autres',$this->session->id);
        $data = array(
            'title' => "Liste des documents",
            'page' => "templates/datatables",
            'menu' => "GED|Autres documents",
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
    * Liste des documents (datasource)
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
            $resultat = $this->m_documents_autres->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'doa_nom' => 't_documents_autres',
                'doa_date' => 't_documents_autres',
                'bar_code' => 't_boites_archive',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_autres->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_autres->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->doa_fichier = construit_lien_fichier($v->dsq_chemin,$v->doa_fichier);
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_documents_autres->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Documents_autres'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Nouveau document
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'doa_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'doa_type','label'=>"Type de document",'rules'=>'required'),
            array('field'=>'doa_nom','label'=>"Référence",'rules'=>'trim|required'),
            array('field'=>'doa_fichier','label'=>"Nom du fichier GED",'rules'=>'trim|required'),
            array('field'=>'doa_boite_archive','label'=>"Boîte archive",'rules'=>'required'),
            array('field'=>'doa_disque_archivage','label'=>"Disque d'archivage",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'doa_date' => formatte_date_to_bd($this->input->post('doa_date')),
                'doa_type' => $this->input->post('doa_type'),
                'doa_nom' => $this->input->post('doa_nom'),
                'doa_fichier' => $this->input->post('doa_fichier'),
                'doa_boite_archive' => $this->input->post('doa_boite_archive'),
                'doa_disque_archivage' => $this->input->post('doa_disque_archivage')
            );
            $resultat = $this->m_documents_autres->nouveau($valeurs);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax, true, "Le document a été créé",'info', $ajaxData);
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
            $valeurs->doa_date = $this->input->post('doa_date');
            $valeurs->doa_type = $this->input->post('doa_type');
            $valeurs->doa_nom = $this->input->post('doa_nom');
            $valeurs->doa_fichier = $this->input->post('doa_fichier');
            $valeurs->doa_boite_archive = $this->input->post('doa_boite_archive');
            $valeurs->doa_disque_archivage = $this->input->post('doa_disque_archivage');
            $this->db->order_by('vtd_type','ASC');
            $q = $this->db->get('v_types_documents');
            $listes_valeurs->doa_type = $q->result();
            $this->db->order_by('bar_code','ASC');
            $q = $this->db->get('t_boites_archive');
            $listes_valeurs->doa_boite_archive = $q->result();
            $this->db->order_by('dsq_nom','ASC');
            $q = $this->db->get('t_disques_archivage');
            $listes_valeurs->doa_disque_archivage = $q->result();
            $scripts = array();
            

            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_autres->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau document",
                'page' => "templates/form",
                'menu' => "GED|Nouvel autre document",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_autres',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un document
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_documents_autres->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'doa_date' => array("Date",'DATE','date','doa_date'),
                   'doa_type' => array("Type de document",'REF','text','vtd_type'),
                   'doa_nom' => array("Référence",'VARCHAR 50','text','doa_nom'),
                   'doa_fichier' => array("Nom du fichier GED",'FICHIER','text','doa_fichier','dsq_chemin'),
                   'doa_boite_archive' => array("Boîte archive",'REF','ref',array('boites_archive','doa_boite_archive','bar_code')),
                   'doa_disque_archivage' => array("Disque d'archivage",'REF','ref',array('disques_archivage','doa_disque_archivage','dsq_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un document",
                'page' => "templates/detail",
                'menu' => "GED|Autre document",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'documents_autres',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un document
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'doa_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'doa_type','label'=>"Type de document",'rules'=>'required'),
            array('field'=>'doa_nom','label'=>"Référence",'rules'=>'trim|required'),
            array('field'=>'doa_fichier','label'=>"Nom du fichier GED",'rules'=>'trim|required'),
            array('field'=>'doa_boite_archive','label'=>"Boîte archive",'rules'=>'required'),
            array('field'=>'doa_disque_archivage','label'=>"Disque d'archivage",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'doa_date' => formatte_date_to_bd($this->input->post('doa_date')),
                'doa_type' => $this->input->post('doa_type'),
                'doa_nom' => $this->input->post('doa_nom'),
                'doa_fichier' => $this->input->post('doa_fichier'),
                'doa_boite_archive' => $this->input->post('doa_boite_archive'),
                'doa_disque_archivage' => $this->input->post('doa_disque_archivage')
            );
            $resultat = $this->m_documents_autres->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "Le document a été modifié";
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
            $redirection = 'documents_autres/detail/'.$id;
			redirect($redirection); 
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_documents_autres->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('doa_date');
            if (isset($valeur)) {
                $valeurs->doa_date = $valeur;
            }
            $valeur = $this->input->post('doa_type');
            if (isset($valeur)) {
                $valeurs->doa_type = $valeur;
            }
            $valeur = $this->input->post('doa_nom');
            if (isset($valeur)) {
                $valeurs->doa_nom = $valeur;
            }
            $valeur = $this->input->post('doa_fichier');
            if (isset($valeur)) {
                $valeurs->doa_fichier = $valeur;
            }
            $valeur = $this->input->post('doa_boite_archive');
            if (isset($valeur)) {
                $valeurs->doa_boite_archive = $valeur;
            }
            $valeur = $this->input->post('doa_disque_archivage');
            if (isset($valeur)) {
                $valeurs->doa_disque_archivage = $valeur;
            }
            $this->db->order_by('vtd_type','ASC');
            $q = $this->db->get('v_types_documents');
            $listes_valeurs->doa_type = $q->result();
            $this->db->order_by('bar_code','ASC');
            $q = $this->db->get('t_boites_archive');
            $listes_valeurs->doa_boite_archive = $q->result();
            $this->db->order_by('dsq_nom','ASC');
            $q = $this->db->get('t_disques_archivage');
            $listes_valeurs->doa_disque_archivage = $q->result();
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_autres->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un document",
                'page' => "templates/form",
                'menu' => "GED|Mise à jour d'autre document",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_autres',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
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
        $resultat = $this->m_documents_autres->archive($id);
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
            $this->my_set_action_response($ajax, true, "Le document a été supprimé", 'info',$ajaxData);
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
        $resultat = $this->m_documents_autres->remove($id);

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
            $this->my_set_action_response($ajax, true, "Le document a été supprimé", 'info',$ajaxData);
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
            $resultat = $this->m_documents_autres->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_documents_autres->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_documents_autres->unremove($id);
        }
    }

}
// EOF
