<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_catalogues m_catalogues
*/
class Catalogues extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('catalogues/nouveau','plus',true,'catalogues_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*catalogues/detail','eye-open',false,'catalogues_detail',null,array('view')),
                    "Modifier" => array('catalogues/modification','pencil',false,'catalogues_modification',null,array('form')),
                    "Supprimer" => array('catalogues/suppression','trash',false,'catalogues_supprimer',"Veuillez confirmer la suppression du catalogue",array('confirm-delete' => array('catalogues/index'))),
            ),
            array(
                    "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('catalogues/nouveau','plus',true,'catalogues_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('catalogues/detail','eye-open',true,'catalogues_detail',null,array('view', 'default-view')),
                    "Modifier" => array('catalogues/modification','pencil',false,'catalogues_modification',null,array('form')),
                    "Supprimer" => array('catalogues/suppression','trash',false,'catalogues_supprimer',"Veuillez confirmer la suppression du catalogue",array('confirm-delete' => array('catalogues/index'))),
            ),
            array(
                    "Articles" => array('*articles/articles_cat[]','tags',true, 'articles_cat'),
                    "Import articles" => array('*catalogues/importation','tags',false, 'catalogue_import'),
                    "Export articles" => array('*catalogues/exportation','tags', true, 'catalogue_export'),
            ),
            array(
                    "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_catalogues');
    }    

     /******************************
     * List of catalogues Data
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
    * Liste des catalogues
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'catalogues/index',
            'detail' => array('catalogues/detail','cat_id','cat_version'),
            'champs' => $this->m_catalogues->get_champs('read'),
            'filterable_columns' => $this->m_catalogues->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'catalogues/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'catalogues/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'catalogues/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'catalogues',
                'methode' => __FUNCTION__,
                'mass_action_toolbar' => true,
                'view_toolbar' => true
            ),true);
        $scripts[] = $this->load->view("catalogues/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('catalogues',$this->session->id);
        $data = array(
            'title' => "Liste des catalogues",
            'page' => "templates/datatables",
            'menu' => "Produits|Catalogues",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'catalogues',
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
    * Liste des catalogues (datasource)
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
            $resultat = $this->m_catalogues->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cat_version' => 't_catalogues',
                'cat_date' => 't_catalogues'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_catalogues->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_catalogues->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_catalogues->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Catalogues'
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
    * Nouveau catalogue
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'cat_version','label'=>"Version",'rules'=>'trim|required'),
            array('field'=>'cat_date','label'=>"Date de mise en service",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'cat_famille' => $this->input->post('cat_famille'),
                'cat_version' => $this->input->post('cat_version'),
                'cat_date' => formatte_date_to_bd($this->input->post('cat_date'))
            );
            $id = $this->m_catalogues->nouveau($valeurs);

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
                $this->my_set_action_response($ajax,true,"Le catalogue a été créé", 'info',$ajaxData);
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
            $valeurs->cat_famille = $this->input->post('cat_famille');
            $valeurs->cat_version = $this->input->post('cat_version');
            $valeurs->cat_date = $this->input->post('cat_date');
            $this->db->order_by('vfm_famille','ASC');
            $q = $this->db->get('v_familles');
            $listes_valeurs->cat_famille = $q->result();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_catalogues->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau catalogue",
                'page' => "templates/form",
                'menu' => "Produits|Ajouter catalogue",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'catalogues',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array         Barre d'action à modifier
     * @param $catalogue    M_catalogues  Les infos du catalogue
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $catalogue) {
        $cat_etat = $catalogue->cat_etat;
        $etats = array(
            'catalogues/modification'          => $cat_etat == 'futur',
            'catalogues/suppression'           => $cat_etat == 'futur',
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'un catalogue
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
            $valeurs = $this->m_catalogues->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action['Element'],$valeurs);

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'catalogues/modification','primary',($valeurs->cat_etat == 'futur')),
            //    array("Supprimer",'catalogues/suppression','danger',($valeurs->cat_etat == 'futur'))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cat_famille' => array("Famille",'REF','text','vfm_famille'),
                   'cat_version' => array("Version",'VARCHAR 30','text','cat_version'),
                   'cat_date' => array("Date de mise en service",'DATE','date','cat_date'),
                   'cat_etat' => array("Etat",'SQL','text','cat_etat')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un catalogue",
                'page' => "templates/detail",
                'menu' => "Produits|Catalogue",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'catalogues',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un catalogue
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $data = $this->m_catalogues->detail($id);
        if (! ($data->cat_etat == 'futur')) {
            if($ajax) {
                $this->my_set_action_response($ajax,false,"Opération non autorisée");
                return;
            }

            $this->session->set_flashdata("danger","Opération non autorisée");
            redirect();

        }
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'cat_version','label'=>"Version",'rules'=>'trim|required'),
            array('field'=>'cat_date','label'=>"Date de mise en service",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'cat_version' => $this->input->post('cat_version'),
                'cat_date' => formatte_date_to_bd($this->input->post('cat_date'))
            );
            $resultat = $this->m_catalogues->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Le catalogue a été modifié";
                    $ajaxData = array(
                         'event' => array(
                             'controleur' => $this->my_controleur_from_class(__CLASS__),
                             'type' => 'recordchange',
                             'id' => $id,
                             'timeStamp' => round(microtime(true) * 1000),
                         ),
                    );
                }
                $this->my_set_action_response($ajax,true,$message, 'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }
            redirect('catalogues/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_catalogues->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('cat_version');
            if (isset($valeur)) {
                $valeurs->cat_version = $valeur;
            }
            $valeur = $this->input->post('cat_date');
            if (isset($valeur)) {
                $valeurs->cat_date = $valeur;
            }
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cat_version' => array("Version",'text','cat_version',true),
                   'cat_date' => array("Date de mise en service",'date','cat_date',true)
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action['Element'],$valeurs);

            $data = array(
                'title' => "Mise à jour d'un catalogue",
                'page' => "templates/form",
                'menu' => "Produits|Mise à jour de catalogue",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'catalogues',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un catalogue
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');

        $data = $this->m_catalogues->detail($id);

        if($data != null) {
            if (! ($data->cat_etat == 'futur')) {
                $this->my_set_action_response($ajax,false,"Opération non autorisée");
                $redirection = '';
            } else {
                $resultat = $this->m_catalogues->remove($id);
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
                    $this->my_set_action_response($ajax,true,"Le catalogue a été supprimé", 'info',$ajaxData);
                }

                if (! $redirection) $redirection = '';
            }
        }
        else {
            $resultat = $this->m_catalogues->remove($id);
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
                $this->my_set_action_response($ajax,true,"Le catalogue a été supprimé", $ajaxData);
            }

            if (! $redirection) $redirection = '';
        }
        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    /******************************
     * Exportation d'un catalogue
     ******************************/
    public function exportation($id) {
        $export = $this->m_catalogues->exportation($id);
        $data = array(
            'title' => "Exportation d'un catalogue",
            'page' => "catalogues/exportation",
            'menu' => "Produits|Exportation de catalogue",
            'values' => array (
                'export' => $export
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Importation d'un catalogue
     ******************************/
    public function importation($id) {
        $this->load->helper(array('form','ctrl'));
        $resultat = false;
        if (array_key_exists('catalogue',$_FILES)){
            $f = $_FILES['catalogue'];
            if ($f['error'] == 0) {
                $extension = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'].$extension;
                rename($f['tmp_name'],$nom_fichier);
                $resultat = $this->m_catalogues->importation($id,$nom_fichier);
                if ($resultat === false) {
                    $this->session->set_flashdata('danger','Un problème technique est survenu - veuillez reessayer ultérieurement');
                }
                elseif ($resultat === true) {
                    $this->session->set_flashdata('success','Le catalogue a été chargé');
                    redirect('catalogues/detail/'.$id);
                }
            }
            else {
                switch($f['error']) {
                    case 1:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 2:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 3:
                        $erreur = 'Le fichier '.$f['name']." n'a été que partiellement téléchargé.";
                        break;
                    case 4:
                        if (file_exists($f['tmp_name'])) {
                            unlink($f['tmp_name']);
                            $erreur = "Un fichier de même nom existait. Veuillez recommencer.";
                        }
                        else {
                            $erreur = "Vous n'avez pas désigné le fichier";
                        }
                        break;
                    case 7:
                        $erreur = 'Le fichier '.$f['name']." n'a pu être enregistré.";
                        break;
                    default:
                        $erreur = 'Erreur lors du téléchargement du fichier '.$f['name'];
                }
                $this->session->set_flashdata('danger',$erreur);
            }
        }
        $data = array(
            'title' => "Importation de catalogue",
            'page' => "catalogues/importation",
            'menu' => "Produits|Importation de catalogue",
            'values' => array (
                'resultat' => $resultat
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_catalogues->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $data = $this->m_catalogues->detail($id);

            if($data != null) {
                if (! ($data->cat_etat == 'futur')) {
                    
                } else {
                    $resultat = $this->m_catalogues->remove($id);       
                }
            }
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_catalogues->unremove($id);
        }
    }

}

// EOF