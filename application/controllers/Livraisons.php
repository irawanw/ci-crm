<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
/**
* @property M_livraisons m_livraisons
*/
class Livraisons extends MY_Controller {
    private $profil;
    private $barre_action = array(
            array(
                "Nouveau" => array('livraisons/nouveau','plus',true,'livraisons_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('*livraisons/detail','eye-open',false,'livraisons_detail',null,array('view')),
                "Modifier" => array('livraisons/modification','pencil',false,'livraisons_modification',null,array('form')),
                "Archiver" => array('livraisons/archive','hdd',false,'livraisons_archiver',"Veuillez confirmer l'archivage de cette livraison", array('confirm-delete' => array('livraisons/index'))),
                "Supprimer" => array('livraisons/remove','trash',false,'livraisons_supprimer',"Veuillez confirmer la suppression du livraison", array('confirm-delete' => array('livraisons/index'))),
            ),
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
            ),
            array(
                "Export Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Export PDF"    => array('#', 'book', true, 'export_pdf'),
                "Imprimer"      => array('#', 'print', true, 'print_list'),
            ),
        );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_livraisons');
    }


    /******************************
    * List of Livraisons Data
    ******************************/
    public function index($id=0,$liste=0){
        $this->liste($id=0, '');
    }
    
    public function archiver(){
        $this->liste($id=0, 'archiver');
    }
    
    public function supprimees(){
        $this->liste($id=0, 'supprimees');
    }
    
    public function all(){
        $this->liste($id=0, 'all');
    }

    public function liste($id=0, $mode=0){
        // commandes globales
        $cmd_globales = array(
            //array("Nouvelle livraison","livraisons/nouveau",'default')
        );
                
        // toolbar
        $toolbar = '';      
        
        // descripteur
        $descripteur = array(
            'datasource' => 'livraisons/index',
            'detail' => array('livraisons/detail','livraisons_id','description'),
            'archive' => array('livraisons/archive','livraisons_id','archive'),
            'champs' => $this->m_livraisons->get_champs('read'),
            'filterable_columns' => $this->m_livraisons->liste_filterable_columns()
        );      
        
        //determine json script that will be loaded 
        //for eg: livraisons/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'livraisons/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'livraisons/deleted';
                break;  
            case 'all':
                $descripteur['datasource'] = 'livraisons/all';
                break;
        }       
        
        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'livraisons',
                'methode' => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar' => true,
            ),true);
        $scripts[] = $this->load->view("livraisons/liste-js",array(),true);
        $scripts[] = $this->load->view('livraisons/form-js', array(), true);
            
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('livraisons',$this->session->id);
        $data = array(
            'title' => "Liste suivi des livraisons",
            'page' => "templates/datatables",
            'menu' => "Extra|Livraisons",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action,  //enable sage bar action
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
    * Ajax call for Livraison List
    ******************************/
    public function index_json($id=0) {
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
            $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'livraisons_id' => 't_livraisons'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_livraisons->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Livraisons'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }
    
    public function archived_json($id=0) {
        $id = 'archived';
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
            $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'livraisons_id' => 't_livraisons'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function deleted_json($id=0) {
        $id = 'deleted';
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
            $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'livraisons_id' => 't_livraisons'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    
    }   

    public function all_json($id=0) {
        $id = 'all';
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
            $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'livraisons_id' => 't_livraisons'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_livraisons->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    
    }       
    
    public function commande_option($id=0) {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat   = $this->m_livraisons->commande_by_client($id);
        $results    = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Pas de Commande</option>";
        foreach($results as $row){
            echo "<option value='".$row['cmd_id']."'>".$row['cmd_reference']."</option>";
        }       
    }   

    /******************************
    * New Livraison
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'client','label'=>"Client",'rules'=>'trim|required'),
            array('field'=>'commande','label'=>"Commande",'rules'=>'trim|required'),
            array('field'=>'date_livraisons','label'=>"Date Livraison",'rules'=>'trim'),
            array('field'=>'palettes','label'=>"Nombre de Palettes",'rules'=>'trim'),
            array('field'=>'cartons','label'=>"Nombre de Cartons",'rules'=>'trim'),
            array('field'=>'remarques','label'=>"Remarques",'rules'=>'trim'),
            array('field'=>'qty','label'=>"Quantité par Carton",'rules'=>'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {
                
            // validation réussie
            $valeurs = array(
                'client' => $this->input->post('client'),
                'commande' => $this->input->post('commande'),
                'date_livraisons' => formatte_date_to_bd($this->input->post('date_livraisons')),
                'palettes' => $this->input->post('palettes'),
                'cartons' => $this->input->post('cartons'),
                'remarques' => $this->input->post('remarques'),
                'qty' => $this->input->post('qty'),
            );
            $id = $this->m_livraisons->nouveau($valeurs);
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
                $this->my_set_action_response($ajax,true,"Livraison a été enregistrée avec succès",'info', $ajaxData);
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
            $valeurs->client = $this->input->post('client');
            $valeurs->commande = $this->input->post('commande');
            $valeurs->date_livraisons = $this->input->post('date_livraisons');
            $valeurs->palettes = $this->input->post('palettes');
            $valeurs->cartons = $this->input->post('cartons');
            $valeurs->remarques = $this->input->post('remarques');
            $valeurs->qty = $this->input->post('qty');
            
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();
            
            //get commandes that belongs to client
            $commande   = $this->m_livraisons->commande(0);
            $new_object = new stdClass;
            $new_object->cmd_id         = "-1";
            $new_object->cmd_reference  = 'Pas de Commande';
            array_unshift($commande, $new_object);
            
            $listes_valeurs->commande = $commande;
            
            $scripts = array();
            
            // descripteur
            $descripteur = array(
                'champs' => $this->m_livraisons->get_champs('write'),
                'onglets' => array(
                    array("Livraisons", array('client','commande','date_livraisons','palettes','cartons','remarques','qty')),
                )
            );

            $data = array(
                'title' => "Ajouter un nouveau livraison",
                'page' => "templates/form",
                'menu' => "Extra|Create Livraisons",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'livraisons',
                'methode' => 'create',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }   
    
    
    /******************************
    * Detail of Livraisons Data
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_livraisons->detail($id);
            $valeurs->total = $valeurs->cartons * $valeurs->qty;
            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'livraisons/modification','primary'),
            //  array("Archiver",'livraisons/archive','warning'),
            //  array("Supprimer",'livraisons/remove','danger')
            );
        
            // descripteur
            $descripteur = array(
                'champs' => array(
                    'client' => array("Client",'VARCHAR 50','text','client_name'),
                    'commande' => array("Commande",'VARCHAR 50','text','commande_reference'),
                    'date_livraisons' => array("Date Livraison",'DATE','text','date_livraisons'),
                    'palettes' => array("Nombre de Palettes",'VARCHAR 50','text','palettes'),
                    'cartons' => array("Nombre de Cartons",'VARCHAR 50','text','cartons'),
                    'remarques' => array("Remarques",'VARCHAR 50','text','remarques'),
                    'qty' => array("Quantité par Carton",'VARCHAR 50','text','qty'),
                    'total' => array("Quantité totale",'VARCHAR 50','text','total'),
                ),
                'onglets' => array(
                    array("Livraisons", array('client','commande','date_livraisons','palettes','cartons','remarques','qty','total')),
                )
            );

            $data = array(
                'title' => "Détail of suivi des livraison",
                'page' => "templates/detail",
                'menu' => "Extra|Livraisons",
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'livraisons',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur,
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Edit function for Livraisons Data
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');
        
        // règles de validation
        $config = array(
            array('field'=>'client','label'=>"Client",'rules'=>'trim|required'),
            array('field'=>'commande','label'=>"Commande",'rules'=>'trim|required'),
            array('field'=>'date_livraisons','label'=>"Date Livraison",'rules'=>'trim'),
            array('field'=>'palettes','label'=>"Nombre de Palettes",'rules'=>'trim'),
            array('field'=>'cartons','label'=>"Nombre de Cartons",'rules'=>'trim'),
            array('field'=>'remarques','label'=>"Remarques",'rules'=>'trim'),
            array('field'=>'qty','label'=>"Quantité par Carton",'rules'=>'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'client' => $this->input->post('client'),
                'commande' => $this->input->post('commande'),
                'date_livraisons' => formatte_date_to_bd($this->input->post('date_livraisons')),
                'palettes' => $this->input->post('palettes'),
                'cartons' => $this->input->post('cartons'),
                'remarques' => $this->input->post('remarques'),
                'qty' => $this->input->post('qty'),
            );
            $resultat = $this->m_livraisons->maj($valeurs,$id);

            $redirection = 'livraisons/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Livraison a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                     );
                 }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_livraisons->detail($id);
            
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('client');
            if (isset($valeur)) {
                $valeurs->client = $valeur;
            }
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();
            
            //get commandes that belongs to client          
            $commande = $this->m_livraisons->commande($id);
            $new_object = new stdClass;
            $new_object->cmd_id         = "-1";
            $new_object->cmd_reference  = 'Pas de Commande';
            array_unshift($commande, $new_object);          
            $listes_valeurs->commande = $commande;
            
            //$listes_valeurs->commande = $q->result();
            
            $scripts = array();
                    
            // descripteur
            $descripteur = array(
                'champs' => $this->m_livraisons->get_champs('write'),
                'onglets' => array(
                    array("Livraisons", array('client','commande','date_livraisons','palettes','cartons','remarques','qty')),
                )
            );

            $data = array(
                'title' => "Modifier livraison",
                'page' => "templates/form",
                'menu' => "Extra|Edit Livraisons",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'livraisons',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Archive Purchase Data
    ******************************/
    public function archive($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_livraisons->archive($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Livraison a été archivée", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }
    
    /******************************
    * Delete Livraisons Data
    ******************************/
    public function remove($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_livraisons->remove($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Livraison a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }
    
    public function mass_archiver(){
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach($ids as $id){
            $resultat = $this->m_livraisons->archive($id);
        }
    }
    
    public function mass_remove(){
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach($ids as $id){
            $resultat = $this->m_livraisons->remove($id);
        }
    }

    public function mass_unremove(){
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach($ids as $id){
            $resultat = $this->m_livraisons->unremove($id);
        }
    }   

}
// EOF
