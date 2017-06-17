<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_amalgame extends MY_Model
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
                array('amalgame_group_id', 'text', "Amalgame#", 'amalgame_group_id'),
                array('name', 'text', "Amalgame Nom", 'name'),
                array('date_de_livraison_del_amalgame', 'text', "Date de livraison réelle", 'date_de_livraison_del_amalgame'),
                array('date_envoi_bat_global', 'text', "Date de livraison réelle", 'date_envoi_bat_global'),
                array('date_livraison_reelle', 'text', "Date de livraison réelle", 'date_livraison_reelle'),
                array('valides', 'text', "Valide", 'valides'),
            ),
            'read.child'   => array(
                array('checkbox', 'text', "Action de masse", 'checkbox'),
                array('name', 'text', "Amalgame Nom", 'name'),
                array('amalgame_id', 'ref', "Doc#", 'amalgame', 'amalgame_id', 'amalgame_id'),
                array('client_name', 'text', "Client", 'client_name'),
                array('commande_name', 'text', "Commande", 'commande_name'),
                array('type_document', 'text', "Type de Document", 'type_document'),
                array('denomination_taille', 'text', "Dénomination Taille Format Ouvert", 'denomination_taille'),
                array('largeur', 'text', "Largeur Format Ouvert", 'largeur'),
                array('longueur', 'text', "Longueur Format Ouvert", 'longueur'),
                array('denomination_taille_ferme', 'text', "Dénomination Taille Format Fermé", 'denomination_taille_ferme'),
                array('largeur_ferme', 'text', "Longueur Format Fermé", 'largeur_ferme'),
                array('longueur_ferme', 'text', "Longueur Format Fermé", 'longueur_ferme'),
                array('qty', 'text', "Quantité", 'qty'),
                array('plis', 'text', "Nombre Plis", 'plis'),
                array('type_plis', 'text', "Type Plis", 'type_plis'),
                array('eq_t50_a5', 'number', "Equivalen T 50000 A5", 'eq_t50_a5'),
                array('bat', 'text', "Bat", 'bat'),
                array('fischiers_imprimer', 'picture', "Fischiers Imprimer", 'fischiers_imprimer', 'amalgame', 'amalgame_id', 'uploadfile'),
                array('lien_fichier', 'text', "Lien pour télécharger le fichier", 'lien_fichier'),
            ),
            'write.parent' => array(),
            'write.child'  => array(
                'client'                    => array("Client", 'select', array('client', 'ctc_id', 'ctc_nom'), false),
                'commande'                  => array("Commande", 'select', array('commande', 'cmd_id', 'cmd_reference'), false),
                'qty'                       => array("Quantité", 'text', 'qty', false),
                'denomination_taille'       => array("Dénomination taille", 'select', array('denomination_taille', 'id', 'value'), false),
                'type_document'             => array("Type de document", 'select', array('type_document', 'id', 'value'), false),
                'largeur'                   => array("Largeur", 'text', 'largeur', false),
                'longueur'                  => array("Longueur", 'text', 'longueur', false),
                'denomination_taille_ferme' => array("Dénomination taille format fermé", 'select', array('denomination_taille_ferme', 'id', 'value'), false),
                'largeur_ferme'             => array("Largeur fermé", 'text', 'largeur_ferme', false),
                'longueur_ferme'            => array("Longueur fermé", 'text', 'largeur_ferme', false),
                'plis'                      => array("Nombre Plis", 'text', 'plis', false),
                'type_plis'                 => array("Type Plis", 'select', array('type_plis', 'id', 'value'), false),
                'eq_t50_a5'                 => array("Equivalen T 50000 A5", 'text', 'eq_t50_a5', false),
                'bat'                       => array("Bat", 'select', array('bat', 'id', 'value'), false),
                'fischiers_imprimer'        => array("Fischiers Imprimer", 'multiple-upload', 'fischiers_imprimer', false),
                'lien_fichier'              => array('Lien pour télécharger le fichier', 'text', 'lien_fichier', false),
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
     * Liste Amalgame Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $group = "", $ordercol = 2, $ordering = "asc")
    {
        $valider_name = $this->uri->segment(3);

        $table = 't_amalgame ta';
        // première partie du select, mis en cache
        $this->db->start_cache();

        $commande           = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name      = $commande . " AS commande_name";
        $fischiers_imprimer = "GROUP_CONCAT('<button data-id=',file_id,' class=\"btn btn-warning btn-xs btn-delete-file\">x</button><a target=_blank href=" . base_url('fichiers/amalgame') . "/',filename,'>',filename,'</a>' SEPARATOR '<br />') as fischiers_imprimer";

        $this->db->select("
				ta.amalgame_id as checkbox, ta.amalgame_id as RowID,
				ta.amalgame_id,
				tc.ctc_nom as client_name,
				$commande_name,
				ta.denomination_taille,
				ta.type_document,
				ta.qty,
				ta.plis,
				ta.type_plis,
				ta.eq_t50_a5,
				CASE WHEN ta.bat = 0 THEN 'Non' ELSE 'Oui' END as bat,
				ta.largeur,
				ta.longueur,
				ta.denomination_taille_ferme,
				ta.largeur_ferme,
				ta.longueur_ferme,
				$fischiers_imprimer,
				ta.lien_fichier,
				tag.name
			");
        $this->db->join('t_contacts as tc', 'tc.ctc_id=ta.client', 'left');
        $this->db->join('t_commandes as tm', 'tm.cmd_id=ta.commande', 'left');
        $this->db->join('t_amalgame_group as tag', 'tag.amalgame_group_id=ta.amalgame_group_id', 'left');
        $this->db->join('t_files', $table . '.amalgame_id=t_files.row_id AND t_files.name="amalgame_fischiers_imprimer"', 'LEFT');
        $this->db->group_by("amalgame_id");

        if ($this->uri->segment(2) == 'group_json' && $this->uri->segment(4) != null) {
            $this->db->where('tag.name = "' . $group . '"');
        } elseif ($this->uri->segment(2) == 'filter_date_livraisons_json') {
            $this->db->where('tag.date_livraison_reelle = "' . $this->uri->segment(3) . '"');
        } else {
            //$this->db->where('ta.amalgame_group_id = "0"');
        }

        switch ($void) {
            case 'archived':
                $this->db->where('ta.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('ta.deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where('ta.inactive = "0000-00-00 00:00:00"');
                $this->db->where('ta.deleted = "0000-00-00 00:00:00"');
                break;
        }

        $id = intval($void);

        if ($id > 0) {
            $this->db->where('ta.amalgame_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'   => 'ctc_nom',
            'commande_name' => $commande,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add another feature to the list like checkbox, upload file, view message
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $data           = $resultat['data'][$i];
            $data->checkbox = '<input type="checkbox" name="ids[]" value="' . $data->amalgame_id . '">';
            if ($data->fischiers_imprimer == '') {
                $data->fischiers_imprimer = '<a class="btn-upload-file" href="#" data-id="' . $data->amalgame_id . '">Telecharger</a>';
            }
        }

        return $resultat;
    }

    public function liste_group($void, $limit = 10, $offset = 1, $filters = null, $group_name = "", $ordercol = 2, $ordering = "asc", $option = "")
    {
        $table                          = 't_amalgame_group';
        $date_de_livraison_del_amalgame = formatte_sql_date("date_de_livraison_del_amalgame");
        $date_envoi_bat_global          = formatte_sql_date("date_envoi_bat_global");
        $date_livraison_reelle          = formatte_sql_date("date_livraison_reelle");
        $valides                        = "CASE WHEN " . $table . ".valid = 1 THEN 'Validé' ELSE 'Non validé' END";

        $this->db->start_cache();
        $this->db->select("*, amalgame_group_id as RowID,
			'checkbox' as checkbox,
			$date_de_livraison_del_amalgame,
			$date_envoi_bat_global,
			$date_livraison_reelle,
			$valides as valides
		");

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
                $this->db->where($table . '.inactive IS NULL');
                $this->db->where($table . '.deleted IS NULL');
                break;
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'valides' => $valides,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns2(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add another feature to the list like checkbox, upload file, view message
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $data           = $resultat['data'][$i];
            $data->checkbox = '<input type="checkbox" name="ids[]" value="' . $data->amalgame_group_id . '">';
            $data->name     = '<a href="' . site_url('amalgame') . '/group/' . $data->name . '">' . $data->name . '</a>';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'amalgame_id'               => 'int',
            'client_name'               => 'char',
            'commande_name'             => 'char',
            'denomination_taille'       => 'char',
            'type_document'             => 'char',
            'qty'                       => 'int',
            'plis'                      => 'int',
            'type_plis'                 => 'char',
            'eq_t50_a5'                 => 'int',
            'bat'                       => 'char',
            'largeur'                   => 'int',
            'longueur'                  => 'int',
            'denomination_taille_ferme' => 'char',
            'largeur_ferme'             => 'int',
            'longueur_ferme'            => 'int',
            //'fischiers_imprimer' => 'char',
            'lien_fichier'              => 'char',
            'valider'                   => 'char',
        );

        return $filterable_columns;
    }

    public function liste_filterable_columns2()
    {
        $filterable_columns = array(
            'amalgame_group_id'              => 'int',
            'name'                           => 'char',
            'date_de_livraison_del_amalgame' => 'date',
            'date_envoi_bat_global'          => 'date',
            'date_livraison_reelle'          => 'date',
            'valides'                        => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Amalgame insert into t_amalgame table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_amalgame', $data);
    }

    /******************************
     * Detail d'une amalgame
     ******************************/
    public function detail($id)
    {
        $this->db->select("
				ta.amalgame_id,
				tc.ctc_id as client,
				tc.ctc_nom as client_name,
				ta.commande as commande,
				CASE WHEN
					ta.commande = -1 THEN 'Pas de Commande'
					ELSE tm.cmd_reference
					END as commande_reference,
				ta.denomination_taille,
				ta.largeur,
				ta.longueur,
				ta.denomination_taille_ferme,
				ta.largeur_ferme,
				ta.longueur_ferme,
				ta.type_document,
				ta.qty,
				ta.plis,
				ta.type_plis,
				ta.eq_t50_a5,
				CASE WHEN ta.bat = 0 THEN 'Non' ELSE 'Oui' END as bat_text,
				ta.bat,
				ta.fischiers_imprimer,
				ta.lien_fichier,
				ta.valider,
				tag.name as group_name
			");
        $this->db->join('t_contacts as tc', 'tc.ctc_id=ta.client', 'left');
        $this->db->join('t_commandes as tm', 'tm.cmd_id=ta.commande', 'left');
        $this->db->join('t_amalgame_group as tag', 'tag.amalgame_group_id=ta.amalgame_group_id', 'left');
        $this->db->where('amalgame_id = "' . $id . '"');
        $q = $this->db->get('t_amalgame as ta');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating amalgame data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_amalgame', $data, $id, 'amalgame_id');
    }

    /**
     * Upload Files
     */
    public function upload_files($filename, $id)
    {
        $table      = "t_amalgame";
        $field_name = "fischiers_imprimer";
        $key        = "amalgame_id";

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
     * Archive amalgame data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_amalgame', $id, 'amalgame_id', 'inactive');
    }

    /******************************
     * Archive amalgame data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_amalgame', $id, 'amalgame_id', 'deleted');
    }

    public function unremove($id)
    {
        $data = array('deleted' => '0000-00-00 00:00:00', 'inactive' => '0000-00-00 00:00:00');
        $this->db->where('amalgame_id', $id);
        $q = $this->db->update('t_amalgame', $data);
    }

    public function commande($amalgame_id)
    {
        $this->db->select("
				tc.cmd_id,
				tc.cmd_reference
			");
        $this->db->join('t_devis as td', 'td.dvi_client = tl.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tl.amalgame_id = "' . $amalgame_id . '"');
        $q = $this->db->get('t_amalgame tl');
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

    public function yes_no_option()
    {
        $result = array();
        $values = array('Non', 'Oui');
        return $this->form_option($values, true);
        return $result;
    }

    public function type_document_option()
    {
        $result = array();
        $values = array('Flyer', 'Dépliant');
        return $this->form_option($values);
    }

    public function denomination_taille_option()
    {
        $result = array();
        $values = array('A1', 'A2', 'A3', 'A4', 'A5', 'DIN LONG', 'A6', 'A7', 'Autres');
        return $this->form_option($values);
    }

    public function type_plis_option()
    {
        $result = array();
        $values = array('Portefeuille', 'Accordéon', 'Pli roulé');
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

    public function is_valider($id)
    {
        $this->db->select("*");
        $this->db->join('t_amalgame_group as tag', 'tag.amalgame_group_id=tl.amalgame_group_id', 'left');
        $this->db->where('tl.amalgame_id = "' . $id . '"');
        $this->db->where('tag.valid = 1');
        $q   = $this->db->get('t_amalgame tl');
        $res = $q->result();
        return count($res);
    }

    public function is_group_valid($name)
    {
        $this->db->select("*");
        $this->db->where('name = "' . $name . '"');
        $this->db->where('valid = 1');
        $q   = $this->db->get('t_amalgame_group');
        $res = $q->result();
        return count($res);
    }

    // public function valider(){
    //     $valider_name         = $this->input->post('valider_name');
    //     $old_valider_name     = $this->input->post('old_valider_name');
    //     $data['valider']     = $valider_name;
    //     $this->db->where('valider', $old_valider_name);
    //     $this->db->where('inactive is NULL');
    //     $this->db->where('deleted is NULL');
    //     $this->db->update('t_amalgame' ,$data);
    // }

    public function valider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('amalgame_group_id = "' . $group->amalgame_group_id . '"');
            $this->db->update('t_amalgame_group', array('valid' => 1));
        }
    }

    public function revalider($data)
    {
        $group = $this->get_group($data);
        if (is_object($group)) {
            $this->db->where('amalgame_group_id = "' . $group->amalgame_group_id . '"');
            $this->db->update('t_amalgame_group', array('valid' => 0));
        }
    }

    // public function revalider($valider_name){
    //     $data['valider'] = '';
    //     $this->db->where('valider', $valider_name);
    //     $this->db->update('t_amalgame' ,$data);
    // }

    // public function get_valider_name($valides=false){
    //     $this->db->select("valider");
    //     $this->db->group_by("valider");
    //     if($valides==false)
    //         $this->db->where('valider LIKE "%-NV"');
    //     else
    //         $this->db->where('valider NOT LIKE "%-NV"');
    //     $q = $this->db->get('t_amalgame tl');
    //     $res = $q->result();

    //     $data = '';
    //     foreach($res as $row){
    //         if($row->valider != '')
    //             $data[] = $row->valider;
    //     }
    //     return $data;
    // }

    public function get_valider_name($valides = false)
    {
        $this->db->select("name");
        if ($valides == false) {
            $this->db->where('valid', 0);
        } else {
            $this->db->where('valid', 1);
        }

        $q   = $this->db->get('t_amalgame_group');
        $res = $q->result();

        $data = '';
        foreach ($res as $row) {
            $data[] = $row->name;
        }
        return $data;
    }

    public function set_group($data)
    {
        $table    = "t_amalgame";
        $group    = $this->get_group($data);
        $group_id = 0;

        if ($group == false) {
            $this->db->insert('t_amalgame_group', $data);
            $new_id = $this->db->insert_id();

            if ($new_id) {
                $group_id = $new_id;
            }
        } else {
            $group_id = $group->amalgame_group_id;
        }

        if ($group_id != 0) {
            $this->db->where('amalgame_group_id', 0);
            $this->db->where('inactive is NULL');
            $this->db->where('deleted is NULL');
            $this->db->update($table, array('amalgame_group_id' => $group_id));
        }
    }

    public function get_group($criteria)
    {
        $query = $this->db->get_where('t_amalgame_group', $criteria);

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
        return $this->_delete('t_amalgame_group', $id, 'amalgame_group_id', 'inactive');
    }

    /******************************
     * Archive and Remove Group amalgame data
     ******************************/
    public function remove_group($id)
    {
        return $this->_delete('t_amalgame_group', $id, 'amalgame_group_id', 'deleted');
    }

    public function unremove_group($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        $this->db->where('amalgame_group_id', $id);
        $q = $this->db->update('t_amalgame_group', $data);
    }

    public function get_fischiers_imprimer_files($id)
    {
        $query = $this->db->select('*')
            ->get_where('t_files', array('row_id' => $id, 'name' => 'amalgame_fischiers_imprimer'));
        $resultat = $query->result();
        return $resultat;
    }

}
// EOF
