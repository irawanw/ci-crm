<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_list_utilisateurs extends MY_Model
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
                array('utl_login', 'text', "Login", 'utl_login'),
                array('vtu_type', 'text', "Fonction", 'vtu_type'),
                array('prf_description', 'text', "Profil", 'prf_nom'),
                array('utl_derniere_connexion', 'datetime', "Date de dernière connexion", 'utl_derniere_connexion'),
                array('utl_date_fin', 'date', "Date de fin de validité", 'utl_date_fin'),
                array('utl_refid', 'text', "ID", 'utl_refid'),
                array('vcv_civilite', 'text', "Civilite", 'vcv_civilite'),
                array('emp_prenom', 'text', "Prénom", 'emp_prenom'),
                array('emp_nom', 'text', "Nom", 'emp_nom'),
                //array('vfo_fonction', 'text', "Fonction", 'vfo_fonction'),
                array('utl_ensigne', 'text', "Enseignes", 'utl_ensigne'),
                array('emp_date_entree', 'date', "Date d'entrée", 'emp_date_entree'),
                array('emp_date_sortie', 'date', "Date de sortie", 'emp_date_sortie'),
                array('emp_commission', 'text', "Commissions", 'emp_commission'),
                array('vee_etat', 'ref', "Etat", 'v_etats_employes', 'vee_etat'),
                array('emp_adresse', 'text', "Adresse", 'emp_adresse'),
                array('emp_cp', 'text', "Code postal", 'emp_cp'),
                array('emp_ville', 'text', "Ville", 'emp_ville'),
                array('emp_telephone1', 'text', "Téléphone 1", 'emp_telephone1'),
                array('emp_telephone2', 'text', "Téléphone 2", 'emp_telephone2'),
                array('emp_email', 'text', "Email", 'emp_email'),
                array('emp_h_jour', 'text', "Nb. heures / jour", 'emp_h_jour'),
                array('emp_h_semaine', 'text', "Nb. heures / semaine", 'emp_h_semaine'),
                array('emp_h_mois', 'text', "Nb. heures / mois", 'emp_h_mois'),
                array('emp_cout_heure', 'text', "Coût horaire", 'emp_cout_heure'),
                array('emp_cv_vehicule', 'text', "Nb. CV véhicule", 'emp_cv_vehicule'),
                array('carte_grise', 'text', "Scan carte grise", 'carte_grise'),
                array('emp_immatriculation', 'text', "Immatriculation", 'emp_immatriculation'),
                array('emp_ptc', 'text', "Poids total en charge", 'emp_ptc'),
                array('emp_indemnite_kilometrique', 'text', "indemnité kilométrique", 'emp_indemnite_kilometrique'),
            ),
            'write' => array(
                'utl_login'                  => array("Login", 'text', 'utl_login', false),
                'utl_mot_de_passe'           => array("Mot de passe", 'password-c', 'utl_mot_de_passe', false),
                '__utl_mot_de_passe'         => array("Mot de passe (confirmation)", 'password-c', '__utl_mot_de_passe', false),
                'utl_type'                   => array("Fonction", 'select', array('utl_type', 'vtu_id', 'vtu_type'), false),
                'utl_sous_traitant'          => array("Type", 'select', array('utl_sous_traitant', 'id', 'value'), false),
                'utl_profil'                 => array("Profil", 'select', array('utl_profil', 'prf_id', 'prf_description'), false),
                'utl_date_fin'               => array("Date de fin de validité", 'date', 'utl_date_fin', false),
                'utl_en_production'          => array("En production", 'checkbox', 'utl_en_production', false),
                'emp_civilite'               => array("Civilite", 'radio-h', array('emp_civilite', 'vcv_id', 'vcv_civilite'), false),
                'emp_nom'                    => array("Nom", 'text', 'emp_nom', false),
                'emp_prenom'                 => array("Prénom", 'text', 'emp_prenom', false),
                // 'emp_fonction'               => array("Fonction", 'select', array('emp_fonction', 'vfo_id', 'vfo_fonction'), false),
                //'emp_commission'             => array("Commissions", 'checkbox', 'emp_commission', false),
                'utl_ensigne'                => array("Enseignes", 'select-multiple', array('utl_ensigne', 'scv_id', 'scv_nom'), false),
                'emp_commission'             => array("Commissions", 'select', array('emp_commission', 'id', 'value'), false),
                'emp_date_entree'            => array("Date d'entrée", 'date', 'emp_date_entree', false),
                'emp_date_sortie'            => array("Date de sortie", 'date', 'emp_date_sortie', false),
                'emp_etat'                   => array("Etat", 'radio-h', array('emp_etat', 'vee_id', 'vee_etat'), false),
                'emp_notes'                  => array("Remarques", 'textarea', 'emp_notes', false),
                'emp_adresse'                => array("Adresse", 'textarea', 'emp_adresse', false),
                'emp_cp'                     => array("Code postal", 'number', 'emp_cp', false),
                'emp_ville'                  => array("Ville", 'text', 'emp_ville', false),
                'emp_telephone1'             => array("Téléphone 1", 'number', 'emp_telephone1', false),
                'emp_telephone2'             => array("Téléphone 2", 'number', 'emp_telephone2', false),
                'emp_email'                  => array("Email", 'email', 'emp_email', false),
                'emp_h_jour'                 => array("Nb. heures / jour", 'number', 'emp_h_jour', false),
                'emp_h_semaine'              => array("Nb. heures / semaine", 'number', 'emp_h_semaine', false),
                'emp_h_mois'                 => array("Nb. heures / mois", 'number', 'emp_h_mois', false),
                'emp_cout_heure'             => array("Coût horaire", 'number', 'emp_cout_heure', false),
                'emp_cv_vehicule'            => array("Nb. CV véhicule", 'number', 'emp_cv_vehicule', false),
                'emp_immatriculation'        => array("Immatriculation", 'number', 'emp_immatriculation', false),
                'emp_ptc'                    => array("Poids total en charge", 'number', 'emp_ptc', false),
                'emp_indemnite_kilometrique' => array("Indemnité Kilométrique", 'number', 'emp_indemnite_kilometrique', false),
            ),
        );

        return $champs[$type];
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        /*
        $first = "(SELECT vehicules_id,utilisateur,GROUP_CONCAT(filename) as carte_grise
        FROM t_vehicules left join t_files on row_id = vehicules_id
        where name = 'vehicules_carte_grise' and utilisateur > 0
        group by utilisateur) AS fname";
        $sel = $this->db->get_compiled_select($first,true);
        $table = 't_utilisateurs';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $utl_refid    = "CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END";
        $utl_refid_as = $utl_refid . " AS utl_refid";
        $utl_nom      = "CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END";
        $utl_nom_as   = $utl_nom . " AS utl_nom";
        //$carte_grise  = 't_files.filename AS carte_grise';
        $this->db->select("utl_id,utl_id as RowID,
        utl_login,
        utl_type,
        vtu_type,
        utl_employe,
        ctc_nom,
        utl_profil,
        prf_nom,
        utl_derniere_connexion,
        utl_date_fin,utl_actif,
        utl_en_production,
        vcv_civilite,
        vfo_fonction,
        vee_etat,
        t_employes.*,
        $utl_refid_as,
        $utl_nom_as"
        , false);

        $this->db->join('v_types_utilisateurs', 'vtu_id=utl_type', 'left');
        $this->db->join('t_employes', 'emp_id=utl_employe', 'left');
        $this->db->join('v_civilites', 'vcv_id=emp_civilite', 'left');
        $this->db->join('v_fonctions', 'vfo_id=emp_fonction', 'left');
        $this->db->join('v_etats_employes', 'vee_id=emp_etat', 'left');
        $this->db->join('t_contacts', 'ctc_id=utl_sous_traitant', 'left');
        $this->db->join('t_profils', 'prf_id=utl_profil', 'left');
        $this->db->join(($sel),'utilisateur = utl_employe and utl_employe <> 0','left');
        $this->db->group_by('utl_id');
        //$this->db->where('utl_inactif is null');
        //$this->db->order_by("utl_login","ASC");
         */
        $this->db->start_cache();
        $this->db->select('*');
        $this->db->stop_cache();

        $utl_refid = "CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END AS utl_refid";
        $utl_nom   = "CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END AS utl_nom";
        $table     = " ( SELECT
                utl_id,
                utl_id as RowID,
                utl_login,
                utl_type,
                vtu_type,
                utl_employe,
                ctc_nom,
                utl_profil,
                prf_nom,
                prf_description,
                utl_derniere_connexion,
                utl_date_fin,
                utl_en_production,
                vcv_civilite,
                vfo_fonction,
                vee_etat,
                emp_nom,
                emp_prenom,
                emp_date_entree,
                emp_date_sortie,
                emp_adresse,
                emp_cp,
                emp_ville,
                emp_telephone1,
                emp_telephone2,
                emp_email,
                emp_h_jour,
                emp_h_semaine,
                emp_h_mois,
                emp_cout_heure,
                emp_cv_vehicule,
                emp_immatriculation,
                emp_ptc,
                emp_indemnite_kilometrique,           
                (select GROUP_CONCAT(scv_nom) from t_societes_vendeuses where find_in_set(scv_id,utl_ensigne)) as utl_ensigne,
                if(emp_commission = 0, 'Non', 'Oui') as emp_commission,
                if(utl_actif = 0, 'Non', 'Oui') as utl_actif,
                if(utl_type = 1, utl_employe, utl_sous_traitant) as utl_refid,
                if(utl_type = 1, emp_nom, ctc_nom ) as utl_nom,
                carte_grise
                from t_utilisateurs
                LEFT JOIN v_types_utilisateurs ON vtu_id=utl_type
                LEFT JOIN t_employes ON emp_id=utl_employe
                LEFT JOIN v_civilites ON vcv_id=emp_civilite
                LEFT JOIN v_fonctions ON vfo_id=emp_fonction
                LEFT JOIN v_etats_employes ON vee_id=emp_etat
                LEFT JOIN t_contacts ON ctc_id=utl_sous_traitant
                LEFT JOIN t_profils ON prf_id=utl_profil              
                LEFT JOIN (
                SELECT vehicules_id,utilisateur, row_id,name,GROUP_CONCAT(filename) as carte_grise
                FROM t_vehicules left join t_files on row_id = vehicules_id
                where name = 'vehicules_carte_grise' and utilisateur > 0
                group by utilisateur) as fname ON utilisateur = utl_employe and utl_employe <> 0";

        switch ($void) {
            case 'archived':
                $table .= ' where utl_archiver != "0000-00-00 00:00:00"';
                break;
            case 'deleted':
                $table .= ' where utl_inactif != "0000-00-00 00:00:00"';
                break;
            case 'all':
                break;
            default:
                $table .= " where utl_archiver is NULL AND utl_inactif is NULL";
                break;
        }

        if ($filters == null) {
            $table .= ' AND emp_etat = 1';
        }

        $id = intval($void);
        if ($id > 0) {
            $table .= " AND utl_id = " . $id;
        }
        $table .= ") ";

