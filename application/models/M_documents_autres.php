<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_documents_autres extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('doa_date','date',"Date"),
                array('vtd_type','ref',"Type de document",'v_types_documents'),
                array('doa_nom','text',"Référence"),
                array('doa_fichier','fichier',"Nom du fichier GED"),
                array('bar_code','ref',"Boîte archive",'boites_archive','doa_boite_archive','bar_code'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','doa_disque_archivage','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'doa_date' => array("Date",'date','doa_date',true),
               'doa_type' => array("Type de document",'select',array('doa_type','vtd_id','vtd_type'),true),
               'doa_nom' => array("Référence",'text','doa_nom',true),
               'doa_fichier' => array("Nom du fichier GED",'text','doa_fichier',true),
               'doa_boite_archive' => array("Boîte archive",'select',array('doa_boite_archive','bar_id','bar_code'),true),
               'doa_disque_archivage' => array("Disque d'archivage",'select',array('doa_disque_archivage','dsq_id','dsq_nom'),true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des documents
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_documents_autres';
        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations
        $doa_nom = formatte_sql_lien('documents_autres/detail','doa_id','doa_nom');
        $doa_date = formatte_sql_date('doa_date');
        $bar_code = formatte_sql_lien('boites_archive/detail','bar_id','bar_code');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("doa_id AS RowID,doa_id,$doa_nom,$doa_date,vtd_type,doa_fichier,$bar_code,$dsq_nom,dsq_chemin",false);
        $this->db->join('v_types_documents','vtd_id=doa_type','left');
        $this->db->join('t_boites_archive','bar_id=doa_boite_archive','left');
        $this->db->join('t_disques_archivage','dsq_id=doa_disque_archivage','left');
        $this->db->where('doa_inactif is null');
		$this->db->where('doa_deleted is null');
        //$this->db->order_by("doa_id desc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('doa_id', $id);
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
            'doa_date'=>'date',
            'vtd_type'=>'char',
            'doa_nom'=>'char',
            'doa_fichier'=>'char',
            'bar_code'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau document
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_documents_autres', $data);
        return $id;
    }

    /******************************
    * Détail d'un document
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("doa_id,doa_date,doa_type,vtd_type,doa_nom,doa_fichier,doa_boite_archive,bar_code,doa_disque_archivage,dsq_nom,dsq_chemin",false);
        $this->db->join('v_types_documents','vtd_id=doa_type','left');
        $this->db->join('t_boites_archive','bar_id=doa_boite_archive','left');
        $this->db->join('t_disques_archivage','dsq_id=doa_disque_archivage','left');
        $this->db->where('doa_id',$id);
        $this->db->where('doa_inactif is null');
        $q = $this->db->get('t_documents_autres');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un document
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('doa_id',$id)->get('t_documents_autres');
        $res =  $this->_update('t_documents_autres',$data,$id,'doa_id');
        return $res;
    }
	
		/******************************
    * Archive test mails data
    ******************************/
    public function archive($id) {
        return $this->_delete('t_documents_autres',$id,'doa_id','doa_inactif');
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_documents_autres',$id,'doa_id','doa_deleted');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_documents_autres',$data, $id,'doa_id');
    }

}
// EOF
