<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_purchases extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('purchase_id', 'ref', "Achat #", 'purchases', 'purchase_id', 'purchase_id'),
                array('description', 'text', "Achat", 'description'),
                array('delivery', 'text', "Lieu de livraison", 'delivery'),
                array('date_limit', 'date', "Date limite", 'date_limit'),
                array('sponsor_name', 'text', "Commanditaire", 'sponsor_name'),
                array('person_name', 'text', "Personne devant passer la commande", 'person_name'),
                array('beneficiary_name', 'text', "Bénéficiaire", 'beneficiary_name'),
            ),
            'write' => array(
                'description' => array("Achat", 'textarea', 'description', false),
                'delivery'    => array("Lieu de livraison", 'text', 'delivery', false),
                'date_limit'  => array("Date limite", 'date', 'date_limit', false),
                'sponsor'     => array("Commanditaire", 'select', array('sponsor', 'emp_id', 'emp_nom'), false),
                'person'      => array("Personne devant passer la commande", 'select', array('person', 'emp_id', 'emp_nom'), false),
                'beneficiary' => array("Bénéficiaire", 'select', array('beneficiary', 'emp_id', 'emp_nom'), false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste Purchases Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {        
        // première partie du select, mis en cache
        $table = 't_purchases';
        $this->db->start_cache();
        
        //$purchase_id = formatte_sql_lien('purchases/detail','purchase_id','purchase_id');
        $date_limit = formatte_sql_date("date_limit");

        $sponsor = "te1.emp_nom";
        $person  = "te2.emp_nom";
        $beneficiary = "te3.emp_nom";

        $sponsor_name  = $sponsor." AS sponsor_name";
        $person_name   = $person." AS person_name";
        $beneficiary_name = $beneficiary." AS beneficiary_name";

		$this->db->select("*, purchase_id as RowID, purchase_id as checkbox, description, delivery, date_limit, $sponsor_name, $person_name, $beneficiary_name");
		$this->db->join('t_employes as te1','te1.emp_id=sponsor','left');
		$this->db->join('t_employes as te2','te2.emp_id=person','left');
		$this->db->join('t_employes as te3','te3.emp_id=beneficiary','left');

        switch($void){
            case 'archived':
                $this->db->where($table.'.inactive is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.deleted is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.inactive is NULL');
                $this->db->where($table.'.deleted is NULL');
                break;
        }

        $id = intval($void);
        
        if ($id > 0) {
         $this->db->where('purchase_id', $id);
        }
		
        $this->db->stop_cache();

        // aliases
        $aliases = array(
           'sponsor_name' => $sponsor,
           'person_name' => $person,
           'beneficiary_name' => $beneficiary,
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
		
		//add checkbox into data
        for($i=0; $i < count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->purchase_id.'">';
		}
		
        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'purchase_id' => 'int',
            'delivery' => 'char',
            'description'=>'char',
            'date_limit' => 'date',
            'sponsor_name' => 'char',
            'person_name' => 'char',
            'beneficiary_name' => 'char'
        );

        return $filterable_columns;
    }

    /******************************
    * New Purchase insert into t_purchases table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_purchases', $data);
    }

    /******************************
    * Détail d'une tâche
    ******************************/
    public function detail($id) {
		$this->db->select("tp.purchase_id, tp.description, tp.delivery, tp.date_limit, te1.emp_nom as sponsor_name, te1.emp_id as sponsor, te2.emp_nom as person_name, te2.emp_id as person, te3.emp_nom as beneficiary_name, te3.emp_id as beneficiary");
		$this->db->join('t_employes as te1','te1.emp_id=sponsor','left');
		$this->db->join('t_employes as te2','te2.emp_id=person','left');
		$this->db->join('t_employes as te3','te3.emp_id=beneficiary','left');
		$this->db->where('purchase_id = '.$id);
		$this->db->where('inactive is NULL');
		$q = $this->db->get('t_purchases as tp');        		
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating purchase data
    ******************************/
    public function maj($data,$id) {
        return $this->_update('t_purchases',$data,$id,'purchase_id');
    }

	/******************************
    * Archive purchase data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_purchases',$id,'purchase_id','inactive');
    }
	
	/******************************
    * Archive purchase data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_purchases',$id,'purchase_id', 'deleted');      
    }	

    /******************************
    * Archive purchase data
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_purchases',$data, $id,'purchase_id');
    }   
}
// EOF
