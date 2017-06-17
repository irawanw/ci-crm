<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_utilisateurs extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Déconnexion
    ******************************/
    public function deconnexion() {
        $session_data = $this->session->userdata();
        foreach ($session_data as $k=>$v) {
            $this->session->unset_userdata($k);
        }
        return true;
    }

    /******************************
    * Connexion
    ******************************/
    public function connexion($conditions) {
        $mot_de_passe = $conditions['utl_mot_de_passe'];
        unset($conditions['utl_mot_de_passe']);
        $this->db->select("utl_id,utl_login,utl_profil,prf_nom,utl_mot_de_passe,sys_salt,utl_en_production",false);
        $this->db->join('t_profils','prf_id=utl_profil');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('v_etats_employes','vee_id=emp_etat','left');

        foreach ($conditions as $k=>$c) {
            $this->db->where($k,$c);
        }
        $this->db->where('utl_inactif is null');
        $this->db->where('emp_etat = 1');
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $info = $q->row_array();
            $hash = $info['utl_mot_de_passe'];
            $salt = $info['sys_salt'];
            if ($hash === sha1($mot_de_passe.$salt)) {
                $this->db->where('utl_id',$info['utl_id'])
                    ->update('t_utilisateurs', array('utl_derniere_connexion'=>date('Y-m-d H:i:s')));
                unset($info['utl_mot_de_passe']);
                $info['id'] = $info['utl_id'];
                unset($info['utl_id']);
                $info['profil'] = $info['prf_nom'];
                unset($info['prf_nom']);
                $this->session->set_userdata($info);
                $this->contexte->init();
                return true;
            }
        }
        return false;
    }

    /******************************
    * Liste des utilisateurs
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $prf_nom = formatte_sql_lien('profils/detail','prf_id','prf_nom');
        $utl_derniere_connexion = formatte_sql_date_heure('utl_derniere_connexion');
        $utl_date_fin = formatte_sql_date('utl_date_fin');
        $utl_refid = "CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END";
        $utl_refid2 = $utl_refid ." AS utl_refid";
        $utl_nom = "CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END";
        $utl_nom2 = $utl_nom ." AS utl_nom";
        $this->db->select("utl_id AS RowID,utl_id,$utl_login,vtu_type,$emp_nom,$ctc_nom,$prf_nom,$utl_derniere_connexion,$utl_date_fin,utl_actif,utl_en_production,$utl_refid2,$utl_nom2",false);
        $this->db->join('v_types_utilisateurs','vtu_id=utl_type','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('t_contacts','ctc_id=utl_sous_traitant','left');
        $this->db->join('t_profils','prf_id=utl_profil','left');
        $this->db->where('utl_inactif is null');
        //$this->db->order_by("utl_login asc");
        $id = intval($void);
        if ($id > 0) {
         $this->db->where('utl_id', $id);
        }

        $this->db->stop_cache();

        $table = 't_utilisateurs';

        // aliases
        $aliases = array(
            'utl_refid'=>$utl_refid,
            'utl_nom'=>$utl_nom
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'utl_login'=>'char',
            'vtu_type'=>'char',
            'emp_nom'=>'char',
            'ctc_nom'=>'char',
            'prf_nom'=>'char',
            'utl_derniere_connexion'=>'datetime',
            'utl_date_fin'=>'date',
            'utl_actif'=>'char',
            'utl_en_production'=>'char',
            'utl_refid'=>'char',
            'utl_nom'=>'char'
        );
        return $filterable_columns;
    }

    public function liste_empty_params() {

        // lecture des informations
        $this->db->select("utl_id,utl_login,utl_type,vtu_type,utl_employe,emp_nom,utl_sous_traitant,ctc_nom,utl_profil,prf_nom,utl_derniere_connexion,utl_date_fin,utl_actif,utl_en_production,CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END AS utl_refid,CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END AS utl_nom",false);
        $this->db->join('v_types_utilisateurs','vtu_id=utl_type','left');
        $this->db->join('t_employes','emp_id=utl_employe');
        $this->db->join('t_contacts','ctc_id=utl_sous_traitant','left');
        $this->db->join('t_profils','prf_id=utl_profil','left');
        $this->db->where('utl_inactif is null');
        $this->db->order_by("utl_login asc");
        $this->db->group_by('emp_nom');
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Liste json
    ******************************/
    public function web_service() {

        // lecture des informations
        $this->db->select("utl_employe,emp_nom,emp_id,emp_prenom,emp_nom",false);
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->where('utl_inactif is null');
        $this->db->order_by("emp_nom asc");
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Liste des utilisateurs d'un profil
    ******************************/
    public function liste_par_profil($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $prf_nom = formatte_sql_lien('profils/detail','prf_id','prf_nom');
        $utl_derniere_connexion = formatte_sql_date_heure('utl_derniere_connexion');
        $utl_date_fin = formatte_sql_date('utl_date_fin');
        $utl_refid = "CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END";
        $utl_refid2 = $utl_refid ." AS utl_refid";
        $utl_nom = "CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END";
        $utl_nom2 = $utl_nom ." AS utl_nom";
        $this->db->select("utl_id AS RowID,utl_id,$utl_login,vtu_type,$emp_nom,$ctc_nom,$prf_nom,$utl_derniere_connexion,$utl_date_fin,utl_en_production,utl_actif,$utl_refid2,$utl_nom2",false);
        $this->db->join('v_types_utilisateurs','vtu_id=utl_type','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('t_contacts','ctc_id=utl_sous_traitant','left');
        $this->db->join('t_profils','prf_id=utl_profil','left');
        $this->db->where("utl_profil",$pere);
        $this->db->where('utl_inactif is null');
        //$this->db->order_by("utl_login asc");
        $this->db->stop_cache();

        $table = 't_utilisateurs';

        // aliases
        $aliases = array(
            'utl_refid'=>$utl_refid,
            'utl_nom'=>$utl_nom
        );

        $resultat = $this->_filtre($table,$this->liste_par_profil_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_profil_filterable_columns() {
    $filterable_columns = array(
            'utl_login'=>'char',
            'vtu_type'=>'char',
            'emp_nom'=>'char',
            'ctc_nom'=>'char',
            'prf_nom'=>'char',
            'utl_derniere_connexion'=>'datetime',
            'utl_date_fin'=>'date',
            'utl_en_production'=>'char',
            'utl_actif'=>'char',
            'utl_refid'=>'char',
            'utl_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvel utilisateur
    ******************************/
    public function nouveau($data) {
        if (isset($data['utl_mot_de_passe'])) {
            $salt = substr(md5(uniqid(rand(), true)), 0, 6);
            $data['utl_mot_de_passe'] = sha1($data['utl_mot_de_passe'].$salt);
            $data['sys_salt'] = $salt;
        }
        $id = $this->_insert('t_utilisateurs', $data);
        return $id;
    }

    /******************************
    * Détail d'un utilisateur
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("utl_id,utl_login,utl_employe,emp_nom,utl_sous_traitant,ctc_nom,utl_profil,prf_nom,utl_derniere_connexion,utl_date_fin,utl_actif,utl_en_production",false);
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('t_contacts','ctc_id=utl_sous_traitant','left');
        $this->db->join('t_profils','prf_id=utl_profil','left');
        $this->db->where('utl_id',$id);
        $this->db->where('utl_inactif is null');
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un utilisateur
    ******************************/
    public function maj($data,$id) {
        if (array_key_exists('utl_mot_de_passe',$data)) {
            if ($data['utl_mot_de_passe'] != '') {
                $q = $this->db->select('sys_salt')
                    ->where('utl_id',$id)
                    ->get('t_utilisateurs');
                if ($q->num_rows() > 0) {
                    $data['utl_mot_de_passe'] = sha1($data['utl_mot_de_passe'].$q->row()->sys_salt);
                }
            }
            else {
                unset($data['utl_mot_de_passe']);
            }
        }
        $q = $this->db->where('utl_id',$id)->get('t_utilisateurs');
        $res =  $this->_update('t_utilisateurs',$data,$id,'utl_id');
        return $res;
    }

/******************************
    * Suppression d'un utilisateur
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('utl_id',$id)->get('t_utilisateurs');

            $res = $this->_delete('t_utilisateurs',$id,'utl_id','utl_inactif');
        return $res;
    }

    /******************************
    * Mise à jour des paramètres d'alerte
    ******************************/
    public function maj_param($data,$id) {
        if (array_key_exists('utl_mot_de_passe',$data)) {
            if ($data['utl_mot_de_passe'] != '') {
                $q = $this->db->select('sys_salt')
                    ->where('utl_id',$id)
                    ->get('t_utilisateurs');
                if ($q->num_rows() > 0) {
                    $data['utl_mot_de_passe'] = sha1($data['utl_mot_de_passe'].$q->row()->sys_salt);
                }
            }
            else {
                unset($data['utl_mot_de_passe']);
            }
        }
        $q = $this->db->where('utl_id',$id)->get('t_utilisateurs');
        $res =  $this->_update('t_utilisateurs',$data,$id,'utl_id');
        return $res;
    }

    /******************************
    * Détail des paramètres d'alerte
    ******************************/
    public function detail_param($id) {

        // lecture des informations
        $this->db->select("utl_id,utl_duree_alerte,utl_son_alerte,vso_son,utl_affichage_alerte",false);
        $this->db->join('v_sons','vso_id=utl_son_alerte','left');
        $this->db->where('utl_id',$id);
        $this->db->where('utl_inactif is null');
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Déconnexion d'un utilisateur
     ******************************/
    public function deconnexion_user($id) {

        // recherche des sessions associées
        $this->load->helper('directory');
        $source = 'sessions/';
        $map = directory_map($source);
        foreach($map as $m) {
            if ($m == 'index.html') continue;
            $session = file_get_contents($source.$m);
            $items = explode(';',$session);
            foreach($items as $i) {
                $champ = explode('|',$i);
                if ($champ[0] == 'id') {
                    $id2 = unserialize($champ[1]);
                    if ($id == $id2) {
                        unlink($source.$m);
                        break;
                    }
                }
            }
        }
        return $id;
    }

    /******************************
     * Informations de la page d'accueil
     ******************************/
    public function info_accueil($id) {
    }

    /******************************
    * Liste des utilisateurs
    ******************************/
    public function liste_option() {

        // lecture des informations
        $this->db->select("utl_id,utl_login,utl_type,vtu_type,utl_employe,emp_nom,utl_sous_traitant,ctc_nom,utl_profil,prf_nom,utl_derniere_connexion,utl_date_fin,utl_actif,utl_en_production,CASE utl_type WHEN 1 THEN utl_employe WHEN 2 THEN utl_sous_traitant END AS utl_refid,CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END AS utl_nom",false);
        $this->db->join('v_types_utilisateurs','vtu_id=utl_type','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('t_contacts','ctc_id=utl_sous_traitant','left');
        $this->db->join('t_profils','prf_id=utl_profil','left');
        $this->db->where('utl_inactif is null');
        $this->db->order_by("utl_login asc");
        $q = $this->db->get('t_utilisateurs');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

}
// EOF
