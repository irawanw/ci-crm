<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_openemm_update extends MY_Model {

    public function __construct() {
        parent::__construct();
	}
	
	public function refresh(){
		$openemm_db = $this->load->database('openemm', true); // the TRUE paramater tells CI that you'd like to return the database object.

        $query      = $openemm_db
						->select('mailing_id as id, shortname as openemm_name')
						->get('mailing_tbl');
        $data = $query->result();

		foreach($data as $row){
			$openemm = $this->get_mailing_detail($row->id);			
					
			$data = new stdClass;
			$data->openemm_id			= $row->id;
			$data->total 				= $openemm['openemm_total'];
			$data->current 				= $openemm['openemm_current'];
			$data->number_of_open 		= $openemm['openemm_opened'];
			$data->open_rate 			= $openemm['openemm_open_rate'];
			$data->number_of_click 		= $openemm['openemm_number_of_click'];
			$data->click_rate 			= $openemm['openemm_click_rate'];
			$data->bounce 				= 0;
			$data->bounce_rate 			= 0;
			$data->hard_bounce_rate		= 0;
			$data->soft_bounce_rate		= 0;
			$data->bounce 				= $openemm['openemm_bounce'];
			$data->bounce_rate 			= $openemm['openemm_bounce_rate'];
			$data->hard_bounce_rate		= $openemm['openemm_hard_bounce_rate'];
			$data->soft_bounce_rate		= $openemm['openemm_soft_bounce_rate'];
			
			$res = $this->db->query("SELECT COUNT(*) as count 
										FROM t_openemm_stats WHERE openemm_id = '".$row->id."'")->result();
			
			if($res[0]->count==0)
				$this->_insert('t_openemm_stats', $data);
			else
				$this->_update('t_openemm_stats', $data, $row->id, 'openemm_id');
		}
	}
	
	/***************************/
	/*  OpenEMM Integration    */
	/***************************/
    public function get_mailing()
    {
        $openemm_db = $this->load->database('openemm', true); // the TRUE paramater tells CI that you'd like to return the database object.
        $query      = $openemm_db
            ->select('mailing_id as id, shortname as value')
            ->where("deleted != 1")
            ->get('mailing_tbl')->result();
        return $query;
    }	
	
    public function get_mailing_detail($id)
    {
        $openemm_db = $this->load->database('openemm', true); // the TRUE paramater tells CI that you'd like to return the database object.

        $query      = $openemm_db
						->select('mailing_id as id, shortname as openemm_name')
						->where("mailing_id = $id")
						->get('mailing_tbl');
        $data = $query->result();
        if (count($data)) {
            $result['openemm_name']    = $data[0]->openemm_name;
        }			
			
        $query = $openemm_db->query("select
                                        SUM(current_mails) as current,
                                        SUM(total_mails) as total
                                    from mailing_backend_log_tbl
                                    where mailing_id=$id;");
        $data = $query->result();
        if (count($data)) {
            $result['sent']    = $data[0];
            $result['total']   = $data[0]->total;
            $result['current'] = $data[0]->current;
        }

        $query = $openemm_db->query("select
                                        SUM(1) as opened_emails
                                        from onepixel_log_tbl
                                    where mailing_id=$id");
        $data = $query->result();
        if (count($data)) {
            $result['opened'] = $data[0]->opened_emails;
        }

        $query = $openemm_db->query("select count(*) as clicked
                                        FROM `rdir_log_tbl`
                                        where mailing_id=$id");
        $data = $query->result();
        if (count($data)) {
            $result['clicked'] = $data[0]->clicked;
        } else {
            $result['clicked'] = 0;
        }

        $query = $openemm_db->query("select bnccnt
                                        FROM `softbounce_email_tbl`
                                        where mailing_id=$id");
        $data = $query->result();
        if (count($data)) {
            $result['softbounce'] = $data[0]->bnccnt;
        } else {
            $result['softbounce'] = 0;
        }

        $query = $openemm_db->query("select count(*) as bounce
                                        FROM `bounce_tbl`
                                        where mailing_id=$id");
        $data = $query->result();
        if (count($data)) {
            $result['hardbounce'] = $data[0]->bounce;
        }

        $result['bounce'] = $result['softbounce'] + $result['hardbounce'];

        //calculate percentage of total email
        if ($result['sent']->total == 0) {
            return false;
        } else {
            $data = array(
				'openemm_name'   			=> $result['openemm_name'],
				'openemm_open_rate' 	   	=> round($result['opened'] / $result['sent']->total, 4),
                'openemm_bounce_rate'      	=> round($result['bounce'] / $result['sent']->total, 4),
                'openemm_hard_bounce_rate' 	=> round($result['hardbounce'] / $result['sent']->total, 4),
                'openemm_soft_bounce_rate' 	=> round($result['softbounce'] / $result['sent']->total, 4),
                'openemm_click_rate'       	=> round($result['clicked'] / $result['sent']->total, 4),
				'openemm_number_of_open'   	=> $result['opened'],
                'openemm_number_of_click'  	=> $result['clicked'],
                'openemm_total'            	=> $result['total'],
                'openemm_current'          	=> $result['current'],
                'openemm_opened'           	=> $result['opened'],
				'openemm_bounce'           	=> $result['bounce'],
            );
            return $data;
        }
    }	
}
// EOF