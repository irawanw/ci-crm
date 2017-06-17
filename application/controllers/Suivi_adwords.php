<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Suivi_adwords extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        /*array(
            "Nouveau" => array('suivi_adwords/nouveau', 'plus', true, 'suivi_adwords_nouveau'),
        ),
        array(
            //"Consulter" => array('suivi_adwords/detail', 'eye-open', false, 'suivi_adwords_detail'),
            "Consulter/Modifier"  => array('suivi_adwords/modification', 'pencil', false, 'suivi_adwords_modification'),
            "Supprimer" => array('#', 'trash', false, 'suivi_adwords_supprimer'),
        ),*/
        array(
            "Nouveau" => array('suivi_adwords/nouveau', 'plus', true, 'suivi_adwords_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('suivi_adwords/modification', 'pencil', false, 'suivi_adwords_modification', null, array('form')),
            "Supprimer"          => array('suivi_adwords/remove', 'trash', false, 'suivi_adwords_supprimer', "Veuillez confirmer la suppression de cette suivi", array('confirm-modify' => array('suivi_adwords/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
		),
		array(
            "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"    => array('#', 'book', true, 'export_pdf'),
			"Imprimer"		=> array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_suivi_adwords');
    }

    /******************************
     * List of suivi_adwords Data
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
            // array("Ajouter un e-mailing pages jaunes","suivi_adwords/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'suivi_adwords/index',
            'detail'             => array('suivi_adwords/detail', 'suivi_adword_id', 'description'),
            'archive'            => array('suivi_adwords/archive', 'suivi_adword_id', 'archive'),
            'champs'             => $this->m_suivi_adwords->get_champs('read'),
            'filterable_columns' => $this->m_suivi_adwords->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: suivi_adwords/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'suivi_adwords/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'suivi_adwords/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'suivi_adwords/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'suivi_adwords',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("suivi_adwords/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('suivi_adwords', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Suivi adwords",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Suivi_adwords",
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
            $resultat = $this->m_suivi_adwords->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'suivi_adword_id' => 't_suivi_adwords',
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

                $resultat = $this->m_suivi_adwords->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_suivi_adwords->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_suivi_adwords->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Suivi_adwords'
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
     * New Livraison
     ******************************/
    public function nouveau($id=0,$ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'mots_clefs', 'label' => "mots clefs", 'rules' => 'trim|required'),
            array('field' => 'distribution_prospectus', 'label' => "distribution prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus', 'label' => "distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_de_flyer', 'label' => "distribution de flyer", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus_en_boites_aux_lettres', 'label' => "distribution de prospectus en boites aux lettres", 'rules' => 'trim'),
            array('field' => 'distribution_de_publicite', 'label' => "distribution de publicité", 'rules' => 'trim'),
            array('field' => 'societe_de_distribution_de_prospectus', 'label' => "societe de distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'tarif_distribution_de_prospectus', 'label' => "tarif distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_prospectus_paris', 'label' => "distribution prospectus paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus_paris', 'label' => "distribution de prospectus paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_flyer_paris', 'label' => "distribution de flyer paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_publicite_paris', 'label' => "distribution de publicité paris", 'rules' => 'trim'),
            array('field' => 'mot', 'label' => "mot", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'mots_clefs' => $this->input->post('mots_clefs'),
                'distribution_prospectus' => $this->input->post('distribution_prospectus'),
                'distribution_de_prospectus' => $this->input->post('distribution_de_prospectus'),
                'distribution_de_flyer' => $this->input->post('distribution_de_flyer'),
                'distribution_de_prospectus_en_boites_aux_lettres' => $this->input->post('distribution_de_prospectus_en_boites_aux_lettres'),
                'distribution_de_publicite' => $this->input->post('distribution_de_publicite'),
                'societe_de_distribution_de_prospectus' => $this->input->post('societe_de_distribution_de_prospectus'),
                'tarif_distribution_de_prospectus' => $this->input->post('tarif_distribution_de_prospectus'),
                'distribution_prospectus_paris' => $this->input->post('distribution_prospectus_paris'),
                'distribution_de_prospectus_paris' => $this->input->post('distribution_de_prospectus_paris'),
                'distribution_de_flyer_paris' => $this->input->post('distribution_de_flyer_paris'),
                'distribution_de_publicite_paris' => $this->input->post('distribution_de_publicite_paris'),
                'mot' => $this->input->post('mot')
            );

            /*$id = $this->m_suivi_adwords->nouveau($valeurs);
            if ($id === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            } else {
                $this->session->set_flashdata('success', "Propriétaire a été enregistré avec succès");
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            }*/
            $resultat = $this->m_suivi_adwords->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Propriétaire a été enregistré avec succès",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection); 
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                = new stdClass();
            $listes_valeurs         = new stdClass();

            $valeurs->mots_clefs = $this->input->post('mots_clefs');
            $valeurs->distribution_prospectus = $this->input->post('distribution_prospectus');
            $valeurs->distribution_de_prospectus = $this->input->post('distribution_de_prospectus');
            $valeurs->distribution_de_flyer = $this->input->post('distribution_de_flyer');
            $valeurs->distribution_de_prospectus_en_boites_aux_lettres = $this->input->post('distribution_de_prospectus_en_boites_aux_lettres');
            $valeurs->distribution_de_publicite = $this->input->post('distribution_de_publicite');
            $valeurs->societe_de_distribution_de_prospectus = $this->input->post('societe_de_distribution_de_prospectus');
            $valeurs->tarif_distribution_de_prospectus = $this->input->post('tarif_distribution_de_prospectus');
            $valeurs->distribution_prospectus_paris = $this->input->post('distribution_prospectus_paris');
            $valeurs->distribution_de_prospectus_paris = $this->input->post('distribution_de_prospectus_paris');
            $valeurs->distribution_de_flyer_paris = $this->input->post('distribution_de_flyer_paris');
            $valeurs->distribution_de_publicite_paris = $this->input->post('distribution_de_publicite_paris');
            $valeurs->mot= $this->input->post('mot');

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_suivi_adwords->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Suivi adword",
                'page'           => "templates/form",
                'menu'           => "Extra|Nouveau Suivi_adwords",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'suivi_adwords',
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
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim'),
            array('field' => 'mots_clefs', 'label' => "mots clefs", 'rules' => 'trim|required'),
            array('field' => 'distribution_prospectus', 'label' => "distribution prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus', 'label' => "distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_de_flyer', 'label' => "distribution de flyer", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus_en_boites_aux_lettres', 'label' => "distribution de prospectus en boites aux lettres", 'rules' => 'trim'),
            array('field' => 'distribution_de_publicite', 'label' => "distribution de publicité", 'rules' => 'trim'),
            array('field' => 'societe_de_distribution_de_prospectus', 'label' => "societe de distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'tarif_distribution_de_prospectus', 'label' => "tarif distribution de prospectus", 'rules' => 'trim'),
            array('field' => 'distribution_prospectus_paris', 'label' => "distribution prospectus paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_prospectus_paris', 'label' => "distribution de prospectus paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_flyer_paris', 'label' => "distribution de flyer paris", 'rules' => 'trim'),
            array('field' => 'distribution_de_publicite_paris', 'label' => "distribution de publicité paris", 'rules' => 'trim'),
            array('field' => 'mot', 'label' => "mot", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'mots_clefs' => $this->input->post('mots_clefs'),
                'distribution_prospectus' => $this->input->post('distribution_prospectus'),
                'distribution_de_prospectus' => $this->input->post('distribution_de_prospectus'),
                'distribution_de_flyer' => $this->input->post('distribution_de_flyer'),
                'distribution_de_prospectus_en_boites_aux_lettres' => $this->input->post('distribution_de_prospectus_en_boites_aux_lettres'),
                'distribution_de_publicite' => $this->input->post('distribution_de_publicite'),
                'societe_de_distribution_de_prospectus' => $this->input->post('societe_de_distribution_de_prospectus'),
                'tarif_distribution_de_prospectus' => $this->input->post('tarif_distribution_de_prospectus'),
                'distribution_prospectus_paris' => $this->input->post('distribution_prospectus_paris'),
                'distribution_de_prospectus_paris' => $this->input->post('distribution_de_prospectus_paris'),
                'distribution_de_flyer_paris' => $this->input->post('distribution_de_flyer_paris'),
                'distribution_de_publicite_paris' => $this->input->post('distribution_de_publicite_paris'),
                'mot' => $this->input->post('mot')
            );

            /*$resultat = $this->m_suivi_adwords->maj($valeurs, $id);
            if ($resultat === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('suivi_adwords/detail/' . $id);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Propriétaire a été modifié";
                }
                $this->session->set_flashdata('success', $message);
                redirect('suivi_adwords');
            }*/
            $resultat = $this->m_suivi_adwords->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Propriétaire a été modifié";
                    $ajaxData = array(
                       'event' => array(
                           'controleur' => $this->my_controleur_from_class(__CLASS__),
                           'type' => 'recordchange',
                           'id' => $id,
                           'timeStamp' => round(microtime(true) * 1000),
                           ),
                       );
                }
                $this->my_set_action_response($ajax, true, $message,'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'suivi_adwords/detail/'.$id;
            redirect($redirection);	
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_suivi_adwords->detail($id);
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_suivi_adwords->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Modifier Suivi adword",
                'page'         => "templates/form",
                'menu'         => "Extra|Edit Suivi Adwords",
                'scripts'      => $scripts,
                'id'           => $id,
                'values'       => $valeurs,
                'action'       => "modif",
                'multipart'    => true,
                'confirmation' => 'Enregistrer',
                'controleur'   => 'suivi_adwords',
                'methode'      => 'modification',
                'descripteur'  => $descripteur,
            );
            /*$layout = "layouts/standard";
            $this->load->view($layout, $data);*/
            $this->my_set_form_display_response($ajax,$data);
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
            $valeurs = $this->m_suivi_adwords->detail($id);

            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'suivi_adwords/modification', 'primary'),
                array("Archiver", 'suivi_adwords/archive', 'warning'),
                array("Supprimer", 'suivi_adwords/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'mots_clefs' => array("mots clefs", 'VARCHAR 50', 'text', 'mots_clefs'),
                    'distribution_prospectus' => array("distribution prospectus", 'VARCHAR 50', 'text', 'distribution_prospectus'),
                    'distribution_de_prospectus' => array("distribution de prospectus", 'VARCHAR 50', 'text', 'distribution_de_prospectus'),
                    'distribution_de_flyer' => array("distribution de flyer", 'VARCHAR 50', 'text', 'distribution_de_flyer'),
                    'distribution_de_prospectus_en_boites_aux_lettres' => array("distribution de prospectus en boites aux lettres", 'VARCHAR 50', 'text', 'distribution_de_prospectus_en_boites_aux_lettres'),
                    'distribution_de_publicite' => array("distribution de publicité", 'VARCHAR 50', 'text', 'distribution_de_publicite'),
                    'societe_de_distribution_de_prospectus' => array("societe de distribution de prospectus", 'VARCHAR 50', 'text', 'societe_de_distribution_de_prospectus'),
                    'tarif_distribution_de_prospectus' => array("tarif distribution de prospectus", 'VARCHAR 50', 'text', 'tarif_distribution_de_prospectus'),
                    'distribution_prospectus_paris' => array("distribution prospectus paris", 'VARCHAR 50', 'text', 'distribution_prospectus_paris'),
                    'distribution_de_prospectus_paris' => array("distribution de prospectus paris", 'VARCHAR 50', 'text', 'distribution_de_prospectus_paris'),
                    'distribution_de_flyer_paris' => array("distribution de flyer paris", 'VARCHAR 50', 'text', 'distribution_de_flyer_paris'),
                    'distribution_de_publicite_paris' => array("distribution de publicité paris", 'VARCHAR 50', 'text', 'distribution_de_publicite_paris'),
                    'mot' => array("mot", 'VARCHAR 50', 'text', 'mot'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Suivi adword",
                'page'         => "templates/detail",
                'menu'         => "Extra|Suivi_adwords",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'suivi_adwords',
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
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        $resultat = $this->m_suivi_adwords->archive($id);
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
            $this->session->set_flashdata('success', "Propriétaire a été archivé");
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
            $this->my_set_action_response($ajax,true,"Propriétaire a été archivé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Delete Suivi_adwords Data
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
        $resultat = $this->m_suivi_adwords->remove($id);
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
            $this->session->set_flashdata('success', "Propriétaire a été supprimé");
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
            $this->my_set_action_response($ajax,true,"Propriétaire a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_suivi_adwords->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_suivi_adwords->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_suivi_adwords->unremove($id);
        }
    }
}
// EOF
