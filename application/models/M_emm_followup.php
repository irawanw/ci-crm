<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_emm_followup extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_mailing()
    {
        $openemm_db = $this->load->database('openemm', true); // the TRUE paramater tells CI that you'd like to return the database object.
        $query      = $openemm_db
            ->select('mailing_id, shortname')
            ->where("deleted != 1")
            ->get('mailing_tbl')->result();
        return $query;
    }

    public function get_mailing_detail($id)
    {
        $openemm_db = $this->load->database('openemm', true); // the TRUE paramater tells CI that you'd like to return the database object.

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
                'openemm_open_rate'        => round($result['opened'] / $result['sent']->total, 4),
                'bounce_rate'      => round($result['bounce'] / $result['sent']->total, 4),
                'hard_bounce_rate' => round($result['hardbounce'] / $result['sent']->total, 4),
                'soft_bounce_rate' => round($result['softbounce'] / $result['sent']->total, 4),
                'click_rate'       => round($result['clicked'] / $result['sent']->total, 4),
                'number_of_clicks' => $result['clicked'],
                'total'            => $result['total'],
                'current'          => $result['current'],
                'opened'           => $result['opened'],
            );
            return $data;
        }
    }

    /******************************
     * Liste Pages_jaunes Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_emm_followup';
        $this->db->start_cache();
        $open_rate              = "CONCAT(ROUND(open_rate*100, 2), '%')";
        $open_rate_alias        = $open_rate . " AS open_rate";
        $bounce_rate            = "CONCAT(ROUND(bounce_rate*100, 2), '%')";
        $bounce_rate_alias      = $bounce_rate . " AS bounce_rate";
        $hard_bounce_rate       = "CONCAT(ROUND(hard_bounce_rate*100, 2), '%')";
        $hard_bounce_rate_alias = $hard_bounce_rate . " AS hard_bounce_rate";
        $soft_bounce_rate       = "CONCAT(ROUND(soft_bounce_rate*100, 2), '%')";
        $soft_bounce_rate_alias = $soft_bounce_rate . " AS soft_bounce_rate";
        $click_rate             = "CONCAT(ROUND(click_rate*100, 2), '%')";
        $click_rate_alias       = $click_rate . " AS click_rate";
        $client                 = "ctc_nom";
        $client_name            = $client . " AS client_name";
        $commande               = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name          = $commande . " AS commande_name";

        $this->db->select($table . ".*,
                    emm_followup_id as checkbox,
                    $open_rate_alias,
                    $bounce_rate_alias,
                    $hard_bounce_rate_alias,
                    $soft_bounce_rate_alias,
                    $click_rate_alias,
                    $client_name,
                    $commande_name");

        $this->db->join('t_contacts as tc', 'ctc_id=client', 'left');
        $this->db->join('t_commandes as tm', 'cmd_id=commande', 'left');

        switch ($void) {
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

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'open_rate'        => $open_rate,
            'bounce_rate'      => $bounce_rate,
            'hard_bounce_rate' => $hard_bounce_rate,
            'soft_bounce_rate' => $soft_bounce_rate,
            'click_rate'       => $click_rate,
            'client_name'      => $client,
            'commande_name'    => $commande,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        if(count($resultat['data']) > 0)
        {
            $custom_data = array();

            foreach($resultat['data'] as $row)
            {
                if($row->message == '' || $row->message == '0') {
                    $row->message = '<a class="btn-upload-file" href="#" data-id="'.$row->emm_followup_id.'">Telecharger</a>';
                } else {
                    $row->message = '<a target="_blank" href="'.base_url('fichiers/emm_followup/'.$row->message).'">'.$row->message.'</a>';
                }

                //add checkbox into data
                $row->checkbox = '<input type="checkbox" name="ids[]" value="'.$row->emm_followup_id.'">';
                
                $custom_data[] = $row;
            }

            $resultat['data'] = $custom_data;
        }

        return $resultat;

    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'client_name'               => 'char',
            'commande_name'             => 'char',
            'quantite_totale_a_envoyer' => 'int',
            'quantite_envoyee'          => 'int',
            'type'                      => 'char',
            'logiciel_utilise'          => 'char',
            'number_of_opens'           => 'int',
            'open_rate'                 => 'int',
            'bounce_rate'               => 'int',
            'hard_bounce_rate'          => 'int',
            'soft_bounce_rate'          => 'int',
            'number_of_clicks'          => 'int',
            'click_rate'                => 'int',
            'deliverance'               => 'char',
            'percentage_delivery'       => 'int',
            'percentage_spam'           => 'int',
            'percentage_not_delivered'  => 'int',
            'ip_blacklist'              => 'int',
            'message_blacklist'         => 'int',
            'domain_blacklist'          => 'int',
            'sender_blacklist'          => 'int',
            'server'                    => 'char',
            'smtp'                      => 'char',
            'rotation'                  => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Pages_jaunes insert into t_emm_followup table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_emm_followup', $data);
    }

    /******************************
     * Detail d'une emm_followup
     ******************************/
    public function detail($id)
    {
        $this->db->select("tef.*,
                    tc.ctc_id as client,
                    tc.ctc_nom as client_name,
                    tef.commande as commande,
                    CASE WHEN
                    tef.commande = -1 THEN 'Pas de Commande'
                    ELSE tm.cmd_reference
                    END as commande_reference");
        $this->db->join('t_contacts as tc', 'tc.ctc_id=tef.client', 'left');
        $this->db->join('t_commandes as tm', 'tm.cmd_id=tef.commande', 'left');
        $this->db->where('emm_followup_id = "' . $id . '"');
        $q = $this->db->get('t_emm_followup as tef');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating emm_followup data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_emm_followup', $data, $id, 'emm_followup_id');
    }

    /******************************
     * Archive emm_followup data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_emm_followup', $id, 'emm_followup_id', 'inactive');
    }

    /******************************
     * Archive emm_followup data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_emm_followup', $id, 'emm_followup_id', 'deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_emm_followup',$data, $id,'emm_followup_id');
    }

    public function status_option()
    {
        $result = array();
        $values = array('Clear', 'Blacklisted', 'No Deliverance');
        return $this->form_option($values);
    }

    public function type_option()
    {
        $result = array();
        $values = array('text', 'html');
        return $this->form_option($values);
    }

    public function logiciel_utilise_option()
    {
        $result = array();
        $values = array('manuel', 'open emm', 'bulk', 'autres');
        return $this->form_option($values);
    }

    public function deliverance_option()
    {
        $result = array();
        $values = array('orange', 'free', 'gmail', 'hotmail', 'yahoo', 'ovh', 'etc');
        return $this->form_option($values);
    }

    public function rotation_option()
    {
        $result = array();
        $values = array('yes', 'no');
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

    public function commande($livraisons_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tl.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tl.livraisons_id = "' . $livraisons_id . '"');
        $q = $this->db->get('t_livraisons tl');
        return $q->result();
    }

    public function commande_by_client($client_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tcs.ctc_id', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tcs.ctc_id = "' . $client_id . '"');
        $q = $this->db->get('t_contacts tcs');
        return $q->result();
    }
}
// EOF
