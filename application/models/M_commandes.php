<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_commandes extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('cmd_numero','number',"Numéro"),
                array('cmd_reference','text',"Référence"),
                array('cmd_date','date',"Date commande"),
                array('dvi_reference','ref',"Devis associé",'devis','cmd_devis','dvi_reference'),
                array('vec_etat','ref',"État",'v_etats_commandes'),
                array('ctc_nom','ref',"Client",'contacts','dvi_client','ctc_nom'),
                array('dvi_montant_ht','number',"Montant devis HT"),
                array('dvi_montant_ttc','number',"Montant devis TTC"),
                array('cmd_p_facture','text',"% facturé"),
                array('cmd_p_regle','text',"% réglé"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array()
        );

        return array_key_exists($type, $champs) ? $champs[$type] : array();
    }

    /******************************
    * Liste des commandes
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $cmd_reference = formatte_sql_lien('commandes/detail','cmd_id','cmd_reference');
        //$cmd_date = formatte_sql_date('cmd_date');
        $cmd_date = 'cmd_date';
        $dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
        $cor_nom = formatte_sql_lien('correspondants/detail','cor_id','cor_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $cmd_p_facture = "0";
        $cmd_p_facture2 = $cmd_p_facture ." AS cmd_p_facture";
        $cmd_p_regle = "0";
        $cmd_p_regle2 = $cmd_p_regle ." AS cmd_p_regle";
        $this->db->select("cmd_id AS RowID,cmd_id,$cmd_reference,cmd_etat,cmd_numero,$cmd_date,$dvi_reference,vec_etat,dvi_id,$cor_nom,$ctc_nom,dvi_tva,dvi_montant_ht,dvi_montant_ttc,$cmd_p_facture2,$cmd_p_regle2",false);
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('v_etats_commandes','vec_id=cmd_etat','left');
        $this->db->join('t_correspondants','cor_id=dvi_correspondant','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->where('cmd_inactif is null');

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('cmd_id', $id);
        }

        //$this->db->order_by("cmd_date desc");
        //$this->db->order_by("cmd_numero desc");
        $this->db->stop_cache();

        $table = 't_commandes';

        // aliases
        $aliases = array(
            'cmd_p_facture'=>$cmd_p_facture,
            'cmd_p_regle'=>$cmd_p_regle
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
            'cmd_numero'=>'int',
            'cmd_reference'=>'char',
            'cmd_date'=>'date',
            'dvi_reference'=>'char',
            'vec_etat'=>'char',
            'dvi_id'=>'int',
            'cor_nom'=>'char',
            'ctc_nom'=>'char',
            'dvi_tva'=>'decimal',
            'dvi_montant_ht'=>'decimal',
            'dvi_montant_ttc'=>'decimal',
            'cmd_p_facture'=>'char',
            'cmd_p_regle'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des commandes
    ******************************/
    public function liste_par_client($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $cmd_reference = formatte_sql_lien('commandes/detail','cmd_id','cmd_reference');
        $cmd_date = formatte_sql_date('cmd_date');
        $dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
        $cor_nom = formatte_sql_lien('correspondants/detail','cor_id','cor_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $cmd_p_facture = "0";
        $cmd_p_facture2 = $cmd_p_facture ." AS cmd_p_facture";
        $cmd_p_regle = "0";
        $cmd_p_regle2 = $cmd_p_regle ." AS cmd_p_regle";
        $this->db->select("cmd_id AS RowID,cmd_id,$cmd_reference,cmd_numero,$cmd_date,$dvi_reference,vec_etat,dvi_id,$cor_nom,$ctc_nom,dvi_tva,dvi_montant_ht,dvi_montant_ttc,$cmd_p_facture2,$cmd_p_regle2",false);
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('v_etats_commandes','vec_id=cmd_etat','left');
        $this->db->join('t_correspondants','cor_id=dvi_correspondant','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->where("dvi_client",$pere);
        $this->db->where('cmd_inactif is null');

        $id = intval($pere);
        if ($id > 0) {
            $this->db->where('cmd_id', $id);
        }

        //$this->db->order_by("cmd_numero asc");
        $this->db->stop_cache();

        $table = 't_commandes';

        // aliases
        $aliases = array(
            'cmd_p_facture'=>$cmd_p_facture,
            'cmd_p_regle'=>$cmd_p_regle
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
            'cmd_numero'=>'int',
            'cmd_reference'=>'char',
            'cmd_date'=>'date',
            'dvi_reference'=>'char',
            'vec_etat'=>'char',
            'dvi_id'=>'int',
            'cor_nom'=>'char',
            'ctc_nom'=>'char',
            'dvi_tva'=>'decimal',
            'dvi_montant_ht'=>'decimal',
            'dvi_montant_ttc'=>'decimal',
            'cmd_p_facture'=>'char',
            'cmd_p_regle'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Liste des commandes passées
    ******************************/
    public function liste_escli($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $cmd_reference = formatte_sql_lien('commandes/detail_escli','cmd_id','cmd_reference');
        $cmd_date = formatte_sql_date('cmd_date');
        $dvi_reference = formatte_sql_lien('devis/detail','dvi_id','dvi_reference');
        $this->db->select("cmd_id AS RowID,cmd_id,$cmd_reference,$cmd_date,$dvi_reference,dvi_id,dvi_tva,dvi_montant_ht,dvi_montant_ttc,vec_etat",false);
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('v_etats_commandes','vec_id=cmd_etat','left');
        $this->db->where("dvi_correspondant",$pere);
        $this->db->where('cmd_inactif is null');

        $id = intval($pere);
        if ($id > 0) {
            $this->db->where('cmd_id', $id);
        }

        //$this->db->order_by("cmd_numero desc");
        $this->db->stop_cache();

        $table = 't_commandes';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_escli_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_escli_filterable_columns() {
    $filterable_columns = array(
            'cmd_reference'=>'char',
            'cmd_date'=>'date',
            'dvi_reference'=>'char',
            'dvi_id'=>'int',
            'dvi_tva'=>'decimal',
            'dvi_montant_ht'=>'decimal',
            'dvi_montant_ttc'=>'decimal',
            'vec_etat'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Annuler une commande
    ******************************/
    public function annuler($id) {
        $data = array('cmd_etat'=>5);
        return $this->_update('t_commandes',$data,$id,'cmd_id');
    }

    /******************************
    * Détail d'une commande
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("cmd_id,cmd_reference,cmd_date,cmd_devis,dvi_reference,dvi_id,dvi_correspondant,dvi_societe_vendeuse,cor_nom,dvi_client,ctc_nom,cor_nom,dvi_tva,dvi_montant_ht,dvi_montant_ttc,0 AS cmd_p_facture,0 AS cmd_p_regle,cmd_etat,vec_etat",false);
        $this->db->join('t_devis','dvi_id=cmd_devis','left');
        $this->db->join('t_correspondants','cor_id=dvi_correspondant','left');
        $this->db->join('t_contacts','ctc_id=dvi_client','left');
        $this->db->join('v_etats_commandes','vec_id=cmd_etat','left');
        $this->db->where('cmd_id',$id);
        $this->db->where('cmd_inactif is null');
        $q = $this->db->get('t_commandes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Créer une facture
     ******************************/
    public function facturer($id) {

        // récupération du devis associé à la commande
        $q = $this->db->where('cmd_id',$id)
            ->get('t_commandes');
        if ($q->num_rows() > 0) {
            $devis = $q->row()->cmd_devis;

            // initialisation de la facture
            $taux_tva = tva();
            $data = array(
                'fac_numero' => 0,
                'fac_date' => date('Y-m-d H:i:s'),
                'fac_commande' => $id,
                'fac_tva' =>$taux_tva,
                'fac_etat' => 1
            );
            $nouveau = $this->_insert('t_factures',$data);

            // récupération des articles du devis
            $q = $this->db->select("art_code,ard_description,ard_prix,ard_quantite,ard_remise_taux,ard_remise_ht,ard_remise_ttc")
                ->join('t_articles','art_id=ard_article','left')
                ->where('ard_devis',$devis)
                ->where('ard_inactif is null')
                ->get('t_articles_devis');
            if ($q->num_rows() > 0) {

                // ajout des articles à la facture
                foreach ($q->result() as $article) {
                    $data = array(
                        'lif_code' => $article->art_code,
                        'lif_prix' => $article->ard_prix,
                        'lif_quantite' => $article->ard_quantite,
                        'lif_description' => $article->ard_description,
                        'lif_remise_taux' => $article->ard_remise_taux,
                        'lif_remise_ht' => $article->ard_remise_ht,
                        'lif_remise_ttc' => $article->ard_remise_ttc,
                        'lif_facture' => $nouveau
                    );
                    $this->_insert('t_lignes_factures',$data);
                }

                // fabrication du pdf
                $this->load->model('m_factures');
                $this->m_factures->trigger_lignes_factures($nouveau);
                $this->m_factures->generer_pdf($nouveau);
            }
            return $nouveau;
        }
        return false;
    }

    /******************************
     * Lancement de la production
     ******************************/
    public function lancer($id) {

        // récupération du devis associé à la commande
        $q = $this->db->where('cmd_id',$id)
            ->get('t_commandes');
        if ($q->num_rows() > 0) {
            $devis = $q->row()->cmd_devis;

            // récupération des articles du devis
            $q = $this->db->select("ard_id,art_code,art_prod,art_data,cat_famille,vfm_production,ard_description,ard_prix,ard_quantite")
                ->join('t_articles','art_id=ard_article','left')
                ->join('t_catalogues','cat_id=art_catalogue','left')
                ->join('v_familles','vfm_id=cat_famille','left')
                ->where('ard_devis',$devis)
                ->where('ard_inactif is null')
                ->get('t_articles_devis');
            if ($q->num_rows() > 0) {

                // création des tâches à partir des articles du devis
                foreach ($q->result() as $article) {
                    if ($article->vfm_production == 0) continue;
                    if ($article->art_prod == '') continue;
                    $data = array(
                        'opr_code_article' => $article->art_code,
                        'opr_code_prod' => $article->art_prod,
                        'opr_description' => $article->ard_description,
                        'opr_prix' => $article->ard_prix,
                        'opr_quantite' => $article->ard_quantite,
                        'opr_famille' => $article->cat_famille,
                        'opr_commande' => $id,
                        'opr_article_devis' => $article->ard_id,
                        'opr_data' => $article->art_data,
                        'opr_etat' => 1
                    );
                    $this->_insert('t_ordres_production',$data);
                }
            }

            // marquage de la commande à l'état lancée
            $data = array('cmd_etat'=>2);
            return $this->_update('t_commandes',$data,$id,'cmd_id');
        }
        return false;
    }

}
// EOF
