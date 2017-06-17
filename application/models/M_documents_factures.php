<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_documents_factures extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('dof_date','date',"Date"),
                array('dof_nom','text',"Référence"),
                array('dof_fichier','fichier',"Nom du fichier GED"),
                array('dof_montant','number',"Montant"),
                array('ctc_nom','ref',"Fournisseur",'contacts','dof_fournisseur','ctc_nom'),
                array('bar_code','ref',"Boîte archive",'boites_archive','dof_boite_archive','bar_code'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','dof_disque_archivage','dsq_nom'),
                array('dsq_chemin','text',"Chemin d'accès"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'dof_date' => array("Date",'date','dof_date',true),
               'dof_fournisseur' => array("Fournisseur",'select',array('dof_fournisseur','ctc_id','ctc_nom'),false),
               'dof_nom' => array("Référence",'text','dof_nom',true),
               'dof_montant' => array("Montant",'number','dof_montant',true),
               'dof_fichier' => array("Nom du fichier GED",'text','dof_fichier',true),
               'dof_boite_archive' => array("Boîte archive",'select',array('dof_boite_archive','bar_id','bar_code'),true),
               'dof_disque_archivage' => array("Disque d'archivage",'select',array('dof_disque_archivage','dsq_id','dsq_nom'),true)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des factures fournisseurs
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_documents_factures';
        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $dof_nom = formatte_sql_lien('documents_factures/detail','dof_id','dof_nom');
        $dof_date = formatte_sql_date('dof_date');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $bar_code = formatte_sql_lien('boites_archive/detail','bar_id','bar_code');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("dof_id AS RowID,dof_id,$dof_nom,$dof_date,dof_fichier,dof_montant,$ctc_nom,$bar_code,$dsq_nom,dsq_chemin",false);
        $this->db->join('t_contacts','ctc_id=dof_fournisseur','left');
        $this->db->join('t_boites_archive','bar_id=dof_boite_archive','left');
        $this->db->join('t_disques_archivage','dsq_id=dof_disque_archivage','left');
        $this->db->where('dof_inactif is null');
        //$this->db->order_by("dof_id desc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('dof_id', $id);
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
            'dof_date'=>'date',
            'dof_nom'=>'char',
            'dof_fichier'=>'char',
            'dof_montant'=>'decimal',
            'ctc_nom'=>'char',
            'bar_code'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvelle facture
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_documents_factures', $data);
        return $id;
    }

    /******************************
    * Détail d'une facture
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("dof_id,dof_date,dof_fournisseur,ctc_nom,dof_nom,dof_montant,dof_fichier,dof_boite_archive,bar_code,dof_disque_archivage,dsq_nom,dsq_chemin",false);
        $this->db->join('t_contacts','ctc_id=dof_fournisseur','left');
        $this->db->join('t_boites_archive','bar_id=dof_boite_archive','left');
        $this->db->join('t_disques_archivage','dsq_id=dof_disque_archivage','left');
        $this->db->where('dof_id',$id);
        $this->db->where('dof_inactif is null');
        $q = $this->db->get('t_documents_factures');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'une facture
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('dof_id',$id)->get('t_documents_factures');
        $res =  $this->_update('t_documents_factures',$data,$id,'dof_id');
        return $res;
    }

}
// EOF
