<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 09/08/15
 * Time: 22:07
 */

/******************************
 * Calcule les prix totaux des factures
 ******************************/
if ( ! function_exists('calcul_factures')) {
    function calcul_factures($data) {
        $CI =& get_instance();

        // récupération des lignes de la facture
        $q = $CI->db->select("lif_code,lif_prix,lif_quantite,lif_remise_ht")
            ->where('lif_inactif is null')
            ->where('lif_facture',$data->fac_id)
            ->get('t_lignes_factures');
        if ($q->num_rows() > 0) {
            $lignes = $q->result();

            // calcul des montants HT et TTC
            $ht = 0;
            $remise = 0;
            foreach($lignes as $l) {
                if ($l->lif_code == 'R') {
                    $remise +=  $l->lif_prix;
                }
                else {
                    $ht += $l->lif_prix * $l->lif_quantite - $l->lif_remise_ht;
                }
            }
            $data->fac_montant_htnr = $ht;
            $ht = $ht * (1 - $remise);
            $tva = $ht * $data->fac_tva;
            $ttc = $ht + $tva;
            $data->fac_montant_ht = $ht;
            $data->fac_montant_tva = $tva;
            $data->fac_montant_ttc = $ttc;
        }
        else {
            $data->fac_montant_htnr = 0;
            $data->fac_montant_ht = 0;
            $data->fac_montant_tva = 0;
            $data->fac_montant_ttc = 0;
        }

        // récupération des règlements imputés
        if (isset($data->fac_reprise) AND $data->fac_reprise == 1) {
            $data->fac_regle = $data->fac_montant_ttc;
            $data->fac_reste = 0;
        }
        else {
            $q = $CI->db->select_sum("ipu_montant")
                ->where('ipu_inactif is null')
                ->where('ipu_facture', $data->fac_id)
                ->get('t_imputations');
            if ($q->num_rows() > 0) {
                $reglements = $q->row()->ipu_montant;
                $data->fac_regle = $reglements;
                $data->fac_reste = round($data->fac_montant_ttc - $reglements, 2);
            } else {
                $data->fac_regle = 0;
                $data->fac_reste = $data->fac_montant_ttc;
            }

            // contrôle de l'existence d'un avoir associé
            $nb_avoirs = $CI->db->where('avr_facture', $data->fac_id)
                ->where('avr_inactif is null')
                ->from('t_avoirs')
                ->count_all_results();
            if ($nb_avoirs > 0) {
                $data->fac_etat = 9;
                $data->vef_etat = 'Transférée en avoir';
            }
        }
        return $data;
    }
}
