<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_evenements_taches m_evenements_taches
*/
class Evenements_taches extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            //array(
            //        "Nouveau" => array('evenements_taches/nouveau','plus',true,'evenements_taches_nouveau',null,array('form')),
            //),
            array(
                    "Consulter" => array('*evenements_taches/detail','eye-open',false,'evenements_taches_detail',null,array('view'))
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('evenements_taches/detail','eye-open',true,'evenements_taches_detail',null,array('view'))
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_evenements_taches');
    }

    /******************************
    * Liste des évènements
    ******************************/
    public function evenements_tache($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'evenements_taches/evenements_tache',
            'champs' => array(
                array('evt_commentaire','text',"Commentaire"),
                array('evt_date','datetime',"Date de l'évènement"),
                array('tac_titre','ref',"Tâche concernée",'taches','evt_tache','tac_titre'),
                array('vet_etat','ref',"État de la tâche",'v_etats_taches'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_evenements_taches->liste_par_tache_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'evenements_taches',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('evenements_taches',$this->session->id);
        $data = array(
            'title' => "Liste des évènements",
            'page' => "templates/datatables",
            'menu' => "Agenda|Évènements de tâches",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'evenements_taches',
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
    * Liste des évènements (datasource)
    ******************************/
    public function evenements_tache_json($id=0) {
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
            $resultat = $this->m_evenements_taches->liste_par_tache($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'evt_date' => 't_evenements_taches',
                'tac_titre' => 't_taches'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_evenements_taches->liste_par_tache($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_evenements_taches->liste_par_tache($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Détail d'un évènement
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
            $valeurs = $this->m_evenements_taches->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evt_commentaire' => array("Commentaire",'VARCHAR 50','text','evt_commentaire'),
                   'evt_date' => array("Date de l'évènement",'DATETIME','datetime','evt_date'),
                   'evt_tache' => array("Tâche concernée",'REF','ref',array('taches','evt_tache','tac_titre')),
                   'evt_etat' => array("État de la tâche",'REF','text','vet_etat')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un évènement",
                'page' => "templates/detail",
                'menu' => "Agenda|Èvènement de tâche",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'evenements_taches',
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