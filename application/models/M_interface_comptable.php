<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_interface_comptable extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

     /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_factures';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select("fac_id,fac_id as RowID,fac_date,fac_tva,dvi_client,ctc_nom,"
            . "IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,"
            . "fac_montant_ht AS total_HT,fac_montant_ttc AS total_TTC,"
            ."vef_etat,fac_reference,scv_nom,fac_reprise",false);

        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_id_comptable','idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left');
        $this->db->join('v_etats_factures','vef_id=fac_etat','left');
        $this->db->where('fac_inactif is null');
        // $this->db->order_by('fac_date','ASC');
        // $this->db->order_by('fac_reference','ASC');

        $this->db->stop_cache();
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
            'fac_id' => 'int',
            'fac_date' => 'date',
            'fac_tva' => 'int',
            'ctc_id_comptable' => 'char',
            'ctc_nom' => 'char',
            'total_HT' => 'int',
            'total_TTC' => 'int',
            'vef_etat' => 'char',
            'fac_referrence' => 'char',
            'scv_nom' => 'char',
            'fac_reprise' => 'int',
        );

        return $filterable_columns;
    }

    /******************************
     * Statistiques de ventes
     ******************************/
    public function liste_factures() {
        $this->db->select("fac_id,fac_date,fac_tva,dvi_client,ctc_nom,"
            . "IF(dvi_id_comptable > 0, CAST(dvi_id_comptable AS CHAR(30)), idc_id_comptable) AS ctc_id_comptable,"
            . "fac_montant_ht AS total_HT,fac_montant_ttc AS total_TTC,"
            ."vef_etat,fac_reference,scv_nom,fac_reprise",false);
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_id_comptable','idc_contact=dvi_client AND idc_societe_vendeuse=dvi_societe_vendeuse','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left');
        $this->db->join('v_etats_factures','vef_id=fac_etat','left');
        $this->db->where('fac_inactif is null');
        $this->db->order_by('fac_date','ASC');
        $this->db->order_by('fac_reference','ASC');
        $q = $this->db->get('t_factures');
        if ($q->num_rows() > 0) {
            $result = $q->result();

            // remplissage du fichier Excel
            //$this->load->library("ExportExcel");
            //return $this->exportexcel->factures($result);
        }
        else {
            return "";
        }
    }

}
// EOF
