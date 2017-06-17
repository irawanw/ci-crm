<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_providers extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('providers_id', 'ref', "provider id#", 'providers_id', 'providers_id', 'providers_id'),
                array('provider', 'text', "Provider", 'provider'),
                array('abuse_email', 'text', "Abuse Email", 'abuse_email'),
                array('abuse_telephone', 'text', "Abuse Telephone", 'abuse_telephone'),
                array('abuse_url', 'text', "Abuse Url", 'abuse_url'),
                array('commentaries', 'text', "Commentaries", 'commentaries'),
            ),
            'write' => array(
                'provider'        => array("Provider", 'text', 'provider', false),
                'abuse_email'     => array("Abuse Email", 'textarea', 'abuse_email', false),
                'abuse_telephone' => array("Abuse Telephone", 'textarea', 'abuse_telephone', false),
                'abuse_url'       => array("Abuse Url", 'textarea', 'abuse_url', false),
                'commentaries'    => array("Commentaries", 'textarea', 'commentaries', false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        // première partie du select, mis en cache
        $this->db->start_cache();
        $providers_id = formatte_sql_lien('providers/detail','providers_id','providers_id');

		$this->db->select("*, providers_id as RowID,providers_id as checkbox,provider,abuse_email,abuse_telephone,abuse_url,commentaries");

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
            $this->db->where('providers_id', $id);
        }

		$this->db->stop_cache();
        $table = 't_providers';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->providers_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'providers_id' => 'int',
            'provider' => 'char',
            'abuse_email' => 'char',
            'abuse_telephone' => 'char',
            'abuse_url' => 'char',
            'commentaries' => 'char'
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_providers table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_providers', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
		$this->db->select("*");
		$this->db->where('providers_id = "'.$id.'"');
		$q = $this->db->get('t_providers');
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
        return $this->_update('t_providers',$data,$id,'providers_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_providers',$id,'providers_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_providers',$id,'providers_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_providers',$data, $id,'providers_id');
    }

    public function liste_option()
    {
        $this->db->select('providers_id as id, provider as value');
        $query = $this->db->get('t_providers');

        return $query->result();
    }
}
// EOF
