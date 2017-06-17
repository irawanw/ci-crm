<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Societes_vendeuses extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('societes_vendeuses/nouveau','plus',true,'societes_vendeuses_nouveau',null,array('form')),
            ),
            array(
                    //"Consulter" => array('*societes_vendeuses/detail','eye-open',false,'societes_vendeuses_detail',null,array('view')),
                    "Consulter<br>Modifier" => array('societes_vendeuses/modification','eye-open',false,'societes_vendeuses_modification',null,array('form', 'dblclick')),
            ),
            array(
					"Export XLS"   => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter<br>modifier" => array('societes_vendeuses/detail','eye-open',true,'societes_vendeuses_detail',null,array('view', 'default-view')),
                    //"Modifier" => array('societes_vendeuses/modification','pencil',true,'societes_vendeuses_modification',null,array('form')),
            ),
            array(
					"Export XLS"   => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Edition" => array(
            array(
                //"Consulter" => array('societes_vendeuses/detail','eye-open',true,'societes_vendeuses_detail',null,array('view', 'default-view')),
                "Consulter<br>Modifier" => array('#','pencil',true,'societes_vendeuses_modification',null,array('form')),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_societes_vendeuses');
    }

     /******************************
     * List of ville Data
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

    /******************************
    * Liste des enseignes
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'societes_vendeuses/index',
            'detail' => array('societes_vendeuses/detail','scv_id','scv_nom'),
            'champs' => $this->m_societes_vendeuses->get_champs('read'),
            'filterable_columns' => $this->m_societes_vendeuses->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'societes_vendeuses/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'societes_vendeuses/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'societes_vendeuses/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'societes_vendeuses',
                'methode' => 'index'
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'societes_vendeuses',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				    'controleur' => 'societes_vendeuses',
                    'custom_mass_action_toolbar' => array('archiver')
                ),
            ), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('societes_vendeuses',$this->session->id);
        $data = array(
            'title' => "Liste des enseignes",
            'page' => "templates/datatables",
            'menu' => "Ventes|Enseignes",
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
    * Liste des enseignes (datasource)
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_societes_vendeuses->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'scv_nom' => 't_societes_vendeuses'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_societes_vendeuses->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_societes_vendeuses->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_societes_vendeuses->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Societes_vendeuses'
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
    * Nouvelle enseigne
     * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'scv_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'scv_adresse','label'=>"Adresse complète",'rules'=>'trim'),
            array('field'=>'scv_telephone','label'=>"Téléphone",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_fax','label'=>"Fax",'rules'=>'trim|is_natural'),
            array('field'=>'scv_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'scv_capital','label'=>"Capital",'rules'=>'trim|required'),
            array('field'=>'scv_rcs','label'=>"RCS",'rules'=>'trim|required'),
            array('field'=>'scv_siret','label'=>"SIRET",'rules'=>'trim|required'),
            array('field'=>'scv_taux_annuel','label'=>"Pénalités (taux ann.)",'rules'=>'trim|decimal|required'),
            array('field'=>'scv_taux_mensuel','label'=>"Pénalités (taux mens.)",'rules'=>'trim|decimal|required'),
            array('field'=>'scv_no_devis','label'=>"Dernier n° devis",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_no_facture','label'=>"Dernier n° facture",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_no_avoir','label'=>"Dernier n° avoir",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_format_devis','label'=>"Format des n° de devis",'rules'=>'trim'),
            array('field'=>'scv_format_facture','label'=>"Format des n° de factures",'rules'=>'trim'),
            array('field'=>'scv_format_avoir','label'=>"Format des n° d'avoirs",'rules'=>'trim'),
            array('field'=>'scv_modele_devis','label'=>"Modèle devis",'rules'=>'trim|required'),
            array('field'=>'scv_modele_facture','label'=>"Modèle facture",'rules'=>'trim|required'),
            array('field'=>'scv_modele_avoir','label'=>"Modèle avoir",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'scv_nom' => $this->input->post('scv_nom'),
                'scv_adresse' => $this->input->post('scv_adresse'),
                'scv_telephone' => $this->input->post('scv_telephone'),
                'scv_fax' => $this->input->post('scv_fax'),
                'scv_email' => $this->input->post('scv_email'),
                'scv_capital' => $this->input->post('scv_capital'),
                'scv_rcs' => $this->input->post('scv_rcs'),
                'scv_siret' => $this->input->post('scv_siret'),
                'scv_taux_annuel' => $this->input->post('scv_taux_annuel'),
                'scv_taux_mensuel' => $this->input->post('scv_taux_mensuel'),
                'scv_no_devis' => $this->input->post('scv_no_devis'),
                'scv_no_facture' => $this->input->post('scv_no_facture'),
                'scv_no_avoir' => $this->input->post('scv_no_avoir'),
                'scv_format_devis' => $this->input->post('scv_format_devis'),
                'scv_format_facture' => $this->input->post('scv_format_facture'),
                'scv_format_avoir' => $this->input->post('scv_format_avoir'),
                'scv_modele_devis' => $this->input->post('scv_modele_devis'),
                'scv_modele_facture' => $this->input->post('scv_modele_facture'),
                'scv_modele_avoir' => $this->input->post('scv_modele_avoir'),
                'scv_en_production' => $this->input->post('scv_en_production'),
                'scv_id_comptable' => $this->input->post('scv_id_comptable')
            );
            if (!isset($valeurs['scv_en_production'])) {
                $valeurs['scv_en_production'] = 0;
            }
            if (!isset($valeurs['scv_id_comptable'])) {
                $valeurs['scv_id_comptable'] = 0;
            }

            $id = $this->m_societes_vendeuses->nouveau($valeurs);
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
                $this->my_set_action_response($ajax,true,"L'enseigne a été créée",'info',$ajaxData);
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
            $valeurs->scv_nom = $this->input->post('scv_nom');
            $valeurs->scv_adresse = $this->input->post('scv_adresse');
            $valeurs->scv_telephone = $this->input->post('scv_telephone');
            $valeurs->scv_fax = $this->input->post('scv_fax');
            $valeurs->scv_email = $this->input->post('scv_email');
            $valeurs->scv_capital = $this->input->post('scv_capital');
            $valeurs->scv_rcs = $this->input->post('scv_rcs');
            $valeurs->scv_siret = $this->input->post('scv_siret');
            $valeurs->scv_taux_annuel = $this->input->post('scv_taux_annuel');
            $valeurs->scv_taux_mensuel = $this->input->post('scv_taux_mensuel');
            $valeurs->scv_no_devis = $this->input->post('scv_no_devis');
            $valeurs->scv_no_facture = $this->input->post('scv_no_facture');
            $valeurs->scv_no_avoir = $this->input->post('scv_no_avoir');
            $valeurs->scv_format_devis = $this->input->post('scv_format_devis');
            $valeurs->scv_format_facture = $this->input->post('scv_format_facture');
            $valeurs->scv_format_avoir = $this->input->post('scv_format_avoir');
            $valeurs->scv_modele_devis = $this->input->post('scv_modele_devis');
            $valeurs->scv_modele_facture = $this->input->post('scv_modele_facture');
            $valeurs->scv_modele_avoir = $this->input->post('scv_modele_avoir');
            $valeurs->scv_en_production = $this->input->post('scv_en_production');
            $valeurs->scv_id_comptable = $this->input->post('scv_id_comptable');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_societes_vendeuses->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle enseigne",
                'page' => "templates/form",
                'menu' => "Ventes|Nouvelle enseigne",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'societes_vendeuses',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            if ($ajax) {
                $data['modal'] = true;
                $html = $this->load->view("layouts/ajax", $data, true);
                if ($this->input->post()) {
                    // validation en echec
                    $success = false;
                    $message = validation_errors() ;
                    log_message('debug', $message) ;
                } else {
                    // premier appel
                    $success = true;
                    $message="";
                }
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(array("success"=>$success, "notif"=>"error", "message"=>$message, "data"=>$html)));
            }
            else {
                $layout="layouts/standard";
                $this->load->view($layout,$data);
            }
        }
    }

    /******************************
    * Détail d'une enseigne
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
            $valeurs = $this->m_societes_vendeuses->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'scv_nom' => array("Nom",'VARCHAR 100','text','scv_nom'),
                   'scv_adresse' => array("Adresse complète",'VARCHAR 400','textarea','scv_adresse'),
                   'scv_telephone' => array("Téléphone",'TELEPHONE','number','scv_telephone'),
                   'scv_fax' => array("Fax",'TELEPHONE','number','scv_fax'),
                   'scv_email' => array("Email",'EMAIL','email','scv_email'),
                   'scv_capital' => array("Capital",'VARCHAR 20','text','scv_capital'),
                   'scv_rcs' => array("RCS",'VARCHAR 30','text','scv_rcs'),
                   'scv_siret' => array("SIRET",'VARCHAR 20','text','scv_siret'),
                   'scv_taux_annuel' => array("Pénalités (taux ann.)",'DECIMAL 4,2','number','scv_taux_annuel'),
                   'scv_taux_mensuel' => array("Pénalités (taux mens.)",'DECIMAL 4,2','number','scv_taux_mensuel'),
                   'scv_no_devis' => array("Dernier n° devis",'INT 9','number','scv_no_devis'),
                   'scv_no_facture' => array("Dernier n° facture",'INT 9','number','scv_no_facture'),
                   'scv_no_avoir' => array("Dernier n° avoir",'INT 9','number','scv_no_avoir'),
                   'scv_format_devis' => array("Format des n° de devis",'VARCHAR 30','text','scv_format_devis'),
                   'scv_format_facture' => array("Format des n° de factures",'VARCHAR 30','text','scv_format_facture'),
                   'scv_format_avoir' => array("Format des n° d'avoirs",'VARCHAR 30','text','scv_format_avoir'),
                   'scv_modele_devis' => array("Modèle devis",'VARCHAR 30','text','scv_modele_devis'),
                   'scv_modele_facture' => array("Modèle facture",'VARCHAR 30','text','scv_modele_facture'),
                   'scv_modele_avoir' => array("Modèle avoir",'VARCHAR 30','text','scv_modele_avoir'),
                   'scv_en_production' => array("En production",'BOOL','checkbox','scv_en_production'),
                   'scv_id_comptable' => array("Gestion des id comptables",'BOOL','checkbox','scv_id_comptable')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une enseigne",
                'page' => "templates/detail",
                'menu' => "Ventes|Enseigne",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'societes_vendeuses',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une enseigne
     * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'scv_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'scv_adresse','label'=>"Adresse complète",'rules'=>'trim'),
            array('field'=>'scv_telephone','label'=>"Téléphone",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_fax','label'=>"Fax",'rules'=>'trim|is_natural'),
            array('field'=>'scv_email','label'=>"Email",'rules'=>'trim|valid_email'),
            array('field'=>'scv_capital','label'=>"Capital",'rules'=>'trim|required'),
            array('field'=>'scv_rcs','label'=>"RCS",'rules'=>'trim|required'),
            array('field'=>'scv_siret','label'=>"SIRET",'rules'=>'trim|required'),
            array('field'=>'scv_taux_annuel','label'=>"Pénalités (taux ann.)",'rules'=>'trim|decimal|required'),
            array('field'=>'scv_taux_mensuel','label'=>"Pénalités (taux mens.)",'rules'=>'trim|decimal|required'),
            array('field'=>'scv_no_devis','label'=>"Dernier n° devis",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_no_facture','label'=>"Dernier n° facture",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_no_avoir','label'=>"Dernier n° avoir",'rules'=>'trim|is_natural|required'),
            array('field'=>'scv_format_devis','label'=>"Format des n° de devis",'rules'=>'trim'),
            array('field'=>'scv_format_facture','label'=>"Format des n° de factures",'rules'=>'trim'),
            array('field'=>'scv_format_avoir','label'=>"Format des n° d'avoirs",'rules'=>'trim'),
            array('field'=>'scv_modele_devis','label'=>"Modèle devis",'rules'=>'trim|required'),
            array('field'=>'scv_modele_facture','label'=>"Modèle facture",'rules'=>'trim|required'),
            array('field'=>'scv_modele_avoir','label'=>"Modèle avoir",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'scv_nom' => $this->input->post('scv_nom'),
                'scv_adresse' => $this->input->post('scv_adresse'),
                'scv_telephone' => $this->input->post('scv_telephone'),
                'scv_fax' => $this->input->post('scv_fax'),
                'scv_email' => $this->input->post('scv_email'),
                'scv_capital' => $this->input->post('scv_capital'),
                'scv_rcs' => $this->input->post('scv_rcs'),
                'scv_siret' => $this->input->post('scv_siret'),
                'scv_taux_annuel' => $this->input->post('scv_taux_annuel'),
                'scv_taux_mensuel' => $this->input->post('scv_taux_mensuel'),
                'scv_no_devis' => $this->input->post('scv_no_devis'),
                'scv_no_facture' => $this->input->post('scv_no_facture'),
                'scv_no_avoir' => $this->input->post('scv_no_avoir'),
                'scv_format_devis' => $this->input->post('scv_format_devis'),
                'scv_format_facture' => $this->input->post('scv_format_facture'),
                'scv_format_avoir' => $this->input->post('scv_format_avoir'),
                'scv_modele_devis' => $this->input->post('scv_modele_devis'),
                'scv_modele_facture' => $this->input->post('scv_modele_facture'),
                'scv_modele_avoir' => $this->input->post('scv_modele_avoir'),
                'scv_en_production' => $this->input->post('scv_en_production'),
                'scv_id_comptable' => $this->input->post('scv_id_comptable')
            );
            $resultat = $this->m_societes_vendeuses->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "L'enseigne a été modifiée";
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
            $redirection = 'societes_vendeuses/detail/'.$id;
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_societes_vendeuses->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('scv_nom');
            if (isset($valeur)) {
                $valeurs->scv_nom = $valeur;
            }
            $valeur = $this->input->post('scv_adresse');
            if (isset($valeur)) {
                $valeurs->scv_adresse = $valeur;
            }
            $valeur = $this->input->post('scv_telephone');
            if (isset($valeur)) {
                $valeurs->scv_telephone = $valeur;
            }
            $valeur = $this->input->post('scv_fax');
            if (isset($valeur)) {
                $valeurs->scv_fax = $valeur;
            }
            $valeur = $this->input->post('scv_email');
            if (isset($valeur)) {
                $valeurs->scv_email = $valeur;
            }
            $valeur = $this->input->post('scv_capital');
            if (isset($valeur)) {
                $valeurs->scv_capital = $valeur;
            }
            $valeur = $this->input->post('scv_rcs');
            if (isset($valeur)) {
                $valeurs->scv_rcs = $valeur;
            }
            $valeur = $this->input->post('scv_siret');
            if (isset($valeur)) {
                $valeurs->scv_siret = $valeur;
            }
            $valeur = $this->input->post('scv_taux_annuel');
            if (isset($valeur)) {
                $valeurs->scv_taux_annuel = $valeur;
            }
            $valeur = $this->input->post('scv_taux_mensuel');
            if (isset($valeur)) {
                $valeurs->scv_taux_mensuel = $valeur;
            }
            $valeur = $this->input->post('scv_no_devis');
            if (isset($valeur)) {
                $valeurs->scv_no_devis = $valeur;
            }
            $valeur = $this->input->post('scv_no_facture');
            if (isset($valeur)) {
                $valeurs->scv_no_facture = $valeur;
            }
            $valeur = $this->input->post('scv_no_avoir');
            if (isset($valeur)) {
                $valeurs->scv_no_avoir = $valeur;
            }
            $valeur = $this->input->post('scv_format_devis');
            if (isset($valeur)) {
                $valeurs->scv_format_devis = $valeur;
            }
            $valeur = $this->input->post('scv_format_facture');
            if (isset($valeur)) {
                $valeurs->scv_format_facture = $valeur;
            }
            $valeur = $this->input->post('scv_format_avoir');
            if (isset($valeur)) {
                $valeurs->scv_format_avoir = $valeur;
            }
            $valeur = $this->input->post('scv_modele_devis');
            if (isset($valeur)) {
                $valeurs->scv_modele_devis = $valeur;
            }
            $valeur = $this->input->post('scv_modele_facture');
            if (isset($valeur)) {
                $valeurs->scv_modele_facture = $valeur;
            }
            $valeur = $this->input->post('scv_modele_avoir');
            if (isset($valeur)) {
                $valeurs->scv_modele_avoir = $valeur;
            }
            $valeur = $this->input->post('scv_en_production');
            if (isset($valeur)) {
                $valeurs->scv_en_production = $valeur;
            }
            $valeur = $this->input->post('scv_id_comptable');
            if (isset($valeur)) {
                $valeurs->scv_id_comptable = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_societes_vendeuses->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une enseigne",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour d'enseigne",
                'barre_action' => $this->barre_action["Edition"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'societes_vendeuses',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            if ($ajax) {
                $data['modal'] = true;
                $html = $this->load->view("layouts/ajax", $data, true);
                if ($this->input->post()) {
                    // validation en echec
                    $success = false;
                    $message = validation_errors() ;
                    log_message('debug', $message) ;
                } else {
                    // premier appel
                    $success = true;
                    $message="";
                }
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(array("success"=>$success, "notif"=>"error", "message"=>$message, "data"=>$html)));
            }
            else {
                $layout="layouts/standard";
                $this->load->view($layout,$data);
            }
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_societes_vendeuses->archive($id);
        }
    }
}

// EOF