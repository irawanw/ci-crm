<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_villes m_villes
*/
class Villes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('villes/nouveau','plus',true,'villes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*villes/detail','eye-open',false,'villes_detail',null,array('view')),
                    "Modifier" => array('villes/modification','pencil',false,'villes_modification',null,array('form')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('villes/detail','eye-open',true,'villes_detail',null,array('view','default-view')),
                    "Modifier" => array('villes/modification','pencil',true,'villes_modification',null,array('form')),
                    "Supprimer" => array('villes/suppression','trash',true,'villes_supprimer',"Veuillez confirmer la suppression de la ville",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Detail" => array(
            array(
                "Nouveau" => array('villes/nouveau','plus',true,'villes_nouveau',null,array('form'))
            ),
            array(
                "Consulter" => array('villes/detail','eye-open',true,'villes_detail',null,array('view','default-view')),
                "Modifier" => array('villes/modification','pencil',true,'villes_modification',null,array('form')),
                "Supprimer" => array('villes/suppression','trash',true,'villes_supprimer',"Veuillez confirmer la suppression de la ville",array('confirm-modify')),
            ),
            array(
                "Secteurs" => array('secteurs/secteurs_ville[]','home',true,'secteurs_ville'),
            ),
            array(
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            )
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_villes');
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
    * Liste des villes
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'villes/index',
            'detail' => array('villes/detail','vil_id','vil_nom'),
            'champs' => $this->m_villes->get_champs('read'),
            'default_order' => array('vil_nom', 'ASC'),
            'filterable_columns' => $this->m_villes->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'villes/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'villes/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'villes/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'villes',
                'methode' => __FUNCTION__,
                'mass_action_toolbar' => true,
                'view_toolbar' => true
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('villes',$this->session->id);
        $data = array(
            'title' => "Liste des villes",
            'page' => "templates/datatables",
            'menu' => "Production|Villes",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'villes',
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
    * Liste des villes (datasource)
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
            $resultat = $this->m_villes->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'vil_nom' => 't_villes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col='vil_nom';
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_villes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_villes->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
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
    * Nouvelle ville
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'vil_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'vil_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'vil_nom' => $this->input->post('vil_nom'),
                'vil_cp' => $this->input->post('vil_cp')
            );
            $id = $this->m_villes->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,true);
            }
            else {
                $this->my_set_action_response($ajax,true,"La ville a été créée");
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
            $valeurs->vil_nom = $this->input->post('vil_nom');
            $valeurs->vil_cp = $this->input->post('vil_cp');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_villes->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle ville",
                'page' => "templates/form",
                'menu' => "Production|Nouvelle ville",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'villes',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une ville
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
            $valeurs = $this->m_villes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'villes/modification','primary')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'vil_nom' => array("Nom",'VARCHAR 100','text','vil_nom'),
                   'vil_cp' => array("Code postal",'VARCHAR 5','number','vil_cp'),
                   'vil_nb_secteurs' => array("Nombre de secteurs",'SQL','number','vil_nb_secteurs')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une ville",
                'page' => "templates/detail",
                'menu' => "Production|Ville",
                'barre_action' => $this->barre_action["Detail"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'villes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une ville
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'vil_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'vil_cp','label'=>"Code postal",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'vil_nom' => $this->input->post('vil_nom'),
                'vil_cp' => $this->input->post('vil_cp')
            );
            $resultat = $this->m_villes->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "La ville a été modifiée";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('villes/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_villes->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('vil_nom');
            if (isset($valeur)) {
                $valeurs->vil_nom = $valeur;
            }
            $valeur = $this->input->post('vil_cp');
            if (isset($valeur)) {
                $valeurs->vil_cp = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_villes->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une ville",
                'page' => "templates/form",
                'menu' => "Production|Mise à jour de ville",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'villes',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_villes->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_villes->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_villes->unremove($id);
        }
    }

}

// EOF