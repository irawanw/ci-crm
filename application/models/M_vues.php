<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_vues extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('vue_nom','text',"Nom"),
                array('vue_controleur','text',"Liste"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'vue_nom' => array("Nom",'text','vue_nom',true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des vues de l'utilisateur
    ******************************/
    public function liste($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $vue_nom = formatte_sql_lien('vues/detail','vue_id','vue_nom');
        $this->db->select("vue_id AS RowID,vue_id,$vue_nom,vue_controleur",false);
        $this->db->where("vue_proprietaire",$pere);
        $this->db->where('vue_inactif is null');
        //$this->db->order_by("vue_nom");
        $this->db->stop_cache();

        $table = 't_vues';

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
            'vue_nom'=>'char',
            'vue_controleur'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'une vue
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("vue_id,vue_nom,vue_controleur",false);
        $this->db->where('vue_id',$id);
        $this->db->where('vue_inactif is null');
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une vue
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('vue_id',$id)->get('t_vues');
        $res =  $this->_update('t_vues',$data,$id,'vue_id');
        return $res;
    }

/******************************
    * Suppression d'une vue
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('vue_id',$id)->get('t_vues');

            $res = $this->_delete('t_vues',$id,'vue_id','vue_inactif');
        return $res;
    }

    /******************************
     * Liste des vues de l'utilisateur pour un contrôleur
     ******************************/
    public function vues_ctrl($ctrl,$id) {
        // lecture des informations
        $this->db->select("vue_id, vue_nom, IF(vue_proprietaire=".$id.",vue_default,0) AS vue_default, vue_default_by_admin ",false);
        //$this->db->where("vue_proprietaire",$id);
        $this->db->where("vue_controleur",$ctrl);
        $this->db->where('vue_inactif is null');
        $this->db->group_start();
        $this->db->or_where("vue_default_by_admin", 1);
        $this->db->or_where("vue_proprietaire",$id);
        $this->db->group_end();
        $this->db->order_by("vue_nom");
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
     * Enregistrement d'une vue
     ******************************/
    public function nouvelle($id,$nom,$ctrl,$reglages) {
        $data = array(
            'vue_proprietaire' => $id,
            'vue_nom' => $nom,
            'vue_controleur' => $ctrl,
            'vue_data' => $reglages
        );
        return $this->_insert('t_vues',$data);
    }

    /******************************
     * Lecture des réglages d'une vue
     ******************************/
    public function reglages($id,$vue) {

        // lecture des informations
        $this->db->select("vue_data",false);
        //$this->db->where("vue_proprietaire",$id);
        $this->db->group_start();
        $this->db->or_where("vue_default_by_admin", 1);
        $this->db->or_where("vue_proprietaire",$id);
        $this->db->group_end();
        $this->db->where("vue_id",$vue);
        $this->db->where('vue_inactif is null');
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = $q->row()->vue_data;
            return $result;
        }
        else {
            return false;
        }
    }
    
    /******************************
     * Lecture des réglages d'une vue
     ******************************/
    public function is_owned($id,$vue) {

        // lecture des informations
        $this->db->select("vue_proprietaire=".$id." AS is_owned",false);
        //$this->db->where("vue_proprietaire",$id);
        $this->db->where("vue_id",$vue);
        $this->db->where('vue_inactif is null');
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = ($q->row()->is_owned==1)?true:false;
            return $result;
        }
        else {
            return false;
        }
    }
    
    /******************************
     * Sets a vue (by vue_id) as default; prior to that removed default flag from all user's (vue_proprietaire) views
     * Need to add db column:
        ALTER TABLE  `t_vues` ADD  `vue_default` BOOLEAN NULL DEFAULT NULL AFTER  `vue_proprietaire` ;
     * Param id: user id (whom owns this view)
     * Param vue: vue_id
     * Param ctrl: controller name
     * Returns: true on success, false on db error
     *          -1 if user does not own view
     *          -2 if current user not administrator
     ******************************/
     public function set_default($id, $vue, $ctrl) {
        $data = array('vue_default'=> 0);
        $res =  $this->_update('t_vues',$data,$id,'vue_proprietaire', array('vue_default'=>1, 'vue_controleur'=>$ctrl));
        if ( $res ) {
            $data = array('vue_default'=> 1);
            $res =  $this->_update('t_vues',$data,$vue,'vue_id');
        } 
        return $res;
     }

    /******************************
     * Sets a vue (by vue_id) as default; prior to that removed default flag from all user's (vue_proprietaire) views
     * Need to add db column:
        ALTER TABLE  `t_vues` ADD  `vue_default` BOOLEAN NULL DEFAULT NULL AFTER  `vue_proprietaire` ;
     * Param id: user id (whom owns this view)
     * Param vue: vue_id
     * Param ctrl: controller name
     * Returns: true on success, false on db error
     *          -1 if user does not own view
     *          -2 if current user not administrator
     ******************************/
     public function set_global_default($id, $vue, $ctrl) {
        if ($this->session->userdata('utl_profil')!=1) { // 1=administrator
            return -2; 
        } else {
            $this->db->select("vue_id",false);
            $this->db->where("vue_id",$vue);
            $this->db->where("vue_proprietaire",$id);
            $this->db->where('vue_inactif is null');
            $q = $this->db->get('t_vues');
            if ($q->num_rows() > 0) {
                $this->db->flush_cache();
                $data = array('vue_default_by_admin'=> 0);
                $res =  $this->_update('t_vues',$data,$id,'vue_proprietaire', array('vue_default_by_admin'=>1, 'vue_controleur'=>$ctrl));
                $data = array('vue_default_by_admin'=> 1);
                $res =  $this->_update('t_vues',$data,$vue,'vue_id');
            } else {
                return -1; // if user does not own view
            }
        }
        return $res;
     }

    /******************************
     * Get the default view style for user (by vue_proprietaire) and a controller (by vue_controleur).
     * Need to add db column:
        ALTER TABLE  `t_vues` ADD  `vue_default` BOOLEAN NULL DEFAULT NULL AFTER  `vue_proprietaire` ;
        ALTER TABLE  `t_vues` ADD  `vue_default_by_admin` TINYINT NULL DEFAULT NULL AFTER  `vue_default` ;
     * Param id: user id (whom owns this view)
     * Param ctrl: controller name
     ******************************/
     public function get_default($id, $ctrl) {
        $default_view = $this->get_default_id($id, $ctrl);
        if ( $default_view==false ) return false;
        $this->db->select("vue_data",false);
        $this->db->where("vue_controleur", $ctrl);
        $this->db->where('vue_inactif is null');
        $this->db->where('vue_id', $default_view);
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = $q->row()->vue_data;
            return $result;
        }
        else {
            return false;
        }
     }
     
    /******************************
     * Get the default view id for user (by vue_proprietaire) and a controller (by vue_controleur).
     * Need to add db column:
        ALTER TABLE  `t_vues` ADD  `vue_default` BOOLEAN NULL DEFAULT NULL AFTER  `vue_proprietaire` ;
        ALTER TABLE  `t_vues` ADD  `vue_default_by_admin` TINYINT NULL DEFAULT NULL AFTER  `vue_default` ;
     * Param id: user id (whom owns this view)
     * Param ctrl: controller name
     ******************************/
     public function get_default_id($id, $ctrl) {
        $this->db->select("vue_id",false);
        $this->db->where("vue_controleur", $ctrl);
        $this->db->where('vue_inactif is null');
        $this->db->where("vue_proprietaire",$id);
        $this->db->where("vue_default", 1);
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = $q->row()->vue_id;
            return $result;
        }
        else {
            $this->db->flush_cache();
            $this->db->select("vue_id",false);
            $this->db->where("vue_controleur", $ctrl);
            $this->db->where('vue_inactif is null');
            $this->db->where("vue_default_by_admin", 1);
            $q = $this->db->get('t_vues');
            if ($q->num_rows() > 0) {
                $result = $q->row()->vue_id;
                return $result;
            }
        }
            return false;
     }     
    
    /******************************
     * Get the default view name for user (by vue_proprietaire) and a controller (by vue_controleur).
     * Param id: user id (whom owns this view)
     * Param ctrl: controller name
     ******************************/
     public function get_name($vue_id) {
        $this->db->select("vue_nom",false);
        $this->db->where("vue_id",$vue_id);
        $this->db->where('vue_inactif is null');
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $result = $q->row()->vue_nom;
            return $result;
        }
        else {
            return false;
        }
     }     
    
    /******************************
     * Get the default view name for user (by vue_proprietaire) and a controller (by vue_controleur).
     * Param id: user id (whom owns this view)
     * Param vue: vue_id
     * Returns: true on success, false on db error
     *          -1 if user does not own view
     *          -2 if view is selected as Default by admin
     ******************************/
     public function delete($id, $vue) {
        $this->db->select("vue_id",false);
        $this->db->where("vue_id",$vue);
        $this->db->where("vue_proprietaire",$id);
        $this->db->where('vue_inactif is null');
        $q = $this->db->get('t_vues');
        if ($q->num_rows() > 0) {
            $this->db->flush_cache();
            $this->db->select("vue_id",false);
            $this->db->where("vue_id",$vue);
            $this->db->group_start();
            $this->db->or_where("vue_default_by_admin is null");
            $this->db->or_where("vue_default_by_admin=0");
            $this->db->group_end();
            $this->db->where('vue_inactif is null');
            $q = $this->db->get('t_vues');
            if ($q->num_rows() > 0) { 
                $this->db->flush_cache();
                $this->db->set('vue_inactif', 'NOW()', FALSE);                
                $this->db->where('vue_id', $vue);
                return $this->db->update('t_vues');
            }
            else {
                return -2; // if view is selected as Default by admin
            }
        }
        else {
            return -1; // if user does not own view
        }
     }     
    
}
// EOF
