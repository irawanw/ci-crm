<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class List_utilisateurs extends MY_Controller
{
    private $profil;
	private $barre_action = array(
        array(
            "Nouveau" => array('list_utilisateurs/nouveau', 'plus', true, 'list_utilisateurs_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier"  => array('list_utilisateurs/modification', 'pencil', false, 'list_utilisateurs_modification',null, array('form')),
            "Dupliquer" => array('list_utilisateurs/dupliquer', 'duplicate', false, 'list_utilisateurs_dupliquer',"Veuillez confirmer la duplique du users", array('confirm-modify' => array('list_utilisateurs/index'))),
			"Archiver" => array('list_utilisateurs/archive', 'folder-close', false, 'list_utilisateurs_archive',"Veuillez confirmer la archive du users", array('confirm-modify' => array('list_utilisateurs/index'))),
			"Supprimer" => array('list_utilisateurs/remove', 'trash', false, 'list_utilisateurs_supprimer',"Veuillez confirmer la suppression du users", array('confirm-modify' => array('list_utilisateurs/index'))),
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
        $this->load->model('m_list_utilisateurs');
    }

    /******************************
     * List of utilisateurs
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
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'list_utilisateurs/index',
            'detail'             => array('list_utilisateurs/detail', 'owner_id', 'description'),
            'archive'            => array('list_utilisateurs/archive', 'owner_id', 'archive'),
            'champs'             => $this->m_list_utilisateurs->get_champs('read'),
            'filterable_columns' => $this->m_list_utilisateurs->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: owners/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'list_utilisateurs/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'list_utilisateurs/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'list_utilisateurs/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'list_utilisateurs',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("list_utilisateurs/liste-js", array(), true);
        $scripts[] = $this->load->view('list_utilisateurs/form-js', array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('list_utilisateurs', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des utilisateurs",
            'page'         => "templates/datatables",
            'menu'         => "Personnel|List Utilisateurs",
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
     * Ajax call for List
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
            $resultat = $this->m_list_utilisateurs->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'utl_id' => 't_utilisateurs',
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

                $resultat = $this->m_list_utilisateurs->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_list_utilisateurs->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_list_utilisateurs->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'List_utilisateurs'
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
     * Creer utilisateur
     ******************************/
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->model(array('m_utilisateurs', 'm_employes'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'utl_login', 'label' => "Login", 'rules' => 'trim|required'),
            array('field' => 'utl_mot_de_passe', 'label' => "Mot de passe", 'rules' => 'trim'),
            array('field' => 'utl_type', 'label' => "Fonction", 'rules' => 'required'),
            array('field' => 'utl_profil', 'label' => "Profil", 'rules' => 'required'),
            array('field' => 'utl_date_fin', 'label' => "Date de fin de validité", 'rules' => 'trim'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
            array('field' => 'emp_nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'emp_prenom', 'label' => "Prénom", 'rules' => 'trim|required'),
            array('field' => 'utl_ensigne[]', 'label' => "Enseignes", 'rules' => 'required'),
            //array('field' => 'emp_fonction', 'label' => "Fonction", 'rules' => 'required'),
            array('field' => 'emp_date_entree', 'label' => "Date d'entrée", 'rules' => 'trim|required'),
            array('field' => 'emp_date_sortie', 'label' => "Date de sortie", 'rules' => 'trim'),
            array('field' => 'emp_notes', 'label' => "Remarques", 'rules' => 'trim'),
            array('field' => 'emp_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'emp_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'emp_telephone1', 'label' => "Téléphone 1", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_telephone2', 'label' => "Téléphone 2", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'emp_h_jour', 'label' => "Nb. heures / jour", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_h_semaine', 'label' => "Nb. heures / semaine", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_h_mois', 'label' => "Nb. heures / mois", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_commission', 'label' => "Commissions", 'rules' => 'required'),
            array('field' => 'emp_cout_heure', 'label' => "Coût horaire", 'rules' => 'trim|greater_than_equal_to[0]'),
            array('field' => 'emp_cv_vehicule', 'label' => "Nb. CV véhicule", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_immatriculation', 'label' => "Immatriculation", 'rules' => 'trim'),
            array('field' => 'emp_ptc', 'label' => "Poids total en charge", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_indemnite_kilometrique', 'label' => "Indemnité Kilométrique", 'rules' => 'trim'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $utl_ensigne_array = $this->input->post('utl_ensigne', true) ? $this->input->post('utl_ensigne', true) : array();
            $utl_ensigne = implode(",", $utl_ensigne_array);
            // validation réussie utilisateurs
            $valeurs_util = array(
                'utl_login'            => $this->input->post('utl_login'),
                'utl_mot_de_passe'     => $this->input->post('utl_mot_de_passe'),
                'utl_type'             => $this->input->post('utl_type'),
                'utl_sous_traitant'    => $this->input->post('utl_sous_traitant'),
                'utl_profil'           => $this->input->post('utl_profil'),
                'utl_date_fin'         => formatte_date_to_bd($this->input->post('utl_date_fin')),
                'utl_en_production'    => $this->input->post('utl_en_production'),
                'utl_duree_alerte'     => "20",
                'utl_son_alerte'       => "1",
                'utl_affichage_alerte' => "1",
                'utl_ensigne'          => $utl_ensigne,
            );

            // validation réussie employes
            $valeurs_emp = array(
                'emp_civilite'               => $this->input->post('emp_civilite'),
                'emp_nom'                    => $this->input->post('emp_nom'),
                'emp_prenom'                 => $this->input->post('emp_prenom'),
                //'emp_fonction'               => $this->input->post('emp_fonction'),
                'emp_commission'             => $this->input->post('emp_commission'),
                'emp_date_entree'            => formatte_date_to_bd($this->input->post('emp_date_entree')),
                'emp_date_sortie'            => formatte_date_to_bd($this->input->post('emp_date_sortie')),
                'emp_etat'                   => $this->input->post('emp_etat'),
                'emp_notes'                  => $this->input->post('emp_notes'),
                'emp_adresse'                => $this->input->post('emp_adresse'),
                'emp_cp'                     => $this->input->post('emp_cp'),
                'emp_ville'                  => $this->input->post('emp_ville'),
                'emp_telephone1'             => $this->input->post('emp_telephone1'),
                'emp_telephone2'             => $this->input->post('emp_telephone2'),
                'emp_email'                  => $this->input->post('emp_email'),
                'emp_h_jour'                 => $this->input->post('emp_h_jour'),
                'emp_h_semaine'              => $this->input->post('emp_h_semaine'),
                'emp_h_mois'                 => $this->input->post('emp_h_mois'),
                'emp_cout_heure'             => $this->input->post('emp_cout_heure'),
                'emp_cv_vehicule'            => $this->input->post('emp_cv_vehicule'),
                'emp_immatriculation'        => $this->input->post('emp_immatriculation'),
                'emp_indemnite_kilometrique' => $this->input->post('emp_indemnite_kilometrique'),
                'emp_ptc'                    => $this->input->post('emp_ptc'),
            );

            if (!isset($valeurs_util['utl_en_production'])) {
                $valeurs_util['utl_en_production'] = 0;
            }

            /*if (!isset($valeurs_emp['emp_commission'])) {
                $valeurs_emp['emp_commission'] = 0;
            }*/

            //insert employes
            $utl_employe = $this->m_employes->nouveau($valeurs_emp);
			
			
            if ($utl_employe) {
                //insert utilisateurs
                $valeurs_util['utl_employe']= $utl_employe;
                $resultat					= $this->m_utilisateurs->nouveau($valeurs_util);
            }
			
			if ($resultat === false) {
					$this->my_set_action_response($ajax, false);
				}
				else {
					$this->my_set_action_response($ajax, true, "Propriétaire a été enregistré avec succès");
					$ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
					$this->my_set_action_response($ajax, true, "Utilisateur a été enregistré avec succès",'info', $ajaxData);
				}
				if ($ajax) {
					return;
				}
				$redirection = $this->session->userdata('_url_retour');
				if (! $redirection) $redirection = '';
				redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                    = new stdClass();
            $listes_valeurs             = new stdClass();
			
            $valeurs->utl_login         		 = $this->input->post('utl_login');
            $valeurs->utl_mot_de_passe  		 = $this->input->post('utl_mot_de_passe');
            $valeurs->utl_type          		 = $this->input->post('utl_type');
            $valeurs->utl_sous_traitant 		 = $this->input->post('utl_sous_traitant');
            $valeurs->utl_profil        		 = $this->input->post('utl_profil');
            $valeurs->utl_date_fin      		 = $this->input->post('utl_date_fin');
            $utl_en_production 					 = $this->input->post('utl_en_production');
            $valeurs->utl_en_production 		 = !isset($utl_en_production) ? 1 : $utl_en_production;
			
			$valeurs->utl_employe       		 = $this->input->post('utl_employe');
            $valeurs->emp_nom                    = $this->input->post('emp_nom');
            $valeurs->emp_prenom                 = $this->input->post('emp_prenom');
            $valeurs->utl_ensigne                = $this->input->post('utl_ensigne');
            //$valeurs->emp_fonction               = $this->input->post('emp_fonction');
            $valeurs->emp_commission             = $this->input->post('emp_commission');
            $valeurs->emp_date_entree            = $this->input->post('emp_date_entree');
            $valeurs->emp_date_sortie            = $this->input->post('emp_date_sortie');
			$emp_civilite      					 = $this->input->post('emp_civilite');
            $emp_etat          					 = $this->input->post('emp_etat');
            $valeurs->emp_notes                  = $this->input->post('emp_notes');
			$valeurs->emp_etat          		 = !isset($emp_etat) ? 1 : $emp_etat;
			$valeurs->emp_civilite      		 = !isset($emp_civilite) ? 1 : $emp_civilite;
			  
            $valeurs->emp_adresse                = $this->input->post('emp_adresse');
            $valeurs->emp_cp                     = $this->input->post('emp_cp');
            $valeurs->emp_ville                  = $this->input->post('emp_ville');
            $valeurs->emp_telephone1             = $this->input->post('emp_telephone1');
            $valeurs->emp_telephone2             = $this->input->post('emp_telephone2');
            $valeurs->emp_email                  = $this->input->post('emp_email');
			
            $valeurs->emp_h_jour                 = $this->input->post('emp_h_jour');
            $valeurs->emp_h_semaine              = $this->input->post('emp_h_semaine');
            $valeurs->emp_h_mois                 = $this->input->post('emp_h_mois');
            $valeurs->emp_cout_heure             = $this->input->post('emp_cout_heure');
            $valeurs->emp_cv_vehicule            = $this->input->post('emp_cv_vehicule');
            $valeurs->emp_immatriculation        = $this->input->post('emp_immatriculation');
            $valeurs->emp_ptc                    = $this->input->post('emp_ptc');
            $valeurs->emp_indemnite_kilometrique = $this->input->post('emp_indemnite_kilometrique');

            $listes_valeurs->utl_type          = $this->m_list_utilisateurs->liste_type();
            $listes_valeurs->utl_employe       = $this->m_list_utilisateurs->liste_employe();
            $listes_valeurs->utl_sous_traitant = $this->m_list_utilisateurs->type_option();
            $listes_valeurs->utl_profil        = $this->m_list_utilisateurs->liste_profil();            
            $listes_valeurs->emp_civilite      = $this->m_list_utilisateurs->liste_civilite();           
            //$listes_valeurs->emp_fonction      = $this->m_list_utilisateurs->liste_fonction();            
            $listes_valeurs->emp_etat          = $this->m_list_utilisateurs->liste_etat();
            $listes_valeurs->emp_commission    = $this->m_list_utilisateurs->liste_commision_option();
            $listes_valeurs->utl_ensigne       = $this->m_list_utilisateurs->liste_ensigne_option();

            $scripts   = array();
            

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_list_utilisateurs->get_champs('write'),
                'onglets' => array(
                    array("Utilisateur", array('utl_login', 'utl_mot_de_passe', '__utl_mot_de_passe', 'utl_type', 'utl_sous_traitant', 'utl_profil', 'utl_date_fin', 'utl_en_production','emp_civilite','emp_nom', 'emp_prenom','utl_ensigne','emp_etat','emp_notes')),
                    // array("Employé", array('emp_civilite', 'emp_nom', 'emp_prenom','utl_ensigne', 'emp_date_entree', 'emp_date_sortie', 'emp_etat', 'emp_notes')),
                    array("Coordonnées", array('emp_adresse', 'emp_cp', 'emp_ville', 'emp_telephone1', 'emp_telephone2', 'emp_email')),
                    array("Infos salariés", array('emp_date_entree', 'emp_date_sortie','emp_h_jour', 'emp_h_semaine', 'emp_h_mois','emp_commission', 'emp_cout_heure', 'emp_cv_vehicule', 'emp_immatriculation', 'emp_ptc', 'emp_indemnite_kilometrique')),
                ),
            );

            $data = array(
                'title'          => "nouveau list utilisateurs",
                'page'           => "templates/form",
                'menu'           => "Personnel|List utilisateurs",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'list_utilisateurs',
                'methode'        =>  __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
			
			$this->my_set_form_display_response($ajax, $data);
        }
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
            $valeurs = $this->m_utilisateurs->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'list_utilisateurs/modification', 'primary'),
                array("Archiver", 'list_utilisateurs/archive', 'warning'),
                array("Supprimer", 'list_utilisateurs/remove', 'danger'),
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
     * Edit function for Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->model(array('m_utilisateurs', 'm_employes'));
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        $valeurs = $this->m_list_utilisateurs->detail($id);

        // règles de validation
        $config = array(
            array('field' => 'utl_login', 'label' => "Login", 'rules' => 'trim|required'),
            array('field' => 'utl_mot_de_passe', 'label' => "Mot de passe", 'rules' => 'trim'),
            array('field' => 'utl_type', 'label' => "Fonction", 'rules' => 'required'),
            array('field' => 'utl_profil', 'label' => "Profil", 'rules' => 'required'),
            array('field' => 'utl_date_fin', 'label' => "Date de fin de validité", 'rules' => 'trim'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
            array('field' => 'emp_nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'emp_prenom', 'label' => "Prénom", 'rules' => 'trim|required'),
            array('field' => 'utl_ensigne[]', 'label' => "Enseignes", 'rules' => 'required'),
            //array('field' => 'emp_fonction', 'label' => "Fonction", 'rules' => 'required'),
            array('field' => 'emp_date_entree', 'label' => "Date d'entrée", 'rules' => 'trim|required'),
            array('field' => 'emp_date_sortie', 'label' => "Date de sortie", 'rules' => 'trim'),
            array('field' => 'emp_notes', 'label' => "Remarques", 'rules' => 'trim'),
            array('field' => 'emp_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'emp_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'emp_telephone1', 'label' => "Téléphone 1", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_telephone2', 'label' => "Téléphone 2", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'emp_h_jour', 'label' => "Nb. heures / jour", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_h_semaine', 'label' => "Nb. heures / semaine", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_h_mois', 'label' => "Nb. heures / mois", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_commission', 'label' => "Commissions", 'rules' => 'required'),
            array('field' => 'emp_cout_heure', 'label' => "Coût horaire", 'rules' => 'trim|greater_than_equal_to[0]'),
            array('field' => 'emp_cv_vehicule', 'label' => "Nb. CV véhicule", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_immatriculation', 'label' => "Immatriculation", 'rules' => 'trim'),
            array('field' => 'emp_ptc', 'label' => "Poids total en charge", 'rules' => 'trim|is_natural'),
            array('field' => 'emp_indemnite_kilometrique', 'label' => "Indemnité Kilométrique", 'rules' => 'trim'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {
            $utl_ensigne_array = $this->input->post('utl_ensigne', true) ? $this->input->post('utl_ensigne', true) : array();
            $utl_ensigne = implode(",", $utl_ensigne_array);
            // validation réussie utilisateurs
            $valeurs_util = array(
                'utl_login'            => $this->input->post('utl_login'),
                'utl_mot_de_passe'     => $this->input->post('utl_mot_de_passe'),
                'utl_type'             => $this->input->post('utl_type'),
                'utl_sous_traitant'    => $this->input->post('utl_sous_traitant'),
                'utl_profil'           => $this->input->post('utl_profil'),
                'utl_date_fin'         => formatte_date_to_bd($this->input->post('utl_date_fin')),
                'utl_en_production'    => $this->input->post('utl_en_production'),
                'utl_ensigne'          => $utl_ensigne,
                'utl_duree_alerte'     => "20",
                'utl_son_alerte'       => "1",
                'utl_affichage_alerte' => "1",
            );

            // validation réussie employes
            $valeurs_emp = array(
                'emp_civilite'               => $this->input->post('emp_civilite'),
                'emp_nom'                    => $this->input->post('emp_nom'),
                'emp_prenom'                 => $this->input->post('emp_prenom'),
                //'emp_fonction'               => $this->input->post('emp_fonction'),
                'emp_commission'             => $this->input->post('emp_commission'),
                'emp_date_entree'            => formatte_date_to_bd($this->input->post('emp_date_entree')),
                'emp_date_sortie'            => formatte_date_to_bd($this->input->post('emp_date_sortie')),
                'emp_etat'                   => $this->input->post('emp_etat'),
                'emp_notes'                  => $this->input->post('emp_notes'),
                'emp_adresse'                => $this->input->post('emp_adresse'),
                'emp_cp'                     => $this->input->post('emp_cp'),
                'emp_ville'                  => $this->input->post('emp_ville'),
                'emp_telephone1'             => $this->input->post('emp_telephone1'),
                'emp_telephone2'             => $this->input->post('emp_telephone2'),
                'emp_email'                  => $this->input->post('emp_email'),
                'emp_h_jour'                 => $this->input->post('emp_h_jour'),
                'emp_h_semaine'              => $this->input->post('emp_h_semaine'),
                'emp_h_mois'                 => $this->input->post('emp_h_mois'),
                'emp_cout_heure'             => $this->input->post('emp_cout_heure'),
                'emp_cv_vehicule'            => $this->input->post('emp_cv_vehicule'),
                'emp_immatriculation'        => $this->input->post('emp_immatriculation'),
                'emp_indemnite_kilometrique' => $this->input->post('emp_indemnite_kilometrique'),
                'emp_ptc'                    => $this->input->post('emp_ptc'),
            );

            if (!isset($valeurs_util['utl_en_production'])) {
                $valeurs_util['utl_en_production'] = 0;
            }

            /*if (!isset($valeurs_emp['emp_commission'])) {
                $valeurs_emp['emp_commission'] = 0;
            }*/

			//update utilisateurs                
			$resultat = $this->m_utilisateurs->maj($valeurs_util, $id);

			if ($resultat === false) {
			$this->my_set_action_response($ajax,false);
			$redirection = '';

			} else {
				//update employes
				$emp_id = $valeurs->utl_employe;
				$resultat = $this->m_employes->maj($valeurs_emp, $emp_id);
				if ($resultat == 0) {
					$message = "Pas de modification demandée";
					$ajaxData = null;
				}
				else {
					$message = "Data a été modifiée";
				}
				$ajaxData = array(
							'event' => array(
							'controleur' => $this->my_controleur_from_class(__CLASS__),
							'type' => 'recordchange',
							'id' => $id,
							'timeStamp' => round(microtime(true) * 1000),
						),
					);
				$this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
			}
				
			if ($ajax) {
				return;
			}
			$redirection = 'list_utilisateurs/detail/'.$id;
			redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
			$listes_valeurs             		= new stdClass();
            $listes_valeurs->utl_type          = $this->m_list_utilisateurs->liste_type();
            $listes_valeurs->utl_employe       	= $this->m_list_utilisateurs->liste_employe();
            $listes_valeurs->utl_sous_traitant 	= $this->m_list_utilisateurs->type_option();;
            $listes_valeurs->utl_profil        	= $this->m_list_utilisateurs->liste_profil();            
            $listes_valeurs->emp_civilite      	= $this->m_list_utilisateurs->liste_civilite();           
            //$listes_valeurs->emp_fonction     	= $this->m_list_utilisateurs->liste_fonction();            
            $listes_valeurs->emp_etat         	= $this->m_list_utilisateurs->liste_etat();
            $listes_valeurs->emp_commission    = $this->m_list_utilisateurs->liste_commision_option();
            $listes_valeurs->utl_ensigne       = $this->m_list_utilisateurs->liste_ensigne_option();

            $scripts   = array();
            //$scripts[] = $this->load->view('list_utilisateurs/form-js', array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_list_utilisateurs->get_champs('write'),
                'onglets' => array(
                    array("Utilisateur", array('utl_login', 'utl_mot_de_passe', '__utl_mot_de_passe', 'utl_type', 'utl_sous_traitant', 'utl_profil', 'utl_date_fin', 'utl_en_production','emp_civilite','emp_nom', 'emp_prenom','utl_ensigne','emp_etat','emp_notes')),
                    // array("Employé", array('emp_civilite', 'emp_nom', 'emp_prenom','utl_ensigne', 'emp_date_entree', 'emp_date_sortie', 'emp_etat', 'emp_notes')),
                    array("Coordonnées", array('emp_adresse', 'emp_cp', 'emp_ville', 'emp_telephone1', 'emp_telephone2', 'emp_email')),
                    array("Infos salariés", array('emp_date_entree', 'emp_date_sortie','emp_h_jour', 'emp_h_semaine', 'emp_h_mois','emp_commission', 'emp_cout_heure', 'emp_cv_vehicule', 'emp_immatriculation', 'emp_ptc', 'emp_indemnite_kilometrique')),
                ),
            );
            $data = array(
                'title'          => "Modifier utilisateur",
                'page'           => "templates/form",
                'menu'           => "Personnel/ Edit utilisateur",
                'scripts'        => $scripts,
				'id' 			 => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'list_utilisateurs',
                'methode' 		 => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
			
			$this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Dupliquer Data
     ******************************/
    public function dupliquer($id, $ajax=false)
    {
		if ($this->input->method() != 'post') {
            die;
        }
		$redirection = 'list_utilisateurs/detail/'.$id;
        if (!$redirection) {
			$redirection = '';
        }
        $resultat = $this->m_list_utilisateurs->dupliquer($id);
        if ($resultat === false) {
           $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                 'event' => array(
                     'controleur' => $this->my_controleur_from_class(__CLASS__),
                     'type'       => 'recordadd',
                     'id'         => $resultat,
                     'timeStamp'  => round(microtime(true) * 1000),
                     'redirect'   => $redirection,
                 ),
             );
              $this->my_set_action_response($ajax, true, "Propriétaire a été dupliquer", 'info',$ajaxData);
			if ($ajax) {
            return;
        }
            redirect($redirection);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
		if ($this->input->method() != 'post') {
            die;
        }
		$redirection = 'list_utilisateurs/detail/'.$id;
        if (!$redirection) {
			$redirection = '';
        }
        $resultat = $this->m_list_utilisateurs->archive($id);
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
              $this->my_set_action_response($ajax, true, "List_utilisateurs a été archive", 'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Delete Owners Data
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
        $resultat = $this->m_list_utilisateurs->remove($id);
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
              $this->my_set_action_response($ajax, true, "List utilisateurs a été supprimé", 'info',$ajaxData);
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
            $resultat = $this->m_list_utilisateurs->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_list_utilisateurs->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_list_utilisateurs->unremove($id);
        }
    }
}
// EOF
