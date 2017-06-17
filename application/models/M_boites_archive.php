<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_boites_archive extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('bar_code','text',"Code"),
                array('bar_nb_docs','text',"Nombre de documents"),
                array('bar_nb_factures','text',"Nombre de factures"),
                array('bar_commentaire','text',"Commentaire"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'bar_code' => array("Code",'text','bar_code',true),
               'bar_commentaire' => array("Commentaire",'textarea','bar_commentaire',false)
            )
        );

        return array_key_exists($type, $champs) ? $champs[$type] : array();
    }

    /******************************
    * Liste des boîtes archive
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

		$table = 't_boites_archive';
        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $bar_code = formatte_sql_lien('boites_archive/detail','bar_id','bar_code');
        $bar_nb_docs = "(SELECT COUNT(*) FROM t_documents_autres WHERE doa_boite_archive = t_boites_archive.bar_id)";
        $bar_nb_docs2 = $bar_nb_docs ." AS bar_nb_docs";
        $bar_nb_factures = "(SELECT COUNT(*) FROM t_documents_factures WHERE dof_boite_archive = t_boites_archive.bar_id)";
        $bar_nb_factures2 = $bar_nb_factures ." AS bar_nb_factures";
        $this->db->select("bar_id AS RowID,bar_id,$bar_code,$bar_nb_docs2,$bar_nb_factures2,bar_commentaire",false);
        $this->db->where('bar_inactif is null');
        //$this->db->order_by("bar_code asc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('bar_id', $id);
        }
        $this->db->stop_cache();
        // aliases
        $aliases = array(
            'bar_nb_docs'=>$bar_nb_docs,
            'bar_nb_factures'=>$bar_nb_factures
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
            'bar_code'=>'char',
            'bar_nb_docs'=>'char',
            'bar_nb_factures'=>'char',
            'bar_commentaire'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle boîte archive
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_boites_archive', $data);
        return $id;
    }

    /******************************
    * Détail d'une boîte archive
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("bar_id,bar_code,(SELECT COUNT(*) FROM t_documents_autres WHERE doa_boite_archive = t_boites_archive.bar_id) AS bar_nb_docs,(SELECT COUNT(*) FROM t_documents_factures WHERE dof_boite_archive = t_boites_archive.bar_id) AS bar_nb_factures,bar_commentaire",false);
        $this->db->where('bar_id',$id);
        $this->db->where('bar_inactif is null');
        $q = $this->db->get('t_boites_archive');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une boîte archive
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('bar_id',$id)->get('t_boites_archive');
        $res =  $this->_update('t_boites_archive',$data,$id,'bar_id');
        return $res;
    }

/******************************
    * Suppression d'une boîte archive
    ******************************/
    public function remove($id) {
        $q = $this->db->where('bar_id',$id)->get('t_boites_archive');

            $res = $this->_delete('t_boites_archive',$id,'bar_id','bar_inactif');
        return $res;
    }

}
// EOF
