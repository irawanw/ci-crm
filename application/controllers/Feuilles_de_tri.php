<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Feuilles_de_tri extends CI_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Supprimer" => array('#', 'trash', false, 'feuilles_de_tri_supprimer'),
        ),
        array(
            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    private $barre_action_group = array(
        array(
            "Creer FDR" => array('feuilles_de_tri/work_order', 'eye-open', true, 'feuilles_de_tri_creer_fdr'),
            "Supprimer" => array('#', 'trash', false, 'feuilles_de_tri_supprimer'),
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
        $this->load->model('m_feuilles_de_tri');
    }

    /******************************
     * List of owners Data
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

    public function group($id)
    {
        $option = '';
        $param  = '?';

        $check = $this->m_feuilles_de_tri->check_exist_group($id);

        if (!$check) {
            redirect('feuilles_de_tri');
        }

        if ($id) {
            $param .= 'id=' . $id;

            $this->group_liste($option . $param, '');
        } else {
            redirect('feuilles_de_tri');
        }
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
            'datasource'         => 'feuilles_de_tri/index',
            'detail'             => array('feuilles_de_tri/detail', 'feuilles_de_tri_id', 'description'),
            // 'archive'            => array('feuilles_de_tri/archive', 'feuilles_de_tri_id', 'archive'),
            'champs'             => $this->m_feuilles_de_tri->get_champs('read','parent'),
            'filterable_columns' => $this->m_feuilles_de_tri->liste_filterable_columns(),
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'feuilles_de_tri/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'feuilles_de_tri/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'feuilles_de_tri/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuilles_de_tri',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'feuilles_de_tri',
                    'custom_mass_action_toolbar' => array('archiver','reintegrer')
                ),
            ), true);
        $scripts[] = $this->load->view("feuilles_de_tri/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuilles_de_tri', $this->session->id);
        $data = array(
            'title'        => "Liste Feuilles de tri",
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

    public function group_liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","owners/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'feuilles_de_tri/group',
            // 'detail'             => array(),
            // 'archive'            => array(),
            'champs'             => $this->m_feuilles_de_tri->get_champs('read','child'),
            'filterable_columns' => $this->m_feuilles_de_tri->group_liste_filterable_columns(),
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts  = array();
        $group_id = $this->uri->segment(3);
        $group    = $this->m_feuilles_de_tri->get_group($group_id);

        $scripts[] = $this->load->view("feuilles_de_tri/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'feuilles_de_tri',
                'methode'               => 'group',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
                    'controleur' => 'feuilles_de_tri',
                    'group'      => $group,
                ),
            ), true);
        $scripts[] = $this->load->view("feuilles_de_tri/group-liste-js", array('group' => $group, 'group_id' => $group_id), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('feuilles_de_tri', $this->session->id);
        $data = array(
            'title'        => "Group Feuilles de tri",
            'page'         => "templates/datatables",
            'menu'         => "",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action_group,
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
            $resultat = $this->m_feuilles_de_tri->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'owner_id' => 't_feuilles_de_tri',
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

                $resultat = $this->m_feuilles_de_tri->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuilles_de_tri->liste($id, $pagelength, $pagestart, $filters);
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

    public function group_json($id = 0)
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
            $resultat = $this->m_feuilles_de_tri->group_liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ard_id' => 't_articles_devis',
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

                $resultat = $this->m_feuilles_de_tri->group_liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_feuilles_de_tri->group_liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Delete Purchase Data
     ******************************/
    public function remove($id)
    {
        $resultat = $this->m_feuilles_de_tri->remove($id);
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
            $this->session->set_flashdata('success', "Feuilles de tri a été supprimé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    public function nouveau_fdr($group_id)
    {
        $this->load->model('m_employes');
        $paramIds = $this->input->get('ids');
        $ids      = explode(",", $paramIds);

        $lists              = $this->m_feuilles_de_tri->get_list_form_fdr($ids);
        $distributeur_liste = $this->m_employes->web_service();
        $checkbox_persons   = $this->m_feuilles_de_tri->checkbox_persons();
        $person_liste       = $this->m_feuilles_de_tri->person_liste();

        // commandes globales
        $cmd_globales = array();

        // toolbar
        $toolbar = '';

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("feuilles_de_tri/form-fdr-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes_trier', $this->session->id);
        $data = array(
            'title'   => "Créer et éditer une feuille de route : Paramètres des feuilles de routes",
            'page'    => "feuilles_de_tri/form-fdr",
            'menu'    => "",
            'scripts' => $scripts,
            'values'  => array(
                'id'                 => 0,
                'vues'               => $vues,
                'cmd_globales'       => $cmd_globales,
                'toolbar'            => $toolbar,
                'descripteur'        => array(),
                'lists'              => $lists,
                'group_id'           => $group_id,
                'distributeur_liste' => $distributeur_liste,
                'checkbox_persons'   => $checkbox_persons,
                'person_liste'       => $person_liste
            ),
        );
        $layout = "layouts/datatables";
        $this->load->view($layout, $data);
    }

    public function save_fdr()
    {
        $result = $this->m_feuilles_de_tri->nouveau_fdr();

        $group_ids = $this->input->post('feuilles_de_tri_id[]');
        $group_id  = $group_ids[0];

        if ($result) {
            $this->session->set_flashdata('success', "Fdr a été enregistré avec succès");
            redirect('feuilles_de_tri/group/' . $group_id, 'refresh');
        } else {
            $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            redirect('feuilles_de_tri/group/' . $group_id, 'refresh');
        }
    }

    public function get_persons($type)
    {
        $result = $this->m_feuilles_de_tri->person_liste($type);

        echo json_encode(array('data' => $result));
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuilles_de_tri->archive($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_feuilles_de_tri->unremove($id);
        }
    }
}
// EOF
