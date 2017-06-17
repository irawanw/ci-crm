<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_secteurs m_secteurs
*/
class Secteurs extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('secteurs/nouveau','plus',true,'secteurs_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*secteurs/detail','eye-open',false,'secteurs_detail',null,array('view')),
                    "Modifier" => array('secteurs/modification','pencil',false,'secteurs_modification',null,array('form'))
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('secteurs/nouveau','plus',true,'secteurs_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('secteurs/detail','eye-open',true,'secteurs_detail',null,array('view')),
                    "Modifier" => array('secteurs/modification','pencil',true,'secteurs_modification',null,array('form'))
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Secteurs_Ville" => array(
            array(
                    "Nouveau" => array('secteurs/nouveau','plus',true,'secteurs_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('secteurs/detail','eye-open',true,'secteurs_detail',null,array('view')),
                    "Modifier" => array('secteurs/modification','pencil',true,'secteurs_modification',null,array('form'))
            ),
            array(
                    "Adresses particulières" => array('*adresses/adresses_secteur[]','home',true,'adresses_secteur'),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_secteurs');
    }

    /******************************
    * Liste des secteurs
    ******************************/
    public function secteurs_ville($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'secteurs/secteurs_ville',
            'detail' => array('secteurs/detail','sec_id','sec_nom'),
            'champs' => $this->m_secteurs->get_champs('read'),
            'filterable_columns' => $this->m_secteurs->liste_par_ville_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'secteurs',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('secteurs',$this->session->id);
        $data = array(
            'title' => "Liste des secteurs",
            'page' => "templates/datatables",
            'menu' => "Production|Secteurs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'secteurs',
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
    * Liste des secteurs (datasource)
    ******************************/
    public function secteurs_ville_json($id=0) {
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
            $resultat = $this->m_secteurs->liste_par_ville($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'sec_nom' => 't_secteurs',
                'vil_nom' => 't_villes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_secteurs->liste_par_ville($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_secteurs->liste_par_ville($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau secteur
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'sec_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'sec_hlm','label'=>"Nombre de BAL HLM",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_hlm_stop','label'=>"Nombre de BAL HLM Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_res','label'=>"Nombre de BAL RES",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_res_stop','label'=>"Nombre de BAL RES Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_pav','label'=>"Nombre de BAL PAV",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_pav_stop','label'=>"Nombre de BAL PAV Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'sec_nom' => $this->input->post('sec_nom'),
                'sec_type' => $this->input->post('sec_type'),
                'sec_hlm' => $this->input->post('sec_hlm'),
                'sec_hlm_stop' => $this->input->post('sec_hlm_stop'),
                'sec_res' => $this->input->post('sec_res'),
                'sec_res_stop' => $this->input->post('sec_res_stop'),
                'sec_pav' => $this->input->post('sec_pav'),
                'sec_pav_stop' => $this->input->post('sec_pav_stop'),
                'sec_ville' => $pere
            );
            $id = $this->m_secteurs->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"Le secteur a été créé");
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
            $valeurs->sec_nom = $this->input->post('sec_nom');
            $valeurs->sec_type = $this->input->post('sec_type');
            $valeurs->sec_hlm = $this->input->post('sec_hlm');
            $valeurs->sec_hlm_stop = $this->input->post('sec_hlm_stop');
            $valeurs->sec_res = $this->input->post('sec_res');
            $valeurs->sec_res_stop = $this->input->post('sec_res_stop');
            $valeurs->sec_pav = $this->input->post('sec_pav');
            $valeurs->sec_pav_stop = $this->input->post('sec_pav_stop');
            $this->db->order_by('vts_type','ASC');
            $q = $this->db->get('v_types_secteurs');
            $listes_valeurs->sec_type = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_secteurs->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau secteur",
                'page' => "templates/form",
                'menu' => "Production|Nouveau secteur",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'secteurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un secteur
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
            $valeurs = $this->m_secteurs->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'secteurs/modification','primary')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'sec_nom' => array("Nom",'VARCHAR 100','text','sec_nom'),
                   'sec_ville' => array("Ville",'REF','ref',array('villes','sec_ville','vil_nom')),
                   'sec_type' => array("Type de secteur",'REF','text','vts_type'),
                   'sec_hlm' => array("Nombre de BAL HLM",'INT 4','number','sec_hlm'),
                   'sec_hlm_stop' => array("Nombre de BAL HLM Stop Pub",'INT 4','number','sec_hlm_stop'),
                   'sec_res' => array("Nombre de BAL RES",'INT 4','number','sec_res'),
                   'sec_res_stop' => array("Nombre de BAL RES Stop Pub",'INT 4','number','sec_res_stop'),
                   'sec_pav' => array("Nombre de BAL PAV",'INT 4','number','sec_pav'),
                   'sec_pav_stop' => array("Nombre de BAL PAV Stop Pub",'INT 4','number','sec_pav_stop')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un secteur",
                'page' => "templates/detail",
                'menu' => "Production|Secteur",
                'barre_action' => $this->barre_action["Secteurs_Ville"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'secteurs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un secteur
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'sec_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'sec_hlm','label'=>"Nombre de BAL HLM",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_hlm_stop','label'=>"Nombre de BAL HLM Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_res','label'=>"Nombre de BAL RES",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_res_stop','label'=>"Nombre de BAL RES Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_pav','label'=>"Nombre de BAL PAV",'rules'=>'trim|is_natural|required'),
            array('field'=>'sec_pav_stop','label'=>"Nombre de BAL PAV Stop Pub",'rules'=>'trim|is_natural|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'sec_nom' => $this->input->post('sec_nom'),
                'sec_type' => $this->input->post('sec_type'),
                'sec_hlm' => $this->input->post('sec_hlm'),
                'sec_hlm_stop' => $this->input->post('sec_hlm_stop'),
                'sec_res' => $this->input->post('sec_res'),
                'sec_res_stop' => $this->input->post('sec_res_stop'),
                'sec_pav' => $this->input->post('sec_pav'),
                'sec_pav_stop' => $this->input->post('sec_pav_stop')
            );
            $resultat = $this->m_secteurs->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_display_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Le secteur a été modifié";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('secteurs/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_secteurs->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('sec_nom');
            if (isset($valeur)) {
                $valeurs->sec_nom = $valeur;
            }
            $valeur = $this->input->post('sec_type');
            if (isset($valeur)) {
                $valeurs->sec_type = $valeur;
            }
            $valeur = $this->input->post('sec_hlm');
            if (isset($valeur)) {
                $valeurs->sec_hlm = $valeur;
            }
            $valeur = $this->input->post('sec_hlm_stop');
            if (isset($valeur)) {
                $valeurs->sec_hlm_stop = $valeur;
            }
            $valeur = $this->input->post('sec_res');
            if (isset($valeur)) {
                $valeurs->sec_res = $valeur;
            }
            $valeur = $this->input->post('sec_res_stop');
            if (isset($valeur)) {
                $valeurs->sec_res_stop = $valeur;
            }
            $valeur = $this->input->post('sec_pav');
            if (isset($valeur)) {
                $valeurs->sec_pav = $valeur;
            }
            $valeur = $this->input->post('sec_pav_stop');
            if (isset($valeur)) {
                $valeurs->sec_pav_stop = $valeur;
            }
            $this->db->order_by('vts_type','ASC');
            $q = $this->db->get('v_types_secteurs');
            $listes_valeurs->sec_type = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_secteurs->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un secteur",
                'page' => "templates/form",
                'menu' => "Production|Mise à jour de secteur",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'secteurs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

}

// EOF