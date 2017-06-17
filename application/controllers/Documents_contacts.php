<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Documents_contacts extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('documents_contacts/nouveau','plus',true,'documents_contacts_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*documents_contacts/detail','eye-open',false,'documents_contacts_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('documents_contacts/nouveau','plus',true,'documents_contacts_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('documents_contacts/detail','eye-open',true,'documents_contacts_detail',null,array('view', 'default-view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Edition" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
        ),
        "Documents_Contact" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                "Nouveau" => array('documents_contacts/nouveau','plus',true,'documents_contacts_nouveau',null,array('form'))
            ),
            array(
                "Consulter" => array('*documents_contacts/detail','eye-open',false,'documents_contacts_detail',null,array('view'))
            ),
            array(
                "Devis" => array('devis/devis_client[]','list-alt',true,'devis'),
                "Commandes" => array('commandes/commandes_client[]','shopping-cart',true,'commandes'),
                "Factures" => array('factures/factures_client[]','folder-open',true,'factures'),
                "Avoirs" => array('avoirs/avoirs_client[]','retweet',true,'avoirs'),
                "Réglements" => array('reglements/reglements_client[]','euro',true,'reglements'),
            ),
            array(
                //"Créer document" => array('documents_contacts/nouveau','paperclip',true,'nouveau_document'),
                "Documents" => array('documents_contacts/documents_contact[]','paperclip',true,'documents'),
            ),
            array(
                "Evènements" => array('evenements/evenements_client[]','calendar',true,'evenements'),
            ),
            array(
                "Correspondants" => array('correspondants/correspondants_contact[]','user',true,'correspondants'),
                "Envoyer email" => array('evenements/email_contact','send',true,'envoi_email',null,array('form')),
            ),
            array(
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_documents_contacts');
    }

    /******************************
    * Liste des documents générés
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_contacts/index',
            'detail' => array('documents_contacts/detail','doc_id','doc_date'),
            'champs' => $this->m_documents_contacts->get_champs('read'),
            'filterable_columns' => $this->m_documents_contacts->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_contacts',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_contacts',$this->session->id);
        $data = array(
            'title' => "Liste des documents générés",
            'page' => "templates/datatables",
            'menu' => "GED|Documents contacts",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'documents_contacts',
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
    * Liste des documents générés (datasource)
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
            $resultat = $this->m_documents_contacts->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'doc_date' => 't_documents_contacts',
                'mod_nom' => 't_modeles_documents',
                'ctc_nom' => 't_contacts',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_contacts->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_contacts->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->doc_fichier = construit_lien_fichier($v->dsq_chemin,$v->doc_fichier);
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Documents générés pour le contact [CONTACT]
    ******************************/
    public function documents_contact($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'documents_contacts/documents_contact',
            'detail' => array('documents_contacts/detail','doc_id','doc_date'),
            'champs' => array(
                array('doc_id','id',"Identifiant"),
                array('doc_date','datetime',"Date"),
                array('mod_nom','ref',"Modèle",'modeles_documents','doc_modele','mod_nom'),
                array('doc_fichier','fichier',"Nom du fichier GED"),
                array('ctc_nom','ref',"Contact",'contacts','doc_contact','ctc_nom'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','doc_disque_archivage','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_documents_contacts->liste_par_contact_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'documents_contacts',
                'methode' => 'documents_contact'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('documents_contacts',$this->session->id);
        $data = array(
            'title' => "Documents générés pour le contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "GED|Documents contacts",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Documents_Contact"],
            'controleur' => 'documents_contacts',
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
    * Documents générés pour le contact [CONTACT] (datasource)
    ******************************/
    public function documents_contact_json($id=0) {
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
            $resultat = $this->m_documents_contacts->liste_par_contact($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'doc_date' => 't_documents_contacts',
                'mod_nom' => 't_modeles_documents',
                'ctc_nom' => 't_contacts',
                'dsq_nom' => 't_disques_archivage'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_documents_contacts->liste_par_contact($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_documents_contacts->liste_par_contact($id,$pagelength, $pagestart, $filters);
            }
        }
        foreach($resultat['data'] as $v) {
            $v->doc_fichier = construit_lien_fichier($v->dsq_chemin,$v->doc_fichier);
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau document
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'doc_modele','label'=>"Modèle",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'doc_modele' => $this->input->post('doc_modele'),
                'doc_contact' => $pere,
                'doc_date' => date('Y-m-d H:i:s')
            );
            $id = $this->m_documents_contacts->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
            }
            else {
                $this->my_set_action_response($ajax,true,"Le document a été généré");
                $redirection = "documents_contacts/detail/$id";
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeurs->doc_modele = $this->input->post('doc_modele');
            $this->db->where("mod_famille=2");
            $this->db->order_by('mod_nom','ASC');
            $q = $this->db->get('t_modeles_documents');
            $listes_valeurs->doc_modele = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_documents_contacts->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = modifie_action_barre_action($this->barre_action['Edition'],'contacts/detail','contacts/detail/'.$pere);

            $data = array(
                'title' => "Nouveau document",
                'page' => "templates/form",
                'menu' => "GED|Nouveau document contact",
                'values' => $valeurs,
                'barre_action' => $barre_action,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'documents_contacts',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un document généré
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
            $valeurs = $this->m_documents_contacts->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'doc_date' => array("Date",'DATETIME','date','doc_date'),
                   'doc_contact' => array("Contact",'REF','ref',array('contacts','doc_contact','ctc_nom')),
                   'doc_modele' => array("Modèle",'REF','ref',array('modeles_documents','doc_modele','mod_nom')),
                   'doc_fichier' => array("Nom du fichier GED",'FICHIER','text','doc_fichier','dsq_chemin'),
                   'doc_disque_archivage' => array("Disque d'archivage",'REF','ref',array('disques_archivage','doc_disque_archivage','dsq_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un document généré",
                'page' => "templates/detail",
                'menu' => "GED|Document contact",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'documents_contacts',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

}
// EOF
