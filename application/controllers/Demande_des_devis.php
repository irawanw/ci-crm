<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_demande des devis m_demande des devis
 */
class Demande_des_devis extends MY_Controller {
	private $profil;
	private $barre_action = array(
			"Demande" => array(
					array(
							"Export PDF" => array('#','book',false,'export_pdf'),
							"Export xlsx"   => array('#', 'list-alt', false, 'export_xls'),
							"Impression" => array('#','print',false,'impression')
					)
			),
	);
	
	public function __construct() {
		parent::__construct();
		$this->load->model('m_demande_des_devis');
	}
	
	protected function get_champs($type)
	{
		$champs = array(
				'list' => array(
						array('checkbox', 'text', "&nbsp", 'checkbox'),
						array('ctc_date_creation','datetime',"Date de création"),
						array('ctc_nom','text',"Nom"),
						array('telephone','text',"Telephone"),
						array('devis_fait','text',"Devis Fait"),
						array('origine_name','text',"Origine"),
						array('emp_nom','text',"Commercial"),
						array('resultat','text',"Resultat"),
						array('numero_factures','text',"Numero De Factures"),
						array('scv_nom','text',"Enseigne"),
						array('comment_desc','text',"Commentaires"),
						array('RowID','text',"__DT_Row_ID")
				)
		);
		
		return $champs[$type];
	}
	
	/******************************
	 * List
	 ******************************/
	public function index($id = 0, $liste = 0)
	{
		$this->liste($id = 0, '');
	}
	
	public function all()
	{
		$this->liste($id = 0, 'all');
	}
	
	/******************************
	 * Liste
	 ******************************/
	public function liste($id=0,$mode=0) {
		
		// commandes globales
		$cmd_globales = array(
		);
		
		// toolbar
		$toolbar = '';
		
		// descripteur
		$descripteur = array(
				'datasource' => 'demande_des_devis/index',
				'detail' => array('demande_des_devis/detail','comment_id','comment_desc'),
				'champs' => $this->get_champs('list'),
				'filterable_columns' => $this->m_demande_des_devis->get_filterable_columns()
		);
		
		$barre_action = $this->barre_action["Demande"];
		$this->session->set_userdata('_url_retour',current_url());
		$scripts = array();
		$scripts[] = $this->load->view("templates/datatables-js",
				array(
						'id'=>$id,
						'descripteur'=>$descripteur,
						'toolbar'=>$toolbar,
						'controleur' => 'demande_des_devis',
						'methode' => __FUNCTION__,
						'mass_action_toolbar' => true,
						'view_toolbar' => true
				), true);
		// listes personnelles
		$scripts[] = $this->load->view('demande_des_devis/form-js', array(), true);
		$vues = $this->m_vues->vues_ctrl('demande_des_devis',$this->session->id);
		$data = array(
				'title' => "Liste Demande des Devis",
				'page' => "templates/datatables",
				'menu' => "Ventes|demande_des_devis",
				'scripts' => $scripts,
				'barre_action' => $barre_action,
				'controleur' => 'demande_des_devis',
				'methode' => __FUNCTION__,
				'animation_barre_action' => false,
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
	 * Liste(datasource)
	 ******************************/
	public function index_json($id=0) {
		if (! $this->input->is_ajax_request()) die('');
		
		//$pagelength = $this->input->post('length');
		//$pagestart  = $this->input->post('start' );
		//debug($this->input->post('filters' ),1);
		$pagelength = 100;
		$pagestart  = 0+$this->input->post('start' );
		if ( $pagestart < 2)
			$pagelength = 50;
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
				$resultat = $this->m_demande_des_devis->liste($id,$pagelength, $pagestart, $filters);
			}
			else {
				
				//list with requested ordering
				$order_col_id   = $order[0]['column'];
				$ordering       = $order[0]['dir'];
				
				// tables for LINK columns
				$tables = array(
						'comment_desc' => 't_contacts_commentaires',
						
				);
				if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
					$order_col = $columns[$order_col_id]['data'];
					if ( empty($order_col) ) $order_col=2;
					if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
					if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
					$resultat = $this->m_demande_des_devis->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
				}
				else {
					$resultat = $this->m_demande_des_devis->liste($id,$pagelength, $pagestart, $filters);
				}
			}
			if($this->input->post('export')) {
				//action export data xls
				$champs = $this->get_champs('list');
				$params = array(
						'records' => $resultat['data'],
						'columns' => $champs,
						'filename' => 'Demande Des Devis'
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
	 * Nouveau
	 ******************************/
	public function nouveau($id=0,$ajax=false)
	{
		$resultat = $this->m_domains->nouveau($valeurs);
		if ($resultat === false) {
			$this->my_set_action_response($ajax, false);
		} else {
			$ajaxData = array(
					'event' => array(
							'controleur' => $this->my_controleur_from_class(__CLASS__),
							'type' => 'recordadd',
							'id' => $resultat,
							'timeStamp' => round(microtime(true) * 1000),
					),
			);
			$this->my_set_action_response($ajax, true, "Domain a été enregistré avec succès", 'info', $ajaxData);
		}
		if ($ajax) {
			return;
		}
		
		$redirection = $this->session->userdata('_url_retour');
		if (!$redirection) {
			$redirection = '';
		}
		
		redirect($redirection);
	}
	
	/******************************
	 * Détail
	 ******************************/
	public function detail($id,$ajax=false) {
		
	}
	
	/******************************
	 * Mise àjour
	 * support AJAX
	 ******************************/
	public function modification($id=0,$ajax=false) {
		
	}
	
	/******************************
	 * Suppression
	 * support AJAX
	 ******************************/
	public function suppression($id,$ajax=false) {
		
	}
	
	public function mass_archiver()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id) {
			$resultat = $this->m_demande_des_devis->archive($id);
		}
	}
	
	public function mass_remove()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id) {
			$resultat = $this->m_demande_des_devis->remove($id);
		}
	}
	
	public function mass_unremove()
	{
		$ids = json_decode($this->input->post('ids'), true); //convert json into array
		foreach ($ids as $id) {
			$resultat = $this->m_demande_des_devis->unremove($id);
		}
	}
	
	function savedcommentaires($id=0,$ajax=false)
	{
		$comment = $this->input->post('comment');
		$data = array('comment_id' => $id,'comment_desc' => $comment);
		//debug($data,1);
		$resultat = $this->m_demande_des_devis->ajoutercomment($data);
		if ($resultat === false) {
			$this->my_set_action_response($ajax, false);
		} else {
			$ajaxData = array(
				'event' => array(
					'controleur' => $this->my_controleur_from_class(__CLASS__),
					'type' => 'recordchange',
					'id' => $id,
					'timeStamp' => round(microtime(true) * 1000),
				),
			);
			$this->my_set_action_response($ajax, true, "Commentaire a été enregistré avec succès", 'info', $ajaxData);
		}
		if ($ajax) {
			return;
		}
		
		$redirection = $this->session->userdata('_url_retour');
		if (!$redirection) {
			$redirection = '';
		}
		redirect($redirection);
	}
}

// EOF