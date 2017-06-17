<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_demande_devis_commerciaux_dt extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->filterable_columns = array(          
            //'utl_login'                => 'char',    
            // 'ctc_date_creation'        => 'datetime',                   
        );
    }

    /******************************
     * Liste des contacts
     ******************************/
    public function liste($id = 0, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {       
        $where = "WHERE utl_inactif IS NULL";      
        //custom filter        
        $arrCond = array(
            'week'   => 'date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day)',
            'month'  => 'month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );

        if(is_array($filters)) {         
            if(array_key_exists('filter_commercial', $filters)) {        
                $filter_commercial = $filters['filter_commercial']['type'];
                if(is_array($filter_commercial)) {
                    $commercial_ids = "(";
                    $commercial_ids .= implode(",", $filter_commercial);
                    $commercial_ids .= ")";

                    $where .= " AND utl_id IN ".$commercial_ids;
                }

                unset($filters['filter-commercial']);
            }

            if(array_key_exists('filter_rangedate', $filters)) {
                $filter_date = $filters['filter_rangedate']['type'] == "all" ? "" : $filters['filter_rangedate']['type'];

                if($filter_date != "") {
                    $condDate = $arrCond[$filter_date];
                    $where .= " AND $condDate";
                }
            }
        }
     
    	$this->db->start_cache();      

        $table = "(SELECT utl_id AS RowID,
			utl_login,
			if(UNIX_TIMESTAMP(ctc_date_creation), ctc_date_creation, null) as ctc_date_creation,
			count(distinct if(origine_group=2,ctc_id, null)) as nombre_adwords,
            count(distinct if(origine_group=2 AND (fac_id != '' OR ctc_signe = 1),ctc_id, null)) as nombre_signe_adwords,
            '' as pourcentage_signe_adwords,
            count(distinct if(origine_group=1,ctc_id, null)) as nombre_emailings,
            count(distinct if(origine_group=1 AND (fac_id != '' OR ctc_signe = 1),ctc_id, null)) as nombre_signes_emailings,
            '' as pourcentage_signe_emailings,
            count(distinct if(origine_group NOT IN(1,2),ctc_id, null)) as nombre_autre_origines,
            count(distinct if(origine_group NOT IN(1,2) AND (fac_id != '' OR ctc_signe = 1),ctc_id, null)) as nombre_signe_autre_origines,
            '' as pourcentage_signe_autre_origines,
            SUM(fac_montant_ht) as ca,
			count(distinct if(ctc_signe= 1,ctc_id, null)) as manual_signe,
            count(distinct if(ctc_statistiques= 1,ctc_id, null)) as total_demande,
            '' as total_signe,
            '' as total_ca       
        	FROM `t_utilisateurs` 
            LEFT JOIN `t_contacts` ON `ctc_commercial_charge` = `utl_id` AND `ctc_statistiques` = 1
        	LEFT JOIN `t_devis` ON `dvi_client`=`ctc_id` 
        	LEFT JOIN `t_commandes` ON `cmd_devis`=`dvi_id` 
        	LEFT JOIN `t_factures` ON `fac_commande`=`cmd_id` AND 
											`fac_etat` = 2 AND
											(`fac_inactif` IS NULL OR `fac_inactif` = '0000-00-00 00:00:00')
            LEFT JOIN `v_types_origine_prospect` `top` ON `top`.`origine_id` = `ctc_origine` 
            LEFT JOIN `v_types_origine_generale` ON `generale_id`=`origine_group`
            ".$where."
            GROUP BY `utl_id`)";
  
        $this->db->stop_cache();

        $aliases = array(
        	
        );
       
        $resultat = $this->_filtre($table, $this->get_filterable_columns_custom(), $aliases, $limit, $offset, $filters, $ordercol, $ordering,true);
        $this->db->flush_cache();

        //add checkbox into data
        $data = $resultat['data'];
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->checkbox     = '<input type="checkbox" name="ids[]" value="' . $data[$i]->RowID . '">';             
            //count percentage adwords signe
            if($data[$i]->nombre_adwords != 0) {
                $pourcentage_signe_adwords = ($data[$i]->nombre_signe_adwords / $data[$i]->nombre_adwords) * 100;
                $data[$i]->pourcentage_signe_adwords = $pourcentage_signe_adwords.'%';
            } else {
                $pourcentage_signe_adwords = $data[$i]->nombre_signe_adwords * 100;
                $data[$i]->pourcentage_signe_adwords = $pourcentage_signe_adwords.'%';
            }
            //count percentage emailings signe
            if($data[$i]->nombre_emailings != 0) {
                $pourcentage_signe_emailings = ($data[$i]->nombre_signes_emailings / $data[$i]->nombre_emailings) * 100;
                $data[$i]->pourcentage_signe_emailings = $pourcentage_signe_emailings.'%';
            } else {
                $pourcentage_signe_emailings = $data[$i]->nombre_signes_emailings * 100;
                $data[$i]->pourcentage_signe_emailings = $pourcentage_signe_emailings.'%';
            }
            //count percentage autre origine signe
            if($data[$i]->nombre_autre_origines != 0) {
                $pourcentage_signe_autre_origines = ($data[$i]->nombre_signe_autre_origines / $data[$i]->nombre_autre_origines) * 100;
                $data[$i]->pourcentage_signe_autre_origines = $pourcentage_signe_autre_origines.'%';
            } else {
                $pourcentage_signe_autre_origines = $data[$i]->nombre_signe_autre_origines * 100;
                $data[$i]->pourcentage_signe_autre_origines = $pourcentage_signe_autre_origines.'%';
            }

            $data[$i]->total_signe = $data[$i]->nombre_signe_adwords + 
									$data[$i]->nombre_signes_emailings + 
									$data[$i]->nombre_signe_autre_origines;
        }

        $resultat['data'] = $data;

        return $resultat;
    }

    public function get_filterable_columns() {
        return $this->filterable_columns;
    }

    public function get_filterable_columns_custom()
    {
        $filters = $this->filterable_columns;
        $filters['ctc_date_creation'] = 'datetime';
        return $filters;
    }   
}

// EOF
