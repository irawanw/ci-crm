<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_gestion_heures extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read.parent'  => array(
                array('checkbox', 'text', "checkbox", 'checkbox'),
                array('gestion_group_id', 'text', "Gestion#", 'gestion_group_id'),
                array('controle', 'text', "Feuilles d'heures", 'controle'),
                array('employee_name', 'text', "Employes", 'employee_name'),
                array('annee', 'text', "Annee", 'annee'),
                array('mois', 'text', "Mois", 'mois'),
                array('valides', 'text', "Valide", 'valides'),
            ),
            'read.child'   => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('gestion_heures_id', 'ref', "Gestion heures#", 'gestion_heures', 'gestion_heures_id', 'gestion_heures_id'),
                array('ville', 'text', "Ville", 'ville'),
                array('urbain', 'number', 'Urbain', 'urbain'),
                array('heures_de_distribution_urbain', 'number', 'Heures De Distribution', ''),
                array('rural', 'number', "Rural", 'rural'),
                array('heures_de_distribution_rural', 'number', 'Heures De Distribution', ''),
                array('controle_autres', 'text', "Heures de controle ou autres", 'controle_autres'),
                array('kilometres', 'number', "Kilometres", 'kilometres'),
                array('frais_kilometres', 'number', "Frais kilométriques", 'frais_kilometres'),
            ),
            'write.parent' => array(
                //'employes' => array("Employes",'select',array('employes','emp_id','emp_nom'),false),
                //'annee' => array("Annee",'text','annee',false),
                //'mois' => array("Mois",'text','mois',false),
                'ville'           => array("Ville", 'text', 'ville', false),
                'urbain'          => array("Urbain", 'text', 'urbain', false),
                'rural'           => array("Rural", 'text', 'rural', false),
                'controle_autres' => array("Heures de controle ou autres", 'text', 'controle_autres', false),
                'kilometres'      => array("Kilometres", 'text', 'kilometres', false),
            ),
            'write.child' => array()
        );

        if ($data != null) {
            $result = $champs[$type . "." . $data];
        } else {
            $result = array_merge($champs[$type . ".parent"], $champs[$type . ".child"]);
        }

        return $result;
    }

    /******************************
     * Liste Livraisons Data
     ******************************/
    public function liste($void, $group = null, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_gestion_heures';
        // première partie du select, mis en cache
        $this->db->start_cache();

        $employes         = "te.emp_nom";
        $employee_name    = $employes . " AS employee_name";
        $frais_kilometres = "CASE WHEN
            gestion_heures_group = 0 THEN TRUNCATE(0, 2)
            ELSE TRUNCATE(kilometres * te.emp_indemnite_kilometrique, 2)
        END";
        $frais_kilometres_alias        = $frais_kilometres . " AS frais_kilometres";
        $heures_de_distribution_urbain = "CASE WHEN
            gestion_heures_group = 0 THEN TRUNCATE(0, 2)
            ELSE TRUNCATE(urbain / tgp.urbain_div, 2)
        END";
        $heures_de_distribution_urbain_alias = $heures_de_distribution_urbain . " AS heures_de_distribution_urbain";
        $heures_de_distribution_rural        = "CASE WHEN
            gestion_heures_group = 0 THEN TRUNCATE(0, 2)
            ELSE TRUNCATE(rural/tgp.rural_div, 2)
        END";
        $heures_de_distribution_rural_alias = $heures_de_distribution_rural . " AS heures_de_distribution_rural";

        $this->db->select("
                gestion_heures_id as checkbox,gestion_heures_id as RowID,
                gestion_heures_id,
                $employee_name,
                employes,
                annee,
                mois,
                ville,
                urbain,
                rural,
                controle_autres,
                kilometres,
                $frais_kilometres_alias,
                $heures_de_distribution_urbain_alias,
                $heures_de_distribution_rural_alias
            ");
        $this->db->join('t_gestion_group as tgp', 'gestion_heures_group=tgp.gestion_group_id', 'left');
        $this->db->join('t_employes as te', 'te.emp_id=tgp.employes', 'left');

        if ($group != null) {
            $group_array = explode("-", $group);
            $employee_id = $group_array[0];
            $annee       = $group_array[1];
            $mois        = $group_array[2];

            if ($employee_id != '') {
                $this->db->where('tgp.employes = "' . $employee_id . '"');
            }

            if ($annee != '') {
                $this->db->where('tgp.annee = "' . $annee . '"');
            }

            if ($mois != '') {
                $this->db->where('tgp.mois = "' . $mois . '"');
            }
        }

        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table . '.deleted is NOT NULL');
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
            $this->db->where('gestion_heures_id', $id);
        }

        // aliases
        $aliases = array(
            'frais_kilometres'              => $frais_kilometres,
            'heures_de_distribution_urbain' => $heures_de_distribution_urbain,
            'heures_de_distribution_rural'  => $heures_de_distribution_rural,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->gestion_heures_id . '">';
        }

        return $resultat;
    }

    public function liste_group($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc", $option = "")
    {
        $table         = 't_gestion_group';
        $employes      = "te.emp_nom";
        $employee_name = $employes . " AS employee_name";
        $valides       = "CASE WHEN " . $table . ".valid = 1 THEN 'Validé' ELSE 'Non validé' END";

        $this->db->start_cache();
        $this->db->select("*,
            'checkbox' as checkbox, gestion_group_id as RowID,
            $employee_name,
            $valides as valides
        ");
        $this->db->join('t_employes as te', 'te.emp_id=t_gestion_group.employes', 'left');

        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table . '.deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table . '.inactive is NULL');
                $this->db->where($table . '.deleted is NULL');
                break;
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'valides'       => $valides,
            'employee_name' => 'emp_nom',
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns2(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add another feature to the list like checkbox, upload file, view message
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $data           = $resultat['data'][$i];
            $data->checkbox = '<input type="checkbox" name="ids[]" value="' . $data->gestion_group_id . '">';
            $data->controle = '<a href="' . site_url('gestion_heures') . '/salarie/' . $data->employes . '/' . $data->annee . '/' . $data->mois . '">' . $data->controle . '</a>';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'gestion_heures_id' => 'int',
            'ville'             => 'char',
            'urbain'            => 'int',
            'rural'             => 'int',
            'kilometres'        => 'int',
        );

        return $filterable_columns;
    }

    public function liste_filterable_columns2()
    {
        $filterable_columns = array(
            'gestion_group_id' => 'int',
            'controle'         => 'char',
            'valides'          => 'char',
            'mois'             => 'int',
            'annee'            => 'int',
            'employee_name'    => 'char',
        );
        return $filterable_columns;
    }

    /******************************
     * New Livraisons insert into t_livraisons table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_gestion_heures', $data);
    }

    /******************************
     * Detail d'une livraisons
     ******************************/
    public function detail($id)
    {
        $this->db->select("
        tg.gestion_heures_id,
        te.emp_id as employes,
        te.emp_nom as employee_name,
        tgp.annee,
        tgp.mois,
        tg.ville,
        tg.urbain,
        tg.rural,
        tg.controle_autres,
        tg.kilometres,
        CASE WHEN
            tg.gestion_heures_group = 0 THEN TRUNCATE(0, 2)
            ELSE TRUNCATE(tg.kilometres/te.emp_indemnite_kilometrique, 2)
        END as frais_kilometres,
        ");

        $this->db->from('t_gestion_heures as tg');
        $this->db->join('t_gestion_group as tgp', 'tg.gestion_heures_group=tgp.gestion_group_id', 'left');
        $this->db->join('t_employes as te', 'te.emp_id=tgp.employes', 'left');
        $this->db->where('gestion_heures_id = "' . $id . '"');
        $q = $this->db->get();
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
        return $this->_update('t_gestion_heures', $data, $id, 'gestion_heures_id');
    }

    /******************************
     * Archive livraisons data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_gestion_heures', $id, 'gestion_heures_id', 'inactive');
    }

    /******************************
     * Archive livraisons data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_gestion_heures', $id, 'gestion_heures_id', 'deleted');
    }

    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        $this->db->where('gestion_heures_id', $id);
        $q = $this->db->update('t_gestion_heures', $data);
    }

    public function valider($data)
    {
        unset($data['filter']);
        $this->db->insert('t_gestion_group', $data);
        $group_id = $this->db->insert_id();

        if ($group_id) {
            $this->db->where('inactive is NULL');
            $this->db->where('deleted is NULL');
            $this->db->update('t_gestion_heures', array('gestion_heures_group' => $group_id));
        }
    }

    public function valides($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $monthNum  = $data['mois'];
            $monthName = date('F', mktime(0, 0, 0, $monthNum, 10)); // March
            $controle  = $group->emp_nom . "-" . $monthName . "-" . $data['annee'];
            $this->db->where('gestion_group_id = "' . $group->gestion_group_id . '"');
            $this->db->update('t_gestion_group', array('valid' => 1, 'controle' => $controle));
        }
    }
    public function revalider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $monthNum  = $data['mois'];
            $monthName = date('F', mktime(0, 0, 0, $monthNum, 10)); // March
            $controle  = $group->emp_nom . "-" . $monthName . "-" . $data['annee'];
            $this->db->where('gestion_group_id = "' . $group->gestion_group_id . '"');
            $this->db->update('t_gestion_group', array('valid' => 0, 'controle' => $controle));
        }
    }

    public function is_valider($id)
    {
        $this->db->select("*");
        $this->db->where('tg.gestion_heures_id = "' . $id . '"');
        $this->db->where('tgp.valid = 1');
        $this->db->join('t_gestion_group as tgp', 'tg.gestion_heures_group=tgp.gestion_group_id', 'left');
        $q   = $this->db->get('t_gestion_heures tg');
        $res = $q->result();
        return count($res);
    }

    public function set_urbain_group($criteria, $urbain_div)
    {

        $group = $this->get_group($criteria);

        if ($group) {
            $data = array('urbain_div' => $urbain_div);
            $this->db->update('t_gestion_group', $data, array('gestion_group_id' => $group->gestion_group_id));
            return true;
        } else {
            return false;
        }
    }

    public function set_rural_group($criteria, $rural_div)
    {
        $group = $this->get_group($criteria);

        if ($group) {
            $data = array('rural_div' => $rural_div);
            $this->db->update('t_gestion_group', $data, array('gestion_group_id' => $group->gestion_group_id));
            return true;
        } else {
            return false;
        }
    }

    public function set_value_group($criteria, $data)
    {
        $group = $this->get_group($criteria);

        if ($group) {
            $this->db->update('t_gestion_group', $data, array('gestion_group_id' => $group->gestion_group_id));
            return true;
        } else {
            return false;
        }
    }

    public function get_group($criteria)
    {
        $query = $this->db->select('t_gestion_group.*, emp_nom')->join('t_employes', 't_gestion_group.employes=t_employes.emp_id')->get_where('t_gestion_group', $criteria);

        return $query->row();
    }

    public function get_indemnite_kilometrique_salarie($employes)
    {
        $resultat = $this->db->get_where('t_employes', array('emp_id' => $employes))->row();

        if ($resultat) {
            return $resultat->emp_indemnite_kilometrique;
        } else {
            return null;
        }
    }

    public function check_group($data)
    {
        $resultat  = $this->db->get_where('t_employes', array('emp_id' => $data['employes']))->row();
        $monthNum  = $data['mois'];
        $monthName = date('F', mktime(0, 0, 0, $monthNum, 10)); // March
        $result    = $this->get_group($data);
        if (!is_object($result)) {
            $data['urbain_div'] = 150;
            $data['rural_div']  = 120;
            $data['controle']   = $resultat->emp_nom . '-' . $monthName . '-' . $data['annee'];
            $group_id           = $this->db->insert('t_gestion_group', $data);
        } else {
            $group_id = $result->gestion_group_id;
        }
        return $group_id;
    }

    public function is_group_valid($id)
    {
        $this->db->select("*");
        $this->db->where('valid = 1');
        $this->db->where('gestion_group_id = ' . $id);
        $q   = $this->db->get('t_gestion_group');
        $res = $q->result();
        return count($res);
    }

    public function get_valider($valides = false)
    {
        $this->db->select("*,emp_nom");
        $this->db->join('t_employes', 't_employes.emp_id=t_gestion_group.employes');
        if ($valides == false) {
            $this->db->where('valid', 0);
        } else {
            $this->db->where('valid', 1);
        }

        $q   = $this->db->get('t_gestion_group');
        $res = $q->result();

        return $res;
    }

    public function get_group_list($param)
    {
        //print_r($param);
        $this->db->select("*");
        if (isset($param['employes']) && $param['employes'] != '' && $param['employes'] != 'tous') {
            $this->db->where('employes', $param['employes']);
        }

        if (isset($param['annee']) && $param['annee'] != '' && $param['employes'] != 'toutes') {
            $this->db->where('annee', $param['annee']);
        }

        if (isset($param['mois']) && $param['mois'] != '' && $param['employes'] != 'tous') {
            $this->db->where('mois', $param['mois']);
        }

        if (isset($param['valid']) && $param['valid'] != '') {
            $this->db->where('valid', $param['valid']);
        }

        $this->db->order_by('employes', 'asc');
        $this->db->order_by('annee', 'desc');
        $this->db->order_by('mois', 'desc');
        $q   = $this->db->get('t_gestion_group');
        $res = $q->result();
        return $res;
    }

    /******************************
     * Archive Group data
     ******************************/
    public function archive_group($id)
    {
        return $this->_delete('t_gestion_group', $id, 'gestion_group_id', 'inactive');
    }

    /******************************
     * Remove Group Data
     ******************************/
    public function remove_group($id)
    {
        return $this->_delete('t_gestion_group', $id, 'gestion_group_id', 'deleted');
    }

    /******************************
     * Unremove Group Data
     ******************************/
    public function unremove_group($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_gestion_group', $data, $id, 'gestion_group_id');
    }

    /**
     * Calculate Indem Kilo
     * @param  [type] $cv [description]
     * @return [type]               [description]
     */
    public function calculate_indem_kilo($employes, $kilometres)
    {
        $row = $this->db->get_where('t_employes', array('emp_id' => $employes))->row();

        $indem_kilo = 0;

        if ($row) {
            $cv = $row->emp_cv_vehicule;
            if ($cv <= 3) {
                $indem_kilo = $this->get_indem_kilo($kilometres, 3);
            } elseif ($cv == 4) {
                $indem_kilo = $this->get_indem_kilo($kilometres, 4);
            } elseif ($cv == 5) {
                $indem_kilo = $this->get_indem_kilo($kilometres, 5);
            } elseif ($cv == 6) {
                $indem_kilo = $this->get_indem_kilo($kilometres, 6);
            } elseif ($cv >= 7) {
                $indem_kilo = $this->get_indem_kilo($kilometres, 7);
            }
        }

        return $indem_kilo;
    }

    public function get_indem_kilo($kilometres, $index)
    {
        $values = array(
            3 => array(0.410, 0.245, 0.286),
            4 => array(0.493, 0.277, 0.323),
            5 => array(0.543, 0.305, 0.364),
            6 => array(0.568, 0.320, 0.382),
            7 => array(0.595, 0.337, 0.401),
        );

        if ($kilometres <= 5000) {
            return $values[$index][0];
        } elseif ($kilometres > 5000 && $kilometres <= 20000) {
            return $values[$index][1];
        } elseif ($kilometres > 20000) {
            return $values[$index][2];
        }
    }

}
// EOF
