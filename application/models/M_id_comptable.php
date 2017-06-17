<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_id_comptable extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /******************************
    * Nouvel ID comptable
    ******************************/
    public function nouveau($data) {
        return $this->_insert('t_id_comptable', $data);
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'idc_id_comptable'=>'char',
            'idc_societe_vendeuse'=>'int',
            'idc_contact'=>'int',
            'scv_id'=>'int',
            'ctc_id'=>'int',
            'scv_nom'=>'char',
            'ctc_nom'=>'char',
        );
        return $filterable_columns;
    }

    public function liste($limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $this->db->select('idc_id AS RowID,idc_id_comptable,idc_contact,idc_societe_vendeuse,scv_id,scv_nom,ctc_nom')
            ->join('t_societes_vendeuses', 'idc_societe_vendeuse=scv_id')
            ->join('t_contacts', 'idc_contact=ctc_id');

        $table = 't_id_comptable';

        $aliases = array(
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit ,$offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    public function liste_par_contact($contact, $enseigne = null) {
        $this->db->select('idc_id_comptable, idc_contact, idc_societe_vendeuse, scv_id, scv_nom, ctc_nom')
            ->join('t_societes_vendeuses', 'idc_societe_vendeuse=scv_id')
            ->join('t_contacts', 'idc_contact=ctc_id')
            ->where('idc_contact', $contact);

        if (!empty($enseigne)) {
            $this->db->where('idc_societe_vendeuse', $enseigne);
        }

        $q = $this->db->get('t_id_comptable');
        return $q->result();
    }

    public function liste_par_id_comptable($id_comptable, $enseigne) {
        $this->db->select('idc_id_comptable, idc_contact, idc_societe_vendeuse, scv_id, scv_nom, ctc_nom')
            ->join('t_societes_vendeuses', 'idc_societe_vendeuse=scv_id')
            ->join('t_contacts', 'idc_contact=ctc_id')
            ->where('idc_id_comptable', $id_comptable)
            ->where('idc_societe_vendeuse', $enseigne);

        $q = $this->db->get('t_id_comptable');
        return $q->result();
    }
}

// EOF