<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_utilisateurs m_utilisateurs
*/
class Utilisateurs extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('utilisateurs/nouveau','plus',true,'boites_archive_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*utilisateurs/detail','eye-open',false,'boites_archive_detail',null,array('view')),
                    "Modifier" => array('utilisateurs/modification','pencil',false,'boites_archive_modification',null,array('form')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('utilisateurs/detail','eye-open',true,'utilisateurs_detail',null,array('view', 'default-view')),
                    "Modifier" => array('utilisateurs/modification','pencil',true,'utilisateurs_modification',null,array('form')),
                    "Supprimer" => array('utilisateurs/suppression','trash',true,'utilisateurs_supprimer',"Veuillez confirmer la suppression de l'utilisateur",array('confirm-delete' => array('utilisateurs/index'))),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Detail" => array(
            array(
                "Consulter" => array('utilisateurs/detail','eye-open',true,'utilisateurs_detail',null,array('view')),
                "Modifier" => array('utilisateurs/modification','pencil',true,'utilisateurs_modification',null,array('form')),
                "Supprimer" => array('utilisateurs/suppression','trash',true,'utilisateurs_supprimer',"Veuillez confirmer la suppression de l'utilisateur",array('confirm-delete' => array('utilisateurs/index'))),
            ),
            array(
                "Nouveau message" => array('messages/nouveau_utilisateur','comment',true,'messages_nouveau_utilisateur',null,array('form')),
            ),
            array(
                "Nouvelle tâche" => array('taches/nouveau_utilisateur','tasks',true,'taches_nouveau_utilisateur',null,array('form')),
                "Historique des actions" => array('*actions/actions_utilisateur[]','zoom-in',true,'actions_utilisateur_historique'),
            ),
            array(
				"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            )
        ),
        "Mon_Compte" => array(
            array(
                "Tâches affectées" => array('*utilisateurs/taches[]','tasks',true,'mes_taches_affectees'),
                "Objectifs" => array('*utilisateurs/objectifs[]','screenshot',true,'mes_objectifs'),
            ),
            array(
                "Changer le mot de passe" => array('utilisateurs/chgt_mdp','lock',true,'mon_compte_mot_de_passe',null,array('form')),
            ),
            array(
                "Paramètres d'alerte" => array('utilisateurs/detail_param','bell',true,'mes_alertes',null,array('view')),
                "Vues personnelles" => array('vues/vues[]','eye-open',true,'mes_vues'),
            ),
        ),
        "Param" => array(
            array(
                "Consulter" => array('utilisateurs/detail_param','eye-open',true,'mes_alertes',null,array('view')),
                "Modifier" => array('utilisateurs/modification_param','pencil',true,'mes_alertes_modification',null,array('form')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_utilisateurs');
    }

    /******************************
    * Déconnexion
    ******************************/
    public function logout($id=0) {
        $id = $this->m_utilisateurs->deconnexion($id);
        if ($id === false) {
            $this->my_set_action_response(false, false);
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
        }
        else {
            $this->my_set_action_response(false,true,"Vous êtes déconnecté");
            $redirection = "";
        }
        redirect($redirection);
    }

    /******************************
    * Connexion
    ******************************/
    public function login() {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'utl_login','label'=>'Login','rules'=>'trim|required'),
            array('field'=>'utl_mot_de_passe','label'=>'Mot de passe','rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {

            // validation réussie
            $valeurs = array(
                'utl_login' => $this->input->post('utl_login'),
                'utl_mot_de_passe' => $this->input->post('utl_mot_de_passe')
            );
            $resultat = $this->m_utilisateurs->connexion($valeurs);
            if ($resultat === false) {
                sleep(2);
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger',"Identifiants non reconnus");
                }
                redirect("utilisateurs/login");
            }
            else {
                    $this->session->set_flashdata('success',"Vous êtes connecté");
                    redirect("utilisateurs/accueil");
                }
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('utl_login');
            $valeurs->utl_login = $valeur;
            $valeur = $this->input->post('utl_mot_de_passe');
            $valeurs->utl_mot_de_passe = $valeur;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_login' => array("Login",'text','utl_login',true),
                   'utl_mot_de_passe' => array("Mot de passe",'password-c','utl_mot_de_passe',false)
                ),
                'onglets' => array()
            );

            $data = array(
                'title' => "Connexion",
                'page' => "templates/form",
                'menu' => "",
                'values' => $valeurs,
                'action' => "login",
                'multipart' => false,
                'confirmation' => 'Se connecter',
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $layout="layouts/standard";
            $this->load->view($layout,$data);
        }
    }

    /******************************
    * Liste des utilisateurs
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'utilisateurs/index',
            'detail' => array('utilisateurs/detail','utl_id','utl_login'),
            'champs' => array(
                array('utl_login','text',"Login"),
                array('vtu_type','ref',"Type d'utilisateur",'v_types_utilisateurs'),
                array('emp_nom','ref',"Employé",'employes','utl_employe','emp_nom'),
                array('ctc_nom','ref',"Sous-traitant",'contacts','utl_sous_traitant','ctc_nom'),
                array('prf_nom','ref',"Profil",'profils','utl_profil','prf_nom'),
                array('utl_derniere_connexion','datetime',"Date de dernière connexion"),
                array('utl_date_fin','date',"Date de fin de validité"),
                array('utl_actif','text',"Actif"),
                array('utl_en_production','text',"En production"),
                array('utl_refid','text',"ID"),
                array('utl_nom','text',"Nom"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_utilisateurs->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'utilisateurs',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'utilisateurs',
                ),
            ), true);
        $scripts[] = $this->load->view("utilisateurs/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('utilisateurs',$this->session->id);
        $data = array(
            'title' => "Liste des utilisateurs",
            'page' => "templates/datatables",
            'menu' => "Personnel|Utilisateurs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'utilisateurs',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des utilisateurs (datasource)
    ******************************/
    public function index_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_utilisateurs->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'utl_login' => 't_utilisateurs',
                'emp_nom' => 't_employes',
                'ctc_nom' => 't_contacts',
                'prf_nom' => 't_profils',
                'utl_derniere_connexion' => 't_utilisateurs',
                'utl_date_fin' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_utilisateurs->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_utilisateurs->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des utilisateurs d'un profil
    ******************************/
    public function utilisateurs_profil($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'utilisateurs/utilisateurs_profil',
            'detail' => array('utilisateurs/detail','utl_id','utl_login'),
            'champs' => array(
                array('utl_login','text',"Login"),
                array('vtu_type','ref',"Type d'utilisateur",'v_types_utilisateurs'),
                array('emp_nom','ref',"Employé",'employes','utl_employe','emp_nom'),
                array('ctc_nom','ref',"Sous-traitant",'contacts','utl_sous_traitant','ctc_nom'),
                array('prf_nom','ref',"Profil",'profils','utl_profil','prf_nom'),
                array('utl_derniere_connexion','datetime',"Date de dernière connexion"),
                array('utl_date_fin','date',"Date de fin de validité"),
                array('utl_en_production','text',"En production"),
                array('utl_actif','text',"Actif"),
                array('utl_refid','text',"ID"),
                array('utl_nom','text',"Nom"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_utilisateurs->liste_par_profil_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('utilisateurs',$this->session->id);
        $data = array(
            'title' => "Liste des utilisateurs d'un profil",
            'page' => "templates/datatables",
            'menu' => "Personnel|Utilisateurs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'utilisateurs',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des utilisateurs d'un profil (datasource)
    ******************************/
    public function utilisateurs_profil_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_utilisateurs->liste_par_profil($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'utl_login' => 't_utilisateurs',
                'emp_nom' => 't_employes',
                'ctc_nom' => 't_contacts',
                'prf_nom' => 't_profils',
                'utl_derniere_connexion' => 't_utilisateurs',
                'utl_date_fin' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_utilisateurs->liste_par_profil($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_utilisateurs->liste_par_profil($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvel utilisateur
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'utl_login','label'=>"Login",'rules'=>'trim|required'),
            array('field'=>'utl_mot_de_passe','label'=>"Mot de passe",'rules'=>'trim'),
            array('field'=>'utl_type','label'=>"Type d'utilisateur",'rules'=>'required'),
            array('field'=>'utl_profil','label'=>"Profil",'rules'=>'required'),
            array('field'=>'utl_date_fin','label'=>"Date de fin de validité",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'utl_login' => $this->input->post('utl_login'),
                'utl_mot_de_passe' => $this->input->post('utl_mot_de_passe'),
                'utl_type' => $this->input->post('utl_type'),
                'utl_employe' => $this->input->post('utl_employe'),
                'utl_sous_traitant' => $this->input->post('utl_sous_traitant'),
                'utl_profil' => $this->input->post('utl_profil'),
                'utl_date_fin' => formatte_date_to_bd($this->input->post('utl_date_fin')),
                'utl_actif' => $this->input->post('utl_actif'),
                'utl_en_production' => $this->input->post('utl_en_production'),
                'utl_duree_alerte' => "20",
                'utl_son_alerte' => "1",
                'utl_affichage_alerte' => "1"
            );
            if (!isset($valeurs['utl_actif'])) {
                $valeurs['utl_actif'] = 0;
            }
            if (!isset($valeurs['utl_en_production'])) {
                $valeurs['utl_en_production'] = 0;
            }
            $id = $this->m_utilisateurs->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax,true,"L'utilisateur a été créé",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeurs->utl_login = $this->input->post('utl_login');
            $valeurs->utl_mot_de_passe = $this->input->post('utl_mot_de_passe');
            $valeurs->utl_type = $this->input->post('utl_type');
            $valeurs->utl_employe = $this->input->post('utl_employe');
            $valeurs->utl_sous_traitant = $this->input->post('utl_sous_traitant');
            $valeurs->utl_profil = $this->input->post('utl_profil');
            $valeurs->utl_date_fin = $this->input->post('utl_date_fin');
            $valeur = $this->input->post('utl_actif');
            if (!isset($valeur)) {
                $valeurs->utl_actif = 1;
            }
            else {
                $valeurs->utl_actif = $valeur;
            }
            $valeur = $this->input->post('utl_en_production');
            if (!isset($valeur)) {
                $valeurs->utl_en_production = 1;
            }
            else {
                $valeurs->utl_en_production = $valeur;
            }
            $this->db->order_by('vtu_type','ASC');
            $q = $this->db->get('v_types_utilisateurs');
            $listes_valeurs->utl_type = $q->result();
            $this->db->order_by('emp_nom','ASC');
            $q = $this->db->get('t_employes');
            $listes_valeurs->utl_employe = $q->result();
            $this->db->where("ctc_fournisseur=1");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->utl_sous_traitant = $q->result();
            $this->db->order_by('prf_nom','ASC');
            $q = $this->db->get('t_profils');
            $listes_valeurs->utl_profil = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#utl_date_fin").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_login' => array("Login",'text','utl_login',true),
                   'utl_mot_de_passe' => array("Mot de passe",'password-c','utl_mot_de_passe',false),
                   '__utl_mot_de_passe' => array("Mot de passe (confirmation)",'password-c','__utl_mot_de_passe',false),
                   'utl_type' => array("Type d'utilisateur",'radio-h',array('utl_type','vtu_id','vtu_type'),true),
                   'utl_employe' => array("Employé",'select',array('utl_employe','emp_id','emp_nom'),false),
                   'utl_sous_traitant' => array("Sous-traitant",'select',array('utl_sous_traitant','ctc_id','ctc_nom'),false),
                   'utl_profil' => array("Profil",'select',array('utl_profil','prf_id','prf_nom'),true),
                   'utl_date_fin' => array("Date de fin de validité",'date','utl_date_fin',false),
                   'utl_actif' => array("Actif",'checkbox','utl_actif',false),
                   'utl_en_production' => array("En production",'checkbox','utl_en_production',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvel utilisateur",
                'page' => "templates/form",
                'menu' => "Personnel|Nouvel utilisateur",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Détail d'un utilisateur
     * support AJAX
    ******************************/
    public function detail($id, $ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_utilisateurs->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'utilisateurs/modification','primary'),
            //    array("Déconnecter",'utilisateurs/deconnexion','warning'),
            //    array("Supprimer",'utilisateurs/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_login' => array("Login",'VARCHAR 20','text','utl_login'),
                   'utl_employe' => array("Employé",'REF','ref',array('employes','utl_employe','emp_nom')),
                   'utl_sous_traitant' => array("Sous-traitant",'REF','ref',array('contacts','utl_sous_traitant','ctc_nom')),
                   'utl_profil' => array("Profil",'REF','ref',array('profils','utl_profil','prf_nom')),
                   'utl_derniere_connexion' => array("Date de dernière connexion",'DATETIME','datetime','utl_derniere_connexion'),
                   'utl_date_fin' => array("Date de fin de validité",'DATE','date','utl_date_fin'),
                   'utl_actif' => array("Actif",'BOOL','checkbox','utl_actif'),
                   'utl_en_production' => array("En production",'BOOL','checkbox','utl_en_production')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un utilisateur",
                'page' => "templates/detail",
                'menu' => "Personnel|Utilisateur",
                'barre_action' => $this->barre_action["Detail"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un utilisateur
     * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'utl_login','label'=>"Login",'rules'=>'trim|required'),
            array('field'=>'utl_mot_de_passe','label'=>"Mot de passe",'rules'=>'trim'),
            array('field'=>'utl_profil','label'=>"Profil",'rules'=>'required'),
            array('field'=>'utl_date_fin','label'=>"Date de fin de validité",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );
        if ($this->input->post('utl_mot_de_passe') !== '') {
            $config[] = array('field'=>'__utl_mot_de_passe','label'=>"Mot de passe (confirmation)",'rules'=>'required|matches[utl_mot_de_passe]');
        }

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'utl_login' => $this->input->post('utl_login'),
                'utl_mot_de_passe' => $this->input->post('utl_mot_de_passe'),
                'utl_profil' => $this->input->post('utl_profil'),
                'utl_date_fin' => formatte_date_to_bd($this->input->post('utl_date_fin')),
                'utl_actif' => $this->input->post('utl_actif'),
                'utl_en_production' => $this->input->post('utl_en_production')
            );
            $resultat = $this->m_utilisateurs->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "L'utilisateur a été modifié";
                    $ajaxData = array(
                         'event' => array(
                             'controleur' => $this->my_controleur_from_class(__CLASS__),
                             'type' => 'recordchange',
                             'id' => $id,
                             'timeStamp' => round(microtime(true) * 1000),
                         ),
                    );
                }
                $this->my_set_action_response($ajax,true,$message,'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'utilisateurs/detail/'.$id;
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_utilisateurs->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('utl_login');
            if (isset($valeur)) {
                $valeurs->utl_login = $valeur;
            }
            $valeur = $this->input->post('utl_mot_de_passe');
            if (isset($valeur)) {
                $valeurs->utl_mot_de_passe = $valeur;
            }
            $valeur = $this->input->post('utl_profil');
            if (isset($valeur)) {
                $valeurs->utl_profil = $valeur;
            }
            $valeur = $this->input->post('utl_date_fin');
            if (isset($valeur)) {
                $valeurs->utl_date_fin = $valeur;
            }
            $valeur = $this->input->post('utl_actif');
            if (isset($valeur)) {
                $valeurs->utl_actif = $valeur;
            }
            $valeur = $this->input->post('utl_en_production');
            if (isset($valeur)) {
                $valeurs->utl_en_production = $valeur;
            }
            $this->db->order_by('prf_nom','ASC');
            $q = $this->db->get('t_profils');
            $listes_valeurs->utl_profil = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#utl_date_fin").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_login' => array("Login",'text','utl_login',true),
                   'utl_mot_de_passe' => array("Mot de passe",'password-c','utl_mot_de_passe',false),
                   '__utl_mot_de_passe' => array("Mot de passe (confirmation)",'password-c','__utl_mot_de_passe',false),
                   'utl_profil' => array("Profil",'select',array('utl_profil','prf_id','prf_nom'),true),
                   'utl_date_fin' => array("Date de fin de validité",'date','utl_date_fin',false),
                   'utl_actif' => array("Actif",'checkbox','utl_actif',false),
                   'utl_en_production' => array("En production",'checkbox','utl_en_production',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un utilisateur",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour d'utilisateur",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Suppression d'un utilisateur
     * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');

        $resultat = $this->m_utilisateurs->suppression($id);
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
            $this->my_set_action_response($ajax,true,"L'utilisateur a été supprimé",'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Mon compte
    * support AJAX
    ******************************/
    public function mon_compte($id=0,$ajax=false) {
        $id = $this->session->id;
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_utilisateurs->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Changer le mot de passe",'utilisateurs/chgt_mdp','primary')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_login' => array("Login",'VARCHAR 20','text','utl_login'),
                   'utl_employe' => array("Employé",'REF','ref',array('employes','utl_employe','emp_nom')),
                   'utl_profil' => array("Profil",'REF','ref',array('profils','utl_profil','prf_nom')),
                   'utl_derniere_connexion' => array("Date de dernière connexion",'DATETIME','datetime','utl_derniere_connexion')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mon compte",
                'page' => "templates/detail",
                'menu' => "Personnel|Mon compte",
                'barre_action' => $this->barre_action['Mon_Compte'],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour des paramètres d'alerte
    * support AJAX
    ******************************/
    public function modification_param($id=0,$ajax=false) {
        $id = $this->session->id;
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'utl_duree_alerte','label'=>"Durée d'affichage (s.)",'rules'=>'trim|required|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'utl_duree_alerte' => $this->input->post('utl_duree_alerte'),
                'utl_son_alerte' => $this->input->post('utl_son_alerte'),
                'utl_affichage_alerte' => $this->input->post('utl_affichage_alerte')
            );
            $resultat = $this->m_utilisateurs->maj_param($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Les paramètres d'alerte ont été modifiés";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            $redirection = "utilisateurs/mon_compte";
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_utilisateurs->detail_param($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('utl_duree_alerte');
            if (isset($valeur)) {
                $valeurs->utl_duree_alerte = $valeur;
            }
            $valeur = $this->input->post('utl_son_alerte');
            if (isset($valeur)) {
                $valeurs->utl_son_alerte = $valeur;
            }
            $valeur = $this->input->post('utl_affichage_alerte');
            if (isset($valeur)) {
                $valeurs->utl_affichage_alerte = $valeur;
            }
            $this->db->order_by('vso_son','ASC');
            $q = $this->db->get('v_sons');
            $listes_valeurs->utl_son_alerte = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_duree_alerte' => array("Durée d'affichage (s.)",'number','utl_duree_alerte',true),
                   'utl_son_alerte' => array("Son d'alerte",'select',array('utl_son_alerte','vso_id','vso_son'),false),
                   'utl_affichage_alerte' => array("Affichage des alertes des tâches déléguées",'checkbox','utl_affichage_alerte',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour des paramètres d'alerte",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour de paramètre",
                'barre_action' => $this->barre_action['Param'],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail des paramètres d'alerte
    * support AJAX
    ******************************/
    public function detail_param($id=0,$ajax=false) {
        $id = $this->session->id;
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_utilisateurs->detail_param($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_duree_alerte' => array("Durée d'affichage (s.)",'INT 2','number','utl_duree_alerte'),
                   'utl_son_alerte' => array("Son d'alerte",'REF','text','vso_son'),
                   'utl_affichage_alerte' => array("Affichage des alertes des tâches déléguées",'BOOL','checkbox','utl_affichage_alerte')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail des paramètres d'alerte",
                'page' => "templates/detail",
                'menu' => "Personnel|Paramètres",
                'barre_action' => $this->barre_action['Param'],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Changement du mot de passe
    ******************************/
    public function chgt_mdp($id=0,$ajax=false) {
            $id = $this->session->id;
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'utl_mot_de_passe','label'=>"Mot de passe",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );
        if ($this->input->post('utl_mot_de_passe') !== '') {
            $config[] = array('field'=>'__utl_mot_de_passe','label'=>"Mot de passe (confirmation)",'rules'=>'required|matches[utl_mot_de_passe]');
        }

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'utl_mot_de_passe' => $this->input->post('utl_mot_de_passe')
            );
            $resultat = $this->m_utilisateurs->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Le mot de passe a été changé";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'utilisateurs/detail/'.$id;
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_utilisateurs->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('utl_mot_de_passe');
            if (isset($valeur)) {
                $valeurs->utl_mot_de_passe = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'utl_mot_de_passe' => array("Mot de passe",'password-c','utl_mot_de_passe',false),
                   '__utl_mot_de_passe' => array("Mot de passe (confirmation)",'password-c','__utl_mot_de_passe',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Changement du mot de passe",
                'page' => "templates/form",
                'menu' => "Personnel|Changement de mot de passe",
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'utilisateurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Page d'accueil de l'utilisateur
     ******************************/
    public function accueil() {
        $id = $this->session->id;
        if (!isset($id)) redirect('');
        $this->load->model('m_messages');
        $messages = $this->m_messages->liste_non_lus($id);
        $data = array(
            'title' => "Bienvenue ".$this->session->utl_login,
            'page' => "utilisateurs/accueil",
            'menu' => "",
            'values' => array (
                'messages' => $messages
            )
        );
        $layout="layouts/accueil";
        $this->load->view($layout,$data);
    }

    /******************************
     * Objectifs
     ******************************/
    public function objectifs() {
        $id = $this->session->id;
        $utilisateur =  $this->m_utilisateurs->detail($id);
        redirect('objectifs/objectifs_employe/'.$utilisateur->utl_employe);
    }

    /******************************
     * Déconnexion d'un utilisateur
     ******************************/
    public function deconnexion($id) {
        $self = $this->session->id;
        if ($id != $self) {
            $this->m_utilisateurs->deconnexion_user($id);
            $this->session->set_flashdata('success',"L'utilisateur a été déconnecté");
        }
        redirect("utilisateurs");
    }

    /******************************
     * Taches affectées
     ******************************/
    public function taches() {
        $id = $this->session->id;
        $utilisateur =  $this->m_utilisateurs->detail($id);
        redirect('taches/taches_employe/'.$utilisateur->utl_employe);
    }

    /******************************
     * Web service liste des utilisateurs
     ******************************/
    public function web_service($cle='') {
        if ($cle != 'kJ45DYh-59') die('');
        $resultat = $this->m_utilisateurs->web_service();
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

}

// EOF