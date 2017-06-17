<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_segments m_segments
 */
class Segments extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Segments" => array(
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
            ),
            array(
                "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"  => array('#', 'book', true, 'export_pdf'),
                "Imprimer"    => array('#', 'print', true, 'print_list'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_segments');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
                //array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('id', 'ref', "id#", 'id', 'id', 'id'),
                array('name', 'text', "Segment Name", 'name'),
                array('filtering', 'text', "Segment Filtering", 'filtering'),
            ),
        );

        return $champs[$type];
    }

    /******************************
     * List of segments Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","segments/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'segments/index',
            'detail'             => array('segments/detail', 'id', 'description'),
            'archive'            => array('segments/archive', 'id', 'archive'),
            'champs'             => $this->get_champs('list'),
            'filterable_columns' => $this->m_segments->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: segments/archived_json in kendo_grid-js
        // switch ($mode) {
        //     case 'archiver':
        //         $descripteur['datasource'] = 'segments/archived';
        //         break;
        //     case 'supprimees':
        //         $descripteur['datasource'] = 'segments/deleted';
        //         break;
        //     case 'all':
        //         $descripteur['datasource'] = 'segments/all';
        //         break;
        // }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'segments',
                'methode'             => 'index',
                'mass_action_toolbar' => false,
                'view_toolbar'        => false,
            ), true);
        //$scripts[] = $this->load->view("segments/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('segments', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Hebergeurs",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Hosts",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action['Segments'],
            'values'       => array(
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
     * Ajax call for Livraison List
     ******************************/
    public function index_json($id = 0)
    {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

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
            $resultat = $this->m_segments->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'id' => 't_segments',
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

                $resultat = $this->m_segments->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_segments->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Segments',
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
    }

    public function sync()
    {
        $this->m_segments->sync();
    }

    public function resync()
    {
        $this->m_segments->resync();
    }

    public function get($id, $field = null)
    {
        $resultat = $this->m_segments->get($id);

        if ($field) {
            $response = $resultat ? $resultat->$field : null;
            if ($this->input->is_ajax_request()) {
                echo json_encode(array('data' => $response));
            } else {
                return $response;
            }
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(array('data' => $resultat));
            } else {
                return $resultat;
            }
        }
    }

}
