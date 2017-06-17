<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Documents_employes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('documents_employes/nouveau','plus',true,'documents_employes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*documents_employes/detail','eye-open',false,'documents_employes_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('documents_employes/nouveau','plus',true,'documents_employes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('documents_employes/detail','eye-open',true,'documents_employes_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Edition" => array(
            array(
                "Fiche Employé(e)" => array('employes/detail','user',true,'employes_detail',null,array('view')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_documents_employes');
    }

    /******************************
    * Liste des documents générés
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_employes/index',
            'detail' => array('documents_employes/detail','doe_id','doe_date'),
            'champs' => $this->m_documents_employes->get_champs('read'),
            'filterable_columns' => $this->m_documents_employes->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_employes',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_employes',$this->session->id);
        $data = array(
            'title' => "Liste des documents générés",
            'page' => "templates/datatables",
            'menu' => "GED|Documents employes",
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
    * Liste des documents générés (datasource)
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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_documents_employes->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'doe_date' => 't_documents_employes',
                'mod_nom' => 't_modeles_documents',
                'emp_nom' => 't_employes',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_employes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_employes->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->doe_fichier = construit_lien_fichier($v->dsq_chemin,$v->doe_fichier);
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des documents générés
    ******************************/
    public function documents_employe($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_employes/documents_employe',
            'detail' => array('documents_employes/detail','doe_id','doe_date'),
            'champs' => array(
                array('doe_id','id',"Identifiant"),
                array('doe_date','datetime',"Date"),
                array('mod_nom','ref',"Modèle",'modeles_documents','doe_modele','mod_nom'),
                array('doe_fichier','fichier',"Nom du fichier GED"),
                array('emp_nom','ref',"Employé",'employes','doe_employe','emp_nom'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','doe_disque_archivage','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_documents_employes->liste_par_employe_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_employes',
                'methode' => 'documents_employe'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_employes',$this->session->id);
        $data = array(
            'title' => "Liste des documents générés",
            'page' => "templates/datatables",
            'menu' => "GED|Documents employés",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'documents_employes',
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
    * Liste des documents générés (datasource)
    ******************************/
    public function documents_employe_json($id=0) {
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
            $resultat = $this->m_documents_employes->liste_par_employe($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'doe_date' => 't_documents_employes',
                'mod_nom' => 't_modeles_documents',
                'emp_nom' => 't_employes',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_employes->liste_par_employe($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_employes->liste_par_employe($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->doe_fichier = construit_lien_fichier($v->dsq_chemin,$v->doe_fichier);
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau document
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'doe_modele','label'=>"Modèle",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'doe_modele' => $this->input->post('doe_modele'),
                'doe_employe' => $pere,
                'doe_date' => date('Y-m-d H:i:s')
            );
            $id = $this->m_documents_employes->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_display_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
            }
            else {
                $this->my_set_display_response($ajax,true,"Le document a été généré");
                $redirection = "documents_employes/detail/".$id;
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeurs->doe_modele = $this->input->post('doe_modele');
            $this->db->where("mod_famille=1");
            $this->db->order_by('mod_nom','ASC');
            $q = $this->db->get('t_modeles_documents');
            $listes_valeurs->doe_modele = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_employes->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = modifie_action_barre_action($this->barre_action['Edition'],'employes/detail','employes/detail/'.$pere);

            $data = array(
                'title' => "Nouveau document",
                'page' => "templates/form",
                'menu' => "GED|Nouveau document employé",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_employes',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un document généré
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
            $valeurs = $this->m_documents_employes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'doe_date' => array("Date",'DATETIME','date','doe_date'),
                   'doe_employe' => array("Employé",'REF','ref',array('employes','doe_employe','emp_nom')),
                   'doe_modele' => array("Modèle",'REF','ref',array('modeles_documents','doe_modele','mod_nom')),
                   'doe_fichier' => array("Nom du fichier GED",'FICHIER','text','doe_fichier','dsq_chemin'),
                   'doe_disque_archivage' => array("Disque d'archivage",'REF','ref',array('disques_archivage','doe_disque_archivage','dsq_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un document généré",
                'page' => "templates/detail",
                'menu' => "GED|Document employé",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'documents_employes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

}

// EOF