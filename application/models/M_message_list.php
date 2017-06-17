<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_message_list extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->filterable_columns = array(
            'message_list_id'       => 'int',
            'name'                  => 'char',
            'message'               => 'char',
            'object'                => 'char',
            'type'                  => 'char',
            'lien_pour_telecharger' => 'char',
            'famille_name'          => 'char',
            'societe_name'          => 'char',
            'salesman_name'         => 'char',
            'telephone'             => 'char',
            'email'                 => 'char',
            'segment_name'          => 'char',
            'segment_number'        => 'int',
            'department'            => 'char',
            'region'                => 'char',
            'country'               => 'char',
            'activities'            => 'char',
            'origin'                => 'char',
            'software'              => 'char',
            'client_name'           => 'char',
            'produit_vendu'         => 'char',
            'database'              => 'char',
        );
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('message_list_id', 'ref', "message_list#", 'message_list', 'message_list_id', 'message_list_id'),
                array('name', 'text', "Name", 'name'),
                array('type', 'text', "Type", 'type'),
                array('message', 'textarea', "Message", 'message_list_id', 'message'),
                array('object', 'text', "Object", 'object'),
                array('lien_pour_telecharger', 'text', "Lien Pour Telecharger", 'lien_pour_telecharger'),
                array('famille_name', "text", "Famille D'Articles", 'famille_name'),
                array('societe_name', "text", "Societe", 'societe_name'),
                array('salesman_name', 'text', "Salesman", 'salesman_name'),
                array('telephone', 'text', "Telephone", 'telephone'),
                array('email', 'text', "Email", 'email'),
                array('segment_name', 'text', "Segment Name", 'segment_name'),
                array('segment_number', 'text', "Segment Number", 'segment_number'),
                array('department', 'text', "Department", 'department'),
                array('region', 'text', "Region", 'region'),
                array('country', 'text', "Country", 'country'),
                array('activities', 'text', "Activities", 'activities'),
                array('origin', 'text', "Origin", 'origin'),
                array('software_nom', 'text', "Software", 'software'),
                array('client_name', 'text', "Client", 'client_name'),
                array('produit_vendu', 'text', "Produit Vendu", 'produit_vendu'),
                array('database', 'text', "Database", 'database'),
            ),
            'write' => array(
                'name'                  => array("Name", 'text', 'name', false),
                'type'                  => array("Type", 'select', array('type', 'id', 'value'), false),
                'message'               => array("Message", 'textarea', 'message', false),
                'object'                => array("Object", 'text', 'object', false),
                'lien_pour_telecharger' => array("Lien Pour Telecharger", 'text', 'lien_pour_telecharger', false),
                'famille_darticles'     => array("Famille D'Articles", 'select', array('famille_darticles', 'cat_id', 'vfm_famille'), false),
                'societe'               => array("Societe", 'select', array('societe', 'scv_id', 'scv_nom'), false),
                'salesman'              => array("Salesman", 'select', array('salesman', 'utl_employe', 'emp_nom'), false),
                'telephone'             => array("Telephone", 'text', 'telephone', false),
                'email'                 => array("Email", 'text', 'email', false),
                'segment_name'          => array("Segment Name", 'text', 'segment_name', false),
                'segment_number'        => array("Segment Number", 'text', 'segment_number', false),
                'department'            => array("Department", 'text', 'department', false),
                'region'                => array("Region", 'select', array('region','id','value'), false),
                'country'               => array("Country", 'text', 'country', false),
                'activities'            => array("Activities", 'text', 'activities', false),
                'origin'                => array("Origin", 'text', 'origin', false),
                'software'              => array("Software", 'select', array('software','id','value'), false),
                'client'                => array("Client", 'select', array('client', 'id', 'value'), false),
                'produit_vendu'         => array("Produit Vendu", 'text', 'produit_vendu', false),
                'database'              => array("Database", 'text', 'database', false),
            )
        );

        return $champs[$type];
    }
    /******************************
     * Liste message list Data
     ******************************/
    public function liste($void = 0, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_message_list';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $salesman              = "te.emp_nom";
        $salesman_name         = $salesman . " AS salesman_name";
        $famille               = "vf.vfm_famille";
        $famille_name          = $famille . " AS famille_name";
        $societe               = "ts.scv_nom";
        $societe_name          = $societe . " AS societe_name";
        $software              = "tss.software_nom";
        $software_nom         = $software . " AS software_nom";
        $message               = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',message_list_id,'\" data-message=\"',message,'\">','Voir Message','</a>') as message";
        $lien_pour_telecharger = "CONCAT('<a href=\"',lien_pour_telecharger,'\" target=\"_blank\">',lien_pour_telecharger,'</a>') as lien_pour_telecharger";
        $client                = "tco.ctc_nom";
        $client_name           = $client . " AS client_name";

        $this->db->select("*
            , message_list_id as checkbox, message_list_id as RowID,
            , $lien_pour_telecharger
            , $message
            , te.emp_nom as salesman_name
            , vf.vfm_famille as famille_name
            , ts.scv_nom as societe_name
            , $client_name
            , $software_nom  
        ");
        $this->db->join('t_utilisateurs as tu', 'salesman = tu.utl_id', 'left');
        $this->db->join('t_employes as te', 'te.emp_id = tu.utl_id', 'left');
        $this->db->join('t_catalogues as tc', 'tc.cat_id = famille_darticles', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = societe', 'left');
        $this->db->join('t_contacts as tco', 'tco.ctc_id=client', 'left');
        $this->db->join('t_softwares as tss', 'tss.software_id=software', 'left');

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
            $this->db->where('message_list_id', $id);
        }
        // aliases
        $aliases = array(
            'salesman_name' => $salesman,
            'famille_name'  => $famille,
            'societe_name'  => $societe,
            'client_name'   => $client,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        //$this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->message_list_id . '">';
        }

        return $resultat;
    }

    public function simple_list()
    {
        $this->db->select("*");
        $this->db->order_by('message_list_id', 'ASC');
        $this->db->where('inactive is NULL');
        $this->db->where('deleted is NULL');
        return $this->db->get('t_message_list')->result();
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'message_list_id'       => 'int',
            'name'                  => 'char',
            'message'               => 'char',
            'object'                => 'char',
            'type'                  => 'char',
            'lien_pour_telecharger' => 'char',
            'famille_name'          => 'char',
            'societe_name'          => 'char',
            'salesman_name'         => 'char',
            'telephone'             => 'char',
            'email'                 => 'char',
            'segment_name'          => 'char',
            'segment_number'        => 'int',
            'department'            => 'char',
            'region'                => 'char',
            'country'               => 'char',
            'activities'            => 'char',
            'origin'                => 'char',
            'software_nom'          => 'char',
            'client_name'           => 'char',
            'produit_vendu'         => 'char',
            'database'              => 'char',
        );

        return $filterable_columns;
    }

    public function get_filterable_columns()
    {
        return $this->filterable_columns;
    }

    /******************************
     * New Message list insert into t_message_list table
     ******************************/
    public function nouveau($data)
    {
        $id = $this->_insert('t_message_list', $data);
        return $id;

    }

    /******************************
     * Detail d'une message list
     ******************************/
    public function detail($id)
    {
        $this->db->select("*
            , vfm_famille as famille_darticles_name
            , scv_nom as societe_name
            , emp_nom as commercial_name
        ");
        $this->db->join('t_utilisateurs as tu', 'salesman = tu.utl_id', 'left');
        $this->db->join('t_employes as te', 'te.emp_id = tu.utl_id', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = societe', 'left');
        $this->db->where('message_list_id = "' . $id . '"');
        $q = $this->db->get('t_message_list as tpj');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating message list data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_message_list', $data, $id, 'message_list_id');
    }

    /******************************
     * Archive message list data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_message_list', $id, 'message_list_id', 'inactive');
    }

    /******************************
     * Archive message list data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_message_list', $id, 'message_list_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_message_list', $data, $id, 'message_list_id');
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

    public function type_option()
    {
        $result = array();
        $values = array('text', 'html');
        return $this->form_option($values);
    }

    public function societe_option()
    {
        $this->db->flush_cache();
        $this->db->reset_query();
        $this->db->select("*");
        $this->db->order_by('scv_nom', 'ASC');
        $res = $this->db->get('t_societes_vendeuses')->result();
        return $res;
    }

    public function region_liste_option()
    {
        $options = array(
            'Toutes',
            'Auvergne-Rhone-Alpes',
            'Bourgogne-Franche-Comte',
            'Bretagne',
            'Centre-Val de Loire',
            'Corse',
            'France',
            'Grand-Est',
            'Hauts-de-France',
            'IDF',
            'Ile-de-France',
            'Normandie',
            'Nouvelle-Aquitaine',
            'Occitanie',
            'Pays de la Loire',
            "Provence-Alpes-Cote d'Azur (PACA)",
        );

        return $this->form_option($options);
    }

    public function software_option()
    {
        return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }
}
// EOF
