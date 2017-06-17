<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_newtournee_journalieres extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des actions
	
    ******************************
	*/
 	 public function index_liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();
         $tournee_nom="CONCAT('<a href=".site_url()."/newtournee_journalieres/listnew/', tourneejouern_nom, '>', t_tourneevigik.tournee_nom, '</a>') as  tournee_nom";///for same column name in two tables
		$query = $this->db->query('SET @serial=0;');
	    $this->db->select("@serial := @serial+1 AS sno,tourneejouern_id,$tournee_nom,tourneejouern_numero,tourneejouern_livreur,tourneejouern_date",false);		
        $this->db->join('t_tourneevigik','tournee_id=tourneejouern_nom','left');
        $this->db->where('tourneejouern_inactif is null');
			  
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_tourneejouern';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='tourneejouern_id';
		$ordering='desc';
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        return $resultat;
    }
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
        $this->db->select("@serial := @serial+1 AS sno,adresse_id,remarques,tourneejouern_resultat,adresse_numero,$adresse_nom,rue,type_voie,ville,code,$tournee_num,ordre_tournee,$client,heure_de_livraison,type_de_livraison,horaires_de_livraison,contact,telephone_contact,derniere_facture,derniere_facture_impayee,avant_derniere,bloque",false);
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
		
            'tourneejouern_resultat'=>'char',
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
            'bloque'=>'char',
            'remarques'=>'char'
			
        );
        return $filterable_columns;
    }
	
	 public function listeold($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc"){

	 ////adresse not set will e empty/////
	/*	$this->db->select("tournee",false);
		$this->db->where('tournee',$void);
		$q = $this->db->get('t_adressevigik');
		if ($q->num_rows() > 0) {
			$empty=$this->db->where("tournee", $void);
		}
		else{
			$empty="";
		}
		*/

		////adresse not set will e empty end/////	
        // première partie du select, mis en cache
        $this->db->start_cache();

		$query = $this->db->query('SET @serial=0;');
	    $this->db->select("@serial := @serial+1 AS sno,tourneejouern_id,tournee_nom,adresse_numero,adresse_id,tourneejouern_numero,tourneejouern_resultat,tourneejouern_livreur,tourneejouern_date,ordre_tournee,adresse_numero,rue,type_voie,ville,ctc_nom,type_de_livraison,horaires_de_livraison,contact,telephone_contact,remarques",false);		
		$this->db->join('t_adressevigik','tournee=tourneejouern_nom','left');
        $this->db->join('t_tourneevigik','tournee_id=tourneejouern_nom','left');
        $this->db->join('t_contacts','ctc_id=client','left');
		$this->db->where("tourneejouern_id", $void);
        $this->db->where('tourneejouern_inactif is null');
		$empty;
			
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_tourneejouern';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='tourneejouern_id';
		$ordering='desc';
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
  /*  public function listeold_filterable_columns() {
        $filterable_columns = array(
            'tourneejouern_nom'=>'char',
            'tourneejouern_numero'=>'char',
            'tourneejouern_livreur'=>'char',
            'tourneejouern_date'=>'char',
            'tourneejouern_resultat'=>'char',
            'tournee_nom'=>'char',
            'ordre_tournee'=>'char',
            'adresse_numero'=>'char',
            'rue'=>'char',
            'type_voie'=>'char',
            'ville'=>'char',
            'ctc_nom'=>'char',
            'type_de_livraison'=>'char',
            'horaires_de_livraison'=>'char',
            'contact'=>'char',
            'telephone_contact'=>'char',
            'remarques'=>'char'
        );
        return $filterable_columns;
    }*/
	public function string_date($id){


	
		$this->db->select("tourneejouern_date,tourneejouern_nom,valider",false);	
		$this->db->where("tourneejouern_nom", $id);
        $this->db->where('tourneejouern_inactif is null');
        $q= $this->db->get('t_tourneejouern');
		if ($q->num_rows() > 0) {
        foreach ($q->result() as $row)
		{
			$resultat=$row->tourneejouern_date;
			$valider=$row->valider;
			$tournee_id=$row->tourneejouern_nom;
		}		
		$date = $resultat;
		$date = str_replace('/', '-', $date);
		$resultat= date('d-m-Y', strtotime($date));
		 $resultat=date( "jS F Y", strtotime( $resultat ));
		 
		 
		 ///////tournee nom//////////
        $this->db->select("tournee_nom",false);	
		$this->db->where("tournee_id", $tournee_id);
        $qres= $this->db->get('t_tourneevigik');
		if ($qres->num_rows() > 0) {
        foreach ($qres->result() as $rowres)
		{
			$tournee_nom=$rowres->tournee_nom;
		}	
		}
//////////tournee nom end/////
		 
		return $data=array($resultat,$valider,$tournee_nom,$tournee_id);
        }
        else {
            return null;
        }
		 
	}
        public function mass_action($void,$id) {
		$this->db->set($void);
		$this->db->where("adresse_id", $id);
		$data=$this->db->update("t_adressevigik", $void);
        return $data;
		}
		public function validate($void,$id) {
		$this->db->set($void);
		$this->db->where("tourneejouern_nom", $id);
		$data=$this->db->update("t_tourneejouern", $void);
        return $data;
		}
    /******************************
    * Détail d'un catalogue
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("tourneejouern_id,tournee_nom,tourneejouern_numero,tourneejouern_livreur,tourneejouern_date",false);
        $this->db->join('t_tourneevigik','tournee_id=tourneejouern_nom','left');
        $this->db->where('tourneejouern_id',$id);
        $this->db->where('tourneejouern_inactif is null');
        $q = $this->db->get('t_tourneejouern');
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
        $q = $this->db->get_where('t_tourneejouern', array('tourneejouern_id' => $id)); 
        if ($q->num_rows() > 0) {
            $query = $q->row();             
		  return $query;
        }
        else {
            return null;
        }
    }
	 
	
	  public function tournee_ajxdetail($id) {

        ///existing tournee check
		
		$this->db->select("tourneejouern_id,tourneejouern_date",false);
        $this->db->where('tourneejouern_nom',$id);
        $q = $this->db->get('t_tourneejouern');
        if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row)
		    {
			$resultnum[]=$row->tourneejouern_id;
			$resultnum[]=$row->tourneejouern_date;
		    }
		}
		else{
			$resultnum[]=0;
			$resultnum[]='';
		}
		//tournee
        $this->db->select("tournee_numero,livreur",false);
        $this->db->where('tournee_id',$id);
        $q = $this->db->get('t_tourneevigik');
        if ($q->num_rows() > 0) {
		 foreach ($q->result() as $row)
		{
			
			$resultnum[]=$id;
			$resultnum[]=$row->tournee_numero;
			$resultnum[]=$row->livreur;
		}
		
		
		
		return $resultnum;
        }
        else {
            return null;
        }
    }
	
    public function tournjouern_form($void,$exist_id,$date) {
     
     // $this->db->insert('t_bornes', $void);
	     if($exist_id=='0'){
		 $data=$this->db->insert('t_tourneejouern',$void);
		 }
		 else{
		$this->db->set($date);
		$this->db->where("tourneejouern_id", $exist_id);
		$data=$this->db->update("t_tourneejouern", $date);
		 }
		
      /*  $this->db->set($adresse);
		$this->db->where("tournee", $tournee_id);
		$data=$this->db->update("t_adressevigik", $adresse);*/
        return $data;
    }
	   public function tournjouern_editform($void,$id) {
     
        $this->db->set($void);
		$this->db->where("tourneejouern_id", $id);
		$data=$this->db->update("t_tourneejouern", $void);
		
		/*$this->db->set($adresse);
		$this->db->where("tournee", $tournee_id);
		$data=$this->db->update("t_adressevigik", $adresse);*/
        return $data;
    }
	
