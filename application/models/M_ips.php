<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_ips extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('ip_id', 'ref', "id#", 'ip_id', 'ip_id', 'ip_id'),
                array('numero', 'text', "Numéro", 'numero'),
                array('serveur_name', 'text', "Serveur", 'serveur_name'),
                array('host_name', 'text', "Hébergeur", 'host_name'),
                array('owner_name', 'text', "Propriétaire", 'owner_name'),
                array('etat', 'text', "État", 'etat'),
                array('utilisation', 'text', "Utilisation", 'utilisation'),
            ),
            'write' => array(
                'numero' => array("Numéro", 'text', 'numero', true),
                'serveur' => array("Serveur", 'select', array('serveur','id','value'), false),
                'host' => array("Hébergeur", 'select', array('host','id', 'value'), false),
                'owner' => array("Proprietaire", 'select', array('owner','id', 'value'), false),
                'etat' => array("État", 'select', array('etat','id','value'), false),
                'utilisation' => array("Utilisation", 'select', array('utilisation','id','value'), false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_ips';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $serveur_name = "t_servers.nom_interne as serveur_name";
        $host_name  = "t_hosts.nom as host_name";
        $owner_name  = "t_owners.nom as owner_name";

		$this->db->select($table.".*,ip_id as RowID, ip_id as checkbox, $host_name, $serveur_name,$owner_name");

        $this->db->join('t_servers', $table.'.serveur= t_servers.server_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
        $this->db->join('t_owners', 't_servers.owner = t_owners.owner_id', 'LEFT');

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
         $this->db->where('ip_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(
            'serveur_name' => 't_servers.nom_interne',
            'host_name' => 't_hosts.nom',
            'owner_name' => 't_owners.nom'
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->ip_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'ip_id' => 'int',
            'numero' => 'char',
            'serveur_name' => 'char',
            'host_name' => 'char',
            'owner_name' => 'char',
            'etat'  => 'char',
            'utilisation' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_ips table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_ips', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
        $table = "t_ips";
        $serveur_name  = "ts.nom_interne as serveur_name";
        $host_name  = "t_hosts.nom as host_name";

		$this->db->select("*,t_ips.utilisation as utilisation, $host_name, $serveur_name,ts.owner,ts.host");
		$this->db->where('ip_id = "'.$id.'"');
        $this->db->join('t_servers ts', $table.'.serveur= ts.server_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
		
        $q = $this->db->get($table);
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
        return $this->_update('t_ips',$data,$id,'ip_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_ips',$id,'ip_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_ips',$id,'ip_id','deleted');
    }
    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_ips',$data, $id,'ip_id');
    }

    public function liste_option($with_ajouter = false)
    {
        $query = $this->db->select('ip_id as id, numero as value')
							->where('inactive IS NULL AND deleted is NULL')
							->order_by('numero')
							->get('t_ips');
        
        $ajouter = new stdClass();
        $ajouter->id = "ajouter";
        $ajouter->value = "Ajouter";

        if($with_ajouter) {
            $ajouter = new stdClass();
            $ajouter->id = "ajouter";
            $ajouter->value = "Ajouter";

            if($query->num_rows() > 0) {
                $data = $query->result();
                array_unshift($data, $ajouter);
            } else {
                $data[] = $ajouter;
            }
        } else {
            $data = $query->result();
        }

        return $data;
    }

    public function etat_liste_option()
    {
        $options = array(
            'propre et rodée',
            'propre et en rodage',
            'blacklistée å la commande',
            'blacklistée suite usage intensif',
        );

        return $this->form_option($options);
    }

    public function utilisation_liste_option()
    {
        $options = array(
            'serveur physique',
            'serveur virtuel',
            'serveur virtuel avec rotation',
        );

        return $this->form_option($options);
    }

    public function get_host($serveurId)
    {
        $query = $this->db->select('host_id, nom as host_name')
                          ->join('t_servers as ts', 'th.host_id=ts.host')
                          ->where('ts.server_id', $serveurId)
                          ->get('t_hosts as th');

        if($query->row())
        {   
            $row = $query->row();
            return array(
                'status' => TRUE,
                'id' => $row->host_id,
                'value' => $row->host_name
            );
        } else {
            return array(
                'status' => FALSE
            );
        }
    }

    public function get_owner($serveurId)
    {
        $query = $this->db->select('owner_id')
                          ->join('t_servers as ts', 'to.owner_id=ts.owner')
                          ->where('ts.server_id', $serveurId)
                          ->get('t_owners as to');

        if($query->row())
        {   
            $row = $query->row();
            return array(
                'status' => TRUE,
                'id' => $row->owner_id,
            );
        } else {
            return array(
                'status' => FALSE
            );
        }
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
