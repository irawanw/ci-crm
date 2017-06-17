<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_correspondants m_correspondants
*/
class Correspondants extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('correspondants/nouveau','plus',true,'correspondants_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*correspondants/detail','eye-open',false,'correspondants_detail',null,array('view', 'dblclick')),
                    "Modifier" => array('correspondants/modification','pencil',false,'correspondants_modification',null,array('form')),
                    "Supprimer" => array('correspondants/suppression','trash',false,'correspondants_supprimer',"Veuillez confirmer la suppression du correspondant",array('confirm-delete')),
            ),
            array(
                "Envoi email type" => array('emails/envoi_email_type','send',true,'email_type',null,array('form')),
                "Envoi courrier type" => array('courriers/envoi_courrier_type','envelope',true,'courrier_type',null,array('form')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('correspondants/nouveau','plus',true,'correspondants_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('correspondants/detail','eye-open',true,'correspondants_detail',null,array('view','default-view')),
                    "Modifier" => array('correspondants/modification','pencil',true,'correspondants_modification',null,array('form')),
                    "Supprimer" => array('correspondants/suppression','trash',true,'correspondants_supprimer',"Veuillez confirmer la suppression du correspondant",array('confirm-delete')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Correspondants_Detail" => array(
            array(
                "Consulter" => array('correspondants/detail','eye-open',true,'correspondants_detail',null,array('view','default-view')),
                "Modifier" => array('correspondants/modification','pencil',true,'correspondants_modification',null,array('form')),
                "Supprimer" => array('correspondants/suppression','trash',true,'correspondants_supprimer',"Veuillez confirmer la suppression du correspondant",array('confirm-delete')),
            ),
            array(
                "Envoyer email" => array('evenements/email_correspondant','send',true,'envoi_email',null,array('form')),
                "CR appel" => array('evenements/appel','earphone',true,'appel',null,array('form')),
            ),
            array(
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            )
        ),
        "Correspondants_Contact" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                "Nouveau" => array('correspondants/nouveau','plus',true,'correspondants_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('*correspondants/detail','eye-open',false,'correspondants_detail',null,array('view','default-view')),
                "Modifier" => array('correspondants/modification','pencil',false,'correspondants_modification',null,array('form'))
            ),
            array(
                "Devis" => array('*devis/devis_client[]','list-alt',true,'devis'),
                "Commandes" => array('*commandes/commandes_client[]','shopping-cart',true,'commandes'),
                "Factures" => array('*factures/factures_client[]','folder-open',true,'factures'),
                "Avoirs" => array('*avoirs/avoirs_client[]','retweet',true,'avoirs'),
                "Réglements" => array('*reglements/reglements_client[]','euro',true,'reglements'),
            ),
            array(
                "Documents" => array('*documents_contacts/documents_contact[]','paperclip',true,'documents'),
            ),
            array(
                "Evènements" => array('*evenements/evenements_client[]','calendar',true,'evenements'),
            ),
            array(
                "Correspondants" => array('*correspondants/correspondants_contact[]','user',true,'correspondants'),
                "Envoi email type" => array('emails/envoi_email_type','send',true,'email_type',null,array('form')),
                "Envoi courrier type" => array('courriers/envoi_courrier_type','envelope',true,'courrier_type',null,array('form')),
            ),
            array(
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_correspondants');
    }

    /******************************
    * Liste des correspondants
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'correspondants/index',
            'detail' => array('correspondants/detail','cor_id','cor_nom'),
            'champs' => $this->m_correspondants->get_champs('read'),
            'filterable_columns' => $this->m_correspondants->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('correspondants',$this->session->id);
        $data = array(
            'title' => "Liste des correspondants",
            'page' => "templates/datatables",
            'menu' => "Contacts|Correspondants",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'correspondants',
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
    * Liste des correspondants (datasource)
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
            $resultat = $this->m_correspondants->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cor_nom' => 't_correspondants',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_correspondants->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_correspondants->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Correspondants du contact [CONTACT]
    ******************************/
    public function correspondants_contact($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'correspondants/correspondants_contact',
            'detail' => array('correspondants/detail','cor_id','cor_nom'),
            'champs' => array(
                array('ctc_nom','ref',"Contact",'contacts','cor_contact','ctc_nom'),
                array('vcp_type','ref',"Type",'v_clients_prospects'),
                array('cor_nom','text',"Nom"),
                array('cor_prenom','text',"Prénom"),
                array('cor_adresse','text',"Adresse"),
                array('cor_ville','text',"Ville"),
                array('cor_telephone1','text',"Téléphone 1"),
                array('cor_telephone2','text',"Téléphone 2"),
                array('cor_fax','text',"Fax"),
                array('cor_email','text',"Email"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_correspondants->liste_par_contact_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('correspondants',$this->session->id);
        $data = array(
            'title' => "Correspondants du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Contacts|Correspondants",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Correspondants_Contact"],
            'controleur' => 'correspondants',
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
    * Correspondants du contact [CONTACT] (datasource)
    ******************************/
    public function correspondants_contact_json($id=0) {
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
            $resultat = $this->m_correspondants->liste_par_contact($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cor_nom' => 't_correspondants',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_correspondants->liste_par_contact($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_correspondants->liste_par_contact($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau correspondant
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'cor_prenom','label'=>"Prénom",'rules'=>'trim'),
            array('field'=>'cor_nom','label'=>"Nom",'rules'=>'trim'),
            array('field'=>'cor_login','label'=>"Login",'rules'=>'trim'),
            array('field'=>'cor_description','label'=>"Description",'rules'=>'trim'),
            array('field'=>'cor_adresse','label'=>"Adresse",'rules'=>'trim'),
            array('field'=>'cor_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'cor_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'cor_complement','label'=>"Complément adresse",'rules'=>'trim'),
            array('field'=>'cor_telephone1','label'=>"Téléphone 1",'rules'=>'trim|is_natural'),
            array('field'=>'cor_telephone2','label'=>"Téléphone 2",'rules'=>'trim|is_natural'),
            array('field'=>'cor_fax','label'=>"Fax",'rules'=>'trim|is_natural'),
            array('field'=>'cor_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'cor_civilite' => $this->input->post('cor_civilite'),
                'cor_prenom' => $this->input->post('cor_prenom'),
                'cor_nom' => $this->input->post('cor_nom'),
                'cor_login' => $this->input->post('cor_login'),
                'cor_description' => $this->input->post('cor_description'),
                'cor_adresse' => $this->input->post('cor_adresse'),
                'cor_cp' => $this->input->post('cor_cp'),
                'cor_ville' => $this->input->post('cor_ville'),
                'cor_complement' => $this->input->post('cor_complement'),
                'cor_telephone1' => $this->input->post('cor_telephone1'),
                'cor_telephone2' => $this->input->post('cor_telephone2'),
                'cor_fax' => $this->input->post('cor_fax'),
                'cor_email' => $this->input->post('cor_email'),
                'cor_msg_distr' => $this->input->post('cor_msg_distr'),
                'cor_msg_cmd' => $this->input->post('cor_msg_cmd'),
                'cor_contact' => $pere
            );
            if (!isset($valeurs['cor_msg_distr'])) {
                $valeurs['cor_msg_distr'] = 0;
            }
            if (!isset($valeurs['cor_msg_cmd'])) {
                $valeurs['cor_msg_cmd'] = 0;
            }
            $id = $this->m_correspondants->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"Le correspondant a été créé");
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
            $valeur = $this->input->post('cor_civilite');
            if (!isset($valeur)) {
                $valeurs->cor_civilite = 1;
            }
            else {
                $valeurs->cor_civilite = $valeur;
            }
            $valeurs->cor_prenom = $this->input->post('cor_prenom');
            $valeurs->cor_nom = $this->input->post('cor_nom');
            $valeurs->cor_login = $this->input->post('cor_login');
            $valeurs->cor_description = $this->input->post('cor_description');
            $valeurs->cor_adresse = $this->input->post('cor_adresse');
            $valeurs->cor_cp = $this->input->post('cor_cp');
            $valeurs->cor_ville = $this->input->post('cor_ville');
            $valeurs->cor_complement = $this->input->post('cor_complement');
            $valeurs->cor_telephone1 = $this->input->post('cor_telephone1');
            $valeurs->cor_telephone2 = $this->input->post('cor_telephone2');
            $valeurs->cor_fax = $this->input->post('cor_fax');
            $valeurs->cor_email = $this->input->post('cor_email');
            $valeurs->cor_msg_distr = $this->input->post('cor_msg_distr');
            $valeurs->cor_msg_cmd = $this->input->post('cor_msg_cmd');
            $this->db->order_by('vcv_civilite','ASC');
            $q = $this->db->get('v_civilites');
            $listes_valeurs->cor_civilite = $q->result();

            // descripteur
            $champs = $this->m_correspondants->get_champs('write');
            unset($champs['cor_contact']);
            $descripteur = array(
                'champs' => $champs,
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau correspondant",
                'page' => "templates/form",
                'menu' => "Contacts|Nouveau correspondant",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un correspondant
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
            $valeurs = $this->m_correspondants->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'correspondants/modification','primary'),
            //    array("Supprimer",'correspondants/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cor_contact' => array("Contact",'REF','ref',array('contacts','cor_contact','ctc_nom')),
                   'ctc_client_prospect' => array("Type",'REF','text','vcp_type'),
                   'cor_civilite' => array("Civilite",'REF','text','vcv_civilite'),
                   'cor_nom' => array("Nom",'VARCHAR 50','text','cor_nom'),
                   'cor_prenom' => array("Prénom",'VARCHAR 50','text','cor_prenom'),
                   'cor_description' => array("Description",'VARCHAR 200','textarea','cor_description'),
                   'cor_adresse' => array("Adresse",'VARCHAR 400','textarea','cor_adresse'),
                   'cor_cp' => array("Code postal",'VARCHAR 5','number','cor_cp'),
                   'cor_ville' => array("Ville",'VARCHAR 50','text','cor_ville'),
                   'cor_complement' => array("Complément adresse",'VARCHAR 40','text','cor_complement'),
                   'cor_telephone1' => array("Téléphone 1",'TELEPHONE','number','cor_telephone1'),
                   'cor_telephone2' => array("Téléphone 2",'TELEPHONE','number','cor_telephone2'),
                   'cor_fax' => array("Fax",'VARCHAR 10','number','cor_fax'),
                   'cor_email' => array("Email",'EMAIL','email','cor_email'),
                   'cor_msg_distr' => array("Messages de distribution",'BOOL','checkbox','cor_msg_distr'),
                   'cor_msg_cmd' => array("Messages de commandes",'BOOL','checkbox','cor_msg_cmd')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un correspondant",
                'page' => "templates/detail",
                'menu' => "Contacts|Correspondant",
                'barre_action' => $this->barre_action["Correspondants_Detail"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur,
                'id_parent' => 'cor_contact'
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un correspondant
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'cor_nom','label'=>"Nom",'rules'=>'trim'),
            array('field'=>'cor_prenom','label'=>"Prénom",'rules'=>'trim'),
            array('field'=>'cor_description','label'=>"Description",'rules'=>'trim'),
            array('field'=>'cor_adresse','label'=>"Adresse",'rules'=>'trim'),
            array('field'=>'cor_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'cor_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'cor_complement','label'=>"Complément adresse",'rules'=>'trim'),
            array('field'=>'cor_telephone1','label'=>"Téléphone 1",'rules'=>'trim|is_natural'),
            array('field'=>'cor_telephone2','label'=>"Téléphone 2",'rules'=>'trim|is_natural'),
            array('field'=>'cor_fax','label'=>"Fax",'rules'=>'trim|is_natural'),
            array('field'=>'cor_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'cor_contact' => $this->input->post('cor_contact'),
                'cor_civilite' => $this->input->post('cor_civilite'),
                'cor_nom' => $this->input->post('cor_nom'),
                'cor_prenom' => $this->input->post('cor_prenom'),
                'cor_description' => $this->input->post('cor_description'),
                'cor_adresse' => $this->input->post('cor_adresse'),
                'cor_cp' => $this->input->post('cor_cp'),
                'cor_ville' => $this->input->post('cor_ville'),
                'cor_complement' => $this->input->post('cor_complement'),
                'cor_telephone1' => $this->input->post('cor_telephone1'),
                'cor_telephone2' => $this->input->post('cor_telephone2'),
                'cor_fax' => $this->input->post('cor_fax'),
                'cor_email' => $this->input->post('cor_email'),
                'cor_msg_distr' => $this->input->post('cor_msg_distr'),
                'cor_msg_cmd' => $this->input->post('cor_msg_cmd')
            );
            $resultat = $this->m_correspondants->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Le correspondant a été modifié";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('correspondants/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_correspondants->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('cor_contact');
            if (isset($valeur)) {
                $valeurs->cor_contact = $valeur;
            }
            $valeur = $this->input->post('cor_civilite');
            if (isset($valeur)) {
                $valeurs->cor_civilite = $valeur;
            }
            $valeur = $this->input->post('cor_nom');
            if (isset($valeur)) {
                $valeurs->cor_nom = $valeur;
            }
            $valeur = $this->input->post('cor_prenom');
            if (isset($valeur)) {
                $valeurs->cor_prenom = $valeur;
            }
            $valeur = $this->input->post('cor_description');
            if (isset($valeur)) {
                $valeurs->cor_description = $valeur;
            }
            $valeur = $this->input->post('cor_adresse');
            if (isset($valeur)) {
                $valeurs->cor_adresse = $valeur;
            }
            $valeur = $this->input->post('cor_cp');
            if (isset($valeur)) {
                $valeurs->cor_cp = $valeur;
            }
            $valeur = $this->input->post('cor_ville');
            if (isset($valeur)) {
                $valeurs->cor_ville = $valeur;
            }
            $valeur = $this->input->post('cor_complement');
            if (isset($valeur)) {
                $valeurs->cor_complement = $valeur;
            }
            $valeur = $this->input->post('cor_telephone1');
            if (isset($valeur)) {
                $valeurs->cor_telephone1 = $valeur;
            }
            $valeur = $this->input->post('cor_telephone2');
            if (isset($valeur)) {
                $valeurs->cor_telephone2 = $valeur;
            }
            $valeur = $this->input->post('cor_fax');
            if (isset($valeur)) {
                $valeurs->cor_fax = $valeur;
            }
            $valeur = $this->input->post('cor_email');
            if (isset($valeur)) {
                $valeurs->cor_email = $valeur;
            }
            $valeur = $this->input->post('cor_msg_distr');
            if (isset($valeur)) {
                $valeurs->cor_msg_distr = $valeur;
            }
            $valeur = $this->input->post('cor_msg_cmd');
            if (isset($valeur)) {
                $valeurs->cor_msg_cmd = $valeur;
            }
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->cor_contact = $q->result();
            $this->db->order_by('vcv_civilite','ASC');
            $q = $this->db->get('v_civilites');
            $listes_valeurs->cor_civilite = $q->result();

            // descripteur
            $champs = $this->m_correspondants->get_champs('write');
            unset($champs['cor_login']);
            $descripteur = array(
                'champs' => $champs,
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un correspondant",
                'page' => "templates/form",
                'menu' => "Contacts|Mise à jour de correspondant",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour du compte
    * support AJAX
    ******************************/
    public function mon_compte($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'cor_mot_de_passe','label'=>"Mot de passe",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );
        if ($this->input->post('cor_mot_de_passe') !== '') {
            $config[] = array('field'=>'__cor_mot_de_passe','label'=>"Mot de passe (confirmation)",'rules'=>'required|matches[cor_mot_de_passe]');
        }

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'cor_mot_de_passe' => $this->input->post('cor_mot_de_passe'),
                'cor_msg_distr' => $this->input->post('cor_msg_distr'),
                'cor_msg_cmd' => $this->input->post('cor_msg_cmd')
            );
            $resultat = $this->m_correspondants->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Les informations ont été modifiées";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('correspondants/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_correspondants->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('cor_mot_de_passe');
            if (isset($valeur)) {
                $valeurs->cor_mot_de_passe = $valeur;
            }
            $valeur = $this->input->post('cor_msg_distr');
            if (isset($valeur)) {
                $valeurs->cor_msg_distr = $valeur;
            }
            $valeur = $this->input->post('cor_msg_cmd');
            if (isset($valeur)) {
                $valeurs->cor_msg_cmd = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cor_mot_de_passe' => array("Mot de passe",'password-c','cor_mot_de_passe',false),
                   '__cor_mot_de_passe' => array("Mot de passe (confirmation)",'password-c','__cor_mot_de_passe',false),
                   'cor_msg_distr' => array("Messages de distribution",'checkbox','cor_msg_distr',false),
                   'cor_msg_cmd' => array("Messages de commandes",'checkbox','cor_msg_cmd',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour du compte",
                'page' => "templates/form",
                'menu' => "Espace client|Mon compte",
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'correspondants',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un correspondant
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_correspondants->suppression($id);
        if ($resultat === false) {
            $this->my_set_display_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"Le correspondant a été supprimé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

}

// EOF