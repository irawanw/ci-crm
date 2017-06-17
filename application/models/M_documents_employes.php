<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_documents_employes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('doe_id','id',"Identifiant"),
                array('doe_date','datetime',"Date"),
                array('mod_nom','ref',"Modèle",'modeles_documents','doe_modele','mod_nom'),
                array('doe_fichier','fichier',"Nom du fichier GED"),
                array('emp_nom','ref',"Employé",'employes','doe_employe','emp_nom'),
                array('dsq_nom','ref',"Disque d'archivage",'disques_archivage','doe_disque_archivage','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'doe_modele' => array("Modèle",'select',array('doe_modele','mod_id','mod_nom'),true)
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
        $doe_date = formatte_sql_lien('documents_employes/detail','doe_id','doe_date');
        $mod_nom = formatte_sql_lien('modeles_documents/detail','mod_id','mod_nom');
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("doe_id AS RowID,doe_id,$doe_date,doe_id,$mod_nom,doe_fichier,$emp_nom,$dsq_nom,dsq_chemin",false);
        $this->db->join('t_modeles_documents','mod_id=doe_modele','left');
        $this->db->join('t_employes','emp_id=doe_employe','left');
        $this->db->join('t_disques_archivage','dsq_id=doe_disque_archivage','left');
        $this->db->where('doe_inactif is null');
        //$this->db->order_by("doe_id desc");
        $this->db->stop_cache();

        $table = 't_documents_employes';

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
            'doe_id'=>'int',
            'doe_date'=>'datetime',
            'mod_nom'=>'char',
            'doe_fichier'=>'char',
            'emp_nom'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des documents générés
    ******************************/
    public function liste_par_employe($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $doe_date = formatte_sql_lien('documents_employes/detail','doe_id','doe_date');
        $mod_nom = formatte_sql_lien('modeles_documents/detail','mod_id','mod_nom');
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("doe_id AS RowID,doe_id,$doe_date,doe_id,$mod_nom,doe_fichier,$emp_nom,$dsq_nom,dsq_chemin",false);
        $this->db->join('t_modeles_documents','mod_id=doe_modele','left');
        $this->db->join('t_employes','emp_id=doe_employe','left');
        $this->db->join('t_disques_archivage','dsq_id=doe_disque_archivage','left');
        $this->db->where("doe_employe",$pere);
        $this->db->where('doe_inactif is null');
        //$this->db->order_by("doe_id desc");
        $this->db->stop_cache();

        $table = 't_documents_employes';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_employe_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_employe_filterable_columns() {
    $filterable_columns = array(
            'doe_id'=>'int',
            'doe_date'=>'datetime',
            'mod_nom'=>'char',
            'doe_fichier'=>'char',
            'emp_nom'=>'char',
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
        $this->db->select("doe_id,doe_date,doe_employe,emp_nom,doe_modele,mod_nom,doe_fichier,doe_disque_archivage,dsq_nom,dsq_chemin",false);
        $this->db->join('t_employes','emp_id=doe_employe','left');
        $this->db->join('t_modeles_documents','mod_id=doe_modele','left');
        $this->db->join('t_disques_archivage','dsq_id=doe_disque_archivage','left');
        $this->db->where('doe_id',$id);
        $this->db->where('doe_inactif is null');
        $q = $this->db->get('t_documents_employes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Génération de document employé
     ******************************/
    public function nouveau($data) {

        // récupération du modèle de document
        $q = $this->db->where('mod_id',$data['doe_modele'])
            ->get('t_modeles_documents');
        if ($q->num_rows() > 0) {
            $modele = $q->row();

            // récupération des informations de l'employé
            $this->load->model('m_employes');
            $employe = $this->m_employes->detail($data['doe_employe']);
            if (isset($employe)) {
                $data['doe_fichier'] = $modele->mod_nom;
                $this->load->model('m_disques_archivage');
                $data['doe_disque_archivage'] = $this->m_disques_archivage->id_generes();
                $id = $this->_insert('t_documents_employes', $data);
                if ($id !== false) {
                    $this->load->model('m_modeles_documents');
                    switch($modele->mod_type) {
                        case 1: // modèle Word
                            $nom = $modele->mod_nom.'-'.$id.'.docx';
                            $res = $this->m_modeles_documents->fusion_word($modele->mod_fichier,$nom,$employe);
                            break;
                        case 2: // modèle texte
                            $nom = $modele->mod_nom.'-'.$id.'.txt';
                            $res = $this->m_modeles_documents->fusion_texte($modele->mod_texte,$nom,$employe);
                            break;
                    }
                    if ($res !== false) {
                        return $this->_update('t_documents_employes',array('doe_fichier'=>$nom),$id,'doe_id');
                    }
                }
            }
        }
        return false;
    }

}
// EOF
