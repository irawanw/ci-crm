<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_adresses extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('adr_adresse','text',"Adresse"),
                array('adr_cp','text',"Code postal"),
                array('adr_ville','text',"Ville"),
                array('adr_contact','text',"Informations de contact"),
                array('adr_info','text',"Informations utiles"),
                array('sec_nom','ref',"Secteur",'secteurs','adr_secteur','sec_nom'),
                array('vtad_type','ref',"Type d'adresse particulière",'v_types_adresses'),
                array('vvi_visibilite','ref',"Visibilité",'v_visibilites'),
                array('RowID','text',"__DT_Row_ID")
            ),            
            'write' => array(
               'adr_adresse' => array("Adresse",'textarea','adr_adresse',true),
               'adr_cp' => array("Code postal",'number','adr_cp',false),
               'adr_ville' => array("Ville",'text','adr_ville',false),
               'adr_contact' => array("Informations de contact",'textarea','adr_contact',false),
               'adr_info' => array("Informations utiles",'textarea','adr_info',false),
               'adr_type' => array("Type d'adresse particulière",'select',array('adr_type','vtad_id','vtad_type'),true),
               'adr_visibilite' => array("Visibilité",'select',array('adr_visibilite','vvi_id','vvi_visibilite'),true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des adresses particulières
    ******************************/
    public function liste($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $adr_adresse = formatte_sql_lien('adresses/detail','adr_id','adr_adresse');
        $sec_nom = formatte_sql_lien('secteurs/detail','sec_id','sec_nom');
        $this->db->select("adr_id AS RowID,adr_id,$adr_adresse,adr_cp,adr_ville,adr_contact,adr_info,$sec_nom,vtad_type,vvi_visibilite",false);
        $this->db->join('t_secteurs','sec_id=adr_secteur','left');
        $this->db->join('v_types_adresses','vtad_id=adr_type','left');
        $this->db->join('v_visibilites','vvi_id=adr_visibilite','left');
        $this->db->where("adr_secteur",$pere);
        $this->db->where('adr_inactif is null');
        //$this->db->order_by("adr_adresse asc");
        $this->db->stop_cache();

        $table = 't_adresses';

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
            'adr_adresse'=>'char',
            'adr_cp'=>'char',
            'adr_ville'=>'char',
            'adr_contact'=>'char',
            'adr_info'=>'char',
            'sec_nom'=>'char',
            'vtad_type'=>'char',
            'vvi_visibilite'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle adresse particulière
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_adresses', $data);
        return $id;
    }

    /******************************
    * Détail d'une adresse
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("adr_id,adr_adresse,adr_cp,adr_ville,adr_secteur,sec_nom,adr_contact,adr_info,adr_type,vtad_type,adr_visibilite,vvi_visibilite",false);
        $this->db->join('t_secteurs','sec_id=adr_secteur','left');
        $this->db->join('v_types_adresses','vtad_id=adr_type','left');
        $this->db->join('v_visibilites','vvi_id=adr_visibilite','left');
        $this->db->where('adr_id',$id);
        $this->db->where('adr_inactif is null');
        $q = $this->db->get('t_adresses');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une adresse
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('adr_id',$id)->get('t_adresses');
        $res =  $this->_update('t_adresses',$data,$id,'adr_id');
        return $res;
    }

/******************************
    * Suppression d'une adresse particulière
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('adr_id',$id)->get('t_adresses');

            $res = $this->_delete('t_adresses',$id,'adr_id','adr_inactif');
        return $res;
    }

}
// EOF
