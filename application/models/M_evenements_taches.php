<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_evenements_taches extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des évènements
    ******************************/
    public function liste_par_tache($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $evt_date = formatte_sql_date_heure('evt_date');
        $tac_titre = formatte_sql_lien('taches/detail','tac_id','tac_titre');
        $this->db->select("evt_id AS RowID,evt_commentaire,$evt_date,$tac_titre,vet_etat",false);
        $this->db->join('t_taches','tac_id=evt_tache','left');
        $this->db->join('v_etats_taches','vet_id=evt_etat','left');
        $this->db->where("evt_tache",$pere);
        $this->db->where('evt_inactif is null');
        //$this->db->order_by("evt_date asc");
        $this->db->stop_cache();

        $table = 't_evenements_taches';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_tache_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_tache_filterable_columns() {
    $filterable_columns = array(
            'evt_commentaire'=>'char',
            'evt_date'=>'datetime',
            'tac_titre'=>'char',
            'vet_etat'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un évènement
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("evt_id,evt_commentaire,evt_date,evt_tache,tac_titre,evt_etat,vet_etat",false);
        $this->db->join('t_taches','tac_id=evt_tache','left');
        $this->db->join('v_etats_taches','vet_id=evt_etat','left');
        $this->db->where('evt_id',$id);
        $this->db->where('evt_inactif is null');
        $q = $this->db->get('t_evenements_taches');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

}
// EOF
