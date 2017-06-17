<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_max_bulk extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type, $data = null)
    {
        $champs = array(
            'read.parent'  => array(
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
                array('max_bulk_id', 'ref', "Max Bulk#", 'max_bulk', 'max_bulk_id', 'max_bulk_id'),
                //PARAMETRES
                array('software', 'text', "Software", 'software'),
                //INFO FACTURATION
                array('client_name', 'text', "Client", 'client_name'),
                array('commande_name', 'text', "Commande", 'commande_name'),
                array('facture_name', 'text', "Facture", 'facture_name'),
                array('ht', 'text', "HT", 'ht'),
                //MESSAGE
                array('message_numero', 'text', "Numéro", 'message_numero'),
                array('message_name', 'text', "Nom", 'message_name'),
                array('message_view', 'text', "View Message", 'message_view'),
                array('message_lien', 'text', 'Lien Pour Télécharger', 'message_lien'),
                array('message_object', 'text', 'Objet Du Message', 'message_object'),
                array('message_type', 'text', 'Type', 'message_type'),
                //CORPS DU MESSSAGE
                array('message_famille', 'text', "famille d'articles", 'message_famille'),
                array('message_societe', 'text', "société", 'message_societe'),
                array('message_commercial', 'text', "Commercial", 'message_commercial'),
                array('message_email', 'text', "e-mail du corps", 'message_email'),
                array('message_telephone', 'text', "telephone", 'message_telephone'),
                //SEGMENT
                array('segment_nom', 'text', "Segment Numéro", 'segment_nom'),
                array('critere', 'text', "Critere", 'critere'),
                array('date_limite_de_fin', 'date', "Date limite de fin", 'date_limite_de_fin'),
                array('quantite_envoyer', 'text', "Quantité à envoyer", 'quantite_envoyer'), //

                array('view_detail', 'text', "View Detail", 'view_detail'),
                array('date_envoi', 'date', "Date envoi", 'date_envoi'),

                array('segment_part', 'text', "Segment Number", 'segment_part'),
                // array('critere_one', 'text', "Critere 1", 'critere_one'), //
                // array('critere_two', 'text', "Critere 2", 'critere_two'), //
                // array('many_criterias', 'text', "As Many Criterias As Necessary", 'many_criterias'), //
                //SUIVI DE I'ENVOI
                array('stats', 'text', "Stats", 'stats'),
                array('quantite_envoyee', 'text', "Quantité envoyée", 'quantite_envoyee'), //
                array('verification_number', 'text', "Verification number sent by manager", 'verification_number'),
                array('open', 'text', "Open", 'open'),
                array('open_pourcentage', 'text', "Open %", 'open_pourcentage'),
                //DELIVRABILITE SUR TEST
                array('deliv_sur_test_orange', 'text', "Orange", 'deliv_sur_test_orange'),
                array('deliv_sur_test_free', 'text', "Free", 'deliv_sur_test_free'),
                array('deliv_sur_test_sfr', 'text', "SFR", 'deliv_sur_test_sfr'),
                array('deliv_sur_test_gmail', 'text', "Gmail", 'deliv_sur_test_gmail'),
                array('deliv_sur_test_yahoo', 'text', "Yahoo", 'deliv_sur_test_yahoo'),
                array('deliv_sur_test_microsoft', 'text', "Microsoft", 'deliv_sur_test_microsoft'),
                array('deliv_sur_test_ovh', 'text', "OVH", 'deliv_sur_test_ovh'),
                array('deliv_sur_test_oneandone', 'text', "1and1", 'deliv_sur_test_oneandone'),
                //DELIVRABILITE REELLE
                array('deliv_reelle_orange', 'text', "Orange", 'deliv_reelle_orange'),
                array('deliv_reelle_free', 'text', "Free", 'deliv_reelle_free'),
                array('deliv_reelle_sfr', 'text', "SFR", 'deliv_reelle_sfr'),
                array('deliv_reelle_gmail', 'text', "Gmail", 'deliv_reelle_gmail'),
                array('deliv_reelle_yahoo', 'text', "Yahoo", 'deliv_reelle_yahoo'),
                array('deliv_reelle_microsoft', 'text', "Microsoft", 'deliv_reelle_microsoft'),
                array('deliv_reelle_ovh', 'text', "OVH", 'deliv_reelle_ovh'),
                array('deliv_reelle_oneandone', 'text', "1and1", 'deliv_reelle_oneandone'),
                //TECHNICAL
                array('operateur_qui_envoie_name', 'text', "Opérateur qui envoie", 'operateur_qui_envoie_name'),
                array('physical_server', 'text', "Physical server", 'physical_server'),
                array('smtp', 'text', "SMTP", 'smtp'),
                array('computer', 'text', "Computer", 'computer'),
            ),
            'read.child'   => array(),
            'write.parent' => array(
                'client'             => array("Client", 'select', array('client', 'ctc_id', 'ctc_nom'), false),
                'commande'           => array("Commande", 'select', array('commande', 'cmd_id', 'cmd_reference'), false),
                'message'            => array("Message", 'select', array('message', 'message_list_id', 'name'), false),
                'segment_numero'     => array("Segment Numéro", 'select', array('segment_numero', 'id', 'value'), false),
                'segment_criteria'   => array("Criteria", 'textarea', 'segment_criteria', false),
                'date_limite_de_fin' => array("Date Limite de Fin", 'date', 'date_limite_de_fin', false),
                'quantite_envoyer'   => array("Quantité à envoyer", 'text', 'quantite_envoyer', false),
            ),
            'write.child'  => array(
                'parent_id'                => array("Parent Sending", 'select', array('parent_id', 'id', 'value'), false),
                'operateur_qui_envoie'     => array("Opérateur qui envoie", 'select', array('operateur_qui_envoie', 'id', 'value'), false),
                'date_envoi'               => array("Date Envoi", 'date', 'date_envoi', false),
                'stats'                    => array("Stats", 'select', array('stats', 'id', 'value'), false),

                'segment_part'             => array("Segment Number", 'select', array('segment_part', 'id', 'value'), false),
                'verification_number'      => array("Verification number sent by manager", 'select', array('verification_number', 'id', 'value'), false),
                'quantite_envoyee'         => array("Quantité envoyee", 'text', 'quantite_envoyee', false),
                'deliv_sur_test_orange'    => array("Orange", 'select', array('deliv_sur_test_orange', 'id', 'value'), false),
                'deliv_sur_test_free'      => array("Free", 'select', array('deliv_sur_test_free', 'id', 'value'), false),
                'deliv_sur_test_sfr'       => array("SFR", 'select', array('deliv_sur_test_sfr', 'id', 'value'), false),
                'deliv_sur_test_gmail'     => array("Gmail", 'select', array('deliv_sur_test_gmail', 'id', 'value'), false),
                'deliv_sur_test_yahoo'     => array("Yahoo", 'select', array('deliv_sur_test_yahoo', 'id', 'value'), false),
                'deliv_sur_test_microsoft' => array("Microsoft", 'select', array('deliv_sur_test_microsoft', 'id', 'value'), false),
                'deliv_sur_test_ovh'       => array("OVH", 'select', array('deliv_sur_test_ovh', 'id', 'value'), false),
                'deliv_sur_test_oneandone' => array("1and1", 'select', array('deliv_sur_test_oneandone', 'id', 'value'), false),
                'physical_server'          => array("Physical server", 'select', array('physical_server', 'id', 'value'), false),
                'smtp'                     => array("SMTP", 'select', array('smtp', 'id', 'value'), false),
                'computer'                 => array("Computer", 'select', array('computer', 'id', 'value'), false),
                'open'                     => array("Open", 'text', 'open', false),
                'open_pourcentage'         => array("Open %", 'text', 'open_pourcentage', false),
            ),
        );

        if ($data != null) {
            $result = $champs[$type . "." . $data];
        } else {
            $result = array_merge($champs[$type . ".parent"], $champs[$type . ".child"]);
        }

        return $result;
    }

    /******************************
     * Liste Livraisons Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $this->load->helper("calcul_factures");
        $table = 't_max_bulk';
        $this->db->start_cache();

        $max_bulk_id = formatte_sql_lien('max_bulk/detail', 'max_bulk_id', 'max_bulk_id');

        /*$date_envoi                = "'' as date_envoi";
        $date_limite_de_fin        = "'' as date_limite_de_fin";*/
        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "''";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";

        $message_numero     = $table . ".message as message_numero";
        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $message_famille    = "vf.vfm_famille AS message_famille";
        $message_societe    = "ts.scv_nom AS message_societe";
        $message_view       = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_max_bulk.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";
        $ht                 = "v_fac.total_ht AS ht";
        $segment_part       = "'' as segment_part";
        //$quantite_envoyer    = "'0' as quantite_envoyer";
        //$quantite_envoyee    = "'0' as quantite_envoyee";
        $verification_number = "'' as verification_number";
        $stats               = "'' as stats";
        $physical_server     = "'' as physical_server";
        $smtp                = "'' as smtp";
        $computer            = "'' as computer";
        $open                = "(select SUM(open) from t_max_bulk_child where parent_id = max_bulk_id AND inactive IS NULL AND deleted IS NULL) as open";
        $open_pourcentage    = "(select ROUND( AVG(open_pourcentage), 1 ) from t_max_bulk_child where parent_id = max_bulk_id AND inactive IS NULL AND deleted IS NULL) as open_pourcentage";
        $quantite_envoyee    = "(select SUM(quantite_envoyee) from t_max_bulk_child where parent_id = max_bulk_id AND inactive IS NULL AND deleted IS NULL) as quantite_envoyee";
        $segment_nom         = "CONCAT(t_segments.id,'-',t_segments.name) as segment_nom";
        $date_envoi          = "'' as date_envoi";

        $this->db->select($table . ".*, max_bulk_id as RowID,
                max_bulk_id as checkbox,
                $client_name,
                $commande_name,
                facture_name,
                $ht,
                $segment_part,
                $segment_nom,
                filtering,
                $quantite_envoyee,
                quantite_envoyer,
                $verification_number,
                $stats,
                $physical_server,
                $smtp,
                $computer,
                $operateur_qui_envoie_name,
                $date_envoi,
                date_limite_de_fin,
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
                $message_commercial
            ", false);
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');
        $this->db->join('t_segments', 't_segments.id=segment_numero', 'left');

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
            $this->db->where('max_bulk_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => "v_fac.total_ht",
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
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
            'message_view'              => 't_max_bulk.message',
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->max_bulk_id . '">';

            $resultat['data'][$i]->deliv_sur_test_orange    = '';
            $resultat['data'][$i]->deliv_sur_test_free      = '';
            $resultat['data'][$i]->deliv_sur_test_sfr       = '';
            $resultat['data'][$i]->deliv_sur_test_gmail     = '';
            $resultat['data'][$i]->deliv_sur_test_yahoo     = '';
            $resultat['data'][$i]->deliv_sur_test_microsoft = '';
            $resultat['data'][$i]->deliv_sur_test_ovh       = '';
            $resultat['data'][$i]->deliv_sur_test_oneandone = '';
            //get delivrabilite reelle
            $resultat['data'][$i]->deliv_reelle_bounce            = '';
            $resultat['data'][$i]->deliv_reelle_bounce_percentage = '';
            $resultat['data'][$i]->deliv_reelle_hard_bounce_rate  = '';
            $resultat['data'][$i]->deliv_reelle_soft_bounce_rate  = '';
            $resultat['data'][$i]->deliv_reelle_orange            = '';
            $resultat['data'][$i]->deliv_reelle_free              = '';
            $resultat['data'][$i]->deliv_reelle_sfr               = '';
            $resultat['data'][$i]->deliv_reelle_gmail             = '';
            $resultat['data'][$i]->deliv_reelle_microsoft         = '';
            $resultat['data'][$i]->deliv_reelle_yahoo             = '';
            $resultat['data'][$i]->deliv_reelle_ovh               = '';
            $resultat['data'][$i]->deliv_reelle_oneandone         = '';

            $resultat['data'][$i]->critere_one    = '';
            $resultat['data'][$i]->critere_two    = '';
            $resultat['data'][$i]->many_criterias = '';

            $resultat['data'][$i]->view_detail = '<a href="#" data-id="' . $resultat['data'][$i]->max_bulk_id . '" class="btn-view-detail">view detail</a>';
            $critere                           = "";

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
        $table = 't_max_bulk';
        $this->db->start_cache();

        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "t_operator.emp_nom";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";

        $message_numero     = $table . ".message as message_numero";
        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $message_famille    = "vf.vfm_famille AS message_famille";
        $message_societe    = "ts.scv_nom AS message_societe";
        $message_view       = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_max_bulk.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";
        $ht                 = "v_fac.total_ht AS ht";

        $this->db->select($table . ".*, t_max_bulk_child.*, max_bulk_id as RowID,
                max_bulk_child_id as checkbox,
                $client_name,
                $commande_name,
                facture_name,
                $ht,
                $operateur_qui_envoie_name,
                t_max_bulk_child.open,
                t_max_bulk_child.open_pourcentage,
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
        $this->db->join('t_max_bulk_child', 'parent_id=max_bulk_id');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
        $this->db->join('t_employes as t_operator', 't_operator.emp_id=operateur_qui_envoie', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');
        $this->db->where('parent_id', $parent_id);

        switch ($void) {
            case 'archived':
                $this->db->where('t_max_bulk_child.inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where('t_max_bulk_child.deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where('t_max_bulk_child.inactive is NULL');
                $this->db->where('t_max_bulk_child.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('max_bulk_child_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => "v_fac.total_ht",
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
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
            'message_view'              => 't_max_bulk.message',
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->max_bulk_id . '">';

            //get delivrabilite reelle
            $resultat['data'][$i]->deliv_reelle_bounce            = '';
            $resultat['data'][$i]->deliv_reelle_bounce_percentage = '';
            $resultat['data'][$i]->deliv_reelle_hard_bounce_rate  = '';
            $resultat['data'][$i]->deliv_reelle_soft_bounce_rate  = '';
            $resultat['data'][$i]->deliv_reelle_orange            = '';
            $resultat['data'][$i]->deliv_reelle_free              = '';
            $resultat['data'][$i]->deliv_reelle_sfr               = '';
            $resultat['data'][$i]->deliv_reelle_gmail             = '';
            $resultat['data'][$i]->deliv_reelle_microsoft         = '';
            $resultat['data'][$i]->deliv_reelle_yahoo             = '';
            $resultat['data'][$i]->deliv_reelle_ovh               = '';
            $resultat['data'][$i]->deliv_reelle_oneandone         = '';

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
            'client_name'               => 'char',
            'commande_name'             => 'char',
            'facture_name'              => 'char',
            'ht'                        => 'int',
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
            'quantite_envoyer'          => 'int',
            'quantite_envoyee'          => 'int',
            'deliv_sur_test_orange'     => 'char',
            'deliv_sur_test_free'       => 'char',
            'deliv_sur_test_sfr'        => 'char',
            'deliv_sur_test_gmail'      => 'char',
            'deliv_sur_test_yahoo'      => 'char',
            'deliv_sur_test_microsoft'  => 'char',
            'deliv_sur_test_ovh'        => 'char',
            'deliv_sur_test_oneandone'  => 'char',
            'verification_number'       => 'char',
            'segment_part'              => 'int',
            'segment_numero'            => 'int',
            'physical_server'           => 'char',
            'smtp'                      => 'char',
            'computer'                  => 'char',
            'open'                      => 'int',
            'open_pourcentage'          => 'double',
        );

        return $filterable_columns;
    }

    /******************************
     * New Livraisons insert into t_max_bulk table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_max_bulk', $data);
    }

    public function nouveau_child($data)
    {
        return $this->_insert('t_max_bulk_child', $data);
    }

    /******************************
     * Detail d'une max_bulk
     ******************************/
    public function detail($id)
    {
        $this->load->helper("calcul_factures");
        //$date_envoi                = formatte_sql_date("date_envoi");
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
                max_bulk_id as checkbox,
                max_bulk_id,
                $client_name,
                $commande_name,
                fac_reference as facture,
                fac_id,
                fac_tva,
                $operateur_qui_envoie_name,
                $date_envoi,
                $date_limite_de_fin,
                quantite_envoyer,
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

        $this->db->where('max_bulk_id = "' . $id . '"');
        $q = $this->db->get('t_max_bulk as tmb');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            //get facture ht
            $facture          = new stdClass;
            $facture->fac_id  = $resultat->fac_id;
            $facture->fac_tva = $resultat->fac_tva;
            $data_factures    = calcul_factures($facture);
            $resultat->ht     = $data_factures->fac_montant_ht;

            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form($id)
    {
        $this->db->select('t_max_bulk.*, message as message_name');
        $this->db->where('max_bulk_id = "' . $id . '"');
        $q = $this->db->get('t_max_bulk');
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
        $this->db->where('max_bulk_child_id = "' . $id . '"');
        $q = $this->db->get('t_max_bulk_child');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating max_bulk data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_max_bulk', $data, $id, 'max_bulk_id');
    }

    public function maj_child($data, $id)
    {
        return $this->_update('t_max_bulk_child', $data, $id, 'max_bulk_child_id');
    }

    /******************************
     * Archive max_bulk data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_max_bulk', $id, 'max_bulk_id', 'inactive');
    }

    public function archive_child($id)
    {
        return $this->_delete('t_max_bulk_child', $id, 'max_bulk_child_id', 'inactive');
    }

    /******************************
     * Archive max_bulk data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_max_bulk', $id, 'max_bulk_id', 'deleted');
    }

    public function remove_child($id)
    {
        return $this->_delete('t_max_bulk_child', $id, 'max_bulk_child_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_max_bulk', $data, $id, 'max_bulk_id');
    }

    public function parent_option()
    {
        $query = $this->db->select("max_bulk_id as id, CONCAT(max_bulk_id,'-',t_message.name) as value")
            ->join('t_message_list as t_message', 't_message.message_list_id=t_max_bulk.message', 'left')
            ->where('t_max_bulk.inactive is NULL')
            ->where('t_max_bulk.deleted is NULL')
            ->get('t_max_bulk');

        return $query->result();
    }

    public function commande($max_bulk_id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tmb.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tmb.max_bulk_id = "' . $max_bulk_id . '"');
        $q      = $this->db->get('t_max_bulk tmb');
        $result = $q->result();
        //that belongs to client
        $new_object                = new stdClass;
        $new_object->cmd_id        = "-1";
        $new_object->cmd_reference = 'Pas de Commande';
        array_unshift($result, $new_object);

        return $result;
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

    /*public function software_option()
    {
    return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }*/

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

    public function computer_option()
    {
        $values = array('comp 1', 'comp 2');
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

    public function calculate_facture($data)
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

    public function get_facture($commande)
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
}
// EOF
