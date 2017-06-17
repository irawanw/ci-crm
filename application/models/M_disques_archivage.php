<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_disques_archivage extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('dsq_nom','text',"Nom"),
                array('dsq_commentaire','text',"Commentaire"),
                array('dsq_chemin','text',"Chemin d'accès"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'dsq_nom' => array("Nom",'text','dsq_nom',true),
               'dsq_commentaire' => array("Commentaire",'textarea','dsq_commentaire',false),
               'dsq_chemin' => array("Chemin d'accès",'text','dsq_chemin',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des disques d'archivage
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_disques_archivage';
        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("dsq_id AS RowID,dsq_id,$dsq_nom,dsq_commentaire,dsq_chemin",false);
        $this->db->where('dsq_inactif is null');
        //$this->db->order_by("dsq_nom asc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('dsq_id', $id);
        }
        $this->db->stop_cache();

        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'dsq_nom'=>'char',
            'dsq_commentaire'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau disque d'archivage
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_disques_archivage', $data);
        return $id;
    }

    /******************************
    * Détail d'un disque d'archivage
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("dsq_id,dsq_nom,dsq_commentaire,dsq_chemin",false);
        $this->db->where('dsq_id',$id);
        $this->db->where('dsq_inactif is null');
        $q = $this->db->get('t_disques_archivage');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un disque d'archivage
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('dsq_id',$id)->get('t_disques_archivage');
        $res =  $this->_update('t_disques_archivage',$data,$id,'dsq_id');
        return $res;
    }

/******************************
    * Suppression d'un disque d'archivage
    ******************************/
    public function remove($id) {
        $q = $this->db->where('dsq_id',$id)->get('t_disques_archivage');

            $res = $this->_delete('t_disques_archivage',$id,'dsq_id','dsq_inactif');
        return $res;
    }

    /******************************
     * Id du disque de stockage des modèles de documents
     ******************************/
    public function id_modeles() {
        return 1;
    }

    /******************************
     * Répertoire de stockage des modèles de documents
     ******************************/
    public function rep_modeles() {
        $q = $this->db->where('dsq_id',$this->id_modeles())
           ->get('t_disques_archivage');
        if ($q->num_rows() > 0) {
            return $q->row()->dsq_chemin.'/';
        }
        return '';
    }

    /******************************
     * Id du disque de stockage des documents générés
     ******************************/
    public function id_generes() {
        return 2;
    }

    /******************************
     * Répertoire de stockage des documents générés
     ******************************/
    public function rep_generes() {
        $q = $this->db->where('dsq_id',$this->id_generes())
            ->get('t_disques_archivage');
        if ($q->num_rows() > 0) {
            return $q->row()->dsq_chemin.'/';
        }
        return '';
    }

}
// EOF
