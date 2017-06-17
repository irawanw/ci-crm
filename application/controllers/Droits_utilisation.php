<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_droits m_droits
*/
class Droits_utilisation extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('droits_utilisation/nouveau','plus',true,'droits_utilisation_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*droits_utilisation/detail','eye-open',false,'droits_utilisation_detail',null,array('view')),
                    "Modifier" => array('droits_utilisation/modification','pencil',false,'droits_utilisation_modification',null,array('form')),
                    "Supprimer" => array('droits_utilisation/suppression','trash',false,'droits_utilisation_supprimer',"Veuillez confirmer la suppression du droit d'utilisation",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('droits_utilisation/nouveau','plus',true,'droits_utilisation_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('droits_utilisation/detail','eye-open',true,'droits_utilisation_detail',null,array('view', 'default-view')),
                    "Modifier" => array('droits_utilisation/modification','pencil',true,'droits_utilisation_modification',null,array('form')),
                    "Supprimer" => array('droits_utilisation/suppression','trash',true,'droits_utilisation_supprimer',"Veuillez confirmer la suppression du droit d'utilisation",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_droits');
    }

    /******************************
    * Liste des droits d'utilisation d'un profil
    ******************************/
    public function droits_profil($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'droits_utilisation/droits_profil',
            'detail' => array('droits_utilisation/detail','dro_id','dro_type_droit'),
            'champs' => $this->m_droits->get_champs('read'),
            'filterable_columns' => $this->m_droits->liste_par_profil_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'droits_utilisation',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('droits_utilisation',$this->session->id);
        $data = array(
            'title' => "Liste des droits d'utilisation d'un profil",
            'page' => "templates/datatables",
            'menu' => "Personnel|Droits d'utilisation",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'droits_utilisation',
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
    * Liste des droits d'utilisation d'un profil (datasource)
    ******************************/
    public function droits_profil_json($id=0) {
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
            $resultat = $this->m_droits->liste_par_profil($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'dro_type_droit' => 't_droits',
                'prf_nom' => 't_profils'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_droits->liste_par_profil($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_droits->liste_par_profil($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau droit d'utilisation
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dro_type_droit','label'=>"Type de droit d'utilisation",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dro_type_droit' => $this->input->post('dro_type_droit'),
                'dro_visibilite' => $this->input->post('dro_visibilite'),
                'dro_profil' => $pere
            );
            if (!isset($valeurs['dro_visibilite'])) {
                $valeurs['dro_visibilite'] = 0;
            }
            $id = $this->m_droits->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"Le droit d'utilisation a été créé");
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
            $valeurs->dro_type_droit = $this->input->post('dro_type_droit');
            $valeurs->dro_visibilite = $this->input->post('dro_visibilite');
            $this->db->order_by('vto_type','ASC');
            $q = $this->db->get('v_types_droits');
            $listes_valeurs->dro_type_droit = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_droits->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau droit d'utilisation",
                'page' => "templates/form",
                'menu' => "Personnel|Nouveau droit d'utilisation",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'droits_utilisation',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un droit d'utilisation
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
            $valeurs = $this->m_droits->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'dro_type_droit' => array("Type de droit d'utilisation",'REF','text','vto_type'),
                   'dro_visibilite' => array("Visibilite totale",'BOOL','checkbox','dro_visibilite'),
                   'dro_profil' => array("Profil",'REF','ref',array('profils','dro_profil','prf_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un droit d'utilisation",
                'page' => "templates/detail",
                'menu' => "Personnel|Droit d'utilisation",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'droits_utilisation',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un droit d'utilisation
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'dro_type_droit','label'=>"Type de droit d'utilisation",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'dro_type_droit' => $this->input->post('dro_type_droit'),
                'dro_visibilite' => $this->input->post('dro_visibilite')
            );
            $resultat = $this->m_droits->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "Le droit d'utilisation a été modifié";
                }
                $this->my_set_action_response($ajax,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('droits_utilisation/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_droits->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('dro_type_droit');
            if (isset($valeur)) {
                $valeurs->dro_type_droit = $valeur;
            }
            $valeur = $this->input->post('dro_visibilite');
            if (isset($valeur)) {
                $valeurs->dro_visibilite = $valeur;
            }
            $this->db->order_by('vto_type','ASC');
            $q = $this->db->get('v_types_droits');
            $listes_valeurs->dro_type_droit = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_droits->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un droit d'utilisation",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour de droit d'utilisation",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'droits_utilisation',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un droit d'utilisation
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_droits->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"Le droit d'utilisation a été supprimé");
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