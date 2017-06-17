<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_messages extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Liste des messages non lus
    ******************************/
    public function liste_non_lus($pere) {

        // lecture des informations
        $this->db->select("msg_envoi,msg_lecture,msg_emetteur,utl_login,LEFT(msg_texte,20) AS msg_amorce",false);
        $this->db->join('t_utilisateurs','utl_id=msg_emetteur','left');
        $this->db->where("msg_destinataire",$pere);
        $this->db->where("msg_lecture = '0000-00-00 00:00:00'");
        $this->db->where('msg_inactif is null');
        $this->db->order_by("msg_envoi desc");
            $q = $this->db->get('t_messages');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Liste des messages envoyés
    ******************************/
    public function liste_emis($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $msg_envoi = formatte_sql_lien('messages/detail_emis','msg_id','msg_envoi');
        $msg_lecture = formatte_sql_date_heure('msg_lecture');
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $msg_amorce = "LEFT(msg_texte,20)";
        $msg_amorce2 = $msg_amorce ." AS msg_amorce";
        $this->db->select("msg_id AS RowID,msg_id,$msg_envoi,msg_id,$msg_lecture,$utl_login,$msg_amorce2",false);
        $this->db->join('t_utilisateurs','utl_id=msg_destinataire','left');
        $this->db->where("msg_emetteur",$pere);
        $this->db->where('msg_inactif is null');
        //$this->db->order_by("msg_envoi desc");
        $this->db->stop_cache();

        $table = 't_messages';

        // aliases
        $aliases = array(
            'msg_amorce'=>$msg_amorce
        );

        $resultat = $this->_filtre($table,$this->liste_emis_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_emis_filterable_columns() {
    $filterable_columns = array(
            'msg_id'=>'int',
            'msg_envoi'=>'datetime',
            'msg_lecture'=>'datetime',
            'utl_login'=>'char',
            'msg_amorce'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des messages recus
    ******************************/
    public function liste_recus($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $msg_envoi = formatte_sql_lien('messages/detail_recu','msg_id','msg_envoi');
        $msg_lecture = formatte_sql_date_heure('msg_lecture');
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $msg_amorce = "LEFT(msg_texte,20)";
        $msg_amorce2 = $msg_amorce ." AS msg_amorce";
        $this->db->select("msg_id AS RowID,msg_id,$msg_envoi,msg_id,$msg_lecture,$utl_login,$msg_amorce2",false);
        $this->db->join('t_utilisateurs','utl_id=msg_emetteur','left');
        $this->db->where("msg_destinataire",$pere);
        $this->db->where('msg_inactif is null');
        //$this->db->order_by("msg_envoi desc");
        $this->db->stop_cache();

        $table = 't_messages';

        // aliases
        $aliases = array(
            'msg_amorce'=>$msg_amorce
        );

        $resultat = $this->_filtre($table,$this->liste_recus_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_recus_filterable_columns() {
    $filterable_columns = array(
            'msg_id'=>'int',
            'msg_envoi'=>'datetime',
            'msg_lecture'=>'datetime',
            'utl_login'=>'char',
            'msg_amorce'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau message
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_messages', $data);
        return $id;
    }

    /******************************
    * Nouveau message
    ******************************/
    public function nouveau_direct($data) {
        $id = $this->_insert('t_messages', $data);
        return $id;
    }

    /******************************
    * Contenu d'un message envoyé
    ******************************/
    public function detail_emis($id) {

        // lecture des informations
        $this->db->select("msg_id,msg_envoi,msg_lecture,msg_destinataire,utl_login,msg_texte",false);
        $this->db->join('t_utilisateurs','utl_id=msg_destinataire','left');
        $this->db->where('msg_id',$id);
        $this->db->where('msg_inactif is null');
        $q = $this->db->get('t_messages');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Contenu d'un message reçu
     ******************************/
    public function detail_recu($id) {

        // lecture des informations
        $this->db->select("msg_id,msg_envoi,msg_lecture,msg_emetteur,utl_login,msg_texte",false);
        $this->db->join('t_utilisateurs','utl_id=msg_emetteur','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->where('msg_id',$id);
        $this->db->where('msg_inactif is null');
        $q = $this->db->get('t_messages');
        if ($q->num_rows() > 0) {

            // mise à jour de la date de lecture
            $data = array(
                'msg_lecture'=>date('Y-m-d H:i:s')
            );
            $this->_update('t_messages', $data,$id,'msg_id',array('msg_lecture'=>'0000-00-00 00:00:00'));
            return $q->row();
        }
        else {
            return null;
        }
    }

    /******************************
     * Nombre de messages non lus
     ******************************/
    public function nb_non_lus($id) {

        $this->db->select("count(*)")
            ->where('msg_destinataire',$id)
            ->where('msg_lecture','0000-00-00 00:00:00')
            ->where('msg_inactif is null');
        return $this->db->count_all_results('t_messages');
    }

}
// EOF
