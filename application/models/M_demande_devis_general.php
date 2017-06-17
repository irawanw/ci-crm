<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_demande_devis_general extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->filterable_columns = array(
            'ctc_id'                   => 'int',
            'ctc_nom'                  => 'char',
            'ctc_date_creation'        => 'datetime',
            'ctc_telephone_nom'        => 'char',
            'comment_desc'             => 'char',
            'utl_login'                => 'char',
            'scv_nom'                  => 'char',
            'generale_name' 		   => 'char',
            'origine_name'             => 'char',
            'ctc_periode'              => 'int',
            'ctc_signe_nom'            => 'char',
            'numero_factures'          => 'char',
            'total_factures_ht'        => 'int',
        );
    }

    /******************************
     * Liste des contacts
     ******************************/
    public function liste($id = 0, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {    
    	$this->db->start_cache();

        $ctc_telephone            = "if(ctc_telephone='','Non','Oui')";
        $ctc_telephone_nom        = $ctc_telephone . " AS ctc_telephone_nom";      
        //$ctc_signe                = "if(ctc_signe, 'Oui', 'Non')";
		$ctc_signe 				  = "if(ctc_signe, 'Oui', if(GROUP_CONCAT(nullif(fac_reference,'')) != '', 'Oui', 'Non'))";
        $ctc_signe_nom            = $ctc_signe . " AS ctc_signe_nom";
        $factures 				  = "GROUP_CONCAT(nullif(fac_reference,''))";
        $numero_factures          = $factures." AS numero_factures";
        $total_factures_ht		  = "SUM(fac_montant_ht) as total_factures_ht";

        $table = "(SELECT ctc_id AS RowID,
			ctc_id,
			ctc_nom,
			ctc_date_creation,
			$ctc_telephone_nom,
			comment_desc,
			utl_login,
			scv_nom,
			generale_name,
			origine_name,
			'' as ctc_periode,
			$ctc_signe_nom,
			$numero_factures,
			$total_factures_ht
        	FROM `t_contacts` 
        	LEFT JOIN `t_devis` ON `dvi_client`=`ctc_id` 
        	LEFT JOIN `t_commandes` ON `cmd_devis`=`dvi_id` 
        	LEFT JOIN `t_factures` ON `fac_commande`=`cmd_id` AND 
											`fac_etat` = 2 AND
											(`fac_inactif` IS NULL OR `fac_inactif` = '0000-00-00 00:00:00')
        	LEFT JOIN `t_societes_vendeuses` ON `scv_id`=`dvi_societe_vendeuse` 
        	LEFT JOIN `v_types_origine_prospect` `top` ON `top`.`origine_id` = `ctc_origine` 
        	LEFT JOIN `t_contacts_commentaires` ON `comment_id`=`ctc_id` 
        	LEFT JOIN `t_utilisateurs` ON `utl_id`=`ctc_commercial_charge` 
        	LEFT JOIN `v_types_origine_generale` ON `generale_id`=`origine_group` 
			WHERE `ctc_statistiques` = 1 AND UNIX_TIMESTAMP(ctc_date_creation) 
			GROUP BY `ctc_id`)";			

  //       $this->db->select("ctc_id AS RowID,
		// ctc_id,
		// ctc_nom,
		// ctc_date_creation,
		// $ctc_telephone_nom,
		// comment_desc,
		// utl_login,
		// scv_nom,
		// generale_name,
		// origine_name,
		// '' as ctc_periode,
		// $ctc_signe_nom,
		// $numero_factures,
		// $total_factures_ht",false);        
		// $this->db->join('t_devis','dvi_client=ctc_id','left');
		// $this->db->join('t_commandes','cmd_devis=dvi_id','left');
		// $this->db->join('t_factures','fac_commande=cmd_id','left');
		// $this->db->join('t_societes_vendeuses','scv_id=dvi_societe_vendeuse','left');
  //       $this->db->join('v_types_origine_prospect top', 'top.origine_id = ctc_origine', 'left');   
  //       $this->db->join('t_contacts_commentaires','comment_id=ctc_id','left');
  //       $this->db->join('t_utilisateurs','utl_id=ctc_commercial_charge','left');
  //       $this->db->join('v_types_origine_generale','generale_id=origine_group','left');
  //       $this->db->where($where);                 
  //       $this->db->group_by('ctc_id');

        $this->db->stop_cache();

        $aliases = array(
        	'ctc_telephone_nom' => $ctc_telephone,        	
        	'ctc_signe_nom' => $ctc_signe,
        	'numero_factures' => $factures
        );
       
        $resultat = $this->_filtre($table, $this->get_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering,true);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox     = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->RowID . '">';
            $comment_desc                       = $resultat['data'][$i]->comment_desc == "" ? '<a href="#" class="update-contact-comment" data-id="' . $resultat['data'][$i]->RowID . '" data-desc="">ajouter</a>' : '<a href="#" class="update-contact-comment" data-id="' . $resultat['data'][$i]->RowID . '" data-desc="' . $resultat['data'][$i]->comment_desc . '">' . $resultat['data'][$i]->comment_desc . '</a>';
            $resultat['data'][$i]->comment_desc = $comment_desc;
        }

        return $resultat;
    }

    public function get_filterable_columns()
    {
        return $this->filterable_columns;
    }

    /******************************
     * Nouveau
     ******************************/
    public function nouveau($data)
    {

    }

    /******************************
     * Détail
     ******************************/
    public function detail($id)
    {

    }

    public function maj($data, $id)
    {

    }

    public function liste_option()
    {

    }

    public function ajoutercomment($data)
    {
        $check = $this->db->get_where('t_contacts_commentaires', array('comment_id' => $data['comment_id']));
        if ($check->num_rows() > 0) {
            $res = $this->_update('t_contacts_commentaires', $data, $data['comment_id'], 'comment_id');
            return $res;
        } else {
            $id = $this->_insert('t_contacts_commentaires', $data);
            return $id;
        }
    }

    public function get_total($id = 0, $filters = null, $ordercol = 2, $ordering = "asc")
    {
    	$this->db->start_cache();

        $ctc_telephone            = "if(ctc_telephone='','Non','Oui')";
        $ctc_telephone_nom        = $ctc_telephone . " AS ctc_telephone_nom";      
        $ctc_signe 				  = "if(ctc_signe, 'Oui', if(GROUP_CONCAT(nullif(fac_reference,'')) != '', 'Oui', 'Non'))";
        $ctc_signe_nom            = $ctc_signe . " AS ctc_signe_nom";
        $factures 				  = "GROUP_CONCAT(nullif(fac_reference,''))";
        $numero_factures          = $factures." AS numero_factures";
        $total_factures_ht		  = "SUM(fac_montant_ht) as total_factures_ht";

        $table = "(SELECT ctc_id AS RowID,
			ctc_id,
			ctc_nom,
			ctc_date_creation,
			$ctc_telephone_nom,
			comment_desc,
			utl_login,
			scv_nom,
			generale_name,
			origine_name,
			'' as ctc_periode,
			$ctc_signe_nom,
			ctc_signe,
			$numero_factures,
			$total_factures_ht
        	FROM `t_contacts` 
        	LEFT JOIN `t_devis` ON `dvi_client`=`ctc_id` 
        	LEFT JOIN `t_commandes` ON `cmd_devis`=`dvi_id` 
        	LEFT JOIN `t_factures` ON `fac_commande`=`cmd_id` AND 
											`fac_etat` = 2 AND
											(`fac_inactif` IS NULL OR `fac_inactif` = '0000-00-00 00:00:00') 
        	LEFT JOIN `t_societes_vendeuses` ON `scv_id`=`dvi_societe_vendeuse` 
        	LEFT JOIN `v_types_origine_prospect` `top` ON `top`.`origine_id` = `ctc_origine` 
        	LEFT JOIN `t_contacts_commentaires` ON `comment_id`=`ctc_id` 
        	LEFT JOIN `t_utilisateurs` ON `utl_id`=`ctc_commercial_charge` 
        	LEFT JOIN `v_types_origine_generale` ON `generale_id`=`origine_group` 
			WHERE `ctc_statistiques` = 1 AND UNIX_TIMESTAMP(ctc_date_creation) 
			GROUP BY `ctc_id`)";

        $this->db->stop_cache();

        $aliases = array(
        	'ctc_telephone_nom' => $ctc_telephone,        	
        	'ctc_signe_nom' => $ctc_signe,
        	'numero_factures' => $factures
        );
       
        $resultat = $this->_filtre($table, $this->get_filterable_columns(), $aliases, 0, 0, $filters, $ordercol, $ordering,true);
        $this->db->flush_cache();
               
        $total = count($resultat['data']);
        $total_signe = 0;        
        $total_ca = 0;

        foreach($resultat['data'] as $row) {
        	if($row->ctc_signe_nom == 'Oui') {
        		++$total_signe;
        	}

        	if($row->total_factures_ht != "") {
        		$total_ca += $row->total_factures_ht;
        	}
        }

        $percentage = round($total_signe / $total, 4) * 100;

        $data = array(
        	'total' => $total,
        	'total_signe' => $total_signe,
        	'percentage' => $percentage > 0 ? $percentage.'%' : '0%',
        	'total_ca' => $total_ca
        );

        return $data;
    }
}

// EOF
