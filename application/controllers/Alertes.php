<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_alertes m_alertes
*/
class Alertes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Consulter" => array('*alertes/detail','eye-open',false,'alertes_detail',null,array('view', 'dblclick')),
            ),
            array(
					"Export XLS" => array('#','list-alt',true,'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('alertes/detail','eye-open',true,'alertes_detail',null,array('view')),
                    "Acquiter"  => array('alertes/acquitter','ok',false,'alertes_acquitter',null,array('modify', 'positive')),
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
        $this->load->model('m_alertes');
    }

     /******************************
     * List of alert Data
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
    * Liste des alertes
    ******************************/
    public function liste($id=0,$mode=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'alertes/index',
            'detail' => array('alertes/detail','ale_id','ale_date'),
            'champs' => $this->m_alertes->get_champs('read'),
            'en_avant' => array(
                array("ale_etat == 'Vue'",'font-weight:bold'),
                array("ale_etat == 'Nouvelle'",'font-weight:bold;color:red')
            ),
            'filterable_columns' => $this->m_alertes->liste_filterable_columns()
        );

        //determine json script that will be loaded
        //for eg: alertes/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'alertes/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'alertes/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'alertes/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'alertes',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		 $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'alertes',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('alertes',$this->session->id);
        $data = array(
            'title' 		=> "Liste des alertes",
            'page' 			=> "templates/datatables",
            'menu' 			=> "Agenda|Alertes",
            'scripts' 		=> $scripts,
            'barre_action' 	=> $this->barre_action["Liste"],
            'values' 			=> array(
                'id' 			=> $id,
                'vues' 			=> $vues,
                'cmd_globales' 	=> $cmd_globales,
                'toolbar'		=>$toolbar,
                'descripteur' 	=> $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des alertes (datasource)
    ******************************/
    public function index_json($id=0, $pere=0) {
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
            $resultat = $this->m_alertes->liste($id,$pere,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ale_date' => 't_alertes',
                'evt_id' => 't_evenements_taches',
                'tac_titre' => 't_taches'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_alertes->liste($id,$pere,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_alertes->liste($id,$pere,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_alertes->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Alertes'
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

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $alerte       M_alertes  Les infos de l'alerte
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $alerte) {
        $ale_etat = $alerte->ale_etat;
        $etats = array(
            'alertes/detail'                => true,
            'alertes/acquitter'             => $ale_etat < 3,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'une alerte
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
            $valeurs = $this->m_alertes->consulter($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Acquitter",'alertes/acquitter','primary',($valeurs->ale_etat < 3))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'ale_date' => array("Date d'apparition",'DATETIME','datetime','ale_date'),
                   'ale_evenement' => array("Évènement associé",'REF','ref',array('evenements_taches','ale_evenement','evt_id')),
                   'evt_commentaire' => array("Commentaire",'VARCHAR 50','text','evt_commentaire'),
                   'ale_tache' => array("Tâche associée",'REF','ref',array('taches','ale_tache','tac_titre')),
                   'ale_tache' => array("Tâche associée",'REF','ref',array('taches','ale_tache','tac_titre')),
                   'tac_type' => array("Type de tâche",'REF','text','vtt_type'),
                   'ale_type' => array("Type de l'alerte",'REF','text','vtal_type'),
                   'ale_etat' => array("État de l'alerte",'REF','text','vea_etat')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"], $valeurs);

            $data = array(
                'title' => "Détail d'une alerte",
                'page' => "templates/detail",
                'menu' => "Agenda|Alerte",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'alertes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Acquitter
    * support AJAX
    ******************************/
    public function acquitter($id=0,$ajax=false) {
        $data = $this->m_alertes->consulter($id);
        if (! ($data->ale_etat < 3)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $id = $this->m_alertes->acquitter($id);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
            }
            else {
                $this->my_set_action_response($ajax,true,"L'alerte a été acquittée");
                $redirection = "alertes/detail/$id";
            }
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Vérification des nouvelles alertes (appelé en AJAX)
     ******************************/
    public function verification() {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_alertes->verification();
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_alertes->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_alertes->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_alertes->unremove($id);
        }
    }

}

// EOF