<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_newadresse extends MY_Model {

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
		$client = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');		
		//$num="CONCAT('<a href=".site_url()."/newadresse/detail/', adresse_id, '>', t_adressevigik.adresse_numero, '</a>') as  adresse_numero";///for same column name in two tables
		$tournee_num="CONCAT('<a href=".site_url()."/newtournee/detail/', tournee_id, '>', t_tourneevigik.tournee_numero, '</a>') as  tournee_numero";///for same column name in two tables
		$adresse_nom="CONCAT(adresse_numero,'-',rue,'-',ville ) as  adresse_nom";
		
		//$societe = formatte_sql_lien('newbornes/societe_detail','scv_id','emp_nom');
        $this->db->select("@serial := @serial+1 AS sno,adresse_id,adresse_numero,$adresse_nom,rue,type_voie,ville,code,$tournee_num,ordre_tournee,$client,heure_de_livraison,type_de_livraison,horaires_de_livraison,contact,telephone_contact,derniere_facture,derniere_facture_impayee,avant_derniere,bloque",false);
		$this->db->join('t_contacts','ctc_id=client','left');
		$this->db->join('t_tourneevigik','t_tourneevigik.tournee_id=t_adressevigik.tournee','left');
        $this->db->where('adresse_inactif is null');		
		if($void!=0){
			$this->db->where('tournee',$void);
		 }
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_adressevigik';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='adresse_id';
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
		
			'adresse_nom'=>'char',
            'adresse_numero'=>'char',
			'rue'=>'char',
			'type_voie'=>'char',
			'ville'=>'char',
			'code'=>'char',
			'tournee_numero'=>'char',
			'ordre_tournee'=>'char',
			'ctc_nom'=>'char',
			'heure_de_livraison'=>'char',
			'type_de_livraison'=>'char',
            'horaires_de_livraison'=>'char',
			'contact'=>'char',
			'telephone_contact'=>'char',
			'derniere_facture'=>'char',
			'derniere_facture_impayee'=>'char',
			'avant_derniere'=>'char',
            'bloque'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un catalogue
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("adresse_id,t_adressevigik.adresse_numero as adresse_numero,rue,type_voie,ville,code,t_tourneevigik.tournee_numero as tnumero,ordre_tournee,ctc_nom,heure_de_livraison,type_de_livraison,horaires_de_livraison,contact,telephone_contact,derniere_facture,derniere_facture_impayee,avant_derniere,bloque",false);
        $this->db->join('t_contacts','ctc_id=client','left');
		$this->db->join('t_tourneevigik','tournee_id=tournee','left');
        $this->db->where('adresse_id',$id);
        $this->db->where('adresse_inactif is null');
        $q = $this->db->get('t_adressevigik');
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
        $q = $this->db->get_where('t_adressevigik', array('adresse_id' => $id)); 
        if ($q->num_rows() > 0) {
            $query = $q->row();             
		  return $query;
        }
        else {
            return null;
        }
    }
	
	 
	
	
    public function form($void) {
     
     // $this->db->insert('t_tourneevigik', $void);
		$data=$this->db->insert('t_adressevigik',$void);
        return $data;
    }
	   public function editform($void,$id) {
     
        $this->db->set($void);
		$this->db->where("adresse_id", $id);
		$data=$this->db->update("t_adressevigik", $void);
        return $data;
    }
	
public function tournee_list($id) {	
	
	   $this->db->select('tournee_id, tournee_numero');
       $query = $this->db->get('t_tourneevigik');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			
			$tournee_id=$row->tournee_id;
			if($id==$tournee_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->tournee_id."  ".$selected.">".$row->tournee_numero."</option>";
		}

	   return $data;
	}
public function client_list($id) {	
	
	   $this->db->select('ctc_id, ctc_nom');
       $query = $this->db->get('t_contacts');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			
			$ctc_id=$row->ctc_id;
			if($id==$ctc_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->ctc_id."  ".$selected.">".$row->ctc_nom."</option>";
		}

	   return $data;
	}	
	public function client_derniere($void,$id) {
		
	// $query = $this->db->get_where('t_devis', array('dvi_client' => $void ,'dvi_inactif' => NULL));
	  // $data = $query->result_array();
	    $this->db->select("fac_id,fac_reference", false);
                $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
                $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
                $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
                $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
                $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
                $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
                $this->db->where('fac_inactif is null');
		        $this->db->where('ctc_id='.$void.'');
                $this->db->order_by('fac_date', 'DESC');
                $this->db->order_by('fac_reference', 'DESC');
                $q = $this->db->get('t_factures','1');
				
	   if ($q->num_rows() > 0) {
            $i=0;
			$result=$q->result();
			$data=$result[0]->fac_reference;
			
	   /*  foreach ($q->result() as $row)
		{
			$i++;
			if($i=='1'){
				$data=$row->fac_reference;
			}
			
		   }
		   */

        }
        else {
           $data="aucun";
        }
		
	   return $data;
	}	
	public function client_impayee($void,$id) {
		
	 $this->db->select("fac_id,fac_reference", false);
                $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
                $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
                $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
                $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
                $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
                $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
                $this->db->where('fac_inactif is null');
		        $this->db->where('ctc_id='.$void.'');
		        $this->db->where('fac_reste > 0');
                $this->db->order_by('fac_date', 'DESC');
                $this->db->order_by('fac_reference', 'DESC');
                $q = $this->db->get('t_factures','1');
	   if ($q->num_rows() > 0) {
            $i=0;
			$result=$q->result();
			$data=$result[0]->fac_reference;
	   /*  foreach ($q->result() as $row)
		{
			$i++;
			if($i=='1'){
				$data=$row->fac_reference;
			}
			
		   }
		   */

        }
        else {
           $data="aucun";
        }
		
	   return $data;
	}	
	
	public function client_avant($void,$id) {
		
	 $this->db->select("fac_id,fac_reference", false);
                $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
                $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
                $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
                $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
                $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
                $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
                $this->db->where('fac_inactif is null');
		        $this->db->where('ctc_id='.$void.'');
		        $this->db->where('fac_reste > 0');
                $this->db->order_by('fac_date', 'DESC');
                $this->db->order_by('fac_reference', 'DESC');
                $q = $this->db->get('t_factures','2','1');
	   if ($q->num_rows() > 0) {
            $i=0;
			$result=$q->result();
			$data=$result[0]->fac_reference;
	   /*  foreach ($q->result() as $row)
		{
			$i++;
			if($i=='2'){
				$data=$row->fac_reference;
			}
			
		   }
		   */

        }
        else {
           $data="aucun";
        }
		
	   return $data;
	}	
	
	 public function suppression($id){		
		$void = array(
					'adresse_inactif' => date("Y-m-d h:i:sa")
		);
		$this->db->set($void);
		$this->db->where("adresse_id", $id);
		$data=$this->db->update("t_adressevigik", $void);		
    }

}
// EOF
