<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_suivi_adwords extends MY_Model {

    protected $_table = "t_suivi_adwords";

    public function __construct() {
        parent::__construct();
    }


    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('suivi_adword_id', 'ref', "id#", 'suivi_adword_id', 'suivi_adword_id', 'suivi_adword_id'),
                array('mots_clefs', 'text', "mots clefs", 'mots_clefs'),
                array('distribution_prospectus', 'text', "distribution prospectus", 'distribution_prospectus'),
                array('distribution_de_prospectus', 'text', "distribution de prospectus", 'distribution_de_prospectus'),
                array('distribution_de_flyer', 'text', "distribution de flyer", 'distribution_de_flyer'),
                array('distribution_de_prospectus_en_boites_aux_lettres', 'text', "distribution de prospectus en boites aux lettres", 'distribution_de_prospectus_en_boites_aux_lettres'),
                array('distribution_de_publicite', 'text', "distribution de publicité", 'distribution_de_publicite'),
                array('societe_de_distribution_de_prospectus', 'text', "societe de distribution de prospectus", 'societe_de_distribution_de_prospectus'),
                array('tarif_distribution_de_prospectus', 'text', "tarif distribution de prospectus", ''),
                array('distribution_prospectus_paris', 'text', "distribution prospectus paris", 'distribution_prospectus_paris'),
                array('distribution_de_prospectus_paris', 'text', "distribution de prospectus paris", 'distribution_de_prospectus_paris'),
                array('distribution_de_flyer_paris', 'text', "distribution de flyer paris", 'distribution_de_flyer_paris'),
                array('distribution_de_publicite_paris', 'text', "distribution de publicité paris", 'distribution_de_publicite_paris'),
                array('mot', 'text', "mot", 'mot'),
            ),
            'write' => array(
                'mots_clefs' => array("mots clefs", 'text', 'mots_clefs', false),
                'distribution_prospectus' => array("distribution prospectus", 'text', 'distribution_prospectus', false),
                'distribution_de_prospectus' => array("distribution de prospectus", 'text', 'distribution_de_prospectus', false),
                'distribution_de_flyer' => array("distribution de flyer", 'text', 'distribution_de_flyer', false),
                'distribution_de_prospectus_en_boites_aux_lettres' => array("distribution de prospectus en boites aux lettres", 'text', 'distribution_de_prospectus_en_boites_aux_lettres', false),
                'distribution_de_publicite' => array("distribution de publicité", 'text', 'distribution_de_publicite', false),
                'societe_de_distribution_de_prospectus' => array("societe de distribution de prospectus", 'text', 'societe_de_distribution_de_prospectus', false),
                'tarif_distribution_de_prospectus' => array("tarif distribution de prospectus", 'text', 'tarif_distribution_de_prospectus', false),
                'distribution_prospectus_paris' => array("distribution prospectus paris", 'text', 'distribution_prospectus_paris', false),
                'distribution_de_prospectus_paris' => array("distribution de prospectus paris", 'text', 'distribution_de prospectus_paris', false),
                'distribution_de_flyer_paris' => array("distribution de flyer paris", 'text', 'distribution_de_flyer_paris', false),
                'distribution_de_publicite_paris' => array("distribution de publicité paris", 'text', 'distribution_de_publicite_paris', false),
                'mot' => array("mot", 'text', 'mot', false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = $this->_table;
        // première partie du select, mis en cache
        $this->db->start_cache();
		$this->db->select($table.".*, suivi_adword_id as RowID,suivi_adword_id as checkbox");

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
            $this->db->where('suivi_adword_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->suivi_adword_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'suivi_adword_id' => 'int',
            'mots_clefs' => 'char',
            'distribution_prospectus' => 'char',
            'distribution_de_prospectus' => 'char',
            'distribution_de_flyer' => 'char',
            'distribution_de_prospectus_en_boites_aux_lettres' => 'char',
            'distribution_de_publicite' => 'char',
            'societe_de_distribution_de_prospectus' => 'char',
            'tarif_distribution_de_prospectus' => 'char',
            'distribution_prospectus_paris' => 'char',
            'distribution_de_prospectus_paris' => 'char',
            'distribution_de_flyer_paris' => 'char',
            'distribution_de_publicite_paris' => 'char',
            'mot' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_suivi_adwords table
    ******************************/
    public function nouveau($data) {
        return $this->_insert($this->_table, $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
		$this->db->select("*");
		$this->db->where('suivi_adword_id = "'.$id.'"');
		$q = $this->db->get($this->_table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Updating test mails data
    ******************************/
    public function maj($data,$id) {
        return $this->_update($this->_table,$data,$id,'suivi_adword_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete($this->_table,$id,'suivi_adword_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete($this->_table,$id,'suivi_adword_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update($this->_table,$data, $id,'suivi_adword_id');
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
