<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_employes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('vcv_civilite','ref',"Civilite",'v_civilites'),
                array('emp_prenom','text',"Prénom"),
                array('emp_nom','text',"Nom"),
                array('vfo_fonction','ref',"Fonction",'v_fonctions'),
                array('emp_date_entree','date',"Date d'entrée"),
                array('emp_date_sortie','date',"Date de sortie"),
                array('emp_commission','text',"Commissions"),
                array('vee_etat','ref',"Etat",'v_etats_employes'),
                array('utl_login','ref',"Login",'utilisateurs','emp_login','utl_login'),
                array('emp_adresse','text',"Adresse"),
                array('emp_cp','text',"Code postal"),
                array('emp_ville','text',"Ville"),
                array('emp_telephone1','text',"Téléphone 1"),
                array('emp_telephone2','text',"Téléphone 2"),
                array('emp_email','text',"Email"),
                array('emp_h_jour','number',"Nb. heures / jour"),
                array('emp_h_semaine','number',"Nb. heures / semaine"),
                array('emp_h_mois','number',"Nb. heures / mois"),
                array('emp_cout_heure','number',"Coût horaire"),
                array('emp_cv_vehicule','number',"Nb. CV véhicule"),
                array('emp_immatriculation','text',"Immatriculation"),
                array('emp_ptc','number',"Poids total en charge"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'emp_civilite' => array("Civilite",'radio-h',array('emp_civilite','vcv_id','vcv_civilite'),false),
               'emp_nom' => array("Nom",'text','emp_nom',true),
               'emp_prenom' => array("Prénom",'text','emp_prenom',true),
               'emp_fonction' => array("Fonction",'select',array('emp_fonction','vfo_id','vfo_fonction'),true),
               'emp_commission' => array("Commissions",'checkbox','emp_commission',false),
               'emp_date_entree' => array("Date d'entrée",'date','emp_date_entree',true),
               'emp_date_sortie' => array("Date de sortie",'date','emp_date_sortie',false),
               'emp_etat' => array("Etat",'radio-h',array('emp_etat','vee_id','vee_etat'),false),
               'emp_notes' => array("Remarques",'textarea','emp_notes',false),
               'emp_adresse' => array("Adresse",'textarea','emp_adresse',false),
               'emp_cp' => array("Code postal",'number','emp_cp',false),
               'emp_ville' => array("Ville",'text','emp_ville',false),
               'emp_telephone1' => array("Téléphone 1",'number','emp_telephone1',false),
               'emp_telephone2' => array("Téléphone 2",'number','emp_telephone2',false),
               'emp_email' => array("Email",'email','emp_email',false),
               'emp_h_jour' => array("Nb. heures / jour",'number','emp_h_jour',false),
               'emp_h_semaine' => array("Nb. heures / semaine",'number','emp_h_semaine',false),
               'emp_h_mois' => array("Nb. heures / mois",'number','emp_h_mois',false),
               'emp_cout_heure' => array("Coût horaire",'number','emp_cout_heure',false),
               'emp_cv_vehicule' => array("Nb. CV véhicule",'number','emp_cv_vehicule',false),
               'emp_immatriculation' => array("Immatriculation",'number','emp_immatriculation',false),
               'emp_ptc' => array("Poids total en charge",'number','emp_ptc',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des employés
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

		$table = 't_employes';
        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $emp_date_entree = formatte_sql_date('emp_date_entree');
        $emp_date_sortie = formatte_sql_date('emp_date_sortie');
        $utl_login = formatte_sql_lien('utilisateurs/detail','utl_id','utl_login');
        $this->db->select("emp_id AS RowID,emp_id,$emp_nom,vcv_civilite,emp_prenom,vfo_fonction,$emp_date_entree,$emp_date_sortie,emp_commission,vee_etat,$utl_login,emp_adresse,emp_cp,emp_ville,emp_telephone1,emp_telephone2,emp_email,emp_h_jour,emp_h_semaine,emp_h_mois,emp_cout_heure,emp_cv_vehicule,emp_immatriculation,emp_ptc",false);
        $this->db->join('v_civilites','vcv_id=emp_civilite','left');
        $this->db->join('v_fonctions','vfo_id=emp_fonction','left');
        $this->db->join('v_etats_employes','vee_id=emp_etat','left');
        $this->db->join('t_utilisateurs','utl_employe=emp_id','left outer');
        $this->db->where('emp_inactif is null');
        //$this->db->order_by("emp_nom asc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('emp_id', $id);
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
            'vcv_civilite'=>'char',
            'emp_prenom'=>'char',
            'emp_nom'=>'char',
            'vfo_fonction'=>'char',
            'emp_date_entree'=>'date',
            'emp_date_sortie'=>'date',
            'emp_commission'=>'char',
            'vee_etat'=>'char',
            'emp_login'=>'char',
            'emp_adresse'=>'char',
            'emp_cp'=>'char',
            'emp_ville'=>'char',
            'emp_telephone1'=>'char',
            'emp_telephone2'=>'char',
            'emp_email'=>'char',
            'emp_h_jour'=>'int',
            'emp_h_semaine'=>'int',
            'emp_h_mois'=>'int',
            'emp_cout_heure'=>'decimal',
            'emp_cv_vehicule'=>'int',
            'emp_immatriculation'=>'char',
            'emp_ptc'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste json
    ******************************/
    public function web_service() {

        // lecture des informations
        $this->db->select("emp_id,emp_prenom,emp_nom",false);
        $this->db->where('emp_inactif is null');
        $this->db->order_by("emp_nom asc");
        $q = $this->db->get('t_employes');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
    * Nouvel employé
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_employes', $data);
        return $id;
    }

    /******************************
    * Détail d'un employé
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("emp_id,emp_civilite,vcv_civilite,emp_nom,emp_prenom,CONCAT(emp_prenom,' ',emp_nom) AS emp_nom_complet,emp_fonction,vfo_fonction,utl_id AS emp_login,utl_login,emp_commission,emp_date_entree,emp_date_sortie,emp_etat,vee_etat,emp_notes,emp_adresse,emp_cp,emp_ville,emp_telephone1,emp_telephone2,emp_email,emp_h_jour,emp_h_semaine,emp_h_mois,emp_cout_heure,emp_cv_vehicule,emp_immatriculation,emp_ptc",false);
        $this->db->join('v_civilites','vcv_id=emp_civilite','left');
        $this->db->join('v_fonctions','vfo_id=emp_fonction','left');
        $this->db->join('t_utilisateurs','utl_employe=emp_id','left outer');
        $this->db->join('v_etats_employes','vee_id=emp_etat','left');
        $this->db->where('emp_id',$id);
        $this->db->where('emp_inactif is null');
        $q = $this->db->get('t_employes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un employé
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('emp_id',$id)->get('t_employes');
        $res =  $this->_update('t_employes',$data,$id,'emp_id');
        return $res;
    }

/******************************
    * Suppression d'un employé
    ******************************/
    public function remove($id) {
        $q = $this->db->where('emp_id',$id)->get('t_employes');

            $res = $this->_delete('t_employes',$id,'emp_id','emp_inactif');
        return $res;
    }
    
    /******************************
     * for statistiques prospection module
     ******************************/
    public function liste_option_by_filter()
    {
    	$this->db->select('emp_id as id, emp_nom as value');
    	$this->db->where('emp_fonction',1); //1 = commercial, 2 = autre
    	$q = $this->db->get('t_employes');
    	return $q->result();
    }
}
// EOF
