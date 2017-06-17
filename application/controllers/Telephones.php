<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */

/**
* @property M_telephones m_telephones
*/
class Telephones extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Ajouter une ligne" => array('telephones/nouveau', 'plus', true, 'telephones_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('telephones/detail','eye-open',false,'telephones_detail'),
            "Consulter/Modifier" => array('telephones/modification', 'pencil', false, 'telephones_modification', null, array('form')),
            "Dupliquer"          => array('telephones/dupliquer', 'duplicate', false, 'telephones_dupliquer', "Veuillez confirmer la duplication du telephone", array('confirm-delete' => array('telephones/index'))),
            "Supprimer"          => array('telephones/remove', 'trash', false, 'telephones_supprimer',"Veuillez confirmer la suppression du telephone", array('confirm-delete' => array('telephones/index'))),
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
        $this->load->model('m_telephones');
    }

    /******************************
     * List of Telephones Data
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
            //array("Ajouter une lignes","telephones/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'telephones/index',
            'detail'             => array('telephones/detail', 'telephones_id', 'description'),
            'archive'            => array('telephones/archive', 'telephones_id', 'archive'),
            'champs'             => $this->m_telephones->get_champs('read'),
            'filterable_columns' => $this->m_telephones->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: telephones/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'telephones/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'telephones/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'telephones/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'telephones',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);

        $scripts[] = $this->load->view("telephones/liste-js", array(), true);
        $scripts[] = $this->load->view('telephones/form-js.php', array(), true);


        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('telephones', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Telephones",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Telephones",
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
            $resultat = $this->m_telephones->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'telephones_id' => 't_telephones',
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

                $resultat = $this->m_telephones->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_telephones->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_telephones->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Telephones'
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

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_telephones->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Passe de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    /******************************
     * New Livraison
     ******************************/

    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'souscription_date', 'label' => "Date Souscription", 'rules' => 'trim|required'),
            array('field' => 'numero_client', 'label' => "Numéro de Client", 'rules' => 'trim'),
            array('field' => 'numero_de_compte_internet', 'label' => "Numéro de Compte Internet", 'rules' => 'trim'),
            array('field' => 'numero_de_tel_internet', 'label' => "Numéro de Tel Internet", 'rules' => 'trim'),
            array('field' => 'numero_tel', 'label' => "Numéro", 'rules' => 'trim'),
            array('field' => 'engagement_jusquau', 'label' => "Engagement Jusqu'au", 'rules' => 'trim'),
            array('field' => 'resiliation_date', 'Résiliation Effectuée à la Date de' => "Client", 'rules' => 'trim'),
            array('field' => 'etat', 'label' => "état", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'fornisseur', 'label' => "Fournisseur", 'rules' => 'trim'),
            array('field' => 'forfait_ligne_fixe', 'label' => "Forfait Ligne Fixe", 'rules' => 'trim'),
            array('field' => 'forfait_portable', 'label' => "Forfait Portable", 'rules' => 'trim'),
            array('field' => 'options', 'label' => "Options", 'rules' => 'trim'),
            array('field' => 'prix', 'label' => "Prix", 'rules' => 'trim'),
            array('field' => 'societe', 'label' => "Société", 'rules' => 'trim'),
            array('field' => 'lieu_ligne', 'label' => "Lieu où se Situe la Ligne", 'rules' => 'trim'),
            array('field' => 'utilisation_actuelle', 'label' => "Utilisation Actuelle", 'rules' => 'trim'),
            array('field' => 'utilisation_future', 'label' => "Utilisation Future", 'rules' => 'trim'),
            array('field' => 'problemes_resoudre', 'label' => "Problèmes à résoudre", 'rules' => 'trim'),
            array('field' => 'url', 'label' => "URL", 'rules' => 'trim|valid_url'),
            array('field' => 'user', 'label' => "ID", 'rules' => 'trim'),
            array('field' => 'mdp', 'label' => "MDP", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'souscription_date'         => formatte_date_to_bd($this->input->post('souscription_date')),
                'numero_client'             => $this->input->post('numero_client'),
                'numero_de_compte_internet' => $this->input->post('numero_de_compte_internet'),
                'numero_de_tel_internet'    => $this->input->post('numero_de_tel_internet'),
                'numero_tel'                => $this->input->post('numero_tel'),
                'engagement_jusquau'        => formatte_date_to_bd($this->input->post('engagement_jusquau')),
                'resiliation_date'          => formatte_date_to_bd($this->input->post('resiliation_date')),
                'etat'                      => $this->input->post('etat'),
                'type'                      => $this->input->post('type'),
                'fornisseur'                => $this->input->post('fornisseur'),
                'forfait_ligne_fixe'        => $this->input->post('forfait_ligne_fixe'),
                'forfait_portable'          => $this->input->post('forfait_portable'),
                'options'                   => $this->input->post('options'),
                'prix'                      => $this->input->post('prix'),
                'societe'                   => $this->input->post('societe'),
                'lieu_ligne'                => $this->input->post('lieu_ligne'),
                'utilisation_actuelle'      => $this->input->post('utilisation_actuelle'),
                'utilisation_future'        => $this->input->post('utilisation_future'),
                'problemes_resoudre'        => $this->input->post('problemes_resoudre'),
                'url'                       => $this->input->post('url'),
                'user'                      => $this->input->post('user'),
                'mdp'                       => $this->input->post('mdp'),
                'pas_engage'                => ($this->input->post('pas_engage') == '')? 0:1,
            );
            if (!isset($valeurs['pas_engage'])) {
                $valeurs['pas_engage'] = 0;
            }

            $resultat = $this->m_telephones->nouveau($valeurs);
            
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
                $this->my_set_action_response($ajax, true, "Telephone a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                            = new stdClass();
            $listes_valeurs                     = new stdClass();
            $valeurs->souscription_date         = formatte_date_to_bd($this->input->post('souscription_date'));
            $valeurs->numero_client             = $this->input->post('numero_client');
            $valeurs->numero_de_compte_internet = $this->input->post('numero_de_compte_internet');
            $valeurs->numero_de_tel_internet    = $this->input->post('numero_de_tel_internet');
            $valeurs->numero_tel                = $this->input->post('numero_tel');
            $valeurs->engagement_jusquau        = $this->input->post('engagement_jusquau');
            $valeurs->resiliation_date          = formatte_date_to_bd($this->input->post('resiliation_date'));
            $valeurs->etat                      = $this->input->post('etat');
            $valeurs->type                      = $this->input->post('type');
            $valeurs->fornisseur                = $this->input->post('fornisseur');
            $valeurs->forfait_ligne_fixe        = $this->input->post('forfait_ligne_fixe');
            $valeurs->forfait_portable          = $this->input->post('forfait_portable');
            $valeurs->options                   = $this->input->post('options');
            $valeurs->prix                      = $this->input->post('prix');
            $valeurs->societe                   = $this->input->post('societe');
            $valeurs->lieu_ligne                = $this->input->post('lieu_ligne');
            $valeurs->utilisation_actuelle      = $this->input->post('utilisation_actuelle');
            $valeurs->utilisation_future        = $this->input->post('utilisation_future');
            $valeurs->problemes_resoudre        = $this->input->post('problemes_resoudre');
            $valeurs->url                       = $this->input->post('url');
            $valeurs->user                      = $this->input->post('user');
            $valeurs->mdp                       = $this->input->post('mdp');
            $valeurs->pas_engage                = $this->input->post('pas_engage');

            $listes_valeurs->etat    = $this->m_telephones->etat_option();
            $listes_valeurs->type    = $this->m_telephones->type_option();
            $listes_valeurs->societe = $this->m_telephones->societe_option();

            $scripts   = array();


            // descripteur
            $descripteur = array(
                'champs'  => $this->m_telephones->get_champs('write'),
                'onglets' => array(
                    array("Telephones", array('souscription_date', 'numero_client', 'numero_de_compte_internet', 'numero_de_tel_internet', 'numero_tel', 'engagement_jusquau', 'pas_engage', 'resiliation_date', 'etat', 'type', 'fornisseur', 'forfait_ligne_fixe', 'forfait_portable', 'options', 'prix', 'societe', 'lieu_ligne', 'utilisation_actuelle', 'utilisation_future', 'problemes_resoudre', 'url', 'user', 'mdp')),
                ),
            );

            $data = array(
                'title' => "Ajouter un nouveau Telephone",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Telephones",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'telephones',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);           
        }
    }

    /******************************
     * Detail of Telephones Data
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
            $valeurs = $this->m_telephones->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'telephones/modification', 'primary'),
                array("Archiver", 'telephones/archive', 'warning'),
                array("Supprimer", 'telephones/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'souscription_date'         => array("Date Souscription", 'DATE', 'date', 'souscription_date'),
                    'numero_client'             => array("Numéro de Client", 'VARCHAR 50', 'text', 'numero_client'),
                    'numero_de_compte_internet' => array("Numéro Internet", 'VARCHAR 25', 'text', 'numero_de_compte_internet'),
                    'numero_de_tel_internet'    => array("Numéro Internet", 'VARCHAR 25', 'text', 'numero_de_tel_internet'),
                    'numero_tel'                => array("Numéro Tel", 'VARCHAR 50', 'text', 'numero_tel'),
                    'engagement_jusquau'        => array("Engagement Jusqu'au", 'DATE', 'date', 'engagement_jusquau'),
                    'resiliation_date'          => array("Résiliation Effectuée à la Date de", 'DATE', 'date', 'resiliation_date'),
                    'etat'                      => array("état", 'VARCHAR 50', 'text', 'etat'),
                    'type'                      => array("Type", 'VARCHAR 50', 'text', 'type'),
                    'fornisseur'                => array("Fournisseur", 'VARCHAR 50', 'text', 'fornisseur'),
                    'forfait_ligne_fixe'        => array("Forfait Ligne Fixe", 'VARCHAR 25', 'text', 'forfait_ligne_fixe'),
                    'forfait_portable'          => array("Forfait Portable", 'VARCHAR 25', 'text', 'forfait_portable'),
                    'options'                   => array("Options", 'VARCHAR 25', 'text', 'options'),
                    'prix'                      => array("Prix", 'VARCHAR 50', 'text', 'prix'),
                    'societe'                   => array("Société", 'VARCHAR 50', 'text', 'societe'),
                    'lieu_ligne'                => array("Lieu où se Situe la Ligne", 'VARCHAR 50', 'text', 'lieu_ligne'),
                    'utilisation_actuelle'      => array("Utilisation Actuelle", 'VARCHAR 50', 'text', 'utilisation_actuelle'),
                    'utilisation_future'        => array("Utilisation Future", 'VARCHAR 50', 'text', 'utilisation_future'),
                    'problemes_resoudre'        => array("Problèmes à résoudre", 'VARCHAR 50', 'text', 'problemes_resoudre'),
                    'url'                       => array("URL", 'VARCHAR 50', 'text', 'url'),
                    'user'                      => array("ID", 'VARCHAR 50', 'text', 'user'),
                    'mdp'                       => array("MDP", 'VARCHAR 50', 'text', 'mdp'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of suivi des Telephone",
                'page'         => "templates/detail",
                'menu'         => "Extra|Telephones",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'telephones',
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
     * Edit function for Telephones Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'souscription_date', 'label' => "Date Souscription", 'rules' => 'trim|required'),
            array('field' => 'numero_client', 'label' => "Numéro de Client", 'rules' => 'trim'),
            array('field' => 'numero_de_compte_internet', 'label' => "Numéro de Compte Internet", 'rules' => 'trim'),
            array('field' => 'numero_de_tel_internet', 'label' => "Numéro de Tel Internet", 'rules' => 'trim'),
            array('field' => 'numero_tel', 'label' => "Numéro", 'rules' => 'trim'),
            array('field' => 'engagement_jusquau', 'label' => "Engagement Jusqu'au", 'rules' => 'trim'),
            array('field' => 'resiliation_date', 'Résiliation Effectuée à la Date de' => "Client", 'rules' => 'trim'),
            array('field' => 'etat', 'label' => "état", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'fornisseur', 'label' => "Fournisseur", 'rules' => 'trim'),
            array('field' => 'forfait_ligne_fixe', 'label' => "Forfait Ligne Fixe", 'rules' => 'trim'),
            array('field' => 'forfait_portable', 'label' => "Forfait Portable", 'rules' => 'trim'),
            array('field' => 'options', 'label' => "Options", 'rules' => 'trim'),
            array('field' => 'prix', 'label' => "Prix", 'rules' => 'trim'),
            array('field' => 'societe', 'label' => "Société", 'rules' => 'trim'),
            array('field' => 'lieu_ligne', 'label' => "Lieu où se Situe la Ligne", 'rules' => 'trim'),
            array('field' => 'utilisation_actuelle', 'label' => "Utilisation Actuelle", 'rules' => 'trim'),
            array('field' => 'utilisation_future', 'label' => "Utilisation Future", 'rules' => 'trim'),
            array('field' => 'problemes_resoudre', 'label' => "Problèmes à résoudre", 'rules' => 'trim'),
            array('field' => 'url', 'label' => "URL", 'rules' => 'trim|valid_url'),
            array('field' => 'user', 'label' => "ID", 'rules' => 'trim'),
            array('field' => 'mdp', 'label' => "MDP", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'souscription_date'         => formatte_date_to_bd($this->input->post('souscription_date')),
                'numero_client'             => $this->input->post('numero_client'),
                'numero_de_compte_internet' => $this->input->post('numero_de_compte_internet'),
                'numero_de_tel_internet'    => $this->input->post('numero_de_tel_internet'),
                'numero_tel'                => $this->input->post('numero_tel'),
                'engagement_jusquau'        => formatte_date_to_bd($this->input->post('engagement_jusquau')),
                'pas_engage'                => $this->input->post('pas_engage'),
                'resiliation_date'          => formatte_date_to_bd($this->input->post('resiliation_date')),
                'etat'                      => $this->input->post('etat'),
                'type'                      => $this->input->post('type'),
                'fornisseur'                => $this->input->post('fornisseur'),
                'forfait_ligne_fixe'        => $this->input->post('forfait_ligne_fixe'),
                'forfait_portable'          => $this->input->post('forfait_portable'),
                'options'                   => $this->input->post('options'),
                'prix'                      => $this->input->post('prix'),
                'societe'                   => $this->input->post('societe'),
                'lieu_ligne'                => $this->input->post('lieu_ligne'),
                'utilisation_actuelle'      => $this->input->post('utilisation_actuelle'),
                'utilisation_future'        => $this->input->post('utilisation_future'),
                'problemes_resoudre'        => $this->input->post('problemes_resoudre'),
                'url'                       => $this->input->post('url'),
                'user'                      => $this->input->post('user'),
                'mdp'                       => $this->input->post('mdp'),
                'pas_engage'                => ($this->input->post('pas_engage') == '')? 0:1,
            );
            if (!isset($valeurs['pas_engage'])) {
                $valeurs['pas_engage'] = 0;
            }
			$resultat = $this->m_telephones->maj($valeurs, $id);
            
            $redirection = 'telephones/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Telephone a été modifié";
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
            $valeurs        = $this->m_telephones->detail($id);
            $listes_valeurs = new stdClass();

            $listes_valeurs->etat    = $this->m_telephones->etat_option();
            $listes_valeurs->type    = $this->m_telephones->type_option();
            $listes_valeurs->societe = $this->m_telephones->societe_option();

            $scripts   = array();           

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_telephones->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier Telephone",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Telephones",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'telephones',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax,$data);         
        }
    }

    /******************************
     * Dupliquer Data
     ******************************/
    public function dupliquer($id, $ajax = false)
    {
        $resultat = $this->m_telephones->dupliquer($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recordadd',
                    'id'         => $resultat,
                    'timeStamp'  => round(microtime(true) * 1000),
                ),
            );
            $this->my_set_action_response($ajax,true,"Telephone a été dupliquer", 'info', $ajaxData);
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_telephones->archive($id);
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
            $this->session->set_flashdata('success', "Telephones a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Telephones Data
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

        $resultat = $this->m_telephones->remove($id);

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
            $this->my_set_action_response($ajax, true, "Telephone a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_telephones->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_telephones->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_telephones->unremove($id);
        }
    }
}
// EOF
