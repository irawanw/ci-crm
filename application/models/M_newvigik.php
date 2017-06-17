<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_newvigik extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des actions
	
    ******************************//*$this->db->select('t_vigik.*,t_contacts.ctc_nom,t_societes_vendeuses.scv_nom,t_devis.dvi_reference,t_factures.fac_reference');
    $this->db->from('t_vigik');
    $this->db->join('t_contacts', 't_contacts.ctc_id = t_vigik.client', 'left'); 
    $this->db->join('t_devis', 't_devis.dvi_id = t_vigik.devis', 'left'); 
    $this->db->join('t_factures', 't_factures.fac_id = t_vigik.client', 'left'); 
    $this->db->join('t_societes_vendeuses', 't_societes_vendeuses.scv_id = t_vigik.societe', 'left'); 
    $this->db->get();
    return $query->result();*/
	 public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();
		
		$query = $this->db->query('SET @serial=0;');
        // lecture des informations
       // $numero = formatte_sql_lien('newvigik/detail','vigik_id','numero');
		//$num="CONCAT('<a href=".site_url()."/newvigik/detail/', vigik_id, '>', t_vigik.vigik_numero, '</a>') as  vigik_numero";///for same column name in two tables
		$numadresse="CONCAT('<a href=".site_url()."/newadresse/detail/', adresse_id, '>', t_adressevigik.adresse_numero, '</a>') as  adresse_numero";///for same column name in two tables
		$numtournee="CONCAT('<a href=".site_url()."/newtournee/detail/', tournee_id, '>', t_tourneevigik.tournee_numero, '</a>') as  tournee_numero";///for same column name in two tables
		$client = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
		$societe = formatte_sql_lien('newvigik/societe_detail','scv_id','scv_nom');
		$fac_reference = formatte_sql_lien('factures/detail','fac_id','fac_reference');
		$dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
		 //$this->db->select("");
        $this->db->select("@serial := @serial+1 AS sno,vigik_id,vigik_numero,$societe,t_bornes.borne_numero as borne_numero,type,$client,$fac_reference,$dvi_reference,etat,adresse_numero,$numtournee,ouvertures,chargements",false);
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
		$this->db->join('t_contacts','ctc_id=client','left');
		$this->db->join('t_devis','dvi_id=devis','left');
		$this->db->join('t_adressevigik','t_adressevigik.adresse_id=t_vigik.cl_adresse','left');
		$this->db->join('t_tourneevigik','t_tourneevigik.tournee_id=t_vigik.tournee','left');
		$this->db->join('t_bornes','t_bornes.bornes_id=t_vigik.borne','left');
		$this->db->join('t_factures','fac_id=facture','left');
        $this->db->where('vigik_inactif is null');		
        
			
        //$this->db->order_by("numero asc");			
        $this->db->stop_cache();
        $table = 't_vigik';
     
        // aliases
        $aliases = array(
		
        );
		$ordercol='vigik_id';
		$ordering='desc';
		
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
		
        $this->db->flush_cache();

		//echo $this->db->last_query();	
        return $resultat;
		
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'vigik_numero'=>'char',
            'borne_numero'=>'char',
            'scv_nom'=>'char',
            'type'=>'char',
			'ctc_nom'=>'char',
            'fac_reference'=>'char',
			'dvi_reference'=>'char',
			'etat'=>'char',
            'adresse_numero'=>'char',
            'tournee_numero'=>'char',
            'ouvertures'=>'char',
            'chargements'=>'char'		
        );
        return $filterable_columns;
    }

	    public function mass_action($void,$id) {
		$this->db->set($void);
		$this->db->where("vigik_id", $id);
		$data=$this->db->update("t_vigik", $void);
        return $data;
		}
	public function upload($void,$numero){	

		////insert and update end
		$this->db->where("vigik_numero", $numero);		
        $this->db->get('t_vigik');
		if($this->db->affected_rows() > 0) {
		$this->db->set($void);
		$this->db->where("vigik_numero", $numero);
		$data=$this->db->update("t_vigik", $void);
        return $data;
		}
		else {
	    $data=$this->db->insert('t_vigik',$void);
        return $data;
    	}
			////insert and update end
	}
    public function detail($id) {

        // lecture des informations
        $this->db->select("vigik_id,t_vigik.vigik_numero as vigik_numero,t_bornes.borne_numero as borne_numero,scv_nom,type,ctc_nom,fac_reference,dvi_reference,etat,t_adressevigik.adresse_numero as adresse_numero,t_tourneevigik.tournee_numero as tournee_numero,ouvertures,chargements",false);
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
		$this->db->join('t_contacts','ctc_id=client','left');
		$this->db->join('t_factures','fac_id=facture','left');
		$this->db->join('t_devis','dvi_id=devis','left');
		$this->db->join('t_adressevigik','t_adressevigik.adresse_id=t_vigik.cl_adresse','left');
		$this->db->join('t_tourneevigik','t_tourneevigik.tournee_id=t_vigik.tournee','left');
		$this->db->join('t_bornes','t_bornes.bornes_id=t_vigik.borne','left');
        $this->db->where('vigik_id',$id);
        $this->db->where('vigik_inactif is null');
        $q = $this->db->get('t_vigik');
		
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
        $q = $this->db->get_where('t_vigik', array('vigik_id' => $id)); 
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
	
	
    public function vigik_form($void) {
     
     // $this->db->insert('t_vigik', $void);
		$data=$this->db->insert('t_vigik',$void);
        return $data;
    }
		
    public function vigik_editform($void,$id) {     
	    $this->db->set($void);
		$this->db->where("vigik_id", $id);
		$data=$this->db->update("t_vigik", $void);
        return $data;
    }
	
	public function client_list($void) {
		
		
       $this->db->select('ctc_id, ctc_nom');
       $query = $this->db->get('t_contacts');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			if($void==$row->ctc_id){ $selected='selected'; }else{ $selected='';}
			
			
			$data[]="<option value=".$row->ctc_id." ".$selected." >".$row->ctc_nom."</option>";
		}
		

	   return $data;

    }
	public function client_data($void) {
	  $data="";
	   $query = $this->db->get_where('t_contacts', array('ctc_id' => $void));
	   foreach ($query->result() as $row)
		{
			$data=$row->ctc_adresse;
	       
		}	   
	   return $data;	   

    }
	public function client_facture($void,$id) {
		
	/*  $query = $this->db->get_where('t_devis', array('dvi_client' => $void));
	  // $data = $query->result_array();
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			$data[]="<option value=".$row->dvi_id.">".$row->dvi_reference."</option>";
		}

	   return $data;
	   */
	    $this->db->select("fac_id,fac_reference");
		$this->db->join('t_devis','ctc_id=dvi_client','inner');
		$this->db->join('t_commandes','dvi_id=cmd_devis','inner');
		$this->db->join('t_factures','cmd_id=fac_commande','inner');
		$this->db->where('ctc_id='.$void.'');
		$q = $this->db->get('t_contacts');
	 
	
	 /*   $this->db->select("fac_id,fac_reference");
		$this->db->join('t_commandes','cmd_id=fac_commande','left');
		$this->db->join('t_devis','dvi_id=cmd_devis','left');
		$this->db->join('t_contacts','ctc_id=dvi_client','left');
		$this->db->where('fac_inactif is null');
		$this->db->where('ctc_id='.$void.'');
		$q = $this->db->get('t_factures');
 
	 SELECT `fac_id`,`fac_reference` FROM `t_contacts` INNER JOIN `t_devis` ON `ctc_id`=`dvi_client`  INNER JOIN `t_commandes` ON `dvi_id`=`cmd_devis`  INNER JOIN `t_factures` ON `cmd_id`=`fac_commande` AND `ctc_id` = 1
		*/
		 $data[]="<option value=''>sélectionner</option>";
		 
        if ($q->num_rows() > 0) {
            
	     foreach ($q->result() as $row)
		{
			if($id==$row->fac_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->fac_id." ".$selected.">".$row->fac_reference."</option>";
		   }
            $data[]="<option value='' >aucun</option>";
        }
        else {
           $data[]="<option value='' selected>aucun</option>";
        }
		
	   return $data;
			   
	   
	}
	public function client_devis($void,$id) {
		
	 $query = $this->db->get_where('t_devis', array('dvi_client' => $void ,'dvi_inactif' => NULL));
	  // $data = $query->result_array();
	   $data[]="<option value=''>sélectionner</option>";
	   if ($query->num_rows() > 0) {
	   foreach ($query->result() as $row)
		{  
			if($id==$row->dvi_id){ $selected='selected'; }else{ $selected='';}
			$data[]="<option value=".$row->dvi_id." ".$selected.">".$row->dvi_reference."</option>";
		}
		 $data[]="<option value='' >aucun</option>";
	   }
	   else 
	   {
           $data[]="<option value='' selected >aucun</option>";
       }

	   return $data;
	}
	public function client_adresse($void,$id) {
		
	 $query = $this->db->get_where('t_adressevigik', array('client' => $void ,'adresse_inactif' => NULL));
	  // $data = $query->result_array();
	   $data[]="<option value=''>sélectionner</option>";
	   if ($query->num_rows() > 0) {
	   foreach ($query->result() as $row)
		{  
			if($id==$row->adresse_id){ $selected='selected'; }else{ $selected='';}
			$data[]="<option value=".$row->adresse_id." ".$selected.">".$row->adresse_numero."</option>";
		}
		return $data;
	   }
	   else 
	   {
           $newdata="create";
       }

	   return $newdata;
	}
	
	public function society_list($id) {		
	   $this->db->select('scv_id, scv_nom');
       $query = $this->db->get('t_societes_vendeuses');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			
			$scv_id=$row->scv_id;
			if($id==$scv_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->scv_id."  ".$selected.">".$row->scv_nom."</option>";
		}

	   return $data;
	}
	public function bornes_list($id) {
		
	   $this->db->select('bornes_id, borne_numero');
       $query = $this->db->get('t_bornes');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			$borne_id=$row->bornes_id;
			if($id==$borne_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->bornes_id." ".$selected.">".$row->borne_numero."</option>";
		}

	   return $data;
	}
	public function adresse_list($id) {		
	   $this->db->select('adresse_id, adresse_numero');
       $query = $this->db->get('t_adressevigik');
	   $data[]="<option value=''>sélectionner</option>";
	   foreach ($query->result() as $row)
		{
			
			$adresse_id=$row->adresse_id;
			if($id==$adresse_id){ $selected='selected'; }else{ $selected='';}
			
			$data[]="<option value=".$row->adresse_id."  ".$selected.">".$row->adresse_numero."</option>";
		}

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
	 public function suppression($id) {
		$void = array(
               'vigik_inactif' => date("d-m-Y h:i:sa")
		);
		$this->db->set($void);
		$this->db->where("vigik_id", $id);
		$data=$this->db->update("t_vigik", $void);
    }

	
 public function exportation($id) {
$result="";

if($id==0){
				
        $q = $this->db->select("vigik_id,t_vigik.vigik_numero as vigik_numero,t_societes_vendeuses.scv_nom as societe,t_bornes.borne_numero as borne_numero,type,t_contacts.ctc_nom as client,fac_reference,dvi_reference,etat,t_adressevigik.adresse_numero as adresse_numero,t_adressevigik.rue as rue,t_adressevigik.ville as ville,t_adressevigik.code as code,t_tourneevigik.tournee_numero as tournee_numero",false)
               ->join('t_societes_vendeuses','scv_id=societe','left')
		       ->join('t_contacts','ctc_id=client','left')
		       ->join('t_devis','dvi_id=devis','left')
		       ->join('t_adressevigik','t_adressevigik.adresse_id=t_vigik.cl_adresse','left')
		       ->join('t_tourneevigik','t_tourneevigik.tournee_id=t_vigik.tournee','left')
		       ->join('t_bornes','t_bornes.bornes_id=t_vigik.borne','left')
		       ->join('t_factures','fac_id=facture','left')
               ->where('vigik_inactif is null')
			   ->get('t_vigik');
			   
		if ($q->num_rows() > 0) {
           $result = $q->result_array();
			
            return $result;
        }
}
else{
	
	        $q = $this->db->select("vigik_id,t_vigik.vigik_numero as vigik_numero,t_societes_vendeuses.scv_nom as societe,t_bornes.borne_numero as borne_numero,type,t_contacts.ctc_nom as client,fac_reference,dvi_reference,etat,t_adressevigik.adresse_numero as adresse_numero,t_adressevigik.rue as rue,t_adressevigik.ville as ville,t_adressevigik.code as code,t_tourneevigik.tournee_numero as tournee_numero",false)
               ->join('t_societes_vendeuses','scv_id=societe','left')
		       ->join('t_contacts','ctc_id=client','left')
		       ->join('t_devis','dvi_id=devis','left')
		       ->join('t_adressevigik','t_adressevigik.adresse_id=t_vigik.cl_adresse','left')
		       ->join('t_tourneevigik','t_tourneevigik.tournee_id=t_vigik.tournee','left')
		       ->join('t_bornes','t_bornes.bornes_id=t_vigik.borne','left')
		       ->join('t_factures','fac_id=facture','left')
			   ->where('vigik_id',$id)
               ->where('vigik_inactif is null')
			   ->get('t_vigik');
			   
		if ($q->num_rows() > 0) {
           $result = $q->result_array();
            return $result;
        }
}	
}/////function close


    /******************************
     * Importation d'un catalogue
     ******************************/
    public function importation($id,$fichier) {

        // récupération de la famille du catalogue
        $q = $this->db->select("vfm_code")
            ->join('v_familles','vfm_id=cat_famille','left')
            ->where('cat_id',$id)
            ->get('t_catalogues');
        if ($q->num_rows() > 0) {
            $famille = 'Famille_'.$q->row()->vfm_code;
        }
        else {
            return false;
        }

        // chargement des bibliothèques PHPExcel famille
        require 'application/third_party/PHPExcel/IOFactory.php';
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library($famille,NULL,'famille');

        // ouverture du fichier
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(
            'memoryCacheSize' => '512k'
        );
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        /*$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod);*/

        // lecture du fichier avec saut de la première ligne
        $lecteur = $objReader->load($fichier);
        $feuille = $lecteur->getSheet(0);
        $highestRow = $feuille->getHighestRow();
        $highestColumn = $feuille->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $data = array();
        for ($row=2; $row <= $highestRow; $row++) {
            if ($feuille->getCellByColumnAndRow(0, $row) == '') continue;
            $ligne = array();
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $cell = $feuille->getCellByColumnAndRow($col, $row);
                $cellule = $cell->getValue();
                if (!isset($cellule)) {
                    $cellule = '';
                }
                $ligne[] = $cellule;
            }
            $data[] = $ligne;
        }

        // exploitation du fichier
        $resultat = $this->famille->exploite($id,$data);
        $lecteur->disconnectWorksheets();
        unset($lecteur);
        unlink($fichier);
        return $resultat;
    }	
}
// EOF
