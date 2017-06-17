<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_newbornes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des actions
	
    ******************************//*$this->db->select('t_bornes.*,t_contacts.ctc_nom,t_societes_vendeuses.scv_nom,t_devis.dvi_reference,t_factures.fac_reference');
    $this->db->from('t_bornes');
    $this->db->join('t_contacts', 't_contacts.ctc_id = t_bornes.client', 'left'); 
    $this->db->join('t_devis', 't_devis.dvi_id = t_bornes.devis', 'left'); 
    $this->db->join('t_factures', 't_factures.fac_id = t_bornes.client', 'left'); 
    $this->db->join('t_societes_vendeuses', 't_societes_vendeuses.scv_id = t_bornes.societe', 'left'); 
    $this->db->get();
    return $query->result();*/
	 public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

		$query = $this->db->query('SET @serial=0;');
		//$this->db->query('SET @serial=0;');
        // lecture des informations
       // $numero = formatte_sql_lien('newbornes/detail','bornes_id','borne_numero');
		$societe = formatte_sql_lien('newbornes/societe_detail','scv_id','scv_nom');
        $this->db->select("@serial := @serial+1 AS sno,bornes_id,bornes_id as RowID,borne_numero,$societe,bornes_adresse",false);
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
        $this->db->where('bornes_inactif is null');

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('bornes_id', $id);
        }
			  
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_bornes';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='bornes_id';
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
            'borne_numero'=>'char',
            'scv_nom'=>'char',
            'bornes_adresse'=>'char'	
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un catalogue
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("bornes_id,borne_numero,scv_nom,bornes_adresse",false);
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
        $this->db->where('bornes_id',$id);
        $this->db->where('bornes_inactif is null');
        $q = $this->db->get('t_bornes');
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
        $q = $this->db->get_where('t_bornes', array('bornes_id' => $id)); 
        if ($q->num_rows() > 0) {
            $query = $q->row();             
		  return $query;
        }
        else {
            return null;
        }
    }
	
	  public function societe_detail($id) {

        // lecture des informations
        $this->db->select("scv_nom",false);
        $this->db->where('scv_id',$id);
        $q = $this->db->get('t_societes_vendeuses');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }
	
	
    public function bornes_form($void) {
     
     // $this->db->insert('t_bornes', $void);
		$data=$this->db->insert('t_bornes',$void);
        return $this->db->insert_id();
    }
	   public function bornes_editform($void,$id) {
     
        $this->db->set($void);
		$this->db->where("bornes_id", $id);
		$data=$this->db->update("t_bornes", $void);
        return $data;
    }
	
public function society_list($id) {		
	   $this->db->select('scv_id, scv_nom');
       $query = $this->db->get('t_societes_vendeuses');
	   //$data[]="<option value=''>sélectionner</option>";
	   
	   return $query->result();
	}
	
	 public function suppression($id) {
	   $void = array(

					'bornes_inactif' => date("d-m-Y h:i:sa")
		);
		$this->db->set($void);
		$this->db->where("bornes_id", $id);
		$data=$this->db->update("t_bornes", $void);
		
    }

}
// EOF
