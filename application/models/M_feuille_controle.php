<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_feuille_controle extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type, $data = null)
    {
        $champs = array(
            'read.parent'  => array(
                array('checkbox', 'text', "checkbox", 'checkbox'),
                array('feuille_controle_group_id', 'text', "Contrôle de Distribution#", 'feuille_controle_group_id'),
                //array('name', 'text', "Contrôle de Distribution Nom<br><br>", 'name'),
                array('controleur_name', 'text', "Controleur", 'controleur_name'),
                array('client_name', 'text', "Client", 'client_name'),
                array('devis_name', 'text', "Devis", 'devis_name'),
                array('facture_name', 'text', "Facture", 'facture_name'),
                array('date_du_controle', 'text', "Date du Contrôle", 'date_du_controle'),
                array('valides', 'text', "Valide", 'valides'),
            ),
            'read.child'   => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('feuille_controle_id', 'ref', "id#", 'feuille_controle_id', 'feuille_controle_id', 'feuille_controle_id'),
                array('group_name', 'text', "Contrôle de Distribution Nom", 'group_name'),
                array('heure_de_debut', 'date', "Heure de debut", 'heure_de_debut'),
                array('heure_de_fin', 'date', "Heure de fin", 'heure_de_fin'),
                array('numero', 'text', "Numero", 'numero'),
                array('rue', 'text', "Rue", 'rue'),
                array('ville', 'text', "Ville", 'ville'),
                array('resultat', 'text', "Resultat", 'resultat'),
                array('commentaire', 'text', "Commentaires", 'commentaire'),
            ),
            'write.parent' => array(),
            'write.child' = array(
                // 'heure_de_debut'     => array("Heure de début",'date','heure_de_debut',false),
                // 'heure_de_fin'       => array("Heure de fin",'date','heure_de_fin',false),
                'numero'      => array("Numero", 'text', 'numero', false),
                'rue'         => array("Rue", 'text', 'rue', false),
                'ville'       => array("Ville", 'text', 'ville', false),
                'resultat'    => array("Resultat", 'select', array('resultat', 'id', 'value'), false),
                'commentaire' => array("Commentaires", 'text', 'commentaire', false),
            ),
        );

        if ($data != null) {
            $result = $champs[$type . "." . $data];
        } else {
            $result = array_merge($champs[$type . ".parent"], $champs[$type . ".child"]);
        }

        return $result;
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $group = '', $ordercol = 2, $ordering = "asc")
    {
        $table          = 't_feuille_controle';
        $group_name     = "t_group.name as group_name";
        $heure_de_debut = formatte_sql_date("heure_de_debut");
        $heure_de_fin   = formatte_sql_date("heure_de_fin");
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select("feuille_controle_id,
                           feuille_controle_id as RowID,
                            heure_de_debut,
                            heure_de_fin,
                            numero,
                            rue,
                            ville,
                            resultat,
                            commentaire,
                            feuille_controle_id as checkbox,
                            $group_name");
        $this->db->join('t_feuille_controle_group as t_group', $table . '.feuille_controle_group_id=t_group.feuille_controle_group_id', 'LEFT');

        if ($this->uri->segment(2) == 'group_json' && $this->uri->segment(4) != null) {
            $this->db->where('t_group.name = "' . $group . '"');
        } else {
            //$this->db->where($table.'.feuille_controle_group_id = "0"');
        }

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
            $this->db->where('feuille_controle_id', $id);
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(
            'group_name' => "t_group.group_name",
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->feuille_controle_id . '">';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'feuille_controle_id' => 'int',
            'group_name'          => 'char',
            'numero'              => 'int',
            'rue'                 => 'char',
            'ville'               => 'char',
            'resultat'            => 'char',
            'commentaire'         => 'char',
            'heure_de_debut'      => 'char',
            'heure_de_fin'        => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Message list insert into t_feuille_controle table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_feuille_controle', $data);
    }

    /******************************
     * Detail d'une test mails
     ******************************/
    public function detail($id)
    {
        $table                     = "t_feuille_controle";
        $date_du_controle          = formatte_sql_date("date_du_controle");
        $controleur_name           = "t_employes.emp_nom as controleur_name";
        $client_name               = "t_contacts.ctc_nom as client_name";
        $devis_name                = "CASE WHEN t_group.devis = -1 THEN 'aucun' ELSE t_devis.dvi_reference END as devis_name";
        $facture_name              = "CASE WHEN t_group.facture = -1 THEN 'aucune' ELSE t_factures.fac_reference END as facture_name";
        $group_name                = "t_group.name as group_name";
        $feuille_controle_group_id = $table . ".feuille_controle_group_id";
        $heure_de_debut            = formatte_sql_date("heure_de_debut");
        $heure_de_fin              = formatte_sql_date("heure_de_fin");

        $this->db->select("feuille_controle_id,$feuille_controle_group_id,$date_du_controle,numero,client, rue, ville, resultat, commentaire, $group_name, feuille_controle_id as checkbox, $client_name, $devis_name, $facture_name, $controleur_name, $heure_de_debut, $heure_de_fin");

        $this->db->join('t_feuille_controle_group as t_group', $table . '.feuille_controle_group_id=t_group.feuille_controle_group_id', 'LEFT');
        $this->db->join('t_contacts', 't_group.client=t_contacts.ctc_id', 'LEFT');
        $this->db->join('t_devis', 't_group.devis=t_devis.dvi_id', 'LEFT');
        $this->db->join('t_factures', 't_group.facture=t_factures.fac_id', 'LEFT');
        $this->db->join('t_utilisateurs as t_util', 't_group.controleur = t_util.utl_id', 'left');
        $this->db->join('t_employes', 't_employes.emp_id = t_util.utl_id', 'left');

        $this->db->where('feuille_controle_id = "' . $id . '"');

        $q = $this->db->get($table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function data_for_form($id)
    {
        $heure_de_debut = formatte_sql_date("heure_de_debut");
        $heure_de_fin   = formatte_sql_date("heure_de_fin");
        $table          = "t_feuille_controle";
        $this->db->select("feuille_controle_id,numero, rue, ville, resultat, commentaire");
        $this->db->where('feuille_controle_id = "' . $id . '"');

        $q = $this->db->get($table);
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
        return $this->_update('t_feuille_controle', $data, $id, 'feuille_controle_id');
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_feuille_controle', $id, 'feuille_controle_id', 'inactive');
    }

    /******************************
     * Archive data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_feuille_controle', $id, 'feuille_controle_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_feuille_controle', $data, $id, 'feuille_controle_id');
    }

    public function liste_option_devis($feuille_controle_id)
    {
        $this->db->select("
                td.dvi_id as id,
                td.dvi_reference as value
            ");

        $this->db->join('t_devis as td', 'td.dvi_client = tfc.client', 'inner');
        $this->db->where('tfc.feuille_controle_id = "' . $feuille_controle_id . '"');
        $q = $this->db->get('t_feuille_controle tfc');

        $commande          = $q->result();
        $new_object        = new stdClass;
        $new_object->id    = "-1";
        $new_object->value = 'Pas de Commande';
        array_unshift($commande, $new_object);

        return $commande;
    }

    public function liste_option_factures($feuille_controle_id)
    {
        $this->db->select("
                tfa.fac_id as id,
                tfa.fac_reference as value
            ");

        $this->db->join('t_commandes as tc', 'tc.cmd_devis = tfc.devis', 'inner');
        $this->db->join('t_factures as tfa', 'tc.cmd_id=tfa.fac_commande');
        $this->db->where('tfc.feuille_controle_id = "' . $feuille_controle_id . '"');
        $q = $this->db->get('t_feuille_controle tfc');

        $commande          = $q->result();
        $new_object        = new stdClass;
        $new_object->id    = "-1";
        $new_object->value = 'Pas de Commande';
        array_unshift($commande, $new_object);

        return $commande;
    }

    /**
     * Get list devis by client id
     */
    public function liste_option_devis_by_client($client_id)
    {
        $this->db->select("
                td.dvi_id as id,
                td.dvi_reference as value
            ");
        $this->db->join('t_contacts as tc', 'td.dvi_client = tc.ctc_id', 'inner');
        $this->db->where('tc.ctc_id = "' . $client_id . '"');
        $q = $this->db->get('t_devis td');
        return $q->result();
    }

    /**
     * Get list facture by client id
     */
    public function liste_option_factures_by_devis($devis_id)
    {
        $this->db->select("
                tfa.fac_id as id,
                tfa.fac_reference as value
            ");
        $this->db->join('t_commandes as tcmd', 'tcmd.cmd_devis = td.dvi_id');
        $this->db->join('t_factures as tfa', 'tcmd.cmd_id=tfa.fac_commande');
        $this->db->where('td.dvi_id = "' . $devis_id . '"');
        $q = $this->db->get('t_devis td');
        return $q->result();
    }

    public function resultat_liste_option()
    {
        $options = array(
            "VU BOITE",
            "VU HABITANT OK",
            "VU HABITANT PAS DISTRIBUE",
            "PAS VU DANS BOITE",
            "INCERTAIN",
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

    public function set_controle_distribution()
    {
        $table = "t_feuille_controle";

        $date           = formatte_date_to_bd($this->input->post('controle_distribution_date'));
        $heure_de_debut = $this->input->post('controle_distribution_heure_de_debut');
        $heure_de_fin   = $this->input->post('controle_distribution_heure_de_fin');
        $controleur     = $this->input->post('controle_distribution_controleur');
        $client         = $this->input->post('controle_distribution_client');
        $devis          = $this->input->post('controle_distribution_devis');
        $facture        = $this->input->post('controle_distribution_facture');
        $name           = $this->input->post('controle_distribution_name');

        $data = array(
            'date_du_controle' => $date,
            'heure_de_debut'   => $heure_de_debut,
            'heure_de_fin'     => $heure_de_fin,
            'controleur'       => $controleur,
            'client'           => $client,
            'devis'            => $devis,
            'facture'          => $facture,
            'name'             => $name,
        );

        //check if group exist
        $this->db->select("name");
        $this->db->where('name = "' . $name . '"');
        $q = $this->db->get('t_feuille_controle_group');
        if ($q->num_rows() == 0) {
            //add new group
            $this->_insert('t_feuille_controle_group', $data);
        }
    }

    public function get_controle_distribution_data($controle_name = null)
    {
        $table = "t_feuille_controle_group";
        if ($controle_name) {
            $query = $this->db->select('controleur,
                                        client,
                                        devis,
                                        facture,
                                        date_du_controle')
                ->get_where($table, array('controle_distribution' => $controle_name));

            if ($query->num_rows() > 0) {
                $result = $query->result();
                return $result[0];
            }
        } else {
            return false;
        }
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

    public function get_valider_name($valides = false)
    {
        $this->db->select("name");
        if ($valides == false) {
            $this->db->where('valid', 0);
        } else {
            $this->db->where('valid', 1);
        }

        $q   = $this->db->get('t_feuille_controle_group');
        $res = $q->result();

        $data = '';
        foreach ($res as $row) {
            $data[] = $row->name;
        }
        return $data;
    }

    public function list_option_group($criteria)
    {
        $query = $this->db->select("name,client")
            ->get_where('t_feuille_controle_group', $criteria);

        return $query->result();
    }

    /**
     * Check data is valider or not
     * @param  [type]  $id [description]
     * @return boolean     [description]
     */
    public function is_valides($id)
    {
        $this->db->select("*");
        $this->db->join('t_feuille_controle_group as tfg', 'tf.feuille_controle_group_id=tfg.feuille_controle_group_id', 'LEFT');
        $this->db->where('feuille_controle_id = "' . $id . '"');
        $this->db->where('tfg.valid', true);
        $q   = $this->db->get('t_feuille_controle tf');
        $res = $q->result();
        return count($res);
    }

    public function is_valider($id)
    {
        $this->db->select("*");
        $this->db->join('t_feuille_controle_group as tg', 'tg.feuille_controle_group_id=tf.feuille_controle_group_id', 'left');
        $this->db->where('tl.feuille_controle_group_id = "' . $id . '"');
        $this->db->where('tg.valid = 1');
        $q   = $this->db->get('t_feuille_controle tf');
        $res = $q->result();
        return count($res);
    }

    public function is_group_valid($name)
    {
        $this->db->select("*");
        $this->db->where('name = "' . $name . '"');
        $this->db->where('valid', true);
        $q   = $this->db->get('t_feuille_controle_group');
        $res = $q->result();
        return count($res);
    }

    public function valider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('feuille_controle_group_id = "' . $group->feuille_controle_group_id . '"');
            $this->db->update('t_feuille_controle_group', array('valid' => true));

            return true;
        } else {
            return false;
        }
    }

    public function revalider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('feuille_controle_group_id = "' . $group->feuille_controle_group_id . '"');
            $this->db->update('t_feuille_controle_group', array('valid' => false));

            return true;
        } else {
            return false;
        }
    }

    /**
     * LISTE GROUP
     */
    public function liste_group($void, $limit = 10, $offset = 1, $filters = null, $group_name = "", $ordercol = 2, $ordering = "asc", $option = "")
    {
        $table            = 't_feuille_controle_group';
        $date_du_controle = formatte_sql_date("date_du_controle");
        $controleur_name  = "t_employes.emp_nom as controleur_name";
        $client_name      = "t_contacts.ctc_nom as client_name";
        $devis_name       = "CASE WHEN " . $table . ".devis = -1 THEN 'aucun' ELSE t_devis.dvi_reference END as devis_name";
        $facture_name     = "CASE WHEN " . $table . ".facture = -1 THEN 'aucune' ELSE t_factures.fac_reference END as facture_name";
        $valides          = "CASE WHEN " . $table . ".valid = 1 THEN 'Validé' ELSE 'Non validé' END";

        $this->db->start_cache();
        $this->db->select($table . ".*,
            'checkbox' as checkbox,
            $date_du_controle,
            $controleur_name,
            $client_name,
            $devis_name,
            $facture_name,
            $valides as valides
        ");

        $this->db->join('t_contacts', $table . '.client=t_contacts.ctc_id', 'LEFT');
        $this->db->join('t_devis', $table . '.devis=t_devis.dvi_id', 'LEFT');
        $this->db->join('t_factures', $table . '.facture=t_factures.fac_id', 'LEFT');
        $this->db->join('t_utilisateurs as t_util', $table . '.controleur = t_util.utl_id', 'left');
        $this->db->join('t_employes', 't_employes.emp_id = t_util.utl_id', 'left');

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
            'controleur_name' => "t_employes.emp_nom",
            'client_name'     => "t_contacts.ctc_nom",
            'devis_name'      => "CASE WHEN " . $table . ".devis = -1 THEN 'Aucun' ELSE t_devis.dvi_reference END",
            'facture_name'    => "CASE WHEN " . $table . ".facture = -1 THEN 'Aucune' ELSE t_factures.fac_reference END",
            'valides'         => $valides,
        );

        $resultat = $this->_filtre($table, $this->liste_group_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add another feature to the list like checkbox, upload file, view message
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $data                            = $resultat['data'][$i];
            $data->checkbox                  = '<input type="checkbox" name="ids[]" value="' . $data->feuille_controle_group_id . '">';
            $data->feuille_controle_group_id = '<a href="' . site_url('feuille_controle') . '/group/' . $data->name . '">' . $data->feuille_controle_group_id . '</a>';
        }

        return $resultat;
    }

    public function liste_group_filterable_columns()
    {
        $filterable_columns = array(
            'feuille_controle_group_id' => 'int',
            'date_du_controle'          => 'date',
            //'name'                      => 'char',
            'controleur_name'           => 'char',
            'client_name'               => 'char',
            'devis_name'                => 'char',
            'facture_name'              => 'char',
        );

        return $filterable_columns;
    }

    public function get_group($criteria)
    {
        $query = $this->db->get_where('t_feuille_controle_group', $criteria);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /******************************
     * Archive Group data
     ******************************/
    public function archive_group($id)
    {
        return $this->_delete('t_feuille_controle_group', $id, 'feuille_controle_group_id', 'inactive');
    }

    /******************************
     * Remove Group Data
     ******************************/
    public function remove_group($id)
    {
        return $this->_delete('t_feuille_controle_group', $id, 'feuille_controle_group_id', 'deleted');
    }

    /******************************
     * Unremove Group Data
     ******************************/
    public function unremove_group($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_feuille_controle_group', $data, $id, 'feuille_controle_group_id');
    }
}
// EOF
