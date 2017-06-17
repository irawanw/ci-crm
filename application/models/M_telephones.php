<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_telephones extends MY_Model
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
                array('telephones_id', 'ref', "Telephones#", 'telephones', 'telephones_id', 'telephones_id'),
                array('souscription_date', 'date', "Date Souscription", 'souscription_date'),
                array('numero_client', 'text', "Numéro de Client", 'numero_client'),
                array('numero_de_compte_internet', 'text', "Numéro de Compte Internet", 'numero_de_compte_internet'),
                array('numero_de_tel_internet', 'text', "Numéro de Tel Internet", 'numero_de_tel_internet'),
                array('numero_tel', 'text', "Numéro Tel", 'numero_tel'),
                array('engagement_jusquau', 'date', "Engagement Jusqu'au", 'engagement_jusquau'),
                array('pas_engage_nom', 'text', "Engagé", 'pas_engage_nom'),
                array('resiliation_date', 'date', "Résiliation Effectuée à la Date de", 'resiliation_date'),
                array('etat', 'text', "état", 'etat'),
                array('type', 'text', "Type", 'type'),
                array('fornisseur', 'text', "Fournisseur", 'fornisseur'),
                array('forfait_ligne_fixe', 'text', "Forfait Ligne Fixe", 'forfait_ligne_fixe'),
                array('forfait_portable', 'text', "Forfait Portable", 'forfait_portable'),
                array('options', 'text', "Options", 'options'),
                array('prix', 'text', "Prix", 'prix'),
                array('scv_nom', 'text', "Société", 'scv_nom'),
                array('lieu_ligne', 'text', "Lieu où se Situe la Ligne", 'lieu_ligne'),
                array('utilisation_actuelle', 'text', "Utilisation Actuelle", 'utilisation_actuelle'),
                array('utilisation_future', 'text', "Utilisation Future", 'utilisation_future'),
                array('problemes_resoudre', 'text', "Problèmes à résoudre", 'problemes_resoudre'),
                array('url', 'text', "URL", 'url'),
                array('user', 'text', "ID", 'user'),
                array('mdp', 'text', "MDP", 'mdp'),
            ),
            'write' => array(
                'souscription_date'         => array("Souscription Date", 'date', 'souscription_date', false),
                'numero_client'             => array("Numéro de Client", 'text', 'numero_client', false),
                'numero_de_compte_internet' => array("Numéro de Compte Internet", 'text', 'numero_de_compte_internet', false),
                'numero_de_tel_internet'    => array("Numéro de Tel Internet", 'text', 'numero_de_tel_internet', false),
                'numero_tel'                => array("Numéro Tel", 'text', 'numero_tel', false),
                'engagement_jusquau'        => array("Engagement Jusqu'au", 'date', 'engagement_jusquau', false),
                'pas_engage'                => array("Pas engagé", 'checkbox-h', 'pas_engage', false),
                'resiliation_date'          => array("Résiliation Effectuée à la Date de", 'date', 'resiliation_date', false),
                'etat'                      => array("état", 'select', array('etat', 'id', 'value'), false),
                'type'                      => array("Type", 'select', array('type', 'id', 'value'), false),
                'fornisseur'                => array("Fournisseur", 'text', 'fornisseur', false),
                'forfait_ligne_fixe'        => array("Forfait Ligne Fixe", 'text', 'forfait_ligne_fixe', false),
                'forfait_portable'          => array("forfait Portable", 'text', 'forfait_portable', false),
                'options'                   => array("Options", 'text', 'options', false),
                'prix'                      => array("Prix", 'text', 'prix', false),
                'societe'                   => array("Société", 'select', array('societe', 'id', 'value'), false),
                'lieu_ligne'                => array("Lieu où se Situe la Ligne", 'text', 'lieu_ligne', false),
                'utilisation_actuelle'      => array("Utilisation Actuelle", 'text', 'utilisation_actuelle', false),
                'utilisation_future'        => array("Utilisation Future", 'text', 'utilisation_future', false),
                'problemes_resoudre'        => array("Problèmes à résoudre", 'text', 'problemes_resoudre', false),
                'url'                       => array("URL", 'text', 'url', false),
                'user'                      => array("ID", 'text', 'user', false),
                'mdp'                       => array("MDP", 'text', 'mdp', false),
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste Telephones Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        // première partie du select, mis en cache
        $table = 't_telephones';
        $this->db->start_cache();
        $telephones_id      = formatte_sql_lien("telephones/detail", "telephones_id", "telephones_id");
        $souscription_date  = formatte_sql_date("souscription_date");
        $engagement_jusquau = formatte_sql_date("engagement_jusquau");
        $resiliation_date   = formatte_sql_date("resiliation_date");        
        $pas_engage         = "if(pas_engage = 0 AND UNIX_TIMESTAMP(engagement_jusquau), 'Oui', 'Non')";
        $pas_engage_nom     = $pas_engage." AS pas_engage_nom";

        $this->db->select("*,
                telephones_id as RowID,
                telephones_id as checkbox,
                $pas_engage_nom,
                telephones_id,
                souscription_date,
                scv_nom,
                resiliation_date,engagement_jusquau", false);

        $this->db->join('t_societes_vendeuses tsv', $table . '.societe=tsv.scv_id', 'left');

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
            $this->db->where('telephones_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'pas_engage_nom' => $pas_engage,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->telephones_id . '">';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'telephones_id'             => 'int',
            'souscription_date'         => 'date',
            'numero_client'             => 'char',
            'numero_de_compte_internet' => 'char',
            'numero_de_tel_internet'    => 'char',
            'numero_tel'                => 'char',
            'engagement_jusquau'        => 'date',
            'resiliation_date'          => 'date',
            'etat'                      => 'char',
            'type'                      => 'char',
            'fornisseur'                => 'char',
            'forfait_ligne_fixe'        => 'char',
            'forfait_portable'          => 'char',
            'options'                   => 'char',
            'prix'                      => 'char',
            'scv_nom'                   => 'char',
            'lieu_ligne'                => 'char',
            'utilisation_actuelle'      => 'char',
            'utilisation_future'        => 'char',
            'problemes_resoudre'        => 'char',
            'url'                       => 'char',
            'user'                      => 'char',
            'mdp'                       => 'char',
            'pas_engage_nom'                => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Telephones insert into t_telephones table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_telephones', $data);
    }

    /******************************
     * Detail d'une telephones
     ******************************/
    public function detail($id)
    {
        $this->db->select("*");
        $this->db->where('telephones_id = "' . $id . '"');
        $q = $this->db->get('t_telephones as tl');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating telephones data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_telephones', $data, $id, 'telephones_id');
    }

    /******************************
     * Dupliquer telephones data
     ******************************/
    public function dupliquer($id)
    {
        $data = $this->db->get_where('t_telephones', array('telephones_id' => $id))->row_array();

        if($data) {
            unset($data['telephones_id']);  

            return $this->_insert('t_telephones', $data);
        }
    }

    /******************************
     * Archive telephones data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_telephones', $id, 'telephones_id', 'inactive');
    }

    /******************************
     * Archive telephones data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_telephones', $id, 'telephones_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_telephones', $data, $id, 'telephones_id');
    }

    public function etat_option()
    {
        $result = array();
        $values = array('Résiliée', 'En cours de résiliation', 'En cours', 'à résilier');
        return $this->form_option($values);
    }

    public function type_option()
    {
        $result = array();
        $values = array('Internet+Analogique', 'Internet Seul', 'Analogique Seul', 'Numeris', 'Portable');
        return $this->form_option($values);
    }

    public function societe_option()
    {
        return $this->db->select('scv_id as id, scv_nom as value')->get('t_societes_vendeuses')->result();

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
