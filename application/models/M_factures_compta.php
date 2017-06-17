<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_factures_compta extends MY_Model
{

    protected $_table = "t_factures_compta";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('factures_compta_id', 'ref', "id#", 'factures_compta_id', 'factures_compta_id', 'factures_compta_id'),
                array('nom_fournisseur', 'text', 'Nom Fournisseur', 'nom_fournisseur'),
                array('intitule_sur_compte', 'text', 'intitulé sur compte', 'intitule_sur_compte'),
                array('montant_debit', 'text', 'Montant débit', 'montant_debit'),
                array('date_debit', 'date', 'date débit', 'date_debit'),
                array('type', 'text', 'type', 'type'),
                array('lien_vers_la_facture', 'text', 'lien vers la facture', 'lien_vers_la_facture'),
                array('facture', 'text', 'facture', 'facture'),
            ),
            'write' => array(
                'nom_fournisseur'      => array('Nom Fournisseur', 'text', 'nom_fournisseur', false),
                'intitule_sur_compte'  => array('intitulé sur compte', 'text', 'intitule_sur_compte', false),
                'montant_debit'        => array('Montant débit', 'text', 'montant_debit', false),
                'date_debit'           => array('date débit', 'date', 'date_debit', false),
                'type'                 => array('type', 'select', array('type', 'id', 'value'), false),
                'lien_vers_la_facture' => array('lien vers la facture', 'text', 'lien_vers_la_facture', false),
                'facture'              => array('facture', 'multiple-upload', 'facture', false),
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table                = $this->_table;
        $date_debit           = formatte_sql_date("date_debit");
        $lien_vers_la_facture = "CONCAT('<a target=_blank href=', lien_vers_la_facture ,'>',lien_vers_la_facture,'</a>') as lien_vers_la_facture";
        $facture              = "GROUP_CONCAT('<button data-id=',file_id,' class=\"btn btn-warning btn-xs btn-delete-file\">x</button><a target=_blank href=" . base_url('fichiers/factures_compta') . "/',filename,'>',filename,'</a>' SEPARATOR '<br />') as facture";
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select($table . ".*, factures_compta_id as RowID, factures_compta_id as checkbox, date_debit, $lien_vers_la_facture, $facture");
        $this->db->join('t_files', $table . '.factures_compta_id=t_files.row_id AND t_files.name="facturescompta_facture"', 'LEFT');
        $this->db->group_by("factures_compta_id");

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

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('factures_compta_id', $id);
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(
        	
        );

        /*$resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);*/
        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        foreach ($resultat['data'] as $row) {

            if ($row->facture == "") {
                $row->facture = '<a class="btn-upload-file" href="#" data-id="' . $row->factures_compta_id . '">Telecharger</a>';
            }

            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->factures_compta_id . '">';
        }

        /*for($i=0; $i < count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->purchase_id.'">';
		}*/

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'factures_compta_id'   => 'int',
            'nom_fournisseur'      => 'char',
            'intitule_sur_compte'  => 'char',
            'montant_debit'        => 'int',
            'date_debit'           => 'date',
            'type'                 => 'char',
            'lien_vers_la_facture' => 'char',
            //'facture'              => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Message list insert into t_factures_compta table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert($this->_table, $data);
    }

    /******************************
     * Detail d'une test mails
     ******************************/
    public function detail($id)
    {
        $this->db->select("*");
        $this->db->where('factures_compta_id = "' . $id . '"');
        $q = $this->db->get($this->_table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
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
        return $this->_update($this->_table, $data, $id, 'factures_compta_id');
    }

    /**
     * Upload Files
     */
    public function upload_files($filename, $id)
    {
        $table      = "t_factures_compta";
        $field_name = "facture";
        $key        = "factures_compta_id";

        $query = $this->db->select($field_name)->get_where($table, array($key => $id));

        if ($query->row()) {
            $row       = $query->row();
            $files     = $row->$field_name;
            $new_files = $files == "" ? $filename : $files . "," . $filename;
            $data      = array($field_name => $new_files);
            return $this->maj($data, $id);
        } else {
            return false;
        }
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function archive($id)
    {
        return $this->_delete($this->_table, $id, 'factures_compta_id', 'inactive');
    }

    /******************************
     * UnArchive test mails data
     ******************************/
    public function unarchive($id)
    {
        $data = array('inactive' => null);
        return $this->_update($this->_table, $data, $id, 'factures_compta_id');
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function remove($id)
    {
        return $this->_delete($this->_table, $id, 'factures_compta_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update($this->_table, $data, $id, 'factures_compta_id');
    }

    public function hard_remove($id)
    {
        return $this->_delete($this->_table, $id, 'factures_compta_id');
    }

    public function liste_option_type()
    {
        $options = array(
            'chèque',
            'espèces',
            'virement',
            'prélèvement',
            'CB',
        );

        return $this->form_option($options);
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

    public function get_facture_files($id)
    {
        $query = $this->db->select('*')
            ->get_where('t_files', array('row_id' => $id, 'name' => 'facturescompta_facture'));
        $resultat = $query->result();
        return $resultat;
    }
}
// EOF
