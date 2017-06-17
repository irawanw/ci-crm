<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_emailing extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /******************************
     * Liste Livraisons Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        //$this->load->helper("calcul_factures");
        $table = 't_emailing';
        $this->db->start_cache();

        $emailing_id = $table . ".emailing_id as RowID";
        $checkbox   = $table . ".emailing_id as checkbox";

        //$date_openemm = formatte_sql_date("date_openemm");
        /*$date_envoi                = "'' as date_envoi";
        $date_limite_de_fin        = "'' as date_limite_de_fin";*/
        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "''";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $message_numero            = $table . ".message as message_numero";
        $message_name              = "t_message.name as message_name";
        $message_lien              = "t_message.lien_pour_telecharger as message_lien";
        $message_object            = "t_message.object as message_object";
        $message_type              = "t_message.type as message_type";
        $message_telephone         = "t_message.telephone as message_telephone";
        $message_email             = "t_message.email as message_email";
        $message_famille           = "vf.vfm_famille AS message_famille";
        $message_societe           = "ts.scv_nom AS message_societe";
        $message_view              = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_emailing.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial        = "t_salesman.emp_nom as message_commercial";
        $ht                        = "v_fac.total_ht AS ht";
        $openemm_current           = "'0' as openemm_current";
        $openemm_number_of_open    = "'0' as openemm_number_of_open";
        $openemm_open_rate         = "'0' as openemm_open_rate";
        $openemm_number_of_click   = "'0' as openemm_number_of_click";
        $openemm_click_rate        = "'0' as openemm_click_rate";    
        $segment_part              = "'' as segment_part";
        $stats                     = "'' as stats";
        $physical_server           = "'' as physical_server";
        $smtp                      = "'' as smtp";
        $rotation                  = "'' as rotation";
        $segment_nom               = "CONCAT(t_segments.id,'-',t_segments.name) as segment_nom";
        $open = "(select SUM(open) from t_emailing_child where parent_id = emailing_id AND inactive IS NULL AND deleted IS NULL) as open";
        //$open_pourcentage = "'' as open_pourcentage";
        $open_pourcentage = "(select ROUND(AVG(open_pourcentage),1) from t_emailing_child where parent_id = emailing_id AND inactive IS NULL AND deleted IS NULL) as open_pourcentage";
        $quantite_envoyee          = "(select SUM(quantite_envoyee) from t_emailing_child where parent_id = emailing_id AND inactive IS NULL AND deleted IS NULL) as quantite_envoyee";
        $software_nom = "IF(t_emailing.software = 0, 'Autres', t_softwares.software_nom) as software_nom";

        $this->db->select($table . ".*,
                $checkbox,
                $emailing_id,
                $client_name,
                $commande_name,
                facture_name,
                $software_nom,
                $segment_nom,
                filtering,
                $ht,
                $operateur_qui_envoie_name,     
                date_limite_de_fin,
                quantite_envoyer,
                $open,
                $open_pourcentage,
                $message_numero,
                $message_name,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone,
                $message_email,
                $message_view,
                $message_commercial,
                $openemm_current,
                $openemm_number_of_open,
                $openemm_open_rate,
                $openemm_number_of_click,
                $openemm_click_rate,
                $quantite_envoyee,                
                $segment_part,
                $stats,
                $physical_server,
                $smtp,
                $rotation
            ", false);
        $this->db->join('t_softwares', 'software=software_id', 'left');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_articles_devis', 'ard_devis=cmd_devis', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');
        $this->db->join('t_segments', 't_segments.id=segment_numero', 'left');


        //customize filter quantite_envoyer using having not where because where in mysql not using group/aggregate function
        if ($filters != null) {
            if (array_key_exists("quantite_envoyer", $filters)) {
                $filters_quantite_envoyer = $filters['quantite_envoyer'];
                $input                    = $filters_quantite_envoyer['input'];

                if (is_numeric($input)) {
                    $this->db->having('SUM(ard_quantite) = ' . $input);
                }
                unset($filters["quantite_envoyer"]);
            }
        }

        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where($table . '.deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table . '.inactive is NULL');
                $this->db->where($table . '.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where($table . '.emailing_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'ht'                        => 'v_fac.total_ht',
            'message_numero'            => $table . ".message",
            'message_name'              => 't_message.name',
            'message_lien'              => 't_message.lien_pour_telecharger',
            'message_object'            => 't_message.object',
            'message_type'              => 't_message.type',
            'message_telephone'         => 't_message.telephone',
            'message_email'             => 't_message.email',
            'message_famille'           => 'vf.vfm_famille',
            'message_societe'           => 'ts.scv_nom',
            'message_commercial'        => 't_salesman.emp_nom',
            'message_view'              => 't_message.message'
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox                 = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->emailing_id . '">';
            $resultat['data'][$i]->deliv_sur_test_orange    = '';
            $resultat['data'][$i]->deliv_sur_test_free      = '';
            $resultat['data'][$i]->deliv_sur_test_sfr       = '';
            $resultat['data'][$i]->deliv_sur_test_gmail     = '';
            $resultat['data'][$i]->deliv_sur_test_yahoo     = '';
            $resultat['data'][$i]->deliv_sur_test_microsoft = '';
            $resultat['data'][$i]->deliv_sur_test_ovh       = '';
            $resultat['data'][$i]->deliv_sur_test_oneandone = '';
            //get quantity envoyer from t_articles_devis
            //$resultat['data'][$i]->qty_envoyer = $this->calc_qty_envoyer($resultat['data'][$i]->commande);
            $resultat['data'][$i]->critere_one    = '';
            $resultat['data'][$i]->critere_two    = '';
            $resultat['data'][$i]->many_criterias = '';

            $resultat['data'][$i]->view_detail = '<a href="#" data-id="' . $resultat['data'][$i]->emailing_id . '" class="btn-view-detail">view detail</a>';

            $critere = "";
            if ($resultat['data'][$i]->filtering != null) {
                $filtering     = json_decode($resultat['data'][$i]->filtering);
                $filtering_arr = (array) $filtering;
                $n             = 0;

                foreach ($filtering_arr as $key => $val) {
                    if ($n % 2 == 0) {
                        $name = $key . "(" . $val . ") : ";
                    }

                    if ($n % 2 == 0) {
                        $critere .= $key . "(" . $val . ") : ";
                    }

                    if ($n % 2 != 0) {
                        if ($val != "") {
                            $critere .= $val . "<br>";
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

    public function liste_child($void, $parent_id = 0, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $this->load->helper("calcul_factures");
        $table = 't_emailing';
        $this->db->start_cache();

        $emailing_id = $table . ".emailing_id as RowID";
        $checkbox   = $table . ".emailing_id as checkbox";

        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "t_operator.emp_nom";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $message_numero            = $table . ".message as message_numero";
        $message_name              = "t_message.name as message_name";
        $message_lien              = "t_message.lien_pour_telecharger as message_lien";
        $message_object            = "t_message.object as message_object";
        $message_type              = "t_message.type as message_type";
        $message_telephone         = "t_message.telephone as message_telephone";
        $message_email             = "t_message.email as message_email";
        $message_famille           = "vf.vfm_famille AS message_famille";
        $message_societe           = "ts.scv_nom AS message_societe";
        $message_view              = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_emailing.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial        = "t_salesman.emp_nom as message_commercial";
        $ht                        = "v_fac.total_ht AS ht";
        

        $this->db->select($table . ".*, t_emailing_child.*,
                $checkbox,
                $emailing_id,
                $client_name,
                $commande_name,
                facture_name,
                software_nom,
                t_emailing_child.open,
                t_emailing_child.open_pourcentage,
                $ht,
                $operateur_qui_envoie_name,                
                $message_numero,
                $message_name,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone,
                $message_email,
                $message_view,
                $message_commercial                    
            ", false);
        $this->db->join('t_emailing_child', 'parent_id=' . $table . '.emailing_id', 'inner');
        $this->db->join('t_softwares', 'software=software_id', 'left');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_articles_devis', 'ard_devis=cmd_devis', 'left');
        $this->db->join('t_employes as t_operator', 't_operator.emp_id=operateur_qui_envoie', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');
        $this->db->where('parent_id', $parent_id);

        //$this->db->where("t_articles_devis.ard_description LIKE 'Envoi d''un email au %'");
        //$this->db->group_by($table . '.emailing_id');

        //customize filter quantite_envoyer using having not where because where in mysql not using group/aggregate function
        if ($filters != null) {
            if (array_key_exists("quantite_envoyer", $filters)) {
                $filters_quantite_envoyer = $filters['quantite_envoyer'];
                $input                    = $filters_quantite_envoyer['input'];

                if (is_numeric($input)) {
                    $this->db->having('SUM(ard_quantite) = ' . $input);
                }
                unset($filters["quantite_envoyer"]);
            }
        }

        switch ($void) {
            case 'archived':
                $this->db->where('t_emailing_child.inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where('t_emailing_child.deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where('t_emailing_child.inactive is NULL');
                $this->db->where('t_emailing_child.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('emailing_child_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'ht'                        => 'v_fac.total_ht',
            'message_numero'            => $table . ".message",
            'message_name'              => 't_message.name',
            'message_lien'              => 't_message.lien_pour_telecharger',
            'message_object'            => 't_message.object',
            'message_type'              => 't_message.type',
            'message_telephone'         => 't_message.telephone',
            'message_email'             => 't_message.email',
            'message_famille'           => 'vf.vfm_famille',
            'message_societe'           => 'ts.scv_nom',
            'message_commercial'        => 't_salesman.emp_nom',
            'message_view'              => 't_message.message'          
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->emailing_id . '">';

            //get quantity envoyer from t_articles_devis
            //$resultat['data'][$i]->qty_envoyer = $this->calc_qty_envoyer($resultat['data'][$i]->commande);

            $resultat['data'][$i]->segment_numero = '';
            $resultat['data'][$i]->critere        = '';
            $resultat['data'][$i]->critere_one    = '';
            $resultat['data'][$i]->critere_two    = '';
            $resultat['data'][$i]->many_criterias = '';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'operateur_qui_envoie_name' => 'char',
            'date_envoi'                => 'date',
            'date_limite_de_fin'        => 'date',
            'stats'                     => 'char',
            'client_name'               => 'char',
            'commande_name'             => 'char',
            'facture_name'              => 'char',
            'ht'                        => 'int',
            'quantite_envoyer'          => 'int',
            'message_numero'            => 'int',
            'message_name'              => 'char',
            'message_lien'              => 'char',
            'message_object'            => 'char',
            'message_type'              => 'char',
            'message_telephone'         => 'char',
            'message_email'             => 'char',
            'message_famille'           => 'char',
            'message_societe'           => 'char',
            'message_commercial'        => 'char',
            'message_view'              => 'char',
            'segment_part'              => 'char',
            'deliv_sur_test_orange'     => 'char',
            'deliv_sur_test_free'       => 'char',
            'deliv_sur_test_sfr'        => 'char',
            'deliv_sur_test_gmail'      => 'char',
            'deliv_sur_test_yahoo'      => 'char',
            'deliv_sur_test_microsoft'  => 'char',
            'deliv_sur_test_ovh'        => 'char',
            'deliv_sur_test_oneandone'  => 'char',
            'physical_server'           => 'char',
            'smtp'                      => 'char',
            'rotation'                  => 'char',
            'quantite_envoyer'          => 'int',
            'openemm_current'           => 'int',
            'openemm_number_of_open'    => 'int',
            'openemm_open_rate'         => 'int',
            'openemm_number_of_click'   => 'int',
            'openemm_click_rate'        => 'int',
            'open'                      => 'int',
            'open_pourcentage'          => 'double',

        );

        return $filterable_columns;
    }

    /******************************
     * Detail d'une openemm
     ******************************/
    public function detail($id)
    {
        $this->load->helper("calcul_factures");
        $date_envoi                = formatte_sql_date("date_envoi");
        $date_limite_de_fin        = formatte_sql_date("date_limite_de_fin");
        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "t_operator.emp_nom";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";

        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $message_famille    = "vf.vfm_famille AS message_famille";
        $message_societe    = "ts.scv_nom AS message_societe";
        $message_view       = "t_message.message as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";

        $this->db->select("tmb.*,
                emailing_id as checkbox,
                emailing_id,
                $client_name,
                $commande_name,
                fac_id,
                fac_tva,
                fac_reference as facture,
                $operateur_qui_envoie_name,
                $date_envoi,
                $date_limite_de_fin,
                $message_name,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone,
                $message_email,
                $message_view,
                $message_commercial
            ", false);
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_employes as t_operator', 't_operator.emp_id=operateur_qui_envoie', 'left');
        $this->db->join('t_factures', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=tmb.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');

        $this->db->where('emailing_id = "' . $id . '"');
        $q = $this->db->get('t_emailing as tmb');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            //get facture ht
            $facture          = new stdClass;
            $facture->fac_id  = $resultat->fac_id;
            $facture->fac_tva = $resultat->fac_tva;
            $data_factures    = calcul_factures($facture);
            $resultat->ht     = $data_factures->fac_montant_ht;

            //get quantity envoyer from t_articles_devis
            $resultat->qty_envoyer = $this->calc_qty_envoyer($resultat->commande);

            //get openemm data
            $openemm_data                      = $this->get_mailing_detail($resultat->openemm);
            $resultat->openemm_name            = $openemm_data['openemm_name'];
            $resultat->openemm_current         = $openemm_data['openemm_current'];
            $resultat->openemm_number_of_open  = $openemm_data['openemm_number_of_open'];
            $resultat->openemm_open_rate       = ($openemm_data['openemm_open_rate'] * 100) . '%';
            $resultat->openemm_number_of_click = $openemm_data['openemm_number_of_click'];
            $resultat->openemm_click_rate      = ($openemm_data['openemm_click_rate'] * 100) . '%';

            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form($id)
    {
        $this->db->select('*');
        $this->db->where('emailing_id = "' . $id . '"');
        $q = $this->db->get('t_emailing');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form_child($id)
    {
        $this->db->select('*');
        $this->db->where('emailing_child_id = "' . $id . '"');
        $q = $this->db->get('t_emailing_child');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * New Livraisons insert into t_emailing table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_emailing', $data);
    }

    public function nouveau_child($data)
    {
        return $this->_insert('t_emailing_child', $data);
    }

    /******************************
     * Updating openemm data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_emailing', $data, $id, 'emailing_id');
    }

    public function maj_child($data, $id)
    {
        return $this->_update('t_emailing_child', $data, $id, 'emailing_child_id');
    }

    /******************************
     * Archive openemm data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_emailing', $id, 'emailing_id', 'inactive');
    }

    public function archive_child($id)
    {
        return $this->_delete('t_emailing_child', $id, 'emailing_child_id', 'inactive');
    }

    /******************************
     * Archive openemm data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_emailing', $id, 'emailing_id', 'deleted');
    }

    public function remove_child($id)
    {
        return $this->_delete('t_emailing_child', $id, 'emailing_child_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_emailing', $data, $id, 'emailing_id');
    }

    public function parent_option()
    {
        $query = $this->db->select("emailing_id as id, CONCAT(emailing_id,'-',t_message.name) as value")
            ->join('t_message_list as t_message', 't_message.message_list_id=t_emailing.message', 'left')
            ->where('t_emailing.inactive is NULL')
            ->where('t_emailing.deleted is NULL')
            ->get('t_emailing');

        return $query->result();
    }

    public function commande($emailing_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tmb.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tmb.emailing_id = "' . $emailing_id . '"');
        $q                         = $this->db->get('t_emailing tmb');
        $resultat                  = $q->result();
        $new_object                = new stdClass;
        $new_object->cmd_id        = "-1";
        $new_object->cmd_reference = 'Pas de Commande';
        array_unshift($resultat, $new_object);

        return $resultat;
    }

    public function commande_by_client($client_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tcs.ctc_id', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tcs.ctc_id = "' . $client_id . '"');
        $q = $this->db->get('t_contacts tcs');
        return $q->result();
    }

    public function software_option()
    {
        return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }

    public function stats_option()
    {
        $values = array('oui', 'non');
        return $this->form_option($values);
    }

    public function message_option()
    {
        $this->db->select('message_list_id as id, name as value');
        $q = $this->db->get('t_message_list');
        return $q->result();
    }

    public function client_option()
    {
        $this->db->order_by('ctc_nom', 'ASC');
        $q = $this->db->get('t_contacts');
        return $q->result();
    }

    public function utilisateurs_option()
    {
        $this->db->select('emp_id as id, emp_nom as value');
        //$this->db->order_by('utl_login','ASC');
        $q = $this->db->get('t_employes');
        return $q->result();
    }

    public function segment_part_option()
    {
        $values = range(1, 100);
        return $this->form_option($values);
    }

    public function verification_number_option()
    {
        $values = array('yes', 'no');
        return $this->form_option($values);
    }

    public function delivrabilite_sur_test_option()
    {
        $values = array('spam', 'not delivered', 'delivered', 'bl IP', 'bl domain', 'bl message');
        return $this->form_option($values);
    }

    public function physical_server_option()
    {
        $values = array('server1', 'server2');
        return $this->form_option($values);
    }

    public function smtp_option()
    {
        $values = array('mx.smtp.fr', 'mxg.125hjk.fr');
        return $this->form_option($values);
    }

    public function rotation_option()
    {
        $values = array('Yes', 'No');
        return $this->form_option($values);
    }

    public function form_option($values, $inc_index = false)
    {
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();
            if ($inc_index) {
                $val->id = $i;
            } else {
                $val->id = $values[$i];
            }

            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }

    public function get_facture($data)
    {
        $data->facture = '';
        $data->ht      = 0;
        // récupération des lignes de la facture
        if ($data->commande != 0) {
            $factures = $this->db->query("SELECT fac_id,fac_reference FROM t_factures WHERE fac_commande =" . $data->commande);

            if ($factures->num_rows() > 0) {
                foreach ($factures->result() as $row) {
                    /** PUSH FACTURES */
                    $data->facture .= $row->fac_reference . " <br />";
                }
            }
        }

        if ($data->dvi_id) {
            /** CALCULATE HT */
            $q = $this->db->query("SELECT art_code,ard_prix,ard_quantite,ard_remise_ht FROM t_articles_devis LEFT JOIN t_articles ON art_id=ard_article WHERE ard_inactif IS NULL AND ard_devis=" . $data->dvi_id);

            if ($q->num_rows() > 0) {
                $articles = $q->result();
                // calcul des montants HT et TTC
                $ht     = 0;
                $remise = 0;
                foreach ($articles as $a) {
                    if ($a->art_code == 'R') {
                        $remise += $a->ard_prix;
                    } else {
                        $ht += $a->ard_prix * $a->ard_quantite - $a->ard_remise_ht;
                    }
                }
                $ht       = $ht * (1 - $remise);
                $data->ht = $ht;
            }
        }

        return $data;
    }

    public function facture_option($commande)
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
            $data['facture'] = '';
            $data['ht']      = 0;
        }
        return $data;
    }

    /***************************/
    /*  OpenEMM Integration    */
    /***************************/
    public function get_mailing()
    {
        $query = $this->db->select('mailing_id as id, shortname as value')
            ->where("deleted != 1")
            ->get('t_mailing_tbl')->result();
        return $query;
    }

    public function get_mailing_detail($id)
    {
        $query = $this->db->get_where('t_emailing_stats', array('emailing_id' => $id));

        if ($query->row()) {
            $row  = $query->row();
            $data = array(
                'openemm_name'             => '',
                'openemm_open_rate'        => $row->open_rate,
                'openemm_bounce_rate'      => $row->bounce_rate,
                'openemm_hard_bounce_rate' => $row->hard_bounce_rate,
                'openemm_soft_bounce_rate' => $row->soft_bounce_rate,
                'openemm_click_rate'       => $row->click_rate,
                'openemm_number_of_open'   => $row->number_of_open,
                'openemm_number_of_click'  => $row->number_of_click,
                'openemm_total'            => $row->total,
                'openemm_current'          => $row->current,
                'openemm_opened'           => 0,
            );

            return $data;
        } else {
            return false;
        }
    }

    public function calc_qty_envoyer($commande)
    {
        $q = $this->db->query("SELECT * FROM t_commandes
                                    INNER JOIN t_articles_devis ON ard_devis = cmd_devis
                                    WHERE cmd_id = $commande AND ard_description LIKE 'Envoi d''un email au %'  ");

        $total = 0;
        foreach ($q->result() as $row) {
            $total = $total + $row->ard_quantite;
        }

        return $total;
    }
}
// EOF
