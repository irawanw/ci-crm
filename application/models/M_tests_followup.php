<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_tests_followup extends MY_Model {
	
	public function __construct() {
		parent::__construct();
	}
	
    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('message_name', 'text', "Message"),
                array('software_name', 'text', "Software","t_tests_followup"),
                array('domain_used_send', 'text', "Domain used sending"),
                array('domain_check_before_send', 'text', "Domain check before sending"),
                array('domain_check_after_send', 'text', "Domain check after sending"),
                array('rbl_blacklists', 'text', "Rbl blacklists"),
                array('lien_desabo', 'text', "Lien desabo"),
                array('provider_name', 'text', "Provider"),
                array('q_day', 'text', "Q day"),
                array('q_hour', 'text', "Q hour"),
                array('database_used', 'text', "Database"),
                array('mail_name', 'text', "Test mail used"),
                array('deliver_before_send', 'text', "Deliver before sending"),
                array('deliver_after_send', 'text', "Deliver after sending"),
                array('number_simultaneaous_mails', 'text', "Number simultaneaous mails"),
                array('email_subject_changed', 'text', "E-mail subject changed every x mails"),                
                array('RowID', 'text', "__DT_Row_ID"),
            ),
            'write' => array(
                'message_id'                 => array("Message", 'select', array('message_id', 'id', 'value'), false),
                'software'                   => array("Software", 'select', array('software','id','value'), false),
                'domain_used_send'           => array("Domain used send", 'text', 'domain_used_send', false),
                'domain_check_before_send'   => array("Domain check before sending", 'select', array('domain_check_before_send', 'id', 'value'), false),
                'domain_check_after_send'    => array("Domain check after sending", 'select', array('domain_check_after_send', 'id', 'value'), false),
                'rbl_blacklists'             => array("Rbl blacklists", 'text', 'rbl_blacklists', false),
                'lien_desabo'                => array("Lien desabo", 'select', array('lien_desabo', 'id', 'value'), false),
                'provider_id'                => array("Provider", 'select', array('provider_id', 'id', 'value'), false),
                'q_day'                      => array("Q day", 'text', 'q_day', false),
                'q_hour'                     => array("Q hour", 'text', 'q_hour', false),
                'database_used'              => array("Database", 'text', 'database_used', false),
                'test_mail_used'             => array("Test mail used", 'select', array('test_mail_used', 'id', 'value'), false),
                'deliver_before_send'        => array("Deliverance before sending", 'select', array('deliver_before_send', 'id', 'value'), false),
                'deliver_after_send'         => array("Deliverance after sending", 'select', array('deliver_after_send', 'id', 'value'), false),
                'number_simultaneaous_mails' => array("Number simultaneaous mails", 'text', 'number_simultaneaous_mails', false),
                'email_subject_changed' => array("E-mail subject changed every x mails", 'text', 'email_subject_changed', false),
            )
        );

        return $champs[$type];
    }

	/******************************
	 * Liste
	 ******************************/
	public function liste($void ,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_tests_followup';
		$this->db->start_cache();
		$message_name   = "t_message_list.name AS message_name";
		$provider_name  = "t_providers.provider AS provider_name";
		$mail_name 		= "t_production_mails.mail AS mail_name";
		$software_name	= "software_nom AS software_name";
		$domain_check_before_send = "if(domain_check_before_send = '0','-',domain_check_before_send) AS domain_check_before_send";
		$domain_check_after_send = "if(domain_check_after_send ='0','-',domain_check_after_send) AS domain_check_after_send";
		$lien_desabo = "if(lien_desabo = '0','-',lien_desabo) AS lien_desabo";
		$deliver_before_send = "if(deliver_before_send = '0','-',deliver_before_send) AS deliver_before_send";
		$deliver_after_send = "if(deliver_after_send = '0','-',deliver_after_send) AS deliver_after_send";
		$this->db->select("followup_id AS RowID,$message_name,message_id,$software_name,domain_used_send,
						$domain_check_before_send,$domain_check_after_send,rbl_blacklists,$lien_desabo,
						$provider_name,provider_id,q_day,q_hour,database_used,$mail_name,test_mail_used,
						$deliver_before_send,$deliver_after_send,number_simultaneaous_mails,email_subject_changed",false);
		$this->db->join('t_message_list','message_id = message_list_id','left');
		$this->db->join('t_providers','provider_id = providers_id','left');
		$this->db->join('t_production_mails','production_mails_id = test_mail_used','left');
		$this->db->join('t_softwares','software_id='.$table.'.software','left');
		//$this->db->where($table.'.inactive is NULL');
		//$this->db->where($table.'.deleted is NULL');
		switch($void){
            case 'archived':
                $this->db->where($table.'.inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.inactive is NULL');
                $this->db->where($table.'.deleted is NULL');
                break;
        }

		$id = intval($void);
		if ($id) {
			$this->db->where('followup_id',$id);
		}

		$this->db->stop_cache();
		
		$aliases = array(
				'message_name'  => 't_message_list.name',
				'provider_name' => 't_providers.provider',
				'mail_name' 	=> 't_production_mails.mail',
				'software_name'	=> 'software_nom',
		);
		$resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
		$this->db->flush_cache();
		
		//add checkbox into data
		for($i=0; $i<count($resultat['data']); $i++){
			$resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
		}
		
		return $resultat;
	}
	
	/******************************
	 * Return filterable columns
	 ******************************/
	public function liste_filterable_columns() {
		$filterable_columns = array(
				'message_id'=>'int',
				'message_name'=>'char',
				'software_name'=>'char',
				'domain_used_send'=>'char',
				'domain_check_before_send'=>'char',
				'domain_check_after_send'=>'char',
				'rbl_blacklists'=>'char',
				'lien_desabo'=>'char',
				'provider_id'=>'int',
				'provider_name'=>'char',
				'q_day' => 'char',
				'q_hour'=>'char',
				'database_used'=>'char',
				'test_mail_used'=>'int',
				'mail_name'=>'char',
				'deliver_before_send'=>'char',
				'deliver_after_send'=>'char',
				'number_simultaneaous_mails' => 'int',
				'email_subject_changed' => 'int'
		);
		return $filterable_columns;
	}
	
	
	/******************************
	 * Nouveau
	 ******************************/
	public function nouveau($data) {
		$check = $this->_insert('t_tests_followup', $data);
		return $check;
	}
	
	/******************************
	 * Détail
	 ******************************/
	public function detail($id) 
	{
		$table 			= 't_tests_followup';
		$message_name   = "t_message_list.name AS message_name";
		$provider_name  = "t_providers.provider AS provider_name";
		$mail_name 		= "t_production_mails.mail AS mail_name";
		$software_name	= $table.".software AS software";
		$domain_check_before_send = "if(domain_check_before_send = '0','-',domain_check_before_send) AS domain_check_before_send";
		$domain_check_after_send = "if(domain_check_after_send ='0','-',domain_check_after_send) AS domain_check_after_send";
		$lien_desabo = "if(lien_desabo = '0','-',lien_desabo) AS lien_desabo";
		$deliver_before_send = "if(deliver_before_send = '0','-',deliver_before_send) AS deliver_before_send";
		$deliver_after_send = "if(deliver_after_send = '0','-',deliver_after_send) AS deliver_after_send";
		$this->db->select("followup_id AS RowID,followup_id,$message_name,message_id,$software_name,domain_used_send,
				$domain_check_before_send,$domain_check_after_send,rbl_blacklists,$lien_desabo,
				$provider_name,provider_id,q_day,q_hour,database_used,$mail_name,test_mail_used,
				$deliver_before_send,$deliver_after_send,number_simultaneaous_mails,email_subject_changed",false);
				$this->db->join('t_message_list','message_id = message_list_id','left');
				$this->db->join('t_providers','provider_id = providers_id','left');
				$this->db->join('t_production_mails','production_mails_id = test_mail_used','left');
		$this->db->where('followup_id',$id);
		$q = $this->db->get($table);
		if ($q->num_rows() > 0) {
			$resultat = $q->row();
			return $resultat;
		}
		else {
			return null;
		}
	}
	
	public function maj($data,$id) 
	{
		return $this->_update('t_tests_followup',$data,$id,'followup_id');
	}

    public function archive($id)
    {
        return $this->_delete('t_tests_followup', $id, 'followup_id', 'inactive');
    }
	
	public function remove($id) 
	{
		return $this->_delete('t_tests_followup',$id,'followup_id','deleted');
	}

	public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_tests_followup',$data, $id,'followup_id');
    }

    public function dupliquer($id)
    {
    	$row = $this->db->get_where('t_tests_followup', array('followup_id' => $id))->row_array();
    	
    	if($row) {
    		unset($row['followup_id']);  
    		return $this->nouveau($row);
    	}

    	return false;
    }
	
	public function liste_option()
	{
		
	}
	
	public function liste_check()
	{
		$options = array(
				"Blacklisted",
				"Not Blacklisted",
				"Spam",
		);
		return $this->form_option($options);
	}
	
	public function liste_lien()
	{
		$options = array(
				"Yes",
				"No",
		);
		return $this->form_option($options);
	}
	
	public function liste_deliverance()
	{
		$options = array(
				"Delivered",
				"Not Delivered",
				"Spam",
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
	
	public function liste_messages()
	{
		$this->db->select('message_list_id as id, name as value');
		$this->db->where('inactive', NULL);
		$this->db->where('deleted', NULL);
		$res = $this->db->get('t_message_list')->result();
		return $res;
	}
	
	public function liste_providers()
	{
		$this->db->select('providers_id as id, provider as value');
		$this->db->where('inactive', NULL);
		$this->db->where('deleted', NULL);
		$res = $this->db->get('t_providers')->result();
		return $res;
	}
	
	public function liste_mails()
	{
		$this->db->select('production_mails_id as id, mail as value');
		$this->db->where('inactive', NULL);
		$this->db->where('deleted', NULL);
		$res = $this->db->get('t_production_mails')->result();
		return $res;
	}
}

// EOF