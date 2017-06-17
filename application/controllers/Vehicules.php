<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_vehicules m_vehicules
*/
class Vehicules extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('vehicules/nouveau', 'plus', true, 'vehicules_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('*vehicules/detail','eye-open',false,'vehicules_detail'),
            "Consulter/Modifier" => array('vehicules/modification', 'pencil', false, 'vehicules_modification', null, array('form')),
            "Supprimer"          => array('vehicules/remove', 'trash', false, 'vehicules_supprimer', "Veuillez confirmer la suppression du vehicule", array('confirm-delete' => array('vehicules/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'vehicules_voir_liste'),
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
        $this->load->model('m_vehicules');
    }

    /******************************
     * List of Vehicules Data
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
            //array("Nouveau véhicule","vehicules/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'vehicules/index',
            'detail'             => array('vehicules/detail', 'vehicules_id', 'description'),
            'archive'            => array('vehicules/archive', 'vehicules_id', 'archive'),
            'champs'             => $this->m_vehicules->get_champs('read'),
            'filterable_columns' => $this->m_vehicules->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: vehicules/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'vehicules/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'vehicules/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'vehicules/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'vehicules',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("vehicules/liste-js", array(
            'url_upload' => site_url('vehicules/upload_carte_grise'),
        ), true);
        $scripts[] = $this->load->view('vehicules/form-js', array(
            'field_name' => 'carte_grise',
            'url_upload' => site_url('vehicules/doupload_multiple'),
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('vehicules', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Véhicules",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Vehicules",
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

    /******************************
     * Ajax call for Purchase List
     ******************************/
    public function index_json($id = 0)
    {
        $pagelength = (int) $this->input->post('length');
        $pagestart  = (int) $this->input->post('start');

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
            $resultat = $this->m_vehicules->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'vehicules_id' => 't_vehicules',
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

                $resultat = $this->m_vehicules->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_vehicules->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_vehicules->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Vehicules'
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
     * New Vehicules
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'marque', 'label' => "Marque", 'rules' => 'trim|required'),
            array('field' => 'modele', 'label' => "Modele", 'rules' => 'trim'),
            array('field' => 'annee', 'label' => "Année", 'rules' => 'trim'),
            array('field' => 'immatriculation', 'label' => "Immatriculation", 'rules' => 'trim'),
            array('field' => 'assureur', 'label' => "Assureur", 'rules' => 'trim'),
            array('field' => 'numero_police', 'label' => "Numero Police", 'rules' => 'trim'),
            array('field' => 'propietaire', 'label' => "Propietaire", 'rules' => 'trim'),
            array('field' => 'utilisateur', 'label' => "Utilisateur Habituel", 'rules' => 'trim'),
            array('field' => 'dernier_controle_date', 'label' => "Dernier Controle Technique", 'rules' => 'trim'),
            array('field' => 'prochain_controle_date', 'label' => "Prochain Controle Technique", 'rules' => 'trim'),
            array('field' => 'list_reparation', 'label' => "Liste des Réparations", 'rules' => 'trim'),
            array('field' => 'formule_assurance', 'label' => "Formule Assurance", 'rules' => 'trim'),
            array('field' => 'prix_annuel_assurance', 'label' => "Prix Annuel Assurance", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'marque'                 => $this->input->post('marque'),
                'modele'                 => $this->input->post('modele'),
                'annee'                  => $this->input->post('annee'),
                'immatriculation'        => $this->input->post('immatriculation'),
                'assureur'               => $this->input->post('assureur'),
                'numero_police'          => $this->input->post('numero_police'),
                'propietaire'            => $this->input->post('propietaire'),
                'utilisateur'            => $this->input->post('utilisateur'),
                'dernier_controle_date'  => formatte_date_to_bd($this->input->post('dernier_controle_date')),
                'prochain_controle_date' => formatte_date_to_bd($this->input->post('prochain_controle_date')),
                'list_reparation'        => $this->input->post('list_reparation'),
                'formule_assurance'        => $this->input->post('formule_assurance'),
                'prix_annuel_assurance'        => $this->input->post('prix_annuel_assurance'),
            );

            $resultat = $this->m_vehicules->nouveau($valeurs);
            if ($resultat === false) {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, false);
            }
            else {
                //upload file when it have value
                if ($this->input->post('carte_grise') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('carte_grise'));
                    $this->m_files->update_row($id, $file_ids);
                }
                
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Véhicule a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);

        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                         = new stdClass();
            $listes_valeurs                  = new stdClass();
            $valeurs->marque                 = $this->input->post('marque');
            $valeurs->modele                 = $this->input->post('modele');
            $valeurs->annee                  = $this->input->post('annee');
            $valeurs->immatriculation        = $this->input->post('immatriculation');
            $valeurs->assureur               = $this->input->post('assureur');
            $valeurs->numero_police          = $this->input->post('numero_police');
            $valeurs->propietaire            = $this->input->post('propietaire');
            $valeurs->utilisateur            = $this->input->post('utilisateur');
            $valeurs->dernier_controle_date  = $this->input->post('dernier_controle_date');
            $valeurs->prochain_controle_date = $this->input->post('prochain_controle_date');
            $valeurs->list_reparation        = $this->input->post('list_reparation');
            $valeurs->formule_assurance      = $this->input->post('formule_assurance');
            $valeurs->prix_annuel_assurance  = $this->input->post('prix_annuel_assurance');
            $valeurs->carte_grise            = $this->input->post('carte_grise');

            $this->db->order_by('emp_nom', 'ASC');
            $q                           = $this->db->get('t_employes');
            $listes_valeurs->utilisateur = $q->result();
            $scripts                     = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_vehicules->get_champs('write'),
                'onglets' => array(
                    array("Vehicules", array('marque', 'modele', 'annee', 'immatriculation', 'assureur', 'numero_police', 'propietaire', 'utilisateur', 'dernier_controle_date', 'prochain_controle_date', 'list_reparation', 'formule_assurance','prix_annuel_assurance','carte_grise')),
                ),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Vehicule",
                'page'           => "templates/form",
                'menu'           => "Agenda|Nouveau Vehicules",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'vehicules',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Purchase Data
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
            $valeurs = $this->m_vehicules->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'vehicules/modification', 'primary'),
                array("Archiver", 'vehicules/archive', 'warning'),
                array("Supprimer", 'vehicules/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    //'vehicules_id' => array("Vehicules#",'VARCHAR 50','text','vehicules_id'),
                    'marque'                 => array("Marque", 'VARCHAR 50', 'text', 'marque'),
                    'modele'                 => array("Modele", 'VARCHAR 50', 'text', 'modele'),
                    'annee'                  => array("Annéee", 'VARCHAR 50', 'text', 'annee'),
                    'immatriculation'        => array("Immatriculation", 'VARCHAR 50', 'text', 'immatriculation'),
                    'assureur'               => array("Assureur", 'VARCHAR 50', 'text', 'assureur'),
                    'numero_police'          => array("Numero Police", 'VARCHAR 50', 'text', 'numero_police'),
                    'propietaire'            => array("Propietaire", 'VARCHAR 50', 'text', 'propietaire'),
                    'utilisateur'            => array("Utilisateur Habituel", 'VARCHAR 50', 'text', 'utilisateur'),
                    'dernier_controle_date'  => array("Dernier Controle Technique", 'DATE', 'date', 'dernier_controle_date'),
                    'prochain_controle_date' => array("Prochain Controle Technique", 'DATE', 'date', 'prochain_controle_date'),
                    'list_reparation'        => array("Liste des Réparations", 'VARCHAR 50', 'text', 'list_reparation'),
                    'formule_assurance'      => array("Formule Assurance", 'VARCHAR 50','text','formule_assurance'),
                    'prix_annuel_assurance'  => array("Prix Annuel Assurance", 'VARCHAR 50','text','prix_annuel_assurance'),
                    'carte_grise'            => array("Carte Grise", 'VARCHAR 50', 'text', 'carte_grise'),
                ),
                'onglets' => array(
                    array("Vehicules", array('marque', 'modele', 'annee', 'immatriculation', 'assureur', 'numero_police', 'propietaire', 'utilisateur', 'dernier_controle_date', 'prochain_controle_date', 'list_reparation', 'formule_assurance','prix_annuel_assurance','carte_grise')),
                ),
            );

            $data = array(
                'title'        => "Détail of suivi des Vehicule",
                'page'         => "templates/detail",
                'menu'         => "Extra|Vehicules",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'vehicules',
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
     * Edit function for Purchase Data
     ******************************/
    public function modification($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'marque', 'label' => "Marque", 'rules' => 'trim|required'),
            array('field' => 'modele', 'label' => "Modele", 'rules' => 'trim|required'),
            array('field' => 'annee', 'label' => "Année", 'rules' => 'trim'),
            array('field' => 'immatriculation', 'label' => "Immatriculation", 'rules' => 'trim'),
            array('field' => 'assureur', 'label' => "Assureur", 'rules' => 'trim'),
            array('field' => 'numero_police', 'label' => "Numero Police", 'rules' => 'trim'),
            array('field' => 'propietaire', 'label' => "Propietaire", 'rules' => 'trim'),
            array('field' => 'utilisateur', 'label' => "Utilisateur Habituel", 'rules' => 'trim'),
            array('field' => 'dernier_controle_date', 'label' => "Dernier Controle Technique", 'rules' => 'trim'),
            array('field' => 'prochain_controle_date', 'label' => "Prochain Controle Technique", 'rules' => 'trim'),
            array('field' => 'list_reparation', 'label' => "Liste des Réparations", 'rules' => 'trim'),
            array('field' => 'formule_assurance', 'label' => "Formule Assurance", 'rules' => 'trim'),
            array('field' => 'prix_annuel_assurance', 'label' => "Prix Annuel Assurance", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'marque'                 => $this->input->post('marque'),
                'modele'                 => $this->input->post('modele'),
                'annee'                  => $this->input->post('annee'),
                'immatriculation'        => $this->input->post('immatriculation'),
                'assureur'               => $this->input->post('assureur'),
                'numero_police'          => $this->input->post('numero_police'),
                'propietaire'            => $this->input->post('propietaire'),
                'utilisateur'            => $this->input->post('utilisateur'),
                'dernier_controle_date'  => formatte_date_to_bd($this->input->post('dernier_controle_date')),
                'prochain_controle_date' => formatte_date_to_bd($this->input->post('prochain_controle_date')),
                'list_reparation'        => $this->input->post('list_reparation'),
                'formule_assurance'      => $this->input->post('formule_assurance'),
                'prix_annuel_assurance'  => $this->input->post('prix_annuel_assurance'),
            );

            $resultat = $this->m_vehicules->maj($valeurs, $id);
            
            $redirection = 'vehicules/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                //upload file when it have value
                if ($this->input->post('carte_grise') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('carte_grise'));
                    $this->m_files->update_row($id, $file_ids);
                }

                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                     );
                 }
                 else {
                     $message = "Véhicule a été modifié";
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
            $valeurs        = $this->m_vehicules->detail($id);
            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('carte_grise');
            if (isset($valeur)) {
                $valeurs->carte_grise = $this->input->post('carte_grise');
            }
            $this->db->order_by('emp_nom', 'ASC');
            $q                           = $this->db->get('t_employes');
            $listes_valeurs->utilisateur = $q->result();

            $scripts   = array();
            $scripts[] = $this->load->view('vehicules/form-js', array(
                'field_name' => 'carte_grise',
                'url_upload' => site_url('vehicules/doupload_multiple'),
            ), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_vehicules->get_champs('write'),
                'onglets' => array(
                    array("Vehicules", array('marque', 'modele', 'annee', 'immatriculation', 'assureur', 'numero_police', 'propietaire', 'utilisateur', 'dernier_controle_date', 'prochain_controle_date', 'list_reparation','formule_assurance','prix_annuel_assurance' ,'carte_grise')),
                ),
            );

            $data = array(
                'title'          => "Modifier Vehicule",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Vehicules",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'vehicules',
                'methode'        => 'modification',
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
        $resultat = $this->m_vehicules->archive($id);
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
            $this->session->set_flashdata('success', "Vehicules a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Purchase Data
     ******************************/
    public function remove($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_vehicules->remove($id);

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
            $this->my_set_action_response($ajax, true, "Host a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function doupload_multiple()
    {
        $this->load->model('m_files');
        $upPath = FCPATH . '/fichiers/carte_grise/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "*",
            'overwrite'     => false,
            'max_size'      => "20480000",
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('files')) {
            echo json_encode(array('status' => false, 'error' => $error));
        } else {
            $upload_data = $this->upload->data();

            $data = array(
                'name'     => 'vehicules_carte_grise',
                'filename' => $upload_data['file_name'],
                'path'     => $upload_data['full_path'],
            );

            $resultat = $this->m_files->nouveau($data);

            if ($resultat) {
                echo json_encode(array('status' => true, 'id' => $resultat));
            } else {
                echo json_encode(array('status' => false, 'error' => "Failed insert files"));
            }
        }
    }

    public function doupload_file()
    {
        $upPath = FCPATH . '/fichiers/carte_grise/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "*",
            'overwrite'     => false,
            'max_size'      => "20480000",
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('files')) {
            $data['file_name'] = '';
            $data['error']     = $this->upload->display_errors();
        } else {
            $data          = $this->upload->data();
            $data['error'] = null;
        }
        return $data;
    }

    public function upload_carte_grise()
    {
        $this->load->model('m_files');
        $data = $this->doupload_file();
        if ($data['error'] == null) {
            $valeurs = array(
                'row_id'   => $this->input->post('upload_id'),
                'name'     => 'vehicules_carte_grise',
                'filename' => $data['file_name'],
                'path'     => $data['full_path'],
            );

            $resultat = $this->m_files->nouveau($valeurs);

            echo json_encode(array('status' => true, 'id' => $resultat));
        } else {
            echo json_encode(array('status' => false, 'error' => $data['error']));
        }
    }

    public function get_carte_grise_files($id = null)
    {
        if ($id) {
            $files = $this->m_vehicules->get_carte_grise_files($id);
            foreach ($files as $file) {
                echo '<div id="file-container-' . $file->file_id . '">
                        <button type="button" onclick="showConfirmRemoveFile(' . $file->file_id . ')" class="btn btn-warning btn-xs btn-delete-file">x</button>
                        <a target="_blank" href="' . base_url('fichiers/carte_grise') . '/' . $file->filename . '">' . $file->filename . '</a>
                      </div>';
            }

            echo '<div class="alert alert-danger" style="display:none;" id="confirm-remove-file">
                        <p>Etes-vous certain de vouloir supprimer le fichier?</p>
                        <button onclick="hideConfirmRemoveFile()" type="button" class="btn btn-default">Non</button>
                        <button class="btn btn-warning" type="button" id="btn-remove-file-ok">Oui</button>
                      </div>';
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_vehicules->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_vehicules->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_vehicules->unremove($id);
        }
    }
}
// EOF
