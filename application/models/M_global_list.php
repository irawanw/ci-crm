<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_global_list extends MY_Model
{
	private $SOFTWARE_IDS = array('openemm' => 1,'max_bulk' => 2,'manual_sending' => 3, 'pages_jaunes' => 4);

    public function __construct()
    {
        parent::__construct();
    }

    /******************************
     * Liste Livraisons Data
     ******************************/
    public function liste($void, $software=null, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        //redirect ordering with right column (ordering by column with type data int/decimal not varchar)
        switch ($ordercol) {
            case 'openemm_open_rate_pct':
                $ordercol = "openemm_open_rate";
                break;
            case 'openemm_click_rate_pct':
                $ordercol = "openemm_click_rate";
                break;
            case 'deliv_reelle_bounce_percentage_pct':
                $ordercol = "deliv_reelle_bounce_percentage";
                break;
            case 'deliv_reelle_hard_bounce_rate_pct':
                $ordercol = "deliv_reelle_hard_bounce_rate";
                break;
            case 'deliv_reelle_soft_bounce_rate_pct':
                $ordercol = "deliv_reelle_soft_bounce_rate";
                break;
            default:
                # code...
                break;
        }

        $table = 'global_view_parent';
        $this->db->start_cache();
        $message_view = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',message_view,'\" data-message=\"',message_view,'\">','Voir Message','</a>') as message_view";       
        $quantite_envoyee    = "(select SUM(quantite_envoyee) 
									from global_view_child 
									where 
										parent_id = global_view_parent.id AND 
										global_view_parent.software_id = global_view_child.software_id AND 
										(global_view_child.inactive IS NULL OR global_view_child.inactive = '0000-00-00 00:00:00') AND 
										(global_view_child.deleted IS NULL OR global_view_child.deleted = '0000-00-00 00:00:00')
								) as quantite_envoyee";
        $open = "(select SUM(open) 
					from global_view_child 
					where 
						parent_id = global_view_parent.id AND 
						global_view_parent.software_id = global_view_child.software_id  AND 
						(global_view_child.inactive IS NULL OR global_view_child.inactive = '0000-00-00 00:00:00') AND 
						(global_view_child.deleted IS NULL OR global_view_child.deleted = '0000-00-00 00:00:00')
					) as open";
        $open_pourcentage = "(select ROUND( AVG(open_pourcentage), 1 ) 
								from 
									global_view_child where parent_id = global_view_parent.id AND 
									global_view_parent.software_id = global_view_child.software_id AND 
									(global_view_child.inactive IS NULL OR global_view_child.inactive = '0000-00-00 00:00:00') AND 
									(global_view_child.deleted IS NULL OR global_view_child.deleted = '0000-00-00 00:00:00')
								) as open_pourcentage";

        $this->db->select("*,
			'checkbox' as checkbox,
            global_view_parent.id as id,
			$message_view,           
            $quantite_envoyee,
            $open,
            $open_pourcentage,
            filtering
		");

        $this->db->join('t_segments','t_segments.id=segment_numero','left');

        switch ($void) {
            case 'archived':
                $where = $table . '.inactive IS NULL OR '.$table . '.inactive = "0000-00-00 00:00:00"';
                $this->db->where($where);
                break;
            case 'deleted':
                $where = $table . '.deleted IS NULL OR '.$table . '.deleted = "0000-00-00 00:00:00"';
                $this->db->where($where);
                break;
            case 'all':
                break;
            default:
                $where = '('.$table . '.inactive IS NULL OR '.$table . '.inactive = "0000-00-00 00:00:00") 
							AND ('.$table.'.deleted IS NULL OR '.$table . '.deleted = "0000-00-00 00:00:00")';
                $this->db->where($where);
                break;
        }

        $id = intval($void);
        if ($id > 0) {        	
            $this->db->where($table.'.id', $id);
            $this->db->where($table.'.software_id', $software);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            //'openemm_open_rate_pct' => 'openemm_open_rate'
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->id . '">';
            $id                             = $resultat['data'][$i]->id;
            $software                       = $resultat['data'][$i]->software_id;
            $software                       = str_replace(" ", "-", $software);
            $rowID                          = $id . "_" . $software;

            $resultat['data'][$i]->RowID = $rowID;

            $resultat['data'][$i]->segment_part           = "";
            $resultat['data'][$i]->segment_nom            = "";
            $resultat['data'][$i]->segment_first_critere  = "";
            $resultat['data'][$i]->segment_second_critere = "";
            $resultat['data'][$i]->segment_many_criterias = "";

            //SUIVI DE L'ENVOI           
            $resultat['data'][$i]->stats                   = "";
            //$resultat['data'][$i]->quantite_envoyer        = "";
            $resultat['data'][$i]->openemm_current         = "";
            $resultat['data'][$i]->openemm_number_of_open  = "";
            $resultat['data'][$i]->openemm_open_rate_pct   = "";
            $resultat['data'][$i]->openemm_number_of_click = "";
            $resultat['data'][$i]->openemm_click_rate_pct  = "";
            $resultat['data'][$i]->verification_number     = "";
            $resultat['data'][$i]->number_sent_through     = "";
            $resultat['data'][$i]->number_sent_mail        = "";
            //DELIVRABILITE SUR TEST
            $resultat['data'][$i]->deliv_sur_test_orange    = "";
            $resultat['data'][$i]->deliv_sur_test_free      = "";
            $resultat['data'][$i]->deliv_sur_test_sfr       = "";
            $resultat['data'][$i]->deliv_sur_test_gmail     = "";
            $resultat['data'][$i]->deliv_sur_test_microsoft = "";
            $resultat['data'][$i]->deliv_sur_test_yahoo     = "";
            $resultat['data'][$i]->deliv_sur_test_ovh       = "";
            $resultat['data'][$i]->deliv_sur_test_oneandone = "";
            //DELIVRABILITE REELLE
            $resultat['data'][$i]->deliv_reelle_bounce                = "";
            $resultat['data'][$i]->deliv_reelle_bounce_percentage_pct = "";
            $resultat['data'][$i]->deliv_reelle_hard_bounce_rate_pct  = "";
            $resultat['data'][$i]->deliv_reelle_soft_bounce_rate_pct  = "";
            $resultat['data'][$i]->deliv_reelle_orange                = "";
            $resultat['data'][$i]->deliv_reelle_free                  = "";
            $resultat['data'][$i]->deliv_reelle_sfr                   = "";
            $resultat['data'][$i]->deliv_reelle_gmail                 = "";
            $resultat['data'][$i]->deliv_reelle_microsoft             = "";
            $resultat['data'][$i]->deliv_reelle_yahoo                 = "";
            $resultat['data'][$i]->deliv_reelle_ovh                   = "";
            $resultat['data'][$i]->deliv_reelle_oneandone             = "";
            //TECHNICAL
            $resultat['data'][$i]->operateur_qui_envoie = "";
            $resultat['data'][$i]->number_sent          = "";
            $resultat['data'][$i]->physical_server      = "";
            $resultat['data'][$i]->provider             = "";
            $resultat['data'][$i]->ip                   = "";
            $resultat['data'][$i]->smtp                 = "";
            $resultat['data'][$i]->rotation 			= "";
             $resultat['data'][$i]->domain              = "";
            $resultat['data'][$i]->computer             = "";
            $resultat['data'][$i]->manual_sender        = "";
            $resultat['data'][$i]->manual_sender_domain = "";
            $resultat['data'][$i]->copy_mail            = "";
            //MANUAL
            $resultat['data'][$i]->speed_hours  = "";
            $resultat['data'][$i]->number_hours = "";

            $resultat['data'][$i]->view_detail = '<a href="#" data-id="'.$rowID.'" class="btn-view-detail">view detail</a>';

            $critere    = "";
            if($resultat['data'][$i]->filtering != null) {
                $filtering  = json_decode($resultat['data'][$i]->filtering);            
                $filtering_arr = (array) $filtering;
                $n = 0;

                foreach($filtering_arr as $key => $val) {
                    if($n % 2 == 0) {
                        $name = $key."(".$val.") : ";
                    }

                    if($n % 2 == 0) {
                        $critere .= $key."(".$val.") : ";
                    }

                    if($n % 2 != 0) {
                        if($val != "") {
                            $critere .= $val."<br>";
                        } else {
                            $critere .= "<br>";
                        }
                    }

                    $n++;
                }
            }
           
            $resultat['data'][$i]->critere = $critere;

        }

        return $resultat;
    }

    public function liste_child($void, $software=null, $parent_id, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 'global_view_child';
        $this->db->start_cache();   

        //$date_envoi = "(select date_envoi from global_view_parent WHERE parent_id=global_view_parent.id AND global_view_child.software_id=global_view_parent.software_id) as date_envoi";
        //$date_limite_de_fin = "(select date_limite_de_fin from global_view_parent WHERE parent_id=global_view_parent.id AND global_view_child.software_id=global_view_parent.software_id) as date_limite_de_fin";
        //$quantite_envoyer = "(select quantite_envoyer from global_view_parent WHERE parent_id=global_view_parent.id AND global_view_child.software_id=global_view_parent.software_id) as quantite_envoyer"; 
     
        //$this->db->select("*,   
        //    date_envoi,
        //    date_limite_de_fin,
        //    quantite_envoyer
        //");
		$this->db->select("*");
        //$this->db->join('global_view_parent','global_view_parent.id=parent_id','left');
        $this->db->where($table.'.software_id', $software);
        $this->db->where('parent_id', $parent_id);        

        switch ($void) {
            case 'archived':
                $where = $table . '.inactive IS NULL OR '.$table . '.inactive = "0000-00-00 00:00:00"';
                $this->db->where($where);
                break;
            case 'deleted':
                $where = $table . '.deleted IS NULL OR '.$table . '.deleted = "0000-00-00 00:00:00"';
                $this->db->where($where);
                break;
            case 'all':
                break;
            default:
                $where = '('.$table . '.inactive IS NULL OR '.$table . '.inactive = "0000-00-00 00:00:00") AND ('.$table.'.deleted IS NULL OR '.$table . '.inactive = "0000-00-00 00:00:00")';
                $this->db->where($where);
                break;
        }

        $id = intval($void);
        if ($id > 0) {          
            $this->db->where($table.'.id', $id);            
        }
		
		//$this->db->group_by('global_view_child.id');

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            //'openemm_open_rate_pct' => 'openemm_open_rate'
        );
		
        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
		
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {            
            $id                             = $resultat['data'][$i]->id;
            $software                       = $resultat['data'][$i]->software_id;            
            $rowID                          = $id . "_" . $software;

            $resultat['data'][$i]->RowID = $rowID;            
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'software_name'                           => 'char',
            // 'operateur_qui_envoie'               => 'char',
            // 'date_envoi'                         => 'date',
            // 'date_limite_de_fin'                 => 'date',
            // 'stats'                              => 'char',
            // 'client'                             => 'char',
            // 'commande'                           => 'char',
            // 'facture'                            => 'char',
            // 'ht'                                 => 'int',
            // 'message_id'                         => 'char',
            // 'segment_part'                       => 'char',
            // 'quantite_envoyer'                   => 'char',
            // 'openemm_number_of_open'             => 'char',
            // 'openemm_open_rate_pct'              => 'char',
            // 'openemm_number_of_click'            => 'char',
            // 'openemm_click_rate_pct'             => 'char',
            // 'verification_number'                => 'char',
            // 'message_name'                       => 'char',
            // 'message_view'                       => 'char',
            // 'message_lien'                       => 'char',
            // 'message_object'                     => 'char',
            // 'message_type'                       => 'char',
            // 'message_famille'                    => 'char',
            // 'message_societe'                    => 'char',
            // 'message_commercial'                 => 'char',
            // 'message_email'                      => 'char',
            // 'message_telephone'                  => 'char',
            // 'deliv_sur_test_orange'              => 'char',
            // 'deliv_sur_test_free'                => 'char',
            // 'deliv_sur_test_sfr'                 => 'char',
            // 'deliv_sur_test_gmail'               => 'char',
            // 'deliv_sur_test_microsoft'           => 'char',
            // 'deliv_sur_test_yahoo'               => 'char',
            // 'deliv_sur_test_ovh'                 => 'char',
            // 'deliv_sur_test_oneandone'           => 'char',
            // 'deliv_reelle_bounce'                => 'char',
            // 'deliv_reelle_bounce_percentage_pct' => 'char',
            // 'deliv_reelle_hard_bounce_rate_pct'  => 'char',
            // 'deliv_reelle_soft_bounce_rate_pct'  => 'char',
            // 'physical_server'                    => 'char',
            // 'provider'                           => 'char',
            // 'ip'                                 => 'char',
            // 'smtp'                               => 'char',
            // 'rotation'                           => 'char',
            // 'domain'                             => 'char',
            // 'computer'                           => 'char',
            // 'manual_sender'                      => 'char',
            // 'manual_sender_domain'               => 'char',
            // 'copy_mail'                          => 'char',
            // 'number_sent_through'                => 'char',
            // 'number_sent_mail'                   => 'char',
            // 'speed_hours'                        => 'int',
            // 'number_hours'                       => 'int',
            // 'segment_numero'                     => 'char',
            // 'segment_nom'                        => 'char',
            // 'segment_first_critere'              => 'char',
            // 'segment_second_critere'             => 'char',
            // 'segment_many_criterias'             => 'char',
            // 'number_sent'                        => 'char',
            // 'openemm_current'                    => 'char',
            // 'deliv_reelle_orange'                => 'char',
            // 'deliv_reelle_free'                  => 'char',
            // 'deliv_reelle_sfr'                   => 'char',
            // 'deliv_reelle_gmail'                 => 'char',
            // 'deliv_reelle_microsoft'             => 'char',
            // 'deliv_reelle_yahoo'                 => 'char',
            // 'deliv_reelle_ovh'                   => 'char',
            // 'deliv_reelle_oneandone'             => 'char',
        );

        return $filterable_columns;
    }

    public function get_facture_data($commande)
    {
        $this->db->select("*");
        $this->db->where('fac_commande = "' . $commande . '"');
        $result = $this->db->get('t_factures')->result();
        if (count($result)) {
            $facture         = $result[0];
            $data['facture'] = $facture->fac_reference;

            //calculate ht
            $this->load->helper("calcul_factures");
            $data_factures = calcul_factures($facture);
            $data['ht']    = $data_factures->fac_montant_ht;
        } else {
            $data['ht']      = '';
            $data['facture'] = 0;
        }
        return $data;
    }

    public function calc_qty_envoyer($commande)
    {
        $q = $this->db->query("SELECT * FROM t_commandes
									INNER JOIN t_articles_devis ON ard_devis = cmd_devis
									WHERE cmd_id = $commande AND ard_description LIKE 'Envoi d''un email au %'	");

        $total = 0;
        foreach ($q->result() as $row) {
            $total = $total + $row->ard_quantite;
        }

        return $total;
    }

    public function software_option()
    {
        return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }

    /******************************
     * Remove data
     ******************************/
    public function remove($id)
    {
        $id_array = explode("_", $id);
        $id       = $id_array[0];
        $software = (int)$id_array[1];

        switch ($software) {
            case 1:
            	$table       = "t_openemm";
                $primary_key = "openemm_id";                
                break;
            case 2:
                $table       = "t_pages_jaunes";
                $primary_key = "max_bulk_id";
                break;
            case 3:
            	$table       = "t_manual_sending";
                $primary_key = "manual_sending_id";                
                break;
            case 4:
                $table       = "t_max_bulk";
                $primary_key = "pages_jaunes_id";
                break;
			case 5:
                $table       = "t_sendgrid";
                $primary_key = "sendgrid_id";
                break;
			case 6:
                $table       = "t_sendinblue";
                $primary_key = "sendinblue_id";
                break;
            case 7:
                $table       = "t_airmail";
                $primary_key = "airmail_id";
                break;
            case 8:
                $table       = "t_mailchimp";
                $primary_key = "mailchimp_id";
                break;
            default:
                break;
        }

        return $this->_delete($table, $id, $primary_key, 'deleted');
    }

    /******************************
     * Archive data
     ******************************/
    public function archive($id)
    {
        $id_array = explode("_", $id);
        $id       = $id_array[0];
        $software = (int)$id_array[1];

        switch ($software) {
            case 1:
            	$table       = "t_openemm";
                $primary_key = "openemm_id";                
                break;
            case 2:
                $table       = "t_pages_jaunes";
                $primary_key = "max_bulk_id";
                break;
            case 3:
            	$table       = "t_manual_sending";
                $primary_key = "manual_sending_id";                
                break;
            case 4:
                $table       = "t_max_bulk";
                $primary_key = "pages_jaunes_id";
                break;
			case 5:
                $table       = "t_sendgrid";
                $primary_key = "sendgrid_id";
                break;
			case 6:
                $table       = "t_sendinblue";
                $primary_key = "sendinblue_id";
                break;
            case 7:
                $table       = "t_airmail";
                $primary_key = "airmail_id";
                break;
            case 8:
                $table       = "t_mailchimp";
                $primary_key = "mailchimp_id";
                break;
            default:
                break;
        }

        return $this->_delete($table, $id, $primary_key, 'inactive');
    }    

    /******************************
     * Archive child data
     ******************************/
    public function archive_child($id)
    {
        $id_array = explode("_", $id);
        $id       = $id_array[0];
        $software = (int)$id_array[1];

        switch ($software) {
            case 1:
                $table       = "t_openemm_child";
                $primary_key = "openemm_child_id";                
                break;
            case 2:
                $table       = "t_pages_jaunes_child";
                $primary_key = "pages_jaunes_child_id";
                break;
            case 3:
                $table       = "t_manual_sending_child";
                $primary_key = "manual_sending_child_id";                
                break;				
            case 4:
                $table       = "t_max_bulk_child";
                $primary_key = "max_bulk_child_id";
                break;
			case 5:
                $table       = "t_sendgrid_child";
                $primary_key = "sendgrid_child_id";
                break;				
			case 6:
                $table       = "t_sendinblue_child";
                $primary_key = "sendinblue_child_id";
                break;
            case 7:
                $table       = "t_airmail_child";
                $primary_key = "airmail_child_id";
                break;
            case 8:
                $table       = "t_mailchimp_child";
                $primary_key = "mailchimp_child_id";
                break;				
            default:
                break;
        }

        return $this->_delete($table, $id, $primary_key, 'inactive');
    }

    /******************************
     * Remove child data
     ******************************/
    public function remove_child($id)
    {
        $id_array = explode("_", $id);
        $id       = $id_array[0];
        $software = (int)$id_array[1];

        switch ($software) {
            case 1:
                $table       = "t_openemm_child";
                $primary_key = "openemm_child_id";                
                break;
            case 2:
                $table       = "t_pages_jaunes_child";
                $primary_key = "pages_jaunes_child_id";
                break;
            case 3:
                $table       = "t_manual_sending_child";
                $primary_key = "manual_sending_child_id";                
                break;				
            case 4:
                $table       = "t_max_bulk_child";
                $primary_key = "max_bulk_child_id";
                break;
			case 5:
                $table       = "t_sendgrid_child";
                $primary_key = "sendgrid_child_id";
                break;				
			case 6:
                $table       = "t_sendinblue_child";
                $primary_key = "sendinblue_child_id";
                break;
            case 7:
                $table       = "t_airmail_child";
                $primary_key = "airmail_child_id";
                break;
            case 8:
                $table       = "t_mailchimp_child";
                $primary_key = "mailchimp_child_id";
                break;				
            default:
                break;
        }

        return $this->_delete($table, $id, $primary_key, 'deleted');
    }

    public function get_software_id($type, $id) {
        $query = $this->db->select('software_id')->get_where('global_view_'.$type, array('id' => $id));

        if($query->row()) {
            $row = $query->row();

            return $row->software_id;
        } else {
            return null;
        }
    }
}
// EOF
