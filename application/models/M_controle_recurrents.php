<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_controle_recurrents extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /******************************
     * Liste Controle Recurrents Data
     ******************************/
    public function liste($void, $type='', $limit = 10, $offset = 1, $filters = null, $group = '', $ordercol = 2, $ordering = "asc")
    {
        $table = "t_controle_recurrents";
        $this->db->start_cache();

        $group_name    = "t_group.name as group_name";
        $type_alias          = $table . ".type";
        $date_controle = formatte_sql_date("date_controle");

        $this->db->select("
                controle_recurrents_id as checkbox, controle_recurrents_id as RowID,
                $group_name,
                controle_recurrents_id,
                ville,
                rue,
                numero,
                nom,
                telephone,
                mail,
                date_controle,
                resultat,
                $type_alias,
                observations
            ");
        $this->db->join('t_controle_recurrents_group as t_group', $table . '.controle_recurrents_group_id=t_group.group_id', 'left');

        if ($this->uri->segment(2) == 'group_json' && $this->uri->segment(4) != null) {
            $this->db->where('t_group.name = "' . $group . '"');
        } else {
            //$this->db->where($table . '.controle_recurrents_group_id = "0"');
        }

        if ($type != "") {
            $this->db->where('t_group.type', $type);
        }

        switch ($void) {
            case 'archiver':
                $this->db->where($table.'.inactive is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.deleted is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.inactive is NULL');
                $this->db->where($table.'.deleted is NULL');
                break;
        }

        $id = intval($void);

        if($id > 0) {
            $this->db->where('controle_recurrents_id', $id);
        }

        $this->db->stop_cache();
        $table = 't_controle_recurrents';

        // aliases
        $aliases = array(
            'group_name' => 't_group.name',
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->controle_recurrents_id . '">';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'controle_recurrents_id' => 'int',
            'group_name'             => 'char',
            'ville'                  => 'char',
            'rue'                    => 'char',
            'numero'                 => 'int',
            'nom'                    => 'char',
            'telephone'              => 'char',
            'mail'                   => 'char',
            'date_controle'          => 'date',
            'resultat'               => 'char',
            'type'                   => 'char',
            'observations'           => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Livraisons insert into t_controle_recurrents table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_controle_recurrents', $data);
    }

    /******************************
     * Detail d'une livraisons
     ******************************/
    public function detail($id)
    {
        $table = "t_controle_recurrents";
        $type  = $table . ".type";
        $this->db->select("
                controle_recurrents_id,
                controle_recurrents_group_id,
                t_group.name as group_name,
                t_group.type as group_type,
                ville,
                rue,
                numero,
                nom,
                telephone,
                mail,
                date_controle,
                resultat,
                $type,
                observations
            ");

        $this->db->join('t_controle_recurrents_group as t_group', $table . '.controle_recurrents_group_id=t_group.group_id', 'left');
        $this->db->where('controle_recurrents_id', $id);
        $q = $this->db->get($table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating livraisons data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_controle_recurrents', $data, $id, 'controle_recurrents_id');
    }

    /******************************
     * Archive livraisons data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_controle_recurrents', $id, 'controle_recurrents_id', 'inactive');
    }

    /******************************
     * Archive livraisons data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_controle_recurrents', $id, 'controle_recurrents_id', 'deleted');
    }

    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        $this->db->where('controle_recurrents_id', $id);
        $q = $this->db->update('t_controle_recurrents', $data);
    }

    public function commande($controle_recurrents_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tcr.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tcr.controle_recurrents_id = "' . $controle_recurrents_id . '"');
        $q = $this->db->get('t_controle_recurrents tcr');
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

    public function resultat_option()
    {
        $result = array();
        $values = array('fait', 'pas fait', 'non contrôlé');
        return $this->form_option($values);
    }

    public function type_option()
    {
        $result = array();
        $values = array('appel', 'terrain', 'mail');
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

    public function list_client()
    {
        $this->db->select("ctc_id, ctc_nom");

        $session = $this->session->userdata;
        if ($session['profil'] == 'Client') {
            $this->db->where('ctc_id = ' . $session['id']);
        }

        $q   = $this->db->order_by('ctc_nom');
        $q   = $this->db->get('t_contacts');
        $res = $q->result();
        return $res;
    }

    public function list_permanent($mode = null, $client_id = null)
    {
        $this->db->select("*");
        $this->db->where('type', "permanent");

        if ($client_id) {
            $this->db->where('client', $client_id);
        }

        if ($mode == 'valides') {
            $this->db->where('valid', true);
        } else if ($mode == 'nonvalides') {
            $this->db->where('valid', false);
        }

        $this->db->where('inactive IS NULL');
        $this->db->where('deleted IS NULL');

        $q   = $this->db->get('t_controle_recurrents_group');
        $res = $q->result();

        return $res;
    }

    public function list_ponctuels($mode = null, $client_id = null)
    {
        $this->db->select("*");
        $q = $this->db->where('type', "ponctuel");

        if ($client_id) {
            $this->db->where('client', $client_id);
        }

        if ($mode == 'valides') {
            $this->db->where('valid', true);
        } else {
            $this->db->where('valid', false);
        }

        $q   = $this->db->get('t_controle_recurrents_group');
        $res = $q->result();

        return $res;
    }

    public function is_valides($id)
    {
        $table = "t_controle_recurrents";
        $this->db->select("controle_recurrents_id");
        $this->db->where('controle_recurrents_id', $id);
        $this->db->where('t_group.valid', true);

        $this->db->join('t_controle_recurrents_group as t_group', $table . '.controle_recurrents_group_id=t_group.group_id', 'left');
        $q = $this->db->get($table);

        $res = $q->result();
        return count($res);
    }

    /**
     * LISTE GROUP
     */
    public function liste_group($void, $limit = 10, $offset = 1, $filters = null, $type_name = "", $ordercol = 2, $ordering = "asc", $option = "")
    {
        $table         			= 't_controle_recurrents_group';
        $client        			= "ctc_nom";
        $client_name   			= $client . " AS client_name";
        $commande      			= "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name 			= $commande . " AS commande_name";
        $valides       			= "CASE WHEN " . $table . ".valid = 1 THEN 'Valides' ELSE 'Non-Valides' END";
		$date_controle_ponctuel	= formatte_sql_date("date_controle_ponctuel");

        $this->db->start_cache();
        $this->db->select($table . ".*,group_id as RowID,
            'checkbox' as checkbox,
            $client_name,
            $commande_name,
            $valides as valides,
			date_controle_ponctuel
        ");

        $this->db->join('t_contacts as tc', 'tc.ctc_id=client', 'left');
        $this->db->join('t_commandes as tm', 'tm.cmd_id=commande', 'left');

        switch ($void) {
            case 'archived':
                $this->db->where('inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where('deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where('inactive is NULL');
                $this->db->where('deleted is NULL');
                break;
        }

        if ($type_name != "") {
            $this->db->where($table . '.type', $type_name);
        }



        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'   => "ctc_nom",
            'commande_name' => $commande,
            'valides'       => $valides,
        );

        $resultat = $this->_filtre($table, $this->liste_group_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add another feature to the list like checkbox, upload file, view message
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $data           = $resultat['data'][$i];
            $data->checkbox = '<input type="checkbox" name="ids[]" value="' . $data->group_id . '">';
            $data->name     = '<a href="' . site_url('controle_recurrents') . '/group/' . $data->type . '/' . $data->name . '">' . $data->name . '</a>';
        }

        return $resultat;
    }

    public function liste_group_filterable_columns()
    {
        $filterable_columns = array(
            'group_id'      => 'int',
            'name'          => 'char',
            'client_name'   => 'char',
            'commande_name' => 'char',
			'date_controle_ponctuel' => 'date'
        );

        return $filterable_columns;
    }

    public function get_group($criteria)
    {
        $query = $this->db->get_where('t_controle_recurrents_group', $criteria);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function set_controle_permanent()
    {
        $table = "t_controle_recurrents_group";

        $date     = $this->input->post('date');
        $client   = $this->input->post('client');
        $commande = $this->input->post('commande');
        $name     = $this->input->post('name');
        $type     = $this->input->post('type');

        $data = array(
            'client'   => $client,
            'commande' => $commande,
            'name'     => $name,
            'type'     => $type,
            'date'     => $date,
        );

        //check if group exist
        $this->db->select("name");
        $this->db->where('name = "' . $name . '"');
        $this->db->where('type = "' . $type . '"');
        $q = $this->db->get('t_controle_recurrents_group');
        if ($q->num_rows() == 0) {
            //add new group
            return $this->_insert('t_controle_recurrents_group', $data);
        } else {
            return false;
        }
    }

    public function set_controle_ponctuel()
    {
        $client                 = $this->input->post('client');
        $controle_permanent_id  = $this->input->post('controle_permanent');
        $name                   = $this->input->post('name');
        $date_controle_ponctuel = formatte_date_to_bd($this->input->post('date_controle_ponctuel'));
        $type_ponctuel          = $this->input->post('type_ponctuel');
        $date                   = formatte_date_to_bd($this->input->post('date'));
        $type                   = $this->input->post('type');

        $data = array(
            'client'                 => $client,
            'controle_permanent_id'  => $controle_permanent_id,
            'name'                   => $name,
            'date_controle_ponctuel' => $date_controle_ponctuel,
            'type_ponctuel'          => $type_ponctuel,
            'date'                   => $date,
            'type'                   => $type,
        );

        //check if group exist
        $this->db->select("*");
        $this->db->where('name = "' . $name . '"');
        $this->db->where('type = "' . $type . '"');
        $q = $this->db->get('t_controle_recurrents_group');
		
        if ($q->num_rows() == 0) {
            //add new group
            $group_id = $this->_insert('t_controle_recurrents_group', $data);
        } else {
			$group		= $q->result();
            $group_id 	= $group[0]->group_id;
        }

        $q = $this->db->query(
            "INSERT INTO t_controle_recurrents (
                controle_recurrents_id,
                ville,
                addresse_exacte,
                rue,
                numero,
                nom,
                telephone,
                mail,
                controle_recurrents_group_id,
                date_controle,
                type
            )
            SELECT
                '',
                ville,
                addresse_exacte,
                rue,
                numero,
                nom,
                telephone,
                mail,
                '" . $group_id . "',
        '" . $date_controle_ponctuel . "',
        '" . $type_ponctuel . "'
            FROM t_controle_recurrents WHERE controle_recurrents_group_id = '" . $controle_permanent_id . "'");

        return TRUE;
    }

    public function valider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('group_id = "' . $group->group_id . '"');
            $this->db->update('t_controle_recurrents_group', array('valid' => true));

            return true;
        } else {
            return false;
        }
    }

    public function revalider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('group_id = "' . $group->group_id . '"');
            $this->db->update('t_controle_recurrents_group', array('valid' => false));

            return true;
        } else {
            return false;
        }
    }

    public function is_group_valid($criteria)
    {
        $this->db->select("group_id");        
        $this->db->where('valid', true);
        $q = $this->db->get_where('t_controle_recurrents_group', $criteria);
        $res = $q->result();
        return count($res);
    }

    /******************************
    * Archive Group data
    ******************************/
    public function archive_group($id) {
        return $this->_delete('t_controle_recurrents_group',$id,'group_id','inactive');
    }

    /******************************
     * Remove Group data
     ******************************/
    public function remove_group($id)
    {
        return $this->_delete('t_controle_recurrents_group', $id, 'group_id', 'deleted');
    }
    /******************************
     * Unremove Group data
     ******************************/
    public function unremove_group($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        $this->db->where('group_id', $id);
        $q = $this->db->update('t_controle_recurrents_group', $data);
    }
}
// EOF
