<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_imputations extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('rgl_reference','ref',"Règlement",'reglements','ipu_reglement','rgl_reference'),
                array('ctc_nom','ref',"Client",'contacts','rgl_client','ctc_nom'),
                array('ipu_montant','number',"Montant"),
                array('fac_reference','ref',"Facture",'factures','ipu_facture','fac_reference'),
                array('avr_reference','ref',"Avoir",'avoirs','ipu_avoir','avr_reference'),
                array('pep_id','ref',"Profits et pertes",'profits_et_pertes','ipu_profits','pep_id'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               
            )
        );

        return $champs[$type];
    }


    /******************************
    * Liste des imputations
    ******************************/
    public function liste($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $ipu_montant = formatte_sql_lien('imputations/detail','ipu_id','ipu_montant');
        $rgl_reference = formatte_sql_lien('reglements/detail','rgl_id','rgl_reference');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $fac_reference = formatte_sql_lien('factures/detail','fac_id','fac_reference');
        $avr_reference = formatte_sql_lien('avoirs/detail','avr_id','avr_reference');
        $pep_id = formatte_sql_lien('profits_et_pertes/detail','pep_id','pep_id');
        $this->db->select("ipu_id AS RowID,ipu_id,ipu_facture,ipu_avoir,ipu_profits,$ipu_montant,$rgl_reference,$ctc_nom,$fac_reference,$avr_reference,$pep_id",false);
        $this->db->join('t_reglements','rgl_id=ipu_reglement','left');
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->join('t_factures','fac_id=ipu_facture','left');
        $this->db->join('t_avoirs','avr_id=ipu_avoir','left');
        $this->db->join('t_profits_et_pertes','pep_id=ipu_profits','left');
        $this->db->where("ipu_reglement",$pere);
        $this->db->where('ipu_inactif is null');
        //$this->db->order_by("ipu_facture asc");
        $this->db->stop_cache();

        $table = 't_imputations';

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
            'rgl_reference'=>'char',
            'ctc_nom'=>'char',
            'ipu_montant'=>'decimal',
            'fac_reference'=>'char',
            'avr_reference'=>'char',
            'pep_id'=>'int'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'une imputation
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("ipu_id,ipu_reglement,rgl_reference,rgl_client,ctc_nom,ipu_montant,ipu_facture,fac_reference,ipu_avoir,avr_reference,ipu_profits,pep_id",false);
        $this->db->join('t_reglements','rgl_id=ipu_reglement','left');
        $this->db->join('t_contacts','ctc_id=rgl_client','left');
        $this->db->join('t_factures','fac_id=ipu_facture','left');
        $this->db->join('t_avoirs','avr_id=ipu_avoir','left');
        $this->db->join('t_profits_et_pertes','pep_id=ipu_profits','left');
        $this->db->where('ipu_id',$id);
        $this->db->where('ipu_inactif is null');
        $q = $this->db->get('t_imputations');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

}
// EOF
