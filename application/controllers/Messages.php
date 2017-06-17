<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_messages m_messages
*/
class Messages extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste_e" => array(
            array(
                    "Nouveau" => array('messages/nouveau','plus',true,'messages_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*messages/detail_emis','eye-open',false,'messages_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Liste_r" => array(
            array(
                    "Nouveau" => array('messages/nouveau','plus',true,'messages_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*messages/detail_recu','eye-open',false,'messages_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element_e" => array(
            array(
                    "Nouveau" => array('messages/nouveau','plus',true,'messages_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('messages/detail_emis','eye-open',true,'messages_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element_r" => array(
            array(
                    "Nouveau" => array('messages/nouveau','plus',true,'messages_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('messages/detail_recu','eye-open',true,'messages_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Utilisateur" => array(
            array(
                    "Fiche utilisateur" => array('utilisateurs/detail','user',true,'utilisateurs_details',null,array('view')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_messages');
    }

    /******************************
    * Liste des messages envoyés
    ******************************/
    public function emis($id=0,$liste=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'messages/emis',
            'detail' => array('messages/detail_emis','msg_id','msg_envoi'),
            'champs' => array(
                array('msg_id','id',"Identifiant"),
                array('msg_envoi','datetime',"Date d'envoi"),
                array('msg_lecture','datetime',"Date de lecture"),
                array('utl_login','ref',"Destinataire",'utilisateurs','msg_destinataire','utl_login'),
                array('msg_amorce','text',"Début du message"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_messages->liste_emis_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'messages',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('messages',$this->session->id);
        $data = array(
            'title' => "Liste des messages envoyés",
            'page' => "templates/datatables",
            'menu' => "Personnel|Messages émis",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste_e"],
            'controleur' => 'messages',
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
    * Liste des messages envoyés (datasource)
    ******************************/
    public function emis_json($id=0) {
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
            $resultat = $this->m_messages->liste_emis($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'msg_envoi' => 't_messages',
                'msg_lecture' => 't_messages',
                'utl_login' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_messages->liste_emis($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_messages->liste_emis($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des messages recus
    ******************************/
    public function recus($id=0,$liste=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'messages/recus',
            'detail' => array('messages/detail_recu','msg_id','msg_envoi'),
            'champs' => array(
                array('msg_id','id',"Identifiant"),
                array('msg_envoi','datetime',"Date d'envoi"),
                array('msg_lecture','datetime',"Date de lecture"),
                array('utl_login','ref',"Émetteur",'utilisateurs','msg_emetteur','utl_login'),
                array('msg_amorce','text',"Début du message"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'en_avant' => array(
                array("msg_lecture == '0000-00-00 00:00:00'",'font-weight:bold')
            ),
            'filterable_columns' => $this->m_messages->liste_recus_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'messages',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('messages',$this->session->id);
        $data = array(
            'title' => "Liste des messages recus",
            'page' => "templates/datatables",
            'menu' => "Personnel|Messages reçus",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste_r"],
            'controleur' => 'messages',
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
    * Liste des messages recus (datasource)
    ******************************/
    public function recus_json($id=0) {
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
            $resultat = $this->m_messages->liste_recus($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'msg_envoi' => 't_messages',
                'msg_lecture' => 't_messages',
                'utl_login' => 't_utilisateurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_messages->liste_recus($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_messages->liste_recus($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau message
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'msg_destinataire','label'=>"Destinataire",'rules'=>'required'),
            array('field'=>'msg_texte','label'=>"Contenu",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'msg_destinataire' => $this->input->post('msg_destinataire'),
                'msg_texte' => $this->input->post('msg_texte'),
                'msg_envoi' => date('Y-m-d H:i:s'),
            'msg_emetteur' => $this->session->id
            );
            $id = $this->m_messages->nouveau($valeurs);
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
            $valeurs->msg_destinataire = $this->input->post('msg_destinataire');
            $valeurs->msg_texte = $this->input->post('msg_texte');
            $this->db->order_by('utl_login','ASC');
            $q = $this->db->get('t_utilisateurs');
            $listes_valeurs->msg_destinataire = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'msg_destinataire' => array("Destinataire",'select',array('msg_destinataire','utl_id','utl_login'),true),
                   'msg_texte' => array("Contenu",'textarea','msg_texte',true)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau message",
                'page' => "templates/form",
                'menu' => "Personnel|Nouveau message",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Envoyer',
                'controleur' => 'messages',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Nouveau message
    * support AJAX
    ******************************/
    public function nouveau_utilisateur($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'msg_texte','label'=>"Contenu",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'msg_texte' => $this->input->post('msg_texte'),
                'msg_envoi' => date('Y-m-d H:i:s'),
            'msg_emetteur' => $this->session->id,
                'msg_destinataire' => $pere
            );
            $id = $this->m_messages->nouveau_direct($valeurs);
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
            $valeurs->msg_texte = $this->input->post('msg_texte');

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'msg_texte' => array("Contenu",'textarea','msg_texte',true)
                ),
                'onglets' => array(
                )
            );

            $barre_action = modifie_action_barre_action($this->barre_action['Utilisateur'], 'utilisateurs/detail', 'utilisateurs/detail/'.$pere);

            $data = array(
                'title' => "Nouveau message",
                'page' => "templates/form",
                'menu' => "Personnel|Nouveau message",
                'barre_action' => $barre_action,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Envoyer',
                'controleur' => 'messages',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Contenu d'un message reçu
    * support AJAX
    ******************************/
    public function detail_recu($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_messages->detail_recu($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'msg_envoi' => array("Date d'envoi",'DATETIME','dateheure','msg_envoi'),
                   'msg_lecture' => array("Date de lecture",'DATETIME','dateheure','msg_lecture'),
                   'msg_emetteur' => array("Émetteur",'REF','ref',array('utilisateurs','msg_emetteur','utl_login')),
                   'msg_texte' => array("Contenu",'VARCHAR 1000','textarea','msg_texte')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Contenu d'un message reçu",
                'page' => "templates/detail",
                'menu' => "Personnel|Message reçu",
                'barre_action' => $this->barre_action["Element_r"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'messages',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Contenu d'un message envoyé
    * support AJAX
    ******************************/
    public function detail_emis($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_messages->detail_emis($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'msg_envoi' => array("Date d'envoi",'DATETIME','dateheure','msg_envoi'),
                   'msg_lecture' => array("Date de lecture",'DATETIME','dateheure','msg_lecture'),
                   'msg_destinataire' => array("Destinataire",'REF','ref',array('utilisateurs','msg_destinataire','utl_login')),
                   'msg_texte' => array("Contenu",'VARCHAR 1000','textarea','msg_texte')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Contenu d'un message envoyé",
                'page' => "templates/detail",
                'menu' => "Personnel|Message émis",
                'barre_action' => $this->barre_action["Element_e"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'messages',
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