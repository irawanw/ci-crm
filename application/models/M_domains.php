<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_domains extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('domain_id', 'ref', "id#", 'domain_id', 'domain_id', 'domain_id'),
                array('domain_name', 'text', "Nom", 'domain_name'),
                array('owner_name', 'text', "Propriétaire", 'owner_name'),
                array('host_name', 'text', "Hébergeur", 'host_name'),
                array('compte', 'text', "Compte", 'compte'),
                array('contrat', 'text', "Contrat", 'contrat'),
                //array('identifiant', 'text', "Identifiant", 'identifiant'),
                array('site', 'text', "Site", 'site'),
                array('utilisation', 'text', "Utilisation ", 'utilisation'),
                array('acces_contrat_url', 'text', "Contrat Url ", 'acces_contrat_url'),
                array('acces_contrat_login', 'text', "Contrat Login", 'acces_contrat_login'),
                array('acces_contrat_pass', 'text', "Contrat Pass", 'acces_contrat_pass'),
                array('acces_contrat_utilisateurs_name', 'text', "Contrat Utilisateurs", 'acces_contrat_utilisateurs_name'),
                array('etat', 'text', "État", 'etat'),
            ),
            'write' => array(
                'nom'                        => array("Nom", 'text', 'nom', false),
                'server'                     => array("Serveurs", 'select', array('server', 'id', 'value'), false),
                'owner'                      => array("Propriétaire", 'select', array('owner', 'id', 'value'), false),
                'host'                       => array("Hébergeur", 'select', array('host', 'id', 'value'), false),
                'compte'                     => array("Compte", 'select', array('compte', 'id', 'value'), false),
                'contrat'                    => array("Contrat", 'select', array('contrat', 'id', 'value'), false),
                //'identifiant' => array("Identifiant", 'text', 'identifiant', false),
                'site'                       => array("Site", 'text', 'site', false),
                'utilisation'                => array("Utilisation ", 'select-multiple', array('utilisation', 'id', 'value'), false),

                'acces_contrat_url'          => array("Url", 'text', 'acces_contrat_url', false),
                'acces_contrat_login'        => array("Login", 'text', 'acces_contrat_login', false),
                'acces_contrat_pass'         => array("Pass", 'text', 'acces_contrat_pass', false),
                'acces_contrat_utilisateurs' => array("utilisateurs agrees ", 'text', 'acces_contrat_utilisateurs', false),
                'etat'                       => array("État", 'select', array('etat', 'id', 'value'), false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void ,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_domains';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $domain_name = $table.".nom as domain_name";
        $owner_name = "t_owners.nom as owner_name";
        $host_name  = "t_hosts.nom as host_name";
        $acces_contrat_url = "t_servers.acces_contrat_url";
        $acces_contrat_login = "t_servers.acces_contrat_login";
        $acces_contrat_pass = "t_servers.acces_contrat_pass";
        $acces_contrat_utilisateurs_name = "t_employes.emp_nom as acces_contrat_utilisateurs_name";

		$this->db->select($table.".*, domain_id as RowID,
            domain_id as checkbox, 
            $domain_name,
            $owner_name, 
            $host_name,
            $acces_contrat_url,
            $acces_contrat_login,
            $acces_contrat_pass,
            $acces_contrat_utilisateurs_name
        ");

        $this->db->join('t_owners', $table.'.owner = t_owners.owner_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
        $this->db->join('t_servers', $table.'.contrat = t_servers.contrat', 'LEFT');
        $this->db->join('t_utilisateurs', 't_servers.acces_contrat_utilisateurs = t_utilisateurs.utl_id', 'left');
        $this->db->join('t_employes', 't_employes.emp_id = t_utilisateurs.utl_id', 'left');
        $this->db->group_by('domain_id');

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
         $this->db->where('domain_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(
            'domain_name' => $table.'.nom',
            'owner_name' => 't_owners.nom',
            'host_name' => 't_hosts.nom',
            'acces_contrat_utilisateurs_name' => 't_employes.emp_nom'
        );
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
		$this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->domain_id.'">';
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'domain_id' => 'int',
            'domain_name' => 'char',
            'owner_name' => 'char',
            'host_name' => 'char',
            'compte' => 'char',
            'contrat' => 'char',
            //'identifiant' => 'char',
            'site' => 'char',
            'utilisation' => 'char',
            'acces_contrat_url' => 'char',
            'acces_contrat_login' => 'char',
            'acces_contrat_pass' => 'char',
            'acces_contrat_utilisateurs_name' => 'char',
			'etat' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_domains table
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_domains', $data);
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
        $table = "t_domains";
		$owner_name = "t_owners.nom as owner_name";
        $host_name  = "t_hosts.nom as host_name";
        $acces_contrat_url = "t_servers.acces_contrat_url";
        $acces_contrat_login = "t_servers.acces_contrat_login";
        $acces_contrat_pass = "t_servers.acces_contrat_pass";
        $acces_contrat_utilisateurs_name = "t_employes.emp_nom as acces_contrat_utilisateurs_name";

        $this->db->select($table.".*, 
            domain_id as checkbox, 
            $owner_name, 
            $host_name,
            $acces_contrat_url,
            $acces_contrat_login,
            $acces_contrat_pass,
            $acces_contrat_utilisateurs_name
        ");
        $this->db->join('t_owners', $table.'.owner = t_owners.owner_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
        $this->db->join('t_servers', $table.'.contrat = t_servers.contrat', 'LEFT');
        $this->db->join('t_utilisateurs', 't_servers.acces_contrat_utilisateurs = t_utilisateurs.utl_id', 'left');
        $this->db->join('t_employes', 't_employes.emp_id = t_utilisateurs.utl_id', 'left');
        $this->db->where('domain_id = "'.$id.'"');
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
        return $this->_update('t_domains',$data,$id,'domain_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_domains',$id,'domain_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_domains',$id,'domain_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_domains',$data, $id,'domain_id');
    }

    public function liste_option($with_ajouter = false)
    {
        $query = $this->db->select('domain_id as id, nom as value')
							->where('inactive IS NULL AND deleted is NULL')
							->order_by('nom', 'DESC')
							->get('t_domains');
        
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

    /**
     * Get utilisation list
     * @return [type] [description]
     */
    public function utilisation_liste_option()
    {
        $options = array(
            'autres',
            'e-mailing',
            'progiciel interne',
            'redirection e-mailing',
            'site internet client',
            'site internet interne',                    
        );

        return $this->form_option($options);
    }

	public function host_liste_option_by_server($server_id)
    {
        $this->db->select('host_id as id, th.nom as value');
		$this->db->from('t_servers ts');
		$this->db->join('t_hosts th','ts.host = th.host_id');
		$this->db->where('ts.server_id', $server_id);
		$sql = $this->db->get();
        return $sql->result();
    }

    public function owner_liste_option_by_server($server_id)
    {
        $this->db->select('owner_id as id, th.nom as value');
        $this->db->from('t_servers ts');
        $this->db->join('t_owners th','ts.owner = th.owner_id');
        $this->db->where('ts.server_id', $server_id);
        $sql = $this->db->get();
        return $sql->result();
    }
	
	public function compte_liste_option_by_server($server_id)
    {
        $query = $this->db->select('numero_de_client as id, numero_de_client as value')
                          ->where('server_id', $server_id)
                          ->get('t_servers');

        return $query->result();
    }
	
	public function contrat_liste_option_by_server($server_id)
    {
        $query = $this->db->select('contrat as id, contrat as value')
                          ->where('server_id', $server_id)
                          ->get('t_servers');
        return $query->result();
	}
	
    public function compte_liste_option_by_host($host_id)
    {
        $query = $this->db->select('numero_de_client as id, numero_de_client as value')
                          ->where('host', $host_id)
                          ->get('t_servers');

        return $query->result();
    }

    public function contrat_liste_option($owner_id, $host_id, $compte)
    {
        $query = $this->db->select('contrat as id, contrat as value')
                          ->where('owner', $owner_id)
                          ->where('host', $host_id)
                          ->where('numero_de_client', $compte)
                          ->get('t_servers');

        return $query->result();
    }

    public function get_contrat_detail($owner_id, $host_id, $compte, $contrat)
    {
        $query = $this->db->select('acces_contrat_url as url, acces_contrat_login as login, acces_contrat_pass as pass, t_employes.emp_nom as utilisateurs')
                          ->join('t_utilisateurs', 't_servers.acces_contrat_utilisateurs = t_utilisateurs.utl_id', 'left')
                          ->join('t_employes', 't_employes.emp_id = t_utilisateurs.utl_id', 'left')
                          ->where('owner', $owner_id)
                          ->where('host', $host_id)
                          ->where('numero_de_client', $compte)
                          ->where('contrat', $contrat)
                          ->get('t_servers');

        if($query->row())
        {
            $result = $query->row();
            return array(
                'url' => $result->url,
                'login' => $result->login,
                'pass' => $result->pass,
                'utilisateurs' => $result->utilisateurs
            );
        } else {
            return array(
                'url' => "",
                'login' => "",
                'pass' => "",
                'utilisateurs' => ""
            );
        }
    }

    //generate liste option for dropdown
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
