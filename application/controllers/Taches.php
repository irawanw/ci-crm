<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_taches m_taches
*/
class Taches extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('taches/nouveau','plus',true,'taches_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*taches/detail','eye-open',false,'taches_detail',null,array('view')),
                    "Modifier" => array('taches/modification','pencil',false,'taches_modification',null,array('form')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('taches/nouveau','plus',true,'taches_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('taches/detail','eye-open',true,'taches_detail',null,array('view', 'default-view')),
                    "Modifier" => array('taches/modification','pencil',false,'taches_modification',null,array('form')),
                    "Supprimer" => array('taches/suppression','trash',true,'taches_supprimer',"Veuillez confirmer la suppression de la tâche",array('confirm-delete' => array('taches/index')))
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Detail" => array(
            array(
                "Nouveau" => array('taches/nouveau','plus',true,'taches_nouveau',null,array('form'))
            ),
            array(
                "Consulter" => array('taches/detail','eye-open',true,'taches_detail',null,array('view', 'default-view')),
                "Modifier" => array('taches/modification','pencil',false,'taches_modification',null,array('form')),
                "Supprimer" => array('taches/suppression','trash',true,'taches_supprimer',"Veuillez confirmer la suppression de la tâche",array('confirm-delete' => array('taches/index'))),
            ),
            array(
                "Historique" => array('*evenements_taches/evenements_tache[]','tasks',true,'evenements_tache_historique'),
            ),
            array(
				"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            )
        ),
        "Detail_Affectee" => array(
            array(
                "Consulter" => array('taches/detail','eye-open',true,'taches_detail',null,array('view')),
                "Terminer" => array('taches/terminer','ok',false,'taches_terminer',null,array('form')),
            ),
            array(
                "Historique" => array('*evenements_taches/evenements_tache[]','tasks',true,'evenements_tache_historique'),
            ),
        ),
        "Detail_Sous_Traitee" => array(
            array(
                "Consulter" => array('taches/detail','eye-open',true,'taches_detail',null,array('view')),
                "Terminer" => array('taches/terminer','ok',false,'taches_terminer',null,array('form')),
                "Annuler" => array('taches/annuler','remove',false,'taches_annuler',"Veuillez confirmer l'annulation de la tâche",array('confirm-modify')),
            ),
            array(
                "Historique" => array('*evenements_taches/evenements_tache[]','tasks',true,'evenements_tache_historique'),
            ),
        ),
        "Utilisateur" => array(
            array(
                "Fiche utilisateur" => array('utilisateurs/detail','user',true,'utilisateurs_details',null,array('view')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_taches');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('utl_login','ref',"Emetteur",'utilisateurs','tac_emetteur','utl_login'),
                array('tac_titre','text',"Titre"),
                array('tac_description','text',"Description"),
                array('tac_info','text',"Informations spécifiques"),
                array('tac_debut_prevu','datetime',"Date de début prévue"),
                array('tac_fin_prevue','date',"Date de fin prévue"),
                array('tac_debut_real','date',"Date de début réalisée"),
                array('tac_fin_real','date',"Date de fin réalisée"),
                array('tac_travail_prevu','number',"Temps de travail prévu (h)"),
                array('tac_travail_real','number',"Temps de travail réalisé (h)"),
                array('emp_nom','ref',"Employé",'employes','tac_employe','emp_nom'),
                array('ctc_nom','ref',"Sous-traitant",'contacts','tac_sous_traitant','ctc_nom'),
                array('vtt_type','ref',"Type de tâche",'v_types_taches'),
                array('vet_etat','ref',"Etat de la tâche",'v_etats_taches'),
                array('RowID','text',"__DT_Row_ID")
            )
        );

        return $champs[$type];
    }

     /******************************
     * List of taches Data
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
    * Liste des tâches sous_traitées
    ******************************/
    public function sous_traitees($id=0,$liste=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'taches/sous_traitees',
            'detail' => array('taches/detail_sous_traitee','tac_id','tac_titre'),
            'champs' => array(
                array('tac_titre','text',"Titre"),
                array('tac_description','text',"Description"),
                array('tac_info','text',"Informations spécifiques"),
                array('tac_debut_prevu','datetime',"Date de début prévue"),
                array('tac_fin_prevue','date',"Date de fin prévue"),
                array('tac_debut_real','date',"Date de début réalisée"),
                array('tac_fin_real','date',"Date de fin réalisée"),
                array('tac_travail_prevu','number',"Temps de travail prévu (h)"),
                array('tac_travail_real','number',"Temps de travail réalisé (h)"),
                array('emp_nom','ref',"Employé",'employes','tac_employe','emp_nom'),
                array('ctc_nom','ref',"Sous-traitant",'contacts','tac_sous_traitant','ctc_nom'),
                array('vtt_type','ref',"Type de tâche",'v_types_taches'),
                array('vet_etat','ref',"Etat de la tâche",'v_etats_taches'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_taches->liste_sous_traitees_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'taches',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'taches',
                ),
            ), true);
        $scripts[] = $this->load->view("taches/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('taches',$this->session->id);
        $data = array(
            'title' => "Liste des tâches sous_traitées",
            'page' => "templates/datatables",
            'menu' => "Agenda|Tâches sous-traitées",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'taches',
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
    * Liste des tâches sous_traitées (datasource)
    ******************************/
    public function sous_traitees_json($id=0) {
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
            $resultat = $this->m_taches->liste_sous_traitees($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tac_titre' => 't_taches',
                'tac_debut_prevu' => 't_taches',
                'tac_fin_prevue' => 't_taches',
                'tac_debut_real' => 't_taches',
                'tac_fin_real' => 't_taches',
                'emp_nom' => 't_employes',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_taches->liste_sous_traitees($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_taches->liste_sous_traitees($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des tâches à faire
    ******************************/
    public function affectees($id=0,$liste=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'taches/affectees',
            'detail' => array('taches/detail_affectee','tac_id','tac_titre'),
            'champs' => array(
                array('utl_login','ref',"Emetteur",'utilisateurs','tac_emetteur','utl_login'),
                array('tac_titre','text',"Titre"),
                array('tac_description','text',"Description"),
                array('tac_info','text',"Informations spécifiques"),
                array('tac_debut_prevu','datetime',"Date de début prévue"),
                array('tac_fin_prevue','date',"Date de fin prévue"),
                array('tac_debut_real','date',"Date de début réalisée"),
                array('tac_fin_real','date',"Date de fin réalisée"),
                array('tac_travail_prevu','number',"Temps de travail prévu (h)"),
                array('tac_travail_real','number',"Temps de travail réalisé (h)"),
                array('emp_nom','ref',"Employé",'employes','tac_employe','emp_nom'),
                array('vtt_type','ref',"Type de tâche",'v_types_taches'),
                array('vet_etat','ref',"Etat de la tâche",'v_etats_taches'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'en_avant' => array(
                array("tac_etat == 'Initiale'",'font-weight:bold')
            ),
            'filterable_columns' => $this->m_taches->liste_affectees_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('taches',$this->session->id);
        $data = array(
            'title' => "Liste des tâches à faire",
            'page' => "templates/datatables",
            'menu' => "Agenda|Tâches affectées",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'taches',
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
    * Liste des tâches à faire (datasource)
    ******************************/
    public function affectees_json($id=0) {
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
            $resultat = $this->m_taches->liste_affectees($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tac_titre' => 't_taches',
                'utl_login' => 't_utilisateurs',
                'tac_debut_prevu' => 't_taches',
                'tac_fin_prevue' => 't_taches',
                'tac_debut_real' => 't_taches',
                'tac_fin_real' => 't_taches',
                'emp_nom' => 't_employes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_taches->liste_affectees($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_taches->liste_affectees($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des tâches
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'taches/index',
            'detail' => array('taches/detail','tac_id','tac_titre'),
            'champs' => $this->get_champs('list'),
            'filterable_columns' => $this->m_taches->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'taches/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'taches/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'taches/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('taches',$this->session->id);
        $data = array(
            'title' => "Liste des tâches",
            'page' => "templates/datatables",
            'menu' => "Agenda|Tâches",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'taches',
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
    * Liste des tâches (datasource)
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
            $resultat = $this->m_taches->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tac_titre' => 't_taches',
                'utl_login' => 't_utilisateurs',
                'tac_debut_prevu' => 't_taches',
                'tac_fin_prevue' => 't_taches',
                'tac_debut_real' => 't_taches',
                'tac_fin_real' => 't_taches',
                'emp_nom' => 't_employes',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_taches->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_taches->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Taches'
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
    * Liste des tâches associées à un employé
    ******************************/
    public function taches_employe($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'taches/taches_employe',
            'detail' => array('taches/detail','tac_id','tac_titre'),
            'champs' => array(
                array('utl_login','ref',"Emetteur",'utilisateurs','tac_emetteur','utl_login'),
                array('tac_titre','text',"Titre"),
                array('tac_description','text',"Description"),
                array('tac_info','text',"Informations spécifiques"),
                array('tac_debut_prevu','datetime',"Date de début prévue"),
                array('tac_fin_prevue','date',"Date de fin prévue"),
                array('tac_debut_real','date',"Date de début réalisée"),
                array('tac_fin_real','date',"Date de fin réalisée"),
                array('tac_travail_prevu','number',"Temps de travail prévu (h)"),
                array('tac_travail_real','number',"Temps de travail réalisé (h)"),
                array('emp_nom','ref',"Employé",'employes','tac_employe','emp_nom'),
                array('ctc_nom','ref',"Sous-traitant",'contacts','tac_sous_traitant','ctc_nom'),
                array('vtt_type','ref',"Type de tâche",'v_types_taches'),
                array('vet_etat','ref',"Etat de la tâche",'v_etats_taches'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_taches->liste_par_employe_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('taches',$this->session->id);
        $data = array(
            'title' => "Liste des tâches associées à un employé",
            'page' => "templates/datatables",
            'menu' => "Agenda|Tâches employés",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'taches',
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
    * Liste des tâches associées à un employé (datasource)
    ******************************/
    public function taches_employe_json($id=0) {
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
            $resultat = $this->m_taches->liste_par_employe($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tac_titre' => 't_taches',
                'utl_login' => 't_utilisateurs',
                'tac_debut_prevu' => 't_taches',
                'tac_fin_prevue' => 't_taches',
                'tac_debut_real' => 't_taches',
                'tac_fin_real' => 't_taches',
                'emp_nom' => 't_employes',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_taches->liste_par_employe($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_taches->liste_par_employe($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvelle tache
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'tac_titre','label'=>"Titre",'rules'=>'trim|required'),
            array('field'=>'tac_description','label'=>"Description",'rules'=>'trim'),
            array('field'=>'tac_info','label'=>"Informations spécifiques",'rules'=>'trim'),
            array('field'=>'tac_type','label'=>"Type de tâche",'rules'=>'required'),
            array('field'=>'tac_travail_prevu','label'=>"Temps de travail prévu (h)",'rules'=>'trim|numeric|greater_than_equal_to[0]'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tac_titre' => $this->input->post('tac_titre'),
                'tac_description' => $this->input->post('tac_description'),
                'tac_info' => $this->input->post('tac_info'),
                'tac_employe' => $this->input->post('tac_employe'),
                'tac_sous_traitant' => $this->input->post('tac_sous_traitant'),
                'tac_type' => $this->input->post('tac_type'),
                'tac_debut_prevu' => formatte_date_to_bd($this->input->post('tac_debut_prevu')),
                'tac_fin_prevue' => formatte_date_to_bd($this->input->post('tac_fin_prevue')),
                'tac_travail_prevu' => $this->input->post('tac_travail_prevu'),
                'tac_etat' => "1",
            'tac_emetteur' => $this->session->id
            );
            $id = $this->m_taches->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax,true,"La tâche a été créée",'info',$ajaxData);
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
            $valeurs->tac_titre = $this->input->post('tac_titre');
            $valeurs->tac_description = $this->input->post('tac_description');
            $valeurs->tac_info = $this->input->post('tac_info');
            $valeurs->tac_employe = $this->input->post('tac_employe');
            $valeurs->tac_sous_traitant = $this->input->post('tac_sous_traitant');
            $valeurs->tac_type = $this->input->post('tac_type');
            $valeurs->tac_debut_prevu = $this->input->post('tac_debut_prevu');
            $valeurs->tac_fin_prevue = $this->input->post('tac_fin_prevue');
            $valeurs->tac_travail_prevu = $this->input->post('tac_travail_prevu');
            $this->db->order_by('emp_nom','ASC');
            $q = $this->db->get('t_employes');
            $listes_valeurs->tac_employe = $q->result();
            $this->db->where("ctc_fournisseur=1");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->tac_sous_traitant = $q->result();
            $this->db->order_by('vtt_type','ASC');
            $q = $this->db->get('v_types_taches');
            $listes_valeurs->tac_type = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tac_debut_prevu").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#tac_fin_prevue").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'text','tac_titre',true),
                   'tac_description' => array("Description",'textarea','tac_description',false),
                   'tac_info' => array("Informations spécifiques",'textarea','tac_info',false),
                   'tac_employe' => array("Employé",'select',array('tac_employe','emp_id','emp_nom'),false),
                   'tac_sous_traitant' => array("Sous-traitant",'select',array('tac_sous_traitant','ctc_id','ctc_nom'),false),
                   'tac_type' => array("Type de tâche",'select',array('tac_type','vtt_id','vtt_type'),true),
                   'tac_debut_prevu' => array("Date de début prévue",'date','tac_debut_prevu',false),
                   'tac_fin_prevue' => array("Date de fin prévue",'date','tac_fin_prevue',false),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'number','tac_travail_prevu',false)
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_employe','tac_sous_traitant','tac_type')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_travail_prevu'))
                )
            );

            $data = array(
                'title' => "Nouvelle tache",
                'page' => "templates/form",
                'menu' => "Agenda|Nouvelle tâche",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Nouvelle tache
    * support AJAX
    ******************************/
    public function nouveau_utilisateur($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'tac_titre','label'=>"Titre",'rules'=>'trim|required'),
            array('field'=>'tac_description','label'=>"Description",'rules'=>'trim'),
            array('field'=>'tac_info','label'=>"Informations spécifiques",'rules'=>'trim'),
            array('field'=>'tac_type','label'=>"Type de tâche",'rules'=>'required'),
            array('field'=>'tac_travail_prevu','label'=>"Temps de travail prévu (h)",'rules'=>'trim|numeric|greater_than_equal_to[0]'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tac_titre' => $this->input->post('tac_titre'),
                'tac_description' => $this->input->post('tac_description'),
                'tac_info' => $this->input->post('tac_info'),
                'tac_type' => $this->input->post('tac_type'),
                'tac_debut_prevu' => formatte_date_to_bd($this->input->post('tac_debut_prevu')),
                'tac_fin_prevue' => formatte_date_to_bd($this->input->post('tac_fin_prevue')),
                'tac_travail_prevu' => $this->input->post('tac_travail_prevu'),
                'tac_etat' => "1",
                'tac_emetteur' => $this->session->id,
                'tac_employe' => $pere
            );
            $id = $this->m_taches->nouveau_utilisateur($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"La tâche a été créée");
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
            $valeurs->tac_titre = $this->input->post('tac_titre');
            $valeurs->tac_description = $this->input->post('tac_description');
            $valeurs->tac_info = $this->input->post('tac_info');
            $valeurs->tac_type = $this->input->post('tac_type');
            $valeurs->tac_debut_prevu = $this->input->post('tac_debut_prevu');
            $valeurs->tac_fin_prevue = $this->input->post('tac_fin_prevue');
            $valeurs->tac_travail_prevu = $this->input->post('tac_travail_prevu');
            $this->db->order_by('vtt_type','ASC');
            $q = $this->db->get('v_types_taches');
            $listes_valeurs->tac_type = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tac_debut_prevu").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#tac_fin_prevue").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'text','tac_titre',true),
                   'tac_description' => array("Description",'textarea','tac_description',false),
                   'tac_info' => array("Informations spécifiques",'textarea','tac_info',false),
                   'tac_type' => array("Type de tâche",'select',array('tac_type','vtt_id','vtt_type'),true),
                   'tac_debut_prevu' => array("Date de début prévue",'date','tac_debut_prevu',false),
                   'tac_fin_prevue' => array("Date de fin prévue",'date','tac_fin_prevue',false),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'number','tac_travail_prevu',false)
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_type')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_travail_prevu'))
                )
            );

            $barre_action = modifie_action_barre_action($this->barre_action['Utilisateur'], 'utilisateurs/detail', 'utilisateurs/detail/'.$pere);

            $data = array(
                'title' => "Nouvelle tache",
                'page' => "templates/form",
                'menu' => "Agenda|Nouvelle tâche",
                'barre_action' => $barre_action,
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $tache        M_taches  Les infos de la tâche
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $tache) {
        $tac_etat = $tache->tac_etat;
        $etats = array(
            'taches/detail'                => true,
            'taches/modification'          => $tac_etat == 1,
            'taches/annuler'               => $tac_etat == 1,
            'taches/terminer'              => $tac_etat == 1,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'une tâche
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
            $valeurs = $this->m_taches->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'taches/modification','primary',($valeurs->tac_etat == 1))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'VARCHAR 50','text','tac_titre'),
                   'tac_description' => array("Description",'VARCHAR 400','textarea','tac_description'),
                   'tac_info' => array("Informations spécifiques",'VARCHAR 512','textarea','tac_info'),
                   'tac_emetteur' => array("Emetteur",'REF','ref',array('utilisateurs','tac_emetteur','utl_login')),
                   'tac_employe' => array("Employé",'REF','ref',array('employes','tac_employe','emp_nom')),
                   'tac_sous_traitant' => array("Sous-traitant",'REF','ref',array('contacts','tac_sous_traitant','ctc_nom')),
                   'tac_type' => array("Type de tâche",'REF','text','vtt_type'),
                   'tac_etat' => array("Etat de la tâche",'REF','text','vet_etat'),
                   'tac_debut_prevu' => array("Date de début prévue",'DATETIME','date','tac_debut_prevu'),
                   'tac_fin_prevue' => array("Date de fin prévue",'DATE','date','tac_fin_prevue'),
                   'tac_debut_real' => array("Date de début réalisée",'DATE','date','tac_debut_real'),
                   'tac_fin_real' => array("Date de fin réalisée",'DATE','date','tac_fin_real'),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'DECIMAL 5,1','number','tac_travail_prevu'),
                   'tac_travail_real' => array("Temps de travail réalisé (h)",'DECIMAL 5,1','number','tac_travail_real')
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_emetteur','tac_employe','tac_sous_traitant','tac_type','tac_etat')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Detail"], $valeurs);

            $data = array(
                'title' => "Détail d'une tâche",
                'page' => "templates/detail",
                'menu' => "Agenda|Tâche",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une tâche
    * support AJAX
    ******************************/
    public function detail_sous_traitee($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_taches->detail_sous_traitee($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Annuler la tâche",'taches/annuler','danger',($valeurs->tac_etat == 1))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'VARCHAR 50','text','tac_titre'),
                   'tac_description' => array("Description",'VARCHAR 400','textarea','tac_description'),
                   'tac_info' => array("Informations spécifiques",'VARCHAR 512','textarea','tac_info'),
                   'tac_emetteur' => array("Emetteur",'REF','ref',array('utilisateurs','tac_emetteur','utl_login')),
                   'tac_employe' => array("Employé",'REF','ref',array('employes','tac_employe','emp_nom')),
                   'tac_sous_traitant' => array("Sous-traitant",'REF','ref',array('contacts','tac_sous_traitant','ctc_nom')),
                   'tac_type' => array("Type de tâche",'REF','text','vtt_type'),
                   'tac_etat' => array("Etat de la tâche",'REF','text','vet_etat'),
                   'tac_debut_prevu' => array("Date de début prévue",'DATETIME','date','tac_debut_prevu'),
                   'tac_fin_prevue' => array("Date de fin prévue",'DATE','date','tac_fin_prevue'),
                   'tac_debut_real' => array("Date de début réalisée",'DATE','date','tac_debut_real'),
                   'tac_fin_real' => array("Date de fin réalisée",'DATE','date','tac_fin_real'),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'DECIMAL 5,1','number','tac_travail_prevu'),
                   'tac_travail_real' => array("Temps de travail réalisé (h)",'DECIMAL 5,1','number','tac_travail_real')
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_emetteur','tac_employe','tac_sous_traitant','tac_type','tac_etat')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Detail_Sous_Traitee"], $valeurs);

            $data = array(
                'title' => "Détail d'une tâche",
                'page' => "templates/detail",
                'menu' => "Agenda|Tâche sous-traitée",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une tâche
    * support AJAX
    ******************************/
    public function detail_affectee($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_taches->detail_affectee($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Terminer",'taches/terminer','success',($valeurs->tac_etat == 1))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'VARCHAR 50','text','tac_titre'),
                   'tac_description' => array("Description",'VARCHAR 400','textarea','tac_description'),
                   'tac_info' => array("Informations spécifiques",'VARCHAR 512','textarea','tac_info'),
                   'tac_emetteur' => array("Emetteur",'REF','ref',array('utilisateurs','tac_emetteur','utl_login')),
                   'tac_employe' => array("Employé",'REF','ref',array('employes','tac_employe','emp_nom')),
                   'tac_sous_traitant' => array("Sous-traitant",'REF','ref',array('contacts','tac_sous_traitant','ctc_nom')),
                   'tac_type' => array("Type de tâche",'REF','text','vtt_type'),
                   'tac_etat' => array("Etat de la tâche",'REF','text','vet_etat'),
                   'tac_debut_prevu' => array("Date de début prévue",'DATETIME','date','tac_debut_prevu'),
                   'tac_fin_prevue' => array("Date de fin prévue",'DATE','date','tac_fin_prevue'),
                   'tac_debut_real' => array("Date de début réalisée",'DATE','date','tac_debut_real'),
                   'tac_fin_real' => array("Date de fin réalisée",'DATE','date','tac_fin_real'),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'DECIMAL 5,1','number','tac_travail_prevu'),
                   'tac_travail_real' => array("Temps de travail réalisé (h)",'DECIMAL 5,1','number','tac_travail_real')
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_emetteur','tac_employe','tac_sous_traitant','tac_type','tac_etat')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Detail_Affectee"], $valeurs);

            $data = array(
                'title' => "Détail d'une tâche",
                'page' => "templates/detail",
                'menu' => "Agenda|Tâche affectée",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une tâche
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'tac_titre','label'=>"Titre",'rules'=>'trim|required'),
            array('field'=>'tac_description','label'=>"Description",'rules'=>'trim'),
            array('field'=>'tac_info','label'=>"Informations spécifiques",'rules'=>'trim'),
            array('field'=>'tac_emetteur','label'=>"Emetteur",'rules'=>'required'),
            array('field'=>'tac_type','label'=>"Type de tâche",'rules'=>'required'),
            array('field'=>'tac_etat','label'=>"Etat de la tâche",'rules'=>'required'),
            array('field'=>'tac_travail_prevu','label'=>"Temps de travail prévu (h)",'rules'=>'trim|numeric|greater_than_equal_to[0]'),
            array('field'=>'tac_travail_real','label'=>"Temps de travail réalisé (h)",'rules'=>'trim|numeric|greater_than_equal_to[0]'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tac_titre' => $this->input->post('tac_titre'),
                'tac_description' => $this->input->post('tac_description'),
                'tac_info' => $this->input->post('tac_info'),
                'tac_emetteur' => $this->input->post('tac_emetteur'),
                'tac_employe' => $this->input->post('tac_employe'),
                'tac_sous_traitant' => $this->input->post('tac_sous_traitant'),
                'tac_type' => $this->input->post('tac_type'),
                'tac_etat' => $this->input->post('tac_etat'),
                'tac_debut_prevu' => formatte_date_to_bd($this->input->post('tac_debut_prevu')),
                'tac_fin_prevue' => formatte_date_to_bd($this->input->post('tac_fin_prevue')),
                'tac_debut_real' => formatte_date_to_bd($this->input->post('tac_debut_real')),
                'tac_fin_real' => formatte_date_to_bd($this->input->post('tac_fin_real')),
                'tac_travail_prevu' => $this->input->post('tac_travail_prevu'),
                'tac_travail_real' => $this->input->post('tac_travail_real')
            );
            $resultat = $this->m_taches->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "La tâche a été modifiée";
                    $ajaxData = array(
                         'event' => array(
                             'controleur' => $this->my_controleur_from_class(__CLASS__),
                             'type' => 'recordchange',
                             'id' => $id,
                             'timeStamp' => round(microtime(true) * 1000),
                         ),
                    );
                }
                $this->my_set_action_response($ajax,true,$message,'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }
            redirect('taches/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_taches->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('tac_titre');
            if (isset($valeur)) {
                $valeurs->tac_titre = $valeur;
            }
            $valeur = $this->input->post('tac_description');
            if (isset($valeur)) {
                $valeurs->tac_description = $valeur;
            }
            $valeur = $this->input->post('tac_info');
            if (isset($valeur)) {
                $valeurs->tac_info = $valeur;
            }
            $valeur = $this->input->post('tac_emetteur');
            if (isset($valeur)) {
                $valeurs->tac_emetteur = $valeur;
            }
            $valeur = $this->input->post('tac_employe');
            if (isset($valeur)) {
                $valeurs->tac_employe = $valeur;
            }
            $valeur = $this->input->post('tac_sous_traitant');
            if (isset($valeur)) {
                $valeurs->tac_sous_traitant = $valeur;
            }
            $valeur = $this->input->post('tac_type');
            if (isset($valeur)) {
                $valeurs->tac_type = $valeur;
            }
            $valeur = $this->input->post('tac_etat');
            if (isset($valeur)) {
                $valeurs->tac_etat = $valeur;
            }
            $valeur = $this->input->post('tac_debut_prevu');
            if (isset($valeur)) {
                $valeurs->tac_debut_prevu = $valeur;
            }
            $valeur = $this->input->post('tac_fin_prevue');
            if (isset($valeur)) {
                $valeurs->tac_fin_prevue = $valeur;
            }
            $valeur = $this->input->post('tac_debut_real');
            if (isset($valeur)) {
                $valeurs->tac_debut_real = $valeur;
            }
            $valeur = $this->input->post('tac_fin_real');
            if (isset($valeur)) {
                $valeurs->tac_fin_real = $valeur;
            }
            $valeur = $this->input->post('tac_travail_prevu');
            if (isset($valeur)) {
                $valeurs->tac_travail_prevu = $valeur;
            }
            $valeur = $this->input->post('tac_travail_real');
            if (isset($valeur)) {
                $valeurs->tac_travail_real = $valeur;
            }
            $this->db->order_by('utl_login','ASC');
            $q = $this->db->get('t_utilisateurs');
            $listes_valeurs->tac_emetteur = $q->result();
            $this->db->order_by('emp_nom','ASC');
            $q = $this->db->get('t_employes');
            $listes_valeurs->tac_employe = $q->result();
            $this->db->where("ctc_fournisseur=1");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->tac_sous_traitant = $q->result();
            $this->db->order_by('vtt_type','ASC');
            $q = $this->db->get('v_types_taches');
            $listes_valeurs->tac_type = $q->result();
            $this->db->order_by('vet_etat','ASC');
            $q = $this->db->get('v_etats_taches');
            $listes_valeurs->tac_etat = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tac_debut_prevu").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#tac_fin_prevue").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#tac_debut_real").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#tac_fin_real").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_titre' => array("Titre",'text','tac_titre',true),
                   'tac_description' => array("Description",'textarea','tac_description',false),
                   'tac_info' => array("Informations spécifiques",'textarea','tac_info',false),
                   'tac_emetteur' => array("Emetteur",'select',array('tac_emetteur','utl_id','utl_login'),true),
                   'tac_employe' => array("Employé",'select',array('tac_employe','emp_id','emp_nom'),false),
                   'tac_sous_traitant' => array("Sous-traitant",'select',array('tac_sous_traitant','ctc_id','ctc_nom'),false),
                   'tac_type' => array("Type de tâche",'select',array('tac_type','vtt_id','vtt_type'),true),
                   'tac_etat' => array("Etat de la tâche",'select',array('tac_etat','vet_id','vet_etat'),true),
                   'tac_debut_prevu' => array("Date de début prévue",'date','tac_debut_prevu',false),
                   'tac_fin_prevue' => array("Date de fin prévue",'date','tac_fin_prevue',false),
                   'tac_debut_real' => array("Date de début réalisée",'date','tac_debut_real',false),
                   'tac_fin_real' => array("Date de fin réalisée",'date','tac_fin_real',false),
                   'tac_travail_prevu' => array("Temps de travail prévu (h)",'number','tac_travail_prevu',false),
                   'tac_travail_real' => array("Temps de travail réalisé (h)",'number','tac_travail_real',false)
                ),
                'onglets' => array(
                    array("Tâche", array('tac_titre','tac_description','tac_info','tac_emetteur','tac_employe','tac_sous_traitant','tac_type','tac_etat')),
                    array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Mise à jour d'une tache",
                'page' => "templates/form",
                'menu' => "Agenda|Mise à jour de tâche",
                'scripts' => $scripts,
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'taches',
                'methode' => __METHOD__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Annuler
    * support AJAX
    ******************************/
    public function annuler($id=0,$ajax=false) {
        $data = $this->m_taches->detail($id);
        if (! ($data->tac_etat == 1)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $id = $this->m_taches->annuler($id);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
            }
            else {
                $this->my_set_action_response($ajax,true,"La tâche a été annulée");
                $redirection = "taches/detail_sous_traitee/".$id;
            }
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
    * Terminaison d'une tâche
    * support AJAX
    ******************************/
    public function terminer($id=0,$ajax=false) {
        $data = $this->m_taches->detail($id);
        if (! ($data->tac_etat == 1)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            if ($ajax) {
                return;
            }
            redirect();
        }
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'tac_info','label'=>"Informations spécifiques",'rules'=>'trim'),
            array('field'=>'tac_travail_real','label'=>"Temps de travail réalisé (h)",'rules'=>'trim|numeric|greater_than_equal_to[0]'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tac_info' => $this->input->post('tac_info'),
                'tac_travail_real' => $this->input->post('tac_travail_real')
            );
            $resultat = $this->m_taches->terminer($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = 'taches/detail/'.$id;
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "La tâche a été marquée terminée";
                }
                $this->my_set_action_response($ajax,true,$message);
                $redirection = "taches/detail_affectee/".$id;
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_taches->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('tac_info');
            if (isset($valeur)) {
                $valeurs->tac_info = $valeur;
            }
            $valeur = $this->input->post('tac_travail_real');
            if (isset($valeur)) {
                $valeurs->tac_travail_real = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tac_info' => array("Informations spécifiques",'textarea','tac_info',false),
                   'tac_travail_real' => array("Temps de travail réalisé (h)",'number','tac_travail_real',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Terminaison d'une tache",
                'page' => "templates/form",
                'menu' => "Agenda|Fin de tâche",
                'barre_action' => $this->barre_action["Detail_Affectee"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Terminer la tâche',
                'controleur' => 'taches',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un taches
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        
        $resultat = $this->m_taches->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax,true,"Le taches a été supprimé", 'info',$ajaxData);
        }

        if (! $redirection) $redirection = '';
        
        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_taches->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_taches->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_taches->unremove($id);
        }
    }

}

// EOF