public function tournee_list($id) {		
	   $this->db->select('tournee_id, tournee_nom');
       $query = $this->db->get('t_tourneevigik');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			
			$tournee_id=$row->tournee_id;
			if($id==$tournee_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->tournee_id."  ".$selected.">".$row->tournee_nom."</option>";
		}

	   return $data;
	}
	public function adresse_list($id) {		
	   $this->db->select('adresse_id,adresse_numero,rue,ville');
       $query = $this->db->get('t_adressevigik');
	   $data[]="<option value=''>sélectionner</option>";
	    $data[]="<option value='".site_url()."/newadresse/create'>nouvelle adrese</option>";
	   foreach ($query->result() as $row)
		{	
			$adresse_id=$row->adresse_id;
			if($id==$adresse_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->adresse_id."  ".$selected.">".$row->adresse_numero."-".$row->rue."-".$row->ville."</option>";
		}

	   return $data;
	}
	public function new_adresse($void,$id) {	

	    $this->db->set($void);
		$this->db->where("adresse_id", $id);
		$data=$this->db->update("t_adressevigik", $void);
	   return $data;
	}
	 public function suppression($id) {
		
		$void = array(
               'tourneejouern_inactif' => date("d-m-Y h:i:sa")
		);
		$this->db->set($void);
		$this->db->where("tourneejouern_id", $id);
		$data=$this->db->update("t_tourneejouern", $void);
		
    }
	
public function exportation($id) {
$result="";
		$query = $this->db->query('SET @serial=0;');
	    $q = $this->db->select("@serial := @serial+1 AS sno,tourneejouern_id,tournee_nom,adresse_numero,tourneejouern_numero,tourneejouern_livreur,tourneejouern_date,ordre_tournee,adresse_numero,rue,type_voie,ville,bloque,ctc_nom,type_de_livraison,horaires_de_livraison,contact,telephone_contact,remarques",false)
               ->join('t_adressevigik','tournee=tourneejouern_nom','left')
		       ->join('t_tourneevigik','tournee_id=tourneejouern_nom','left')
		       ->join('t_contacts','ctc_id=client','left')
               ->where('tourneejouern_inactif is null')
			   ->get('t_tourneejouern');
			   
		if ($q->num_rows() > 0) {
           $result = $q->result_array();
			
            return $result;
        }
}/////function close

}
// EOF
