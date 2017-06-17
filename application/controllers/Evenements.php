<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_evenements m_evenements
*/
class Evenements extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                    "Nouveau" => array('evenements/nouveau','plus',true,'boites_archive_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*evenements/detail','eye-open',false,'boites_archive_detail',null,array('view')),
                    "Modifier" => array('evenements/modification','pencil',false,'boites_archive_modification',null,array('modify')),
                    "Supprimer" => array('evenements/suppression','trash',true,'evenements_supprimer',"Veuillez confirmer la suppression de l'évènement",array('confirm-modify')),
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
                    "Envoyer email" => array('evenements/email_contact','send',true,'envoi_email',null,array('form')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('evenements/nouveau','plus',true,'evenements_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('evenements/detail','eye-open',true,'evenements_detail',null,array('view')),
                    "Modifier" => array('evenements/modification','pencil',true,'evenements_modification',null,array('modify')),
                    "Supprimer" => array('evenements/suppression','trash',true,'evenements_supprimer',"Veuillez confirmer la suppression de l'évènement",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Contact" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
        ),
        "Correspondant" => array(
            array(
                "Fiche Correspondant" => array('correspondants/detail','user',true,'contacts_detail',null,array('view')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_evenements');
    }

    /******************************
    * Evènements du contact [CONTACT]
    ******************************/
    public function evenements_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'evenements/evenements_client',
            'detail' => array('evenements/detail','evn_id','evn_objet'),
            'champs' => $this->m_evenements->get_champs('read'),
            'filterable_columns' => $this->m_evenements->liste_par_client_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'evenements',
                'methode' => __FUNCTION__
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('evenements',$this->session->id);
        $data = array(
            'title' => "Evènements du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Contacts|Evènements",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'evenements',
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
    * Evènements du contact [CONTACT] (datasource)
    ******************************/
    public function evenements_client_json($id=0) {
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
            $resultat = $this->m_evenements->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'evn_objet' => 't_evenements',
                'evn_date' => 't_evenements',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_evenements->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_evenements->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvel évènement
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'evn_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'evn_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'evn_duree','label'=>"Durée",'rules'=>'trim'),
            array('field'=>'evn_contenu','label'=>"Contenu",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'evn_date' => formatte_date_to_bd($this->input->post('evn_date')),
                'evn_nature' => $this->input->post('evn_nature'),
                'evn_objet' => $this->input->post('evn_objet'),
                'evn_duree' => $this->input->post('evn_duree'),
                'evn_contenu' => $this->input->post('evn_contenu'),
                'evn_client' => $pere
            );
            $id = $this->m_evenements->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"L'évènement a été créé");
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
            $valeurs->evn_date = $this->input->post('evn_date');
            $valeurs->evn_nature = $this->input->post('evn_nature');
            $valeurs->evn_objet = $this->input->post('evn_objet');
            $valeurs->evn_duree = $this->input->post('evn_duree');
            $valeurs->evn_contenu = $this->input->post('evn_contenu');
            $this->db->order_by('vnt_evenement','ASC');
            $q = $this->db->get('v_natures');
            $listes_valeurs->evn_nature = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#evn_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => $this->m_evenements->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvel évènement",
                'page' => "templates/form",
                'menu' => "Contacts|Nouvel évènement",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Nouvel appel
    * support AJAX
    ******************************/
    public function appel($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'evn_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'evn_contenu','label'=>"Contenu",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'evn_objet' => $this->input->post('evn_objet'),
                'evn_contenu' => $this->input->post('evn_contenu'),
                'evn_client' => $pere,
                'evn_date' => date('Y-m-d H:i:s'),
                'evn_nature' => "2"
            );
            $id = $this->m_evenements->nouvel_appel($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"L'évènement a été créé");
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
            $valeurs->evn_objet = $this->input->post('evn_objet');
            $valeurs->evn_contenu = $this->input->post('evn_contenu');

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evn_objet' => array("Objet",'text','evn_objet',true),
                   'evn_contenu' => array("Contenu",'textarea','evn_contenu',false)
                ),
                'onglets' => array(
                )
            );

            $barre_action  = modifie_action_barre_action($this->barre_action['Contact'], 'contacts/detail', 'contacts/detail/'.$pere);

            $data = array(
                'title' => "Nouvel appel",
                'page' => "templates/form",
                'menu' => "Contacts|Nouvel appel",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Nouvel email
    * support AJAX
    ******************************/
    public function email_correspondant($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'evn_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'evn_contenu','label'=>"Contenu",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'evn_objet' => $this->input->post('evn_objet'),
                'evn_contenu' => $this->input->post('evn_contenu'),
                'evn_client' => $pere,
                'evn_date' => date('Y-m-d H:i:s'),
                'evn_nature' => "4"
            );
            $id = $this->m_evenements->nouvel_email_cor($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"Le message a été envoyé");
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
            $valeurs->evn_objet = $this->input->post('evn_objet');
            $valeurs->evn_contenu = $this->input->post('evn_contenu');

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evn_objet' => array("Objet",'text','evn_objet',true),
                   'evn_contenu' => array("Contenu",'textarea','evn_contenu',false)
                ),
                'onglets' => array(
                )
            );

            $barre_action  = modifie_action_barre_action($this->barre_action['Correspondant'], 'correspondants/detail', 'correspondants/detail/'.$pere);

            $data = array(
                'title' => "Nouvel email",
                'page' => "templates/form",
                'menu' => "Contacts|Nouveau message correspondant",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Envoyer',
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Nouvel email
    * support AJAX
    ******************************/
    public function email_contact($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'evn_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'evn_contenu','label'=>"Contenu",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'evn_objet' => $this->input->post('evn_objet'),
                'evn_contenu' => $this->input->post('evn_contenu'),
                'evn_client' => $pere,
                'evn_date' => date('Y-m-d H:i:s'),
                'evn_nature' => "4"
            );
            $id = $this->m_evenements->nouvel_email_ctc($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,'success',"Le message a été envoyé");
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
            $valeurs->evn_objet = $this->input->post('evn_objet');
            $valeurs->evn_contenu = $this->input->post('evn_contenu');

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evn_objet' => array("Objet",'text','evn_objet',true),
                   'evn_contenu' => array("Contenu",'textarea','evn_contenu',false)
                ),
                'onglets' => array(
                )
            );

            $barre_action  = modifie_action_barre_action($this->barre_action['Contact'], 'contacts/detail', 'contacts/detail/'.$pere);

            $data = array(
                'title' => "Nouvel email",
                'page' => "templates/form",
                'menu' => "Contacts|Nouveau message contact",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Envoyer',
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Détail d'un évènement
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
            $valeurs = $this->m_evenements->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'evenements/modification','primary'),
            //    array("Supprimer",'evenements/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evn_client' => array("Client",'REF','ref',array('contacts','evn_client','ctc_nom')),
                   'ctc_client_prospect' => array("Type",'REF','text','vcp_type'),
                   'evn_date' => array("Date",'DATETIME','date','evn_date'),
                   'evn_nature' => array("Nature",'REF','text','vnt_evenement'),
                   'evn_objet' => array("Objet",'VARCHAR 100','text','evn_objet'),
                   'evn_duree' => array("Durée",'VARCHAR 20','text','evn_duree'),
                   'evn_contenu' => array("Contenu",'VARCHAR 2000','textarea','evn_contenu')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un évènement",
                'page' => "templates/detail",
                'menu' => "Contacts|Evènement",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            if ($ajax) {
                $data['modal'] = true;
                $html = $this->load->view("layouts/ajax", $data, true);
                $this->output->set_content_type('application/json')->set_output(json_encode(array("success"=>true, "data"=>$html)));
            } else {
                $layout="layouts/standard";
                $this->load->view($layout,$data);
            }
        }
    }

    /******************************
    * Mise à jour d'un évènement
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'evn_date','label'=>"Date",'rules'=>'trim|required'),
            array('field'=>'evn_objet','label'=>"Objet",'rules'=>'trim|required'),
            array('field'=>'evn_duree','label'=>"Durée",'rules'=>'trim'),
            array('field'=>'evn_contenu','label'=>"Contenu",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'evn_client' => $this->input->post('evn_client'),
                'evn_date' => formatte_date_to_bd($this->input->post('evn_date')),
                'evn_nature' => $this->input->post('evn_nature'),
                'evn_objet' => $this->input->post('evn_objet'),
                'evn_duree' => $this->input->post('evn_duree'),
                'evn_contenu' => $this->input->post('evn_contenu')
            );
            $resultat = $this->m_evenements->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "L'évènement a été modifié";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('evenements/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_evenements->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('evn_client');
            if (isset($valeur)) {
                $valeurs->evn_client = $valeur;
            }
            $valeur = $this->input->post('evn_date');
            if (isset($valeur)) {
                $valeurs->evn_date = $valeur;
            }
            $valeur = $this->input->post('evn_nature');
            if (isset($valeur)) {
                $valeurs->evn_nature = $valeur;
            }
            $valeur = $this->input->post('evn_objet');
            if (isset($valeur)) {
                $valeurs->evn_objet = $valeur;
            }
            $valeur = $this->input->post('evn_duree');
            if (isset($valeur)) {
                $valeurs->evn_duree = $valeur;
            }
            $valeur = $this->input->post('evn_contenu');
            if (isset($valeur)) {
                $valeurs->evn_contenu = $valeur;
            }
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->evn_client = $q->result();
            $this->db->order_by('vnt_evenement','ASC');
            $q = $this->db->get('v_natures');
            $listes_valeurs->evn_nature = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#evn_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'evn_client' => array("Client",'select',array('evn_client','ctc_id','ctc_nom'),false),
                   'evn_date' => array("Date",'date','evn_date',true),
                   'evn_nature' => array("Nature",'select',array('evn_nature','vnt_id','vnt_evenement'),false),
                   'evn_objet' => array("Objet",'text','evn_objet',true),
                   'evn_duree' => array("Durée",'text','evn_duree',false),
                   'evn_contenu' => array("Contenu",'textarea','evn_contenu',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un évènement",
                'page' => "templates/form",
                'menu' => "Contacts|Mise à jour d'évènement",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'evenements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
    * Suppression d'un évènement
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_evenements->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"L'évènement a été supprimé");
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