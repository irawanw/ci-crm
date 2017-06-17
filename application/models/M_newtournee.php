<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_newtournee extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des actions
	
    ******************************//*$this->db->select('t_tourneevigik.*,t_contacts.ctc_nom,t_societes_vendeuses.scv_nom,t_devis.dvi_reference,t_factures.fac_reference');
    $this->db->from('t_tourneevigik');
    $this->db->join('t_contacts', 't_contacts.ctc_id = t_tourneevigik.client', 'left'); 
    $this->db->join('t_devis', 't_devis.dvi_id = t_tourneevigik.devis', 'left'); 
    $this->db->join('t_factures', 't_factures.fac_id = t_tourneevigik.client', 'left'); 
    $this->db->join('t_societes_vendeuses', 't_societes_vendeuses.scv_id = t_tourneevigik.societe', 'left'); 
    $this->db->get();
    return $query->result();*/
	 public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

		$query = $this->db->query('SET @serial=0;');
        // lecture des informations
       // $numero = formatte_sql_lien('newtournee/detail','tournee_id','tournee_numero');
		//$societe = formatte_sql_lien('newbornes/societe_detail','scv_id','emp_nom');
		  $tournee_nom="CONCAT('<a href=".site_url()."/newadresse/index/', tournee_id, '>', t_tourneevigik.tournee_nom, '</a>') as  tournee_nom";///for same column
        $this->db->select("@serial := @serial+1 AS sno,tournee_id,tournee_id as RowID,tournee_numero,$tournee_nom,emp_nom,remarques",false);
        $this->db->join('t_employes','emp_id=livreur','left');
        $this->db->where('tournee_inactif is null');

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('tournee_id', $id);
        }
			  
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_tourneevigik';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='tournee_id';
		$ordering='desc';
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'tournee_numero'=>'char',
			'tournee_nom'=>'char',
            'emp_nom'=>'char',
			'remarques'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un catalogue
    ******************************/
    public function detail($id) {
        // lecture des informations
        $this->db->select("tournee_id,tournee_numero,tournee_nom,emp_nom,remarques",false);
        $this->db->join('t_employes','emp_id=livreur','left');
        $this->db->where('tournee_id',$id);
        $this->db->where('tournee_inactif is null');
        $q = $this->db->get('t_tourneevigik');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }
	    public function edit_detail($id) {

        // lecture des informations        
        $q = $this->db->get_where('t_tourneevigik', array('tournee_id' => $id)); 
        if ($q->num_rows() > 0) {
            $query = $q->row();             
		  return $query;
        }
        else {
            return null;
        }
    }
	
	  public function employe_detail($id) {

        // lecture des informations
        $this->db->select("emp_nom",false);
        $this->db->where('emp_id',$id);
        $q = $this->db->get('t_employes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }
	
	
    public function form($void) {
     
     // $this->db->insert('t_tourneevigik', $void);
		$data=$this->db->insert('t_tourneevigik',$void);
        return $this->db->insert_id();
    }
	   public function editform($void,$id) {
     
        $this->db->set($void);
		$this->db->where("tournee_id", $id);
		$data=$this->db->update("t_tourneevigik", $void);
        return $data;
    }
	
public function employe_list($id) {	
	
	   $this->db->select('emp_id, emp_nom');
       $query = $this->db->get('t_employes');
	   //$data[]="<option value=''>sélectionner</option>";
	   

	   return $query->result();
	}
	
	 public function suppression($id) {
		 $void = array(

					'tournee_inactif' => date("d-m-Y h:i:sa")
		);
		$this->db->set($void);
		$this->db->where("tournee_id", $id);
		$data=$this->db->update("t_tourneevigik", $void);
		
    }

}
// EOF
