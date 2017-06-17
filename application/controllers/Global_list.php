<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Global_list extends MY_Controller {
    private $profil;
    private $barre_action = array(	
			array(
				"Ajouter un e-mailing" => array('#','plus',true,'global_list_nouveau',null,array('form')),
                "Ajouter un e-mailing child" => array('#', 'plus', true, 'global_list_nouveau_child'),
			),	
			array(
	            //"Consulter" => array('#', 'eye-open', false, 'global_list_detail'),
	            "Consulter/Modifier"  => array('#', 'pencil', true, 'global_list_modification'),
                "Archiver"           => array('global_list/archive', 'folder-close', false, 'global_list_archiver', "Veuillez confirmer la archive du global list"),
	            "Supprimer" => array('global_list/remove', 'trash', false, 'global_list_supprimer',"Veuillez confirmer la suppression du Global list"),
	        ),
			array(
				"Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
			),			
			array(
				"Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
				"Export pdf"    => array('#', 'book', true, 'export_pdf'),
				"Imprimer"		=> array('#', 'print', true, 'print_list'),
			),
        );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_global_list');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
            	//PARAMETRES
				array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('software_name', 'text', 'Software', 'software_name'),				
				//INFO FACTURATION
				array('client_name', 'text', 'Client', 'client_name'),
				array('cmd_name', 'text', 'Commande', 'cmd_name'),
				array('facture_name', 'text', 'Facture', 'facture_name'),
				array('ht', 'text', 'HT', 'ht'),				
				//MESSAGE
				array('message_id', 'text', 'Numéro Message', 'message_id'),
				array('message_name', 'text', 'Nom Message', 'message_name'),
				array('message_view', 'text', 'View Message', 'message_view'),
				array('message_lien', 'text', 'Lien pour télécharger', 'message_lien'),
				array('message_object', 'text', 'Objet du message', 'message_object'),
				array('message_type', 'text', 'Type', 'message_type'),
				//CORPS DU MESSAGE
				array('message_famille', 'text', 'Famille d\'articles', 'message_famille'),
				array('message_societe', 'text', 'Société', 'message_societe'),
				array('message_commercial', 'text', 'Commercial', 'message_commercial'),
				array('message_email', 'text', 'E-mail du corps', 'message_email'),
				array('message_telephone', 'text', 'Telephone', 'message_telephone'),
				//SEGMENT
				array('segment_numero', 'text', "Segment Numéro", 'segment_numero'),
                array('critere', 'text', "Critere", 'critere'), //
				array('date_limite_de_fin', 'date', 'Date limite de fin', 'date_limite_de_fin'),
				array('quantite_envoyer', 'text', 'Quantité à envoyer', 'quantite_envoyer'),
                array('view_detail', 'text', "View Detail", 'view_detail'),
				array('date_envoi', 'date', 'Date envoi', 'date_envoi'),
                array('segment_part', 'text', "Segment Number", 'segment_part'),
				//array('segment_nom', 'text', "Nom Segment", 'segment_nom'),
				//array('segment_first_critere', 'text', "Critere 1", 'segment_first_critere'),
				//array('segment_second_critere', 'text', "Critere 2", 'segment_second_critere'),
				//array('segment_many_criterias', 'text', "As Many Criterias As Necessary", 'segment_many_criterias'),

				//SUIVI DE L'ENVOI
				array('stats', 'text', 'Stats', 'stats'),				
				array('quantite_envoyee', 'text', 'Quantité envoyée', 'quantite_envoyee'),
				array('open', 'text', 'Open', 'open'),
				array('open_pourcentage', 'text', '%Open', 'open_pourcentage'),
				array('openemm_number_of_click', 'text', 'Click', 'openemm_number_of_click'),
				array('openemm_click_rate_pct', 'text', '%Click', 'openemm_click_rate_pct'),
				array('verification_number', 'text', 'Verif. number sent by manager', 'verification_number'),
				array('number_sent_through', 'text', 'Number sent through form', 'number_sent_through'),
				array('number_sent_mail', 'text', 'Number sent through mails', 'number_sent_mail'),
				//DELIVRABILITE SUR TEST
				array('deliv_sur_test_orange', 'text', 'ORANGE/WANADOO', 'deliv_sur_test_orange'),
				array('deliv_sur_test_free', 'text', 'FREE', 'deliv_sur_test_free'),
				array('deliv_sur_test_sfr', 'text', 'SFR', 'deliv_sur_test_sfr'),
				array('deliv_sur_test_gmail', 'text', 'GMAIL', 'deliv_sur_test_sfr'),
				array('deliv_sur_test_microsoft', 'text', 'MICROSOFT', 'deliv_sur_test_microsoft'),
				array('deliv_sur_test_yahoo', 'text', 'YAHOO', 'deliv_sur_test_yahoo'),
				array('deliv_sur_test_ovh', 'text', 'OVH', 'deliv_sur_test_ovh'),
				array('deliv_sur_test_oneandone', 'text', '1AND1', 'deliv_sur_test_oneandone'),
				//DELIVRABILITE REELLE
				array('deliv_reelle_bounce', 'text', 'Bounce', 'deliv_reelle_bounce'),
				array('deliv_reelle_bounce_percentage_pct', 'text', '%Bounce', 'deliv_reelle_bounce_percentage'),
				array('deliv_reelle_hard_bounce_rate_pct', 'text', 'Hard Bounce Rate', 'deliv_reelle_hard_bounce_rate'),
				array('deliv_reelle_soft_bounce_rate_pct', 'text', 'Soft Bounce Rate', 'deliv_reelle_soft_bounce_rate'),
				array('deliv_reelle_orange', 'text', 'ORANGE/WANADOO', 'deliv_reelle_orange'),
				array('deliv_reelle_free', 'text', 'FREE', 'deliv_reelle_free'),
				array('deliv_reelle_sfr', 'text', 'SFR', 'deliv_reelle_sfr'),
				array('deliv_reelle_gmail', 'text', 'GMAIL', 'deliv_reelle_sfr'),
				array('deliv_reelle_microsoft', 'text', 'MICROSOFT', 'deliv_reelle_microsoft'),
				array('deliv_reelle_yahoo', 'text', 'YAHOO', 'deliv_reelle_yahoo'),
				array('deliv_reelle_ovh', 'text', 'OVH', 'deliv_reelle_ovh'),
				array('deliv_reelle_oneandone', 'text', '1AND1', 'deliv_reelle_oneandone'),				
				//TECHNICAL
				array('operateur_qui_envoie', 'text', 'Opérateur qui envoie', 'operateur_qui_envoie'),
				array('number_sent', 'text', 'number sent simultaneaously', 'number_sent'),
				array('physical_server', 'text', 'Physical Server', 'physical_server'),
				array('provider', 'text', 'Provider', 'provider'),
				array('ip', 'text', 'IP', 'ip'),
				array('smtp', 'text', 'SMTP', 'smtp'),
				array('rotation', 'text', 'Rotation', 'rotation'),
				array('domain', 'text', 'Domain', 'domain'),
				array('computer', 'text', 'Computer', 'computer'),
				array('manual_sender', 'text', 'Sender E-mail', 'manual_sender'),
				array('manual_sender_domain', 'text', 'Manual sender email domain', 'manual_sender_domain'),
				array('copy_mail', 'text', 'Copy mail', 'copy_mail'),
				//MANUAL		
				array('speed_hours', 'text', 'Speed Hours', 'speed_hours'),
				array('number_hours', 'text', 'Number Hours', 'number_hours')				
            )
        );

        return $champs[$type];
    }

	/******************************
    * List of Max Bulk Data
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
            //array("Nouvelle livraison","max_bulk/nouveau",'default')
        );
				
        // toolbar
        $toolbar = '';		
		
		// descripteur
        $descripteur = array(
            'datasource' => 'global_list/index',
            'detail' => array('global_list/detail','global_list_id','description'),
			'archive' => array('global_list/archive','global_list_id','archive'),
            'champs' => $this->get_champs('list'),
            'filterable_columns' => $this->m_global_list->liste_filterable_columns()
        );		
        
		//determine json script that will be loaded 
		//for eg: global_list/archived_json in kendo_grid-js
		switch ($mode) {
			case 'archiver':
				$descripteur['datasource'] = 'global_list/archived';
				break;
			case 'supprimees':
				$descripteur['datasource'] = 'global_list/deleted';
				break;	
			case 'all':
				$descripteur['datasource'] = 'global_list/all';
				break;
		}		
		
        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'global_list',
                'methode' => 'index',
				'mass_action_toolbar' => true,
				'view_toolbar' => true,
                'external_toolbar' => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'global_list',
                )
            ),true);
		$scripts[] = $this->load->view("global_list/column-js",array(),true);	
        $scripts[] = $this->load->view("global_list/liste-js",array(),true);
        $scripts[] = $this->load->view("global_list/form-js",array(),true);
			
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('global_list',$this->session->id);
        $data = array(
            'title' => "Liste Globale Des Envois",
            'page' => "templates/datatables",
            'menu' => "Extra|Global List",
            'scripts' => $scripts,
			'barre_action' => $this->barre_action,	//enable sage bar action
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

        if($id != 0) {
            $ids = explode("_", $id);
            $id = $ids[0];
            $software = $ids[1];
        } else {
            $software = null;
        }

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_global_list->liste($id, $software, $pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
			$tables = array(
               
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_global_list->liste($id, $software, $pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_global_list->liste($id, $software, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Global_lists'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }	

    public function index_child_json($id=0) {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        $parent_id  = $this->input->post('parentId');

        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        
        $ids = explode("_", $id);
        $id = $ids[0];
        $software = $ids[1];
        

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        // echo $id; 
        // echo $parent_id; 
        // echo $software;
        // die();

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_global_list->liste_child($id, $software, $parent_id, $pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
               
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_global_list->liste_child($id, $software, $parent_id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_global_list->liste_child($id, $software, $parent_id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Global_lists_child'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }
	
	public function deleted_json(){
		$this->index_json('deleted');
	}

	public function archived_json(){
		$this->index_json('archived');
	}

    /******************************
     * Archiver Data
     ******************************/
    public function archive($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_global_list->archive($id);

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
            $this->my_set_action_response($ajax, true, "Global list a été archiver", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);  
    }

	/******************************
     * Delete Data
     ******************************/
    public function remove($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_global_list->remove($id);

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
            $this->my_set_action_response($ajax, true, "Global list a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);  
    }

    /******************************
     * Archiver Child Data
     ******************************/
    public function archive_child($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_global_list->archive_child($id);

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
                    'isChild'    => true
                ),
            );
            $this->my_set_action_response($ajax, true, "Global list child a été archiver", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);  
    }

    /******************************
     * Delete Child Data
     ******************************/
    public function remove_child($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_global_list->remove_child($id);

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
                    'isChild'    => true
                ),
            );
            $this->my_set_action_response($ajax, true, "Global list child a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);  
    }

    public function get_software_id($type = "parent", $id)
    {
        if (!$this->input->is_ajax_request()) {
            die;
        }

        $software_id = $this->m_global_list->get_software_id($type, $id);

        echo json_encode(array('id' => $software_id));
    }
}	
?>