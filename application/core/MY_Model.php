<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 22/07/15
 * Time: 09:19
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property CI_Input $input
 */
class MY_Model extends CI_Model {

    const INSERT = 1;
    const UPDATE = 2;
    const DELETE_SOFT = 3;
    const DELETE_HARD = 4;
    
    protected $filterable_columns = array();
    
    /******************************
     * Creation
     ******************************/
    public function _insert($table, $data) {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
            $id = $this->db->insert_id();
            $this->_historise($table, $id, '', $data, $this::INSERT);
            return $id;
        }
        return false;
    }

    /**
     * Returns whether the instance can be updated
     *
     * @param integer|stdClass $id_or_values Record info
     * @param array            $data         Data to be set
     * @return bool
     */
    public function can_update($id_or_values, $data)
    {
        return true;
    }

    /******************************
     * Mise à jour
     ******************************/
    public function _update($table, $data, $id, $champ_id, $where = array()) {		
        // récupération de l'état actuel de l'enregistrement
        foreach ($where as $k => $v) {
            $this->db->where($k, $v);
        }
        $q = $this->db->where($champ_id, $id)
            ->get($table);
        if ($q->num_rows() == 0) return $id;
        $data_orig = $q->row_array();

        // vérification du besoin de mise à jour
        $change = false;
        foreach ($data as $k => $v) {
            if ($data_orig[$k] != $v) {
                $change = true;
                break;
            }
        }
        if (!$change) {
            return 0;
        }

        // mise à jour effective
        foreach ($where as $k => $v) {
            $this->db->where($k, $v);
        }
        $update = $this->db->where($champ_id, $id)
            ->update($table, $data);
		
		//on mysql 5.7.18 sometimes affected_rows given 0 result even the update is success
		//adding more condition to check update query if success by checking true or false the result of query
        if ($this->db->affected_rows() == 1 || $update == true) {
            $this->_historise($table, $id, $champ_id, $data_orig, $this::UPDATE);
            return $id;
        }
        return false;
    }

    /**
     * Returns whether the instance can be deleted
     *
     * @param integer|stdClass $id_or_values
     * @return bool
     */
    public function can_delete($id_or_values)
    {
        return true;
    }

    /******************************
     * Suppression
     ******************************/
    public function _delete($table, $id, $champ_id, $champ_inactif = false) {
        // récupération de l'état actuel de l'enregistrement
        $q = $this->db->where($champ_id, $id)
            ->get($table);
        if ($q->num_rows() == 0) return false;
        $data_orig = $q->row_array();

        if ($champ_inactif !== false) {
            $data = array($champ_inactif => date('Y-m-d H:i:s'));
            $this->db->where($champ_id, $id)
                ->update($table, $data);
            if ($this->db->affected_rows() == 1) {
                $this->_historise($table, $id, $champ_id, $data_orig, $this::DELETE_SOFT);
                return true;
            }
        } else {
            $this->db->where($champ_id, $id)
                ->delete($table);
            if ($this->db->affected_rows() == 1) {
                $this->_historise($table, $id, $champ_id, $data_orig, $this::DELETE_HARD);
                return true;
            }
        }
        return false;
    }

    /******************************
     * Historisation
     ******************************/
    private function _historise($table, $id, $champ_id, $info, $action, $restauration = 0) {

        switch ($table) {
            case 't_actions':
            case 't_alertes':
            case 't_messages':
                break;
            default:
                $data = array(
                    'act_date' => date('Y-m-d H:i:s'),
                    'act_table' => $table,
                    'act_obj_id' => $id,
                    'act_champ_id' => $champ_id,
                    'act_info' => serialize($info),
                    'act_user' => $this->session->id,
                    'act_action' => $action,
                    'act_restauration' => $restauration,
                );
                $this->db->insert('t_actions', $data);
        }
    }

    /******************************
     * Restauration
     ******************************/
    public function _restaure($id) {

        // récupération de l'enregistrement de l'action
        $q = $this->db->where('act_id', $id)
            ->get('t_actions');
        if ($q->num_rows() > 0) {
            $info = $q->row();

            // récupération de l'état actuel de l'enregistrement
            $q2 = $this->db->where($info->act_champ_id, $info->act_obj_id)
                ->get($info->act_table);
            if ($q2->num_rows() > 0) {
                $data_orig = $q->row_array();
            }

            // remise à l'état avant l'action
            $data = unserialize($info->act_info);
            switch ($info->act_action) {
                case $this::INSERT:
                    $this->db->where($info->act_champ_id, $info->act_obj_id)
                        ->delete($info->act_table);
                    $this->_historise($info->act_table, $info->act_obj_id, $info->act_champ_id, $data_orig, $this::DELETE_HARD, $id);
                    break;
                case $this::UPDATE:
                case $this::DELETE_SOFT:
                    $this->db->where($info->act_champ_id, $info->act_obj_id)
                        ->update($info->act_table, $data);
                    $this->_historise($info->act_table, $info->act_obj_id, $info->act_champ_id, $data_orig, $this::UPDATE, $id);
                    break;
                case $this::DELETE_HARD:
                    $this->db->insert($info->act_table, $data);
                    $this->_historise($info->act_table, $info->act_obj_id, $info->act_champ_id, array(), $this::INSERT, $id);
                    break;
            }
            return true;
        }
        return false;
    }

    /******************************
     * Construction de requête de filtrage pour les datatables
     ******************************/
    protected function _filtre($table,$filterable_columns,$aliases,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc", $basetable_is_query=false) {
        //log_message('DEBUG', json_encode( func_get_args() ));

        // le début de la requête est en cache
        $total_rows = $this->db->count_all_results($table . (($basetable_is_query==true)?" AS TBL":""));
        
        //log_message('DEBUG', 'In _filtre total_rows='.$total_rows);

        $this->db->start_cache();

        if ( array_key_exists('_global', ($filters===NULL)?array():$filters) ) {
            $this->db->group_start();
            foreach (array_keys($filterable_columns) as $filter_key) {
                $this->add_or_where_contains($this->db, $filter_key, $filters['_global'], $filterable_columns[$filter_key]);
            }
            $this->db->group_end();
        } else if ( $filters!==NULL && count($filters)>0 ) {
            $filters = array_intersect_key($filters, $filterable_columns);
            foreach (array_keys($filters) as $filter_key) {
                switch ($filters[$filter_key]['type']) {
                    case 'eq':
                        $this->add_where_equals($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'noteq':
                        $this->add_where_does_not_equal($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'st':
                        $this->add_where_starts($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'notst':
                        $this->add_where_does_not_start($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'cont':
                        $this->add_where_contains($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'notcont':
                        $this->add_where_does_not_contain($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'btw':
                        $this->add_where_between($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'isempty':
                        $this->add_where_is_empty($this->db, $filter_key, $filterable_columns[$filter_key]);
                        break;
                    case 'isnotempty':
                        $this->add_where_is_not_empty($this->db, $filter_key, $filterable_columns[$filter_key]);
                        break;
                    case 'lt':
                        $this->add_where_less($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'lte':
                        $this->add_where_less_or_equal($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'gt':
                        $this->add_where_greater($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    case 'gte':
                        $this->add_where_greater_or_equal($this->db, $filter_key, $filters[$filter_key]['input'], $filterable_columns[$filter_key]);
                        break;
                    default:
                }
            }
        }
        $this->db->stop_cache();

        // requête de comptage
        if ($basetable_is_query==true) {
            $query = $this->db->get_compiled_select("__wrap_table__",false);
            
            //log_message('DEBUG', 'basetable_is_query'.$basetable_is_query.': '. $query);
            $query = str_replace("`__wrap_table__`", "$table AS TBL", $query);
        
            //log_message('DEBUG', $query);
            //$query = "SELECT COUNT(*) as c FROM (".$query.") AS CNT_TBL";

            $q = $this->db->query("SELECT COUNT(*) as c FROM (".$query.") AS CNT_TBL");
            $filtered_rows = $q->row()->c;

        }
        else {
            $query = $this->db->get_compiled_select($table,false);
            
            // substitution des champs calculés dans la clause where
            $from = strrpos($query,'FROM');
            $gauche = substr($query,0,$from);
            $droite = substr($query,$from);
            foreach($aliases as $k=>$v) {
                $droite = str_replace('`'.$k.'`',$v,$droite);
                $droite = str_replace($k,$v,$droite);
            }
            $query = "SELECT COUNT(*) as c ".$droite;

            $q = $this->db->query($query);
            if ($q->num_rows() > 0) {
                $filtered_rows = $q->row()->c;
            }
            else {
                $filtered_rows = 0;
            }
            $query = $gauche.$droite;
        }
        
        //log_message('DEBUG', 'In _filtre filtered_rows='.$filtered_rows);

        
        if ( !is_int($offset) || $limit<0  ) $limit=500;
        if ( !is_int($limit)  || $offset<1 ) $offset=0;

        //set no limit if this action for export data
        if($limit == false) {
            $limit = 0;
        }

        // requête de sélection
        if ( !empty($ordercol)) {
            // Order by single field
            if ( !is_array($ordercol) ) {
            //    $query .= " ORDER BY ".($ordercol)." ".$ordering ;    
                $ordercol = array($ordercol);
                $ordering = array($ordering);
            }
            // Order by multiple fields
            //else {
                $ordersep = " ";
                $orderstmt = " ORDER BY ";
                foreach ($ordercol as $key => $col) {
                    $orderstmt .= $ordersep. $col ." ". $ordering[$key];
                    $ordersep = ", ";
                }
                $query .= $orderstmt;
            //}
        }
        if ( $limit != 0 )
            $query .= " LIMIT $offset, $limit";
            
        //log_message('DEBUG', 'MY_Model data query: '.$query);
        $q = $this->db->query($query);
        
        if ( $q->num_rows() > 0 ) {
            $result = array(
                "data"=>$q->result(),
                "recordsTotal"=>$total_rows, "recordsFiltered"=>intval($filtered_rows),
                "recordsOffset"=>$offset, "recordsLimit"=>$limit,
                "ordercol"=>$ordercol, "ordering"=>$ordering);
            return $result;
        }
        else {
            $result = array("data"=>array(), 
                "recordsTotal"=>$total_rows, "recordsFiltered"=>intval($filtered_rows),
                "recordsOffset"=>$offset, "recordsLimit"=>$limit);
            return $result;
        }
    }

    /***********************************************************
     * Helpers privés pour la construction de la requête SQL
     * Private helper functions helping build filtering queries.
     **********************************************************/
     
    private function add_where_equals(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
            case 'int':
            case 'decimal':
                $db->where($column, $value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) = STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) = STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
    private function add_where_does_not_equal(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
            case 'int':
            case 'decimal':
                $db->where($column.' !=',$value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) != STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) != STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
    private function add_where_starts(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
                $db->like($column, $value, 'after');
                break;
            case 'int':
            case 'decimal':
                $db->like('CAST('.$column.' AS CHAR) ', $value, 'after');
                break;
        }
    }
    private function add_where_does_not_start(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
                $db->not_like($column, $value, 'after');
                break;
            case 'int':
            case 'decimal':
                $db->not_like('CAST('.$column.' AS CHAR) ', $value, 'after');
                break;
        }
    }
    private function add_where_contains(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
                $db->like($column, $value, 'both');
                break;
            case 'int':
            case 'decimal':
                $db->like('CAST('.$column.' AS CHAR) ', $value, 'both');
                break;
        }
    }
    private function add_where_does_not_contain(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
                $db->not_like($column, $value, 'both');
                break;
            case 'int':
            case 'decimal':
                $db->not_like('CAST('.$column.' AS CHAR) ', $value, 'both');
                break;
        }
    }
    private function add_where_between(&$db, $column, $value, $datatype) {
        if (strpos($value,"-")===false)
            return; 
        $fromto = explode("-", $value);
        if ( count($fromto)!=2 ) {
        // || !is_numeric($fromto[0]) || !is_numeric($fromto[1]) ) 
            log_message('error', "Error parsing btw values '$column', '$value', '$datatype'");
            return;
        }
        $minvalue = trim(($fromto[0]<$fromto[1])?$fromto[0]:$fromto[1]);
        $maxvalue = trim(($fromto[0]>$fromto[1])?$fromto[0]:$fromto[1]);
        switch ($datatype) {
            case 'char':
                //log_message('error', "Cannot apply BETWEEN filter to CHAR column: '$column', '$value', '$datatype'");
                if ( is_numeric($minvalue) && is_numeric($maxvalue) )
                    $db->where("$column BETWEEN $minvalue AND $maxvalue");
                else 
                    $db->where("$column BETWEEN '$minvalue' AND '$maxvalue'");
                break;
            case 'int':
            case 'decimal':
                //$db->like('CAST('.$column.' AS CHAR) ', $value, 'both');
                $db->where("$column BETWEEN $minvalue AND $maxvalue");
                break;
            case 'date':
                $db->where('(CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d"),DATETIME) >= STR_TO_DATE( "'.$minvalue.'","%d/%m/%Y") AND CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d"),DATETIME) <= STR_TO_DATE( "'.$maxvalue.'","%d/%m/%Y"))');
                break;
            case 'datetime':
                $db->where('(CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) >= STR_TO_DATE( "'.$minvalue.' 00:00","%d/%m/%Y %H:%i") AND CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:59"),DATETIME) <= STR_TO_DATE( "'.$maxvalue.' 23:59","%d/%m/%Y %H:%i"))');
                break;
                
        }
    }
    private function add_where_is_empty(&$db, $column, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
            case 'date':
            case 'datetime':
                $db->where('('.$column .' IS NULL OR '.$column .'="")', NULL, FALSE);
                break;
            case 'int':
            case 'decimal':
                $db->where($column .' IS NULL ', NULL, FALSE);
                break;
        }
    }
    private function add_where_is_not_empty(&$db, $column, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
            case 'date':
            case 'datetime':
                $db->where('('.$column .' IS NOT NULL AND '.$column .'!="")', NULL, FALSE);
                break;
            case 'int':
            case 'decimal':
                $db->where($column .' IS NOT NULL ', NULL, FALSE);
                break;
        }
    }
    private function add_or_where_contains(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'char':
            case 'select':
                $db->or_like($column, $value, 'both');
                break;
            case 'int':
            case 'decimal':
                $db->or_like('CAST('.$column.' AS CHAR) ', $value, 'both');
                break;
            case 'date':
            case 'datetime':
                $db->or_like('DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:%s") ', $value, 'both');
                break;
        }
    }
    private function add_where_less(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'int':
            case 'decimal':
                $db->where($column.'<'.$value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) < STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) < STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
    private function add_where_less_or_equal(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'int':
            case 'decimal':
                $db->where($column.'<='.$value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) <= STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) <= STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
    private function add_where_greater(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'int':
            case 'decimal':
                $db->where($column.'>'.$value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) > STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) > STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
    private function add_where_greater_or_equal(&$db, $column, $value, $datatype) {
        switch ($datatype) {
            case 'int':
            case 'decimal':
                $db->where($column.'>='.$value);
                break;
            case 'datetime':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d %H:%i:00"),DATETIME) >= STR_TO_DATE( "'.$value.'","%d/%m/%Y %H:%i")');
                break;
            case 'date':
                $db->where('CONVERT(DATE_FORMAT('.$column.',"%Y-%m-%d 00:00:00"),DATETIME) >= STR_TO_DATE( "'.$value.'","%d/%m/%Y")');
                break;
        }
    }
}
// EOF
