<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articles_distribution_base_price extends MY_Controller {

	private $profil;

	private $barre_action         = array(
        array(
            "Nouveau"       => array('articles_distribution_base_price/nouveau', 'plus', true, 'articles_distribution_base_price_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier"  => array('articles_distribution_base_price/modification', 'pencil', false, 'articles_distribution_base_price_modification', null, array('form')),
            "Supprimer"          => array('articles_distribution_base_price/remove', 'trash', false, 'articles_distribution_base_price_supprimer', "Veuillez confirmer la suppression du articles distribution base price"),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
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
        $this->load->model(array('m_articles_distribution_base_price'));
    }

    public function get_champs($type)
    {
        $champs = array(
            'list' => array(
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
                array('adb_id', 'ref', "id#", 'adb_id', 'adb_id', 'adb_id'),
                array('adb_secteur', 'text', "Secteur", 'adb_secteur'),
                array('adb_baseprice', 'text', "Baseprice", 'adb_baseprice')
                
            ),
        );

        return $champs[$type];
    }

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
            //array("Nouvelle livraison","airmail/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'articles_distribution_base_price/index',
            'detail'             => array('articles_distribution_base_price/detail', 'adb_id', 'description'),
            'archive'            => array('articles_distribution_base_price/archive', 'adb_id', 'archive'),
            'champs'             => $this->m_articles_distribution_base_price->get_champs('read'),
            'filterable_columns' => $this->m_articles_distribution_base_price->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: articles_distribution_base_price/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'articles_distribution_base_price/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'articles_distribution_base_price/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'articles_distribution_base_price/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'articles_distribution_base_price',
                'methode'             => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);
        $scripts[] = $this->load->view("articles_distribution_base_price/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('articles_distribution_base_price', $this->session->id);
        $data = array(
            'title'        => "Envois Articles Distribution Base Price",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Articles Distribution Base Price",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action, //enable sage bar action
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
            $resultat = $this->m_articles_distribution_base_price->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'adb_id' => 't_articles_distribution_base_price',
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

                $resultat = $this->m_articles_distribution_base_price->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_articles_distribution_base_price->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_articles_distribution_base_price->get_champs('read');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Articles Distribution Base Price',
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

    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'adb_secteur', 'label' => "Secteur", 'rules' => 'trim|required'),
            array('field' => 'adb_baseprice', 'label' => "Baseprice", 'rules' => 'trim|required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'adb_secteur'      => $this->input->post('adb_secteur'),
                'adb_baseprice'       => $this->input->post('adb_baseprice')
            );

            $resultat = $this->m_articles_distribution_base_price->nouveau($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $resultat,
                        'timeStamp'  => round(microtime(true) * 1000),
                    ),
                );
                $this->my_set_action_response($ajax, true, "Articles distribution Base price a été enregistré avec succès", 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();

            $valeurs->adb_secteur        = $this->input->post('adb_secteur');
            $valeurs->adb_baseprice         = $this->input->post('adb_baseprice');
            $listes_valeurs->adb_secteur    = $this->m_articles_distribution_base_price->secteurs_option();  
            // descripteur
            $descripteur = array(
                'champs'  => $this->m_articles_distribution_base_price->get_champs('write'),
                'onglets' => array( 
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Articles distribution Base price",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Articles distribution Base price",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'articles_distribution_base_price',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

     public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'adb_secteur', 'label' => "Secteur", 'rules' => 'trim|required'),
            array('field' => 'adb_baseprice', 'label' => "Baseprice", 'rules' => 'trim|required')  
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'adb_secteur'      => $this->input->post('adb_secteur'),
                'adb_baseprice'       => $this->input->post('adb_baseprice')
            );

            $resultat = $this->m_articles_distribution_base_price->maj($valeurs, $id);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Hebergeur a été modifié";
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

            $redirection = 'articles_distribution_base_price/detail/'.$id;
            redirect($redirection);
            
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_articles_distribution_base_price->detail($id);
            $scripts = array();
			
			$listes_valeurs = new stdClass();
			$listes_valeurs->adb_secteur = $this->m_articles_distribution_base_price->secteurs_option();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_articles_distribution_base_price->get_champs('write'),
                'onglets' => array(),
            );           

            $data = array(
                'title' => "Modifier Hebergeur",
                'page' => "templates/form",
                'menu' => "Extra|Edit Articles distribution Base price",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'articles_distribution_base_price',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    public function remove($id, $ajax = false)
    {

        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_articles_distribution_base_price->remove($id);

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
            $this->my_set_action_response($ajax, true, "Articles Distribution Base Price a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

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
            $valeurs = $this->m_articles_distribution_base_price->detail($id);

            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'm_articles_distribution_base_price/modification', 'primary'),
                array("Archiver", 'm_articles_distribution_base_price/archive', 'warning'),
                array("Supprimer", 'm_articles_distribution_base_price/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'adb_secteur' => array("Secteur", 'VARCHAR 50', 'text', 'adb_secteur'),
                    'adb_baseprice' => array("Baseprice", 'VARCHAR 50', 'text', 'adb_baseprice')
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Hebergeur",
                'page'         => "templates/detail",
                'menu'         => "Extra|Articles distribution Base price",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'articles_distribution_base_price',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_articles_distribution_base_price->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_articles_distribution_base_price->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_articles_distribution_base_price->unremove($id);
        }
    }


}

/* End of file Articles_distribution_base_price.php */
/* Location: .//tmp/fz3temp-1/Articles_distribution_base_price.php */