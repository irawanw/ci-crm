<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_demande_des_devis extends MY_Model {
	
	public function __construct() {
		parent::__construct();
		$this->filterable_columns = array(
				'ctc_date_creation'=>'datetime',
				'ctc_id' => 'int',
				'ctc_nom'=>'char',
				'telephone'=>'char',
				'devis_fait'=>'char',
				'origine_name'=>'char',
				'emp_nom'=>'char',
				'resultat'=>'char',
				'numero_factures' => 'char',
				'comment_desc'=>'char',
				'scv_nom'=>'char'
		);
	}
	
	/******************************
	 * Liste des contacts
	 ******************************/
	public function liste($id=0,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		//$table = 't_contacts';
		//$this->db->start_cache();
		/*
		 //resultat
		 $query ="(SELECT ctc_id as r_id,
		 count(fac_id) as resultat
		 FROM t_factures
		 LEFT join t_commandes on cmd_id = fac_commande
		 LEFT join t_devis on dvi_id = cmd_devis
		 LEFT join t_contacts on ctc_id = dvi_client
		 WHERE dvi_client = ctc_id
		 GROUP BY ctc_id) as result ON r_id = ctc_id)";
		 $resultat = $this->db->get_compiled_select($query,true);
		 
		 //numero_factures
		 $query ="(SELECT ctc_id as n_id,
		 GROUP_CONCAT(nullif(fac_reference,'')) as numero_factures
		 FROM t_factures
		 LEFT join t_commandes on cmd_id = fac_commande
		 LEFT join t_devis on dvi_id = cmd_devis
		 LEFT join t_contacts on ctc_id = dvi_client
		 WHERE dvi_client = ctc_id
		 GROUP BY ctc_id) as numero ON n_id = ctc_id)";
		 $numero = $this->db->get_compiled_select($query,true);
		 
		 $this->db->select("	tc.ctc_id AS RowID,
		 tc.ctc_id,
		 DATE_FORMAT(ctc_date_creation, '%m/%d/%Y %H:%i') AS ctc_date_creation,
		 tc.ctc_nom,if(tc.ctc_telephone='','NON','OUI') as telephone,
		 if((select count(*) from t_devis tde where tde.dvi_client = tc.ctc_id) > 0,'OUI','NON') as fait,
		 resultat,
		 numero_factures,
		 ctc_commercial,
		 te.emp_nom,
		 top.origine_name,
		 tsv.scv_nom,
		 '' as commentaires",
		 false);
		 $this->db->join('t_devis td','td.dvi_client = tc.ctc_id','LEFT');
		 $this->db->join('t_employes te','te.emp_id = tc.ctc_commercial','LEFT');
		 $this->db->join('t_societes_vendeuses tsv','td.dvi_societe_vendeuse = tsv.scv_id','LEFT');
		 $this->db->join('v_types_origine_prospect top','tc.ctc_origine = top.origine_name','LEFT');
		 $this->db->join($resultat,'LEFT');
		 $this->db->join($numero,'LEFT');
		 $this->db->group_by('td.dvi_client');
		 $this->db->where('tc.ctc_inactif is null');
		 
		 switch($void){
		 case 'archived':
		 $this->db->where('tc.ctc_archiver is NOT NULL');
		 break;
		 case 'deleted':
		 $this->db->where('tc.ctc_inactif is NOT NULL');
		 break;
		 case 'all':
		 break;
		 default:
		 $this->db->where('tc.ctc_archiver is NULL');
		 $this->db->where('tc.ctc_inactif is NULL');
		 break;
		 }
		 
		 $id = intval($void);
		 if ($id > 0) {
		 $this->db->where('tc.ctc_id', $id);
		 }
		 */
		//$this->db->stop_cache();
		//$comment_desc = formatte_sql_xedit('#','comment_id','comment_desc');
		$table = "( SELECT
		ctc_id AS RowID,ctc_id,ctc_date_creation,
		ctc_nom,if(ctc_telephone='','NON','OUI') as telephone,
		if(devis_fait > 0,'OUI','NON') as devis_fait,
		origine_name,
		emp_nom,
		if(resultat > 0,'SIGNE','NON QUALIFIE') as resultat,
		numero_factures,
		scv_nom,
		comment_desc
		FROM t_contacts
		LEFT JOIN t_devis on dvi_client = ctc_id
		LEFT JOIN t_employes on emp_id = ctc_commercial
		LEFT JOIN t_societes_vendeuses on dvi_societe_vendeuse = scv_id
		LEFT JOIN v_types_origine_prospect on ctc_origine = origine_name
		LEFT JOIN t_contacts_commentaires on ctc_id = comment_id
		LEFT JOIN (
		select tco.ctc_id as f_id, count(tde.dvi_client) as devis_fait
		from t_contacts tco
		left join t_devis tde on tde.dvi_client = tco.ctc_id
		where tco.ctc_id = tde.dvi_client
		group by tco.ctc_id
		) as fait on f_id = ctc_id
		LEFT JOIN (
		SELECT tco.ctc_id as r_id, count(tfa.fac_id) as resultat
		from t_factures tfa
		left join t_commandes tcom on tcom.cmd_id = tfa.fac_commande
		left join t_devis tde on tde.dvi_id = tcom.cmd_devis
		left join t_contacts tco on ctc_id = tde.dvi_client
		where tco.ctc_id  = tde.dvi_client
		group by tco.ctc_id
		) as fac_result on r_id = ctc_id
		LEFT JOIN (
		select tco.ctc_id as n_id, GROUP_CONCAT(nullif(tfa.fac_reference,'')) as numero_factures
		from t_factures tfa
		left join t_commandes tcom on tcom.cmd_id = tfa.fac_commande
		left join t_devis tde on tde.dvi_id = tcom.cmd_devis
		left join t_contacts tco on tco.ctc_id = tde.dvi_client
		where tde.dvi_client = tco.ctc_id
		group by tco.ctc_id
		) as num_facture on n_id = ctc_id
		WHERE ctc_inactif is null";
		if ($id) {
			$table .= " AND ctc_id = ".intval($id);
		}
		$table .= " GROUP BY dvi_client";
		$table .= " ORDER BY ctc_date_creation desc)";
		
		
		$aliases = array();
		
		//$resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
		$resultat = $this->_filtre($table,$this->get_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering, true);
		$this->db->flush_cache();
		
		 //add checkbox into data
		 for($i=0; $i<count($resultat['data']); $i++){
			 $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
			 $comment_desc = $resultat['data'][$i]->comment_desc == "" ? '<a href="#" class="update-contact-comment" data-id="'.$resultat['data'][$i]->RowID.'" data-desc="">ajouter</a>' : '<a href="#" class="update-contact-comment" data-id="'.$resultat['data'][$i]->RowID.'" data-desc="'.$resultat['data'][$i]->comment_desc.'">'.$resultat['data'][$i]->comment_desc.'</a>';
				$resultat['data'][$i]->comment_desc = $comment_desc;
		 }
		 
		return $resultat;
	}
	
	/******************************
	 * Return filterable columns
	 ******************************/
	public function liste_filterable_columns() {
		$filterable_columns = array(
				'ctc_date_creation'=>'datetime',
				'ctc_nom'=>'char',
				'telephone'=>'char',
				'devis_fait'=>'char',
				'origine_name'=>'char',
				'emp_nom'=>'char',
				'resultat'=>'char',
				'numero_factures' => 'char',
				'commentaires'=>'char',
				'scv_nom'=>'char'
		);
		return $filterable_columns;
	}
	
	public function get_filterable_columns() {
		return $this->filterable_columns;
	}
	
	
	/******************************
	 * Nouveau
	 ******************************/
	public function nouveau($data) {
		
	}
	
	/******************************
	 * Détail
	 ******************************/
	public function detail($id) {
		
	}
	
	public function maj($data,$id) {
		
	}
	
	public function liste_option()
	{
		
	}
	
	function ajoutercomment($data)
	{
		$check = $this->db->get_where('t_contacts_commentaires',array('comment_id' => $data['comment_id']));
		if($check->num_rows() > 0)
		{
			$res =  $this->_update('t_contacts_commentaires',$data,$data['comment_id'],'comment_id');
			return $res;
		}
		else
		{
			$id = $this->_insert('t_contacts_commentaires', $data);
			return $id;
		}
	}
}

// EOF