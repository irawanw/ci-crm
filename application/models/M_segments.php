<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_segments extends MY_Model
{

    private $_TABLE       = "t_segments";
    private $_PRIMARY_KEY = "id";

    public function __construct()
    {
        parent::__construct();
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table       = $this->_TABLE;
        $primary_key = $this->_PRIMARY_KEY;
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select($table . ".*,$primary_key as RowID, $primary_key as checkbox");

        // switch($void){
        //     case 'archived':
        //         $this->db->where('inactive != "0000-00-00 00:00:00"');
        //         break;
        //     case 'deleted':
        //         $this->db->where('deleted != "0000-00-00 00:00:00"');
        //         break;
        //     case 'all':
        //         break;
        //     default:
        //         $this->db->where('inactive is NULL');
        //         $this->db->where('deleted is NULL');
        //         break;
        // }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where($primary_key, $id);
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->RowID . '">';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'name' => 'char',
        );

        return $filterable_columns;
    }

    public function sync()
    {
        $url = 'http://contact.bal-idf.com/dev/v.8.8.segment-json/segment/api.php'; // (contact.bal-idf)
        //$url = 'https://web-workspace-riggas.c9users.io/contacts-db/v.8.8.segment-json/segment/api.php';  // (Dimitrios dev)

        $data = array(
            'url_key'  => 'f03ca409f613ba896cd7e54456f9af2c61675307dec55c25c45e7d8262186c24', //required (contact.bal-idf)
            //'url_key' => '6da685fadf8f9e12f731b9ef6ef861c9167f28784a6fedba208de6a43073a683', //required (Dimitrios dev)
            'function' => 'segments_list', //required
            'params'   => array('last_segment_id' => 0), //optional
        );

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        $result  = explode('response: ', $result);
        $result  = json_decode($result[1], true);
        $data    = $result['data'];

        $check = $this->db->get($this->_TABLE);

        if ($check->num_rows() == 0) {
            $this->db->insert_batch($this->_TABLE, $data);
        }
    }

    public function resync()
    {
        $url = 'http://contact.bal-idf.com/dev/v.8.8.segment-json/segment/api.php';
        $data = array(
            'url_key'  => 'f03ca409f613ba896cd7e54456f9af2c61675307dec55c25c45e7d8262186c24', 
            'function' => 'segments_list', //required
            'params'   => array('last_segment_id' => 0), //optional
        );

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        $result  = explode('response: ', $result);
        $result  = json_decode($result[1], true);
        $data    = $result['data'];

        $this->db->truncate($this->_TABLE);
        $this->db->insert_batch($this->_TABLE, $data);
    }

    public function get($id)
    {
        $query = $this->db->get_where($this->_TABLE, array($this->_PRIMARY_KEY => $id));

        if($query->row()) {
            $resultat = $query->row();
            if ($resultat->filtering != null) {
                $filtering     = json_decode($resultat->filtering);
                $filtering_arr = (array) $filtering;
                $n             = 0;
                $criteria      = "";

                foreach ($filtering_arr as $key => $val) {
                    if ($n % 2 == 0) {
                        $name = $key . "(" . $val . ") : ";
                    }

                    if ($n % 2 == 0) {
                        $criteria .= $key . "(" . $val . ") : ";
                    }

                    if ($n % 2 != 0) {
                        if ($val != "") {
                            $criteria .= $val . "&#13;&#10;";
                        } else {
                            $criteria .= "&#13;&#10;";
                        }
                    }

                    $n++;
                }
            }

            $resultat->criteria = $criteria;

            return $resultat;
        } else {
            return false;
        }
    }

    public function insert($data)
    {
        $this->db->insert($this->_TABLE, $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function update($id, $data)
    {
        $update = $this->db->where($this->_PRIMARY_KEY, $id)
            ->update($this->_TABLE, $data);

        if ($this->db->affected_rows() == 1 || $update == true) {
            return $id;
        }

        return false;

    }

    public function delete($id)
    {
        $this->db->where($this->_PRIMARY_KEY, $id)
            ->delete($this->_TABLE);
        if ($this->db->affected_rows() == 1) {            
            return true;
        } else {
            return false;
        }
    }

    public function liste_option()
    {
        $query = $this->db->select("id,CONCAT(id,'-',name) as value")
            ->order_by('name', 'ASC')
            ->get($this->_TABLE);

        $result = $query->result();

        return $result;
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
