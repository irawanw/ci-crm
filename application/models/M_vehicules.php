<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_vehicules extends MY_Model
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
                array('vehicules_id', 'ref', "Vehicules#", 'vehicules', 'vehicules_id', 'vehicules_id'),
                array('marque', 'text', "Marque", 'marque'),
                array('modele', 'text', "Modéle", 'marque'),
                array('annee', 'text', "Année", 'annee'),
                array('immatriculation', 'text', "Immatriculation", 'immatriculation'),
                array('utilisateur_name', 'text', "Utilisateur Habituel", 'utilisateur_name'),
                array('assureur', 'text', "Assureur", 'assureur'),
                array('numero_police', 'text', "Numero Police", 'numero_police'),
                array('propietaire', 'text', "Propietaire", 'propietaire'),
                array('dernier_controle_date', 'date', "Dernier Controle Technique", 'dernier_controle_date'),
                array('prochain_controle_date', 'date', "Prochain Controle Technique", 'dernier_controle_date'),
                array('list_reparation', 'text', "Liste des Réparations", 'list_reparation'),
                array('formule_assurance', 'text', "Formule Assurance", 'formule_assurance'),
                array('prix_annuel_assurance', 'int', "Prix Annuel Assurance", 'prix_annuel_assurance'),
                array('carte_grise', 'picture', "Carte Grise", 'carte_grise', 'carte_grise', 'vehicules_id', 'uploadfile'),
            ),
            'write' => array(
                'marque'                 => array("Marque", 'text', 'marque', false),
                'modele'                 => array("Modele", 'text', 'modele', false),
                'annee'                  => array("Année", 'text', 'annee', false),
                'immatriculation'        => array("Immatriculation", 'text', 'immatriculation', false),
                'assureur'               => array("Assureur", 'text', 'assureur', false),
                'numero_police'          => array("Numero_Police", 'text', 'numero_police', false),
                'propietaire'            => array("Propietaire", 'text', 'propietaire', false),
                'utilisateur'            => array("Utilisateur Habituel", 'select', array('utilisateur', 'emp_id', 'emp_nom'), false),
                'dernier_controle_date'  => array("Dernier Controle Technique", 'date', 'dernier_controle_date', false),
                'prochain_controle_date' => array("Prochain Controle Technique", 'date', 'prochain_controle_date', false),
                'list_reparation'        => array("Liste des Réparations", 'textarea', 'list_reparation', false),
                'formule_assurance'      => array("Formule Assurance", 'text', 'formule_assurance', false),
                'prix_annuel_assurance'  => array("Prix Annuel Assurance", 'text', 'prix_annuel_assurance', false),
                'carte_grise'            => array("Scan carte grise", 'multiple-upload', 'carte_grise', false),
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste Vehicules Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        // première partie du select, mis en cache
        $this->db->start_cache();
        $table = 't_vehicules';

        $vehicules_id           = formatte_sql_lien('vehicules/detail', 'vehicules_id', 'vehicules_id');
        $dernier_controle_date  = formatte_sql_date("dernier_controle_date");
        $prochain_controle_date = formatte_sql_date("prochain_controle_date");
        $utilisateur            = 'te1.emp_nom';
        $utilisateur_name       = $utilisateur . " AS utilisateur_name";
        $carte_grise            = "GROUP_CONCAT('<button data-id=',file_id,' class=\"btn btn-warning btn-xs btn-delete-file\">x</button><a target=_blank href=" . base_url('fichiers/carte_grise') . "/',filename,'>',filename,'</a>' SEPARATOR '<br />') as carte_grise";

        $this->db->select("vehicules_id as RowID,vehicules_id as checkbox, vehicules_id, marque, modele, annee, immatriculation, assureur, numero_police, propietaire, utilisateur, $utilisateur_name, dernier_controle_date, prochain_controle_date, $carte_grise, list_reparation,formule_assurance,prix_annuel_assurance");
        $this->db->join('t_employes as te1', 'te1.emp_id=utilisateur', 'left');
        $this->db->join('t_files', $table . '.vehicules_id=t_files.row_id AND t_files.name="vehicules_carte_grise"', 'LEFT');
        $this->db->group_by("vehicules_id");

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
            $this->db->where('vehicules_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'utilisateur_name' => $utilisateur,

        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        if (count($resultat['data']) > 0) {
            $custom_data = array();

            foreach ($resultat['data'] as $row) {
                if ($row->carte_grise == '') {
                    $row->carte_grise = '<a class="btn-upload-file" href="#" data-id="' . $row->vehicules_id . '">Telecharger</a>';
                }

                $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->vehicules_id . '">';

                $custom_data[] = $row;
            }

            $resultat['data'] = $custom_data;

            //echo print_r($resultat['data']);
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'vehicules_id'           => 'int',
            'marque'                 => 'char',
        	'modele'                 => 'char',
        	'annee'                  => 'int',
            'immatriculation'        => 'char',
            'utilisateur_name'       => 'char',
            'assureur'               => 'char',
            'numero_police'          => 'char',
            'propietaire'            => 'char',
            'dernier_controle_date'  => 'date',
            'prochain_controle_date' => 'date',
            'list_reparation'        => 'char',
            'formule_assurance'      => 'char',
            'prix_annuel_assurance'  => 'int',
            //'carte_grise'            => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Vehicules insert into t_vehicules table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_vehicules', $data);
    }

    /******************************
     * Detail d'une vehicules
     ******************************/
    public function detail($id)
    {
        $this->db->select("tv.vehicules_id, tv.marque, tv.modele, tv.annee, tv.immatriculation, tv.assureur, tv.numero_police, tv.propietaire, tv.utilisateur, tv.dernier_controle_date, tv.prochain_controle_date, tv.list_reparation, tv.formule_assurance, tv.prix_annuel_assurance");
        $this->db->join('t_employes as te1', 'te1.emp_id=utilisateur', 'left');
        $this->db->where('inactive is NULL');
        $this->db->where('vehicules_id = "' . $id . '"');
        $q = $this->db->get('t_vehicules as tv');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating vehicules data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_vehicules', $data, $id, 'vehicules_id');
    }

    /**
     * Upload Files
     */
    public function upload_files($filename, $id)
    {
        $table      = "t_vehicules";
        $field_name = "carte_grise";
        $key        = "vehicules_id";

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
     * Archive vehicules data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_vehicules', $id, 'vehicules_id', 'inactive');
    }

    /******************************
     * Archive vehicules data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_vehicules', $id, 'vehicules_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_vehicules', $data, $id, 'vehicules_id');
    }

    public function get_carte_grise_files($id)
    {
        $query = $this->db->select('*')
            ->get_where('t_files', array('row_id' => $id, 'name' => 'vehicules_carte_grise'));
        $resultat = $query->result();
        return $resultat;
    }
}
// EOF
