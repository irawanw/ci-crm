<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Providers extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Providers" => array(
        /*array(
            "Nouveau" => array('providers/nouveau', 'plus', true, 'providers_nouveau'),
        ),
        array(
            //"Consulter" => array('providers/detail', 'eye-open', false, 'providers_detail'),
            "Consulter/Modifier"  => array('providers/modification', 'pencil', false, 'providers_modification'),
            "Supprimer" => array('#', 'trash', false, 'providers_supprimer'),
        ),*/
            array(
                "Nouveau" => array('providers/nouveau', 'plus', true, 'providers_nouveau', null, array('form')),
            ),
            array(
                "Consulter/Modifier" => array('providers/modification', 'pencil', false, 'providers_modification', null, array('form')),
                "Supprimer"          => array('providers/remove', 'trash', false, 'providers_supprimer', "Veuillez confirmer la suppression de cette provider", array('confirm-delete' => array('providers/index'))),
            ),
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
    		),
    		array(
                "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"    => array('#', 'book', true, 'export_pdf'),
    			"Imprimer"		=> array('#', 'print', true, 'print_list'),
            ),
        )
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_providers');
    }

    
    /******************************
     * List of providers Data
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

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","providers/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'providers/index',
            'detail'             => array('providers/detail', 'providers_id', 'description'),
            'archive'            => array('providers/archive', 'providers_id', 'archive'),
            'champs'             => $this->m_providers->get_champs('read'),
            'filterable_columns' => $this->m_providers->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: providers/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'providers/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'providers/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'providers/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'providers',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);

        $scripts[] = $this->load->view("providers/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('providers', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Providers",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Providers",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action['Providers'],
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
            $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'providers_id' => 't_providers',
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

                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_providers->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Providers'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    public function archived_json($id = 0)
    {
        $id         = 'archived';
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
            $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'providers_id' => 't_providers',
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

                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function deleted_json($id = 0)
    {
        $id         = 'deleted';
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
            $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'providers_id' => 't_providers',
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

                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    public function all_json($id = 0)
    {
        $id         = 'all';
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
            $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'providers_id' => 't_providers',
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

                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_providers->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id=0,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'provider', 'label' => "Provider", 'rules' => 'trim|required'),
            array('field' => 'abuse_email', 'label' => "Abuse Email", 'rules' => 'trim|valid_emails'),
            array('field' => 'abuse_telephone', 'label' => "Abuse Telephone", 'rules' => 'trim'),
            array('field' => 'abuse_url', 'label' => "Abuse Url", 'rules' => 'trim|valid_urls'),
            array('field' => 'commentaries', 'label' => "Commentaries", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'provider'        => $this->input->post('provider'),
                'abuse_email'     => $this->input->post('abuse_email'),
                'abuse_telephone' => $this->input->post('abuse_telephone'),
                'abuse_url'       => $this->input->post('abuse_url'),
                'commentaries'    => $this->input->post('commentaries'),
            );

            $resultat = $this->m_providers->nouveau($valeurs);
            /*if ($id === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            } else {
                $this->session->set_flashdata('success', "Providers a été enregistré avec succès");
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            }*/
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {  
                $ajaxData = array(
                   'event' => array(
                       'controleur' => $this->my_controleur_from_class(__CLASS__),
                       'type' => 'recordadd',
                       'id' => $resultat,
                       'timeStamp' => round(microtime(true) * 1000),
                       ),
                   );            
                $this->my_set_action_response($ajax, true, "Providers a été enregistré avec succès",'info', $ajaxData);

            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection); 
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                  = new stdClass();
            $listes_valeurs           = new stdClass();
            $valeurs->provider        = $this->input->post('provider');
            $valeurs->abuse_email     = $this->input->post('abuse_email');
            $valeurs->abuse_telephone = $this->input->post('abuse_telephone');
            $valeurs->abuse_url       = $this->input->post('abuse_url');
            $valeurs->commentaries    = $this->input->post('commentaries');

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_providers->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Provider",
                'page'           => "templates/form",
                'menu'           => "Extra|Nouveau Providers",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'providers',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
     ******************************/
    public function detail($id,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_providers->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'providers/modification', 'primary'),
                array("Archiver", 'providers/archive', 'warning'),
                array("Supprimer", 'providers/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'provider'        => array("Provider", 'VARCHAR 50', 'text', 'provider'),
                    'abuse_email'     => array("Abuse Email", 'VARCHAR 50', 'text', 'abuse_email'),
                    'abuse_telephone' => array("Abuse Email", 'VARCHAR 50', 'text', 'abuse_telephone'),
                    'abuse_url'       => array("Abuse Url", 'VARCHAR 50', 'text', 'abuse_url'),
                    'commentaries'    => array("Commentaries", 'VARCHAR 50', 'text', 'commentaries'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Provider",
                'page'         => "templates/detail",
                'menu'         => "Extra|Providers",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'providers',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'provider', 'label' => "Provider", 'rules' => 'trim|required'),
            array('field' => 'abuse_email', 'label' => "Abuse Email", 'rules' => 'trim|valid_emails'),
            array('field' => 'abuse_telephone', 'label' => "Abuse Telephone", 'rules' => 'trim'),
            array('field' => 'abuse_url', 'label' => "Abuse Url", 'rules' => 'trim|valid_urls'),
            array('field' => 'commentaries', 'label' => "Commentaries", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'provider'        => $this->input->post('provider'),
                'abuse_email'     => $this->input->post('abuse_email'),
                'abuse_telephone' => $this->input->post('abuse_telephone'),
                'abuse_url'       => $this->input->post('abuse_url'),
                'commentaries'    => $this->input->post('commentaries'),
            );

            $resultat = $this->m_providers->maj($valeurs, $id);
            /*if ($resultat === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('providers/detail/' . $id);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Providers a été modifié";
                }
                $this->session->set_flashdata('success', $message);
                redirect('providers');
            }*/
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Providers a été modifié";
                    $ajaxData = array(
                       'event' => array(
                           'controleur' => $this->my_controleur_from_class(__CLASS__),
                           'type' => 'recordchange',
                           'id' => $id,
                           'timeStamp' => round(microtime(true) * 1000),
                           ),
                       );
                }
                $this->my_set_action_response($ajax,true,$message,'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'providers/detail/'.$id;
            redirect($redirection); 
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_providers->detail($id);
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_providers->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Modifier Provider",
                'page'         => "templates/form",
                'menu'         => "Extra|Edit Providers",
                'scripts'      => $scripts,
                'id'           => $id,
                'values'       => $valeurs,
                'action'       => "modif",
                'multipart'    => true,
                'confirmation' => 'Enregistrer',
                'controleur'   => 'providers',
                'methode'      => 'modification',
                'descripteur'  => $descripteur,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        $resultat = $this->m_providers->archive($id);
        /*if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Providers a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }*/
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"Providers a été archivé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Delete Providers Data
     ******************************/
    public function remove($id,$ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_providers->remove($id);
        /*if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Providers a été supprimé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }*/
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax,true,"Providers a été supprimé",'info', $ajaxData);
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
            $resultat = $this->m_providers->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_providers->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_providers->unremove($id);
        }
    }
}
// EOF
