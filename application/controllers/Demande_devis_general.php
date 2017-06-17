<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_demande des devis m_demande des devis
 */
class Demande_devis_general extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "List" => array(
            array(
                "Export PDF"  => array('#', 'book', false, 'export_pdf'),
                "Export xlsx" => array('#', 'list-alt', false, 'export_xls'),
                "Impression"  => array('#', 'print', false, 'impression'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_demande_devis_general');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('ctc_nom', 'text', "Nom Contact"),                
                array('ctc_date_creation', 'date', "Date Saisie"),
                array('ctc_telephone_nom', 'text', "Telephone"),                
                array('comment_desc', 'text', "Commentaires"),
                array('utl_login', 'text', "Commercial"),
                array('scv_nom', 'text', "Enseigne"),
                array('generale_name', 'text', "Origine Type"),                
                array('origine_name', 'text', "Origine Detail"),                
                array('ctc_periode', 'text', "Periode"),
                array('ctc_signe_nom', 'text', "Signe"),
                array('numero_factures', 'text', "Factures"),
				array('total_factures_ht', 'text', "CA"),             
                array('RowID', 'text', "__DT_Row_ID"),
            ),
        );

        return $champs[$type];
    }

    /******************************
     * List
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    /******************************
     * Liste
     ******************************/
    public function liste($id = 0, $mode = 0)
    {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'demande_devis_general/index',
            'detail'             => array(),
            'champs'             => $this->get_champs('list'),
            'filterable_columns' => $this->m_demande_devis_general->get_filterable_columns(),
        );

        $barre_action = $this->barre_action["List"];
        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view('demande_devis_general/liste-js', array(), true);
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'demande_devis_general',
                'methode'             => __FUNCTION__,
                'mass_action_toolbar' => false,
                'view_toolbar'        => false,
                'external_toolbar'    => 'custom-toolbar',
                'external_toolbar_data' => array()
            ), true);
        // listes personnelles        
        $vues      = $this->m_vues->vues_ctrl('demande_devis_general', $this->session->id);
        $data      = array(
            'title'                  => "Liste Demande devis general",
            'page'                   => "templates/datatables",
            'menu'                   => "Ventes|demande_devis_general",
            'scripts'                => $scripts,
            'barre_action'           => $barre_action,
            'controleur'             => 'demande_devis_general',
            'methode'                => __FUNCTION__,
            'animation_barre_action' => false,
            'values'                 => array(
                'id'           => $id,
                'vues'         => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'      => $toolbar,
                'descripteur'  => $descripteur,
            ),
        );
        $layout = "layouts/datatables";
        $this->load->view($layout, $data);
    }

    /******************************
     * Liste(datasource)
     ******************************/
    public function index_json($id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            die('');
        }

        //$pagelength = $this->input->post('length');
        //$pagestart  = $this->input->post('start' );
        //debug($this->input->post('filters' ),1);
        $pagelength = 100;
        $pagestart  = 0 + $this->input->post('start');
        if ($pagestart < 2) {
            $pagelength = 50;
        }

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_demande_devis_general->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ctc_id' => 't_contacts',
            );

            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_demande_devis_general->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_demande_devis_general->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Demande Devis general',
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
     * Détail
     ******************************/
    public function detail($id, $ajax = false)
    {

    }

    public function get_total($id)
    {
    	if (!$this->input->is_ajax_request()) {
            die('');
        }

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }
        

        if (empty($order) || empty($columns)) {
            //list with default ordering
            $resultat = $this->m_demande_devis_general->get_total($id, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ctc_id' => 't_contacts',
            );

            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_demande_devis_general->get_total($id, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_demande_devis_general->get_total($id, $filters);
            }
        }

        echo json_encode($resultat);
    }
   
    /******************************
     * Suppression
     * support AJAX
     ******************************/
    public function suppression($id, $ajax = false)
    {

    }

    public function mass_archiver()
    {
       
    }

    public function mass_remove()
    {
        
    }

    public function mass_unremove()
    {
        
    }

}

// EOF
