<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_adresses m_adresses
*/
class Adresses extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('adresses/nouveau','plus',true,'adresses_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*adresses/detail','eye-open',false,'adresses_detail',null,array('view')),
                    "Modifier" => array('adresses/modification','pencil',false,'adresses_modification',null,array('form')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('adresses/nouveau','plus',true,'adresses_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('adresses/detail','eye-open',true,'adresses_detail',null,array('view')),
                    "Modifier" => array('adresses/modification','pencil',true,'adresses_modification',null,array('form')),
                    "Supprimer" => array('adresses/suppression','trash',true,'adresses_supprimer',"Veuillez confirmer la suppression de l'adresse",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_adresses');
    }

    /******************************
    * Liste des adresses
    ******************************/
    public function adresses_secteur($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'adresses/adresses_secteur',
            'detail' => array('adresses/detail','adr_id','adr_adresse'),
            'champs' => $this->m_adresses->get_champs('read'),
            'filterable_columns' => $this->m_adresses->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'adresses',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('adresses',$this->session->id);
        $data = array(
            'title' => "Liste des adresses",
            'page' => "templates/datatables",
            'menu' => "Production|Adresses particulières",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'adresses',
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
    * Liste des adresses (datasource)
    ******************************/
    public function adresses_secteur_json($id=0) {
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
            $resultat = $this->m_adresses->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'adr_adresse' => 't_adresses',
                'sec_nom' => 't_secteurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_adresses->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_adresses->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvelle adresse particulière
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'adr_adresse','label'=>"Adresse",'rules'=>'trim|required'),
            array('field'=>'adr_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'adr_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'adr_contact','label'=>"Informations de contact",'rules'=>'trim'),
            array('field'=>'adr_info','label'=>"Informations utiles",'rules'=>'trim'),
            array('field'=>'adr_type','label'=>"Type d'adresse particulière",'rules'=>'required'),
            array('field'=>'adr_visibilite','label'=>"Visibilité",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'adr_adresse' => $this->input->post('adr_adresse'),
                'adr_cp' => $this->input->post('adr_cp'),
                'adr_ville' => $this->input->post('adr_ville'),
                'adr_contact' => $this->input->post('adr_contact'),
                'adr_info' => $this->input->post('adr_info'),
                'adr_type' => $this->input->post('adr_type'),
                'adr_visibilite' => $this->input->post('adr_visibilite'),
                'adr_secteur' => $pere
            );
            $id = $this->m_adresses->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"L'adresse particulière a été créée");
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
            $valeurs->adr_adresse = $this->input->post('adr_adresse');
            $valeurs->adr_cp = $this->input->post('adr_cp');
            $valeurs->adr_ville = $this->input->post('adr_ville');
            $valeurs->adr_contact = $this->input->post('adr_contact');
            $valeurs->adr_info = $this->input->post('adr_info');
            $valeurs->adr_type = $this->input->post('adr_type');
            $valeurs->adr_visibilite = $this->input->post('adr_visibilite');
            $this->db->order_by('vtad_type','ASC');
            $q = $this->db->get('v_types_adresses');
            $listes_valeurs->adr_type = $q->result();
            $this->db->order_by('vvi_visibilite','ASC');
            $q = $this->db->get('v_visibilites');
            $listes_valeurs->adr_visibilite = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_adresses->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle adresse particulière",
                'page' => "templates/form",
                'menu' => "Production|Nouvelle adresse particulière",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'adresses',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une adresse
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
            $valeurs = $this->m_adresses->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'adr_adresse' => array("Adresse",'VARCHAR 400','textarea','adr_adresse'),
                   'adr_cp' => array("Code postal",'VARCHAR 5','number','adr_cp'),
                   'adr_ville' => array("Ville",'VARCHAR 50','text','adr_ville'),
                   'adr_secteur' => array("Secteur",'REF','ref',array('secteurs','adr_secteur','sec_nom')),
                   'adr_contact' => array("Informations de contact",'VARCHAR 400','textarea','adr_contact'),
                   'adr_info' => array("Informations utiles",'VARCHAR 400','textarea','adr_info'),
                   'adr_type' => array("Type d'adresse particulière",'REF','text','vtad_type'),
                   'adr_visibilite' => array("Visibilité",'REF','text','vvi_visibilite')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une adresse",
                'page' => "templates/detail",
                'menu' => "Production|Adresse particulière",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'adresses',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une adresse
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'adr_adresse','label'=>"Adresse",'rules'=>'trim|required'),
            array('field'=>'adr_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'adr_ville','label'=>"Ville",'rules'=>'trim'),
            array('field'=>'adr_contact','label'=>"Informations de contact",'rules'=>'trim'),
            array('field'=>'adr_info','label'=>"Informations utiles",'rules'=>'trim'),
            array('field'=>'adr_type','label'=>"Type d'adresse particulière",'rules'=>'required'),
            array('field'=>'adr_visibilite','label'=>"Visibilité",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'adr_adresse' => $this->input->post('adr_adresse'),
                'adr_cp' => $this->input->post('adr_cp'),
                'adr_ville' => $this->input->post('adr_ville'),
                'adr_contact' => $this->input->post('adr_contact'),
                'adr_info' => $this->input->post('adr_info'),
                'adr_type' => $this->input->post('adr_type'),
                'adr_visibilite' => $this->input->post('adr_visibilite')
            );
            $resultat = $this->m_adresses->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "L'adresse particulière a été modifiée";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('adresses/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_adresses->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('adr_adresse');
            if (isset($valeur)) {
                $valeurs->adr_adresse = $valeur;
            }
            $valeur = $this->input->post('adr_cp');
            if (isset($valeur)) {
                $valeurs->adr_cp = $valeur;
            }
            $valeur = $this->input->post('adr_ville');
            if (isset($valeur)) {
                $valeurs->adr_ville = $valeur;
            }
            $valeur = $this->input->post('adr_contact');
            if (isset($valeur)) {
                $valeurs->adr_contact = $valeur;
            }
            $valeur = $this->input->post('adr_info');
            if (isset($valeur)) {
                $valeurs->adr_info = $valeur;
            }
            $valeur = $this->input->post('adr_type');
            if (isset($valeur)) {
                $valeurs->adr_type = $valeur;
            }
            $valeur = $this->input->post('adr_visibilite');
            if (isset($valeur)) {
                $valeurs->adr_visibilite = $valeur;
            }
            $this->db->order_by('vtad_type','ASC');
            $q = $this->db->get('v_types_adresses');
            $listes_valeurs->adr_type = $q->result();
            $this->db->order_by('vvi_visibilite','ASC');
            $q = $this->db->get('v_visibilites');
            $listes_valeurs->adr_visibilite = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_adresses->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une adresse",
                'page' => "templates/form",
                'menu' => "Production|Mise à jour d'adresse particulière",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'adresses',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'une adresse particulière
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_adresses->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"L'adresse particulière a été supprimée");
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