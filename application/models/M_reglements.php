<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_reglements extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->filterable_columns = array(
            'rgl_reference'=>'char',
            'rgl_date'=>'date',
            'rgl_montant'=>'decimal',
            'vtr_type'=>'select',
            'rgl_cheque'=>'char',
            'rgl_banque'=>'char',
            //'pieces'=>'char',
            'ctc_nom'=>'char',
            'avr_references'=>'char',
            'fac_references'=>'char'
        );
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('RowID','text','__DT_Row_ID', 'invisible'),
                array('rgl_reference','href',"Référence", 'reglements/detail/', 'rgl_id'),
                array('rgl_date','date',"Date règlement"),
                array('rgl_montant','currency',"Montant payé"),
                array('vtr_type','select',"Type de règlement"),
                array('rgl_cheque','text',"Numéro de chèque"),
                array('rgl_banque','text',"Banque"),
                //array('pieces','text',"Pièces"),
                array('fac_references','hreflist','Factures', '*factures/detail2/', 'ipu_factures'),
                array('avr_references','hreflist','Avoirs', '*avoirs/detail2/', 'ipu_avoirs'),
                array('ctc_nom','href',"Client", 'contacts/detail/', 'rgl_client')
            ),
            'write' => array(
                
            )
        );

        return $champs[$type];
    }
    
    /******************************
    * Liste filterable columns
    ******************************/
    public function get_filterable_columns() {
        return $this->filterable_columns;
    }

    /******************************
    * Liste des règlements
    ******************************/
    public function liste_par_client($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $rgl_reference = formatte_sql_lien('reglements/detail','rgl_id','rgl_reference');
        $rgl_date = formatte_sql_date('rgl_date');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("rgl_id AS RowID,rgl_id,$rgl_reference,$rgl_date,rgl_montant,vtr_type,rgl_cheque,rgl_banque,$ctc_nom",false);
        $this->db->join('v_types_reglements','vtr_id=rgl_type','left');
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->where("rgl_client",$pere);
        $this->db->where('rgl_inactif is null');
        //$this->db->order_by("rgl_date asc");
        $this->db->stop_cache();

        $table = 't_reglements';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_client_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_client_filterable_columns() {
    $filterable_columns = array(
            'rgl_reference'=>'char',
            'rgl_date'=>'date',
            'rgl_montant'=>'decimal',
            'vtr_type'=>'char',
            'rgl_cheque'=>'char',
            'rgl_banque'=>'char',
            'ctc_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un règlement
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("rgl_id,rgl_reference,rgl_date,rgl_client,ctc_nom,rgl_type,vtr_type,rgl_montant,rgl_banque,rgl_cheque",false);
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->join('v_types_reglements','vtr_id=rgl_type','left');
        $this->db->where('rgl_id',$id);
        $this->db->where('rgl_inactif is null');
        $q = $this->db->get('t_reglements');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un règlement
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('rgl_id',$id)->get('t_reglements');
        $res =  $this->_update('t_reglements',$data,$id,'rgl_id');
        return $res;
    }

    /******************************
    * Liste distinct types d'un reglement
    ******************************/
    public function get_types() {

        $q = $this->db->query('SELECT DISTINCT  `rgl_type` ,  `vtr_type` 
                                FROM  `t_reglements` 
                                LEFT JOIN  `v_types_reglements` ON  `vtr_id` =  `rgl_type` ');
        if ($q->num_rows() > 0) {
            $resultat = $q->result();
            return $resultat;
        }
        else {
            return array();
        }
    }    

    
    /******************************
     * Liste des règlements
     ******************************/
    public function liste($void, $limit=0, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
        // première partie du select, mis en cache
        $this->db->start_cache();
        // lecture des informations
        $this->db->select("*",false);
        $this->db->stop_cache();

        $where_id = "";

        if($void) {
            switch($void){
                case 'archived':
                    $where_id = " AND `rgl_archiver` IS NOT NULL ";
                    break;
                case 'deleted':
                    $where_id = " AND `rgl_inactif` IS NOT NULL ";
                    break;
                case 'all':
                    break;
                default:
                    $where_id = " AND `rgl_archiver` IS NOT NULL ";
                    $where_id = " AND `rgl_inactif` IS NOT NULL ";
                    break;
            }
        }

        $id = intval($void);
        if ($id > 0) {
            $where_id = " AND `rgl_id` = ". $id." ";
        }

        $table = "( SELECT rgl_id AS RowID,  
                            rgl_id, 
                            rgl_reference, 
                            rgl_date, 
                            rgl_montant, 
                            rgl_type, 
                            vtr_type, 
                            rgl_cheque, 
                            rgl_banque, 
                            rgl_client, 
                            ctc_nom, 
                            ctc_id,
                            av_ipu_reglement, 
                            ipu_avoirs, avr_references,
                            fac_ipu_reglement, 
                            ipu_factures, fac_references 
                      FROM  `t_reglements` 
                    LEFT JOIN  `v_types_reglements` ON  `vtr_id` =  `rgl_type` 
                    LEFT JOIN  `t_contacts` ON  `ctc_id` =  `rgl_client` 
                    LEFT JOIN (
                        SELECT  `ipu_reglement` AS av_ipu_reglement, 
                                GROUP_CONCAT(  `ipu_avoir` SEPARATOR  ':' ) AS  `ipu_avoirs` , 
                            GROUP_CONCAT(  `avr_reference` SEPARATOR  ':' ) AS  `avr_references` 
                        FROM  `t_imputations` 
                        LEFT JOIN  `t_avoirs` ON  `avr_id` =  `ipu_avoir` 
                        WHERE  `ipu_inactif` IS NULL 
                        AND  `avr_reference` IS NOT NULL 
                        AND  `avr_reference` >  ''
                        GROUP BY  `ipu_reglement` 
                        ORDER BY  `ipu_reglement`
                        ) AS AVOIRS ON  `av_ipu_reglement` =  `rgl_id` 
                    LEFT JOIN (
                        SELECT  `ipu_reglement` AS fac_ipu_reglement, 
                                GROUP_CONCAT(  `ipu_facture` SEPARATOR  ':' ) AS  `ipu_factures`, 
                                GROUP_CONCAT(  `fac_reference` SEPARATOR  ':' ) AS  `fac_references` 
                        FROM `t_imputations` 
                        LEFT JOIN `t_factures` ON `fac_id` = `ipu_facture` 
                        WHERE `ipu_inactif` IS NULL 
                          AND `fac_reference` IS NOT NULL 
                          AND `fac_reference` >  ''
                        GROUP BY `ipu_reglement` 
                        ORDER BY `ipu_reglement`         
                        ) AS FACTURES ON  `fac_ipu_reglement` =  `rgl_id` 
                    WHERE  `rgl_inactif` IS NULL ".$where_id." 
                    ORDER BY `rgl_date` DESC, `rgl_id` DESC 
                    )";

        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table,$this->get_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering, true);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
        } 

        return $resultat;
    }       
    public function liste__() {
        
        /*
        // This query gets it all:
        SELECT rgl_id, rgl_reference, rgl_date, rgl_montant, rgl_type, vtr_type, rgl_cheque, rgl_banque, rgl_client, ctc_nom, 
                `av_ipu_reglement`, `ipu_avoirs` , `avr_references`,
                `fac_ipu_reglement`, `ipu_factures`, `fac_references` 
          FROM  `t_reglements` 
        LEFT JOIN  `v_types_reglements` ON  `vtr_id` =  `rgl_type` 
        LEFT JOIN  `t_contacts` ON  `ctc_id` =  `rgl_client` 
        LEFT JOIN (
            SELECT  `ipu_reglement` AS av_ipu_reglement, 
                    GROUP_CONCAT(  `ipu_avoir` SEPARATOR  ':' ) AS  `ipu_avoirs` , 
                GROUP_CONCAT(  `avr_reference` SEPARATOR  ':' ) AS  `avr_references` 
            FROM  `t_imputations` 
            LEFT JOIN  `t_avoirs` ON  `avr_id` =  `ipu_avoir` 
            WHERE  `ipu_inactif` IS NULL 
            AND  `avr_reference` IS NOT NULL 
            AND  `avr_reference` >  ''
            GROUP BY  `ipu_reglement` 
            ORDER BY  `ipu_reglement`
            ) AS AVOIRS ON  `av_ipu_reglement` =  `rgl_id` 
        LEFT JOIN (
            SELECT  `ipu_reglement` AS fac_ipu_reglement, 
                    GROUP_CONCAT(  `ipu_facture` SEPARATOR  ':' ) AS  `ipu_factures`, 
                    GROUP_CONCAT(  `fac_reference` SEPARATOR  ':' ) AS  `fac_references` 
            FROM `t_imputations` 
            LEFT JOIN `t_factures` ON `fac_id` = `ipu_facture` 
            WHERE `ipu_inactif` IS NULL 
              AND `fac_reference` IS NOT NULL 
              AND `fac_reference` >  ''
            GROUP BY `ipu_reglement` 
            ORDER BY `ipu_reglement`         
            ) AS FACTURES ON  `fac_ipu_reglement` =  `rgl_id` 
        WHERE  `rgl_inactif` IS NULL 
        ORDER BY `rgl_date` DESC, `rgl_id` DESC 
        
        ///////////
        SELECT rgl_id AS RowID, rgl_id, rgl_reference, rgl_date, rgl_montant, rgl_type, 
                                    vtr_type, rgl_cheque, rgl_banque, rgl_client, ctc_nom
                                FROM `t_reglements`
                                LEFT JOIN `v_types_reglements` ON `vtr_id`=`rgl_type`
                                LEFT JOIN `t_contacts` ON `ctc_id`=`rgl_client`
                                WHERE `rgl_inactif` is null
                                ORDER BY `rgl_date` desc, `rgl_id` desc
        
        ////////////
        SELECT  `ipu_reglement` , 
                GROUP_CONCAT(  `ipu_facture` SEPARATOR  ':' ) AS  `ipu_factures`, 
                GROUP_CONCAT(  `fac_reference` SEPARATOR  ':' ) AS  `fac_references` 
        FROM `t_imputations` 
        LEFT JOIN `t_factures` ON `fac_id` = `ipu_facture` 
        WHERE `ipu_inactif` IS NULL 
          AND `fac_reference` IS NOT NULL 
          AND `fac_reference` >  ''
        GROUP BY `ipu_reglement` 
        ORDER BY `ipu_reglement`         
        
        ////////////
        SELECT  `ipu_reglement` , 
        	GROUP_CONCAT(`ipu_avoir` SEPARATOR ':') AS `ipu_avoirs`, 
        	GROUP_CONCAT(`avr_reference` SEPARATOR ':') AS `avr_references`
        FROM  `t_imputations` 
        LEFT JOIN `t_avoirs` ON `avr_id`=`ipu_avoir`
        WHERE  `ipu_inactif` IS NULL 
          AND  `avr_reference` IS NOT NULL
          AND  `avr_reference` > ''
        GROUP BY `ipu_reglement` 
        ORDER BY `ipu_reglement` 
        
        */

        // lecture des informations
        $this->db->select("rgl_id,rgl_reference,rgl_date,rgl_montant,rgl_type,vtr_type,rgl_cheque,rgl_banque,rgl_client,ctc_nom",false);
        $this->db->join('v_types_reglements','vtr_id=rgl_type','left');
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->where('rgl_inactif is null');
        $this->db->order_by("rgl_date desc");
        $this->db->order_by("rgl_id desc");
        $query = $this->db->get_compiled_select('t_reglements');
        //log_message('DEBUG', 'M_reglements '.$query);
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            $result = $q->result();
            foreach($result as $r) {
                $query = $this->db->where('ipu_reglement',$r->rgl_id)
                    ->where('ipu_inactif is null')
                    ->select('ipu_facture,fac_reference,ipu_avoir,avr_reference')
                    ->join('t_factures','fac_id=ipu_facture','left')
                    ->join('t_avoirs','avr_id=ipu_avoir','left')
                    ->get_compiled_select('t_imputations');
                //log_message('DEBUG', 'M_reglements '.$query);
                $q = $this->db->query($query);
                $pieces = '';
                $sep = '';
                foreach ($q->result() as $p) {
                    $pieces .= $sep;
                    if ($p->fac_reference != '') {
                        $pieces .= anchor_popup('factures/detail2/'.$p->ipu_facture,$p->fac_reference);
                    }
                    else if ($p->avr_reference != '') {
                        $pieces .= anchor_popup('avoirs/detail2/'.$p->ipu_avoir,$p->avr_reference);
                    }
                    $sep = '<br />';
                }
                $r->pieces = $pieces;
                if ( $pieces!='' )
                    log_message('DEBUG', 'M_reglements ipu_reglement:'.$r->rgl_id.' pieces:'.$pieces);
            }
            return $result;
        }
        else {
            return array();
        }
    }
    
    /******************************
     * Liste des reglements for DataTables
     ******************************/
    public function liste_chunk($limit=0, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
        
        /*
        // première partie du select, mis en cache
        $this->db->start_cache();
        
        // lecture des informations
        $this->db->select("rgl_id,rgl_id AS RowID,rgl_reference,rgl_date,rgl_montant,rgl_type,vtr_type,rgl_cheque,rgl_banque,rgl_client,ctc_nom",false);
        $this->db->join('v_types_reglements','vtr_id=rgl_type','left');
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->where('rgl_inactif is null');
        $this->db->order_by("rgl_date desc");
        $this->db->order_by("rgl_id desc");
        
        $this->db->stop_cache();

        $table = 't_reglements';

        // aliases
        $aliases = array( );
        
        $resultat = $this->_filtre($table,$this->get_filterable_columns(),$aliases,$limit,$offset,$filters,0,NULL);
        $this->db->flush_cache();
        
        */
        $resultat = array();
        $q = $this->db->query("SELECT rgl_id, rgl_id AS RowID, rgl_reference, rgl_date, rgl_montant, rgl_type, 
                                    vtr_type, rgl_cheque, rgl_banque, rgl_client, ctc_nom
                                FROM `t_reglements`
                                LEFT JOIN `v_types_reglements` ON `vtr_id`=`rgl_type`
                                LEFT JOIN `t_contacts` ON `ctc_id`=`rgl_client`
                                WHERE `rgl_inactif` is null
                                ORDER BY `rgl_date` desc, `rgl_id` desc");
        
        if ( $q->num_rows() > 0 ) {
            $resultat = array("data"=>$q->result(), "recordsTotal"=>$q->num_rows(), "recordsFiltered"=>$q->num_rows(),
                "recordsOffset"=>$offset, "recordsLimit"=>$limit,
                "ordercol"=>"fac_date", "ordering"=>"desc");
            //return $result;
        }
        else {
            $resultat = array("data"=>array(), "recordsTotal"=>$total_rows, "recordsFiltered"=>$filtered_rows, "recordsOffset"=>$offset, "recordsLimit"=>$limit);
            //return $result;
        }
                                
        $resultat2 = array();
        foreach($resultat['data'] as $v) {
            $q = $this->db->where('ipu_reglement',$v->rgl_id)
                ->where('ipu_inactif is null')
                ->select('ipu_facture,fac_reference,ipu_avoir,avr_reference')
                ->join('t_factures','fac_id=ipu_facture','left')
                ->join('t_avoirs','avr_id=ipu_avoir','left')
                ->get('t_imputations');
            $pieces = '';
            $sep = '';
            
            if (count($q->result())>5)
                log_message('DEBUG', 'ABNORMAL ipu_reglement:'.$v->rgl_id.' ['.count($q->result()).']');
            
            foreach ($q->result() as $p) {
                $pieces .= $sep;
                if ($p->fac_reference != '') {
                    $pieces .= anchor_popup('factures/detail2/'.$p->ipu_facture,$p->fac_reference);
                }
                else if ($p->avr_reference != '') {
                    $pieces .= anchor_popup('avoirs/detail2/'.$p->ipu_avoir,$p->avr_reference);
                }
                $sep = '<br />';
            }
            $v->pieces = $pieces;
            $resultat2[] = $v;
        }
        $resultat['data'] = $resultat2; 
        return $resultat;    
    }    

    /******************************
     * Factures et avoirs
     ******************************/
    public function factures_et_avoirs($contact, $enseigne = null) {
        $this->load->model('m_factures');
        $this->load->model('m_avoirs');
        $factures = $this->m_factures->liste_par_client2($contact);
        $avoirs = $this->m_avoirs->liste_par_client2($contact);
        $resultat = array();
        foreach ($factures as $f) {
            if (in_array($f->fac_etat,array(2,9)) && $f->fac_reste > 0
                && (!$enseigne || $enseigne == $f->dvi_societe_vendeuse)) {
                $facture = new stdClass();
                $facture->id = 'F'.$f->fac_id;
                $facture->type = 'Facture';
                $facture->date = $f->fac_date_paiement;
                $facture->montant = $f->fac_montant_ttc;
                $facture->tva = $f->fac_tva;
                $facture->numero = $f->fac_reference;
                $facture->du = $f->fac_reste;
                $resultat[] = $facture;
            }
        }
        foreach ($avoirs as $a) {
            if (in_array($a->avr_etat,array(2,3))
                && (!$enseigne || $enseigne == $a->avr_societe_vendeuse)) {
                $avoir = new stdClass();
                $avoir->id = 'A'.$a->avr_id;
                $avoir->type = 'Avoir';
                $avoir->date = $a->avr_date;
                $avoir->montant = $a->avr_montant_ttc;
                $avoir->tva = $a->avr_tva;
                $avoir->numero = $a->avr_reference;
                $avoir->du = $a->avr_montant_ttc;
                $resultat[] = $avoir;
            }
        }
        return $resultat;
    }

    /******************************
     * Nouveau règlement
     ******************************/
    public function nouveau($data) {
        $montant_regle_saisi = floatval(str_replace(',', '.', $data['rgl_montant']));
        $this->load->model('m_factures');
        $this->load->model('m_avoirs');

        /**
         * @var M_factures $m_factures
         * @var M_avoirs $m_avoirs
         */
        $m_factures = $this->m_factures;
        $m_avoirs = $this->m_avoirs;

        $pieces = explode(',',$data['pieces']);
        unset ($data['pieces']);

        $trop_verse_ttc = floatval(str_replace(',', '.', $data['trop_verse']));
        if ($trop_verse_ttc > 0) {
            $compensation = $data['compensation'];
        }
        unset ($data['trop_verse']);
        unset ($data['compensation']);

        $scv_id = $data['rgl_societe_vendeuse'];
        unset($data['rgl_societe_vendeuse']);
        $ctc_id = $data['rgl_client'];

        // récupération des factures et avoirs concernés par le paiement
        $factures = array();
        $avoirs = array();
        $montant_avoirs = 0.0;
        $montant_factures = 0.0;
        foreach ($pieces as $p) {
            $id = substr($p,1);
            if (substr($p,0,1) == 'F') {
                $facture = $m_factures->detail($id);
                if ($scv_id && $scv_id != $facture->dvi_societe_vendeuse) {
                    throw new MY_Exceptions_AccountingDiscrepancy('Mélange de factures et d\'avoirs de plusieurs enseignes');
                }
                $scv_id = $facture->dvi_societe_vendeuse;
                if ($ctc_id != $facture->dvi_client) {
                    throw new MY_Exceptions_AccountingDiscrepancy('Mélange de factures et d\'avoirs de plusieurs clients');
                }
                $montant_reste = floatval($facture->fac_reste);
                if ($montant_reste > 0) {
                    $factures[] = $facture;
                    $montant_factures += $montant_reste;
                }
            }
            else if (substr($p,0,1) == 'A') {
                $avoir = $m_avoirs->detail($id);
                if ($scv_id && $scv_id != $avoir->avr_societe_vendeuse) {
                    throw new MY_Exceptions_AccountingDiscrepancy('Mélange de factures et d\'avoirs de plusieurs enseignes');
                }
                $scv_id = $avoir->avr_societe_vendeuse;
                if ($ctc_id != $avoir->avr_client) {
                    throw new MY_Exceptions_AccountingDiscrepancy('Mélange de factures et d\'avoirs de plusieurs clients');
                }
                $montant_avoir = floatval($avoir->avr_montant_ttc);
                if ($montant_avoir > 0.0) {
                    $avoirs[] = $avoir;
                    $montant_avoirs += $montant_avoir;
                }
            }
        }
        if ($montant_avoirs > 0.0) {
            if ($montant_regle_saisi > $montant_avoirs) {
                throw new MY_Exceptions_AccountingDiscrepancy("Le montant réglé ne doit pas être plus grand que le total des avoirs sélectionnés");
            }
            if ($montant_factures <= 0.0) {
                throw new MY_Exceptions_AccountingDiscrepancy("Aucun solde dû");
            }
            if ($data['rgl_type'] != 4) {   // rgl_type : 4 = avoirs (voir table v_types_reglements)
                throw new MY_Exceptions_AccountingDiscrepancy("Le type de règlement doit être 'Avoirs' quand des avoirs sont sélectionnés");
            }

        } elseif ($montant_regle_saisi <= 0.0 && $montant_factures <= 0.0) {
            return false;
        }

        // création du règlement et des imputations
        $this->db->trans_start();
        $reglement = $this->_insert('t_reglements', $data);
        $this->_update('t_reglements',array('rgl_reference'=>'R-'.$reglement),$reglement,'rgl_id');
        $montant_avoirs_utilise = 0.0;
        if ($montant_avoirs > 0 || $data['rgl_type'] == 4) {
            // On utilise les avoirs pour payer le solde dû sur les factures sélectionnées.
            $avoirs_utilises = array();
            foreach ($avoirs as $a) {
                // Reste t'il un montant dû ?
                if (round($montant_avoirs_utilise - $montant_factures, 2) >= 0.0) {
                    break;
                }
                $imputation = array(
                    'ipu_montant' => $a->avr_montant_ttc,
                    'ipu_reglement' => $reglement,
                    'ipu_avoir' => $a->avr_id,
                    'ipu_facture' => 0,
                    'ipu_profits' => 0
                );
                $this->_insert('t_imputations', $imputation);
                $this->_update('t_avoirs',array('avr_etat'=>4),$a->avr_id,'avr_id');
                $avoirs_utilises[] = $a;
                $montant_avoirs_utilise += $a->avr_montant_ttc;
            }
            $montant_regle = $montant_avoirs_utilise;
        } else {
            $montant_regle = $montant_regle_saisi;
        }
        $facture_id = 0;
        $montant = $montant_regle;
        foreach ($factures as $f) {
            if ($montant <= 0.004) break;
            $difference = $f->fac_reste - $montant;
            if ($difference > 0.004) { // imputation partielle
                $montant_fact = $montant;
                $montant = 0;
            }
            else {
                $montant_fact = $f->fac_reste;
                $montant -= $montant_fact;
            }
            $imputation = array(
                'ipu_montant' => $montant_fact,
                'ipu_reglement' => $reglement,
                'ipu_avoir' => 0,
                'ipu_facture' => $f->fac_id,
                'ipu_profits' => 0
            );
            $this->_insert('t_imputations', $imputation);
            $m_factures->trigger_imputations($f->fac_id);
            $facture_id = $f->fac_id;
        }

        // écriture de compensation éventuelle
        $trop_verse_ttc = max(0.0, round($montant_regle - $montant_factures, 2));
        if ($trop_verse_ttc > 0.0) {
            if ($compensation == 'avoir') {
                $tva = tva();
                $trop_verse_ht = $trop_verse_ttc / (1 + $tva);
                $trop_verse_tva = $trop_verse_ttc - $trop_verse_ht;
                $data = array(
                    'avr_numero' => 0,
                    'avr_date' => date('Y-m-d'),
                    'avr_societe_vendeuse' => $scv_id,
                    'avr_client' => $ctc_id,
                    'avr_montant_ttc' => $trop_verse_ttc,
                    'avr_montant_ht' => $trop_verse_ht,
                    'avr_montant_tva' => $trop_verse_tva,
                    'avr_tva' => $tva,
                    'avr_justification' => 'Trop perçu',
                    'avr_facture' => $facture_id,
                    'avr_type' => 3,         // Compensation
                    'avr_etat' => 2,         // Validé
                );
                $avoir = $this->_insert('t_avoirs',$data);

                // Insert the matching line item
                $data = array(
                    'lia_avoir' => $avoir,
                    'lia_code' => 'G',
                    'lia_prix' => $trop_verse_ht,
                    'lia_description' => 'Trop perçu',
                    'lia_quantite' => 1,
                );
                $this->_insert('t_lignes_avoirs',$data);

                // Marquer la facture comme transférée en avoir (pour le montant trop perçu)
                $m_factures->trigger_avoirs($facture_id);
                $imputation = array(
                    'ipu_montant' => $trop_verse_ttc,
                    'ipu_reglement' => $reglement,
                    'ipu_avoir' => $avoir,
                    'ipu_facture' => 0,
                    'ipu_profits' => 0
                );
                $this->_insert('t_imputations', $imputation);
            }
            else {
                $data = array(
                    'pep_date' => date('Y-m-d'),
                    'pep_montant' => $trop_verse_ttc,
                    'pep_reglement' => $reglement
                );
                $pep = $this->_insert('t_profits_et_pertes',$data);
                $imputation = array(
                    'ipu_montant' => $trop_verse_ttc,
                    'ipu_reglement' => $reglement,
                    'ipu_avoir' => 0,
                    'ipu_facture' => 0,
                    'ipu_profits' => $pep
                );
                $this->_insert('t_imputations', $imputation);
            }
        }

        $this->db->trans_complete();

        //return $this->db->trans_status();
        return $reglement;
    }

    /******************************
     * Suppression d'un règlement
     *
     * @return array A list of updated, deleted and added record IDs
     ******************************/
    public function suppression($id) {
        $q = $this->db->where('ipu_reglement',$id)
            ->get('t_imputations');
        $this->load->model('m_factures');
        $this->db->trans_start();

        // remise des avoirs à l'état non réglé et suppression de l'imputation
        $records = array(
            'deleted' => array(
                'm_avoirs' => array(),
                'm_imputations' => array(),
                'm_reglements' => array(),
                'm_profits_et_pertes' => array(),
            ),
            'updated' => array(
                'm_avoirs' => array(),
            ),
        );

        foreach($q->result() as $r) {
            if ($r->ipu_avoir > 0) {
                $q1 = $this->db->where('avr_id',$r->ipu_avoir)
                    ->get('t_avoirs');
                if ($q1->row()->avr_type == 3) {

                    // avoir de compensation créé automatiquement : suppression de l'avoir et de ses imputations ultérieures éventuelles
                    $this->_delete('t_avoirs', $r->ipu_avoir, 'avr_id','avr_inactif');
                    $records['deleted']['m_avoirs'][] = $r->ipu_avoir;

                    $q2 = $this->db->where('ipu_avoir',$r->ipu_avoir)
                        ->get('t_imputations');
                    foreach ($q2->result() as $r2) {
                        $this->_delete('t_imputations',$r2->ipu_id,'ipu_id','ipu_inactif');
                        $records['deleted']['m_imputations'][] = $r2->ipu_id;
                    }
                }
                else {

                    // avoir manuel : remise à l'état initial
                    $this->_update('t_avoirs', array('avr_etat' => 2), $r->ipu_avoir, 'avr_id');
                    $records['updated']['m_avoirs'][] = $r->ipu_avoir;
                }
            }
            else {
                $this->_delete('t_profits_et_pertes', $r->ipu_profits, 'pep_id','pep_inactif');
                $records['deleted']['m_profits_et_pertes'][] = $r->ipu_profits;
            }
            $this->_delete('t_imputations',$r->ipu_id,'ipu_id','ipu_inactif');
            $records['deleted']['m_imputations'][] = $r->ipu_id;
            if ($r->ipu_facture > 0) {
                $_records = $this->m_factures->trigger_imputations($r->ipu_facture);
                $records = array_merge($records, $_records);
            }
        }

        // suppression du règlement
        $this->_delete('t_reglements',$id,'rgl_id','rgl_inactif');
        $records['deleted']['m_reglements'][] = $id;

        $this->db->trans_complete();
        $result = $this->db->trans_status();
        return ($result) ? $records : $result;
    }

    public function client_option($ctc_id = 0)
    {
        $ctc_id = filter_var($ctc_id, FILTER_VALIDATE_INT);
        if ($ctc_id > 0) {
            $this->db->where("ctc_id", $ctc_id);
        }

        $this->db->where("ctc_client_prospect=2");
        $this->db->order_by('ctc_nom','ASC');
        $q = $this->db->get('t_contacts');

        return $q->result();
    }

    public function type_option()
    {
        $this->db->order_by('vtr_type','ASC');
        $q = $this->db->get('v_types_reglements');

        return $q->result();
    }

    public function compensation_option()
    {
        $options = array(
            "avoir" => "Transformer en avoir",
            "profits_pertes" => "Profits et pertes"
        );

        $i = 0;
        foreach($options as $id => $value) {
            $val = new stdClass();
            
            $val->id = $id;
            $val->value = $value;
            $result[$i] = $val;

            $i++;
        }

        return $result;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_reglements',$id,'rgl_id','rgl_archiver');
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

}
// EOF
