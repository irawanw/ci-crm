<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_taux_tva m_taux_tva
*/
class Taux_tva extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('taux_tva/nouveau','plus',true,'taux_tva_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*taux_tva/detail','eye-open',false,'taux_tva_detail',null,array('view')),
                    "Modifier" => array('taux_tva/modification','pencil',false,'taux_tva_modification',null,array('form')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('taux_tva/detail','eye-open',true,'taux_tva_detail',null,array('view')),
                    "Modifier" => array('taux_tva/modification','pencil',false,'taux_tva_modification',null,array('form')),
                    "Supprimer" => array('taux_tva/suppression','trash',false,'taux_tva_supprimer',"Veuillez confirmer la suppression du taux de TVA",array('confirm-modify')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_taux_tva');
    }

     /******************************
     * List of taux tva Data
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
    * Liste des taux de TVA
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'taux_tva/index',
            'detail' => array('taux_tva/detail','tva_id','tva_taux'),
            'champs' => $this->m_taux_tva->get_champs('read'),
            'filterable_columns' => $this->m_taux_tva->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'taux_tva/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'taux_tva/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'taux_tva/all';
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
                'controleur' => 'taux_tva',
                'methode' => 'index'
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'taux_tva',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('taux_tva',$this->session->id);
        $data = array(
            'title' => "Liste des taux de TVA",
            'page' => "templates/datatables",
            'menu' => "Ventes|Taux de TVA",
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
    * Liste des taux de TVA (datasource)
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
            $resultat = $this->m_taux_tva->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tva_taux' => 't_taux_tva',
                'tva_date' => 't_taux_tva'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_taux_tva->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_taux_tva->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_taux_tva->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Taux_tva'
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
    * Nouveau taux de TVA
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'tva_taux','label'=>"Taux de TVA",'rules'=>'trim|numeric|required|greater_than_equal_to[0]'),
            array('field'=>'tva_date','label'=>"Date d'application",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tva_taux' => $this->input->post('tva_taux'),
                'tva_date' => formatte_date_to_bd($this->input->post('tva_date'))
            );
            $id = $this->m_taux_tva->nouveau($valeurs);
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
                $this->my_set_action_response($ajax,true,"Le taux de TVA a été créé",'info',$ajaxData);
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
            $valeurs->tva_taux = $this->input->post('tva_taux');
            $valeurs->tva_date = $this->input->post('tva_date');
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_taux_tva->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau taux de TVA",
                'page' => "templates/form",
                'menu' => "Ventes|Nouveau taux de TVA",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'taux_tva',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $tva          M_taux_tva Les infos du taux de TVA
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $tva) {
        $tva_etat = $tva->tva_etat;

        $etats = array(
            'taux_tva/modification'            => $tva_etat == 'futur',
            'taux_tva/suppression'             => $tva_etat == 'futur',
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'un taux de TVA
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_taux_tva->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tva_taux' => array("Taux de TVA",'DECIMAL 6,4','number','tva_taux'),
                   'tva_date' => array("Date d'application",'DATE','date','tva_date'),
                   'tva_etat' => array("Etat",'SQL','text','tva_etat')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Détail d'un taux de TVA",
                'page' => "templates/detail",
                'menu' => "Ventes|Taux de TVA",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'taux_tva',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un taux de TVA
    ******************************/
    public function modification($id=0,$ajax=false) {
        $data = $this->m_taux_tva->detail($id);
        if (! ($data->tva_etat == 'futur')) {
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
            array('field'=>'tva_taux','label'=>"Taux de TVA",'rules'=>'trim|numeric|required|greater_than_equal_to[0]'),
            array('field'=>'tva_date','label'=>"Date d'application",'rules'=>'trim|required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'tva_taux' => $this->input->post('tva_taux'),
                'tva_date' => formatte_date_to_bd($this->input->post('tva_date'))
            );
            $resultat = $this->m_taux_tva->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Le taux de TVA a été modifié";
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
            redirect('taux_tva/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_taux_tva->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('tva_taux');
            if (isset($valeur)) {
                $valeurs->tva_taux = $valeur;
            }
            $valeur = $this->input->post('tva_date');
            if (isset($valeur)) {
                $valeurs->tva_date = $valeur;
            }
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_taux_tva->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Mise à jour d'un taux de TVA",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour de taux de TVA",
                'scripts' => $scripts,
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'taux_tva',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un taux de TVA
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');

        $data = $this->m_taux_tva->detail($id);
        
        if($data != null) {
            if (! ($data->tva_etat == 'futur')) {
                $this->my_set_action_response($ajax,false,"Opération non autorisée");
                $redirection = '';
            }
            else {
                $resultat = $this->m_taux_tva->remove($id);
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
                    $this->my_set_action_response($ajax,true,"Le taux de TVA a été supprimé",'info',$ajaxData);
                }
                
                if (! $redirection) $redirection = '';
            }
        } else {
            $this->my_set_action_response($ajax,false);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_taux_tva->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $data = $this->m_taux_tva->detail($id);
        
            if($data != null) {
                if (! ($data->tva_etat == 'futur')) {
                    
                }
                else {
                    $resultat = $this->m_taux_tva->remove($id);     
                }
            }
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_taux_tva->unremove($id);
        }
    }

}
// EOF
