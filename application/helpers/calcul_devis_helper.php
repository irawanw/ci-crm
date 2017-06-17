<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 09/08/15
 * Time: 22:07
 */

/******************************
 * Calcule les prix totaux des devis
 ******************************/
if ( ! function_exists('calcul_devis')) {
    function calcul_devis($data) {
        $CI =& get_instance();

        // récupération des articles du devis
        $q = $CI->db->select("art_code,ard_prix,ard_quantite,ard_remise_ht")
            ->join('t_articles','art_id=ard_article','left')
            ->where('ard_inactif is null')
            ->where('ard_devis',$data->dvi_id)
            ->get('t_articles_devis');
        if ($q->num_rows() > 0) {
            $articles = $q->result();

            // calcul des montants HT et TTC
            $ht = 0;
            $remise = 0;
            foreach($articles as $a) {
                if ($a->art_code == 'R') {
                    $remise +=  $a->ard_prix;
                    /* Replaced by MySQL Query:
                            SELECT  `ard_devis`,  
                                    SUM(  `ard_prix` ) AS REMISE
                            FROM  `t_articles_devis` 
                            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article` 
                            WHERE  `ard_inactif` IS NULL 
                              AND  `art_code` =  'R'
                            GROUP BY  `ard_devis` 
                        
                     **All rows:
                            SELECT  `ard_devis` ,  `art_code` ,  `ard_prix` 
                            FROM  `t_articles_devis` 
                            LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article` 
                            WHERE  `ard_inactif` IS NULL 
                            AND  `art_code` =  'R'
                    */
                }
                else {
                    $ht += $a->ard_prix * $a->ard_quantite - $a->ard_remise_ht;
                    /* Replaced by MySQL Query:
                            SELECT `ard_devis`, 
                                    SUM(`ard_prix` *  `ard_quantite` -  `ard_remise_ht`) AS HT
                            FROM `t_articles_devis` 
                                LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article` 
                            WHERE `ard_inactif` IS NULL 
                              AND (`art_code` IS NULL OR `art_code`<>'R')
                            GROUP BY `ard_devis`
                            
                     **All rows:
                            SELECT `ard_devis`, `art_code`, `ard_prix`, `ard_quantite`, `ard_remise_ht` , 
                                    (`ard_prix` *  `ard_quantite` -  `ard_remise_ht`) AS HT
                            FROM `t_articles_devis` 
                                LEFT JOIN  `t_articles` ON  `art_id` =  `ard_article` 
                            WHERE `ard_inactif` IS NULL 
                              AND (`art_code` IS NULL OR `art_code`<>'R')
                    */
                }
            }
            /* Replaced by MySQL Query:
                    SELECT  TT.`dvi_id` AS CALCUL_dvi_id,
                            TT.tva_taux AS tva_taux, 
                    --php:  $data->dvi_montant_htnr = $ht;
                            TT.HT AS dvi_montant_htnr, 
                    --php:  $ht = $ht * (1 - $remise);
                            (TT.HT * (1-TT.REMISE)) AS HT, 
                    --php:  $ttc = $ht * (1 + $data->dvi_tva);
                            (TT.HT * (1-TT.REMISE))*(1+TT.tva_taux) AS TTC, 
                    --php:  $data->dvi_montant_ht = $ht;
                            (TT.HT * (1-TT.REMISE)) AS dvi_montant_ht, 
                    --php:  $data->dvi_montant_ttc = $ttc;
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
            
            
             **All required data:
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
                    WHERE T.`dvi_inactif` IS NULL          
                    
            */            
            $data->dvi_montant_htnr = $ht;
            $ht = $ht * (1 - $remise);
            $ttc = $ht * (1 + $data->dvi_tva);
            $data->dvi_montant_ht = $ht;
            $data->dvi_montant_ttc = $ttc;
        }
        else {
            $data->dvi_montant_htnr = 0;
            $data->dvi_montant_ht = 0;
            $data->dvi_montant_ttc = 0;
        }
        //log_message('DEBUG', 'calcul_devis: '.json_encode($data));
        return $data;
    }
}
