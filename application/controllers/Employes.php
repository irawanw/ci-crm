<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_employes m_employes
*/
class Employes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('employes/nouveau','plus',true,'employes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*employes/detail','eye-open',false,'employes_detail',null,array('view')),
                    "Modifier" => array('employes/modification','pencil',false,'employes_modification',null,array('form')),
            ),
            array(
					"Export XLS"   => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('employes/nouveau','plus',true,'employes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('employes/detail','eye-open',true,'employes_detail',null,array('view')),
                    "Modifier" => array('employes/modification','pencil',true,'employes_modification',null,array('form')),
                    "Supprimer" => array('employes/remove','trash',true,'employes_remove',"Veuillez confirmer la suppression de l'employé(e)",array('confirm-action')),
            ),
        ),
        "Employe_Detail" => array(
            array(
                "Nouveau" => array('employes/nouveau','plus',true,'employes_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('employes/detail','eye-open',true,'employes_detail',null,array('view')),
                "Modifier" => array('employes/modification','pencil',true,'employes_modification',null,array('form')),
                "Supprimer" => array('employes/remove','trash',true,'employes_remove',"Veuillez confirmer la suppression de l'employé(e)",array('confirm-action'))
            ),
            array(
                "Tâches" => array('taches/taches_employe[]','tasks', true, 'taches_employe'),
                "Objectifs" => array('objectifs/objectifs_employe[]','screenshot',true,'objectifs_employe'),
            ),
            array(
                "Créer document" => array('documents_employes/nouveau','paperclip',true,'documents_employe_nouveau',null,array('form')),
                "Documents" => array('documents_employes/documents_employe[]','paperclip',true,'documents_employe'),
            ),
            array(
                "Envoyer email" => array('emails_emp/email','send',true,'envoi_email_employe',null,array('form')),
            ),
            array(
				"Export XLS"   => array('#', 'list-alt', true, 'export_xls'),
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_employes');
    }

    /******************************
    * Liste des employés
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'employes/index',
            'detail' => array('employes/detail','emp_id','emp_nom'),
            'champs' => $this->m_employes->get_champs('read'),
            'filterable_columns' => $this->m_employes->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'employes',
                'methode' => 'index'
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'employes',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'employes',
                ),
            ), true);
        //$scripts[] = $this->load->view("employes/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('employes',$this->session->id);
        $data = array(
            'title' => "Liste des employés",
            'page' => "templates/datatables",
            'menu' => "Personnel|Employés",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
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
    * Liste des employés (datasource)
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
            $resultat = $this->m_employes->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'emp_nom' => 't_employes',
                'emp_date_entree' => 't_employes',
                'emp_date_sortie' => 't_employes',
                'utl_login' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_employes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_employes->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvel employé(e)
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'emp_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'emp_prenom','label'=>"Prénom",'rules'=>'trim|required'),
            array('field'=>'emp_fonction','label'=>"Fonction",'rules'=>'required'),
            array('field'=>'emp_date_entree','label'=>"Date d'entrée",'rules'=>'trim|required'),
            array('field'=>'emp_date_sortie','label'=>"Date de sortie",'rules'=>'trim'),
            array('field'=>'emp_notes','label'=>"Remarques",'rules'=>'trim'),
            array('field'=>'emp_adresse','label'=>"Adresse",'rules'=>'trim'),
            array('field'=>'emp_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'emp_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'emp_telephone1','label'=>"Téléphone 1",'rules'=>'trim|is_natural'),
            array('field'=>'emp_telephone2','label'=>"Téléphone 2",'rules'=>'trim|is_natural'),
            array('field'=>'emp_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'emp_h_jour','label'=>"Nb. heures / jour",'rules'=>'trim|is_natural'),
            array('field'=>'emp_h_semaine','label'=>"Nb. heures / semaine",'rules'=>'trim|is_natural'),
            array('field'=>'emp_h_mois','label'=>"Nb. heures / mois",'rules'=>'trim|is_natural'),
            array('field'=>'emp_cout_heure','label'=>"Coût horaire",'rules'=>'trim|greater_than_equal_to[0]'),
            array('field'=>'emp_cv_vehicule','label'=>"Nb. CV véhicule",'rules'=>'trim|is_natural'),
            array('field'=>'emp_immatriculation','label'=>"Immatriculation",'rules'=>'trim'),
            array('field'=>'emp_ptc','label'=>"Poids total en charge",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'emp_civilite' => $this->input->post('emp_civilite'),
                'emp_nom' => $this->input->post('emp_nom'),
                'emp_prenom' => $this->input->post('emp_prenom'),
                'emp_fonction' => $this->input->post('emp_fonction'),
                'emp_commission' => $this->input->post('emp_commission'),
                'emp_date_entree' => formatte_date_to_bd($this->input->post('emp_date_entree')),
                'emp_date_sortie' => formatte_date_to_bd($this->input->post('emp_date_sortie')),
                'emp_etat' => $this->input->post('emp_etat'),
                'emp_notes' => $this->input->post('emp_notes'),
                'emp_adresse' => $this->input->post('emp_adresse'),
                'emp_cp' => $this->input->post('emp_cp'),
                'emp_ville' => $this->input->post('emp_ville'),
                'emp_telephone1' => $this->input->post('emp_telephone1'),
                'emp_telephone2' => $this->input->post('emp_telephone2'),
                'emp_email' => $this->input->post('emp_email'),
                'emp_h_jour' => $this->input->post('emp_h_jour'),
                'emp_h_semaine' => $this->input->post('emp_h_semaine'),
                'emp_h_mois' => $this->input->post('emp_h_mois'),
                'emp_cout_heure' => $this->input->post('emp_cout_heure'),
                'emp_cv_vehicule' => $this->input->post('emp_cv_vehicule'),
                'emp_immatriculation' => $this->input->post('emp_immatriculation'),
                'emp_ptc' => $this->input->post('emp_ptc')
            );
            if (!isset($valeurs['emp_commission'])) {
                $valeurs['emp_commission'] = 0;
            }
			
            $resultat = $this->m_employes->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "L'employé(e) a été créé(e)",'info', $ajaxData);
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
            $valeur = $this->input->post('emp_civilite');
            if (!isset($valeur)) {
                $valeurs->emp_civilite = 1;
            }
            else {
                $valeurs->emp_civilite = $valeur;
            }
            $valeurs->emp_nom = $this->input->post('emp_nom');
            $valeurs->emp_prenom = $this->input->post('emp_prenom');
            $valeurs->emp_fonction = $this->input->post('emp_fonction');
            $valeurs->emp_commission = $this->input->post('emp_commission');
            $valeurs->emp_date_entree = $this->input->post('emp_date_entree');
            $valeurs->emp_date_sortie = $this->input->post('emp_date_sortie');
            $valeur = $this->input->post('emp_etat');
            if (!isset($valeur)) {
                $valeurs->emp_etat = 1;
            }
            else {
                $valeurs->emp_etat = $valeur;
            }
            $valeurs->emp_notes = $this->input->post('emp_notes');
            $valeurs->emp_adresse = $this->input->post('emp_adresse');
            $valeurs->emp_cp = $this->input->post('emp_cp');
            $valeurs->emp_ville = $this->input->post('emp_ville');
            $valeurs->emp_telephone1 = $this->input->post('emp_telephone1');
            $valeurs->emp_telephone2 = $this->input->post('emp_telephone2');
            $valeurs->emp_email = $this->input->post('emp_email');
            $valeurs->emp_h_jour = $this->input->post('emp_h_jour');
            $valeurs->emp_h_semaine = $this->input->post('emp_h_semaine');
            $valeurs->emp_h_mois = $this->input->post('emp_h_mois');
            $valeurs->emp_cout_heure = $this->input->post('emp_cout_heure');
            $valeurs->emp_cv_vehicule = $this->input->post('emp_cv_vehicule');
            $valeurs->emp_immatriculation = $this->input->post('emp_immatriculation');
            $valeurs->emp_ptc = $this->input->post('emp_ptc');
            $this->db->order_by('vcv_civilite','ASC');
            $q = $this->db->get('v_civilites');
            $listes_valeurs->emp_civilite = $q->result();
            $this->db->order_by('vfo_fonction','ASC');
            $q = $this->db->get('v_fonctions');
            $listes_valeurs->emp_fonction = $q->result();
            $this->db->order_by('vee_etat','ASC');
            $q = $this->db->get('v_etats_employes');
            $listes_valeurs->emp_etat = $q->result();
            $scripts = array();
            if (!$ajax) {
                $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#emp_date_entree").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#emp_date_sortie").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_employes->get_champs('write'),
                'onglets' => array(
                    array("Employé", array('emp_civilite','emp_nom','emp_prenom','emp_fonction','emp_commission','emp_date_entree','emp_date_sortie','emp_etat','emp_notes')),
                    array("Coordonnées", array('emp_adresse','emp_cp','emp_ville','emp_telephone1','emp_telephone2','emp_email')),
                    array("Autres infos", array('emp_h_jour','emp_h_semaine','emp_h_mois','emp_cout_heure','emp_cv_vehicule','emp_immatriculation','emp_ptc'))
                )
            );

            $data = array(
                'title' => "Nouvel employé",
                'page' => "templates/form",
                'menu' => "Personnel|Nouvel employé",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'employes',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Détail d'un employé
    * support AJAX
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_employes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Employe_Detail"],$valeurs);

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'employes/modification','primary'),
            //    array("Supprimer",'employes/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'emp_civilite' => array("Civilite",'REF','text','vcv_civilite'),
                   'emp_nom' => array("Nom",'VARCHAR 50','text','emp_nom'),
                   'emp_prenom' => array("Prénom",'VARCHAR 50','text','emp_prenom'),
                   'emp_fonction' => array("Fonction",'REF','text','vfo_fonction'),
                   'emp_login' => array("Login",'REF_INV','text','emp_login'),
                   'emp_commission' => array("Commissions",'BOOL','checkbox','emp_commission'),
                   'emp_date_entree' => array("Date d'entrée",'DATE','date','emp_date_entree'),
                   'emp_date_sortie' => array("Date de sortie",'DATE','date','emp_date_sortie'),
                   'emp_etat' => array("Etat",'REF','text','vee_etat'),
                   'emp_notes' => array("Remarques",'VARCHAR 1000','textarea','emp_notes'),
                   'emp_adresse' => array("Adresse",'VARCHAR 400','textarea','emp_adresse'),
                   'emp_cp' => array("Code postal",'VARCHAR 5','number','emp_cp'),
                   'emp_ville' => array("Ville",'VARCHAR 50','text','emp_ville'),
                   'emp_telephone1' => array("Téléphone 1",'TELEPHONE','number','emp_telephone1'),
                   'emp_telephone2' => array("Téléphone 2",'TELEPHONE','number','emp_telephone2'),
                   'emp_email' => array("Email",'EMAIL','email','emp_email'),
                   'emp_h_jour' => array("Nb. heures / jour",'INT 2','number','emp_h_jour'),
                   'emp_h_semaine' => array("Nb. heures / semaine",'INT 2','number','emp_h_semaine'),
                   'emp_h_mois' => array("Nb. heures / mois",'INT 3','number','emp_h_mois'),
                   'emp_cout_heure' => array("Coût horaire",'DECIMAL 6,2','number','emp_cout_heure'),
                   'emp_cv_vehicule' => array("Nb. CV véhicule",'INT 2','number','emp_cv_vehicule'),
                   'emp_immatriculation' => array("Immatriculation",'VARCHAR 10','number','emp_immatriculation'),
                   'emp_ptc' => array("Poids total en charge",'INT 5','number','emp_ptc')
                ),
                'onglets' => array(
                    array("Employé", array('emp_civilite','emp_nom','emp_prenom','emp_fonction','emp_login','emp_commission','emp_date_entree','emp_date_sortie','emp_etat','emp_notes')),
                    array("Coordonnées", array('emp_adresse','emp_cp','emp_ville','emp_telephone1','emp_telephone2','emp_email')),
                    array("Autres infos", array('emp_h_jour','emp_h_semaine','emp_h_mois','emp_cout_heure','emp_cv_vehicule','emp_immatriculation','emp_ptc'))
                )
            );

            $data = array(
                'title' => "Détail d'un employé",
                'page' => "templates/detail",
                'menu' => "Personnel|Employé",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'employes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array       Barre d'action à modifier
     * @param $facture      M_employees Les infos de l'employé
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $employe) {
        $etats = array(
            'emails_emp/email' => ($employe->emp_email != ''),
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }


    /******************************
    * Mise à jour d'un employé
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'emp_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'emp_prenom','label'=>"Prénom",'rules'=>'trim|required'),
            array('field'=>'emp_fonction','label'=>"Fonction",'rules'=>'required'),
            array('field'=>'emp_date_entree','label'=>"Date d'entrée",'rules'=>'trim|required'),
            array('field'=>'emp_date_sortie','label'=>"Date de sortie",'rules'=>'trim'),
            array('field'=>'emp_notes','label'=>"Remarques",'rules'=>'trim'),
            array('field'=>'emp_adresse','label'=>"Adresse",'rules'=>'trim'),
            array('field'=>'emp_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'emp_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'emp_telephone1','label'=>"Téléphone 1",'rules'=>'trim|is_natural'),
            array('field'=>'emp_telephone2','label'=>"Téléphone 2",'rules'=>'trim|is_natural'),
            array('field'=>'emp_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'emp_h_jour','label'=>"Nb. heures / jour",'rules'=>'trim|is_natural'),
            array('field'=>'emp_h_semaine','label'=>"Nb. heures / semaine",'rules'=>'trim|is_natural'),
            array('field'=>'emp_h_mois','label'=>"Nb. heures / mois",'rules'=>'trim|is_natural'),
            array('field'=>'emp_cout_heure','label'=>"Coût horaire",'rules'=>'trim|greater_than_equal_to[0]'),
            array('field'=>'emp_cv_vehicule','label'=>"Nb. CV véhicule",'rules'=>'trim|is_natural'),
            array('field'=>'emp_immatriculation','label'=>"Immatriculation",'rules'=>'trim'),
            array('field'=>'emp_ptc','label'=>"Poids total en charge",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'emp_civilite' => $this->input->post('emp_civilite'),
                'emp_nom' => $this->input->post('emp_nom'),
                'emp_prenom' => $this->input->post('emp_prenom'),
                'emp_fonction' => $this->input->post('emp_fonction'),
                'emp_commission' => $this->input->post('emp_commission'),
                'emp_date_entree' => formatte_date_to_bd($this->input->post('emp_date_entree')),
                'emp_date_sortie' => formatte_date_to_bd($this->input->post('emp_date_sortie')),
                'emp_etat' => $this->input->post('emp_etat'),
                'emp_notes' => $this->input->post('emp_notes'),
                'emp_adresse' => $this->input->post('emp_adresse'),
                'emp_cp' => $this->input->post('emp_cp'),
                'emp_ville' => $this->input->post('emp_ville'),
                'emp_telephone1' => $this->input->post('emp_telephone1'),
                'emp_telephone2' => $this->input->post('emp_telephone2'),
                'emp_email' => $this->input->post('emp_email'),
                'emp_h_jour' => $this->input->post('emp_h_jour'),
                'emp_h_semaine' => $this->input->post('emp_h_semaine'),
                'emp_h_mois' => $this->input->post('emp_h_mois'),
                'emp_cout_heure' => $this->input->post('emp_cout_heure'),
                'emp_cv_vehicule' => $this->input->post('emp_cv_vehicule'),
                'emp_immatriculation' => $this->input->post('emp_immatriculation'),
                'emp_ptc' => $this->input->post('emp_ptc')
            );
            $resultat = $this->m_employes->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "L'employé(e) a été modifié(e)";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('employes/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_employes->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('emp_civilite');
            if (isset($valeur)) {
                $valeurs->emp_civilite = $valeur;
            }
            $valeur = $this->input->post('emp_nom');
            if (isset($valeur)) {
                $valeurs->emp_nom = $valeur;
            }
            $valeur = $this->input->post('emp_prenom');
            if (isset($valeur)) {
                $valeurs->emp_prenom = $valeur;
            }
            $valeur = $this->input->post('emp_fonction');
            if (isset($valeur)) {
                $valeurs->emp_fonction = $valeur;
            }
            $valeur = $this->input->post('emp_commission');
            if (isset($valeur)) {
                $valeurs->emp_commission = $valeur;
            }
            $valeur = $this->input->post('emp_date_entree');
            if (isset($valeur)) {
                $valeurs->emp_date_entree = $valeur;
            }
            $valeur = $this->input->post('emp_date_sortie');
            if (isset($valeur)) {
                $valeurs->emp_date_sortie = $valeur;
            }
            $valeur = $this->input->post('emp_etat');
            if (isset($valeur)) {
                $valeurs->emp_etat = $valeur;
            }
            $valeur = $this->input->post('emp_notes');
            if (isset($valeur)) {
                $valeurs->emp_notes = $valeur;
            }
            $valeur = $this->input->post('emp_adresse');
            if (isset($valeur)) {
                $valeurs->emp_adresse = $valeur;
            }
            $valeur = $this->input->post('emp_cp');
            if (isset($valeur)) {
                $valeurs->emp_cp = $valeur;
            }
            $valeur = $this->input->post('emp_ville');
            if (isset($valeur)) {
                $valeurs->emp_ville = $valeur;
            }
            $valeur = $this->input->post('emp_telephone1');
            if (isset($valeur)) {
                $valeurs->emp_telephone1 = $valeur;
            }
            $valeur = $this->input->post('emp_telephone2');
            if (isset($valeur)) {
                $valeurs->emp_telephone2 = $valeur;
            }
            $valeur = $this->input->post('emp_email');
            if (isset($valeur)) {
                $valeurs->emp_email = $valeur;
            }
            $valeur = $this->input->post('emp_h_jour');
            if (isset($valeur)) {
                $valeurs->emp_h_jour = $valeur;
            }
            $valeur = $this->input->post('emp_h_semaine');
            if (isset($valeur)) {
                $valeurs->emp_h_semaine = $valeur;
            }
            $valeur = $this->input->post('emp_h_mois');
            if (isset($valeur)) {
                $valeurs->emp_h_mois = $valeur;
            }
            $valeur = $this->input->post('emp_cout_heure');
            if (isset($valeur)) {
                $valeurs->emp_cout_heure = $valeur;
            }
            $valeur = $this->input->post('emp_cv_vehicule');
            if (isset($valeur)) {
                $valeurs->emp_cv_vehicule = $valeur;
            }
            $valeur = $this->input->post('emp_immatriculation');
            if (isset($valeur)) {
                $valeurs->emp_immatriculation = $valeur;
            }
            $valeur = $this->input->post('emp_ptc');
            if (isset($valeur)) {
                $valeurs->emp_ptc = $valeur;
            }
            $this->db->order_by('vcv_civilite','ASC');
            $q = $this->db->get('v_civilites');
            $listes_valeurs->emp_civilite = $q->result();
            $this->db->order_by('vfo_fonction','ASC');
            $q = $this->db->get('v_fonctions');
            $listes_valeurs->emp_fonction = $q->result();
            $this->db->order_by('vee_etat','ASC');
            $q = $this->db->get('v_etats_employes');
            $listes_valeurs->emp_etat = $q->result();
            $scripts = array();
            if (!$ajax) {
                $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#emp_date_entree").kendoDatePicker({format: "dd/MM/yyyy"});
        $("#emp_date_sortie").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_employes->get_champs('write'),
                'onglets' => array(
                    array("Employé", array('emp_civilite','emp_nom','emp_prenom','emp_fonction','emp_commission','emp_date_entree','emp_date_sortie','emp_etat','emp_notes')),
                    array("Coordonnées", array('emp_adresse','emp_cp','emp_ville','emp_telephone1','emp_telephone2','emp_email')),
                    array("Autres infos", array('emp_h_jour','emp_h_semaine','emp_h_mois','emp_cout_heure','emp_cv_vehicule','emp_immatriculation','emp_ptc'))
                )
            );

            $data = array(
                'title' => "Mise à jour d'un(e) employé(e)",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour d'employé(e)",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'employes',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Suppression d'un employé
    * support AJAX
    ******************************/
    public function remove($id,$ajax=false) {
		 if ($this->input->method() != 'post') {
            die;
        }
		$redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
		$resultat = $this->m_employes->remove($id);
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
            $this->my_set_action_response($ajax, true, "L'employé(e) a été supprimé(e)", 'info',$ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);  
    }

    /******************************
     * Web service liste des employes
     ******************************/
    public function web_service($cle='') {
        if ($cle != 'kJ45DYh-59') die('');
        $resultat = $this->m_employes->web_service();
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

}

// EOF