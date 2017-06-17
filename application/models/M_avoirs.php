<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_avoirs extends MY_Model {

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('avr_numero','number',"Numéro"),
                array('avr_reference','text',"Référence"),
                array('avr_date','date',"Date avoir"),
                array('avr_montant_ttc','number',"Montant TTC"),
                array('vev_etat','ref',"État de l'avoir",'v_etats_avoirs'),
                array('avr_transmis', 'text','Transmis'),
                array('vta_type','ref',"Type d'avoir",'v_types_avoirs'),
                array('avr_justification','text',"Justification de l'avoir"),
                //array('avr_fichier','hreffile','PDF', '/'),
                array('fac_reference','ref',"Facture associée",'factures','avr_facture','fac_reference'),
                array('reglements','fichier',"Règlements"),
                array('ctc_nom','ref',"Client",'contacts','avr_client','ctc_nom'),
            ),
            'write' => array(
                'avr_date' => array("Date avoir", 'date',null, false),
                'avr_societe_vendeuse' => array("Enseigne",'select',array(null, 'id', 'value'),true),
                'avr_client' => array("Client",'select',array(null, 'id', 'value'),true),
                'avr_correspondant' => array("Correspondant",'select',array(null, 'id', 'value'),false),
                'avr_type' => array("Type d'avoir",'select',array(null, 'id', 'value'),true),
                'avr_justification' => array("Justification de l'avoir",'textarea',null,false),
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des avoirs
    ******************************/
    public function liste_par_client($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $avr_reference = formatte_sql_lien('avoirs/detail','avr_id','avr_reference');
        $avr_date = formatte_sql_date('avr_date');
        $fac_reference = formatte_sql_lien('factures/detail','fac_id','fac_reference');
        $cmd_reference = formatte_sql_lien('commandes/detail','cmd_id','cmd_reference');
        $dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("avr_id AS RowID,avr_id,$avr_reference,avr_numero,$avr_date,avr_montant_ttc,vev_etat,avr_etat,vta_type,avr_justification,$fac_reference,$cmd_reference,$dvi_reference,$ctc_nom",false);
        $this->db->join('v_etats_avoirs','vev_id=avr_etat','left');
        $this->db->join('v_types_avoirs','vta_id=avr_type','left');
        $this->db->join('t_factures','fac_id=avr_facture','left');
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_contacts','ctc_id=avr_client','left');
        $this->db->where("avr_client",$pere);
        $this->db->where('avr_inactif is null');
        //$this->db->order_by("avr_numero asc");

        $this->db->stop_cache();

        $table = 't_avoirs';

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
            'avr_numero'=>'int',
            'avr_reference'=>'char',
            'avr_date'=>'date',
            'avr_montant_ttc'=>'decimal',
            'vev_etat'=>'char',
            'vta_type'=>'char',
            'avr_justification'=>'char',
            'fac_reference'=>'char',
            'cmd_reference'=>'char',
            'dvi_reference'=>'char',
            'ctc_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un avoir
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("avr_id,avr_reference,avr_date,avr_facture,avr_justification,avr_type,avr_client,avr_correspondant,avr_societe_vendeuse,avr_montant_ttc,avr_montant_ht,avr_tva,avr_etat,fac_reference,fac_commande,cmd_reference,cmd_devis,dvi_reference,scv_nom,ctc_id,ctc_adresse,ctc_cp,ctc_ville,ctc_nom,cor_id,cor_nom,vev_etat,vta_type",false);
        $this->db->join('t_factures','fac_id=avr_facture','left');
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_contacts','ctc_id=avr_client','inner');
        $this->db->join('t_societes_vendeuses','scv_id=avr_societe_vendeuse','left');
        $this->db->join('t_correspondants','cor_id=avr_correspondant','left');
        $this->db->join('v_types_avoirs','vta_id=avr_type','left');
        $this->db->join('v_etats_avoirs','vev_id=avr_etat','left');
        $this->db->where('avr_id',$id);
        $this->db->where('avr_inactif is null');
        $q = $this->db->get('t_avoirs');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    public function can_delete($id_or_values) {
        if (!is_object($id_or_values)) {
            $values = $this->detail($id_or_values);
        } else {
            $values = $id_or_values;
        }
        return ($values && ($values->avr_etat < 2));
    }

    /******************************
    * Mise à jour d'un avoir
    ******************************/
    public function maj($data,$id) {
        if (!$this->can_update($id, $data)) {
            throw new MY_Exceptions_OperationNotAllowed('Not allowed to update record given its current state and values');
        }
        $q = $this->db->where('avr_id',$id)->get('t_avoirs');
        $res =  $this->_update('t_avoirs',$data,$id,'avr_id');
        $this->load->model('m_factures');
        $this->m_factures->trigger_avoirs($q->row()->avr_facture);
        return $res;
    }

    /******************************
    * Suppression d'un avoir
    ******************************/
    public function suppression($id) {
        if (!$this->can_delete($id)) {
            throw new MY_Exceptions_OperationNotAllowed('Not allowed to delete record given its current state');
        }
        $q = $this->db->where('avr_id',$id)->get('t_avoirs');

        $res = $this->_delete('t_avoirs',$id,'avr_id','avr_inactif');
        $this->load->model('m_factures');
        $this->m_factures->trigger_avoirs($q->row()->avr_facture);
        return $res;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'avr_numero'=>'int',
            'avr_reference'=>'char',
            'avr_date'=>'date',
            'avr_montant_ttc'=>'decimal',
            'vev_etat'=>'char',
            'vta_type'=>'char',
            'avr_justification'=>'char',
            'fac_reference'=>'char',
            'cmd_reference'=>'char',
            'dvi_reference'=>'char',
            'ctc_nom'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
     * Liste des avoirs
     ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_avoirs';
        $this->db->start_cache();

        // lecture des informations
        $avr_reference = formatte_sql_lien('avoirs/lignes','avr_id','avr_reference', 'target="_blank"');
        //$avr_date = formatte_sql_date('avr_date');
        $fac_reference = formatte_sql_lien('factures/detail','fac_id','fac_reference');
        $cmd_reference = formatte_sql_lien('commandes/detail','cmd_id','cmd_reference');
        $dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("avr_id AS RowID,avr_id,$avr_reference,avr_numero,avr_date,avr_transmis,avr_montant_ttc,vev_etat,avr_etat,vta_type,avr_justification,$fac_reference,$cmd_reference,$dvi_reference,$ctc_nom",false);
        $this->db->join('v_etats_avoirs','vev_id=avr_etat','left');
        $this->db->join('v_types_avoirs','vta_id=avr_type','left');
        $this->db->join('t_factures','fac_id=avr_facture','left');
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_contacts','ctc_id=avr_client','left');
        //$this->db->where('avr_inactif is null');
        //$this->db->order_by("avr_numero asc");
        switch($void){
            case 'archived':
                $this->db->where($table.'.avr_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.avr_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.avr_archiver is NULL');
                $this->db->where($table.'.avr_inactif is NULL');
                break;
        }
        
        $id = intval($void);
        if ($id > 0) {
         $this->db->where('avr_id', $id);
        }
        
        $this->db->stop_cache();

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        // ajout des règlements
        foreach($resultat['data'] as $a) {
            $q = $this->db->where('ipu_avoir',$a->avr_id)
                ->where('ipu_inactif is null')
                ->select('ipu_reglement,rgl_reference')
                ->join('t_reglements','rgl_id=ipu_reglement','left')
                ->get('t_imputations');
            $reglements = '';
            $sep = '';
            foreach ($q->result() as $r) {
                $reglements .= $sep . anchor_popup('reglements/detail/'.$r->ipu_reglement,$r->rgl_reference,false,array("view-detail"));
                $sep = '<br />';
            }
            $a->reglements = $reglements;
            $a->checkbox = '<input type="checkbox" name="ids[]" value="'.$a->RowID.'">';
        }

        return $resultat;



        // lecture des informations
/*        if ($q->num_rows() > 0) {
            $result = $q->result();
            foreach($result as $a) {
                $q = $this->db->where('ipu_avoir',$a->avr_id)
                    ->where('ipu_inactif is null')
                    ->select('ipu_reglement,rgl_reference')
                    ->join('t_reglements','rgl_id=ipu_reglement','left')
                    ->get('t_imputations');
                $reglements = '';
                $sep = '';
                foreach ($q->result() as $r) {
                    $reglements .= $sep . anchor_popup('reglements/detail/'.$r->ipu_reglement,$r->rgl_reference,false,array("view-detail"));
                    $sep = '<br />';
                }
                $a->reglements = $reglements;
            }
            return $result;
        }
        else {
            return array();
        }*/
    }

    /******************************
     * Liste des avoirs
     ******************************/
    public function liste_par_client2($pere) {

        // lecture des informations
        $this->db->select("avr_id,avr_numero,avr_reference,avr_date,avr_montant_ttc,avr_tva,avr_etat,vev_etat,avr_type,vta_type,avr_transmis,avr_justification,avr_societe_vendeuse,avr_facture,avr_client,fac_reference,fac_commande,cmd_reference,cmd_devis,dvi_reference,ctc_nom",false);
        $this->db->join('v_etats_avoirs','vev_id=avr_etat','left');
        $this->db->join('v_types_avoirs','vta_id=avr_type','left');
        $this->db->join('t_factures','fac_id=avr_facture','left');
        $this->db->join('t_commandes','cmd_id=fac_commande','left');
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_contacts','ctc_id=avr_client','left');
        $this->db->where("avr_client",$pere);
        $this->db->where('avr_inactif is null');
        $this->db->order_by("avr_numero asc");
        $q = $this->db->get('t_avoirs');
        if ($q->num_rows() > 0) {
            $result = $q->result();
            return $result;
        }
        else {
            return array();
        }
    }

    /******************************
     * Generates then returns PDF file path for the avoir
     *
     * @param int $id Avoir id
     * @return string|boolean Path to PDF file or FALSE on failure
     * @throws MY_Exceptions_NoSuchRecord in case there is no such "avoir" record
     * @throws MY_Exceptions_NoSuchTemplate in case there is no template to generate the PDF
     ******************************/
    public function pdf($id) {
        return $this->generer_pdf($id, true);
    }

    /******************************
     * Génération de l'avoir en pdf
     ******************************/
    public function generer_pdf($id, $pdf = true) {

        // récupération des informations de la facture associée
        $q = $this->db->where('avr_id',$id)
            ->select('t_avoirs.*,t_societes_vendeuses.*,t_contacts.*,dvi_id_comptable,idc_id_comptable')
            ->join('t_societes_vendeuses', 'scv_id=avr_societe_vendeuse', 'inner')
            ->join('t_contacts', 'ctc_id=avr_client', 'left')
            ->join('t_id_comptable', 'avr_client=idc_contact AND avr_societe_vendeuse=idc_societe_vendeuse', 'left')
            ->join('t_factures', 'fac_id=avr_facture', 'left')
            ->join('t_commandes', 'cmd_id=fac_commande', 'left')
            ->join('t_devis', 'dvi_id=cmd_devis', 'left')
            ->get('t_avoirs');
        if ($q->num_rows() != 1) {
            throw new MY_Exceptions_NoSuchRecord('Impossible de trouver l\'avoir ' . $id);
        }

        $avoir = $q->row();

        // Sélection du bon id comptable
        if ($avoir->dvi_id_comptable > 0) {
            $avoir->ctc_id_comptable = $avoir->dvi_id_comptable;
        } else {
            $avoir->ctc_id_comptable = $avoir->idc_id_comptable;
        }

        // récupération du détail de l'avoir
        $q = $this->db->where('lia_avoir', $id)
            ->where('lia_inactif is null')
            ->get('t_lignes_avoirs');
        $lignes = array();
        if ($q->num_rows() > 0) {
            $lignes = $q->result();
        }

        // génération du html
        $this->load->helper('view');
        $modele = '_modeles/' . $avoir->scv_modele_avoir;
        if (!view_exists($modele)) {
            throw new MY_Exceptions_NoSuchTemplate('Could not load view file '.$modele);
        }

        $html = $this->load->view($modele, array(
            'avoir' => $avoir,
            'facture' => $avoir,
            'societe' => $avoir,
            'contact' => $avoir,
            'lignes' => $lignes), true);

        if (!$pdf) {
            return $html;
        }

        // génération du pdf
        $this->load->library('pdf');
        $uniqid = uniqid();
        $chemin = "fichiers/avoirs/avoir-_$avoir->scv_id-$uniqid-$avoir->avr_numero.pdf";
        if (!$this->pdf->creation($html, $chemin, $avoir->scv_en_production)) {
            throw new MY_Exceptions_CouldNotGenerate('Could not generate PDF file');
        }

        return $chemin;
    }

    /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_avoirs',$id,'avr_id','avr_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        $values = $this->detail($id);
        $res = $this->_delete('t_avoirs',$id,'avr_id','avr_inactif');

        $this->load->model('m_factures');
        $this->m_factures->trigger_avoirs($values->avr_facture);
        return $res;
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('avr_inactif' => null, 'avr_archiver' => null);
        return $this->_update('t_avoirs',$data, $id,'avr_id');
    }

    /******************************
     * Nouvel avoir
     ******************************/
    public function nouveau($data) {
        if (!isset($data['avr_date'])) {
            $data['avr_date'] = date('Y-m-d H:i:s');
        }
        $data['avr_etat'] = 1;
        if (!isset($data['avr_type'])) {
            $data['avr_type'] = 3; // Compensation
        }
        if (!isset($data['avr_tva'])) {
            $data['avr_tva'] = tva();
        }
        $data['avr_numero'] = 0;
        unset($data['avr_facture']);
        unset($data['avr_id']);
        return $this->_insert('t_avoirs', $data);
    }

    /******************************
     * Duplication d'une avoir
     ******************************/
    public function dupliquer($id) {

        // récupération de l'avoir actuel
        $q = $this->db->where('avr_id', $id)
            ->get('t_avoirs');
        if ($q->num_rows() == 0) {
            return false;
        }
        $data = $q->row_array();

        // initialisation de la nouvelle avoir
        unset($data['avr_id']);
        $data['avr_numero'] = 0;
        $data['avr_date'] = date('Y-m-d H:i:s');
        $data['avr_etat'] = 1;
        $data['avr_fichier'] = '';
        $data['avr_transmis'] = '';
        $data['avr_justification'] = '';
        $nouvel_id = $this->_insert('t_avoirs', $data);
        if ($nouvel_id !== false) {

            // duplication des articles
            $q = $this->db->where('lia_avoir', $id)
                ->get('t_lignes_avoirs');
            if ($q->num_rows() > 0) {
                $lignes = $q->result_array();
                foreach ($lignes as $l) {
                    unset($l['lia_id']);
                    $l['lia_avoir'] = $nouvel_id;
                    $resultat = $this->_insert('t_lignes_avoirs', $l);
                    if ($resultat === false) {
                        return $resultat;
                    }
                }
            }
            // fabrication du pdf
            $this->trigger_lignes_avoirs($nouvel_id);
        }
        return $nouvel_id;
    }

    /******************************
     * Envoi de l'avoir par email
     ******************************/
    public function envoyer_email($id) {
        $q = $this->db->where('avr_id',$id)
            ->where('avr_inactif is null')
            ->select('t_avoirs.*,scv_email,avr_correspondant,avr_client')
            ->join('t_societes_vendeuses','scv_id=avr_societe_vendeuse','inner')
            ->get('t_avoirs');
        if ($q->num_rows() != 1) {
            throw new MY_Exceptions_NoSuchRecord("Pas d'avoir numéro ".$id);
        }

        $avoir = $q->row();

        if (!$avoir->scv_email) {
            throw new MY_Exceptions_NoEmailAddress("L'enseigne n'a pas d'adresse email");
        }

        // récupération de l'adresse email
        $this->load->helper('email');
        $email = get_email_address($avoir->avr_correspondant, $avoir->avr_client);
        if (!$email) {
            throw new MY_Exceptions_NoEmailAddress("Le contact n'a pas d'adresse email");
        }

        $this->load->model('m_modeles_documents');

        // récupération du modèle de document
        $modele = $this->m_modeles_documents->detail('AVOIR');
        if (!$modele) {
            throw new MY_Exceptions_NoSuchTemplate("Pas de modèle disponible pour le message email");
        }
        $sujet = $modele->mod_sujet;
        $corps = $modele->mod_texte;

        // Generate PDF
        $pdf = $this->pdf($id);

        // envoi du mail
        $this->load->library('email');
        $resultat = $this->email->send_one($email, $avoir->scv_email, $sujet, $corps, $pdf);
        if (!$resultat) {
            return false;
        }

        // enregistrement de la transmission
        $transmis = $avoir->avr_transmis;
        if ($transmis != '') {
            $transmis .= '<br />';
        }
        $transmis .= date('d/m/Y') . '&nbsp;Mail';
        $data = array(
            'avr_transmis' => $transmis
        );
        return $this->_update('t_avoirs', $data, $id, 'avr_id');
    }


    /******************************
     * Trigger lignes d'un avoir
     ******************************/
    public function trigger_lignes_avoirs($id) {

        // récupération du taux de TVA
        $q = $this->db->select('avr_tva')
            ->where('avr_id',$id)
            ->get('t_avoirs');
        $tva = $q->row()->avr_tva;

        // récupération des lignes de l'avoir
        $q = $this->db->select("lia_code,lia_prix,lia_quantite,lia_remise_ht")
            ->where('lia_inactif is null')
            ->where('lia_avoir',$id)
            ->get('t_lignes_avoirs');
        if ($q->num_rows() > 0) {
            $lignes = $q->result();

            // calcul des montants HT et TTC
            $ht = 0;
            $remise = 0;
            foreach($lignes as $l) {
                if ($l->lia_code == 'R') {
                    $remise +=  $l->lia_prix;
                }
                else {
                    $ht += $l->lia_prix * $l->lia_quantite - $l->lia_remise_ht;
                }
            }
            $montant_htnr = $ht;
            $ht = $ht * (1 - $remise);
            $tva = $ht * $tva;
            $ttc = $ht + $tva;
            $montant_ht = $ht;
            $montant_tva = $tva;
            $montant_ttc = $ttc;
        }
        else {
            $montant_htnr = 0;
            $montant_ht = 0;
            $montant_tva = 0;
            $montant_ttc = 0;
        }
        $data = array(
            'avr_montant_htnr' => $montant_htnr,
            'avr_montant_ht' => $montant_ht,
            'avr_montant_tva' => $montant_tva,
            'avr_montant_ttc' => $montant_ttc,
        );
        $this->db->where('avr_id',$id)
            ->update('t_avoirs',$data);
    }

    public function liste_types_avoirs()
    {
        $this->db->select('vta_id as id, vta_type as value');
        $sql = $this->db->get('v_types_avoirs');
        if ($sql->num_rows() > 0) {
            $resultat = $sql->result();
            return $resultat;
        }
        return array();
    }
}

// EOF