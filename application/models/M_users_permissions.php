<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_users_permissions extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read'  => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('usp_id', 'ref', "#ID", 'users_permissions', 'usp_id', 'usp_id'),
                array('usp_utilisateurs', 'text', "Utilisateurs", 'usp_utilisateurs'),
                array('usp_table', 'text', "Module", 'usp_table'),
                array('usp_type', 'text', "Type", 'usp_type'),
                array('usp_fields', 'text', "Fields", 'usp_fields'),
            ),
            'write' => array(
                'usp_utilisateurs' => array("Utilisateurs", 'select', array('usp_utilisateurs', 'id', 'value'), false),
                'usp_table'        => array("Modul", 'select', array('usp_table', 'id', 'value'), false),
                'usp_type'         => array("Type", 'select', array('usp_type', 'id', 'value'), false),
                'usp_fields'       => array("Fields", 'select-multiple', array('usp_fields', 'value', 'value'), false),

            ),
        );

        return $champs[$type];
    }

    public function liste($void = 0, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_users_permissions';
        // première partie du select, mis en cache
        $this->db->start_cache();

        $this->db->select("*
            , usp_id as checkbox, usp_id as RowID,
            , tu.utl_login as usp_utilisateurs
        ");
        $this->db->join('t_utilisateurs as tu', 'tu.utl_id = usp_utilisateurs', 'left');

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
            $this->db->where('usp_id', $id);
        }
        // aliases
        $aliases = array(
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        //$this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->usp_id . '">';
        }

        return $resultat;
    }

    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'usp_id'           => 'int',
            'usp_utilisateurs' => 'char',
            'usp_table'        => 'char',
            'usp_fields'       => 'char',
        );

        return $filterable_columns;
    }

    public function nouveau($data)
    {
        return $this->_insert('t_users_permissions', $data);
    }

    public function detail($id)
    {
        $this->db->select('t_users_permissions.*');
        $this->db->where('usp_id = "' . $id . '"');
        $q = $this->db->get('t_users_permissions');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function maj($data, $id)
    {
        return $this->_update('t_users_permissions', $data, $id, 'usp_id');
    }

    public function archive($id)
    {
        return $this->_delete('t_users_permissions', $id, 'usp_id', 'inactive');
    }

    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_users_permissions', $data, $id, 'usp_id');
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

    public function utilisateurs_liste_option()
    {
        $where = "inactive IS NULL AND deleted IS NULL";
        return $this->db->select('utl_id as id,utl_login as value')
            ->where($where)
            ->order_by('utl_login', 'ASC')
            ->get('t_utilisateurs')->result();
    }

    public function table_liste_option()
    {
        // $options = array(
        //           't_actions' => 'Actions',
        //           't_adresses' => 'Adresses',
        //           't_airmail' => 'Airmail',
        //           't_alertes' => 'Alertes',
        //           't_amalgame' => 'Amalgame',
        //           't_articles' => 'Articles',
        //           't_avoirs' => 'Avoirs',
        //           't_boites_archive' => 'Boites archive',
        //           't_cartes_blues' => 'Cartes blues',
        //           't_catalogues' => 'Catalogues',
        //           't_commandes' => 'Commandes',
        //           't_contacts' => 'Contacts',
        //           't_controle_recurrents' => 'Controle recurrents',
        //           't_correspondants' => 'correspondants',
        //           't_developpers_followup' => 'Developpers followup',
        //           't_devis' => 'Devis',
        //           't_disques_archivage' => 'Disques archivage',
        //           't_documents_autres' => 'Documents autres',
        //           't_documents_contacts' => 'Documents contacts',
        //           't_documents_employes' => 'Documents employes',
        //           't_documents_factures' => 'Documents factures',
        //           't_document_templates' => 'Document templates',
        //           't_domains' => 'Domains',
        //           't_droits' => 'Droits utilisation',
        //           't_emails_emp' => 'Emails emp',
        //           't_emm_followup' => 'Emm followup',
        //           't_employes' => 'Employes',
        //           't_evenements' => 'Evenements',
        //           't_factures' => 'Factures',
        //           't_factures_compta' => 'Factures compta',
        //           't_feuilles_de_tri' => 'Feuilles de tri',
        //           't_feuille_controle' => 'Feuille controle',
        //           't_feuille_de_routes' => 'Feuille de routes',
        //           't_files' => 'Ged',
        //           't_gestion_heures' => 'Gestion heures',
        //           't_hosts' => 'Hosts',
        //           't_imputations' => 'Imputations',
        //           't_ips' => 'Ips',
        //           't_lignes_factures' => 'Lignes factures',
        //           't_livraisons' => 'Livraisons',
        //           't_mailchimp' => 'Mailchimp',
        //           't_manual_sending' => 'Manual sending',
        //           't_max_bulk' => 'Max bulk',
        //           't_messages' => 'Messages',
        //           't_message_list' => 'Message_list',
        //           't_modeles_documents' => 'Modeles documents',
        //           't_objectif' => 'Objectif',
        //           't_objectifs' => 'Objectifs',
        //           't_openemm' => 'Openemm',
        //           't_ordres_production' => 'Ordres production',
        //           't_owners' => 'Owners',
        //           't_pages_jaunes' => 'Pages jaunes',
        //           't_plaintes' => 'Plaintes',
        //           't_preparation' => 'Preparation',
        //           't_production_mails' => 'Production mails',
        //           't_profils' => 'Profils',
        //           't_promotions' => 'Promotions',
        //           't_providers' => 'Providers',
        //           't_purchases' => 'Purchases',
        //           't_rbl_liste' => 'Rbl liste',
        //           't_reglements' => 'Reglements',
        //           't_secteurs' => 'Secteurs',
        //           't_sendgrid' => 'Sendgrid',
        //           't_sendinblue' => 'Sendinblue',
        //           't_servers' => 'Servers',
        //           't_societes_vendeuses' => 'Societes vendeuses',
        //           't_softwares' => 'Softwares',
        //           't_suivi_adwords' => 'Suivi_adwords',
        //           't_taches' => 'Taches',
        //           't_taux_tva' => 'Taux tva',
        //           't_telephones' => 'Telephones',
        //           't_tests_followup' => 'Tests followup',
        //           't_utilisateurs' => 'Utilisateurs',
        //           't_vehicules' => 'Vehicules',
        //           't_villes' => 'Villes',
        //           't_vues' => 'Vues',
        //       );
        // $i=0;
        //       foreach ($options as $key => $value) {
        //           $val = new stdClass();
        //           $val->id = $key;
        //           $val->value = $value;
        //           $result[$i] = $val;
        //           $i++;
        //       }

        //       return $result;
        include APPPATH . 'config/modules.php';

        $options = array();
        $i       = 0;
        foreach ($modules as $key => $value) {
            $val         = new stdClass();
            $val->id     = $key;
            $val->value  = $value['name'];
            $options[$i] = $val;
            $i++;
        }

        return $options;
    }

    public function check_is_standarize_module($module)
    {
        $model         = 'm_' . $module;
        $is_standarize = false;

        if (file_exists(APPPATH . "models/$model.php")) {
            if (method_exists($this->$model, 'get_champs')) {
                $is_standarize = true;
            }
        }

        return $is_standarize;
    }

    public function dupliquer($id)
    {
        $row = $this->db->get_where('t_users_permissions', array('usp_id' => $id))->row_array();

        if ($row) {
            unset($row['usp_id']);
            return $this->nouveau($row);
        }

        return false;
    }

    public function remove($id)
    {
        return $this->_delete('t_users_permissions', $id, 'usp_id', 'deleted');
    }

    public function type_option()
    {
        $options = array('read', 'write');

        return $this->form_option($options);
    }

    public function field_liste_option($module = '', $type = '')
    {
        //       if ($modul!='') {
        //           $table_schema = $this->db->database;
        //        $query = "SELECT COLUMN_NAME as value FROM INFORMATION_SCHEMA.COLUMNS WHERE
        //     TABLE_SCHEMA = '".$table_schema."' AND TABLE_NAME = '".$modul."'";
        //     $result = $this->db->query($query)->result();
        //       }else{
        //           $result = array();
        //       }
        if ($module == "" || $type == "") {
            return array();
        }

        // return $result;
        $model = 'm_' . $module;
        $this->load->model($model);

        if (method_exists($this->$model, 'get_champs') == false) {
            return array();
        }

        $champs  = $this->$model->get_champs($type);
        $options = array();
        $i       = 0;

        if ($type == 'read') {
            foreach ($champs as $champ) {
                $val         = new stdClass();
                $val->id     = $champ[0];
                $val->value  = $champ[0];
                $options[$i] = $val;
                $i++;
            }
        } else {
            foreach ($champs as $key => $champ) {
                $val         = new stdClass();
                $val->id     = $key;
                $val->value  = $key;
                $options[$i] = $val;
                $i++;
            }
        }

        return $options;
    }

    public function get_viewable_fields($module, $type, $username)
    {
        $query = $this->db->select('usp_fields')
            ->join('t_utilisateurs', 'utl_id=usp_utilisateurs')
            ->where('usp_table', $module)
            ->where('usp_type', $type)
            ->where('utl_login', $username)
            ->get('t_users_permissions');

        $fields = array();

        if ($query->row()) {
            $row           = $query->row();
            $fields_string = $row->usp_fields;

            if ($fields_string != "") {
                $fields = explode(",", $fields_string);
            }

        }

        return $fields;
    }

    public function get_permissions($id)
    {
        $this->db->select('t_users_permissions.*');
        $this->db->where('usp_utilisateurs = "' . $id . '"');
        $this->db->where('(deleted = "0000-00-00 00:00:00" OR deleted IS NULL)');
        $this->db->where('(inactive = "0000-00-00 00:00:00" OR inactive IS NULL)');
        $q = $this->db->get('t_users_permissions');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $thefields                                    = explode(',', $row->usp_fields);
                $permissions[$row->usp_table][$row->usp_type] = $thefields;
            }
            return $permissions;
        } else {
            return null;
        }
    }

    public function update_permissions($id)
    {
        $module       = $this->input->post('module');
        $fields_read  = $this->input->post('fields_read');
        $fields_write = $this->input->post('fields_write');
        $update_read  = '';
        $update_write = '';

        //find existing read permissions
        //if so then update the permissions
        //if not available create new permissions
        $this->db->select('t_users_permissions.*');
        $this->db->where('usp_utilisateurs = "' . $id . '"');
        $this->db->where('usp_type = "read"');
        $this->db->where('usp_table = "' . $module . '"');
        $this->db->where('(deleted = "0000-00-00 00:00:00" OR deleted IS NULL)');
        $this->db->where('(inactive = "0000-00-00 00:00:00" OR inactive IS NULL)');
        $q = $this->db->get('t_users_permissions');

        if ($q->num_rows() > 0) {
            $result = $q->row();
            $usp_id = $result->usp_id;
            $data   = array(
                'usp_fields' => $fields_read,
            );
            $update_read = $this->_update('t_users_permissions', $data, $usp_id, 'usp_id');
        } else {
            $data = array(
                'usp_utilisateurs' => $id,
                'usp_fields'       => $fields_read,
                'usp_type'         => 'read',
                'usp_table'        => $module,
            );
            $update_read = $this->_insert('t_users_permissions', $data);
        }
        //echo $this->db->last_query();

        //find existing wrte permissions
        //if so then update the permissions
        //if not available create new permissions
        $this->db->select('t_users_permissions.*');
        $this->db->where('usp_utilisateurs = "' . $id . '"');
        $this->db->where('usp_type = "write"');
        $this->db->where('usp_table = "' . $module . '"');
        $this->db->where('(deleted = "0000-00-00 00:00:00" OR deleted IS NULL)');
        $this->db->where('(inactive = "0000-00-00 00:00:00" OR inactive IS NULL)');
        $q = $this->db->get('t_users_permissions');

        if ($q->num_rows() > 0) {
            $result = $q->row();
            $usp_id = $result->usp_id;
            $data   = array(
                'usp_fields' => $fields_write,
            );
            $update_write = $this->_update('t_users_permissions', $data, $usp_id, 'usp_id');
        } else {
            $data = array(
                'usp_utilisateurs' => $id,
                'usp_fields'       => $fields_write,
                'usp_type'         => 'write',
                'usp_table'        => $module,
            );
            $update_write = $this->_insert('t_users_permissions', $data);
        }

        if ($update_read && $update_write) {
            return true;
        } else {
            return false;
        }
    }
}

/* End of file M_users_permissions.php */
/* Location: .//tmp/fz3temp-1/M_users_permissions.php */
