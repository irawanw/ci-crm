<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_servers extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('server_id', 'ref', "id#", 'server_id', 'server_id', 'server_id'),
                array('host_name', 'text', "Hébergeur", 'host_name'),
                array('nom_interne', 'text', "Nom Interne", 'nom_interne'),
                array('numero_de_client', 'text', "Numéro de client ou compte", 'numero_de_client'),
                array('owner_name', 'text', "Propriétaire", 'owner_name'),
                array('contrat', 'text', "Contrat", 'contrat'),
                array('options', 'text', "Options", 'options'),
                array('system_exploration', 'text', "Système d'exploitation", 'system_exploration'),
                array('type_serveur', 'text', "Type Serveur", 'type_serveur'),
                array('utilisation', 'text', "Utilisation", 'utilisation'),
                array('remarques', 'text', "Remarques", 'remarques'),

                array('acces_plesk_url', 'text', "plesk url", 'acces_plesk_url'),
                array('acces_plesk_login', 'text', "plesk login", 'acces_plesk_login'),
                array('acces_plesk_pass', 'text', "plesk pass", 'acces_plesk_pass'),
                array('acces_plesk_utilisateurs_name', 'text', "plesk utilisateurs", 'acces_plesk_utilisateurs_name'),

                array('acces_compte_client_url', 'text', "compte client url", 'acces_compte_client_url'),
                array('acces_compte_client_login', 'text', "compte client login", 'acces_compte_client_login'),
                array('acces_compte_client_pass', 'text', "compte client pass", 'acces_compte_client_pass'),
                array('acces_compte_client_utilisateurs_name', 'text', "compte client utilisateurs", 'acces_compte_client_utilisateurs_name'),

                array('acces_contrat_url', 'text', "contrat url", 'acces_contrat_url'),
                array('acces_contrat_login', 'text', "contrat login", 'acces_contrat_login'),
                array('acces_contrat_pass', 'text', "contrat pass", 'acces_contrat_pass'),
                array('acces_contrat_utilisateurs_name', 'text', "contrat utilisateurs", 'acces_contrat_utilisateurs_name'),

                array('acces_root_url', 'text', "root url", 'acces_root_url'),
                array('acces_root_login', 'text', "root login", 'acces_root_login'),
                array('acces_root_pass', 'text', "root pass", 'acces_root_pass'),
                array('acces_root_utilisateurs_name', 'text', "root utilisateurs", 'acces_root_utilisateurs_name'),

                array('prix', 'text', "Prix", 'prix'),
                array('type_de_paiement', 'text', "Type de paiement", 'type_de_paiement'),
                array('echeance_du_paiement', 'date', "Echéance du paiement", 'echeance_du_paiement'),
                array('date_de_resiliation', 'date', "Date de résiliation", 'date_de_resiliation'),
                array('moyen_de_paiement', 'text', "Moyen De Paiement", 'moyen_de_paiement'),
                array('compte_paypal_utilise','text',"Compte paypal utilisé",'compte_paypal_utilise'),
                array('cb_utilsée', 'text', "CB Utilsée", 'cb_utilsée'),
                array('ips_numero', 'text', "Ips", 'ips_numero'),
                array('domaines_name', 'text', "Domaines ", 'domaines_name'),
                array('eta_du_serveur', 'text', "Eta du Serveur", 'eta_du_serveur'),
            ),
            'write' => array(
                'svrid'                            => array("svrid", 'hidden', 'svrid', false),
                'host'                             => array("Hébergeur", 'select', array('host', 'id', 'value'), false),
                'nom_interne'                      => array("Nom Interne", 'text', 'nom_interne', true),
                'numero_de_client'                 => array("Numéro de client ou compte", 'text', 'numero_de_client', false),
                'owner'                            => array("Propriétaire", 'select', array('owner', 'id', 'value'), false),
                'contrat'                          => array("Contrat", 'text', 'contrat', false),
                'options'                          => array("Options", 'text', 'options', false),
                'system_exploration'               => array("Système d'exploitation", 'select', array('system_exploration', 'id', 'value'), false),
                'type_serveur'                     => array("Type Serveur", 'text', 'type_serveur', false),
                'utilisation'                      => array("Utilisation", 'select-multiple', array('utilisation','id','value'), false),
                'remarques'                        => array("Remarques", 'textarea', 'remarques', false),

                'acces_plesk_url'                  => array("Url", 'text', 'acces_plesk_url', false),
                'acces_plesk_login'                => array("Login", 'text', 'acces_plesk_login', false),
                'acces_plesk_pass'                 => array("Pass", 'text', 'acces_plesk_pass', false),
                'acces_plesk_utilisateurs'         => array("utilisateurs agrees ", 'select', array('acces_plesk_utilisateurs', 'utl_id', 'emp_nom'), false),

                'acces_compte_client_url'          => array("Url", 'text', 'acces_compte_client_url', false),
                'acces_compte_client_login'        => array("Login", 'text', 'acces_compte_client_login', false),
                'acces_compte_client_pass'         => array("Pass", 'text', 'acces_compte_client_pass', false),
                'acces_compte_client_utilisateurs' => array("utilisateurs agrees ", 'select', array('acces_compte_client_utilisateurs', 'utl_id', 'emp_nom'), false),

                'acces_contrat_url'                => array("Url", 'text', 'acces_contrat_url', false),
                'acces_contrat_login'              => array("Login", 'text', 'acces_contrat_login', false),
                'acces_contrat_pass'               => array("Pass", 'text', 'acces_contrat_pass', false),
                'acces_contrat_utilisateurs'       => array("utilisateurs agrees ", 'select', array('acces_contrat_utilisateurs', 'utl_id', 'emp_nom'), false),

                'acces_root_url'                   => array("Url", 'text', 'acces_root_url', false),
                'acces_root_login'                 => array("Login", 'text', 'acces_root_login', false),
                'acces_root_pass'                  => array("Pass", 'text', 'acces_root_pass', false),
                'acces_root_utilisateurs'          => array("utilisateurs agrees ", 'select', array('acces_root_utilisateurs', 'utl_id', 'emp_nom'), false),

                'prix'                             => array("Prix", 'text', 'prix', false),
                'type_de_paiement'                 => array("Type de paiement", 'select', array('type_de_paiement', 'id', 'value'), false),
                'echeance_du_paiement'             => array("Echéance du paiement", 'date', 'echeance_du_paiement', false),
                'date_de_resiliation'              => array("Date de résiliation", 'date', 'date_de_resiliation', false),
                'pas_engage'                       => array("Pas engagé", 'checkbox-h', 'pas_engage', false),
                'moyen_de_paiement'                => array("Moyen De Paiement", 'select', array('moyen_de_paiement', 'id', 'value'), false),
                'compte_paypal_utilise'            => array("Compte paypal utilisé", 'text', 'compte_paypal_utilise', false),
                'cb_utilsée'                       => array("CB Utilsée", 'select', array('cb_utilsée', 'id', 'value'), false),
                'ips'                              => array("Ajouter des IPs", 'select-multiple', array('ips', 'id', 'value'), false),
                'domaines'                         => array("Ajouter des domaines ", 'select-multiple', array('domaines', 'id', 'value'), false),
                'ajouter_des_sites_hébergés'       => array("Ajouter des Sites Hébergés", 'text', 'ajouter_des_sites_hébergés', false),
                'eta_du_serveur'                   => array("État du serveur", 'select', array('eta_du_serveur', 'id', 'value'), false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($id,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
		/*
        $table = 't_servers';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $echeance_du_paiement = formatte_sql_date("echeance_du_paiement");
        $date_de_resiliation = formatte_sql_date("date_de_resiliation");
        $host_name  = "t_hosts.nom as host_name";
        $owner_name = "t_owners.nom as owner_name";
        //$ips_numero = "t_ips.numero as ips_numero";
        //$domaines_name = "t_domains.nom as domaines_name";
        $acces_plesk_utilisateurs_name = "te1.emp_nom as acces_plesk_utilisateurs_name";
        $acces_compte_client_utilisateurs_name = "te2.emp_nom as acces_compte_client_utilisateurs_name";
        $acces_contrat_utilisateurs_name = "te3.emp_nom as acces_contrat_utilisateurs_name";
        $acces_root_utilisateurs_name = "te4.emp_nom as acces_root_utilisateurs_name";
        $ips_numero       = "GROUP_CONCAT(t_ips.numero) as ips_numero";
        $domaines_name    = "GROUP_CONCAT(t_domains.nom) as domaines_name";

		$this->db->select($table.".*, server_id as RowID,
            server_id as checkbox,
            echeance_du_paiement,
            date_de_resiliation,
            $host_name,
            $owner_name,
            $ips_numero,
            $domaines_name,
            $acces_plesk_utilisateurs_name,
            $acces_compte_client_utilisateurs_name,
            $acces_contrat_utilisateurs_name,
            $acces_root_utilisateurs_name,
        ");

        $this->db->join('t_owners', $table.'.owner = t_owners.owner_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
        //$this->db->join('t_ips', $table.'.ips=t_ips.ip_id', 'LEFT');
        //$this->db->join('t_domains', $table.'.domaines=t_domains.domain_id', 'LEFT');
        $this->db->join('t_ips', 'FIND_IN_SET(t_ips.ip_id, ips)', 'left');
        $this->db->join('t_domains', 'FIND_IN_SET(t_domains.domain_id, domaines)', 'left');
        
        $this->db->join('t_utilisateurs as tu1', $table.'.acces_plesk_utilisateurs = tu1.utl_id', 'left');
        $this->db->join('t_employes as te1', 'te1.emp_id = tu1.utl_id', 'left');
        
        $this->db->join('t_utilisateurs as tu2', $table.'.acces_compte_client_utilisateurs = tu2.utl_id', 'left');
        $this->db->join('t_employes as te2', 'te2.emp_id = tu2.utl_id', 'left');

        $this->db->join('t_utilisateurs as tu3', $table.'.acces_contrat_utilisateurs = tu3.utl_id', 'left');
        $this->db->join('t_employes as te3', 'te3.emp_id = tu3.utl_id', 'left');

        $this->db->join('t_utilisateurs as tu4', $table.'.acces_root_utilisateurs = tu4.utl_id', 'left');
        $this->db->join('t_employes as te4', 'te4.emp_id = tu4.utl_id', 'left');

        $this->db->group_by('server_id');

        if($filters == null)
        {
        	$this->db->where($table.'.eta_du_serveur = "en cours"');
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
         $this->db->where('server_id', $id);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(
            'host_name' => 't_hosts.nom',
            'owner_name' => 't_owners.nom',
            'ips_numero' => 't_ips.numero',
            'domaines_name' => 't_domains.nom',
        );
		
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();	
		
        for($i=0; $i<count($resultat['data']); $i++)
		{
			//add checkbox into data
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->server_id.'">';
        }
		*/
		$this->db->start_cache();
		$this->db->select('*');
		$this->db->stop_cache();
		$table = " ( SELECT `t_servers`.*, `server_id` as `RowID`, `server_id` as `checkbox`, 
			 `t_hosts`.`nom` as `host_name`, `t_owners`.`nom` as `owner_name`,
			(select GROUP_CONCAT(numero) from t_ips where find_in_set(ip_id,ips)) as ips_numero,
			(select GROUP_CONCAT(nom) from t_domains where find_in_set(domain_id,domaines)) as domaines_name,
			`te1`.`emp_nom` as `acces_plesk_utilisateurs_name`, `te2`.`emp_nom` as `acces_compte_client_utilisateurs_name`, `te3`.`emp_nom` as `acces_contrat_utilisateurs_name`, `te4`.`emp_nom` as `acces_root_utilisateurs_name`
			FROM `t_servers`
			LEFT JOIN `t_owners` ON `t_servers`.`owner` = `t_owners`.`owner_id`
			LEFT JOIN `t_hosts` ON `t_servers`.`host` = `t_hosts`.`host_id`
			LEFT JOIN `t_utilisateurs` as `tu1` ON `t_servers`.`acces_plesk_utilisateurs` = `tu1`.`utl_id`
			LEFT JOIN `t_employes` as `te1` ON `te1`.`emp_id` = `tu1`.`utl_id`
			LEFT JOIN `t_utilisateurs` as `tu2` ON `t_servers`.`acces_compte_client_utilisateurs` = `tu2`.`utl_id`
			LEFT JOIN `t_employes` as `te2` ON `te2`.`emp_id` = `tu2`.`utl_id`
			LEFT JOIN `t_utilisateurs` as `tu3` ON `t_servers`.`acces_contrat_utilisateurs` = `tu3`.`utl_id`
			LEFT JOIN `t_employes` as `te3` ON `te3`.`emp_id` = `tu3`.`utl_id`
			LEFT JOIN `t_utilisateurs` as `tu4` ON `t_servers`.`acces_root_utilisateurs` = `tu4`.`utl_id`
			LEFT JOIN `t_employes` as `te4` ON `te4`.`emp_id` = `tu4`.`utl_id`
			WHERE
			`t_servers`.`inactive` is NULL
			AND `t_servers`.`deleted` is NULL";
			
			if($filters == null)
			{
				$table  .= ' AND t_servers.eta_du_serveur = "en cours"';
			}

			if ($id > 0) {
				$table  .= ' AND server_id = '.$id;
			}
				$table  .= ' group by server_id )';

			$aliases = array(
						'host_name' => 't_hosts.nom',
						'owner_name' => 't_owners.nom',
						'ips_numero' => 't_ips.numero',
						'domaines_name' => 't_domains.nom',
				);				
			$resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering,true);
			$this->db->flush_cache();
			//add checkbox into data
			for($i=0; $i<count($resultat['data']); $i++)
			{
				$resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->server_id.'">';
			
				if($resultat['data'][$i]->ips_numero != null)
				{
					$ShowData 	= 5; // set limit number show of data liste
					$numero 	= explode(",",$resultat['data'][$i]->ips_numero);
					$cnt 		= count($numero);
					$data 		= ''; //null set :)
					if ($cnt > $ShowData)
					{
						for($x=0;$x<$cnt;$x++)
						{
							if($x < $ShowData)
							{
								if($x == 0)
								{
									$data .= '<div class="show" id="show_ips_numero_'.$resultat['data'][$i]->RowID.'">'.$numero[$x].'<br/>';
								}
								elseif($x+1 == $ShowData) //X=4 --- ShowData=4
								{
									$data .= $numero[$x].'</div>';
								}
								else
								{
									$data .= $numero[$x].'<br/>';
								}
							}
							else //x == 5
							{
								if($x == $ShowData)
								{
									$data .= '<div class="hide" id="hide_ips_numero_'.$resultat['data'][$i]->RowID.'">'.$numero[$x].'<br/>';
								}
								else
								{
									if($x == $cnt-1)
									{
										//debug($x.'/elseif'.$ShowData,0);
										$data .= $numero[$x].'</div>';
									}
									else
									{
										//debug($x.'/else '.$ShowData,0);
										$data .= $numero[$x].'<br/>';
									}
								}
								
							}
						}
						$button = '<div><input type="button" id="btn_ips_numero_'.$resultat['data'][$i]->RowID.'" class="btn btn-xs btn-success btn-block btn_ips_numero" name="btn-expand" id="" value="Elargir la liste"></div>';
						$resultat['data'][$i]->ips_numero = $data.$button;
					}
					else
					{
						$data .= '<div class="show">'.formatte_texte_down($resultat['data'][$i]->ips_numero).'</div>';
						$resultat['data'][$i]->ips_numero = $data;
					}
				}
				
				if($resultat['data'][$i]->domaines_name != null)
				{
					$ShowData 	= 5; // set limit number show of data liste
					$numero 	= explode(",",$resultat['data'][$i]->domaines_name);
					$cnt 		= count($numero);
					$data 		= ''; //null set :)
					$x			= 0;
					if ($cnt > $ShowData)
					{
						for($x=0;$x<$cnt;$x++)
						{
							if($x <= $ShowData)
							{
								if($x == 0)
								{
									$data .= '<div class="show" id="show_domaines_name_'.$resultat['data'][$i]->RowID.'">'.$numero[$x].'<br/>';
								}
								elseif($x > 0 && $x < $ShowData)
								{
									$data .= $numero[$x].'<br/>';
								}
								else
								{
									$data .= '</div>';
								}
							}
							else
							{
								if($x < $cnt && $x == $ShowData+1)
								{
									$data .= '<div class="hide" id="hide_domaines_name_'.$resultat['data'][$i]->RowID.'">'.$numero[$x].'<br/>';
								}
								elseif($x == $cnt-1)
								{
									$data .= $numero[$x].'</div>';
								}
								else
								{
									$data .= $numero[$x].'<br/>';
								}
								
							}
						}
						$button = '<input type="button" id="btn_domaines_name_'.$resultat['data'][$i]->RowID.'" class="btn btn-xs btn-success btn-block btn_domaines_name" name="btn-expand" id="" value="Elargir la liste">';
						$resultat['data'][$i]->domaines_name = $data.$button;
					}
					else
					{
						$data .= '<div class="show">'.formatte_texte_down($resultat['data'][$i]->domaines_name).'</div>';
						$resultat['data'][$i]->domaines_name = $data;
					}
				}
			}
        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'server_id' => 'int',
            'host_name' => 'char',
            'nom_interne' => 'char',
            'numero_de_client' => 'char',
            'owner_name' => 'char',
            'contrat' => 'char',
            'options' => 'char',
            'system_exploration' => 'char',
            'type_serveur' => 'char',
            'utilisation' => 'char',
            'remarques' => 'char',

			'acces_compte_client_url' => 'char',
			'acces_compte_client_login' => 'char',
			'acces_compte_client_pass' => 'char',
			'acces_compte_client_utilisateurs' => 'char',
			
			'acces_plesk_url' => 'char',
			'acces_plesk_login' => 'char',
			'acces_plesk_pass' => 'char',
			'acces_plesk_utilisateurs' => 'char',

			'acces_root_url' => 'char',
			'acces_root_login' => 'char',
			'acces_root_pass' => 'char',
			'acces_root_utilisateurs' => 'char',

			'acces_contrat_url' => 'char',
			'acces_contrat_login' => 'char',
			'acces_contrat_pass' => 'char',
			'acces_contrat_utilisateurs' => 'char',			
		
            'prix' => 'int',
            'type_de_paiement' => 'char',
            'echeance_du_paiement' => 'date',
            'date_de_resiliation' => 'date',
            'moyen_de_paiement' => 'char',
            'cb_utilsée' => 'char',
            'ips_numero' => 'char',
            'domaines_name' => 'char',
            'ajouter_des_sites_hébergés' => 'char',
            'eta_du_serveur' => 'char',
        );

        return $filterable_columns;
    }

    /******************************
    * New Message list insert into t_servers table
    ******************************/
    public function nouveau($data) {
        $check = $this->_insert('t_servers', $data);
		//$id = $this->db->insert_id();
		//$this->session->set_userdata('isSave',$id);
		return $check;
    }

    /******************************
    * Detail d'une test mails
    ******************************/
    public function detail($id) {
        $table = 't_servers as ts';
        $host_name  = "t_hosts.nom as host_name";
        $owner_name = "t_owners.nom as owner_name";
        $ips_numero = "t_ips.numero as ips_numero";
        $domaines_name = "t_domains.nom as domaines_name";

        $acces_plesk_utilisateurs_name = "te1.emp_nom as acces_plesk_utilisateurs_name";
        $acces_compte_client_utilisateurs_name = "te2.emp_nom as acces_compte_client_utilisateurs_name";
        $acces_contrat_utilisateurs_name = "te3.emp_nom as acces_contrat_utilisateurs_name";
        $acces_root_utilisateurs_name = "te4.emp_nom as acces_root_utilisateurs_name";

		$this->db->select("*, ts.host as host,ts.owner as owner, ts.contrat as contrat,ts.utilisation as utilisation,
                $host_name, 
                $owner_name,
                $ips_numero,
                $domaines_name,
                $acces_plesk_utilisateurs_name,
                $acces_compte_client_utilisateurs_name,
                $acces_contrat_utilisateurs_name,
                $acces_root_utilisateurs_name,
        ");
        $this->db->join('t_owners', $table.'.owner = t_owners.owner_id', 'LEFT');
        $this->db->join('t_hosts', $table.'.host = t_hosts.host_id', 'LEFT');
        $this->db->join('t_ips', $table.'.ips=t_ips.ip_id', 'LEFT');
        $this->db->join('t_domains', $table.'.domaines=t_domains.domain_id', 'LEFT');

        $this->db->join('t_utilisateurs as tu1', $table.'.acces_plesk_utilisateurs = tu1.utl_id', 'left');
        $this->db->join('t_employes as te1', 'te1.emp_id = tu1.utl_id', 'left');
        
        $this->db->join('t_utilisateurs as tu2', $table.'.acces_compte_client_utilisateurs = tu2.utl_id', 'left');
        $this->db->join('t_employes as te2', 'te2.emp_id = tu2.utl_id', 'left');

        $this->db->join('t_utilisateurs as tu3', $table.'.acces_contrat_utilisateurs = tu3.utl_id', 'left');
        $this->db->join('t_employes as te3', 'te3.emp_id = tu3.utl_id', 'left');

        $this->db->join('t_utilisateurs as tu4', $table.'.acces_root_utilisateurs = tu4.utl_id', 'left');
        $this->db->join('t_employes as te4', 'te4.emp_id = tu4.utl_id', 'left');
        
		$this->db->where('server_id = "'.$id.'"');
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
        return $this->_update('t_servers',$data,$id,'server_id');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_servers',$id,'server_id','inactive');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_servers',$id,'server_id','deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_servers',$data, $id,'server_id');
    }

    public function liste_host()
    {
        return $this->db->select('host_id as id,nom as value')->order_by('nom', 'ASC')->get('t_hosts')->result();
    }

    public function liste_option()
    {
        $table = "t_servers";
		$query = $this->db->select('server_id as id, nom_interne as value')->get($table);
		$ajouter = new stdClass();
        $ajouter->id = "ajouter";
        $ajouter->value = "Ajouter";
		if($query->num_rows() > 0)
		{
			$data = $query->result();
			array_unshift($data, $ajouter);
			
		} 
		else 
		{
			$data[] = $ajouter;
		}
        return $data;
    }

    public function moyen_de_paiement_liste_option()
    {
        $options = array('CB', 'prel', 'manuel', 'cheque','paypal CB');
        return $this->form_option($options);
    }

    public function system_exploration_liste_option()
    {
        $options = array(
            'Linux',
            'Windows',
            'Ubuntu Server 14.04 (64 bits)',
            'Debian 8 (64 bits)',
            'Centos 6 (64 bits)',
        );

        return $this->form_option($options);
    }

    public function type_de_paiement_liste_option()
    {
        $options = array('Automatique', 'Manuel');
        return $this->form_option($options);
    }

    public function eta_du_serveur_liste_option()
    {
        $options = array(
            'en cours',
            'résilié',
            'à résilier',
            'en cours de résiliation',
            'bloqué impayé',
            'bloqué spam',
        );

        return $this->form_option($options);
    }

    public function utilisation_liste()
    {
        $options = array(
            'e-mailing',
            'backup',
            'sites clients',
            'sites internes',
            'sites internes de production',
            'domaines',
            'domaines redirection e-mailing',
            'développement',
            'autres'
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
