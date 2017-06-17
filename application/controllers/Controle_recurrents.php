<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * M_controle_recurrents m_controle_recurrents 
 */
class Controle_recurrents extends MY_Controller
{
    private $profil;
    private $default_profil = 'Administrateur';
    private $barre_action   = array(
        'Administrateur' => array(
            array(
                "Creer un<br>controle permanent"  => array('#', 'plus', true, 'create_controle_permanent'),
                "Valider<br>controle permanent"   => array('#', 'ok', true, 'valider_controle_permanent'),
                "Devalider<br>controle permanent" => array('#', 'remove', true, 'devalider_controle_permanent'),
                "Liste des<br>contrôles permanents"     => array('#', 'th-list', true, 'liste_controle_permanent'),
            ),
            array(
                "Creer un<br>controle ponctuel"  => array('#', 'plus', true, 'create_controle_ponctuels'),
                "Valider<br>controle ponctuel"   => array('#', 'ok', true, 'valider_controle_ponctuels'),
                "Devalider<br>controle ponctuel" => array('#', 'remove', true, 'devalider_controle_ponctuels'),
                "Liste des<br>contrôles ponctuels"    => array('#', 'th-list', true, 'liste_controle_ponctuels'),
            ),
            array(
                //"Liste des Contrôles" => array('#', 'th-list', true, 'liste_controle'),
                "Export xlsx"    => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"     => array('#', 'book', true, 'export_pdf'),
                "Imprimer"       => array('#', 'print', true, 'print_list'),
            ),
        ),
        'Client'         => array(
            array(
                "liste contrôles permanent" => array('#', 'th-list', true, 'liste_controle_permanent'),
                "liste contrôles ponctuels" => array('#', 'th-list', true, 'liste_controle_ponctuels'),
            ),
            array(
                "liste contrôles" => array('#', 'th-list', true, 'liste_controle'),
                "Export xlsx"    => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"     => array('#', 'book', true, 'export_pdf'),
                "Imprimer"       => array('#', 'print', true, 'print_list'),
            ),
        ),
        "Group_nonvalid" => array(
            array(
                "Ajouter<br> un address" => array('controle_recurrents/nouveau', 'plus', true, 'group_controle_recurrents_ajouter', null, array('form')),
            ),
            array(
                "Consulter/Modifier<br> une address"  => array('controle_recurrents/modification', 'pencil', false, 'group_controle_recurrents_modification',null, array('form')),
            ),
            array(
                "Supprimer<br> un address" => array('controle_recurrents/remove', 'trash', false, 'group_controle_recurrents_supprimer',"Veuillez confirmer la suppression du Controle Recurrent", array('confirm-delete' => array('controle_recurrents/group'))),
            ),
            array(
                "Voir la liste<br>complete des adresse" => array('#', 'th-list', true, 'controle_recurrents_voir_liste'),
            ),
        ),
        "Group_valid" => array(
            array(
                "Voir la liste<br>complete des adresse" => array('#', 'th-list', true, 'controle_recurrents_voir_liste'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_controle_recurrents');
    }

    protected function get_champs($type, $group_type = null)
    {
        $champs = array(
            'list' => array(
                array('checkbox', 'text', "checkbox", 'checkbox'),
                array('group_id', 'text', "Contrôle ID#", 'group_id'),
                array('type', 'text', "Type", 'type'),
                array('name', 'text', "Contrôle Nom", 'name'),
                array('client_name', 'text', "Client", 'client_name'),
                array('commande_name', 'text', "Commande", 'commande_name'),
                array('date_controle_ponctuel', 'date', "Date Controle Ponctuel", 'date_controle_ponctuel'),
                array('valides', 'text', "Valide", 'valides')
            ),
            'group' => array(
                array('checkbox', 'text', "Action de masse", 'checkbox'),
                array('controle_recurrents_id', 'ref', "Address#", 'controle_recurrents', 'controle_recurrents_id', 'controle_recurrents_id'),
                array('group_name', 'text', "Contrôle Nom", 'group_name'),
                array('ville', 'text', "Ville", 'ville'),
                array('rue', 'text', "Rue", 'rue'),
                array('numero', 'text', "Numero", 'numero'),
                array('nom', 'text', "Nom", 'nom'),
                array('telephone', 'text', "Telephone", 'telephone'),
                array('mail', 'text', "Mail", 'mail'),
            )
        );

        if($group_type == 'ponctuel') {
            $champs['group'][] = array('date_controle', 'date', "Date Controle", 'date_controle');
            $champs['group'][] = array('type', 'text', "Type", 'type');
            $champs['group'][] = array('resultat', 'text', "Resultat", 'resultat');
            $champs['group'][] = array('observations', 'textarea', "Observations", 'controle_recurrents_id', 'observations');
        }

        return $champs[$type];
    }

    /**
     * get barre_action by profile user logged in
     * @return Array [barre acton toolbar]
     */
    public function get_barre_action()
    {
        //get profil user logged in
        $profil       = $this->session->profil;
        $barre_action = '';

        switch ($profil) {
            case 'Client':
                $barre_action = $this->barre_action[$profil];
                break;
            default:
                $barre_action = $this->barre_action[$this->default_profil];
                break;
        }

        return $barre_action;
    }

    /**
     * Check whether the user is currently logged profile is 'Client'
     * @return boolean isClient
     */
    public function is_client()
    {
        return $this->session->profil == 'Client' ? true : false;
    }

    /******************************
     * List of Livraisons Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste();
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archived');
    }

    public function deleted()
    {
        $this->liste($id = 0, 'deleted');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    public function group($type = null, $group_name = null, $option = '')
    {
        $id = 0;
        if($option != '') {
            $id = $option;
        }

        $id .= '/'.urldecode($group_name) . '/' . $type;
        $this->liste_group($id, 'group');
    }

    public function permanent($id = 0, $liste = 0)
    {
        $this->liste($id, "permanent");
    }

    public function ponctuel($id = 0, $liste = 0)
    {
        $this->liste($id, "ponctuel");
    }

    public function liste($id = 0, $mode = "")
    {
        // commandes globales
        $cmd_globales = array(

        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'controle_recurrents/view_group',
            'detail'             => array('controle_recurrents/detail', 'controle_recurrents_group_id', 'description'),
            'archive'            => array('controle_recurrents/archive', 'controle_recurrents_group_id', 'archive'),
            'champs'             => $this->get_champs('list'),
            'filterable_columns' => $this->m_controle_recurrents->liste_group_filterable_columns(),
        );

        switch ($mode) {
            case 'deleted':
                $descripteur['datasource'] = 'controle_recurrents/view_group_deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'controle_recurrents/view_group_all';
                break;
            case 'archived':
                $descripteur['datasource'] = 'controle_recurrents/view_group_archived';
                break;
            case 'ponctuel':
                $descripteur['datasource'] = 'controle_recurrents/ponctuel_group';
                break;
            case 'permanent':
                $descripteur['datasource'] = 'controle_recurrents/permanent_group';
                break;
            default:
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $external_toolbar_data = array(
            'list_client' => $this->m_controle_recurrents->list_client(),
            'liste_permanent' => $this->m_controle_recurrents->list_permanent(),
            'liste_type' => $this->m_controle_recurrents->type_option(),
            'is_liste_group' => TRUE
        );

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'controle_recurrents',
                'methode'               => 'index',
                'mass_action_checkbox'  => true,
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'recherche_toolbar'     => false,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => $external_toolbar_data,
            ), true);

        $scripts[] = $this->load->view("controle_recurrents/liste-js",
            array(
                'controleur' => 'controle_recurrents',
            ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('controle_recurrents', $this->session->id);
        $data = array(
            'title'        => "Liste des Controles Recurrents",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Controle Recurrents",
            'scripts'      => $scripts,
            'barre_action' => $this->get_barre_action(), //enable sage bar action
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

    public function liste_group($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            //array("Nouvelle livraison","livraisons/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        $group = $this->m_controle_recurrents->get_group(array('type' => $this->uri->segment(3), 'name' => $this->uri->segment(4)));

        // descripteur
        $descripteur = array(
            'datasource'         => 'controle_recurrents/group',
            'detail'             => array('controle_recurrents/detail', 'controle_recurrents_id', 'description'),
            'archive'            => array('controle_recurrents/archive', 'controle_recurrents_id', 'archive'),
            'champs'             => $this->get_champs('group', $group->type),
            'filterable_columns' => $this->m_controle_recurrents->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: livraisons/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'controle_recurrents/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'controle_recurrents/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'controle_recurrents/all';
                break;
            case 'group':
                $descripteur['datasource'] = 'controle_recurrents/group';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $is_group_valid = $this->m_controle_recurrents->is_group_valid(array('type' => $this->uri->segment(3), 'name' => $this->uri->segment(4)));
        $barre_action   =  ($is_group_valid == 0 && $this->is_client() == false) ? $this->barre_action['Group_nonvalid'] : $this->barre_action['Group_valid']; 


        $external_toolbar_data = array(
            'list_client'               => $this->m_controle_recurrents->list_client(),
            'list_permanent_valides'    => $this->m_controle_recurrents->list_permanent('valides'),
            'list_permanent_nonvalides' => $this->m_controle_recurrents->list_permanent('nonvalides'),
            'list_ponctuels_valides'    => $this->m_controle_recurrents->list_ponctuels('valides'),
            'list_ponctuels_nonvalides' => $this->m_controle_recurrents->list_ponctuels('nonvalides'),
            'group'                     => $group,
            'liste_permanent'           => $this->m_controle_recurrents->list_permanent(),
            'group_valid'               => $is_group_valid,
            'barre_action'              => $barre_action
        );

        //get view custom toolbar by profile user logged in
        if ($this->is_client()) {
            $external_toolbar                                = 'client-toolbar';
            $external_toolbar_data['list_ponctuels_valides'] = $this->m_controle_recurrents->list_ponctuels($this->session->userdata['id'], 'valides');
        } else {
            $external_toolbar = 'admin-toolbar';
        }

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'controle_recurrents',
                'methode'               => 'index',
                'mass_action_checkbox'  => true,
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'recherche_toolbar'     => false,
                'external_toolbar'      => $external_toolbar,
                'external_toolbar_data' => $external_toolbar_data,
            ), true);

        $clients   = $this->client_option();
        $types     = $this->m_controle_recurrents->type_option();
        $scripts[] = $this->load->view("controle_recurrents/common-js",
            array(
                'controleur' => 'controle_recurrents',
                'clients'    => $clients,
                'types'      => $types,
                'group'      => $group,
            ), true);
        $scripts[] = $this->load->view("controle_recurrents/liste-js",
            array(
				'group_valid' => $this->m_controle_recurrents->is_group_valid(array('type' => $this->uri->segment(3), 'name' => $this->uri->segment(4))),
                'controleur' => 'controle_recurrents',
            ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('controle_recurrents', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des controle recurrents",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Controle Recurrents",
            'scripts'      => $scripts,
            'barre_action' => $this->get_barre_action(), //enable sage bar action
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

    public function view_group_json($id = 0, $type = '')
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
            $resultat = $this->m_controle_recurrents->liste_group($id, $pagelength, $pagestart, $filters, $type);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'controle_recurrents_group_id' => 't_controle_recurrents_group',
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

                $resultat = $this->m_controle_recurrents->liste_group($id, $pagelength, $pagestart, $filters, $type, $order_col, $ordering);
            } else {
                $resultat = $this->m_controle_recurrents->liste_group($id, $pagelength, $pagestart, $filters, $type);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Contrôle_recurrent_groups'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
     * Ajax call for Livraison List
     ******************************/
    public function index_json($id = 0, $group = '', $type='')
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
            $resultat = $this->m_controle_recurrents->liste($id, $type, $pagelength, $pagestart, $filters, $group);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'controle_recurrents_id' => 't_controle_recurrents',
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

                $resultat = $this->m_controle_recurrents->liste($id, $type, $pagelength, $pagestart, $filters, $group, $order_col, $ordering);
            } else {
                $resultat = $this->m_controle_recurrents->liste($id, $type, $pagelength, $pagestart, $filters, $group, $order_col = '', $ordering = '');
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $group_name = str_replace("_", " ", $group);
            $champs = $this->get_champs('group', $type);
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Contrôle_recurrents',
                'headers' => array(
                    array('text' => 'Liste des Contrôle '.$type),
                    array('text' => $group_name)
                )
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

    public function group_json($id=0, $group = "", $type='')
    {
        $this->index_json($id, $group, $type);
    }

    public function view_group_deleted_json($id = 0)
    {
        $this->view_group_json('deleted');
    }

    public function view_group_all_json($id = 0)
    {
        $this->view_group_json('all');
    }

    public function view_group_archived_json($id = 0)
    {
        $this->view_group_json('archived');
    }

    public function permanent_group_json($id = 0)
    {
        $this->view_group_json($id, "permanent");
    }

    public function ponctuel_group_json($id = 0)
    {
        $this->view_group_json($id, "ponctuel");
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_controle_recurrents->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Pas de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    public function controler_permanent_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_controle_recurrents->list_permanent($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='' selected='selected'>(choisissez)</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row . "'>" . $row . "</option>";
        }
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id=0, $group_name = null, $controle = null, $ajax)
    {
        if ($group_name == null && $controle == null) {
            $this->session->set_flashdata('warning', "No Selected Contrôle");
            redirect('controle_recurrents');
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        //if param group_name if exist
        $resultat_group = $this->m_controle_recurrents->get_group(array('type' => $controle, 'name' => $group_name));

        if (!$resultat_group) {
            $this->session->set_flashdata('warning', "No Found Contrôle");
            redirect('controle_recurrents');
        }

        // règles de validation
        $config = array(
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim|required'),
            array('field' => 'rue', 'label' => "Rue", 'rules' => 'trim'),
            array('field' => 'numero', 'label' => "Numero", 'rules' => 'trim'),
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'telephone', 'label' => "Telephone", 'rules' => 'trim'),
            array('field' => 'mail', 'label' => "Mail", 'rules' => 'trim|valid_email'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'ville'                        => $this->input->post('ville'),
                'rue'                          => $this->input->post('rue'),
                'numero'                       => $this->input->post('numero'),
                'nom'                          => $this->input->post('nom'),
                'telephone'                    => $this->input->post('telephone'),
                'mail'                         => $this->input->post('mail'),
                'controle_recurrents_group_id' => $resultat_group->group_id,
            );

            //if controle ponctuels
            if ($controle == "ponctuel") {
                $valeurs['date_controle'] = formatte_date_to_bd($this->input->post('date_controle'));
                $valeurs['type']          = $this->input->post('type');
                $valeurs['resultat']      = $this->input->post('resultat');
                $valeurs['observations']  = $this->input->post('observations');
            }

            $id = $this->m_controle_recurrents->nouveau($valeurs);

            if($id === false) {
                $this->my_set_action_response($ajax, false);
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
                $this->my_set_action_response($ajax, true, "Controle Recurrent a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs            = new stdClass();
            $listes_valeurs     = new stdClass();
            $valeurs->ville     = $this->input->post('ville');
            $valeurs->rue       = $this->input->post('rue');
            $valeurs->numero    = $this->input->post('numero');
            $valeurs->nom       = $this->input->post('nom');
            $valeurs->telephone = $this->input->post('telephone');
            $valeurs->mail      = $this->input->post('mail');

            //if controle ponctuels
            if ($controle == "ponctuel") {
                $valeurs->date_controle = $this->input->post('date_controle');
                $valeurs->type          = $this->input->post('type');
                $valeurs->resultat      = $this->input->post('resultat');
                $valeurs->observations  = $this->input->post('observations');
                
                $listes_valeurs->type     = $this->m_controle_recurrents->type_option();
                $listes_valeurs->resultat = $this->m_controle_recurrents->resultat_option();
            }

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'ville'     => array("Ville", 'text', 'ville', false),
                    'rue'       => array("Rue", 'text', 'rue', false),
                    'numero'    => array("Numero", 'text', 'numero', false),
                    'nom'       => array("Nom", 'text', 'nom', false),
                    'telephone' => array("Telephone", 'text', 'telephone', false),
                    'mail'      => array("Mail", 'text', 'mail', false),
                ),
                'onglets' => array(),
            );

            if ($controle == "ponctuel") {
                $descripteur['champs']['date_controle'] = array("Date Controle", 'date', 'date_controle', false);
                $descripteur['champs']['type']          = array("Type", 'select', array('type', 'id', 'value'), false);
                $descripteur['champs']['resultat']      = array("Resultat", 'select', array('resultat', 'id', 'value'), false);
                $descripteur['champs']['observations']  = array("Observations", 'textarea', 'observations', false);
            }

            $data = array(
                'title'          => "Ajouter une adresse au contrôle récurrent",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Controle Recurrent",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'controle_recurrents',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Livraisons Data
     ******************************/
    public function detail($id)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_controle_recurrents->detail($id);

            //echo print_r($valeurs); exit();

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'controle_recurrents/modification', 'primary'),
                array("Archiver", 'controle_recurrents/archive', 'warning'),
                array("Supprimer", 'controle_recurrents/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'client'        => array("Client", 'VARCHAR 50', 'text', 'client_name'),
                    'commande'      => array("Commande", 'VARCHAR 50', 'text', 'commande_reference'),
                    'ville'         => array("Ville", 'VARCHAR 50', 'text', 'ville', false),
                    //'addresse_exacte' => array("Addresse Exacte", 'VARCHAR 255','text','addresse_exacte',false),
                    'rue'           => array("Rue", 'VARCHAR 255', 'text', 'rue', false),
                    'numero'        => array("Numero", 'VARCHAR 255', 'text', 'numero', false),
                    'nom'           => array("Nom", 'VARCHAR 50', 'text', 'nom', false),
                    'telephone'     => array("Telephone", 'VARCHAR 50', 'text', 'telephone', false),
                    'mail'          => array("Mail", 'VARCHAR 50', 'text', 'mail', false),
                    'date_controle' => array("Date Controle", 'DATE', 'text', 'date_controle'),
                    'type'          => array("Type", 'VARCHAR 50', 'text', 'type'),
                    'resultat'      => array("Resultat", 'VARCHAR 50', 'text', 'resultat'),
                    'observations'  => array("Observations", 'VARCHAR 50', 'text', 'observations'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of suivi des Controle Recurrents",
                'page'         => "templates/detail",
                'menu'         => "Extra|Controle_recurrents",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'controle_recurrents',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    /******************************
     * Edit function for Livraisons Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {

        $addressline = $this->m_controle_recurrents->detail($id);

        if ($this->m_controle_recurrents->is_valides($id)) {
            $this->session->set_flashdata('danger', "Attention pour modifier ce controle vouz devez le devalider");
            if ($addressline->controle_recurrents_group_id != '') {
                $mode       = 'permanent';
                $group_name = $addressline->group_name;
            }
            if ($addressline->controle_recurrents_group_id != '') {
                $mode       = 'ponctuels';
                $group_name = $addressline->group_name;
            }

            redirect('controle_recurrents/group/' . $group_name);
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim|required'),
            array('field' => 'rue', 'label' => "Rue", 'rules' => 'trim'),
            array('field' => 'numero', 'label' => "Numero", 'rules' => 'trim'),
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'telephone', 'label' => "Telephone", 'rules' => 'trim'),
            array('field' => 'mail', 'label' => "Mail", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'ville'     => $this->input->post('ville'),
                'rue'       => $this->input->post('rue'),
                'numero'    => $this->input->post('numero'),
                'nom'       => $this->input->post('nom'),
                'telephone' => $this->input->post('telephone'),
                'mail'      => $this->input->post('mail'),
            );

            //if controle ponctuels
            if ($addressline->group_type == "ponctuel") {
                $valeurs['date_controle'] = formatte_date_to_bd($this->input->post('date_controle'));
                $valeurs['type']          = $this->input->post('type');
                $valeurs['resultat']      = $this->input->post('resultat');
                $valeurs['observations']  = $this->input->post('observations');
            }

            $resultat = $this->m_controle_recurrents->maj($valeurs, $id);

            $redirection = 'controle_recurrents/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                //upload file when it have value
                if ($this->input->post('fischiers_imprimer') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('fischiers_imprimer'));
                    $this->m_files->update_row($id, $file_ids);
                }

                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Controle recurrent a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                     );
                 }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = $this->m_controle_recurrents->detail($id);
            $listes_valeurs = new stdClass();

            $scripts = array();        

            //if controle ponctuels
            if ($addressline->group_type == "ponctuel") {               
                $scripts[] = $this->load->view('controle_recurrents/form-js', array(), true);

                $listes_valeurs->type     = $this->m_controle_recurrents->type_option();
                $listes_valeurs->resultat = $this->m_controle_recurrents->resultat_option();
            }

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'ville'     => array("Ville", 'text', 'ville', false),
                    'rue'       => array("Rue", 'text', 'rue', false),
                    'numero'    => array("Numero", 'text', 'numero', false),
                    'nom'       => array("Nom", 'text', 'nom', false),
                    'telephone' => array("Telephone", 'text', 'telephone', false),
                    'mail'      => array("Mail", 'text', 'mail', false),
                ),
                'onglets' => array(),
            );

            if ($addressline->group_type == "ponctuel") {
                $descripteur['champs']['date_controle'] = array("Date Controle", 'text', 'date_controle', false);
                $descripteur['champs']['type']          = array("Type", 'select', array('type', 'id', 'value'), false);
                $descripteur['champs']['resultat']      = array("Resultat", 'select', array('resultat', 'id', 'value'), false);
                $descripteur['champs']['observations']  = array("Observations", 'textarea', 'observations', false);
            }            

            $data = array(
                'title'          => "Modifier Controle Recurrent",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Controle Recurrent",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'controle_recurrents',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
           
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_controle_recurrents->archive($id);
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
            $this->session->set_flashdata('success', "Controle Recurrents a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Livraisons Data
     ******************************/
    public function remove($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_controle_recurrents->remove($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Controle Recurrent a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->unremove($id);
        }
    }

    public function mass_resultat_fait()
    {
        $ids     = json_decode($this->input->post('ids'), true); //convert json into array
        $valeurs = array('resultat' => "fait");
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->maj($valeurs, $id);
        }
    }

    public function mass_resultat_pas_fait()
    {
        $ids     = json_decode($this->input->post('ids'), true); //convert json into array
        $valeurs = array('resultat' => "pas fait");
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->maj($valeurs, $id);
        }
    }

    public function mass_resultat_non_controle()
    {
        $ids     = json_decode($this->input->post('ids'), true); //convert json into array
        $valeurs = array('resultat' => "non controlé");
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->maj($valeurs, $id);
        }
    }   

    public function client_option()
    {
        $this->db->select('ctc_id,ctc_nom')->order_by('ctc_nom', 'ASC');

        $session = $this->session->userdata;
        if ($session['profil'] == 'Client') {
            $this->db->where('ctc_id = ' . $session['id']);
        }

        $q = $this->db->get('t_contacts');
        return $q->result();
    }

    public function get_permanent($client, $mode=null)
    {
		/*
        if ($mode == 'valides') {
            $res = $this->m_controle_recurrents->list_permanent('valides', $client);
        } else if($mode == 'nonvalides'){
            $res = $this->m_controle_recurrents->list_permanent('nonvalides', $client);
        } else {
			//$res = $this->m_controle_recurrents->list_permanent('nonvalides', $client);
		}
		*/
		$res = $this->m_controle_recurrents->list_permanent($mode, $client);

        $option = '<option value="">(choisissez)</option>';
        if (is_array($res)) {
            foreach ($res as $row) {
                $option .= '<option value="'.$row->group_id.'">'.$row->name.'</option>';
            };
        }
        echo $option;
    }

    public function get_ponctuels($client, $mode)
    {
        if ($mode == 'valides') {
            $res = $this->m_controle_recurrents->list_ponctuels('valides', $client);
        } else {
            $res = $this->m_controle_recurrents->list_ponctuels('nonvalides', $client);
        }

        $option = '<option value="">(choisissez)</option>';
        if (is_array($res)) {
            foreach ($res as $row) {

                $option .= '<option value="' . site_url('controle_recurrents') . '/group/' . $row->type . '/' . $row->name . '">' . $row->name . '</option>';
            };
        }
        echo $option;
    }   

    public function set_controle_permanent()
    {
        $group_name = $this->input->post('name');
        $resultat   = $this->m_controle_recurrents->set_controle_permanent();

        if ($resultat) {
            $this->session->set_flashdata('success', "Addresses have been successfully saved as " . $group_name);
        }

        redirect('controle_recurrents/group/permanent/' . $group_name);
    }

    public function set_controle_ponctuel()
    {
        $group_name = $this->input->post('name');
        $resultat   = $this->m_controle_recurrents->set_controle_ponctuel();

        if ($resultat) {
            $this->session->set_flashdata('success', "Addresses have been successfully saved as " . $group_name);
        }

        redirect('controle_recurrents/group/ponctuel/' . $group_name);
    }

    public function update_value()
    {
        if ($this->m_controle_recurrents->is_valides($this->input->post('id'))) {
            $this->session->set_flashdata('danger', "Vous devez dé-valider le contrôle pour le modifier");
        } else {
            foreach ($_POST as $key => $value) {
                if ($key != 'id') {
                    $valeurs[$key] = $value;
                }

            }
            $this->m_controle_recurrents->maj($valeurs, $this->input->post('id'));
        }
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }

    /******************************
     * Valider-Devalider Contrôle
     ******************************/
    public function set_valider($type = null, $valider_name = null)
    {
        if ($valider_name == null) {
            redirect('controle_recurrents');
        } else {
            $valider_name = urldecode($valider_name);
            $data         = array('name' => $valider_name, 'type' => $type);
            $resultat     = $this->m_controle_recurrents->valider($data);

            if ($resultat) {
                $this->session->set_flashdata('success', "Contrôle " . $type . " ont été validées avec succès comme " . $valider_name);
            }

            redirect('controle_recurrents/group/'.$type.'/'. $valider_name);
        }
    }
    public function unset_valider($type = null, $valider_name = null)
    {
        if ($valider_name == null) {
            redirect('controle_recurrents');
        } else {
            $valider_name = urldecode($valider_name);
            $data         = array('name' => $valider_name, 'type' => $type);

            $resultat = $this->m_controle_recurrents->revalider($data);

            if ($resultat) {
                $this->session->set_flashdata('success', "Contrôle " . $type . " n'ont pas été validées comme as " . $valider_name);
            }

            redirect('controle_recurrents/group/'.$type.'/'. $valider_name);
        }
    }
    /******************************
     * Mass Archiver Group Data
     ******************************/
    public function mass_archiver_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->archive_group($id);
        }
    }
    /**
     * Mass Remove Group Data
     * @return [type] [description]
     */
    public function mass_remove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->remove_group($id);
        }
    }
    /**
     * Mass Unremove Group Data
     * @return [type] [description]
     */
    public function mass_unremove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_controle_recurrents->unremove_group($id);
        }
    }
}
// EOF
