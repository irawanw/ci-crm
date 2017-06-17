<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_actions extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('act_id','id',"Identifiant"),
                array('act_date','datetime',"Date"),
                array('act_table','text',"Table"),
                array('act_obj_id','number',"Identifiant"),
                array('act_champ_id','text',"Champ ID"),
                array('utl_login','ref',"Utilisateur",'utilisateurs','act_user','utl_login'),
                array('vat_action','ref',"Action effectuée",'v_actions'),
                array('act_restauration','number',"Restauration"),
                array('RowID','text',"__DT_Row_ID")
            ),            
            'write' => array()
        );

        return $champs[$type];
    }

    /******************************
    * Liste des actions
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $act_date = formatte_sql_date_heure_lien('actions/detail','act_id','act_date');
        $act_date = 'act_date';
        $act_id = formatte_sql_lien('actions/detail','act_id','act_id');
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $this->db->select("act_id AS RowID,act_id,$act_date,$act_id,act_table,act_obj_id,act_champ_id,$utl_login,vat_action,act_restauration",false);
        $this->db->join('t_utilisateurs','utl_id=act_user','left');
        $this->db->join('v_actions','vat_id=act_action','left');
        //$this->db->order_by("act_date desc");
        $this->db->stop_cache();

        $table = 't_actions';

        // aliases
        $aliases = array(

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
            'act_id'=>'int',
            'act_date'=>'datetime',
            'act_table'=>'char',
            'act_obj_id'=>'int',
            'act_champ_id'=>'char',
            'utl_login'=>'char',
            'vat_action'=>'char',
            'act_restauration'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des actions de l'utilisateur
    ******************************/
    public function liste_par_utilisateur($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $act_date = formatte_sql_lien('actions/detail','act_id','act_date');
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $this->db->select("act_id AS RowID,act_id,$act_date,act_id,act_table,act_obj_id,act_champ_id,$utl_login,vat_action,act_restauration",false);
        $this->db->join('t_utilisateurs','utl_id=act_user','left');
        $this->db->join('v_actions','vat_id=act_action','left');
        $this->db->where("act_user",$pere);
        //$this->db->order_by("act_date desc");
        $this->db->stop_cache();

        $table = 't_actions';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_utilisateur_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_utilisateur_filterable_columns() {
    $filterable_columns = array(
            'act_id'=>'int',
            'act_date'=>'datetime',
            'act_table'=>'char',
            'act_obj_id'=>'int',
            'act_champ_id'=>'char',
            'utl_login'=>'char',
            'vat_action'=>'char',
            'act_restauration'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'une action
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("act_id,act_user,utl_login,act_date,act_table,act_obj_id,act_action,vat_action,act_info",false);
        $this->db->join('t_utilisateurs','utl_id=act_user','left');
        $this->db->join('v_actions','vat_id=act_action','left');
        $this->db->where('act_id',$id);
        $q = $this->db->get('t_actions');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Historique d'un enregistrement
     ******************************/
    public function historique($id) {

        // récupération des identifiants
        $q = $this->db->select("")
            ->where('act_id',$id)
            ->get('t_actions');
        if ($q->num_rows() > 0) {
            $enregistrement = $q->row();

            // lecture des informations
            $this->db->select("act_id,act_user,utl_login,act_date,act_table,act_obj_id,act_action,vat_action,act_restauration",false);
            $this->db->join('t_utilisateurs','utl_id=act_user','left');
            $this->db->join('v_actions','vat_id=act_action','left');
            $this->db->where('act_table',$enregistrement->act_table);
            $this->db->where('act_obj_id',$enregistrement->act_obj_id);
            $this->db->order_by("act_date desc");
            $q = $this->db->get('t_actions');
            if ($q->num_rows() > 0) {
                $result = $q->result();
                return $result;
            }
        }
        return array();
    }

    /******************************
     * Restauration d'un enregistrement
     ******************************/
    public function restaurer($id) {
        return $this->_restaure($id);
    }

}
// EOF
