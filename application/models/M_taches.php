<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_taches extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des tâches sous_traitées
    ******************************/
    public function liste_sous_traitees($pere) {

        // lecture des informations
        $this->db->select("tac_id,tac_titre,tac_description,tac_info,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat",false);
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where("tac_emetteur",$pere);
        $this->db->where('tac_inactif is null');
        $this->db->order_by("tac_fin_prevue");
            $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Liste des tâches à faire
    ******************************/
    public function liste_affectees($pere) {

        // lecture des informations
        $this->db->select("tac_id,tac_emetteur,utl_login,tac_titre,tac_description,tac_info,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real,tac_employe,emp_nom,tac_type,vtt_type,tac_etat,vet_etat",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where("tac_employe",$pere);
        $this->db->where("tac_employe > 0");
        $this->db->where("tac_etat = 1");
        $this->db->where('tac_inactif is null');
        $this->db->order_by("tac_fin_prevue");
            $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Liste des tâches
    ******************************/
    /**
    public function liste() {

        // lecture des informations
        
        $this->db->select("tac_id,tac_emetteur,utl_login,tac_titre,tac_description,tac_info,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where('tac_inactif is null');
        $this->db->order_by("tac_debut_prevu asc");
        $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    } **/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        // première partie du select, mis en cache
        $table = 't_taches';
        $this->db->start_cache();
        
        $utl_login = formatte_sql_lien('utilisateur/detail','tac_emetteur','utl_login');
        $tac_titre = formatte_sql_lien('taches/detail', 'tac_id', 'tac_titre');
        $emp_nom   = formatte_sql_lien('employes/detail', 'tac_employe', 'emp_nom');
        $ctc_nom   = formatte_sql_lien('contacts/detail', 'tac_sous_traitant', 'ctc_nom');

        //formatter date
        $tac_debut_prevu = formatte_sql_date_heure("tac_debut_prevu");
        $tac_fin_prevue  = formatte_sql_date("tac_fin_prevue");
        $tac_debut_real  = formatte_sql_date("tac_debut_real");
        $tac_fin_real    = formatte_sql_date("tac_fin_real");

        $this->db->select("tac_id,tac_id as id,tac_id as RowID,tac_emetteur,$utl_login,$tac_titre,tac_description,tac_info,$tac_debut_prevu,$tac_fin_prevue,$tac_debut_real,$tac_fin_real,tac_travail_prevu,tac_travail_real,tac_employe,$emp_nom,tac_sous_traitant,$ctc_nom,tac_type,vtt_type,tac_etat,vet_etat",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        //$this->db->where('tac_inactif is null');
        switch($void){
            case 'archived':
                $this->db->where($table.'.tac_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.tac_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.tac_archiver is NULL');
                $this->db->where($table.'.tac_inactif is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('tac_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
           
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

         //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->id.'">';
        }

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'tac_titre' => 'char'
        );

        return $filterable_columns;
    }

    /******************************
    * Liste des tâches associées à un employé
    ******************************/
    public function liste_par_employe($pere) {

        // lecture des informations
        $this->db->select("tac_id,tac_emetteur,utl_login,tac_titre,tac_description,tac_info,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where("tac_employe",$pere);
        $this->db->where('tac_inactif is null');
        $this->db->order_by("tac_debut_prevu asc");
            $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Nouvelle tâche
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_taches', $data);
    }

    /******************************
    * Détail d'une tâche
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("tac_id,tac_titre,tac_description,tac_info,tac_emetteur,utl_login,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where('tac_id',$id);
        $this->db->where('tac_inactif is null');
        $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Détail d'une tâche
    ******************************/
    public function detail_sous_traitee($id) {

        // lecture des informations
        $this->db->select("tac_id,tac_titre,tac_description,tac_info,tac_emetteur,utl_login,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where('tac_id',$id);
        $this->db->where('tac_inactif is null');
        $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Détail d'une tâche
    ******************************/
    public function detail_affectee($id) {

        // lecture des informations
        $this->db->select("tac_id,tac_titre,tac_description,tac_info,tac_emetteur,utl_login,tac_employe,emp_nom,tac_sous_traitant,ctc_nom,tac_type,vtt_type,tac_etat,vet_etat,tac_debut_prevu,tac_fin_prevue,tac_debut_real,tac_fin_real,tac_travail_prevu,tac_travail_real",false);
        $this->db->join('t_utilisateurs','utl_id=tac_emetteur','left');
        $this->db->join('t_employes','emp_id=tac_employe','left');
        $this->db->join('t_contacts','ctc_id=tac_sous_traitant','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->where('tac_id',$id);
        $this->db->where('tac_inactif is null');
        $q = $this->db->get('t_taches');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une tâche
    ******************************/
    public function maj($data,$id) {
        return $this->_update('t_taches',$data,$id,'tac_id');
    }

    /******************************
     * Mise de la tâche à l'état annulée
     ******************************/
    public function annuler($id) {
        $data = array('tac_etat'=>3);
        $update = $this->_update('t_taches', $data,$id,'tac_id');
        if ($update) {
            $data = array(
                'evt_commentaire'=>'annulation',
                'evt_date'=>date('Y-m-d H:i:s'),
                'evt_tache'=>$id,
                'evt_etat'=>3
            );
            $new = $this->_insert('t_evenements_taches', $data);
            if ($new > 0) {
                return $id;
            }
        }
        return false;
    }

    /******************************
     * Nombre de tâches nouvelles
     ******************************/
    public function nb_nouvelles($id) {

        $this->db->select("count(*)")
            ->where('tac_employe',$id)
            ->where('tac_etat',1)
            ->where('tac_inactif is null');
        return $this->db->count_all_results('t_taches');
    }

    /******************************
     * Nouvelle tâche pour un utilisateur
     ******************************/
    public function nouveau_utilisateur($data) {

        // récupération desinfirmations de l'utilisateur
        $q = $this->db->select("utl_employe,utl_sous_traitant")
            ->where('utl_id',$data['tac_employe'])
            ->where('utl_inactif is null')
            ->get('t_utilisateurs');
        if ($q->num_rows() == 0) return false;
        $data['tac_employe'] = $q->row()->utl_employe;
        $data['tac_sous_traitant'] = $q->row()->utl_sous_traitant;
        return $this->_insert('t_taches', $data);
    }

    /******************************
     * Mise de la tâche à l'état terminée
     ******************************/
    public function terminer($data,$id) {
        $fin = date('Y-m-d H:i:s');
        $data['tac_etat'] = 2;
        $data['tac_fin_real'] = $fin;
        $update = $this->_update('t_taches', $data,$id,'tac_id');
        if ($update) {
            $data = array(
                'evt_commentaire'=>'marquée terminée',
                'evt_date'=>$fin,
                'evt_tache'=>$id,
                'evt_etat'=>2
            );
            $new = $this->_insert('t_evenements_taches', $data);
            if ($new > 0) {

                // récupération de l'utilisateur courant et de l'émetteur de la tâche
                $user = $this->session->id;
                $q = $this->db->select('tac_emetteur')
                    ->where('tac_id',$id)
                    ->get('t_taches');
                if ($q->num_rows() > 0) {
                    $emetteur = $q->row()->tac_emetteur;

                    // génération d'une alerte si l'utilisateur courant n'est pas l'émetteur
                    // et qu'il a demandé à recevoir ce type d'alerte
                    if ($user != $emetteur) {
                        $q = $this->db->where('utl_affichage_alerte',1)
                            ->where('utl_id',$emetteur)
                            ->count_all_results('t_utilisateurs');
                        if ($q > 0) {
                            $data = array(
                                'ale_date' => $fin,
                                'ale_evenement' => $new,
                                'ale_tache' => $id,
                                'ale_destinataire' => $emetteur,
                                'ale_type' => 4,
                                'ale_etat' => 1
                            );
                            $this->db->insert('t_alertes',$data);
                        }
                    }
                }
                return $id;
            }
        }
        return false;
    }

    public function suppression($id)
    {
        return $this->_delete('t_taches',$id,'tac_id','tac_inactif');
    }

    /******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_taches',$id,'tac_id','tac_archiver');
    }

    /******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_taches',$id,'tac_id','tac_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('tac_inactif' => null, 'tac_archiver' => null);
        return $this->_update('t_taches',$data, $id,'tac_id');
    }

}
// EOF
