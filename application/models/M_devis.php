<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_devis extends MY_Model
{
    private $article_distribution_option = array(
        'HABITAT'      => 'habitat',
        'DOCUMENT'     => 'document',
        'DISTRIBUTION' => 'type_distribution',
        'DELAI'        => 'delai',
        'CONTROLE'     => 'controle',
    );
    public function __construct()
    {
        parent::__construct();
        $this->filterable_columns = array(
            'dvi_rappel'     => 'datetime',
            'dvi_reference'  => 'char',
            'dvi_date'       => 'date',
            //'ctc_id_comptable'=>'char',
            'mail'           => 'char',
            'telephone'      => 'char',
            'portable'       => 'char',
            'dvi_client'     => 'char',
            'ved_etat'       => 'select',
            //'dvi_fichier'=>'char',
            'ctc_nom'        => 'char',
            'cor_nom'        => 'char',
            'scv_nom'        => 'char',
            'total_HT'       => 'decimal',
            'total_TTC'      => 'decimal',
            'fac_references' => 'char',
            'dvi_chaleur'    => 'char',
            'dvi_etat'       => 'int',
            'dvi_couleur'    => 'int',
            'dvi_transmis'   => 'char',
            'dvi_relance'    => 'char',
            'dvi_notes'      => 'char',
            //'dvi_fichier'=>'char'
        );

    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('RowID','text','__DT_Row_ID'),    // Row unique ID; DB query column __DT_Row_ID
                array('CBSelect','','#'),               // Row selection checkbox.
                array('dvi_id','ref','RELANCES'),       // Select actions menu
                array('dvi_rappel','datetime','Rappel'),
                array('scv_nom','text','Enseigne'),
                array('dvi_reference','href','N° pièce', '*devis/modification/', 'dvi_id'),
                array('dvi_date','date','Date'),
                //array('ctc_id_comptable','text','ID compta'),   // Note: This is NOT necessarily the value
                //                                                //       in the column with the same name!
                array('dvi_transmis','text','Envoyé le'),
                array('dvi_relance','text','Relance'),
                array('ctc_nom','href','Client', 'contacts/detail/', 'dvi_client'),
                array('cor_nom','text','Correspondant'),
                array('mail','text','Mail'),
                array('telephone','text','Téléphone'),
                array('portable','text','Portable'),
                array('total_HT','currency','Total HT'),
                array('total_TTC','currency','Total TTC'),
                array('fac_references','hreflist','Facture', 'factures/detail/', 'fac_ids'),
                array('dvi_chaleur','text','Chaleur'),
                //array('dvi_fichier','hreffile','PDF', '/'),
                array('dvi_notes','text','Remarques'),
                array('ved_etat','select','État'),
                array('dvi_etat','int','Etat ID', 'invisible'),
                array('dvi_couleur','int','Couleur', 'invisible') //, 'invisible'
            )
        );

        return $champs[$type];
    }

    /******************************
     * Liste filterable columns
     ******************************/
    public function get_filterable_columns()
    {
        return $this->filterable_columns;
    }

    /******************************
     * Liste distinct etats d'un devis
     ******************************/
    public function get_etats()
    {

        $q = $this->db->query('SELECT DISTINCT ved_etat
                                FROM  `t_devis`
                                LEFT JOIN  `v_etats_devis` ON  `ved_id` =  `dvi_etat`
                                ORDER BY dvi_etat ASC ');
        if ($q->num_rows() > 0) {
            $resultat = $q->result();
            return $resultat;
        } else {
            return array();
        }
    }

    /******************************
     * Liste des devis for DataTables
     ******************************/
    public function liste($void = 0, $limit = 0, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations
        $this->db->select("*", false);
        $this->db->stop_cache();

        $table = "(SELECT
                    dvi_id AS RowID,
                    dvi_id,
                    dvi_id_rappel,
                    IF(dvi_id_rappel=0,'', dvi_rappel) AS dvi_rappel,
                    scv_nom,
                    dvi_reference,
                    dvi_date,
                    IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,
                    dvi_transmis,
                    dvi_relance,
                    ctc_nom, dvi_client,
                    cor_nom,
                    COALESCE(cor_email, ctc_email, '') AS mail,
                    COALESCE(cor_telephone1, ctc_telephone, '') AS telephone,
                    COALESCE(cor_telephone2, ctc_mobile, '') AS portable,
                    dvi_montant_ht AS total_HT,
                    dvi_montant_ttc AS total_TTC,
                    fac_references, fac_ids,
                    dvi_chaleur,
                    dvi_fichier,
                    dvi_notes,
                    ved_etat,
                    dvi_etat,
                    dvi_couleur
                FROM  `t_devis`
                LEFT JOIN  `t_id_comptable` ON  (`dvi_client` = `idc_contact` AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
                LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
                LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
                LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
                LEFT JOIN  `v_etats_devis` ON  `ved_id` =  `dvi_etat`
                LEFT JOIN  `t_commandes` ON  `cmd_devis` =  `dvi_id`
                LEFT JOIN (
                    SELECT  `fac_commande` , GROUP_CONCAT(  `fac_id`
                    SEPARATOR  ':' ) AS  `fac_ids` , GROUP_CONCAT(  `fac_reference`
                    SEPARATOR  ':' ) AS  `fac_references`
                    FROM  `t_factures`
                    GROUP BY  `fac_commande`
                ) AS FACTURES ON `fac_commande` = `cmd_id`
                WHERE  `dvi_inactif` IS NULL ";

        $id = intval($void);
        if ($id > 0) {
            $table .= " AND `dvi_id` = " . $id;
        }

        $table .= " ORDER BY  `dvi_date` DESC ,  `dvi_reference` DESC )";

        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table, $this->get_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering, true);
        $this->db->flush_cache();

        return $resultat;
    }
    /******************************
     * Liste des devis for DataTables
     ******************************/
    public function liste_chunk($void = 0, $limit = 0, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $this->load->helper("calcul_devis");

        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations

        $this->db->select("dvi_id, dvi_id AS RowID, dvi_rappel,dvi_id_rappel,dvi_date,IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,dvi_id_comptable,dvi_client,ctc_nom,"
            . "dvi_correspondant,cor_nom,cor_email,dvi_transmis,dvi_relance,dvi_notes,cor_telephone1,"
            . "cor_telephone2,ctc_email,ctc_telephone,ctc_mobile,dvi_chaleur,dvi_couleur,dvi_etat,ved_etat,"
            . "dvi_fichier,dvi_reference,scv_nom,cmd_id", false);
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->join('t_id_comptable', 'idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse', 'left');
        $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
        $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
        $this->db->join('v_etats_devis', 'ved_id=dvi_etat', 'left');
        $this->db->join('t_commandes', 'cmd_devis=dvi_id', 'left');
        $this->db->where('dvi_inactif is null');

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('dvi_id', $id);
        }

        $this->db->order_by("dvi_date desc");
        $this->db->order_by('dvi_reference desc');

        $this->db->stop_cache();

        $table = 't_devis';

        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table, $this->get_filterable_columns(), $aliases, $limit, $offset, $filters, 0, null);
        $this->db->flush_cache();

        $resultat2 = array();
        foreach ($resultat['data'] as $v) {
            $data            = new stdClass();
            $data->dvi_id    = $v->dvi_id;
            $data->dvi_tva   = tva();
            $data            = calcul_devis($data);
            $v->total_HT     = $data->dvi_montant_ht;
            $v->total_TTC    = $data->dvi_montant_ttc;
            $v->mail         = ($v->cor_email != '') ? $v->cor_email : $v->ctc_email;
            $v->telephone    = ($v->cor_telephone1 != '') ? $v->cor_telephone1 : $v->ctc_telephone;
            $v->portable     = ($v->cor_telephone2 != '') ? $v->cor_telephone2 : $v->ctc_mobile;
            $v->texte_rappel = '';
            if ($v->dvi_id_rappel == 0) {
                $v->dvi_rappel = '';
            }

            //$v->dvi_fichier = construit_lien_fichier("",$v->dvi_fichier);
            // cas des reprises de données
            if ($v->dvi_client == 0) {
                $v->ctc_id_comptable = $v->dvi_id_comptable;
                $v->mail             = '';
                $v->telephone        = '';
                $v->portable         = '';
            }

            /* Replaced by MySQL query:
            SELECT dvi_id AS RowID,
            dvi_id,
            --php: if ($v->dvi_id_rappel == 0) $v->dvi_rappel = '';
            dvi_id_rappel,
            IF(dvi_id_rappel=0,'', dvi_rappel) AS dvi_rappel,
            dvi_date,
            --php: if ($v->dvi_client == 0) $v->ctc_id_comptable = $v->dvi_id_comptable;
            IF(dvi_client=0, dvi_id_comptable, ctc_id_comptable) AS ctc_id_comptable,
            dvi_id_comptable, dvi_client,
            ctc_nom, dvi_correspondant, cor_nom,
            --php: $v->mail = ($v->cor_email != '')?$v->cor_email:$v->ctc_email;
            COALESCE(cor_email, ctc_email, '') AS mail,
            dvi_transmis, dvi_relance, dvi_notes,
            --php: $v->telephone = ($v->cor_telephone1 != '')?$v->cor_telephone1:$v->ctc_telephone;
            COALESCE(cor_telephone1, ctc_telephone, '') AS telephone,
            --php: $v->portable = ($v->cor_telephone2 != '')?$v->cor_telephone2:$v->ctc_mobile;
            COALESCE(cor_telephone2, ctc_mobile, '') AS portable,
            '' AS texte_rappel,
            ctc_email, ctc_telephone, ctc_mobile, dvi_chaleur,
            dvi_couleur, dvi_etat, ved_etat,
            --php: $v->dvi_fichier = construit_lien_fichier("",$v->dvi_fichier);
            --php: ignored, will be done on front-end
            dvi_fichier,
            dvi_reference, scv_nom, cmd_id,
            tva_taux, HT, TTC,
            --php: $v->total_HT = $data->dvi_montant_ht;
            dvi_montant_ht AS total_HT,
            --php: $v->total_TTC = $data->dvi_montant_ttc;
            dvi_montant_ttc AS total_TTC,
            dvi_montant_htnr
            FROM  `t_devis`
            LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
            LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
            LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
            LEFT JOIN  `v_etats_devis` ON  `ved_id` =  `dvi_etat`
            LEFT JOIN  `t_commandes` ON  `cmd_devis` =  `dvi_id`
            LEFT JOIN (         SELECT  TT.`dvi_id` AS CALCUL_dvi_id,
            TT.tva_taux AS tva_taux,
            TT.HT AS dvi_montant_htnr,
            (TT.HT * (1-TT.REMISE)) AS HT,
            (TT.HT * (1-TT.REMISE))*(1+TT.tva_taux) AS TTC,
            (TT.HT * (1-TT.REMISE)) AS dvi_montant_ht,
            (TT.HT * (1-TT.REMISE))*(1+TT.tva_taux) AS dvi_montant_ttc
            FROM (
            SELECT T.`dvi_id` , COALESCE( REMISE, 0 ) AS REMISE, COALESCE( HT, 0 ) AS HT, tva_taux
            FROM (
            SELECT tva_taux
            FROM  `t_taux_tva`
            WHERE tva_date = (
            SELECT MAX( tva_date )
            FROM t_taux_tva
            WHERE tva_date <= CURDATE( ) )
            AND tva_inactif IS NULL
            ) AS T_TVA,
            `t_devis` AS T
            LEFT JOIN (
            SELECT  `ard_devis` , SUM(  `ard_prix` ) AS REMISE
            FROM  `t_articles_devis`
            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article`
            WHERE  `ard_inactif` IS NULL
            AND  `art_code` =  'R'
            GROUP BY  `ard_devis`
            ) AS REMISES ON T.`dvi_id` = REMISES.`ard_devis`
            LEFT JOIN (
            SELECT  `ard_devis` , SUM(  `ard_prix` *  `ard_quantite` -  `ard_remise_ht` ) AS HT
            FROM  `t_articles_devis`
            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article`
            WHERE  `ard_inactif` IS NULL
            AND (`art_code` IS NULL OR  `art_code` <>  'R')
            GROUP BY  `ard_devis`
            ) AS HTS ON T.`dvi_id` = HTS.`ard_devis`
            WHERE T.`dvi_inactif` IS NULL ) AS TT
            ) AS CALCUL_DEVIS ON CALCUL_dvi_id =  `dvi_id`
            WHERE  `dvi_inactif` IS NULL
            ORDER BY  `dvi_date` DESC ,  `dvi_reference` DESC

             */
            // recherche des factures associées
            $v->factures = '';
            if ($v->cmd_id > 0) {
                /*$q = $this->db->select("fac_id,fac_reference")
                ->where('fac_commande',$v->cmd_id)
                ->get('t_factures');*/
                $query = $this->db->select("fac_id,fac_reference")
                    ->where('fac_commande', $v->cmd_id)
                    ->get_compiled_select('t_factures');
                //log_message('DEBUG', $query);
                $q = $this->db->query($query);
                if ($q->num_rows() > 0) {
                    $factures = $q->result();
                    $sep      = '';
                    foreach ($factures as $f) {
                        $v->factures .= $sep . anchor('factures/detail/' . $f->fac_id, $f->fac_reference, 'target="_blank"');
                        $sep = '<br />';
                    }
                }
                /* Replace by MySQL Query:
            --view: view_dt_list__devis
            SELECT dvi_id AS RowID,
            dvi_id,
            dvi_id_rappel,
            IF(dvi_id_rappel=0,'', dvi_rappel) AS dvi_rappel,
            dvi_date,
            IF(dvi_client=0, dvi_id_comptable, ctc_id_comptable) AS ctc_id_comptable,
            dvi_id_comptable, dvi_client,
            ctc_nom, dvi_correspondant, cor_nom,
            COALESCE(cor_email, ctc_email, '') AS mail,
            dvi_transmis, dvi_relance, dvi_notes,
            COALESCE(cor_telephone1, ctc_telephone, '') AS telephone,
            COALESCE(cor_telephone2, ctc_mobile, '') AS portable,
            '' AS texte_rappel,
            ctc_email, ctc_telephone, ctc_mobile, dvi_chaleur,
            dvi_couleur, dvi_etat, ved_etat,
            dvi_fichier,
            dvi_reference, scv_nom, cmd_id,
            tva_taux, HT, TTC,
            dvi_montant_ht AS total_HT,
            dvi_montant_ttc AS total_TTC,
            dvi_montant_htnr,
            fac_commande, fac_ids, fac_references
            FROM  `t_devis`
            LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
            LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
            LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
            LEFT JOIN  `v_etats_devis` ON  `ved_id` =  `dvi_etat`
            LEFT JOIN  `t_commandes` ON  `cmd_devis` =  `dvi_id`
            LEFT JOIN (
            --view: view_helper__calcul_devis
            SELECT  TT.`dvi_id` AS CALCUL_dvi_id,
            TT.tva_taux AS tva_taux,
            TT.HT AS dvi_montant_htnr,
            (TT.HT * (1-TT.REMISE)) AS HT,
            (TT.HT * (1-TT.REMISE))*(1+TT.tva_taux) AS TTC,
            (TT.HT * (1-TT.REMISE)) AS dvi_montant_ht,
            (TT.HT * (1-TT.REMISE))*(1+TT.tva_taux) AS dvi_montant_ttc
            FROM (
            --view: view_helper__devis__remise_ht_tva
            SELECT T.`dvi_id` , COALESCE( REMISE, 0 ) AS REMISE, COALESCE( HT, 0 ) AS HT, tva_taux
            FROM (
            --view: view_helper__taux_tva__current
            SELECT tva_taux
            FROM  `t_taux_tva`
            WHERE tva_date = (
            SELECT MAX( tva_date )
            FROM t_taux_tva
            WHERE tva_date <= CURDATE( ) )
            AND tva_inactif IS NULL
            ) AS T_TVA,
            `t_devis` AS T
            LEFT JOIN (
            --view: view_helper__articles_devis__remise
            SELECT  `ard_devis` , SUM(  `ard_prix` ) AS REMISE
            FROM  `t_articles_devis`
            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article`
            WHERE  `ard_inactif` IS NULL
            AND  `art_code` =  'R'
            GROUP BY  `ard_devis`
            ) AS REMISES ON T.`dvi_id` = REMISES.`ard_devis`
            LEFT JOIN (
            --view: view_helper__articles_devis__ht
            SELECT  `ard_devis` , SUM(  `ard_prix` *  `ard_quantite` -  `ard_remise_ht` ) AS HT
            FROM  `t_articles_devis`
            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article`
            WHERE  `ard_inactif` IS NULL
            AND (`art_code` IS NULL OR  `art_code` <>  'R')
            GROUP BY  `ard_devis`
            ) AS HTS ON T.`dvi_id` = HTS.`ard_devis`
            WHERE T.`dvi_inactif` IS NULL ) AS TT
            ) AS CALCUL_DEVIS ON CALCUL_dvi_id =  `dvi_id`
            LEFT JOIN (
            --view: view__helper__factures__commande_ids_references
            SELECT  `fac_commande` , GROUP_CONCAT(  `fac_id`
            SEPARATOR  ':' ) AS  `fac_ids` , GROUP_CONCAT(  `fac_reference`
            SEPARATOR  ':' ) AS  `fac_references`
            FROM  `t_factures`
            GROUP BY  `fac_commande`
            ) AS FACTURES ON `fac_commande` = `cmd_id`
            WHERE  `dvi_inactif` IS NULL
            ORDER BY  `dvi_date` DESC ,  `dvi_reference` DESC
             */
            }
            $resultat2[] = $v;
        }
        $resultat['data'] = $resultat2;
        return $resultat;

    }

    /******************************
     * Liste des devis
     ******************************/
    public function liste_par_client($pere, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $dvi_reference = formatte_sql_lien('devis/detail', 'dvi_id', 'dvi_reference');
        $dvi_date      = formatte_sql_date('dvi_date');
        $cor_nom       = formatte_sql_lien('correspondants/detail', 'cor_id', 'cor_nom');
        $ctc_nom       = formatte_sql_lien('contacts/detail', 'ctc_id', 'ctc_nom');
        $scv_nom       = formatte_sql_lien('societes_vendeuses/detail', 'scv_id', 'scv_nom');
        $this->db->select("dvi_id AS RowID,dvi_id,$dvi_reference,dvi_numero,$dvi_date,dvi_tva,vch_degre,$cor_nom,$ctc_nom,$scv_nom,dvi_montant_ht,dvi_montant_ttc,ved_etat,dvi_fichier", false);
        $this->db->join('v_chaleur', 'vch_id=dvi_chaleur', 'left');
        $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
        $this->db->join('v_etats_devis', 'ved_id=dvi_etat', 'left');
        $this->db->where("dvi_client", $pere);
        $this->db->where('dvi_inactif is null');

        $id = intval($pere);
        if ($id > 0) {
            $this->db->where('dvd_id', $id);
        }

        //$this->db->order_by("dvi_numero asc");
        $this->db->stop_cache();

        $table = 't_devis';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table, $this->liste_par_client_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_par_client_filterable_columns()
    {
        $filterable_columns = array(
            'dvi_numero'      => 'int',
            'dvi_reference'   => 'char',
            'dvi_date'        => 'date',
            'dvi_tva'         => 'decimal',
            'vch_degre'       => 'char',
            'cor_nom'         => 'char',
            'ctc_nom'         => 'char',
            'scv_nom'         => 'char',
            'dvi_montant_ht'  => 'decimal',
            'dvi_montant_ttc' => 'decimal',
            'ved_etat'        => 'char',
            //'dvi_fichier'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
     * Marquer un refus de devis
     ******************************/
    public function marquer_refus($id)
    {
        $data = array('dvi_etat' => 5);
        return $this->_update('t_devis', $data, $id, 'dvi_id');
    }

    /******************************
     * Détail d'un devis
     ******************************/
    public function detail($id)
    {

        // lecture des informations
        $this->db->select("dvi_id,dvi_numero,dvi_reference,dvi_date,dvi_chaleur,vch_degre,dvi_client,ctc_id"
            .",ctc_nom,ctc_adresse,ctc_cp,ctc_ville,dvi_correspondant,cor_nom,cor_id,dvi_societe_vendeuse"
            .",scv_nom,dvi_montant_ht,dvi_montant_ttc,dvi_etat,ved_etat,dvi_tva,dvi_notes,dvi_fichier"
            .",ctc_commercial, emp_nom, emp_prenom, vcv_civilite",false);
        $this->db->join('v_chaleur','vch_id=dvi_chaleur','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->join('t_correspondants','cor_id=dvi_correspondant','left');
        $this->db->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left');
        $this->db->join('v_etats_devis','ved_id=dvi_etat','left');
        $this->db->join('t_utilisateurs','utl_id=ctc_commercial_charge','left');
        $this->db->join('t_employes','emp_id=utl_employe','left');
        $this->db->join('v_civilites','vcv_id=emp_civilite','left');
        $this->db->where('dvi_id',$id);
        $this->db->where('dvi_inactif is null');
        $q = $this->db->get('t_devis');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        return null;
    }

    /******************************
     * Mise à jour d'un devis
     ******************************/
    public function maj($data, $id)
    {
        $q   = $this->db->where('dvi_id', $id)->get('t_devis');
        $res = $this->_update('t_devis', $data, $id, 'dvi_id');
        return $res;
    }

    /******************************
     * Lecture des catalogues
     ******************************/
    public function lecture_catalogue($code)
    {
        $famille = 'Famille_' . substr($code, 0, 1);
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library($famille, null, 'famille');
        return $this->famille->catalogue($code);
    }

    /******************************
     * Nouveau devis
     ******************************/
    public function nouveau($data)
    {
        $data['dvi_date']    = date('Y-m-d H:i:s');
        $data['dvi_chaleur'] = 9;
        $data['dvi_etat']    = 1;
        return $this->_insert('t_devis', $data);
    }

    /******************************
     * Constitution des devis
     ******************************/
    public function constitution($id, $commande)
    {
        if ($id == 0) {

        }
        switch ($commande) {
            case 'create':
                $data = $this->input->post('models');
                foreach ($data as &$d) {
                    $nouveau              = $d;
                    $nouveau['ard_devis'] = $id;
                    $res                  = $this->_insert('t_articles_devis', $nouveau);
                    $this->trigger_articles_devis($id);
                    if ($res !== false) {
                        $d['ard_id'] = $res;
                    }
                }
                return $data;
                break;
            case 'destroy':
                $data = $this->input->post('models');
                $ops  = 0;
                foreach ($data as $d) {
                    $res = $this->_delete('t_articles_devis', $d['ard_id'], 'ard_id', 'ard_inactif');
                    $this->trigger_articles_devis($id);
                    if ($res !== false) {
                        $ops++;
                    }
                }
                return $ops;
                break;
            case 'get':
                $this->db->select("ard_id,ard_article,ard_code,ard_info,ard_description,ard_prix,ard_quantite,ard_remise_taux,ard_remise_ht,ard_remise_ttc");
                $this->db->where("ard_devis", $id);
                $this->db->where('ard_inactif is null');
                $this->db->order_by("ard_code asc");
                $q = $this->db->get('t_articles_devis');
                if ($q->num_rows() > 0) {
                    $result = $q->result();
                    foreach ($result as $v) {
                        $v->actions = anchor("articles_devis/detail/$v->ard_id", "Consulter", 'class="btn btn-default btn-sm view-detail" role="button"') . '&nbsp;';
                    }
                    return $result;
                } else {
                    return array();
                }
                break;
            case 'update':
                $data = $this->input->post('models');
                $ops  = 0;
                foreach ($data as $d) {
                    $d['ard_devis'] = $id;
                    unset($d['actions']);
                    $res = $this->_update('t_articles_devis', $d, $d['ard_id'], 'ard_id');
                    $this->trigger_articles_devis($id);
                    if ($res !== false) {
                        $ops++;
                    }
                }
                return $ops;
                break;
            default:
                return false;
        }

    }

    /******************************
     * Duplication d'un devis
     ******************************/
    public function dupliquer($id)
    {

        // récupération du devis actuel
        $q = $this->db->where('dvi_id', $id)
            ->get('t_devis');
        if ($q->num_rows() > 0) {
            $data = $q->row_array();

            // initialisation du nouveau devis
            unset($data['dvi_id']);
            unset($data['dvi_relance']);
            $data['dvi_numero']    = 0;
            $data['dvi_date']      = date('Y-m-d H:i:s');
            $data['dvi_chaleur']   = 9;
            $data['dvi_etat']      = 1;
            $data['dvi_notes']     = '';
            $data['dvi_transmis']  = '';
            $data['dvi_relance']   = '';
            $data['dvi_couleur']   = 0;
            $data['dvi_id_rappel'] = 0;
            $data['dvi_fichier']   = '';
            $nouvel_id             = $this->_insert('t_devis', $data);
            if ($nouvel_id !== false) {

                // récupération des articles
                $q = $this->db->where('ard_devis', $id)
                    ->where('ard_inactif is null')
                    ->get('t_articles_devis');
                if ($q->num_rows() > 0) {
                    $lignes = $q->result_array();
                    foreach ($lignes as $l) {
                        unset($l['ard_id']);
                        $l['ard_devis'] = $nouvel_id;
                        $resultat       = $this->_insert('t_articles_devis', $l);
                        $this->trigger_articles_devis($nouvel_id);
                        if ($resultat === false) {
                            return $resultat;
                        }
                    }
                }
            }

            // fabrication du pdf
            $this->generer_pdf($nouvel_id);
            return $nouvel_id;
        }
        return false;
    }

    /******************************
     * Envoi du devis par email
     ******************************/
    public function envoyer_email($id)
    {
        $q = $this->db->where('dvi_id', $id)
            ->where('dvi_inactif is null')
            ->select('t_devis.*,scv_email')
            ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left')
            ->get('t_devis');
        if ($q->num_rows() != 1) {
            throw new MY_Exceptions_NoSuchRecord('Pas de devis numéro ' . $id);
        }
        $row = $q->row();

        // récupération de l'adresse email
        $email = null;
        $q     = $this->db->where('cor_id', $row->dvi_correspondant)
            ->where('LENGTH(cor_email)>0')
            ->get('t_correspondants');
        if ($q->num_rows() == 1) {
            $email = $q->row()->cor_email;
        } else {
            $q = $this->db->where('ctc_id', $row->dvi_client)
                ->where('LENGTH(ctc_email)>0')
                ->get('t_contacts');
            if ($q->num_rows() == 1) {
                $email = $q->row()->ctc_email;
            }
        }
        if (!$email) {
            throw new MY_Exceptions_NoEmailAddress("Le contact n'a pas d'adresse email");
        }

        // récupération du modèle de document
        $q = $this->db->where('mod_nom', 'DEVIS')
            ->get('t_modeles_documents');
        if ($q->num_rows() == 0) {
            throw new MY_Exceptions_NoSuchTemplate("Pas de modèle disponible pour le message email");
        }
        $sujet = $q->row()->mod_sujet;
        $corps = $q->row()->mod_texte;

        $dvi_fichier = $this->generer_pdf($id);

        // envoi du mail
        $this->load->library('email');
        $resultat = $this->email->send_one($email, $row->scv_email, $sujet, $corps, $dvi_fichier);
        if (!$resultat) {
            return false;
        }

        // enregistrement de la transmission
        $transmis = $row->dvi_transmis;
        if ($transmis != '') {
            $transmis .= '<br />';
        }
        $transmis .= date('d/m/Y') . '&nbsp;Mail';
        $data = array(
            'dvi_transmis' => $transmis,
        );
        $this->_update('t_devis', $data, $id, 'dvi_id');

        // changement d'état
        $data = array(
            'dvi_etat' => 3,
        );
        return $this->_update('t_devis', $data, $id, 'dvi_id', array('dvi_etat' => 1));
    }

    /******************************
     * Marquer "transmis par courrier"
     ******************************/
    public function marquer_transmis($id)
    {

        // enregistrement de la transmission
        $q = $this->db->where('dvi_id', $id)
            ->get('t_devis');
        if ($q->num_rows() == 1) {
            $transmis = $q->row()->dvi_transmis;
            if ($transmis != '') {
                $transmis .= '<br />';
            }
            $transmis .= date('d/m/Y') . '&nbsp;Courrier';
            $data = array(
                'dvi_transmis' => $transmis,
            );
            $this->_update('t_devis', $data, $id, 'dvi_id');

            // changement d'état
            $data = array(
                'dvi_etat' => 3,
            );
            return $this->_update('t_devis', $data, $id, 'dvi_id', array('dvi_etat' => 1));
        }

    }

    /******************************
     * Passer commande
     ******************************/
    public function passer_commande($id)
    {
        $data = array(
            'cmd_numero'     => 0,
            'cmd_date'       => date('Y-m-d H:i:s'),
            'cmd_devis'      => $id,
            'cmd_etat'       => 1,
            'cmd_cmd_maitre' => 0,
            'cmd_reference'  => '',
        );
        $cmd_id = $this->_insert('t_commandes', $data);
        if ($cmd_id !== false) {
            $data   = array('dvi_etat' => 4);
            $update = $this->_update('t_devis', $data, $id, 'dvi_id');
            if ($update) {
                $q = $this->db->select("dvi_client,scv_id_comptable,IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,ctc_client_prospect,dvi_societe_vendeuse")
                    ->where('dvi_id', $id)
                    ->join('t_id_comptable', 'idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse', 'left')
                    ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left')
                    ->join('t_contacts', 'ctc_id=dvi_client', 'left')
                    ->get('t_devis');
                if ($q->num_rows() > 0) {
                    $id_contact = $q->row('dvi_client');

                    // transformation du prospect en client (le cas échéant)
                    if ($q->row('ctc_client_prospect') < 2) {
                        $this->_update('t_contacts', array('ctc_client_prospect' => 2), $id_contact, 'ctc_id');
                    }
                }

                //insert detail to table t_distributions if code is 'D'
                $this->_insert_detail_distribution($id);

                return $cmd_id;
            }
        }
        return false;
    }

    /******************************
     * Actions de relance
     ******************************/
    public function encours($commande)
    {
        switch ($commande) {
            case 'get':
                $id = $this->input->get('dvi_id');
                if (empty($id)) {
                    return liste();
                }

                $query = "(SELECT
                            dvi_id AS RowID,
                            dvi_id,
                            dvi_id_rappel,
                            IF(dvi_id_rappel=0,'', dvi_rappel) AS dvi_rappel,
                            scv_nom,
                            dvi_reference,
                            dvi_date,
                            IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,
                            dvi_transmis,
                            dvi_relance,
                            ctc_nom, dvi_client,
                            cor_nom,
                            COALESCE(cor_email, ctc_email, '') AS mail,
                            COALESCE(cor_telephone1, ctc_telephone, '') AS telephone,
                            COALESCE(cor_telephone2, ctc_mobile, '') AS portable,
                            dvi_montant_ht AS total_HT,
                            dvi_montant_ttc AS total_TTC,
                            fac_references, fac_ids,
                            dvi_chaleur,
                            dvi_fichier,
                            dvi_notes,
                            ved_etat,
                            dvi_etat,
                            dvi_couleur
                        FROM  `t_devis`
                        LEFT JOIN  `t_id_comptable` ON  (`dvi_client` = `idc_contact` AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
                        LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
                        LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
                        LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
                        LEFT JOIN  `v_etats_devis` ON  `ved_id` =  `dvi_etat`
                        LEFT JOIN  `t_commandes` ON  `cmd_devis` =  `dvi_id`
                        LEFT JOIN (
                            SELECT  `fac_commande` , GROUP_CONCAT(  `fac_id`
                            SEPARATOR  ':' ) AS  `fac_ids` , GROUP_CONCAT(  `fac_reference`
                            SEPARATOR  ':' ) AS  `fac_references`
                            FROM  `t_factures`
                            GROUP BY  `fac_commande`
                        ) AS FACTURES ON `fac_commande` = `cmd_id`
                        WHERE  `dvi_id` = $id
                          AND `dvi_inactif` IS NULL
                        ORDER BY  `dvi_date` DESC ,  `dvi_reference` DESC )";

                $q = $this->db->query($query);
                if ($q->num_rows() == 1) {
                    return $q->result();
                } else {
                    return array();
                }

                break;
            case 'update':
                //log_message('DEBUG', 'In M_devis/encours/update; input: '.json_encode($this->input));
                //log_message('DEBUG', 'In M_devis/encours/update; input POST: '.json_encode($_POST));
                //log_message('DEBUG', 'In M_devis/encours/update; input GET: '.json_encode($_GET));
                //log_message('DEBUG', 'In M_devis/encours/update; dvi_id: '.$this->input->get('dvi_id'));

                if (empty($this->input->get('dvi_id'))) {
                    return false;
                }

                $date_rappel = new DateTime($this->input->get('dvi_rappel'));
                //log_message('DEBUG', 'In M_devis/encours/update; dvi_id: '.$id.', with date_rappel: '.$date_rappel);
                $data = array(
                    'dvi_etat'      => $this->input->get('dvi_etat'),
                    'dvi_couleur'   => $this->input->get('dvi_couleur'),
                    'dvi_chaleur'   => $this->input->get('dvi_chaleur'),
                    'dvi_relance'   => $this->input->get('dvi_relance'),
                    'dvi_rappel'    => $date_rappel->format('Y-m-d H:i:s'),
                    'dvi_id_rappel' => $this->input->get('dvi_id_rappel'),
                    'dvi_notes'     => $this->input->get('dvi_notes'),
                );
                $id = $this->input->get('dvi_id');
                //log_message('DEBUG', 'In M_devis/encours/update; dvi_id: '.$id.', with data: '.json_encode($data));
                $res = $this->_update('t_devis', $data, $id, 'dvi_id');
                if ($res !== false) {
                    return 1;
                }
                return 0;
                break;
            default:return false;
        }
    }

    /******************************
     * Renvoi d'un devis par mail
     ******************************/
    public function renvoi($type, $dev_id)
    {
        $aujourdhui = date('d/m/Y');
        if ($type == 'RI') {

            // renvoi de type impression
            $q = $this->db->select('dvi_fichier')
                ->where('dvi_id', $dev_id)
                ->get('t_devis');
            if ($q->num_rows() == 1) {
                $lien  = construit_lien_fichier("", $q->row()->dvi_fichier);
                $texte = "<tr><td>$aujourdhui</td><td>$lien</td>";
                return $texte;
            }
        } else {
            $type = 'DEVIS';

            // récupération du modèle de document
            $q = $this->db->where('mod_nom', 'DEVIS')
                ->get('t_modeles_documents');
            if ($q->num_rows() > 0) {

                // récupération des informations du devis
                $this->db->select("dvi_id,dvi_rappel,dvi_date,IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,ctc_nom,cor_nom,cor_email,dvi_transmis,"
                    . "dvi_relance,dvi_notes,cor_telephone1,cor_telephone2,ctc_email,ctc_telephone,ctc_mobile,dvi_chaleur,"
                    . "dvi_montant_ht AS total_HT,dvi_montant_ttc AS total_TTC,"
                    . "dvi_couleur,dvi_etat,dvi_fichier,dvi_reference,scv_email", false);
                $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
                $this->db->join('t_id_comptable', 'idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse', 'left');
                $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
                $this->db->join('v_civilites', 'vcv_id=cor_civilite', 'left');
                $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
                $this->db->where('dvi_id', $dev_id);
                $q2 = $this->db->get('t_devis');
                if ($q2->num_rows() > 0) {
                    $v       = $q2->row();
                    $v->mail = ($v->cor_email != '') ? $v->cor_email : $v->ctc_email;
                    if ($v->mail == '') {
                        return false;
                    }

                    // envoi du mail
                    $sujet = $q->row()->mod_sujet;
                    $corps = $q->row()->mod_texte;
                    $this->load->library('email');
                    $resultat = $this->email->send_one($v->mail, $v->scv_email, $sujet, $corps, $v->dvi_fichier);

                    if ($resultat) {
                        $texte = "<tr><td>$aujourdhui</td><td>Mail</td>";
                        return $texte;
                    }
                }
            }
        }
        return false;
    }

    /******************************
     * Returns PDF file path for the devis
     *
     * If the PDF file does not already exist, it will attempt to generate it.
     *
     * @param int $id Devis id
     * @return array|boolean Associative array with indexes on success, FALSE otherwise.
     * Indexes of array:
     * <ul>
     *  <li>path: (string) Path to PDF file</li>
     *  <li>created: (boolean) Whether the PDF was created by this call</li>
     * </ul>
     * @throws MY_Exceptions_NoSuchRecord in case there is no such devis
     * @throws MY_Exceptions_NoSuchTemplate in case there is no template to generate the PDF
     ******************************/
    public function pdf($id)
    {
        $valeurs = $this->detail($id);
        if (!$valeurs) {
            throw new MY_Exceptions_NoSuchRecord('Impossible de trouver le devis ' . $id);
        }
        $chemin = $this->generer_pdf($id, true);
        if ($chemin) {
            return array(
                'path'    => $chemin,
                'created' => true,
            );
        }
        return $chemin;
    }

    /******************************
     * Génération du devis en pdf
     ******************************/
    public function generer_pdf($id, $pdf = true)
    {

        // récupération des informations du devis
        $q = $this->db->where('dvi_id',$id)
            ->select('t_devis.*,t_societes_vendeuses.*,t_contacts.*,t_id_comptable.*,t_correspondants.*')
            ->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left')
            ->join('t_contacts','ctc_id=dvi_client','left')
            ->join('t_correspondants','dvi_correspondant=cor_id','left')
            ->join('t_id_comptable','dvi_client=idc_contact AND dvi_societe_vendeuse=idc_societe_vendeuse','left')
            ->get('t_devis');
        if ($q->num_rows() != 1) {
            return false;
        }
        $devis = $q->row();

        // Sélection du bon id comptable
        if ($devis->dvi_id_comptable > 0) {
            $devis->ctc_id_comptable = $devis->dvi_id_comptable;
        } else {
            $devis->ctc_id_comptable = $devis->idc_id_comptable;
        }

        // récupération du détail du devis
        $q = $this->db->where('ard_devis', $id)
            ->where('ard_inactif is null')
            ->get('t_articles_devis');
        $articles = array();
        if ($q->num_rows() > 0) {
            $articles = $q->result();
        }

        // génération du html
        $this->load->helper('view');
        $modele = '_modeles/' . $devis->scv_modele_devis;
        if (!view_exists($modele)) {
            throw new MY_Exceptions_NoSuchTemplate('Could not load view file ' . $modele);
        }
        $html = $this->load->view($modele, array('devis' => $devis, 'articles' => $articles), true);
        if ($pdf) {

            // génération du pdf
            $this->load->library('pdf');
            $uniqid = uniqid();
            $chemin = "fichiers/devis/devis_$devis->scv_id-$uniqid-$devis->dvi_numero.pdf";
            $this->pdf->creation($html, $chemin, $devis->scv_en_production);

            // mémorisation du pdf
            $this->_update('t_devis', array('dvi_fichier' => $chemin), $id, 'dvi_id');

            return $chemin;
        }
        return $html;
    }

    /******************************
     * Suppression d'un devis
     ******************************/
    public function suppression($id)
    {
        // récupération des informations sur le numéro
        $d = $this->db->select("scv_id")
            ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse')
            ->where('dvi_id', $id)
            ->get('t_devis')
            ->row();
        if (empty($d)) {
            return false;
        }

        // suppression et ajustement du numéro de dernier devis
        $this->db->trans_start();
        $this->_delete('t_devis', $id, 'dvi_id', 'dvi_inactif');

        $row = $this->db->select('MAX(dvi_numero) AS dvi_numero_max')
            ->where('dvi_inactif IS NULL')
            ->get('t_devis')
            ->row();

        $this->_update('t_societes_vendeuses', array('scv_no_devis' => max(0, $row->dvi_numero_max)), $d->scv_id, 'scv_id');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            return false;
        }

        return true;
    }

    /******************************
     * Trigger articles de devis
     ******************************/
    public function trigger_articles_devis($id)
    {
        // récupération du taux de TVA
        $q = $this->db->select('dvi_tva')
            ->where('dvi_id', $id)
            ->get('t_devis');
        $tva = $q->row()->dvi_tva;

        // récupération des articles du devis
        $q = $this->db->select("art_code,ard_prix,ard_quantite,ard_remise_ht")
            ->join('t_articles', 'art_id=ard_article', 'left')
            ->where('ard_inactif is null')
            ->where('ard_devis', $id)
            ->get('t_articles_devis');
        if ($q->num_rows() > 0) {
            $articles = $q->result();

            // calcul des montants HT et TTC
            $ht     = 0;
            $remise = 0;
            foreach ($articles as $a) {
                if ($a->art_code == 'R') {
                    $remise += $a->ard_prix;
                } else {
                    $ht += $a->ard_prix * $a->ard_quantite - $a->ard_remise_ht;
                }
            }
            $montant_htnr = $ht;
            $ht           = $ht * (1 - $remise);
            $ttc          = $ht * (1 + $tva);
            $montant_ht   = $ht;
            $montant_ttc  = $ttc;
        } else {
            $montant_htnr = 0;
            $montant_ht   = 0;
            $montant_ttc  = 0;
        }
        $data = array(
            'dvi_montant_htnr' => $montant_htnr,
            'dvi_montant_ht'   => $montant_ht,
            'dvi_montant_ttc'  => $montant_ttc,
        );
        $this->db->where('dvi_id', $id)
            ->update('t_devis', $data);
    }

    public function _insert_detail_distribution($devis_id)
    {
        $query = $this->db->select('ard_code,ard_quantite,ard_info')->where('ard_devis', $devis_id)->get('t_articles_devis');

        if ($query->num_rows() > 0) {
            $articles_devis = $query->result();
            $data           = array();

            foreach ($articles_devis as $row) {
                $ard_code          = $row->ard_code;
                $code_distribution = substr($ard_code, 0, 1);

                if ($code_distribution == "D") {
                    $code        = substr($row->ard_info, 0, 3);
                    $info_string = substr($row->ard_info, 3);
                    $info_arr    = explode(":", $info_string);
                    $ville_id    = $info_arr[0];
                    $secteur_id  = $info_arr[1];

                    if (!array_key_exists($ville_id, $data)) {
                        $data[$ville_id] = array();
                    }

                    if (!array_key_exists($secteur_id, $data[$ville_id])) {
                        $data[$ville_id][$secteur_id] = array();
                    }

                    if (!array_key_exists($code, $data[$ville_id][$secteur_id])) {
                        $data[$ville_id][$secteur_id][$code] = $row->ard_quantite;
                    }
                }
            }

            //insert to table t_distributions
            if (count($data) > 0) {
                $distributions = array();
                foreach ($data as $ville_id => $secteurs) {
                    foreach ($secteurs as $secteur_id => $codes) {

                        if ($secteur_id != 0) {
                            $hlm = !empty($codes['HLM']) ? $codes['HLM'] : 0;
                            $res = !empty($codes['RES']) ? $codes['RES'] : 0;
                            $pav = !empty($codes['PAV']) ? $codes['PAV'] : 0;

                            $distribution = array(
                                'devis_id'   => $devis_id,
                                'secteur_id' => $secteur_id,
                                'hlm'        => $hlm,
                                'res'        => $res,
                                'pav'        => $pav,
                            );

                            $distributions[] = $distribution;
                        } else {
                            $secteurs = $this->db->select('sec_id,sec_hlm,sec_res,sec_pav')->where('sec_ville', $ville_id)->get('t_secteurs')->result();

                            // $hlm      = !empty($codes['HLM']) ? $codes['HLM'] : 0;
                            // $res      = !empty($codes['RES']) ? $codes['RES'] : 0;
                            // $pav      = !empty($codes['PAV']) ? $codes['PAV'] : 0;

                            foreach ($secteurs as $secteur) {
                                $distribution = array(
                                    'devis_id'   => $devis_id,
                                    'secteur_id' => $secteur->sec_id,
                                    'hlm'        => $secteur->sec_hlm,
                                    'res'        => $secteur->sec_res,
                                    'pav'        => $secteur->sec_pav,
                                );

                                $distributions[] = $distribution;
                            }
                        }
                    }
                }

                $this->db->insert_batch('t_distributions', $distributions);
            }
        }
    }

    /**
     * @param integer $enseigne
     *
     * @return integer
     */
    public function plus_grand_numero($enseigne)
    {
        $row = $this->db->select('MAX(dvi_numero) AS max_dvi_numero')
            ->where('dvi_societe_vendeuse', $enseigne)
            ->where('dvi_inactif IS NULL')
            ->get('t_devis')
            ->row();
        return intval($row->max_dvi_numero, 10);
    }

}

// EOF