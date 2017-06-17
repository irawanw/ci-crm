<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_objectif extends MY_Model
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
                array('objectifs_id', 'ref', "objectif#", 'objectif', 'objectifs_id', 'objectifs_id'),
                array('titre', 'text', "Titre", 'titre'),
                array('nature', 'text', "Nature", 'nature'),
                array('date_limite', 'date', "Date Limite", 'date_limite'),
                array('resultat_date_limite', 'date', "Résultat a date limite", 'resultat_date_limite'),
            ),
            'write' => array(
                'titre'                => array("Titre", 'text', 'titre', false),
                'nature'               => array("Nature", 'text', 'nature', false),
                'date_limite'          => array("Date Limite", 'date', 'date_limite', false),
                'resultat_date_limite' => array("Résultat Date Limite", 'date', 'resultat_date_limite', false),
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste message list Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        // première partie du select, mis en cache
        $this->db->start_cache();

        $objectifs_id         = formatte_sql_lien('objectif/detail', 'objectifs_id', 'objectifs_id');
        $date_limite          = formatte_sql_date("date_limite");
        $resultat_date_limite = formatte_sql_date("resultat_date_limite");
        $this->db->select("*, objectifs_id as RowID,objectifs_id as checkbox, titre, nature, date_limite, resultat_date_limite");

        switch ($void) {
            case 'archived':
                $this->db->where('inactive is NOT NULL');
                break;
            case 'deleted':
                $this->db->where('deleted is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where('inactive is NULL');
                $this->db->where('deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('objectifs_id', $id);
        }

        $this->db->stop_cache();
        $table = 't_objectif';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->objectifs_id.'">';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'objectifs_id'         => 'int',
            'titre'                => 'char',
            'nature'               => 'int',
            'date_limite'          => 'date',
            'resultat_date_limite' => 'date',
        );

        return $filterable_columns;
    }

    /******************************
     * New Message list insert into t_objectif table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_objectif', $data);
    }

    /******************************
     * Detail d'une message list
     ******************************/
    public function detail($id)
    {
        $this->db->select("*");
        $this->db->where('objectifs_id = "' . $id . '"');
        $q = $this->db->get('t_objectif');
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
        return $this->_update('t_objectif', $data, $id, 'objectifs_id');
    }

    /******************************
     * Archive message list data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_objectif', $id, 'objectifs_id', 'inactive');
    }

    /******************************
     * Archive message list data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_objectif', $id, 'objectifs_id', 'deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null);
        return $this->_update('t_objectif',$data, $id,'objectifs_id');
    }
}
// EOF
