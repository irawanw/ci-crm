<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_actions m_actions
*/
class Actions extends MY_Controller {
    private $profil;
    private $barre_action = array(
        array(
            "Consulter" => array('*actions/detail','eye-open',false,'action_detail',null,array('view')),
        ),
        array(
            "Historique" => array('*actions/historique[]','zoom-in',false,'actions_historique'),
            "Restaurer" => array('actions/restaurer','play',false,'action_restauration',"Veuillez confirmer la restauration de cette action",array('confirm-action')),
            ),
        array(
			"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
            "Impression" => array('#','print',true,'impression')
            )
        );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_actions');
    }

    /******************************
    * Liste des actions
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'actions/index',
            'detail' => array('actions/detail','act_id','act_date'),
            'champs' => $this->m_actions->get_champs('read'),
            'filterable_columns' => $this->m_actions->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'actions',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'actions',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'actions',
                ),
            ), true);
        //$scripts[] = $this->load->view("actions/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('actions',$this->session->id);
        $data = array(
            'title' => "Liste des actions",
            'page' => "templates/datatables",
            'menu' => "Personnel|Actions",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action,
            'controleur' => 'actions',
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
    * Liste des actions (datasource)
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
            $resultat = $this->m_actions->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'act_date' => 't_actions',
                'utl_login' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_actions->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_actions->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des actions de l'utilisateur
    ******************************/
    public function actions_utilisateur($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'actions/actions_utilisateur',
            'detail' => array('actions/detail','act_id','act_date'),
            'champs' => $this->m_actions->get_champs('read'),
            'filterable_columns' => $this->m_actions->liste_par_utilisateur_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'actions',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('actions',$this->session->id);
        $data = array(
            'title' => "Liste des actions de l'utilisateur",
            'page' => "templates/datatables",
            'menu' => "Personnel|Actions",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action,
            'controleur' => 'actions',
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
    * Liste des actions de l'utilisateur (datasource)
    ******************************/
    public function actions_utilisateur_json($id=0) {
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
            $resultat = $this->m_actions->liste_par_utilisateur($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'act_date' => 't_actions',
                'utl_login' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_actions->liste_par_utilisateur($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_actions->liste_par_utilisateur($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Historique d'un enregistrement
    ******************************/
    public function historique($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'actions/historique',
            'detail' => array('actions/detail','act_id','act_id'),
            'champs' => array(
                array('act_id','id',"Identifiant"),
                array('utl_login','ref',"Utilisateur",'utilisateurs','act_user','utl_login'),
                array('act_date','datetime',"Date"),
                array('act_table','text',"Table"),
                array('act_obj_id','number',"Identifiant"),
                array('vat_action','ref',"Action effectuée",'v_actions'),
                array('act_restauration','number',"Restauration")
            )
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/kendo_grid-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'actions',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('actions',$this->session->id);
        $data = array(
            'title' => "Historique d'un enregistrement",
            'page' => "templates/kendo_grid",
            'menu' => "Personnel|Historique des actions",
            'scripts' => $scripts,
            'controleur' => 'actions',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
    * Historique d'un enregistrement (datasource)
    ******************************/
    public function historique_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_actions->historique($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));

    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $action       M_actions  Les infos de l'action
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $action) {
        $etats = array(
            'actions/restaurer'                => true,
            'actions/detail'                   => true,
            'actions/historique'               => true,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'une action
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
            $valeurs = $this->m_actions->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Historique",'actions/historique','primary'),
            //    array("Restaurer",'actions/restaurer','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'act_user' => array("Utilisateur",'REF','ref',array('utilisateurs','act_user','utl_login')),
                   'act_date' => array("Date",'DATETIME','datetime','act_date'),
                   'act_table' => array("Table",'VARCHAR 30','text','act_table'),
                   'act_obj_id' => array("Identifiant",'INT 9','number','act_obj_id'),
                   'act_action' => array("Action effectuée",'REF','text','vat_action'),
                   'act_info' => array("Informations",'VARCHAR 5000','textarea','act_info')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action,$valeurs);

            $data = array(
                'title' => "Détail d'une action",
                'page' => "templates/detail",
                'menu' => "Personnel|Action",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'actions',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Restaurer
    * support AJAX
    ******************************/
    public function restaurer($id=0,$ajax=false) {
        $id = $this->m_actions->restaurer($id);
        if ($id === false) {
            $this->my_set_action_response($ajax,false);
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
        }
        else {
            $this->my_set_action_response($ajax,true,"L'enregistrement a été restauré");
            $redirection = "actions";
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

}

// EOF