<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_alertes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('ale_id','id#',"Identifiant"),
                array('ale_date','datetime',"Date d'apparition"),
                array('evt_commentaire','text',"Commentaire"),
                array('tac_titre','ref',"Tâche associée",'taches','ale_tache','tac_titre'),
                array('vtt_type','ref',"Type de tâche",'v_types_taches'),
                array('vet_etat','ref',"État de la tâche",'v_etats_taches'),
                array('vtal_type','ref',"Type de l'alerte",'v_types_alertes'),
                array('vea_etat','ref',"État de l'alerte",'v_etats_alertes'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array()
        );

        return $champs[$type];
    }

    /******************************
    * Liste des alertes
    ******************************/
    public function liste($void, $pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_alertes';
        $this->db->start_cache();

        // lecture des informations
        //$ale_date = formatte_sql_date_heure_lien('alertes/detail','ale_id','ale_date');
        $ale_date = formatte_sql_date_heure('ale_date');
        $ale_id = formatte_sql_lien('alertes/detail','ale_id','ale_id');
        $evt_id = formatte_sql_lien('evenements_taches/detail','evt_id','evt_id');
        $tac_titre = formatte_sql_lien('taches/detail','tac_id','tac_titre');
        $this->db->select("ale_id AS RowID,ale_id as id,ale_id,$ale_date,$ale_id,$evt_id,evt_commentaire,$tac_titre,vtt_type,vet_etat,vtal_type,vea_etat",false);
        $this->db->join('t_evenements_taches','evt_id=ale_evenement','left');
        $this->db->join('t_taches','tac_id=ale_tache','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_etats_taches','vet_id=tac_etat','left');
        $this->db->join('v_types_alertes','vtal_id=ale_type','left');
        $this->db->join('v_etats_alertes','vea_id=ale_etat','left');
        
        if($pere != 0)
            $this->db->where("ale_destinataire",$pere);
        //$this->db->where('ale_inactif is null');
        //$this->db->order_by("ale_date desc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.ale_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.ale_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.ale_archiver is NULL');
                $this->db->where($table.'.ale_inactif is NULL');
                break;
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
            'ale_id'=>'int',
            'ale_date'=>'datetime',
            'evt_id'=>'int',
            'evt_commentaire'=>'char',
            'tac_titre'=>'char',
            'vtt_type'=>'char',
            'vet_etat'=>'char',
            'vtal_type'=>'char',
            'vea_etat'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Acquittement de l'alerte
    ******************************/
    public function acquitter($id) {
        $data = array('ale_etat'=>3);
        return $this->_update('t_alertes',$data,$id,'ale_id');
    }

    /******************************
     * Consultation de l'alerte
     ******************************/
    public function consulter($id) {

        // lecture des informations
        $this->db->select("ale_id,ale_date,ale_evenement,evt_id,evt_commentaire,ale_tache,tac_id,tac_titre,tac_type,vtt_type,ale_type,vtal_type,ale_etat,vea_etat",false);
        $this->db->join('t_evenements_taches','evt_id=ale_evenement','left');
        $this->db->join('t_taches','tac_id=ale_tache','left');
        $this->db->join('v_types_taches','vtt_id=tac_type','left');
        $this->db->join('v_types_alertes','vtal_id=ale_type','left');
        $this->db->join('v_etats_alertes','vea_id=ale_etat','left');
        $this->db->where('ale_id',$id);
        $this->db->where('ale_inactif is null');
        $q = $this->db->get('t_alertes');
        if ($q->num_rows() > 0) {

            // mise à jour de l'état de l'alerte
            $data = array(
                'ale_etat'=>2
            );
            $this->_update('t_alertes', $data,$id,'ale_id',array('ale_etat'=>1));

            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }

    }

    /******************************
     * Nombre d'alertes non acquittées
     ******************************/
    public function nb_non_acquittees($id) {

        $this->db->select("count(*)")
            ->where('ale_destinataire',$id)
            ->where('ale_etat <',3)
            ->where('ale_inactif is null');
        return $this->db->count_all_results('t_alertes');
    }

    /******************************
     * Vérification des nouvelles alertes
     ******************************/
    public function verification() {

        // tâches de type rappel arrivées à échéance
        $this->db->select("tac_id,tac_info,tac_description")
            ->where('tac_debut_prevu < NOW()')
            ->where('tac_employe',$this->session->id)
            ->where('tac_type',9)
            ->where('tac_etat',1);
        $q = $this->db->get('t_taches');
        $resultat = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $r) {

                // création de l'alerte
                $data = array(
                    'ale_date' => date('Y-m-d H:i:s'),
                    'ale_evenement' => 0,
                    'ale_tache' => $r->tac_id,
                    'ale_destinataire' => $this->session->id,
                    'ale_type' => 1,
                    'ale_etat' => 1
                );
                $this->db->insert('t_alertes',$data);

                // mise à jour de l'état de la tâche
                $data = array(
                    'tac_etat' => 4
                );
                $this->db->where('tac_id', $r->tac_id)
                    ->update('t_taches', $data);
                $resultat[] = $r;
            }
            return $resultat;
        }
        else {
            return false;
        }

    }

    /******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_alertes',$id,'ale_id','ale_archiver');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_alertes',$id,'ale_id','ale_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('ale_inactif' => null, 'ale_archiver' => null);
        return $this->_update('t_alertes',$data, $id,'ale_id');
    }

}
// EOF
