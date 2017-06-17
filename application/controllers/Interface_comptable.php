<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Interface_comptable extends MY_Controller {
    private $profil;
    private $barre_action = array(
        array(
            "Export xlsx"   => array('#', 'list-alt', true, 'export_xls')
        ),
    );


    public function __construct() {
        parent::__construct();
        $this->load->model('m_interface_comptable');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
                array('fac_id', 'text', "ID#", 'fac_id'),
                array('fac_date', 'date', "Date", 'fac_date'),
                array('fac_tva', 'text', "mountant TVA", 'fac_tva'),
                array('ctc_id_comptable', 'text', "ID Comptable", 'ctc_id_comptable'),   // Note: This is NOT necessarily the value
                                                                                         //       in the column with the same name!
                array('ctc_nom', 'text', "Client", 'ctc_nom'),
                array('total_HT', 'text', "Total HT", 'total_HT'),
                array('total_TTC', 'text', "Total TTC", 'total_TTC'),
                array('vef_etat', 'text', "État", 'vef_etat'),
                array('fac_reference', 'text', "Référence", 'fac_reference'),
                array('scv_nom', 'text', "Enseigne", 'scv_nom'),
                array('fac_reprise', 'text', "Reprise", 'fac_reprise'),
            )
        );

        return $champs[$type];
    }

    /******************************
     * List of owners Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","owners/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'interface_comptable/index',
            'detail'             => array('interface_comptable/detail', 'fac_id', 'description'),
            'archive'            => array('interface_comptable/archive', 'fac_id', 'archive'),
            'champs'             => $this->get_champs('list'),
            'filterable_columns' => $this->m_interface_comptable->liste_filterable_columns(),
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'Interface_comptable',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => true,
            ), true);
      
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('interface_comptable', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Interface comptables",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Interface Comptable",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_interface_comptable->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'owner_id' => 't_factures',
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

                $resultat = $this->m_interface_comptable->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_interface_comptable->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Interface_comptables'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

}
// EOF
