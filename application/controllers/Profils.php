<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_profils m_profils
*/
class Profils extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('profils/nouveau','plus',true,'profils_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*profils/detail','eye-open',false,'profils_detail',null,array('form')),
                    "Modifier" => array('profils/modification','pencil',false,'profils_modification',null,array('form')),
                    "Supprimer" => array('profils/suppression','trash',false,'profils_supprimer',"Veuillez confirmer la suppression du profil",array('confirm-delete' => array('profils/index'))),
            ),
            array(
                    "Droits d'utilisation" => array('*droits_utilisation/droits_profil[]','ok-circle',false,'utilisation_droits_profil'),
                    "Utilisateurs" => array('*utilisateurs/utilisateurs_profil[]','user',false,'utilisateurs_profil'),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('profils/nouveau','plus',true,'profils_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('profils/detail','eye-open',true,'profils_detail',null,array('view','default-view')),
                    "Modifier" => array('profils/modification','pencil',true,'profils_modification',null,array('form')),
                    "Supprimer" => array('profils/suppression','trash',true,'profils_supprimer',"Veuillez confirmer la suppression du profil",array('confirm-delete' => array('profils/index'))),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Profil_Detail" => array(
            array(
                "Nouveau" => array('profils/nouveau','plus',true,'profils_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('profils/detail','eye-open',true,'profils_detail',null,array('view','default-view')),
                "Modifier" => array('profils/modification','pencil',true,'profils_modification',null,array('form')),
                "Supprimer" => array('profils/suppression','trash',true,'profils_supprimer',"Veuillez confirmer la suppression du profil",array('confirm-delete' => array('profils/index'))),
            ),
            array(
                "Droits d'utilisation" => array('*droits_utilisation/droits_profil[]','ok-circle',true,'utilisation_droits_profil'),
                "Utilisateurs" => array('*utilisateurs/utilisateurs_profil[]','user',true,'utilisateurs_profil'),
            ),
            array(
                "Export PDF" => array('#','book',false,'export_pdf'),
                "Impression" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_profils');
    }

    /******************************
    * Liste des profils
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'profils/index',
            'detail' => array('profils/detail','prf_id','prf_nom'),
            'champs' => $this->m_profils->get_champs('read'),
            'filterable_columns' => $this->m_profils->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'profils',
                'methode' => __FUNCTION__,
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'profils',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'profils',
                ),
            ), true);
        //$scripts[] = $this->load->view("profils/liste-js", array(), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('profils',$this->session->id);
        $data = array(
            'title' => "Liste des profils",
            'page' => "templates/datatables",
            'menu' => "Personnel|Profils",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'profils',
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
    * Liste des profils (datasource)
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
            $resultat = $this->m_profils->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'prf_nom' => 't_profils'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_profils->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_profils->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouveau profil
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'prf_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'prf_nom' => $this->input->post('prf_nom')
            );
            $id = $this->m_profils->nouveau($valeurs);
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
                $this->my_set_action_response($ajax,true,"Le profil a été créé",'info',$ajaxData);
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
            $valeurs->prf_nom = $this->input->post('prf_nom');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_profils->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau profil",
                'page' => "templates/form",
                'menu' => "Personnel|Nouveau profil",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'profils',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un profil
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
            $valeurs = $this->m_profils->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'prf_nom' => array("Nom",'VARCHAR 30','text','prf_nom')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un profil",
                'page' => "templates/detail",
                'menu' => "Personnel|Profil",
                'barre_action' => $this->barre_action["Profil_Detail"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'profils',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un profil
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'prf_nom','label'=>"Nom",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'prf_nom' => $this->input->post('prf_nom')
            );
            $resultat = $this->m_profils->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Le profil a été modifié";
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
            redirect('profils/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_profils->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('prf_nom');
            if (isset($valeur)) {
                $valeurs->prf_nom = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_profils->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un profil",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour de profil",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'profils',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un profil
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');

        $resultat = $this->m_profils->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax,true,"Le profil a été supprimé",'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
       
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

}

// EOF