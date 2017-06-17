<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_livraisons extends MY_Model {

    public function __construct() {
        parent::__construct();
    }


    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('livraisons_id','ref',"Livraisons#",'livraisons','livraisons_id','livraisons_id'),
                array('client_name','text',"Client",'client_name'),
                array('commande_name','text',"Commande",'commande_name'),
                array('date_livraisons','date',"Date Livraison",'date_livraisons'),
                array('palettes','text',"Nombre de Palettes",'palettes'),
                array('cartons','text',"Nombre de Cartons",'cartons'),
                array('remarques','text',"Remarques",'remarques'),
                array('qty','text',"Quantité par Carton",'qty'),
                array('total','text',"Quantité totale",'total'),
            ),
            'write' => array(              
                'client' => array("Client",'select',array('client','ctc_id','ctc_nom'),false),
                'commande' => array("Commande",'select',array('commande','cmd_id','cmd_reference'),false),                  
                'date_livraisons' => array("Date Livraison",'date','date_livraisons',false),
                'palettes' => array("Nombre de Palettes",'text','palettes',false),
                'cartons' => array("Nombre de Cartons",'text','cartons',false),
                'remarques' => array("Remarques",'text','remarques',false),
                'qty' => array("Quantité par Carton",'text','qty',false),
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste Livraisons Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
    	$this->db->start_cache();
        
        $livraisons_id = formatte_sql_lien('livraisons/detail','livraisons_id','livraisons_id');
        $date_livraisons = formatte_sql_date("date_livraisons");
        $client = "ctc_nom";
        $client_name = $client." AS client_name";
        $commande = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
		$commande_name = $commande." AS commande_name";

		$this->db->select("
				livraisons_id as checkbox, livraisons_id as RowID,
                livraisons_id, 
				$client_name,
				$commande_name,
				date_livraisons,
				palettes,
				cartons,
				remarques,
				qty,
                cartons * qty as total
			", false);
		$this->db->join('t_contacts','ctc_id=client','left');
		$this->db->join('t_commandes','cmd_id=commande','left');
		
        switch($void){      
            case 'archived':
                $this->db->where('inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default: 
                $this->db->where('inactive is NULL');
                $this->db->where('deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('livraisons_id', $id);
        }
		
		$this->db->stop_cache();
        $table = 't_livraisons';

        // aliases
        $aliases = array(
          'client_name' => $client,
          'commande_name' => $commande
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->livraisons_id.'">';
        }   

        return $resultat;
    }	

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'livraisons_id' => 'int',
            'client_name' => 'char',
            'commande_name' => 'char',
            'date_livraisons' => 'date',
            'palettes' => 'int',
            'cartons' => 'int',
            'remarques' => 'char',
            'qty' => 'int',
            'total' => 'int'
        );

        return $filterable_columns;
    }

    /******************************
    * New Livraisons insert into t_livraisons table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_livraisons', $data);
    }

    /******************************
    * Detail d'une livraisons
    ******************************/
    public function detail($id) {
        $date_livraisons = formatte_sql_date("date_livraisons");
		$this->db->select("
				tl.livraisons_id, 
				tc.ctc_id as client,
				tc.ctc_nom as client_name,
				tl.commande as commande,
				CASE WHEN 
					tl.commande = -1 THEN 'Pas de Commande'
					ELSE tm.cmd_reference 
					END as commande_reference,
				$date_livraisons,
				tl.palettes,
				tl.cartons,
				tl.remarques,
                tl.qty
			");
		$this->db->join('t_contacts as tc','tc.ctc_id=tl.client','left');
		$this->db->join('t_commandes as tm','tm.cmd_id=tl.commande','left');
		$this->db->where('livraisons_id = "'.$id.'"');
		$q = $this->db->get('t_livraisons as tl');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating livraisons data
    ******************************/
    public function maj($data,$id) {
        return $this->_update('t_livraisons',$data,$id,'livraisons_id');
    }

	/******************************
    * Archive livraisons data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_livraisons',$id,'livraisons_id','inactive');
    }
	
	/******************************
    * Archive livraisons data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_livraisons',$id,'livraisons_id','deleted');
    }	

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_livraisons',$data, $id,'livraisons_id');
    }
	
	public function commande($livraisons_id){
		$this->db->select("
				tc.cmd_id,
				tc.cmd_reference
			");
		$this->db->join('t_devis as td','td.dvi_client = tl.client','inner');
		$this->db->join('t_commandes as tc','tc.cmd_devis = td.dvi_id');
		$this->db->where('tl.livraisons_id = "'.$livraisons_id.'"');
		$q = $this->db->get('t_livraisons tl');
        return $q->result();	
	}
	
	public function commande_by_client($client_id){
		$this->db->select("
				tc.cmd_id,
				tc.cmd_reference
			");
		$this->db->join('t_devis as td','td.dvi_client = tcs.ctc_id','inner');
		$this->db->join('t_commandes as tc','tc.cmd_devis = td.dvi_id');
		$this->db->where('tcs.ctc_id = "'.$client_id.'"');
		$q = $this->db->get('t_contacts tcs');
        return $q->result();	
	}	
}
// EOF
