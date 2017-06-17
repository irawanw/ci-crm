<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_documents_contacts extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('doc_id','id',"Identifiant"),
                array('doc_date','datetime',"Date"),
                array('mod_nom','ref',"Modèle",'modeles_documents','doc_modele','mod_nom'),
                array('doc_fichier','fichier',"Nom du fichier GED"),
                array('ctc_nom','ref',"Contact",'contacts','doc_contact','ctc_nom'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','doc_disque_archivage','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'doc_modele' => array("Modèle",'select',array('doc_modele','mod_id','mod_nom'),true)
            )
        );

        return $champs[$type];
    }
    

    /******************************
    * Liste des documents générés
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $doc_date = formatte_sql_lien('documents_contacts/detail','doc_id','doc_date');
        $mod_nom = formatte_sql_lien('modeles_documents/detail','mod_id','mod_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("doc_id AS RowID,doc_id,$doc_date,doc_id,$mod_nom,doc_fichier,$ctc_nom,$dsq_nom,dsq_chemin",false);
        $this->db->join('t_modeles_documents','mod_id=doc_modele','left');
        $this->db->join('t_contacts','ctc_id=doc_contact','left');
        $this->db->join('t_disques_archivage','dsq_id=doc_disque_archivage','left');
        $this->db->where('doc_inactif is null');
        //$this->db->order_by("doc_id desc");
        $this->db->stop_cache();

        $table = 't_documents_contacts';

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
            'doc_id'=>'int',
            'doc_date'=>'datetime',
            'mod_nom'=>'char',
            'doc_fichier'=>'char',
            'ctc_nom'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des documents générés
    ******************************/
    public function liste_par_contact($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $doc_date = formatte_sql_lien('documents_contacts/detail','doc_id','doc_date');
        $mod_nom = formatte_sql_lien('modeles_documents/detail','mod_id','mod_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("doc_id AS RowID,doc_id,$doc_date,doc_id,$mod_nom,doc_fichier,$ctc_nom,$dsq_nom,dsq_chemin",false);
        $this->db->join('t_modeles_documents','mod_id=doc_modele','left');
        $this->db->join('t_contacts','ctc_id=doc_contact','left');
        $this->db->join('t_disques_archivage','dsq_id=doc_disque_archivage','left');
        $this->db->where("doc_contact",$pere);
        $this->db->where('doc_inactif is null');
        //$this->db->order_by("doc_id desc");
        $this->db->stop_cache();

        $table = 't_documents_contacts';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_contact_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_contact_filterable_columns() {
    $filterable_columns = array(
            'doc_id'=>'int',
            'doc_date'=>'datetime',
            'mod_nom'=>'char',
            'doc_fichier'=>'char',
            'ctc_nom'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un document généré
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("doc_id,doc_date,doc_contact,ctc_nom,doc_modele,mod_nom,doc_fichier,doc_disque_archivage,dsq_nom,dsq_chemin",false);
        $this->db->join('t_contacts','ctc_id=doc_contact','left');
        $this->db->join('t_modeles_documents','mod_id=doc_modele','left');
        $this->db->join('t_disques_archivage','dsq_id=doc_disque_archivage','left');
        $this->db->where('doc_id',$id);
        $this->db->where('doc_inactif is null');
        $q = $this->db->get('t_documents_contacts');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Génération de document contact
     ******************************/
    public function nouveau($data) {

        // récupération du modèle de document
        $q = $this->db->where('mod_id',$data['doc_modele'])
            ->get('t_modeles_documents');
        if ($q->num_rows() > 0) {
            $modele = $q->row();

            // récupération des informations du contact
            $this->load->model('m_contacts');
            $contact = $this->m_contacts->detail($data['doc_contact']);
            if (isset($contact)) {
                $data['doc_fichier'] = $modele->mod_nom;
                $this->load->model('m_disques_archivage');
                $data['doc_disque_archivage'] = $this->m_disques_archivage->id_generes();
                $id = $this->_insert('t_documents_contacts', $data);
                if ($id !== false) {
                    $this->load->model('m_modeles_documents');
                    switch($modele->mod_type) {
                        case 1: // modèle Word
                            $nom = $modele->mod_nom.'-'.$id.'.docx';
                            $res = $this->m_modeles_documents->fusion_word($modele->mod_fichier,$nom,$contact);
                            break;
                        case 2: // modèle texte
                            $nom = $modele->mod_nom.'-'.$id.'.txt';
                            $res = $this->m_modeles_documents->fusion_texte($modele->mod_texte,$nom,$contact);
                            break;
                    }
                    if ($res !== false) {
                        return $this->_update('t_documents_contacts',array('doc_fichier'=>$nom),$id,'doc_id');
                    }
                }
            }
        }
        return false;
    }

}
// EOF
