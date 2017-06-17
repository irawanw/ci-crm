<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_manual_sending extends MY_Model
{

    private $used_for_label = 'Manual Sending';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type, $data = null)
    {
        $champs = array(
            'read.parent'  => array(
                /** PARENT FIELDS */
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('manual_sending_id', 'ref', "#ID", 'manual_sending', 'manual_sending_id', 'manual_sending_id'),
                //PARAMETRES
                array('software_nom', 'text', "Software", 'software_nom'),
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
                array('critere', 'text', "Critere", 'critere'), //
                array('date_limite_de_fin', 'date', "Date limite de fin", 'date_limite_de_fin'),
                array('quantite_envoyer', 'text', "Quantité à envoyer", 'quantite_envoyer'),
                array('view_detail', 'text', "View Detail", 'view_detail'),
                array('date_envoi', 'date', "Date envoi", 'date_envoi'),

                /** CHILD FIELDS */
                //SEGMENT
                array('segment_part', 'text', "Segment number", 'segment_part'),
                //array('critere_one', 'text', "Critere 1", 'critere_one'), //
                // array('critere_two', 'text', "Critere 2", 'critere_two'), //
                // array('many_criterias', 'text', "As Many Criterias As Necessary", 'many_criterias'), //
                //ENVOI

                array('quantite_envoyee', 'text', "Quantité envoyée", 'quantite_envoyee'),
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
                //TECHNICAL
                array('operateur_qui_envoie_name', 'text', "Opérateur qui envoie", 'operateur_qui_envoie_name'),
                array('manual_sender_email', 'text', "Manual Sender Email", 'manual_sender_email'),
                array('manual_sender_domain', 'text', "Manual Sender Domain", 'manual_sender_domain'),
                //DELIVRABILITE REELLE
                array('deliv_reelle_orange', 'text', "Orange", 'deliv_reelle_orange'),
                array('deliv_reelle_free', 'text', "Free", 'deliv_reelle_free'),
                array('deliv_reelle_sfr', 'text', "SFR", 'deliv_reelle_sfr'),
                array('deliv_reelle_gmail', 'text', "Gmail", 'deliv_reelle_gmail'),
                array('deliv_reelle_yahoo', 'text', "Yahoo", 'deliv_reelle_yahoo'),
                array('deliv_reelle_microsoft', 'text', "Microsoft", 'deliv_reelle_microsoft'),
                array('deliv_reelle_ovh', 'text', "OVH", 'deliv_reelle_ovh'),
                array('deliv_reelle_oneandone', 'text', "1and1", 'deliv_reelle_oneandone'),
                //MANUAL
            ),
            'read.child'   => array(),
            'write.parent' => array(
                'client'             => array("Client", 'select', array('client', 'id', 'value'), false),
                'commande'           => array("Commande", 'select', array('commande', 'id', 'value'), false),
                'message'            => array("Nom", 'select', array('message_name', 'message_list_id', 'name'), false),
                'segment_numero'     => array("Segment Numéro", 'select', array('segment_numero', 'id', 'value'), false),
                'segment_criteria'   => array("Criteria", 'textarea', 'segment_criteria', false),
                'date_limite_de_fin' => array("Date Limite de Fin", 'date', 'date_limite_de_fin', false),
                'quantite_envoyer'   => array("Quantité à envoyer", 'text', 'quantite_envoyer', false),
            ),
            'write.child'  => array(
                'operateur_qui_envoie'     => array("Opérateur Qui Envoie", 'select', array('operateur_qui_envoie', 'id', 'value'), false),
                'date_envoi'               => array("Date Envoi", 'date', 'date_envoi', false),
                'segment_part'             => array("Segment number", 'select', array('segment_part', 'id', 'value'), false),
                'quantite_envoyee'         => array("Quantité envoyee", 'text', 'quantite_envoyee', false),
                'verification_number'      => array("Validation number by manager", 'select', array('verification_number', 'id', 'value'), false),
                'deliv_sur_test_orange'    => array("Orange/Wanadoo", 'select', array('deliv_sur_test_orange', 'id', 'value'), false),
                'deliv_sur_test_free'      => array("Free", 'select', array('deliv_sur_test_free', 'id', 'value'), false),
                'deliv_sur_test_sfr'       => array("SFR", 'select', array('deliv_sur_test_sfr', 'id', 'value'), false),
                'deliv_sur_test_gmail'     => array("Gmail", 'select', array('deliv_sur_test_gmail', 'id', 'value'), false),
                'deliv_sur_test_microsoft' => array("Microsoft", 'select', array('deliv_sur_test_microsoft', 'id', 'value'), false),
                'deliv_sur_test_yahoo'     => array("Yahoo", 'select', array('deliv_sur_test_yahoo', 'id', 'value'), false),
                'deliv_sur_test_ovh'       => array("OVH", 'select', array('deliv_sur_test_ovh', 'id', 'value'), false),
                'deliv_sur_test_oneandone' => array("1 and 1", 'select', array('deliv_sur_test_oneandone', 'id', 'value'), false),
                'manual_sender'            => array("Manual Sender", 'select', array('manual_sender', 'id', 'value'), false),
                'parent_id'                => array("Parent Sending", 'select', array('parent_id', 'id', 'value'), false),
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
     * Liste Pages_jaunes Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $this->load->helper("calcul_factures");
        $table = 't_manual_sending';
        $this->db->start_cache();

        /*$date_envoi                 = "'' as date_envoi";
        $date_limite_de_fin         = "'' as date_limite_de_fin";*/
        $client                     = "ctc_nom";
        $client_name                = $client . " AS client_name";
        $commande                   = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name              = $commande . " AS commande_name";
        $operateur_qui_envoie       = "''";
        $operateur_qui_envoie_name  = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $manual_sender_email        = "''";
        $manual_sender_email_alias  = $manual_sender_email . " AS manual_sender_email";
        $manual_sender_domain       = "''";
        $manual_sender_domain_alias = $manual_sender_domain . " AS manual_sender_domain";

        $message_numero     = $table . ".message as message_numero";
        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $message_famille    = "vf.vfm_famille AS message_famille";
        $message_societe    = "ts.scv_nom AS message_societe";
        $message_view       = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_manual_sending.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";
        $ht                 = "v_fac.total_ht AS ht";
        $date_envoi         = "'' as date_envoi";
        $segment_part       = "'' as segment_part";
        //$quantite_envoyer    = "(select SUM(quantite_envoyer) from t_manual_sending_child where parent_id = manual_sending_id AND inactive IS NULL AND deleted IS NULL) as quantite_envoyer";
        $quantite_envoyee    = "(select SUM(quantite_envoyee) from t_manual_sending_child where parent_id = manual_sending_id AND inactive IS NULL AND deleted IS NULL) as quantite_envoyee";
        $verification_number = "'' as verification_number";
        $open                = "(select SUM(open) from t_manual_sending_child where parent_id = manual_sending_id AND inactive IS NULL AND deleted IS NULL) as open";
        $open_pourcentage    = "(select ROUND( AVG(open_pourcentage), 1 ) from t_manual_sending_child where parent_id = manual_sending_id AND inactive IS NULL AND deleted IS NULL) as open_pourcentage";
        $segment_nom         = "CONCAT(t_segments.id,'-',t_segments.name) as segment_nom";

        $this->db->select($table . ".*, manual_sending_id as RowID,
                manual_sending_id as checkbox,
                $client_name,
                $commande_name,
                facture_name,
                software_nom,
                filtering,
                $ht,
                $segment_part,
                $segment_nom,
                $quantite_envoyee,
                quantite_envoyer,
                $verification_number,
                $open,
                $open_pourcentage,
                $operateur_qui_envoie_name,
                $date_envoi,
                date_limite_de_fin,
                $manual_sender_email_alias,
                $manual_sender_domain_alias,
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
            ", false);

        $this->db->join('t_softwares', 'software_id=software', 'left');
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
            $this->db->where('manual_sending_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => 'v_fac.total_ht',
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'manual_sender_email'       => $manual_sender_email,
            'manual_sender_domain'      => $manual_sender_domain,
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
            'message_view'              => "t_message.message",
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox                 = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->manual_sending_id . '">';
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

            $resultat['data'][$i]->view_detail = '<a href="#" data-id="' . $resultat['data'][$i]->manual_sending_id . '" class="btn-view-detail">view detail</a>';

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

    /******************************
     * Liste Pages_jaunes Data
     ******************************/
    public function liste_child($void, $parent_id, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $this->load->helper("calcul_factures");
        $table = 't_manual_sending';
        $this->db->start_cache();

        $client                     = "ctc_nom";
        $client_name                = $client . " AS client_name";
        $commande                   = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name              = $commande . " AS commande_name";
        $operateur_qui_envoie       = "t_operator.emp_nom";
        $operateur_qui_envoie_name  = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $manual_sender_email        = "t_production_mails.mail";
        $manual_sender_email_alias  = $manual_sender_email . " AS manual_sender_email";
        $manual_sender_domain       = "t_production_mails.domain";
        $manual_sender_domain_alias = $manual_sender_domain . " AS manual_sender_domain";

        $message_numero     = $table . ".message as message_numero";
        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $message_famille    = "vf.vfm_famille AS message_famille";
        $message_societe    = "ts.scv_nom AS message_societe";
        $message_view       = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',t_manual_sending.message,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";
        $ht                 = "v_fac.total_ht AS ht";

        $this->db->select("manual_sending_child_id as RowID,
                manual_sending_child_id as checkbox,
                '' as manual_sending_id,
                manual_sending_child_id,
                t_manual_sending.software,
                $client_name,
                $commande_name,
                facture_name,
                date_envoi,
                $ht,
                segment_numero,
                t_manual_sending_child.segment_part,
                t_manual_sending_child.quantite_envoyee,
                t_manual_sending_child.verification_number,
                t_manual_sending_child.open,
                t_manual_sending_child.open_pourcentage,
                $operateur_qui_envoie_name,

                t_manual_sending_child.deliv_sur_test_orange,
                t_manual_sending_child.deliv_sur_test_free,
                t_manual_sending_child.deliv_sur_test_sfr,
                t_manual_sending_child.deliv_sur_test_gmail,
                t_manual_sending_child.deliv_sur_test_yahoo,
                t_manual_sending_child.deliv_sur_test_microsoft,
                t_manual_sending_child.deliv_sur_test_ovh,
                t_manual_sending_child.deliv_sur_test_oneandone,

                $manual_sender_email_alias,
                $manual_sender_domain_alias,
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
            ", false);

        $this->db->join('t_manual_sending_child', 'parent_id=manual_sending_id');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_employes as t_operator', 't_operator.emp_id=t_manual_sending_child.operateur_qui_envoie', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_production_mails', 't_production_mails.production_mails_id=t_manual_sending_child.manual_sender', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');
        $this->db->where('parent_id', $parent_id);

        switch ($void) {
            case 'archived':
                $this->db->where('t_manual_sending_child.inactive != "0000-00-00 00:00:00"');
                break;
            case 'deleted':
                $this->db->where('t_manual_sending_child.deleted != "0000-00-00 00:00:00"');
                break;
            case 'all':
                break;
            default:
                $this->db->where('t_manual_sending_child.inactive is NULL');
                $this->db->where('t_manual_sending_child.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('manual_sending_child_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => 'v_fac.total_ht',
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'manual_sender_email'       => $manual_sender_email,
            'manual_sender_domain'      => $manual_sender_domain,
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
            'message_view'              => "t_message.message",
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->manual_sending_id . '">';
            // $resultat['data'][$i]->deliv_sur_test_orange            = '';
            // $resultat['data'][$i]->deliv_sur_test_free            = '';
            // $resultat['data'][$i]->deliv_sur_test_sfr            = '';
            // $resultat['data'][$i]->deliv_sur_test_gmail            = '';
            // $resultat['data'][$i]->deliv_sur_test_yahoo            = '';
            // $resultat['data'][$i]->deliv_sur_test_microsoft        = '';
            // $resultat['data'][$i]->deliv_sur_test_ovh            = '';
            // $resultat['data'][$i]->deliv_sur_test_oneandone        = '';

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

            $resultat['data'][$i]->view_detail = '<a href="#" data-id="' . $resultat['data'][$i]->manual_sending_id . '" class="btn-view-detail">view detail</a>';
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
            'quantite_envoyer'          => 'int',
            'quantite_envoyee'          => 'int',
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
            'manual_sender_email'       => 'char',
            'manual_sender_domain'      => 'char',
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
            'open'                      => 'int',
            'open_pourcentage'          => 'double',
            'message_view'              => 'char',
        );

        return $filterable_columns;
    }

    /******************************
     * New Pages_jaunes insert into t_manual_sending table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_manual_sending', $data);
    }

    /******************************
     * New Pages_jaunes insert into t_manual_sending table
     ******************************/
    public function nouveau_child($data)
    {
        return $this->_insert('t_manual_sending_child', $data);
    }

    /******************************
     * Detail d'une manual_sending
     ******************************/
    public function detail($id)
    {
        $this->load->helper("calcul_factures");

        $table = "t_manual_sending";
        //$date_envoi                 = formatte_sql_date("date_envoi");
        $date_limite_de_fin         = formatte_sql_date("date_limite_de_fin");
        $client                     = "ctc_nom";
        $client_name                = $client . " AS client_name";
        $commande                   = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name              = $commande . " AS commande_name";
        $operateur_qui_envoie       = "t_operator.emp_nom";
        $operateur_qui_envoie_name  = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $manual_sender_email        = "t_production_mails.mail";
        $manual_sender_email_alias  = $manual_sender_email . " AS manual_sender_email";
        $manual_sender_domain       = "t_production_mails.domain";
        $manual_sender_domain_alias = $manual_sender_domain . " AS manual_sender_domain";

        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $message_email      = "t_message.email as message_email";
        $famille            = "vf.vfm_famille";
        $message_famille    = $famille . " AS message_famille";
        $societe            = "ts.scv_nom";
        $message_societe    = $societe . " AS message_societe";
        $message_view       = "t_message.message as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";

        $this->db->select($table . ".*,
                manual_sending_id as checkbox,
                $client_name,
                $commande_name,
                fac_reference as facture,
                fac_id,
                fac_tva,
                $operateur_qui_envoie_name,
                $date_limite_de_fin,
                quantite_envoyer,
                $manual_sender_email_alias,
                $manual_sender_domain_alias,
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
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_production_mails', 't_production_mails.production_mails_id=manual_sender');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');

        $this->db->where('manual_sending_id = "' . $id . '"');
        $q = $this->db->get($table);
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            //get facture ht
            $facture          = new stdClass;
            $facture->fac_id  = $resultat->fac_id;
            $facture->fac_tva = $resultat->fac_tva;
            $data_factures    = calcul_factures($facture);
            $resultat->ht     = $data_factures->fac_montant_ht;

            //get quantity envoyer from t_articles_devis
            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form($id)
    {
        $this->db->select('t_manual_sending.*, message as message_name');
        $this->db->where('manual_sending_id = "' . $id . '"');
        $q = $this->db->get('t_manual_sending');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form_child($id)
    {
        $this->db->select('t_manual_sending_child.*');
        $this->db->where('manual_sending_child_id = "' . $id . '"');
        $q = $this->db->get('t_manual_sending_child');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating manual_sending data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_manual_sending', $data, $id, 'manual_sending_id');
    }

    /******************************
     * Updating manual_sending data
     ******************************/
    public function maj_child($data, $id)
    {
        return $this->_update('t_manual_sending_child', $data, $id, 'manual_sending_child_id');
    }

    /*update message in voir message*/
    public function updateMessage($data, $id)
    {
        return $this->_update('t_message_list', $data, $id, 'message_list_id');
    }
    /******************************
     * Archive manual_sending data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_manual_sending', $id, 'manual_sending_id', 'inactive');
    }

    /******************************
     * Archive manual_sending data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_manual_sending', $id, 'manual_sending_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_manual_sending', $data, $id, 'manual_sending_id');
    }

    /******************************
     * Archive manual_sending data
     ******************************/
    public function archive_child($id)
    {
        return $this->_delete('t_manual_sending_child', $id, 'manual_sending_child_id', 'inactive');
    }

    /******************************
     * Archive manual_sending data
     ******************************/
    public function remove_child($id)
    {
        return $this->_delete('t_manual_sending_child', $id, 'manual_sending_child_id', 'deleted');
    }

    /*public function software_option()
    {
    return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }*/

    public function client_option()
    {
        $this->db->select('ctc_id as id, ctc_nom as value');
        $this->db->order_by('ctc_nom', 'ASC');
        $q = $this->db->get('t_contacts');
        return $q->result();
    }

    public function commande($manual_sending_id = 0, $client_id = 0)
    {
        if ($client_id != 0) {
            $this->db->select("
                    tc.cmd_id as id,
                    tc.cmd_reference as value
                ");
            $this->db->join('t_devis as td', 'td.dvi_client = tms.client', 'inner');
            $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
            $this->db->where('tc.cmd_etat <> 4');
            $this->db->where('td.dvi_client', $client_id);
            if ($manual_sending_id != 0) {
                $this->db->where('tms.manual_sending_id = "' . $manual_sending_id . '"');
            }
            $q        = $this->db->get('t_manual_sending tms');
            $commande = $q->result();
        } else {
            $commande = array();
        }

        $new_object        = new stdClass;
        $new_object->id    = "-1";
        $new_object->value = 'Pas de Commande';
        array_unshift($commande, $new_object);

        return $commande;
    }

    public function commande_by_client($client_id)
    {
        $this->db->select("
                tc.cmd_id as id,
                tc.cmd_reference as value
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tcs.ctc_id', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tcs.ctc_id = "' . $client_id . '"');
        $this->db->where('tc.cmd_etat <> 4');
        $q = $this->db->get('t_contacts tcs');
        return $q->result();
    }

    public function operateur_qui_envoie_option()
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

    public function deliv_sur_test_option()
    {
        $values = array('spam', 'not delivered', 'delivered', 'bl IP', 'bl domain', 'bl message');
        return $this->form_option($values);
    }

    public function message_option()
    {
        $this->db->select('message_list_id as id, name as value');
        $q = $this->db->get('t_message_list');
        return $q->result();
    }

    public function manual_sender_option()
    {
        $query = $this->db->select('tpm.mail as value, tpm.production_mails_id as id')
            ->from('t_production_mails as tpm')
            ->where('tpm.used_for', $this->used_for_label)
            ->get();

        return $query->result();
    }

    public function parent_option()
    {
        $query = $this->db->select("manual_sending_id as id, CONCAT(manual_sending_id,'-',t_message.name) as value")
            ->join('t_message_list as t_message', 't_message.message_list_id=t_manual_sending.message', 'left')
            ->where('t_manual_sending.inactive is NULL')
            ->where('t_manual_sending.deleted is NULL')
            ->get('t_manual_sending');

        return $query->result();
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
