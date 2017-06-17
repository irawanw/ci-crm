<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_production_mails extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('production_mails_id', 'ref', "production_mails#", 'production_mails', 'production_mails_id', 'production_mails_id'),
                array('mail', 'text', "Mail", 'mail'),
                array('domain', 'text', "Domain", 'domain'),
                array('login_url', 'text', "Login Url", 'login_url'),
                array('login', 'text', "Login", 'login'),
                array('pass', 'text', "Pass", 'pass'),
                array('provider_name', 'text', "Provider", 'provider_name'),
                array('provider_abuse_email', 'text', "Abuse Email", 'provider_abuse_email'),
                array('provider_abuse_telephone', 'text', "Abuse Telephone", 'provider_abuse_telephone'),
                array('provider_abuse_url', 'text', "Abuse Url", 'provider_abuse_url'),
                array('commentaries', 'text', "Commentaries", 'commentaries'),
                array('used_for', 'text', "Used for", 'used_for'),
                array('status', 'text', "Status", 'status'),
                array('blacklist_provider', 'text', "Blacklist", 'blacklist_provider'),
            ),
            'write' => array(
                'mail'            => array("Mail", 'text', 'mail', false),
                'domain'          => array("Domain", 'text', 'domain', false),
                'login'           => array("Login", 'text', 'login', false),
                'pass'            => array("Pass", 'text', 'pass', false),
                'provider'        => array("Provider", 'select', array('provider', 'id', 'value'), false),
                'abuse_email'     => array("Abuse Email", 'textarea', 'abuse_email', false),
                'abuse_telephone' => array("Abuse Telephone", 'textarea','abuse_telephone', false),
                'abuse_url'       => array("Abuse Url", 'textarea', 'abuse_url', false),
                'login_url'       => array("Login Url", 'text', 'login_url', false),
                'commentaries'    => array("Commentaries", 'textarea', 'commentaries', false),
                'used_for'        => array("Used for", 'select', array('used_for', 'id', 'value'), false),
                'status'          => array("Status", 'select', array('status', 'id', 'value'), false),
                'blacklist'       => array("Blacklist", 'select', array('blacklist', 'id', 'value'), false),
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void = "", $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {


        // première partie du select, mis en cache
        $this->db->start_cache();
        $table                    = 't_production_mails';
        $production_mails_id      = formatte_sql_lien('production_mails/detail', 'production_mails_id', 'production_mails_id');
        $provider                 = "tp.provider";
        $provider_name            = $provider . " AS provider_name";
        $provider_abuse_email     = "tp.abuse_email as provider_abuse_email";
        $provider_abuse_telephone = "tp.abuse_telephone as provider_abuse_telephone";
        $provider_abuse_url       = "tp.abuse_url as provider_abuse_url";
        //$blacklist_provider       = "GROUP_CONCAT(tp2.provider) as blacklist_provider";
		$blacklist_provider       = "rbl.rbl_nom as blacklist_provider";

        $this->db->select($table . ".*,
                    production_mails_id as RowID,
                    production_mails_id as checkbox,
                    $provider_name,
                    $provider_abuse_email,
                    $provider_abuse_telephone,
                    $provider_abuse_url,
					$blacklist_provider
        ", false);
        $this->db->join('t_providers as tp', 'tp.providers_id = ' . $table . '.provider', 'left');
		$this->db->join('t_rbl_liste as rbl', 'rbl.rbl_id = ' . $table . '.blacklist', 'left');
        //$this->db->join('t_providers as tp2', 'FIND_IN_SET(tp2.providers_id, blacklist)', 'left');
        $this->db->group_by('production_mails_id');

        //customize filter blacklist using having not where because where in mysql not using group/aggregate function
		/*
        if($filters != null) {
            if (array_key_exists("blacklist_provider",$filters)) {
                $filters_blacklist = $filters['blacklist_provider'];
                $input = '%'.$filters_blacklist['input'].'%';

                $this->db->having('GROUP_CONCAT(tp2.provider) LIKE "'.$input.'"');
                unset($filters["blacklist_provider"]);
            }
        }
		*/
        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where($table . '.deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table . '.inactive is NULL');
                $this->db->where($table . '.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('production_mails_id', $id);
        }
        $this->db->stop_cache();
        // aliases
        $aliases = array(
            'provider_name'            => $provider,
            'provider_abuse_email'     => "tp.abuse_email",
            'provider_abuse_telephone' => "tp.abuse_telephone",
            'provider_abuse_url'       => "tp.abuse_url",
            //'blacklist_provider'       => "tp2.provider",
			'blacklist_provider'       => "rbl.rbl_nom",
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        if (count($resultat['data']) > 0) {
            $result = $resultat['data'];
            $data   = array();

            foreach ($result as $row) {
                $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->production_mails_id . '">';

                $data[] = $row;
            }

            $resultat['data'] = $data;
        }

        return $resultat;

    }

    public function simple_list()
    {
        $this->db->select("*");
        $this->db->order_by('production_mails_id', 'ASC');
        $this->db->where('inactive is NULL');
        $this->db->where('deleted is NULL');
        return $this->db->get('t_production_mails')->result();
    }
    // public function custom_resultat($resultat)
    // {
    //     if(count($resultat['data']) > 0) {
    //         $result = $resultat['data'];
    //             $data = array();

    //             foreach($result as $row) {
    //                 $blacklist_ids = $row->blacklist ? $row->blacklist : null;

    //                 if($blacklist_ids) {

    //                     $blacklists_result = $this->db->query('select provider from t_providers WHERE provider IN ('.$blacklist_ids.')')->result();

    //                     if($blacklists_result) {
    //                         $blacklist = '';
    //                         foreach($blacklists_result as $bl) {
    //                             $blacklist .= $bl->provider.' ';
    //                         }

    //                         $row->blacklist = $blacklist;
    //                     }
    //                 }

    //                 $data[] = $row;
    //             }

    //         $resultat['data'] = $data;
    //     }

    //     return $resultat;
    // }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'production_mails_id'      => 'int',
            'mail'                     => 'char',
            'domain'                   => 'char',
            'login_url'                => 'char',
            'login'                    => 'char',
            'pass'                     => 'char',
            'provider_name'            => 'char',
            'provider_abuse_email'     => 'char',
            'provider_abuse_telephone' => 'char',
            'provider_abuse_url'       => 'char',
            'commentaries'             => 'char',
            'used_for'                 => 'char',
            'status'                   => 'char',
            'blacklist_provider'       => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Message list insert into t_production_mails table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_production_mails', $data);
    }

    /******************************
     * Detail d'une test mails
     ******************************/
    public function detail($id)
    {
        $this->db->select("tm.*, tp.provider as provider_name, tp.abuse_email as abuse_email, tp.abuse_telephone as abuse_telephone, tp.abuse_url as abuse_url");
        $this->db->join('t_providers as tp', 'tp.providers_id = tm.provider', 'left');
        $this->db->where('tm.production_mails_id = "' . $id . '"');
        $q = $this->db->get('t_production_mails as tm');
        if ($q->num_rows() > 0) {
            $resultat      = $q->row();
            $blacklist_ids = $resultat->blacklist ? explode(",", $resultat->blacklist) : null;

            if ($blacklist_ids) {
                $blacklists_result = $this->db->select('provider')->where_in('providers_id', $blacklist_ids)->get('t_providers')->result();
                if ($blacklists_result) {
                    $blacklist = '';
                    foreach ($blacklists_result as $bl) {
                        $blacklist .= $bl->provider . ' ';

                    }

                    $resultat->blacklist = $blacklist;
                }
            }

            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating test mails data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_production_mails', $data, $id, 'production_mails_id');
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_production_mails', $id, 'production_mails_id', 'inactive');
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_production_mails', $id, 'production_mails_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_production_mails', $data, $id, 'production_mails_id');
    }

    public function get_provider_detail($id)
    {
        $result = $this->db->get_where('t_providers', array('providers_id' => $id))->row();

        if ($result) {
            $abuse_email_string     = $result->abuse_email;
            $abuse_telephone_string = $result->abuse_telephone;
            $abuse_url_string       = $result->abuse_url;

            $abuse_email_arr     = $abuse_email_string != '' ? explode("\n", $abuse_email_string) : false;
            $abuse_telephone_arr = $abuse_telephone_string != '' ? explode("\n", $abuse_telephone_string) : false;
            $abuse_url_arr       = $abuse_url_string != '' ? explode("\n", $abuse_url_string) : false;

            $data = array(
                'abuse_email'     => $abuse_email_arr,
                'abuse_telephone' => $abuse_telephone_arr,
                'abuse_url'       => $abuse_url_arr,
            );
        } else {
            $data = array(
                'abuse_email'     => false,
                'abuse_telephone' => false,
                'abuse_url'       => false,
            );
        }

        return $data;
    }

    public function liste_providers()
    {
        return $this->db->select('tp.providers_id as id, provider as value')->order_by('provider', 'ASC')->get('t_providers as tp')->result();
    }


    public function liste_abuse_email($provider_id = null)
    {
        $values = '';
        if ($provider_id) {
            $provider = $this->get_provider_detail($provider_id);
            if (is_array($provider)) {
                foreach ($provider['abuse_email'] as $row) {
                    $values[] = trim($row);
                }
            }
        } else {
            $values = array('0');
        }

        return $this->form_option($values);
    }

    public function liste_abuse_telephone($provider_id = null)
    {
        if ($provider_id) {
            $provider = $this->get_provider_detail($provider_id);
            if (is_array($provider)) {
                foreach ($provider['abuse_telephone'] as $row) {
                    $values[] = trim($row);
                }
            }
        } else {
            $values = array('0');
        }
        return $this->form_option($values);
    }

    public function liste_abuse_url($provider_id = null)
    {
        if ($provider_id) {
            $provider = $this->get_provider_detail($provider_id);
            if (is_array($provider)) {
                foreach ($provider['abuse_url'] as $row) {
                    $values[] = trim($row);
                }
            }
        } else {
            $values = array('0');
        }
        return $this->form_option($values);
    }
	
	public function liste_blacklist()
	{
		$this->db->select("rbl_id as id, rbl_nom as value");
		$sql = $this->db->get('t_rbl_liste');
		$result = $sql->result();
		return $result;
	}

    public function used_for_option()
    {
        $result = array();
        $values = array('Anti-blacklist replacement mail', 
						'Deliverance test', 
						'Manual sending', 
						'Pages jaunes checking', 
						'Spamtrap', 
						'Message body', 
						'Other');
        return $this->form_option($values);
    }

    public function status_option()
    {
        $values = array('valid', 'partly blacklisted', 'dead');
        return $this->form_option($values);
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
