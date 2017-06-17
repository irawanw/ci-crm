<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Objectif extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        /*array(
            "Nouveau" => array('objectif/nouveau', 'plus', true, 'objectif_nouveau'),
        ),
        array(
            //"Consulter" => array('objectif/detail', 'eye-open', false, 'objectif_detail'),
            "Consulter/Modifier"  => array('objectif/modification', 'pencil', false, 'objectif_modification'),
            "Supprimer" => array('#', 'trash', false, 'objectif_supprimer'),
        ),*/
        array(
            "Nouveau" => array('objectif/nouveau', 'plus', true, 'objectif_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('objectif/modification', 'pencil', false, 'objectif_modification', null, array('form')),
            "Supprimer"          => array('objectif/remove', 'trash', false, 'objectif_supprimer', "Veuillez confirmer la suppression de cette objectif", array('confirm-modify' => array('objectif/index'))),
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
        $this->load->model('m_objectif');
    }


    /******************************
     * List of objectif Data
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
            // array("Ajouter un e-mailing pages jaunes","objectif/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'objectif/index',
            'detail'             => array('objectif/detail', 'objectifs_id', 'description'),
            'archive'            => array('objectif/archive', 'objectifs_id', 'archive'),
            'champs'             => $this->m_objectif->get_champs('read'),
            'filterable_columns' => $this->m_objectif->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: objectif/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'objectif/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'objectif/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'objectif/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'objectif',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("objectif/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('objectif', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Objectifs",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Objectif",
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
            $resultat = $this->m_objectif->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'objectifs_id' => 't_objectif',
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

                $resultat = $this->m_objectif->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_objectif->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_objectif->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Objectifs'
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
            array('field' => 'titre', 'label' => "Titre", 'rules' => 'trim|required'),
            array('field' => 'nature', 'label' => "Nature", 'rules' => 'trim'),
            array('field' => 'date_limite', 'label' => "Date Limite", 'rules' => 'trim'),
            array('field' => 'resultat_date_limite', 'label' => "Résultat a date limite", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'titre'                => $this->input->post('titre'),
                'nature'               => $this->input->post('nature'),
                'date_limite'          => formatte_date_to_bd($this->input->post('date_limite')),
                'resultat_date_limite' => formatte_date_to_bd($this->input->post('resultat_date_limite')),
            );

            $id = $this->m_objectif->nouveau($valeurs);
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
                $this->session->set_flashdata('success', "Objectif a été enregistré avec succès");
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            }*/
            if ($id === false) {
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
                $this->my_set_action_response($ajax, true, "Objectif a été enregistré avec succès",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                       = new stdClass();
            $valeurs->titre                = $this->input->post('titre');
            $valeurs->nature               = $this->input->post('nature');
            $valeurs->date_limite          = formatte_date_to_bd($this->input->post('date_limite'));
            $valeurs->resultat_date_limite = formatte_date_to_bd($this->input->post('resultat_date_limite'));

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_objectif->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Ajouter un nouveau Objectif",
                'page'         => "templates/form",
                'menu'         => "Extra|Create Objectif",
                'scripts'      => $scripts,
                'values'       => $valeurs,
                'action'       => "création",
                'multipart'    => false,
                'confirmation' => 'Enregistrer',
                'controleur'   => 'objectif',
                'methode'      => __FUNCTION__,
                'descripteur'  => $descripteur,
            );
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
            $valeurs = $this->m_objectif->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'objectif/modification', 'primary'),
                array("Archiver", 'objectif/archive', 'warning'),
                array("Supprimer", 'objectif/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'titre'                => array("Titre", 'VARCHAR 50', 'text', 'titre'),
                    'nature'               => array("Nature", 'VARCHAR 50', 'text', 'nature'),
                    'date_limite'          => array("Date Limite", 'DATE', 'date', 'date_limite'),
                    'resultat_date_limite' => array("Résultat Date Limite", 'DATE', 'date', 'resultat_date_limite'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Objectif",
                'page'         => "templates/detail",
                'menu'         => "Extra|Objectif",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'objectif',
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
            array('field' => 'titre', 'label' => "Titre", 'rules' => 'trim|required'),
            array('field' => 'nature', 'label' => "Nature", 'rules' => 'trim'),
            array('field' => 'date_limite', 'label' => "Date Limite", 'rules' => 'trim'),
            array('field' => 'resultat_date_limite', 'label' => "Résultat a date limite", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'titre'                => $this->input->post('titre'),
                'nature'               => $this->input->post('nature'),
                'date_limite'          => formatte_date_to_bd($this->input->post('date_limite')),
                'resultat_date_limite' => formatte_date_to_bd($this->input->post('resultat_date_limite')),
            );

            $resultat = $this->m_objectif->maj($valeurs, $id);
            /*if ($resultat === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('objectif/detail/' . $id);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Objectif a été modifié";
                }
                $this->session->set_flashdata('success', $message);
                redirect('objectif');
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
                    $message = "Objectif a été modifiée";
                    $ajaxData = array(
                       'event' => array(
                           'controleur' => $this->my_controleur_from_class(__CLASS__),
                           'type' => 'recordchange',
                           'id' => $id,
                           'timeStamp' => round(microtime(true) * 1000),
                           ),
                       );
                }
                $this->my_set_action_response($ajax,true,$message, 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'objectif/detail/'.$id;
            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs   = $this->m_objectif->detail($id);
            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_objectif->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Modifier Objectif",
                'page'         => "templates/form",
                'menu'         => "Extra|Edit Objectif",
                'scripts'      => $scripts,
                'id'           => $id,
                'values'       => $valeurs,
                'action'       => "modif",
                'multipart'    => false,
                'confirmation' => 'Enregistrer',
                'controleur'   => 'objectif',
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
        $resultat = $this->m_objectif->archive($id);
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
            $this->session->set_flashdata('success', "Objectif a été archivé");
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
            $this->my_set_action_response($ajax,true,"Objectif a été archivée");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Delete Objectif Data
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
        $resultat = $this->m_objectif->remove($id);
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
            $this->session->set_flashdata('success', "Objectif a été supprimé");
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
            $this->my_set_action_response($ajax,true,"Objectif a été supprimée", 'info', $ajaxData);
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
            $resultat = $this->m_objectif->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_objectif->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_objectif->unremove($id);
        }
    }
}
// EOF