/*
switch ($void) {
case 'archived':
$this->db->where('utl_archiver != "0000-00-00 00:00:00"');
break;
case 'deleted':
$this->db->where('utl_inactif != "0000-00-00 00:00:00"');
break;
case 'all':
break;
default:
$this->db->where('utl_archiver is NULL');
$this->db->where('utl_inactif is NULL');
break;
}
$id = intval($void);
if ($id > 0) {
$this->db->where('utl_id', $id);
}
 */
        //$this->db->stop_cache();

        // aliases
        $aliases = array(
            'utl_refid' => $utl_refid,
            'utl_nom'   => $utl_nom,

        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering, true);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->utl_id . '">';

            $filename = explode(',', $resultat['data'][$i]->carte_grise);
            if (is_array($filename)) {
                $final_filename = array();
                foreach ($filename as $row) {
                    $final_filename[] = '<a href="' . base_url() . '/fichiers/carte_grise/' . $row . '" target="_blank">' . $row . '</a>';
                }
                $final_filename = implode('<br>', $final_filename);
            } else {
                $file           = $resultat['data'][$i]->carte_grise;
                $final_filename = '<a href="' . base_url() . '/fichiers/carte_grise/' . $file . '" target="_blank">' . $file . '</a>';
            }
            $resultat['data'][$i]->carte_grise = $final_filename;
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'utl_login'                  => 'char',
            'vtu_type'                   => 'char',
            'prf_nom'                    => 'char',
            'utl_derniere_connexion'     => 'date',
            'utl_date_fin'               => 'date',
            'utl_actif'                  => 'char',
            'utl_refid'                  => 'char',
            'vcv_civilite'               => 'char',
            'emp_prenom'                 => 'char',
            'utl_nom'                    => 'char',
            'vfo_fonction'               => 'char',
            'emp_date_entree'            => 'date',
            'emp_date_sortie'            => 'date',
            'emp_commission'             => 'int',
            'vee_etat'                   => 'char',
            'utl_login'                  => 'char',
            'emp_adresse'                => 'char',
            'emp_cp'                     => 'char',
            'emp_ville'                  => 'char',
            'emp_telephone1'             => 'char',
            'emp_telephone2'             => 'char',
            'emp_email'                  => 'char',
            'emp_h_jour'                 => 'int',
            'emp_h_semaine'              => 'int',
            'emp_h_mois'                 => 'int',
            'emp_cout_heure'             => 'int',
            'emp_cv_vehicule'            => 'int',
            'carte_grise'                => 'char',
            'emp_immatriculation'        => 'int',
            'emp_ptc'                    => 'int',
            'emp_indemnite_kilometrique' => 'int',
            'utl_ensigne'                => 'char',

        );

        return $filterable_columns;
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function detail($id)
    {
        $table = 't_utilisateurs';
        // première partie du select, mis en cache
        $this->db->select("utl_id,
            utl_id as RowID,
            utl_login,
            utl_type,
            vtu_type,
            utl_employe,
            ctc_nom,
            utl_profil,
            prf_nom,
            utl_derniere_connexion,
            utl_date_fin,utl_actif,
            utl_en_production,
            utl_sous_traitant,
            vcv_civilite,
            vfo_fonction,
            vee_etat,
            utl_ensigne,
            t_employes.*");

        $this->db->join('v_types_utilisateurs', 'vtu_id=utl_type', 'left');
        $this->db->join('t_employes', 'emp_id=utl_employe', 'left');
        $this->db->join('v_civilites', 'vcv_id=emp_civilite', 'left');
        $this->db->join('v_fonctions', 'vfo_id=emp_fonction', 'left');
        $this->db->join('v_etats_employes', 'vee_id=emp_etat', 'left');
        $this->db->join('t_contacts', 'ctc_id=utl_sous_traitant', 'left');
        $this->db->join('t_profils', 'prf_id=utl_profil', 'left');
        $this->db->where('utl_id', $id);
        $q      = $this->db->get($table);
        $result = $q->row();
        //debug($result,1);
        return $result;
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_utilisateurs', $id, 'utl_id', 'utl_archiver');
    }

    /******************************
     * Archive test mails data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_utilisateurs', $id, 'utl_id', 'utl_inactif');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('utl_inactif' => null, 'utl_archiver' => null);
        return $this->_update('t_utilisateurs', $data, $id, 'utl_id');
    }

    public function dupliquer($id)
    {
        $this->load->model(array('m_utilisateurs', 'm_employes'));

        $row = $this->db->get_where('t_utilisateurs', array('utl_id' => $id))->row_array();

        if ($row) {
            $employe   = $this->db->get_where('t_employes', array('emp_id' => $row['utl_employe']))->row_array();
            $random_id = mt_rand(10, 100);

            unset($row['utl_id']);
            unset($employe['emp_id']);
            $row['utl_login']    = $row['utl_login'] . "-copy" . $random_id;
            $row['utl_archiver'] = null;
            $row['utl_inactif']  = null;

            $emp_id = $this->m_employes->nouveau($employe);

            if ($emp_id) {
                $row['utl_employe'] = $emp_id;
                $id                 = $this->m_utilisateurs->nouveau($row);

                if ($id) {
                    return $id;
                }

                return false;
            }

            return false;

        }

        return false;
    }

    public function liste_type()
    {
        $this->db->order_by('vtu_type', 'ASC');
        $q = $this->db->get('v_types_utilisateurs');
        return $q->result();
    }

    public function liste_employe()
    {
        $this->db->order_by('emp_nom', 'ASC');
        $q = $this->db->get('t_employes');
        return $q->result();
    }

    /*public function liste_sous_traitant()
    {
    $this->db->where("ctc_fournisseur",1);
    $this->db->order_by('ctc_nom', 'ASC');
    $q = $this->db->get('t_contacts');
    return $q->result();
    }*/

    public function type_option()
    {
        $values = array('sous-traitant', 'salarié', 'autres');
        //return $this->form_option($values);
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();

            $val->id    = $i + 1;
            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }

    //(choisissez)
    public function liste_commision_option()
    {
        //$values = array('(choisissez)', 'Non', 'Oui');
        $values = array('Non', 'Oui');
        //return $this->form_option($values);
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();

            $val->id    = $i;
            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }

    public function liste_profil()
    {
        $this->db->order_by('prf_id', 'ASC');
        $q = $this->db->get('t_profils');

        return $q->result();
    }

    public function liste_civilite()
    {
        $this->db->order_by('vcv_civilite', 'ASC');
        $q = $this->db->get('v_civilites');

        return $q->result();
    }

    public function liste_fonction()
    {
        $this->db->order_by('vfo_fonction', 'ASC');
        $q = $this->db->get('v_fonctions');

        return $q->result();
    }

    public function liste_etat()
    {
        $this->db->order_by('vee_etat', 'ASC');
        $q = $this->db->get('v_etats_employes');

        return $q->result();
    }

    public function liste_ensigne_option()
    {
        $this->db->flush_cache();
        $this->db->reset_query();
        $this->db->select("scv_id,scv_nom");
        $this->db->order_by('scv_nom', 'ASC');
        $res = $this->db->get('t_societes_vendeuses')->result();
        return $res;
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
