<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_developpers_followup extends MY_Model {

    private $table = 't_developpers_followup';
    private $primary_key = 'dev_id';

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('dev_id', 'ref', "Numéro", 'dev_id', 'dev_id', 'dev_id'),
                array('cor_tiket', 'text', "Cor tiket", 'cor_tiket'),
                array('priorite', 'text', "Priorité", 'priorite'),
                array('name', 'text', "Name", 'name'),
                array('descriptif', 'text', "Descriptif", 'descriptif'),
                array('developpeur_nom', 'text', "Développeur", 'developpeur_nom'),
                array('date_demande', 'date', "Date demande", 'date_demande'),
                array('date_de_fin_souhaitee', 'date', "Date de fin souhaitée", 'date_de_fin_souhaitee'),
                array('etat', 'text', "État", 'etat'),                     
                array('type', 'text', "Type", 'type'),                     
                array('url', 'text', "Url", 'url'),                     
            ),
            'write' => array(
                'cor_tiket' => array("Cor tiket", 'text', 'cor_tiket', false),
                'priorite' => array("Priorité", 'select', array('priorite','id','value'), false),
                'name' => array("Name", 'text', 'name', false),
                'descriptif' => array("Descriptif", 'textarea', 'descriptif', false),
                'developpeur' => array("Développeur", 'select', array('developpeur','id','value'), false),
                'date_demande' => array("Date demandée", 'date', 'date_demande', false),
                'date_de_fin_souhaitee' => array("Date de fin souhaitée", 'date', 'date_de_fin_souhaitee', false),
                'etat' => array("État", 'select', array('etat','id','value'), false),
                'type' => array("Type", 'select', array('type','id','value'), false),
                'url' => array("Url", 'text', 'url', false),
            )
        );

        return $champs[$type];
    }


    /******************************
    * Liste Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_developpers_followup';
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*,dev_id as RowID, dev_id as checkbox,utl_login as developpeur_nom");
        $this->db->join('t_utilisateurs', 'developpeur=utl_id','left');
		
	    if($filters == null)
        {
        	$this->db->where($table.'.etat = "en cours"');
        }

        switch($void){
            case 'archived':
                $this->db->where($table.'.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where($table.'.deleted != "0000-00-00 00:00:00"');
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
         $this->db->where($this->primary_key, $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(
            'developpeur_nom' => 'utl_login'
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->dev_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'dev_id' => 'int',
            'cor_tiket' => 'char',
            'priorite' => 'char',
            'name' => 'char',
            'descriptif' => 'char',
            'developpeur_nom' => 'char',
            'date_demande' => 'date',
            'date_de_fin_souhaitee' => 'date',
            'etat' => 'char',
            'type' => 'char',
            'url' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New 
    ******************************/
    public function nouveau($data) {
        return $this->_insert($this->table, $data);
    }

    /******************************
    * Detail d'une
    ******************************/
    public function detail($id) {
		$this->db->select($this->table.".*");
        $this->db->join('t_utilisateurs', 'developpeur=utl_id','left');
		$this->db->where($this->primary_key.' = "'.$id.'"');
		$q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating data
    ******************************/
    public function maj($data,$id) {
        return $this->_update($this->table,$data,$id,$this->primary_key);
    }

	/******************************
    * Archive data
    ******************************/
    public function archive($id) {
        return $this->_delete($this->table,$id,$this->primary_key,'inactive');
    }

	/******************************
    * Archive data
    ******************************/
    public function remove($id) {
        return $this->_delete($this->table,$id,$this->primary_key,'deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update($this->table,$data, $id,$this->primary_key);
    }

    public function priorite_liste()
    {
        $options = array(
            "maximale",
            "grande",
            "moyenne",
            "faible",
        );

        return $this->form_option($options);
    }

    public function developpeur_liste()
    {
        $query = $this->db->select('utl_id as id,utl_login as value')
                          ->get('t_utilisateurs');

        return $query->result();
    }

    public function etat_liste()
    {
        $options = array(
            "en cours",
            "abandonné",
            "terminé",
        );

        return $this->form_option($options);
    }

    public function type_liste()
    {
        $options = array(
            "new development",
            "improvement",
            "bug fix",
        );

        return $this->form_option($options);
    }

    public function form_option($values, $inc_index = false)
    {
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();
            if ($inc_index) {
                $val->id = $i;
            } else {
                $val->id = $values[$i];
            }

            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }
}
// EOF
