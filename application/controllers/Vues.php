<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Vues extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Consulter" => array('*vues/detail','eye-open',false,'vues_detail',null,array('view')),
                    "Modifier" => array('vues/modification','pencil',false,'vues_modification',null,array('form')),
                    "Supprimer" => array('vues/suppression','trash',false,'vues_supprimer',"Veuillez confirmer la suppression de la vue",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('vues/detail','eye-open',true,'vues_detail',null,array('view')),
                    "Modifier" => array('vues/modification','pencil',true,'vues_modification',null,array('form')),
                    "Supprimer" => array('vues/suppression','trash',true,'vues_supprimer',"Veuillez confirmer la suppression de la vue",array('confirm-modify')),
            ),
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_vues');
    }

    /******************************
    * Liste des vues de l'utilisateur
    ******************************/
    public function vues($id=0,$liste=0) {
        $id = $this->session->id;

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'vues/vues',
            'detail' => array('vues/detail','vue_id','vue_nom'),
            'champs' => $this->m_vues->get_champs('read'),
            'filterable_columns' => $this->m_vues->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'vues',
                'methode' => 'vues'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('vues',$this->session->id);
        $data = array(
            'title' => "Liste des vues de l'utilisateur",
            'page' => "templates/datatables",
            'menu' => "Personnel|Vues",
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
    * Liste des vues de l'utilisateur (datasource)
    ******************************/
    public function vues_json($id=0) {
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
            $resultat = $this->m_vues->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'vue_nom' => 't_vues'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_vues->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_vues->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Détail d'une vue
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_vues->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'vue_nom' => array("Nom",'VARCHAR 50','text','vue_nom'),
                   'vue_controleur' => array("Liste",'VARCHAR 50','text','vue_controleur')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une vue",
                'page' => "templates/detail",
                'menu' => "Personnel|Vue",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'vues',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une vue
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'vue_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'vue_nom' => $this->input->post('vue_nom')
            );
            $resultat = $this->m_vues->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "La vue a été modifiée";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('vues/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_vues->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('vue_nom');
            if (isset($valeur)) {
                $valeurs->vue_nom = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_vues->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une vue",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour de vue",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'vues',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'une vue
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_vues->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"La vue a été supprimée");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Enregistrement d'une vue (appelé en AJAX par le composant Grid)
     ******************************/
    public function nouvelle() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->nouvelle($id,$this->input->post('vue'),$this->input->post('ctrl'),$this->input->post('data'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Lecture des réglages d'une vue (appelé en AJAX par le composant Grid)
     * Reading the settings of a view (called in AJAX by the Grid component)
     ******************************/
    public function reglages() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->reglages($id,$this->input->post('id_vue'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Checks if a vew (by id_vue) is owned by current user (by session->id)
     * Returns true/false
     ******************************/
    public function is_owned() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->is_owned($id,$this->input->post('id_vue'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Sets a vue (by id_vue) as default for controller (ctrl).
     ******************************/
    public function set_default() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->set_default($id,$this->input->post('id_vue'),$this->input->post('ctrl'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Sets a vue (by id_vue) as GLOBAL default for controller (ctrl) and for all users.
     * Returns: true on success
     *          negative numeric error code on failure
     ******************************/
    public function set_global_default() {
        if (! $this->input->is_ajax_request()) die('');
        if ($this->session->userdata('utl_profil')!=1) { // 1=administrator
            $resultat = -2; 
        } else {
            $id = $this->session->id;
            $resultat = $this->m_vues->set_global_default($id,$this->input->post('id_vue'),$this->input->post('ctrl'));
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Gets the default vue for user (by id) and controller (by ctrl).
     ******************************/
    public function get_default() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->get_default($id, $this->input->post('ctrl'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Gets the default vue for user (by id) and controller (by ctrl).
     ******************************/
    public function get_default_id() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->get_default_id($id, $this->input->post('ctrl'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Gets the default vue for user (by id).
     ******************************/
    public function get_name() {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_vues->get_name($this->input->post('id_vue'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    /******************************
     * Delete (attempt) a vue (by id).
     * Returns: true on success
     *          negative numeric error code on failure
     ******************************/
    public function delete() {
        if (! $this->input->is_ajax_request()) die('');
        $id = $this->session->id;
        $resultat = $this->m_vues->delete($id, $this->input->post('id_vue')); // This may return:    true on success
                                                                              //                     -1 if user does not own view
                                                                              //                     -2 if view is selected as Default by admin
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    
}
// EOF
