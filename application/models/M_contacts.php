<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_contacts extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->filterable_columns = array(
            //'ctc_id_comptable'=>'int',
            'ctc_date_creation'=>'datetime',
            'ctc_adresse'=>'char',
            'ctc_nom'=>'char', 
            'ctc_telephone'=>'char',
            'ctc_fax'=>'char',
            'ctc_mobile'=>'char',
            'ctc_email'=>'char',
            'ctc_cp'=>'int', 
            'ctc_ville'=>'char',
            'vcp_type'=>'char',
        	'ctc_marche' => 'char',
        	'ctc_date_marche' => 'datetime',
        	'ctc_alerte' => 'char',
        	'ctc_date_alerte'=>'char',
        	'ctc_remarques_sur_marche' => 'char',
            'ctc_client_prospect'=>'char',
            'ctc_fournisseur'=>'char',
            'ctc_activite'=>'char',
            'vac_activite'=>'char',
            'origine_name'=>'char',
            'ctc_origine_generale' => 'char',
            'ctc_url'=>'char',
            'ctc_commercial'=>'char',
            'emp_nom'=>'char',
            'ctc_site'=>'char',
            'ctc_qual_comm'=>'char',
            'ctc_prospection'=>'char',
            'ctc_prix_rural'=>'decimal',
            'ctc_prix_urbain'=>'decimal',
            'ctc_prix_urgent'=>'decimal',
            'ctc_prix_cible'=>'decimal',
            'ctc_login'=>'char',
            'ctc_type_distribution'=>'char',
            'vtdi_type'=>'char',
            'ctc_dist_hlm'=>'int',
            'ctc_dist_res'=>'int',
            'ctc_dist_pav'=>'int',
            'ctc_stock'=>'int',
            'ctc_cycle_complet'=>'int',
            'ctc_del_avant_distrib'=>'int',
            'ctc_poids'=>'int',
            'ctc_sonner'=>'int',
            'ctc_livr_adresse'=>'char',
            'ctc_livr_cp'=>'char',
            'ctc_livr_ville'=>'char',
            'ctc_livr_horaire'=>'char',
            'ctc_livr_info'=>'char',
            'utl_login' => 'char',
            'scv_nom' => 'char',
            'ctc_statistiques_nom' => 'char'          
        );
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                //array('ctc_id_comptable', 'number', "Id compta"),
                array('ctc_date_creation', 'datetime', "Date de création"),
                array('ctc_nom', 'text', "Nom"),
                array('ctc_adresse', 'text', "Adresse"),
                array('ctc_cp', 'text', "Code postal"),
                array('ctc_ville', 'text', "Ville"),
                array('ctc_telephone', 'text', "Téléphone"),
                array('ctc_fax', 'text', "Fax"),
                array('ctc_mobile', 'text', "Mobile"),
                array('ctc_email', 'text', "Email"),
                array('vcp_type', 'text', "Type", 'v_clients_prospects'),
                array('ctc_marche', 'text', "Marche"),
                array('ctc_date_marche', 'datetime', "Date Marche"),
                array('ctc_alerte', 'text', 'Alerte'),
                array('ctc_date_alerte', 'datetime', 'Date Alerte'),
                array('ctc_remarques_sur_marche', 'text', "Remarques Sur Marche"),
                array('ctc_fournisseur', 'text', "Est fournisseur"),
                array('vac_activite', 'text', "Activité", 'v_activites'),
                array('utl_login', 'text', "Commercial en charge", 'ctc_commercial_charge_nom'),
                array('origine_name', 'text', "Origine détaillée", 'v_types_origine_prospect'),
                array('ctc_origine_generale_nom', 'text', "Origine générale", 'ctc_origine_generale_nom'),
                array('scv_nom', 'text', "Enseigne", 'scv_nom'),
                array('ctc_url', 'text', "URL espace client"),
                array('emp_nom', 'text', "Commercial", 'employes', 'ctc_commercial', 'emp_nom'),
                array('ctc_site', 'text', "Site internet"),
                array('ctc_qual_comm', 'text', "Qualité commerciale"),
                array('ctc_prospection', 'text', "Statut prospection"),
                array('ctc_statistiques_nom', 'text', "Statistiques"),
                array('ctc_signe_nom', 'text', "Signe",'ctc_signe_nom'),
                array('ctc_prix_rural', 'number', "Prix rural"),
                array('ctc_prix_urbain', 'number', "Prix urbain"),
                array('ctc_prix_urgent', 'number', "Prix urgent"),
                array('ctc_prix_cible', 'number', "Prix cible"),
                array('ctc_login', 'text', "Login"),
                array('vtdi_type', 'text', "Type de distribution", 'v_types_distributions'),
                array('ctc_dist_hlm', 'number', "Distribuer HLM"),
                array('ctc_dist_res', 'number', "Distribuer HLM"),
                array('ctc_dist_pav', 'number', "Distribuer HLM"),
                array('ctc_stock', 'number', "Stock actuel"),
                array('ctc_cycle_complet', 'text', "Cycle complet"),
                array('ctc_del_avant_distrib', 'number', "Délai avant prochaine distribution"),
                array('ctc_poids', 'number', "Poids"),
                array('ctc_sonner', 'text', "Sonner aux interphones"),
                array('ctc_livr_adresse', 'text', "Adresse"),
                array('ctc_livr_cp', 'text', "Code postal"),
                array('ctc_livr_ville', 'text', "Ville"),
                array('ctc_livr_horaire', 'text', "Horaires"),
                array('ctc_livr_info', 'text', "Autres informations"),
                array('RowID', 'text', "__DT_Row_ID"),
            ),
            'write' => array(
                //'ctc_id_comptable'         => array("Id compta", 'text', 'ctc_id_comptable', false),
                'ctc_nom'                  => array("Nom", 'text', 'ctc_nom', true),
                'ctc_adresse'              => array("Adresse", 'textarea', 'ctc_adresse', false),
                'ctc_cp'                   => array("Code postal", 'number', 'ctc_cp', false),
                'ctc_ville'                => array("Ville", 'text', 'ctc_ville', false),
                'ctc_complement'           => array("Complément adresse", 'text', 'ctc_complement', false),
                'ctc_telephone'            => array("Téléphone", 'number', 'ctc_telephone', false),
                'ctc_fax'                  => array("Fax", 'number', 'ctc_fax', false),
                'ctc_mobile'               => array("Mobile", 'number', 'ctc_mobile', false),
                'ctc_email'                => array("Email", 'email', 'ctc_email', false),
                'ctc_site'                 => array("Site internet", 'url', 'ctc_site', false),
                'ctc_activite'             => array("Activité", 'select', array('ctc_activite', 'vac_id', 'vac_activite'), false),
                'ctc_commercial_charge'    => array("Commercial en charge", 'select', array('ctc_commercial_charge', 'utl_id', 'utl_login'), false),
                'ctc_origine'              => array("Origine détaillée", 'select', array('ctc_origine', 'id', 'value'), false),                    
                'ctc_enseigne'             => array("Enseigne", 'select', array('ctc_enseigne', 'id', 'value'), false),
                'ctc_notes'                => array("Remarques", 'textarea', 'ctc_notes', false),
                'ctc_client_prospect'      => array("Type", 'radio-h', array('ctc_client_prospect', 'vcp_id', 'vcp_type'), false),
                'ctc_qual_comm'            => array("Qualité commerciale", 'text', 'ctc_qual_comm', false),
                'ctc_prospection'          => array("Statut prospection", 'text', 'ctc_prospection', false),
                'ctc_marche'               => array("Marché", 'checkbox', 'ctc_marche', false),
                'ctc_date_marche'          => array("Date marché", 'date', 'ctc_date_marche', false),
                'ctc_alerte'               => array("Alerte", 'checkbox', 'ctc_alerte', false),
                'ctc_date_alerte'          => array("Date alerte", 'date', 'ctc_date_alerte', false),
                'ctc_remarques_sur_marche' => array("Remarques sur marché", 'textarea', 'ctc_remarques_sur_marche', false),
                'ctc_statistiques'         => array("Statistiques", 'select', array('ctc_statistiques', 'id', 'value'), false),
                'ctc_signe'                => array("Signe", 'select', array('ctc_signe', 'id', 'value'), false),
                'ctc_fournisseur'          => array("Est fournisseur", 'checkbox', 'ctc_fournisseur', false),
                'ctc_url'                  => array("URL espace client", 'url', 'ctc_url', false),
                'ctc_info_connexion'       => array("Informations de connexion", 'textarea', 'ctc_info_connexion', false),
                'ctc_login'                => array("Login", 'text', 'ctc_login', false),
                'ctc_type_distribution'    => array("Type de distribution", 'select', array('ctc_type_distribution', 'vtdi_id', 'vtdi_type'), false),
                'ctc_dist_hlm'             => array("Distribuer HLM", 'number', 'ctc_dist_hlm', false),
                'ctc_dist_res'             => array("Distribuer HLM", 'number', 'ctc_dist_res', false),
                'ctc_dist_pav'             => array("Distribuer HLM", 'number', 'ctc_dist_pav', false),
                'ctc_stock'                => array("Stock actuel", 'number', 'ctc_stock', false),
                'ctc_cycle_complet'        => array("Cycle complet", 'checkbox', 'ctc_cycle_complet', false),
                'ctc_del_avant_distrib'    => array("Délai avant prochaine distribution", 'number', 'ctc_del_avant_distrib', false),
                'ctc_poids'                => array("Poids", 'number', 'ctc_poids', false),
                'ctc_doc_manuel'           => array("Document à ajouter à la main", 'checkbox', 'ctc_doc_manuel', false),
                'ctc_sonner'               => array("Sonner aux interphones", 'checkbox', 'ctc_sonner', false),
                'ctc_prix_rural'           => array("Prix rural", 'number', 'ctc_prix_rural', false),
                'ctc_prix_urbain'          => array("Prix urbain", 'number', 'ctc_prix_urbain', false),
                'ctc_prix_urgent'          => array("Prix urgent", 'number', 'ctc_prix_urgent', false),
                'ctc_prix_cible'           => array("Prix cible", 'number', 'ctc_prix_cible', false),
                'ctc_commercial'           => array("Commercial", 'select', array('ctc_commercial', 'emp_id', 'emp_nom'), true),
                'ctc_livr_adresse'         => array("Adresse", 'textarea', 'ctc_livr_adresse', false),
                'ctc_livr_cp'              => array("Code postal", 'number', 'ctc_livr_cp', false),
                'ctc_livr_ville'           => array("Ville", 'text', 'ctc_livr_ville', false),
                'ctc_livr_horaire'         => array("Horaires", 'text', 'ctc_livr_horaire', false),
                'ctc_livr_info'            => array("Autres informations", 'textarea', 'ctc_livr_info', false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des contacts
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="desc") {

        // première partie du select, mis en cache
        $table = 't_contacts';
        $this->db->start_cache();

        // lecture des informations
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $ctc_date_creation = formatte_sql_date_heure('ctc_date_creation', "'%m/%d/%Y %H:%i'");
        $emp_nom = formatte_sql_lien('employes/detail','emp_id','emp_nom');
        $ctc_statistiques = "if(ctc_statistiques, 'Oui', 'Non')";
        $ctc_statistiques_nom = $ctc_statistiques." AS ctc_statistiques_nom";
		
		//this is hack to filter signe by factures_total
		//will change later with more appropriate way
        //$ctc_signe = "if(ctc_signe, 'Oui', 'Non')";
		//$ctc_signe = "if((ctc_signe = 1 OR fac_reference IS NOT NULL), 'Oui', 'Non')";
		//$ctc_signe = "if(ctc_signe, 'Oui', if(GROUP_CONCAT(nullif(fac_reference,'')) != '', 'Oui', 'Non'))";
		$ctc_signe = "if((ctc_signe = 1 OR (SELECT
                        fac_reference
                      FROM
                        t_factures tf
                      LEFT JOIN
                        t_commandes tco ON tco.cmd_id = tf.fac_commande
                      LEFT JOIN
                        t_devis td ON td.dvi_id = tco.cmd_devis
                      WHERE
                        td.dvi_client = ctc_id AND fac_etat = 2 AND (fac_inactif IS NULL OR fac_inactif = '0000-00-00 00:00:00')
                     GROUP BY dvi_client
                    ) IS NOT NULL), 'Oui', 'Non')"; 

        $ctc_signe_nom = $ctc_signe." AS ctc_signe_nom";
        $ctc_origine_generale = "v_types_origine_generale.generale_name";
        $ctc_origine_generale_nom = $ctc_origine_generale." AS ctc_origine_generale_nom";

        $this->db->select("ctc_id AS RowID,ctc_id,$ctc_nom,$ctc_date_creation,ctc_adresse,ctc_cp,ctc_ville,ctc_telephone,ctc_fax,ctc_mobile,ctc_email,vcp_type,ctc_fournisseur,vac_activite,origine_name,ctc_url,$emp_nom,ctc_site,ctc_qual_comm,if(ctc_marche = 0,'Non','Oui') ctc_marche,ctc_date_marche,if(ctc_alerte = 0,'Non','Oui') ctc_alerte,ctc_date_alerte,ctc_remarques_sur_marche,ctc_prospection,ctc_prix_rural,ctc_prix_urbain,ctc_prix_urgent,ctc_prix_cible,ctc_login,vtdi_type,ctc_dist_hlm,ctc_dist_res,ctc_dist_pav,ctc_stock,ctc_cycle_complet,ctc_del_avant_distrib,ctc_poids,ctc_sonner,ctc_livr_adresse,ctc_livr_cp,ctc_livr_ville,ctc_livr_horaire,ctc_livr_info,utl_login,$ctc_origine_generale_nom,scv_nom,$ctc_statistiques_nom,$ctc_signe_nom",false);
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->join('v_activites','vac_id=ctc_activite','left');
        $this->db->join('v_types_origine_prospect','origine_id=ctc_origine','left');
        $this->db->join('v_types_origine_generale','generale_id=origine_group','left');
        $this->db->join('t_employes','emp_id=ctc_commercial','left');
        $this->db->join('v_types_distributions','vtdi_id=ctc_type_distribution','left');
        $this->db->join('t_utilisateurs','utl_id=ctc_commercial_charge','left');
        $this->db->join('t_societes_vendeuses','scv_id=ctc_enseigne','left');
		
		//check available etat factures
		// $this->db->join('t_devis td', 'ctc_id = td.dvi_client', 'left');
		// $this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
		// $this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande AND 
		// 									fac_etat = 2 AND
		// 									(fac_inactif IS NULL OR fac_inactif = "0000-00-00 00:00:00")', 'left');		
		//$this->db->group_by('ctc_id');

        //$this->db->where('ctc_inactif is null');
        switch($void){
            case 'archived':
                $this->db->where($table.'.ctc_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.ctc_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.ctc_archiver is NULL');
                $this->db->where($table.'.ctc_inactif is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('ctc_id', $id);
        }

        //$this->db->order_by("ctc_nom asc");
        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'ctc_statistiques_nom' => $ctc_statistiques,
            'ctc_signe_nom'        => $ctc_signe,
            'ctc_origine_generale_nom' => $ctc_origine_generale
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
        }

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            //'ctc_id_comptable'=>'int',
            'ctc_date_creation'=>'datetime',
            'ctc_nom'=>'char',
            'ctc_adresse'=>'char',
            'ctc_cp'=>'char',
            'ctc_ville'=>'char',
            'ctc_telephone'=>'char',
            'ctc_fax'=>'char',
            'ctc_mobile'=>'char',
            'ctc_email'=>'char',
            'vcp_type'=>'char',
        	'ctc_marche' => 'char',
        	'ctc_date_marche' => 'datetime',
        	'ctc_alerte' => 'char',
        	'ctc_date_alerte'=>'datetime',
			'ctc_remarques_sur_marche' => 'char',
            'ctc_fournisseur'=>'char',
            'vac_activite'=>'char',
            'utl_login' => 'char',
            'origine_name'=>'char',
            'scv_nom' => 'char',
            'ctc_url'=>'char',
            'emp_nom'=>'char',
            'ctc_site'=>'char',
            'ctc_qual_comm'=>'char',
            'ctc_prospection'=>'char',
            'ctc_prix_rural'=>'decimal',
            'ctc_prix_urbain'=>'decimal',
            'ctc_prix_urgent'=>'decimal',
            'ctc_prix_cible'=>'decimal',
            'ctc_login'=>'char',
            'vtdi_type'=>'char',
            'ctc_dist_hlm'=>'int',
            'ctc_dist_res'=>'int',
            'ctc_dist_pav'=>'int',
            'ctc_stock'=>'int',
            'ctc_cycle_complet'=>'char',
            'ctc_del_avant_distrib'=>'int',
            'ctc_poids'=>'int',
            'ctc_sonner'=>'char',
            'ctc_livr_adresse'=>'char',
            'ctc_livr_cp'=>'char',
            'ctc_livr_ville'=>'char',
            'ctc_livr_horaire'=>'char',
            'ctc_livr_info'=>'char',        
            'ctc_statistiques_nom' => 'char',
            'ctc_signe_nom' => 'char',
            'ctc_origine_generale_nom' => 'char'
        );
        return $filterable_columns;
    }

    /******************************
     * Liste des villes for DataTables
     ******************************/
    public function liste_chunk($limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select("ctc_id AS RowID,ctc_id,ctc_date_creation,ctc_nom,ctc_adresse,ctc_cp,ctc_ville,ctc_telephone,ctc_fax,ctc_mobile,ctc_email,ctc_client_prospect,vcp_type,if(ctc_marche = 0,'Non','Oui') ctc_marche,ctc_date_marche,if(ctc_alerte = 0,'Non','Oui') ctc_alerte,ctc_date_alerte,ctc_remarques_sur_marche,ctc_fournisseur,ctc_activite,vac_activite,ctc_origine,origine_name,ctc_url,ctc_commercial,emp_nom,ctc_site,ctc_qual_comm,ctc_prospection,ctc_prix_rural,ctc_prix_urbain,ctc_prix_urgent,ctc_prix_cible,ctc_login,ctc_type_distribution,vtdi_type,ctc_dist_hlm,ctc_dist_res,ctc_dist_pav,ctc_stock,ctc_cycle_complet,ctc_del_avant_distrib,ctc_poids,ctc_sonner,ctc_livr_adresse,ctc_livr_cp,ctc_livr_ville,ctc_livr_horaire,ctc_livr_info",false);
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->join('v_activites','vac_id=ctc_activite','left');
        $this->db->join('v_types_origine_prospect','origine_id=ctc_origine','left');
        $this->db->join('t_employes','emp_id=ctc_commercial','left');
        $this->db->join('v_types_distributions','vtdi_id=ctc_type_distribution','left');
        $this->db->where('ctc_inactif is null');
        $this->db->stop_cache();

        $table = 't_contacts';
        
        // aliases
        $aliases = array( );

        $resultat = $this->_filtre($table,$this->get_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }
    
    // Return filterable columns
    public function get_filterable_columns() {
            return $this->filterable_columns;
    }
    

    /******************************
    * Nouveau contact
    ******************************/
    public function nouveau_contact($data) {
        $id = $this->_insert('t_contacts', $data);
        return $id;
    }

    /******************************
    * Détail d'un contact
    ******************************/
    public function detail($id) {
        $ctc_origine_generale = "v_types_origine_generale.generale_name";
        $ctc_origine_generale_nom = $ctc_origine_generale." AS ctc_origine_generale_nom";

        // lecture des informations
    	$this->db->select("ctc_id, ctc_nom, ctc_adresse, ctc_cp, ctc_ville, ctc_complement, ctc_telephone"
            .",ctc_fax ,ctc_mobile, ctc_email, ctc_site, ctc_activite, vac_activite, ctc_origine, origine_name"
            .",ctc_notes ,ctc_date_creation, ctc_client_prospect, ctc_marche, ctc_date_marche, ctc_alerte,"
            ."ctc_date_alerte ,ctc_remarques_sur_marche, vcp_type, ctc_qual_comm, ctc_prospection, ctc_fournisseur"
            .",ctc_url ,ctc_info_connexion, ctc_login, ctc_type_distribution, vtdi_type, ctc_dist_hlm, ctc_dist_res"
            .",ctc_dist_pav, ctc_stock, ctc_cycle_complet, ctc_del_avant_distrib, ctc_poids, ctc_doc_manuel, ctc_sonner"
            .",ctc_prix_rural, ctc_prix_urbain, ctc_prix_urgent, ctc_prix_cible, ctc_commercial, emp_nom"
            .",emp_prenom, vcv_civilite"
            .",ctc_livr_adresse, ctc_livr_cp, ctc_livr_ville, ctc_livr_horaire, ctc_livr_info"
            .",ctc_commercial_charge, ctc_enseigne, ctc_statistiques, ctc_signe, $ctc_origine_generale_nom",false);
        $this->db->join('v_activites','vac_id=ctc_activite','left');
        $this->db->join('v_types_origine_prospect','origine_id=ctc_origine','left');
        $this->db->join('v_types_origine_generale','generale_id=origine_group','left');
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->join('v_types_distributions','vtdi_id=ctc_type_distribution','left');
        $this->db->join('t_utilisateurs','utl_id=ctc_commercial_charge','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('v_civilites','vcv_id=emp_civilite','left');
        $this->db->where('ctc_id',$id);
        $this->db->where('ctc_inactif is null');
        $q = $this->db->get('t_contacts');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un contact
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('ctc_id',$id)->get('t_contacts');
        $res =  $this->_update('t_contacts',$data,$id,'ctc_id');
        return $res;
    }

     /**
     * List option for dropdown
     */

    public function liste_option()
    {
        $this->db->select('ctc_id as id, ctc_nom as value');
        $this->db->where('ctc_archiver is NULL');
        $this->db->where('ctc_inactif is NULL');
        $this->db->order_by('ctc_nom','ASC');
        $q = $this->db->get('t_contacts');
        return $q->result();
    }

    /**
     * List option for dropdown
     */
    
    public function liste_origine_prospect()
    {
    	$this->db->select('origine_id as id, origine_name as value');
    	$this->db->order_by('origine_id','ASC');
    	$q = $this->db->get('v_types_origine_prospect');
    	return $q->result();
    }
    
    /**
     * List option for dropdown
     */
    
    public function liste_origine_prospect_per_group()
    {
        $this->db->select('origine_id as id, origine_name as value, origine_group as group_id,generale_name as group');
        $this->db->join('v_types_origine_generale','generale_id=origine_group','left');
        $this->db->order_by('origine_id','ASC');
        $q = $this->db->get('v_types_origine_prospect');
        $result = $q->result();
        $customResult = array();

        $groups = $this->db->select('generale_id as id')->get('v_types_origine_generale')->result();

        foreach($groups as $group) {
            foreach($result as $row) {
                if($row->group_id == $group->id) {
                    $customResult[$group->id][] = $row;
                }
            }
        }

        return $customResult;
    }
    

    public function get_all($order, $sort)
    {
        $this->db->select('ctc_id as id, ctc_nom as value');
        $this->db->order_by('ctc_nom','ASC');        

        if($order) {         
            $this->db->order_by($order, $sort);
        }

        $q = $this->db->get('t_contacts');

        return $q->result();
    }


    public function get($id) 
    {
        $this->db->select('ctc_id as id, ctc_nom as value');
        $this->db->where('ctc_id', $id);
        $this->db->order_by('ctc_nom','ASC');
        $q = $this->db->get('t_contacts');
        return $q->row();
    }

    public function commercial_charge_liste()
    {
        //$utl_nom      = "CASE utl_type WHEN 1 THEN emp_nom WHEN 2 THEN ctc_nom END as value";
        $this->db->select("utl_id as id,utl_login as value",false);
        $this->db->join('t_employes', 'emp_id=utl_employe', 'left');
        $this->db->join('t_contacts', 'ctc_id=utl_sous_traitant', 'left');
        $this->db->join('t_profils', 'prf_id=utl_profil', 'left');
        $this->db->where('prf_nom', "Commercial");
        $query = $this->db->get('t_utilisateurs');

        return $query->result();
    }

    public function enseigne_liste()
    {
        $query = $this->db->select('scv_id as id,scv_nom as value')
                 ->get('t_societes_vendeuses');

        return $query->result();
    }

    public function statistiques_liste()
    {
        $options = array(
            array('id' => 1, 'value' => 'Oui'),
            array('id' => 0, 'value' => 'Non')
        );

        $result = array();

        foreach($options as $row)
        {
            $data = new stdClass;
            $data->id = $row['id'];
            $data->value = $row['value'];
            $result[] = $data;
        }

        return $result;
    }

    public function signe_liste()
    {
        $options = array(
            array('id' => 1, 'value' => 'Oui'),
            array('id' => 0, 'value' => 'Non')
        );

        $result = array();

        foreach($options as $row)
        {
            $data = new stdClass;
            $data->id = $row['id'];
            $data->value = $row['value'];
            $result[] = $data;
        }

        return $result;
    }
	
	public function is_having_factures($id){
		$this->db->select('ctc_id, count(tf.fac_id) as total_factures, sum(fac_montant_ht) as total_ht');
		$this->db->from('t_contacts tc');
		$this->db->join('t_devis td', 'tc.ctc_id = td.dvi_client', 'left');
		$this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
		$this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande AND 
											fac_etat = 2 AND
											(fac_inactif IS NULL OR fac_inactif = "0000-00-00 00:00:00")', 'left');
		$this->db->where('ctc_id = '.$id);
		$sql = $this->db->get();
		$result = $sql->row();
		
		return $result->total_factures;
	}

    public function origine_generale_liste()
    {
        $this->db->select('generale_id as id, generale_name as value');
        $query = $this->db->get('v_types_origine_generale');

        return $query->result();
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_contacts',$id,'ctc_id','ctc_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_contacts',$id,'ctc_id','ctc_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('ctc_inactif' => null, 'ctc_archiver' => null);
        return $this->_update('t_contacts',$data, $id,'ctc_id');
    }

    public function form_option($values, $inc_index = false)
    {
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();
            if ($inc_index) {
                $val->id = $i;
            } else {
                $val->id = $values[$i];
            }

            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }
	
	public function document_detail($id) 
	{
    	$this->db->select("ctc_id,ctc_nom,ctc_adresse,ctc_cp,ctc_ville,ctc_complement,ctc_telephone,ctc_fax,",false);
        $this->db->join('t_employes','emp_id=ctc_commercial','left');
        $this->db->where('ctc_id',$id);
        $this->db->where('ctc_inactif is null');
        $sql = $this->db->get('t_contacts');
        if ($sql->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }
	
	public function liste_template()
	{
		$this->db->select('tpl_id as id, tpl_nom as value');
		$this->db->where('tpl_inactive is NULL');
		$this->db->where('tpl_deleted is NULL');
		$sql = $this->db->get('t_document_templates');
		$result = $sql->result();
		return $result;
	}
	
	public function template_detail($template_id)
	{
		$this->db->where('tpl_inactive is NULL');
		$this->db->where('tpl_deleted is NULL');
		$this->db->where('tpl_id',$template_id);
		$sql = $this->db->get('t_document_templates');
		$result = $sql->row();
		return $result;
	}
	
	function getContacts($id)
	{
		$sql = $this->db->get_where('t_contacts',array('ctc_id' => $id));
		$result= $sql->row('ctc_nom');
		return $result;
	}
	
	function getTemplate($id)
	{
		$sql = $this->db->get_where('t_document_templates',array('tpl_id' => $id));
		$result= $sql->row('tpl_nom');
		return $result;
	}

    function get_selected($ids)
    {
        if(count($ids) > 0) {
            $query = $this->db->select('ctc_id as id, ctc_nom as value')->where_in('ctc_id', $ids)->get('t_contacts');

            $result = $query->result();
            return $result;
        } else {
            return array();
        }
    }
}
// EOF