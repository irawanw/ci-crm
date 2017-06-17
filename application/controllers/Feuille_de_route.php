<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Feuille_de_route extends CI_Controller
{
    private $profil;
    private $barre_action = array(
        array(
			"Creer" => array('#', 'plus', true, 'feuille_de_route_creer'),
            "Consulter" => array('*feuille_de_route/detail', 'eye-open', false, 'feuille_de_route_detail'),			
            "Supprimer" => array('#', 'trash', false, 'feuille_de_route_supprimer'),
        ),
        array(
            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    private $barre_action_ville = array(
        array(
            "Creer" => array('#', 'plus', true, 'feuille_de_route_creer'),
            "Consulter" => array('*feuille_de_route/detail_ville', 'eye-open', false, 'feuille_de_route_detail_ville'),         
            "Supprimer" => array('#', 'trash', false, 'feuille_de_route_supprimer'),
        ),
        array(
            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_feuille_de_route');
    }

    /******************************
     * List of Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    /******************************
     * List of Data
     ******************************/
    public function ville($id = 0, $liste = 0)
    {
        $this->ville_liste($id = 0, '');
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
            'datasource'         => 'feuille_de_route/index',
            'detail'             => array('feuille_de_route/detail', 'feuille_de_route_id', 'description'),           
            'champs'             => $this->m_feuille_de_route->get_champs('read','parent'),
            'filterable_columns' => $this->m_feuille_de_route->liste_filterable_columns(),
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuille_de_route',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'feuille_de_route',
                ),
            ), true);
        $scripts[] = $this->load->view("feuille_de_route/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuille_de_route', $this->session->id);
        $data = array(
            'title'        => "Liste Feuille de routes",
            'page'         => "templates/datatables",
            'menu'         => "",
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

    public function ville_liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","owners/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'feuille_de_route/ville',
            //'detail'             => array('feuille_de_route/detail_ville', 'vil_id', 'description'),     
            'champs'             => $this->m_feuille_de_route->get_champs('read','child'),
            'filterable_columns' => $this->m_feuille_de_route->ville_liste_filterable_columns(),
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuille_de_route',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                // 'external_toolbar'      => 'custom-toolbar',
                // 'external_toolbar_data' => array(
                //     'controleur' => 'feuille_de_route',
                // ),
            ), true);
        //$scripts[] = $this->load->view("feuille_de_route/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuille_de_route', $this->session->id);
        $data = array(
            'title'        => "Liste Feuille de routes by ville",
            'page'         => "templates/datatables",
            'menu'         => "",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action_ville,
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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_feuille_de_route->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'feuille_de_route_id' => 't_feuille_de_route',
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

                $resultat = $this->m_feuille_de_route->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuille_de_route->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Ajax call for Livraison List
     ******************************/
    public function ville_json($id = 0)
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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_feuille_de_route->ville_liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'feuille_de_route_id' => 't_feuille_de_route',
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

                $resultat = $this->m_feuille_de_route->ville_liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuille_de_route->ville_liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Détail d'un feuille de route
     ******************************/
    public function detail($id)
    {

        $valeurs = $this->m_feuille_de_route->detail($id);
        // commandes globales
        $cmd_globales = array();

        // commandes locales
        $cmd_locales = array();      

        $data = array(
            'title'        => "Détail d'un feuille de route",
            'page'         => "feuille_de_route/detail",
            'menu'         => "",
            'id'           => $id,
            'values'       => $valeurs,
            'controleur'   => 'feuille_de_route',
            'methode'      => 'detail',
            'cmd_globales' => $cmd_globales,
            'cmd_locales'  => $cmd_locales,
            'descripteur'  => array(),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }  

    /******************************
     * Détail d'un feuille de route ville
     ******************************/
    public function detail_ville($ville_id)
    {

        $valeurs = $this->m_feuille_de_route->detail_ville($ville_id);
        // commandes globales
        $cmd_globales = array();

        // commandes locales
        $cmd_locales = array();      

        $data = array(
            'title'        => "Détail ville d'un feuille de route",
            'page'         => "feuille_de_route/detail-ville",
            'menu'         => "",
            'id'           => $ville_id,
            'values'       => $valeurs,
            'controleur'   => 'feuille_de_route',
            'methode'      => 'detail',
            'cmd_globales' => $cmd_globales,
            'cmd_locales'  => $cmd_locales,
            'descripteur'  => array(),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }  

     /******************************
     * Delete Purchase Data
     ******************************/
    public function remove($id)
    {
        $resultat = $this->m_feuille_de_route->remove($id);
        if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Feuilles de route a été supprimé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }
}
// EOF
