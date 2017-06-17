<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_newsous_traitants extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /******************************
     * Liste des actions

     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {

        // première partie du select, mis en cache
        $this->db->start_cache();

        $query = $this->db->query('SET @serial=0;');
        //$this->db->query('SET @serial=0;');
        // lecture des informations
        //$numero = formatte_sql_lien('newsous_traitants/detail','sous_traitants_id','societe');

        $client = formatte_sql_lien('contacts/detail', 'ctc_id', 'ctc_nom');
        $villes = formatte_sql_lien('villes/detail', 'vil_id', 'vil_nom');

        //$employee = formatte_sql_lien('list_utilisateurs/detail','utl_id','emp_nom');
        $societe_name = "scv_nom as societe_name";
        $this->db->select("@serial := @serial+1 AS sno,sous_traitants_id,sous_traitants_id as RowID,$societe_name,$client,$villes,pavillons,total_distribuer,residences,hlm,type_doc,type_client,prix_max,date_limite,    semaine_prevue,emp_nom,tel_sous_traitant,mail", false);

        $this->db->join('t_villes', 'vil_id=ville', 'left');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_employes', 'emp_id=sous_traitant_demande', 'left');
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
        $this->db->where('sous_inactif is null');

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('sous_traitants_id', $id);
        }

        //$this->db->order_by("numero asc");
        $this->db->stop_cache();
        $table = 't_sous_traitants';

        // aliases
        $aliases = array(
            'societe_name' => 'scv_nom'
        );
        $ordercol = 'sous_traitants_id';
        $ordering = 'desc';
        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'societe_name'           => 'char',
            'ctc_nom'           => 'char',
            'prix_max'          => 'char',
            'vil_nom'           => 'char',
            'pavillons'         => 'char',
            'total_distribuer'  => 'char',
            'residences'        => 'char',
            'hlm'               => 'char',
            'type_doc'          => 'char',
            'type_client'       => 'char',
            'date_limite'       => 'char',
            'semaine_prevue'    => 'char',
            'emp_nom'           => 'char',
            'tel_sous_traitant' => 'char',
            'mail'              => 'char',
        );
        return $filterable_columns;
    }

    /******************************
     * Détail d'un catalogue
     ******************************/
    public function detail($id)
    {

        // lecture des informations
        $this->db->select("sous_traitants_id,scv_nom as societe_name,ctc_nom,vil_nom,pavillons,total_distribuer,residences,hlm,type_doc,type_client,prix_max,date_limite,   semaine_prevue,emp_nom,tel_sous_traitant,mail", false);
        $this->db->join('t_villes', 'vil_id=ville', 'left');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_employes', 'emp_id=sous_traitant_demande', 'left');
        $this->db->join('t_societes_vendeuses','scv_id=societe','left');
        $this->db->where('sous_traitants_id', $id);
        $this->db->where('sous_inactif is null');
        $q = $this->db->get('t_sous_traitants');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }
    public function edit_detail($id)
    {

        // lecture des informations
        $q = $this->db->get_where('t_sous_traitants', array('sous_traitants_id' => $id));
        if ($q->num_rows() > 0) {
            $query = $q->row();
            return $query;
        } else {
            return null;
        }
    }

    public function sous_form($void)
    {

        // $this->db->insert('t_bornes', $void);
        $data = $this->db->insert('t_sous_traitants', $void);
        return $this->db->insert_id();
    }
    public function sous_editform($void, $id)
    {

        $this->db->set($void);
        $this->db->where("sous_traitants_id", $id);
        $data = $this->db->update("t_sous_traitants", $void);
        return $data;
    }
    public function ville_list($id)
    {

        $this->db->select('vil_id, vil_nom');
        $query  = $this->db->get('t_villes');
        $data[] = "<option value=''>sélectionner</option>";
        foreach ($query->result() as $row) {

            $vil_id = $row->vil_id;
            if ($id == $vil_id && $id != "") {$selected = 'selected';} else { $selected = '';}

            $data[] = "<option value=" . $row->vil_id . "  " . $selected . ">" . $row->vil_nom . "</option>";
        }

        return $data;
    }
    public function dupliquer($id)
    {
        $this->load->model(array('m_newsous_traitants'));

        $row = $this->db->get_where('t_sous_traitants', array('sous_traitants_id' => $id))->row_array();

        if ($row) {
            $random_id = mt_rand(10, 100);

            unset($row['sous_traitants_id']);
            $row['societe']      = $row['societe'] . "-copy" . $random_id;
            $row['sous_inactif'] = null;
            $id                  = $this->m_newsous_traitants->nouveau($row);

            if ($id) {
                return true;
            }

            return false;

        }

        return false;
    }
    public function nouveau($data)
    {
        $id = $this->_insert('t_sous_traitants', $data);
        return $id;
    }

    public function client_list($id)
    {
        $this->db->select('ctc_id, ctc_nom');
        $query  = $this->db->get('t_contacts');
        $data[] = "<option value=''>sélectionner</option>";
        foreach ($query->result() as $row) {

            $ctc_id = $row->ctc_id;
            if ($id == $ctc_id && $id != "") {$selected = 'selected';} else { $selected = '';}

            $data[] = "<option value=" . $row->ctc_id . "  " . $selected . ">" . $row->ctc_nom . "</option>";
        }

        return $data;
    }
    
    public function employe_list($id)
    {
        $this->db->select('emp_id, emp_nom');
        $query  = $this->db->get('t_employes');
        $data[] = "<option value=''>sélectionner</option>";
        foreach ($query->result() as $row) {

            $emp_id = $row->emp_id;
            if ($id == $emp_id && $id != "") {$selected = 'selected';} else { $selected = '';}

            $data[] = "<option value=" . $row->emp_id . "  " . $selected . ">" . $row->emp_nom . "</option>";
        }

        return $data;
    }

    public function employee_data($id)
    {

        $data  = array();
        $query = $this->db->get_where('t_employes', array('emp_id' => $id));
        foreach ($query->result() as $row) {
            $data[0] = $row->emp_telephone1;
            $data[1] = $row->emp_email;

        }
        return $data;
    }

    public function suppression($id)
    {
        $void = array(
            'sous_inactif' => date("d-m-Y h:i:sa"),
        );
        $this->db->set($void);
        $this->db->where("sous_traitants_id", $id);
        $data = $this->db->update("t_sous_traitants", $void);

    }

}
// EOF
