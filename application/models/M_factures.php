<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_factures extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->filterable_columns = array(
            'fac_rappel'       => 'datetime',
            'scv_nom'          => 'char',
            'fac_reference'    => 'char',
            'dvi_reference'    => 'char',
            'fac_date'         => 'date',
            'ctc_id_comptable' => 'char',
            'fac_transmise'    => 'char',
            'fac_relance'      => 'char',
            'ctc_nom'          => 'char',
            'cor_nom'          => 'char',
            'mail'             => 'char',
            'telephone'        => 'char',
            'portable'         => 'char',
            'fac_montant_ttc'  => 'char',
            'fac_montant_ht'   => 'char',
            'solde_du'         => 'decimal',
            'rgl_references'   => 'char',
            'fac_contentieux'  => 'char',
            //'fac_fichier'=>'char',
            'fac_notes'        => 'char',
            'vef_etat'         => 'select',
            'fac_masque'       => 'int',
            'due'              => 'int',
        );
    }

    public function get_champs($type)
    {
        $champs = array(
            'read'  => array(
                array('RowID', 'text', '__DT_Row_ID', 'invisible'), // Row unique ID; DB query column __DT_Row_ID
                array('CBSelect', '', '#'), // Row selection checkbox.
                array('fac_id', 'int', 'RELANCES'), // Select actions menu
                array('fac_rappel', 'datetime', 'Rappel'),
                array('scv_nom', 'text', 'Enseigne'),
                array('fac_reference', 'href', 'N° pièce', '*factures/lignes/', 'fac_id'),
                array('dvi_reference', 'text', 'N° devis'),
                array('fac_date', 'date', 'Date'),
                array('ctc_id_comptable', 'text', 'ID compta'), // Note: This is NOT necessarily the value
                //       in the column with the same name!
                array('fac_transmise', 'text', 'Transmise'),
                array('fac_relance', 'text', 'Relance'),
                array('ctc_nom', 'href', 'Client', 'contacts/detail/', 'dvi_client'),
                array('cor_nom', 'text', 'Correspondant'),
                array('mail', 'text', 'Mail'),
                array('telephone', 'text', 'Téléphone'),
                array('portable', 'text', 'Portable'),
                array('fac_montant_ht', 'currency', 'Total HT'),
                array('fac_montant_ttc', 'currency', 'Total TTC'),
                array('solde_du', 'currency', 'Solde dû'),
                array('rgl_references', 'hreflist', 'Règlements', 'reglements/detail/', 'ipu_reglements'),
                array('fac_contentieux', 'text', 'Contentieux'),
                //array('fac_fichier','hreffile','PDF', '/'),
                array('fac_notes', 'text', 'Remarques'),
                array('vef_etat', 'select', 'État'),
                array('fac_masque', 'int', 'fac_masque', 'invisible'),
                array('due', 'int', 'due', 'invisible'),
            ),
            'write' => array(
                'fac_reference'      => array("Référence", 'text', 'fac_reference', true),
                'fac_delai_paiement' => array("Délai de paiement", 'number', 'fac_delai_paiement', true),
                'fac_type'           => array("Type de facture", 'select', array('fac_type', 'vtf_id', 'vtf_type'), true),
                'fac_notes'          => array("Remarques", 'textarea', 'fac_notes', false),
            ),
        );

        if (array_key_exists($type, $champs)) {
            return $champs[$type];
        }

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

        $q = $this->db->query('SELECT DISTINCT vef_etat
                                FROM  `t_factures`
                                LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
                                ORDER BY fac_etat ASC ');
        if ($q->num_rows() > 0) {
            $resultat = $q->result();
            return $resultat;
        } else {
            return array();
        }
    }

    /******************************
     * Liste des factures for DataTables
     ******************************/
    public function liste($id = 0, $limit = 0, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        /*  PHP Logic replaced by query:
        ====================================================================================
        SELECT fac_id AS RowID, fac_id, fac_date, fac_tva,
        ctc_nom, dvi_correspondant, cor_nom, fac_transmise, fac_relance, fac_contentieux,
        fac_notes, vef_etat, fac_couleur, fac_masque,
        fac_fichier, fac_reference, scv_nom, dvi_reference, fac_reprise,
        fac_montant_htnr, fac_montant_ht, fac_montant_tva, fac_montant_ttc, fac_regle,
        fac_reste, fac_reste AS solde_du,
        fac_etat,
        IF( fac_reste>=0.01 AND fac_etat=2, 1, 0) AS due,
        ipu_facture, ipu_reglements, rgl_references,
        dvi_client, dvi_id_comptable,
        IF( dvi_client=0, dvi_id_comptable, ctc_id_comptable) AS ctc_id_comptable,
        cor_email, ctc_email,
        IF( dvi_client=0, '', COALESCE(cor_email, ctc_email,'')) AS mail,
        cor_telephone1, ctc_telephone,
        IF( dvi_client=0, '', COALESCE(cor_telephone1, ctc_telephone,'')) AS telephone,
        cor_telephone2, ctc_mobile,
        IF( dvi_client=0, '', COALESCE(cor_telephone2, ctc_mobile,'')) AS portable,
        '' AS texte_rappel,
        fac_id_rappel,
        IF(fac_id_rappel=0, '', fac_rappel) AS fac_rappel
        FROM  `t_factures`
        LEFT JOIN  `t_commandes` ON  `cmd_id` =  `fac_commande`
        LEFT JOIN  `t_devis` ON  `dvi_id` =  `cmd_devis`
        LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
        LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
        LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
        LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
        LEFT JOIN  (
        SELECT  `ipu_facture` ,
        GROUP_CONCAT(`ipu_reglement` SEPARATOR ':') AS ipu_reglements,
        GROUP_CONCAT(`rgl_reference` SEPARATOR ':') AS rgl_references
        FROM  `t_imputations`
        LEFT JOIN  `t_reglements` ON  `rgl_id` =  `ipu_reglement`
        WHERE  `ipu_inactif` IS NULL
        GROUP BY  `ipu_facture`
        ORDER BY  `ipu_facture`
        ) AS REGLEMENTS ON fac_id = ipu_facture
        WHERE  `fac_inactif` IS NULL
        ORDER BY  `fac_date` DESC ,  `fac_reference` DESC
        ====================================================================================
         */

        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations
        $this->db->select("*", false);
        $this->db->stop_cache();

        $table = "(SELECT fac_id AS RowID, fac_id, fac_date, fac_tva,
                        ctc_nom, dvi_correspondant, cor_nom, fac_transmise, fac_relance, fac_contentieux,
                        fac_notes, vef_etat, fac_couleur, fac_masque,
                        fac_fichier, fac_reference, scv_nom, dvi_reference, fac_reprise,
                        fac_montant_htnr, fac_montant_ht, fac_montant_tva, fac_montant_ttc, fac_regle,
                        fac_reste, fac_reste AS solde_du,
                        fac_etat,
                        IF( fac_reste>=0.01 AND fac_etat=2, 1, 0) AS due,
                        ipu_facture, ipu_reglements, rgl_references,
                        dvi_client, dvi_id_comptable,
                        IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,
                        cor_email, ctc_email,
                        IF( dvi_client=0, '', COALESCE(cor_email, ctc_email,'')) AS mail,
                        cor_telephone1, ctc_telephone,
                        IF( dvi_client=0, '', COALESCE(cor_telephone1, ctc_telephone,'')) AS telephone,
                        cor_telephone2, ctc_mobile,
                        IF( dvi_client=0, '', COALESCE(cor_telephone2, ctc_mobile,'')) AS portable,
                        '' AS texte_rappel,
                        fac_id_rappel,
                        IF(fac_id_rappel=0, '', fac_rappel) AS fac_rappel
                    FROM  `t_factures`
                    LEFT JOIN  `t_commandes` ON  `cmd_id` =  `fac_commande`
                    LEFT JOIN  `t_devis` ON  `dvi_id` =  `cmd_devis`
                    LEFT JOIN  `t_id_comptable` ON  (`dvi_client` = `idc_contact` AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
                    LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
                    LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
                    LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
                    LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
                    LEFT JOIN  (
                                SELECT  `ipu_facture` ,
                                        GROUP_CONCAT(`ipu_reglement` SEPARATOR ':') AS ipu_reglements,
                                        GROUP_CONCAT(`rgl_reference` SEPARATOR ':') AS rgl_references
                                FROM  `t_imputations`
                                LEFT JOIN  `t_reglements` ON  `rgl_id` =  `ipu_reglement`
                                WHERE  `ipu_inactif` IS NULL
                                GROUP BY  `ipu_facture`
                                ORDER BY  `ipu_facture`
                            ) AS REGLEMENTS ON fac_id = ipu_facture
                    WHERE  `fac_inactif` IS NULL
                      AND  `fac_masque` = 0 ";
        if ($id) {
            $table .= " AND fac_id = " . intval($id);
        }
        $table .= " ORDER BY  `fac_date` DESC ,  `fac_reference` DESC )";

        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table, $this->get_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering, true);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
     * Liste des factures for DataTables
     ******************************/
    public function liste_chunk($limit = 0, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {

        /*  Replace by query:

        SELECT fac_id AS RowID, fac_id, fac_rappel, fac_id_rappel, fac_date, fac_tva, ctc_id_comptable, dvi_id_comptable,
        dvi_client, ctc_nom, dvi_correspondant, cor_nom, cor_email, fac_transmise, fac_etat, fac_relance, fac_contentieux,
        fac_notes, cor_telephone1, vef_etat, cor_telephone2, ctc_email, ctc_telephone, ctc_mobile, fac_couleur, fac_masque,
        fac_fichier, fac_reference, scv_nom, dvi_reference, fac_reprise,
        fac_montant_htnr, fac_montant_ht, fac_montant_tva, fac_montant_ttc, fac_regle, fac_reste,
        --psevdo-php ($v->solde_du >= 0.01 AND $v->fac_etat == 2)? $v->due = 1 : $v->due = 0;
        fac_reste AS solde_du,
        IF( fac_reste>=0.01 AND fac_etat=2, 1, 0) AS due
        FROM  `t_factures`
        LEFT JOIN  `t_commandes` ON  `cmd_id` =  `fac_commande`
        LEFT JOIN  `t_devis` ON  `dvi_id` =  `cmd_devis`
        LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
        LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
        LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
        LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
        WHERE  `fac_inactif` IS NULL
        ORDER BY  `fac_date` DESC ,  `fac_reference` DESC

         */

        $this->load->helper('calcul_factures');

        /* Disable query builder
        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations

        $this->db->select("fac_id, fac_id AS RowID,fac_rappel,fac_id_rappel,fac_date,fac_tva,ctc_id_comptable,dvi_id_comptable,dvi_client,ctc_nom,"
        ."dvi_correspondant,cor_nom,cor_email,fac_transmise,fac_etat,fac_relance,fac_contentieux,fac_notes,"
        ."cor_telephone1,vef_etat,cor_telephone2,ctc_email,ctc_telephone,ctc_mobile,fac_couleur,fac_masque,"
        ."fac_fichier,fac_id_rappel,fac_reference,scv_nom,dvi_reference,fac_reprise",false);
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->join('t_correspondants','cor_id=dvi_correspondant','left');
        $this->db->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left');
        $this->db->join('v_etats_factures','vef_id=fac_etat','left');
        $this->db->where('fac_inactif is null');
        $this->db->order_by('fac_date','DESC');
        $this->db->order_by('fac_reference','DESC');

        $this->db->stop_cache();

        $table = 't_factures';

        // aliases
        $aliases = array( );

        $resultat = $this->_filtre($table,$this->get_filterable_columns(),$aliases,$limit,$offset,$filters,0,NULL);
        $this->db->flush_cache();

         */
        $resultat = array();
        $q        = $this->db->query("SELECT fac_id, fac_id AS RowID, fac_rappel, fac_id_rappel, fac_date, fac_tva,
                                    IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable, dvi_id_comptable,
                                    dvi_client, ctc_nom, dvi_correspondant, cor_nom, cor_email, fac_transmise, fac_etat, fac_relance, fac_contentieux,
                                    fac_notes, cor_telephone1, vef_etat, cor_telephone2, ctc_email, ctc_telephone, ctc_mobile, fac_couleur, fac_masque,
                                    fac_fichier, fac_reference, scv_nom, dvi_reference, fac_reprise
                                FROM  `t_factures`
                                LEFT JOIN  `t_commandes` ON  `cmd_id` =  `fac_commande`
                                LEFT JOIN  `t_devis` ON  `dvi_id` =  `cmd_devis`
                                LEFT JOIN  `t_id_comptable` ON (`dvi_client` = `idc_contact` AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
                                LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
                                LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
                                LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
                                LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
                                WHERE  `fac_inactif` IS NULL
                                ORDER BY  `fac_date` DESC ,  `fac_reference` DESC ");

        if ($q->num_rows() > 0) {
            $resultat = array("data" => $q->result(), "recordsTotal" => $q->num_rows(), "recordsFiltered" => $q->num_rows(),
                "recordsOffset"          => $offset, "recordsLimit"      => $limit,
                "ordercol"               => "fac_date", "ordering"       => "desc");
            //return $result;
        } else {
            $resultat = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0, "recordsOffset" => $offset, "recordsLimit" => $limit);
            //return $result;
        }

        $resultat2 = array();
        foreach ($resultat['data'] as $v) {
            // informations de prix
            $data              = new stdClass();
            $data->fac_id      = $v->fac_id;
            $data->fac_tva     = $v->fac_tva;
            $data->fac_etat    = $v->fac_etat;
            $data->vef_etat    = $v->vef_etat;
            $data->fac_reprise = $v->fac_reprise;
            $data              = calcul_factures($data);
            $v->fac_etat       = $data->fac_etat;
            $v->vef_etat       = $data->vef_etat;
            $v->total_HT       = $data->fac_montant_ht;
            $v->total_TTC      = $data->fac_montant_ttc;
            $v->solde_du       = $data->fac_reste;
            if ($v->solde_du >= 0.01 and $v->fac_etat == 2) {
                $v->due = 1;
            } else {
                $v->due = 0;
            }

            // informations de règlement
            $query = $this->db->where('ipu_facture', $v->fac_id)
                ->where('ipu_inactif is null')
                ->select('ipu_reglement,rgl_reference')
                ->join('t_reglements', 'rgl_id=ipu_reglement', 'left')
                ->get_compiled_select('t_imputations', false);
            //log_message('DEBUG', 'M_factures informations de règlement\n '. $query);
            $q          = $this->db->query($query);
            $reglements = '';
            $sep        = '';
            foreach ($q->result() as $r) {
                $reglements .= $sep . anchor_popup('reglements/detail/' . $r->ipu_reglement, $r->rgl_reference);
                $sep = '<br />';
            }
            $v->reglements = $reglements;

            // autres informations
            $v->mail         = ($v->cor_email != '') ? $v->cor_email : $v->ctc_email;
            $v->telephone    = ($v->cor_telephone1 != '') ? $v->cor_telephone1 : $v->ctc_telephone;
            $v->portable     = ($v->cor_telephone2 != '') ? $v->cor_telephone2 : $v->ctc_mobile;
            $v->texte_rappel = '';
            if ($v->fac_id_rappel == 0) {
                $v->fac_rappel = '';
            }

            //$v->fac_fichier = construit_lien_fichier("",$v->fac_fichier);

            // cas des reprises de données
            if ($v->dvi_client == 0) {
                $v->ctc_id_comptable = $v->dvi_id_comptable;
                $v->mail             = '';
                $v->telephone        = '';
                $v->portable         = '';
            }

            $resultat2[] = $v;
        }
        $resultat['data'] = $resultat2;
        return $resultat;
    }

    /******************************
     * Liste des factures
     ******************************/
    public function liste_par_client($pere, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $fac_reference      = formatte_sql_lien('factures/detail', 'fac_id', 'fac_reference');
        $fac_date           = formatte_sql_date('fac_date');
        $fac_date_paiement  = "DATE_ADD(fac_date,INTERVAL fac_delai_paiement DAY)";
        $fac_date_paiement2 = $fac_date_paiement . " AS fac_date_paiement";
        $cmd_reference      = formatte_sql_lien('commandes/detail', 'cmd_id', 'cmd_reference');
        $dvi_reference      = formatte_sql_lien('devis/detail', 'dvi_id', 'dvi_reference');
        $ctc_nom            = formatte_sql_lien('contacts/detail', 'ctc_id', 'ctc_nom');
        $this->db->select("fac_id AS RowID,fac_id,$fac_reference,fac_numero,$fac_date,fac_tva,fac_montant_ht,fac_montant_ttc,fac_delai_paiement,$fac_date_paiement2,vtf_type,vef_etat,$cmd_reference,$dvi_reference,$ctc_nom,fac_regle,fac_reste,fac_fichier", false);
        $this->db->join('v_types_factures', 'vtf_id=fac_type', 'left');
        $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
        $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
        $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->where("dvi_client", $pere);
        $this->db->where('fac_inactif is null');
        //$this->db->order_by("fac_numero asc");
        $this->db->stop_cache();

        $table = 't_factures';

        // aliases
        $aliases = array(
            'fac_date_paiement' => $fac_date_paiement,
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
            'fac_numero'         => 'int',
            'fac_reference'      => 'char',
            'fac_date'           => 'date',
            'fac_tva'            => 'decimal',
            'fac_montant_ht'     => 'decimal',
            'fac_montant_ttc'    => 'decimal',
            'fac_delai_paiement' => 'int',
            'fac_date_paiement'  => 'char',
            'vtf_type'           => 'char',
            'vef_etat'           => 'char',
            'cmd_reference'      => 'char',
            'dvi_reference'      => 'char',
            'ctc_nom'            => 'char',
            'fac_regle'          => 'decimal',
            'fac_reste'          => 'decimal',
            //'fac_fichier'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
     * Détail d'une facture
     ******************************/
    public function detail($id)
    {

        // lecture des informations
        $this->db->select("fac_id,fac_reference,fac_numero,fac_date,fac_commande,cmd_reference,cmd_devis,dvi_reference,dvi_correspondant,dvi_client,dvi_societe_vendeuse,ctc_id,ctc_nom,ctc_adresse,ctc_cp,ctc_ville,scv_nom,cor_nom,cor_id,fac_tva,fac_montant_ht,fac_montant_htnr,fac_montant_tva,fac_montant_ttc,fac_delai_paiement,DATE_ADD(fac_date,INTERVAL fac_delai_paiement DAY) AS fac_date_paiement,fac_regle,fac_reste,fac_type,vtf_type,fac_etat,vef_etat,fac_notes,fac_fichier,fac_reprise", false);
        $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
        $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
        $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
        $this->db->join('v_types_factures', 'vtf_id=fac_type', 'left');
        $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
        $this->db->where('fac_id', $id);
        $this->db->where('fac_inactif is null');
        $q = $this->db->get('t_factures');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Mise à jour d'une facture
     ******************************/
    public function maj($data, $id)
    {
        $q   = $this->db->where('fac_id', $id)->get('t_factures');
        $res = $this->_update('t_factures', $data, $id, 'fac_id');
        return $res;
    }

    /******************************
     * Duplication d'une facture
     ******************************/
    public function dupliquer($id)
    {

        // récupération de la facture actuelle
        $q = $this->db->where('fac_id', $id)
            ->get('t_factures');
        if ($q->num_rows() > 0) {
            $data = $q->row_array();

            // initialisation de la nouvelle facture
            unset($data['fac_id']);
            $data['fac_numero']      = 0;
            $data['fac_date']        = date('Y-m-d H:i:s');
            $data['fac_etat']        = 1;
            $data['fac_fichier']     = '';
            $data['fac_notes']       = '';
            $data['fac_transmise']   = '';
            $data['fac_relance']     = '';
            $data['fac_contentieux'] = '';
            $data['fac_couleur']     = 0;
            $data['fac_id_rappel']   = 0;
            $nouvel_id               = $this->_insert('t_factures', $data);
            if ($nouvel_id !== false) {

                // duplication des articles
                $q = $this->db->where('lif_facture', $id)
                    ->get('t_lignes_factures');
                if ($q->num_rows() > 0) {
                    $lignes = $q->result_array();
                    foreach ($lignes as $l) {
                        unset($l['lif_id']);
                        $l['lif_facture'] = $nouvel_id;
                        $resultat         = $this->_insert('t_lignes_factures', $l);
                        if ($resultat === false) {
                            return $resultat;
                        }
                    }
                }
            }

            // fabrication du pdf
            $this->trigger_lignes_factures($nouvel_id);
            $this->generer_pdf($nouvel_id);
            return $nouvel_id;
        }
        return false;
    }

    /******************************
     * Envoi de la facture par email
     ******************************/
    public function envoyer_email($id)
    {

        $q = $this->db->where('fac_id', $id)
            ->where('fac_inactif is null')
            ->select('t_factures.*,scv_email')
            ->join('t_commandes', 'cmd_id=fac_commande', 'left')
            ->join('t_devis', 'dvi_id=cmd_devis', 'left')
            ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left')
            ->get('t_factures');
        if ($q->num_rows() != 1) {
            throw new MY_Exceptions_NoSuchRecord('Impossible de trouver la facture numéro ' . $id);
        }
        $row = $q->row();

        // récupération du contact et du correspondant
        $q = $this->db->select("dvi_client,dvi_correspondant")
            ->join('t_commandes', 'cmd_id=' . $row->fac_commande, 'left')
            ->join('t_devis', 'dvi_id=cmd_devis', 'left')
            ->get('t_factures');
        if ($q->num_rows() == 0) {
            throw new MY_Exceptions_NoSuchUser('Pas de correspondant trouvé pour la facture ' . $id);
        }

        $row2 = $q->row();

        // récupération de l'adresse email
        $q = $this->db->where('cor_id', $row2->dvi_correspondant)
            ->where('LENGTH(cor_email)>0')
            ->get('t_correspondants');
        if ($q->num_rows() == 1) {
            $email = $q->row()->cor_email;
        } else {
            $q = $this->db->where('ctc_id', $row2->dvi_client)
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
        $q = $this->db->where('mod_nom', 'FACTURE')
            ->get('t_modeles_documents');
        if ($q->num_rows() == 0) {
            throw new MY_Exceptions_NoSuchTemplate("Pas de modèle disponible pour le message email");
        }
        $sujet = $q->row()->mod_sujet;
        $corps = $q->row()->mod_texte;

        $fac_fichier = $this->generer_pdf($id);

        // envoi du mail
        $this->load->library('email');
        $resultat = $this->email->send_one($email, $row->scv_email, $sujet, $corps, $fac_fichier);
        if (!$resultat) {
            return false;
        }

        // enregistrement de la transmission
        $transmise = $row->fac_transmise;
        if ($transmise != '') {
            $transmise .= '<br />';
        }
        $transmise .= date('d/m/Y') . '&nbsp;Mail';
        $data = array(
            'fac_transmise' => $transmise,
        );
        return $this->_update('t_factures', $data, $id, 'fac_id');
    }

    /******************************
     * Marquer "transmise par courrier"
     ******************************/
    public function marquer_transmise($id)
    {

        // enregistrement de la transmission
        $q = $this->db->where('fac_id', $id)
            ->get('t_factures');
        if ($q->num_rows() == 1) {
            $transmise = $q->row()->fac_transmise;
            if ($transmise != '') {
                $transmise .= '<br />';
            }
            $transmise .= date('d/m/Y') . '&nbsp;Courrier';
            $data = array(
                'fac_transmise' => $transmise,
            );
            return $this->_update('t_factures', $data, $id, 'fac_id');
        }
        return false;
    }

    /******************************
     * Liste des factures (actions de relance)
     ******************************/
    public function liste_commande($commande)
    {
        switch ($commande) {
            case 'get':
                $id = $this->input->get('fac_id');
                if (empty($id)) {
                    return array();
                }

                $query = "(SELECT fac_id AS RowID, fac_id, fac_date, fac_tva,
                                ctc_nom, dvi_correspondant, cor_nom, fac_transmise, fac_relance, fac_contentieux,
                                fac_notes, vef_etat, fac_couleur, fac_masque,
                                fac_fichier, fac_reference, scv_nom, dvi_reference, fac_reprise,
                                fac_montant_htnr, fac_montant_ht, fac_montant_tva, fac_montant_ttc, fac_regle,
                                fac_reste, fac_reste AS solde_du,
                                fac_etat,
                                IF( fac_reste>=0.01 AND fac_etat=2, 1, 0) AS due,
                                ipu_facture, ipu_reglements, rgl_references,
                                dvi_client, dvi_id_comptable,
                                IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,
                                cor_email, ctc_email,
                                IF( dvi_client=0, '', COALESCE(cor_email, ctc_email,'')) AS mail,
                                cor_telephone1, ctc_telephone,
                                IF( dvi_client=0, '', COALESCE(cor_telephone1, ctc_telephone,'')) AS telephone,
                                cor_telephone2, ctc_mobile,
                                IF( dvi_client=0, '', COALESCE(cor_telephone2, ctc_mobile,'')) AS portable,
                                '' AS texte_rappel,
                                fac_id_rappel,
                                IF(fac_id_rappel=0, '', fac_rappel) AS fac_rappel
                            FROM  `t_factures`
                            LEFT JOIN  `t_commandes` ON  `cmd_id` =  `fac_commande`
                            LEFT JOIN  `t_devis` ON  `dvi_id` =  `cmd_devis`
                            LEFT JOIN  `t_id_comptable` ON  (`idc_contact` =  `dvi_client` AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
                            LEFT JOIN  `t_contacts` ON  `ctc_id` =  `dvi_client`
                            LEFT JOIN  `t_correspondants` ON  `cor_id` =  `dvi_correspondant`
                            LEFT JOIN  `t_societes_vendeuses` ON  `scv_id` =  `dvi_societe_vendeuse`
                            LEFT JOIN  `v_etats_factures` ON  `vef_id` =  `fac_etat`
                            LEFT JOIN  (
                                        SELECT  `ipu_facture` ,
                                                GROUP_CONCAT(`ipu_reglement` SEPARATOR ':') AS ipu_reglements,
                                                GROUP_CONCAT(`rgl_reference` SEPARATOR ':') AS rgl_references
                                        FROM  `t_imputations`
                                        LEFT JOIN  `t_reglements` ON  `rgl_id` =  `ipu_reglement`
                                        WHERE  `ipu_inactif` IS NULL
                                        GROUP BY  `ipu_facture`
                                        ORDER BY  `ipu_facture`
                                    ) AS REGLEMENTS ON fac_id = ipu_facture
                            WHERE  `fac_id` = $id
                            ORDER BY  `fac_date` DESC ,  `fac_reference` DESC )";

                $q = $this->db->query($query);
                if ($q->num_rows() == 1) {
                    return $q->result();
                } else {
                    return array();
                }

                break;
            case 'update':
                $date_rappel = new DateTime($this->input->get('fac_rappel'));
                $data        = array(
                    'fac_couleur'     => $this->input->get('fac_couleur'),
                    'fac_masque'      => $this->input->get('fac_masque'),
                    'fac_relance'     => $this->input->get('fac_relance'),
                    'fac_rappel'      => $date_rappel->format('Y-m-d H:i:s'),
                    'fac_id_rappel'   => $this->input->get('fac_id_rappel'),
                    'fac_notes'       => $this->input->get('fac_notes'),
                    'fac_contentieux' => $this->input->get('fac_contentieux'),
                );
                $id  = $this->input->get('fac_id');
                $res = $this->_update('t_factures', $data, $id, 'fac_id');
                if ($res !== false) {
                    return 1;
                }
                return 0;
                break;
            case 'demasque_all':
                $res = $this->db->query('UPDATE `t_factures` SET fac_masque=0 WHERE `fac_inactif` IS NULL AND  `fac_masque` > 0 ');
                if ($res !== false) {
                    return 1;
                }
                return 0;
                break;
            default:
                return false;
        }
    }

    /******************************
     * Liste des factures
     ******************************/
    public function liste_par_client2($pere)
    {

        // lecture des informations
        $this->db->select("fac_id,fac_numero,fac_reference,fac_date,fac_tva,fac_delai_paiement,"
            . "DATE_ADD(fac_date,INTERVAL fac_delai_paiement DAY) AS fac_date_paiement,fac_type,vtf_type,fac_etat,vef_etat,"
            . "fac_montant_ht,fac_montant_ttc,fac_reste,dvi_societe_vendeuse,"
            . "fac_commande,cmd_reference,cmd_devis,dvi_reference,dvi_client,ctc_nom,fac_fichier", false);
        $this->db->join('v_types_factures', 'vtf_id=fac_type', 'left');
        $this->db->join('v_etats_factures', 'vef_id=fac_etat', 'left');
        $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
        $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->where("dvi_client", $pere);
        $this->db->where('fac_inactif is null');
        $this->db->order_by("fac_numero asc");
        $q = $this->db->get('t_factures');
        return $q->result();
    }

    /******************************
     * Envoi d'une relance
     ******************************/
    public function relance($sous_type, $fac_id)
    {
        $aujourdhui = date('d/m/Y');
        $resultat   = array();
        $type       = substr($sous_type, 0, 2);

        // décodage du type de relance
        if (substr($sous_type, 2, 1) == 'A') {
            $this->db->where('mod_nom', $type . 'M')
                ->or_where('mod_nom', $type . 'L');
        } else {
            $this->db->where('mod_nom', $sous_type);
        }

        // récupération du modèle de document
        $q = $this->db->get('t_modeles_documents');
        if ($q->num_rows() > 0) {

            // récupération des informations de la facture
            $this->db->select("fac_id,fac_date,fac_tva,ctc_nom,cor_nom,cor_email,fac_transmise,"
                . "IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,"
                . "fac_relance,fac_contentieux,fac_notes,cor_telephone1,cor_telephone2,ctc_email,ctc_telephone,ctc_mobile,"
                . "fac_montant_ht AS total_HT,fac_montant_ttc AS total_TTC,fac_reste AS solde_du,"
                . "ctc_adresse,ctc_cp,ctc_ville,ctc_complement,fac_reprise,"
                . "vcv_civilite,cor_prenom,cor_nom,cor_adresse,cor_cp,cor_ville,cor_complement,fac_reference", false);
            $this->db->join('t_commandes', 'cmd_id=fac_commande', 'left');
            $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
            $this->db->join('t_id_comptable', 'idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse', 'left');
            $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
            $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
            $this->db->join('v_civilites', 'vcv_id=cor_civilite', 'left');
            $this->db->where('fac_id', $fac_id);
            $q2 = $this->db->get('t_factures');
            if ($q2->num_rows() > 0) {
                $v       = $q2->row();
                $v->mail = ($v->cor_email != '') ? $v->cor_email : $v->ctc_email;
                if ($v->cor_adresse != '') {
                    $v->adresse    = $v->cor_adresse;
                    $v->cp         = $v->cor_cp;
                    $v->ville      = $v->cor_ville;
                    $v->complement = $v->cor_complement;
                } else {
                    $v->adresse    = $v->ctc_adresse;
                    $v->cp         = $v->ctc_cp;
                    $v->ville      = $v->ctc_ville;
                    $v->complement = $v->ctc_complement;
                }

                // génération du document à partir du modèle
                $this->load->model('m_modeles_documents');
                $fichier = substr($type, 0, 2) . '_' . $v->ctc_nom . '_' . $v->fac_reference . '_' . date('Ymd_His') . '_' . $this->session->id;
                foreach ($q->result() as $modele) {
                    switch ($modele->mod_type) {
                        case 1: // modèle Word
                            $nom = $fichier;
                            $res = $this->m_modeles_documents->fusion_word_pdf($modele->mod_fichier, $nom, $v);
                            if ($res !== false) {
                                $lien_doc   = '<a href="' . base_url($res[0]) . '" target="_blank">Doc</a>';
                                $lien_pdf   = '<a href="' . base_url($res[1]) . '" target="_blank">Pdf</a>';
                                $resultat[] = "<tr><td>$aujourdhui</td><td>$type</td><td>$lien_doc</td><td>$lien_pdf</td></tr>";
                            }
                            break;
                        case 2: // modèle texte TODO sujet de l'email
                            if ($v->mail == '') {
                                continue;
                            }

                            $nom = $fichier . '.txt';
                            $res = $this->m_modeles_documents->fusion_texte($modele->mod_texte, $nom, $v, $v->mail, $modele->mod_sujet);
                            if ($res) {
                                $resultat[] = "<tr><td>$aujourdhui</td><td>$type</td><td>Mail</td><td>&nbsp;</td>";
                            }

                            break;
                    }
                }
            }
        }
        if (count($resultat) == 0) {
            $resultat[0] = false;
        }

        return implode('', $resultat);
    }

    /******************************
     * Transférer une facture en avoir (en réalité : création d'un avoir à partir d'une facture)
     ******************************/
    public function transferer_avoir($id)
    {
        $id = intval($id, 10);

        // récupération de la facture actuelle
        $facture = $this->detail($id);

        // création de l'avoir
        if (!isset($facture)) {
            return false;
        }
        $data = array(
            'avr_numero'           => 0,
            'avr_date'             => date('Y-m-d'),
            'avr_montant_ttc'      => $facture->fac_montant_ttc,
            'avr_montant_ht'       => $facture->fac_montant_ht,
            'avr_montant_htnr'     => $facture->fac_montant_htnr,
            'avr_montant_tva'      => $facture->fac_montant_tva,
            'avr_tva'              => $facture->fac_tva,
            'avr_type'             => 1,
            'avr_etat'             => 1,
            'avr_justification'    => '',
            'avr_facture'          => $id,
            'avr_societe_vendeuse' => $facture->dvi_societe_vendeuse,
            'avr_correspondant'    => $facture->dvi_correspondant,
            'avr_client'           => $facture->dvi_client,
        );
        $avoir_id = $this->_insert('t_avoirs', $data);

        // Copy line items
        $sql = '
INSERT INTO `t_lignes_avoirs` (`lia_code`, `lia_prix`, `lia_quantite`, `lia_description`, `lia_remise_taux`, `lia_remise_ht`, `lia_remise_ttc`, `lia_inactif`, `lia_avoir`)
SELECT `lif_code`, `lif_prix`, `lif_quantite`, `lif_description`, `lif_remise_taux`, `lif_remise_ht`, `lif_remise_ttc`, `lif_inactif`, ' . $avoir_id . '
FROM `t_lignes_factures`
WHERE `lif_facture` = ' . $id;

        $this->db->query($sql);

        $this->trigger_avoirs($id);
        return $avoir_id;
    }

    /******************************
     * Validation de la facture - génération du pdf
     ******************************/
    public function valider($id)
    {
        $data = array(
            'fac_etat' => 2,
        );
        return $this->_update('t_factures', $data, $id, 'fac_id');
    }

    /******************************
     * Returns PDF file path for the facture
     *
     * If the PDF file does not already exist, it will attempt to generate it.
     *
     * @param int $id Facture id
     * @return array|boolean Associative array with indexes on success, FALSE otherwise.
     * Indexes of array:
     * <ul>
     *  <li>path: (string) Path to PDF file</li>
     *  <li>created: (boolean) Whether the PDF was created by this call</li>
     * </ul>
     * @throws MY_Exceptions_NoSuchRecord in case there is no such facture record
     * @throws MY_Exceptions_NoSuchTemplate in case there is no template to generate the PDF
     ******************************/
    public function pdf($id)
    {
        $valeurs = $this->detail($id);
        if (!$valeurs) {
            throw new MY_Exceptions_NoSuchRecord('Impossible de trouver la facture ' . $id);
        }
        /*if ($valeurs->fac_fichier && file_exists($valeurs->fac_fichier)) {
        return array(
        'path'    => $valeurs->fac_fichier,
        'created' => false,
        );
        }*/
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
     * Génération de la facture en pdf
     ******************************/
    public function generer_pdf($id, $pdf = true)
    {

        // récupération des informations de la facture
        $q = $this->db->where('fac_id', $id)
            ->select('t_factures.*,t_societes_vendeuses.*,t_contacts.*,dvi_id_comptable,idc_id_comptable')
            ->join('t_commandes', 'cmd_id=fac_commande', 'left')
            ->join('t_devis', 'dvi_id=cmd_devis', 'left')
            ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left')
            ->join('t_contacts', 'ctc_id=dvi_client', 'left')
            ->join('t_id_comptable', 'idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse', 'left')
            ->get('t_factures');
        if ($q->num_rows() != 1) {
            throw new MY_Exceptions_NoSuchRecord('Could not find facture with id ' . $id);
        }
        $facture = $q->row();

        // Sélection du bon id comptable
        if ($facture->dvi_id_comptable > 0) {
            $facture->ctc_id_comptable = $facture->dvi_id_comptable;
        } else {
            $facture->ctc_id_comptable = $facture->idc_id_comptable;
        }

        // récupération du détail de la facture
        $q = $this->db->where('lif_facture', $id)
            ->where('lif_inactif is null')
            ->get('t_lignes_factures');
        $lignes = array();
        if ($q->num_rows() > 0) {
            $lignes = $q->result();
        }

        // génération du html
        $this->load->helper('view');
        $modele = '_modeles/' . $facture->scv_modele_facture;
        if (!view_exists($modele)) {
            throw new MY_Exceptions_NoSuchTemplate('Could not load view file ' . $modele);
        }
        $html = $this->load->view($modele, array('facture' => $facture, 'lignes' => $lignes), true);
        if ($pdf) {

            // génération du pdf
            $this->load->library('pdf');
            $uniqid = uniqid();
            $chemin = "fichiers/factures/facture-_$facture->scv_id-$uniqid-$facture->fac_numero.pdf";
            $this->pdf->creation($html, $chemin, $facture->scv_en_production);

            // mémorisation du pdf
            $this->_update('t_factures', array('fac_fichier' => $chemin), $id, 'fac_id');

            return $chemin;
        }
        return $html;
    }

    /******************************
     * Suppression d'une facture
     ******************************/
    public function suppression($id)
    {

        // récupération des informations sur le numéro
        $q = $this->db->select("scv_id,scv_no_facture,fac_etat,fac_numero")
            ->join('t_commandes', 'cmd_id=fac_commande')
            ->join('t_devis', 'dvi_id=cmd_devis')
            ->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse')
            ->where('fac_id', $id)
            ->get('t_factures');
        if ($q->num_rows() > 0) {
            $f = $q->row();
            if ($f->scv_no_facture == $f->fac_numero and $f->fac_etat == 1) {
                $no_facture = $f->scv_no_facture - 1;

                // suppression et ajustement du numéro de dernière facture
                $this->db->trans_start();
                $this->_delete('t_factures', $id, 'fac_id', 'fac_inactif');
                $this->_update('t_societes_vendeuses', array('scv_no_facture' => $no_facture), $f->scv_id, 'scv_id');
                $this->db->trans_complete();
                if ($this->db->trans_status() === false) {
                    return false;
                }

                // Attention : il y a un risque qu'une autre facture ait été créée entre temps !
                return true;
            }
        }
        return false;
    }

    /******************************
     * Trigger avoirs
     ******************************/
    public function trigger_avoirs($id)
    {

        // contrôle de l'existence d'un avoir associé
        $nb_avoirs = $this->db->where('avr_facture', $id)
            ->where('avr_inactif is null')
            ->from('t_avoirs')
            ->count_all_results();
        if ($nb_avoirs > 0) {
            // fac_etat : 9 = Transféré en avoir (voir table v_etats_factures)
            $this->db->where('fac_id', $id)
                ->update('t_factures', array('fac_etat' => 9));
        } else {
            // fac_etat : 2 = Validée (voir table v_etats_factures)
            $this->db->where('fac_id', $id)
                ->update('t_factures', array('fac_etat' => 2));
        }
    }

    /******************************
     * Trigger imputations
     ******************************/
    public function trigger_imputations($id)
    {

        // récupération du montant TTC de la facture
        $q = $this->db->select('fac_montant_ttc,fac_reprise')
            ->where('fac_id', $id)
            ->get('t_factures');
        $montant_ttc = $q->row()->fac_montant_ttc;
        $reprise     = $q->row()->fac_reprise;

        // récupération des règlements imputés
        //if ($reprise == 1) {
        //    $regle = $montant_ttc;
        //    $reste = 0;
        //}
        //else {
        $q = $this->db->select_sum("ipu_montant")
            ->where('ipu_inactif is null')
            ->where('ipu_facture', $id)
            ->get('t_imputations');
        if ($q->num_rows() > 0) {
            $reglements = $q->row()->ipu_montant;
            $regle      = $reglements;
            $reste      = round($montant_ttc - $reglements, 2);
        } else {
            $regle = 0;
            $reste = $montant_ttc;
        }
        //}
        $data = array(
            'fac_regle' => $regle,
            'fac_reste' => $reste,
        );
        $this->db->where('fac_id', $id)
            ->update('t_factures', $data);

        $records = array(
            'updated' => array(
                'm_factures' => array($id),
            ),
        );

        return $records;
    }

    /******************************
     * Trigger lignes de factures
     ******************************/
    public function trigger_lignes_factures($id)
    {

        // récupération du taux de TVA
        $q = $this->db->select('fac_tva')
            ->where('fac_id', $id)
            ->get('t_factures');
        $tva = $q->row()->fac_tva;

        // récupération des lignes de la facture
        $q = $this->db->select("lif_code,lif_prix,lif_quantite,lif_remise_ht")
            ->where('lif_inactif is null')
            ->where('lif_facture', $id)
            ->get('t_lignes_factures');
        if ($q->num_rows() > 0) {
            $lignes = $q->result();

            // calcul des montants HT et TTC
            $ht     = 0;
            $remise = 0;
            foreach ($lignes as $l) {
                if ($l->lif_code == 'R') {
                    $remise += $l->lif_prix;
                } else {
                    $ht += $l->lif_prix * $l->lif_quantite - $l->lif_remise_ht;
                }
            }
            $montant_htnr = $ht;
            $ht           = $ht * (1 - $remise);
            $tva          = $ht * $tva;
            $ttc          = $ht + $tva;
            $montant_ht   = $ht;
            $montant_tva  = $tva;
            $montant_ttc  = $ttc;
        } else {
            $montant_htnr = 0;
            $montant_ht   = 0;
            $montant_tva  = 0;
            $montant_ttc  = 0;
        }
        $data = array(
            'fac_montant_htnr' => $montant_htnr,
            'fac_montant_ht'   => $montant_ht,
            'fac_montant_tva'  => $montant_tva,
            'fac_montant_ttc'  => $montant_ttc,
            'fac_regle'        => 0,
            'fac_reste'        => $montant_ttc,
        );
        $this->db->where('fac_id', $id)
            ->update('t_factures', $data);
    }

    public function liste_types_factures()
    {
        $q = $this->db->select('vtf_id AS id, vtf_type AS value')
            ->get('v_types_factures');

        return (array) $q->result();
    }

    /**
     * @param integer $enseigne
     *
     * @return integer
     */
    public function plus_grand_numero($enseigne)
    {
        $row = $this->db->select('MAX(fac_numero) AS max_fac_numero')
            ->join('t_commandes', 'fac_commande = cmd_id')
            ->join('t_devis', 'cmd_devis = dvi_id')
            ->where('dvi_societe_vendeuse', $enseigne)
            ->where('fac_inactif IS NULL')
            ->where('dvi_inactif IS NULL')
            ->where('cmd_inactif IS NULL')
            ->get('t_factures')
            ->row();
        return intval($row->max_fac_numero, 10);
    }
}

// EOF