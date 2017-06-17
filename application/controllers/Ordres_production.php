<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_ordres_production m_ordres_production
*/
class Ordres_production extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Export pdf" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_ordres_production');
    }

     /******************************
     * List of ville Data
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
    * Liste des ordres de production
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'ordres_production/index',
            'detail' => array('ordres_production/detail','opr_id','opr_code_article'),
            'champs' => $this->m_ordres_production->get_champs('read'),
            'filterable_columns' => $this->m_ordres_production->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'ordres_production',
                'methode' => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar' => true
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('ordres_production',$this->session->id);
        $data = array(
            'title' => "Liste des ordres de production",
            'page' => "templates/datatables",
            'menu' => "Production|Ordres de production",
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
    * Liste des ordres de production (datasource)
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
            $resultat = $this->m_ordres_production->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'opr_code_article' => 't_ordres_production',
                'cmd_reference' => 't_commandes',
                'ard_id' => 't_articles_devis'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_ordres_production->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_ordres_production->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
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
    * Liste des ordres de production
    ******************************/
    public function op_commande($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'ordres_production/op_commande',
            'detail' => array('ordres_production/detail','opr_id','opr_code_article'),
            'champs' => array(
                array('opr_code_article','text',"Code article"),
                array('opr_code_prod','text',"Code production"),
                array('opr_description','text',"Description"),
                array('opr_prix','number',"PUHT"),
                array('opr_quantite','number',"Quantité"),
                array('vfm_famille','ref',"Famille",'v_familles'),
                array('cmd_reference','ref',"Commande associée",'commandes','opr_commande','cmd_reference'),
                array('ard_id','ref',"Article de devis associé",'articles_devis','opr_article_devis','ard_id'),
                array('vfm_famille','ref',"Famille",'v_etats_ordres_production'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_ordres_production->liste_par_commande_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'ordres_production',
                'methode' => 'op_commande'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('ordres_production',$this->session->id);
        $data = array(
            'title' => "Liste des ordres de production",
            'page' => "templates/datatables",
            'menu' => "Production|Ordres de production",
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
    * Liste des ordres de production (datasource)
    ******************************/
    public function op_commande_json($id=0) {
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
            $resultat = $this->m_ordres_production->liste_par_commande($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'opr_code_article' => 't_ordres_production',
                'cmd_reference' => 't_commandes',
                'ard_id' => 't_articles_devis'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_ordres_production->liste_par_commande($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_ordres_production->liste_par_commande($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_ordres_production->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_ordres_production->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_ordres_production->unremove($id);
        }
    }

}
// EOF
