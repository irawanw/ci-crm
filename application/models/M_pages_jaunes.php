<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_pages_jaunes extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_openemm');
        $this->db->flush_cache();
        $this->db->reset_query();
    }

    public function get_champs($type, $data = null)
    {
        $champs = array(
            'read.parent'  => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('pages_jaunes_id', 'ref', "Pages jaunes#", 'pages_jaunes', 'pages_jaunes_id', 'pages_jaunes_id'),
                //PARAMETRES
                array('software_nom', 'text', "Software", 'software_nom'),
                //INFO FACTURATION
                array('client_name', 'text', "Client", 'client_name'),
                array('commande_name', 'text', "Commande", 'commande_name'),
                array('facture_name', 'text', "Facture", 'facture_name'),
                array('ht', 'text', "HT", 'ht'),
                //MESSAGE
                array('message_numero', 'text', "Nom", 'message_numero'),
                array('message_name', 'text', "Nom", 'message_name'),
                array('message_view', 'text', "Message", 'message_view'),
                array('message_lien', 'text', 'Lien Pour Télécharger', 'message_lien'),
                array('message_object', 'text', 'Objet Du Message', 'message_object'),
                array('message_type', 'text', 'Type', 'message_type'),
                //CORPS DU MESSAGE
                array('message_famille', 'text', 'Famille d\'articles', 'message_famille'),
                array('message_societe', 'text', 'Société', 'message_societe'),
                array('message_telephone', 'text', 'Telephone', 'message_telephone'),
                array('segment_nom', 'text', "Segment Numéro", 'segment_nom'),
                array('critere', 'text', "Critere", 'critere'),
                array('date_limite_de_fin', 'date', "Date limite de fin", 'date_limite_de_fin'),
                array('quantite_envoyer', 'text', "Quantité à envoyér", 'quantite_envoyer'),
                array('view_detail', 'text', "View Detail", 'view_detail'),
                array('date_envoi', 'date', "Date envoi", 'date_envoi'),
                array('segment_part', 'text', "Segment Number", 'segment_part'),
                //SUIVI DE L'ENVOI
                array('quantite_envoyee', 'text', "Quantité envoyée", 'quantite_envoyee'),
                array('verification_number', 'text', "Verification number sent by manager", 'verification_number'),
                array('open', 'text', "Open", 'open'),
                array('open_pourcentage', 'text', "Open %", 'open_pourcentage'),
                array('number_sent_through', 'text', "Number sent through form", 'number_sent_through'),
                array('number_sent_mail', 'text', "Number sent through mails", 'number_sent_mail'),
                //TECHNICAL
                array('operateur_qui_envoie_name', 'text', "Opérateur qui envoie", 'operateur_qui_envoie_name'),
                array('copy_mail_name', 'text', "Copy Mail", 'copy_mail_name'),

            ),
            'read.child'   => array(),
            'write.parent' => array(
                'client'             => array("Client", 'select', array('client', 'ctc_id', 'ctc_nom'), false),
                'commande'           => array("Commande", 'select', array('commande', 'cmd_id', 'cmd_reference'), false),
                'message'            => array("Nom", 'select', array('message', 'message_list_id', 'name'), false),
                'segment_numero'     => array("Segment Numéro", 'select', array('segment_numero', 'id', 'value'), false),
                'segment_criteria'   => array("Criteria", 'textarea', 'segment_criteria', false),
                'date_limite_de_fin' => array("Date Limite de Fin", 'date', 'date_limite_de_fin', false),
                'quantite_envoyer'   => array("Quantité à envoyer", 'text', 'quantite_envoyer', false),
            ),
            'write.child'  => array(
                'parent_id'            => array("Parent Sending", 'select', array('parent_id', 'id', 'value'), false),
                'date_envoi'           => array("Date Envoi", 'date', 'date_envoi', false),
                'segment_part'         => array("Segment Number", 'select', array('segment_part', 'id', 'value'), false),
                'operateur_qui_envoie' => array("Opérateur qui envoie", 'select', array('operateur_qui_envoie', 'emp_id', 'emp_nom'), false),
                'verification_number'  => array("Validation number by manager", 'select', array('verification_number', 'id', 'value'), false),
                'copy_mail'            => array("Copy Mail", 'select', array('copy_mail', 'production_mails_id', 'mail'), false),
                'number_sent_through'  => array("Number sent through form", 'text', 'number_sent_through', false),
                'number_sent_mail'     => array("Number sent through mails", 'text', 'number_sent_mail', false),
                'quantite_envoyee'     => array("Quantité envoyee", 'text', 'quantite_envoyee', false),
                'open'                 => array("Open", 'text', 'open', false),
                'open_pourcentage'     => array("Open %", 'text', 'open_pourcentage', false),
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
        $table = 't_pages_jaunes';
        $this->db->start_cache();

        /*$date_envoi                = "'' as date_envoi";
        $date_limite_de_fin        = "'' as date_limite_de_fin";*/
        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "''";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";

        $copy_mail_name = "'' AS copy_mail_name";

        $message_numero      = $table . ".message as message_numero";
        $message_name        = "t_message.name as message_name";
        $message_lien        = "t_message.lien_pour_telecharger as message_lien";
        $message_object      = "t_message.object as message_object";
        $message_type        = "t_message.type as message_type";
        $message_telephone   = "t_message.telephone as message_telephone";
        $famille             = "vf.vfm_famille";
        $message_famille     = $famille . " AS message_famille";
        $societe             = "ts.scv_nom";
        $message_societe     = $societe . " AS message_societe";
        $message_view        = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',message_list_id,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $ht                  = "v_fac.total_ht AS ht";
        $segment_part        = "'' as segment_part";
        $open                = "(select SUM(open) from t_pages_jaunes_child where parent_id = pages_jaunes_id AND inactive IS NULL AND deleted IS NULL) as open";
        $open_pourcentage    = "(select ROUND( AVG(open_pourcentage), 1 ) from t_pages_jaunes_child where parent_id = pages_jaunes_id AND inactive IS NULL AND deleted IS NULL) as open_pourcentage";
        $quantite_envoyee    = "(select SUM(quantite_envoyee) from t_pages_jaunes_child where parent_id = pages_jaunes_id AND inactive IS NULL AND deleted IS NULL) as quantite_envoyee";
        $verification_number = "'' as verification_number";
        $number_sent_through = "'0' as number_sent_through";
        $number_sent_mail    = "'0' as number_sent_mail";
        $segment_nom         = "CONCAT(t_segments.id,'-',t_segments.name) as segment_nom";
        $date_envoi          = "'' as date_envoi";
        $this->db->select($table . ".*, pages_jaunes_id as RowID,
                pages_jaunes_id as checkbox,
                $client_name,
                $commande_name,
                facture_name,
                software_nom,
                $ht,
                $segment_part,
                $segment_nom,
                filtering,
                $quantite_envoyee,
                quantite_envoyer,
                $verification_number,
                $operateur_qui_envoie_name,
                $number_sent_through,
                $number_sent_mail,
                $date_envoi,
                date_limite_de_fin,
                $open,
                $open_pourcentage,
                $copy_mail_name,
                $message_numero,
                $message_name,
                $message_view,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone
            ", false);

        $this->db->join('t_softwares', 'software_id=software', 'left');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
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
            $this->db->where('pages_jaunes_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => "v_fac.total_ht",
            'message_numero'            => $table . ".message",
            'message_name'              => 't_message.name',
            'message_view'              => 't_message.message',
            'message_lien'              => 't_message.lien_pour_telecharger',
            'message_object'            => 't_message.object',
            'message_type'              => 't_message.type',
            'message_famille'           => $famille,
            'message_societe'           => $societe,
            'message_telephone'         => 't_message.telephone',
            'copy_mail_name'            => "''",
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->pages_jaunes_id . '">';

            //get facture ht
            // $facture = new stdClass;
            // $facture->fac_id = $resultat['data'][$i]->fac_id;
            // $facture->fac_tva = $resultat['data'][$i]->fac_tva;
            // $data_factures = calcul_factures($facture);
            // $resultat['data'][$i]->ht = $data_factures->fac_montant_ht;

            $resultat['data'][$i]->openemm_number_of_open  = 0; //$openemm_data['openemm_number_of_open'];
            $resultat['data'][$i]->openemm_open_rate       = 0; //($openemm_data['openemm_open_rate']*100).'%';
            $resultat['data'][$i]->openemm_number_of_click = 0; //$openemm_data['openemm_number_of_click'];
            $resultat['data'][$i]->openemm_click_rate      = 0; //($openemm_data['openemm_click_rate']*100).'%';
            $resultat['data'][$i]->view_detail             = '<a href="#" data-id="' . $resultat['data'][$i]->pages_jaunes_id . '" class="btn-view-detail">view detail</a>';

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
        $table = 't_pages_jaunes';
        $this->db->start_cache();

        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "emp_nom";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $copy_mail                 = "t_production_mails.mail";
        $copy_mail_alias           = $copy_mail . " AS copy_mail_name";

        $message_numero    = $table . ".message as message_numero";
        $message_name      = "t_message.name as message_name";
        $message_lien      = "t_message.lien_pour_telecharger as message_lien";
        $message_object    = "t_message.object as message_object";
        $message_type      = "t_message.type as message_type";
        $message_telephone = "t_message.telephone as message_telephone";
        $famille           = "vf.vfm_famille";
        $message_famille   = $famille . " AS message_famille";
        $societe           = "ts.scv_nom";
        $message_societe   = $societe . " AS message_societe";
        $message_view      = "CONCAT('<a href=\"#\" class=\"view-text\" data-id=\"',message_list_id,'\" data-message=\"',t_message.message,'\">','Voir Message','</a>') as message_view";
        $ht                = "v_fac.total_ht AS ht";

        $this->db->select($table . ".*, t_pages_jaunes_child.*, pages_jaunes_id as RowID,
                pages_jaunes_id as checkbox,
                $client_name,
                $commande_name,
                facture_name,
                $ht,
                $operateur_qui_envoie_name,
                t_pages_jaunes_child.open,
                t_pages_jaunes_child.open_pourcentage,
                $copy_mail_alias,
                $message_numero,
                $message_name,
                $message_view,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone
            ", false);

        $this->db->join('t_pages_jaunes_child', 'parent_id=pages_jaunes_id');
        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_employes', 'emp_id=operateur_qui_envoie', 'left');
        $this->db->join('factures_view as v_fac', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_production_mails', 't_production_mails.production_mails_id=copy_mail', 'left');
        $this->db->where('parent_id', $parent_id);

        switch ($void) {
            case 'archived':
                $this->db->where('t_pages_jaunes_child.inactive IS NOT NULL');
                break;
            case 'deleted':
                $this->db->where('t_pages_jaunes_child.deleted IS NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where('t_pages_jaunes_child.inactive is NULL');
                $this->db->where('t_pages_jaunes_child.deleted is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
            $this->db->where('pages_jaunes_child_id', $id);
        }

        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'operateur_qui_envoie_name' => $operateur_qui_envoie,
            'client_name'               => $client,
            'commande_name'             => $commande,
            'ht'                        => "v_fac.total_ht",
            'message_numero'            => $table . ".message",
            'message_name'              => 't_message.name',
            'message_view'              => 't_message.message',
            'message_lien'              => 't_message.lien_pour_telecharger',
            'message_object'            => 't_message.object',
            'message_type'              => 't_message.type',
            'message_famille'           => $famille,
            'message_societe'           => $societe,
            'message_telephone'         => 't_message.telephone',
            'copy_mail_name'            => $copy_mail,
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();
        $this->db->reset_query();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->pages_jaunes_id . '">';

            //get facture ht
            // $facture = new stdClass;
            // $facture->fac_id = $resultat['data'][$i]->fac_id;
            // $facture->fac_tva = $resultat['data'][$i]->fac_tva;
            // $data_factures = calcul_factures($facture);
            // $resultat['data'][$i]->ht = $data_factures->fac_montant_ht;

            $resultat['data'][$i]->openemm_number_of_open  = 0; //$openemm_data['openemm_number_of_open'];
            $resultat['data'][$i]->openemm_open_rate       = 0; //($openemm_data['openemm_open_rate']*100).'%';
            $resultat['data'][$i]->openemm_number_of_click = 0; //$openemm_data['openemm_number_of_click'];
            $resultat['data'][$i]->openemm_click_rate      = 0; //($openemm_data['openemm_click_rate']*100).'%';
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
            'message_view'              => 'char',
            'message_lien'              => 'char',
            'message_object'            => 'char',
            'message_type'              => 'char',
            'message_famille'           => 'char',
            'message_societe'           => 'char',
            'message_telephone'         => 'char',
            'verification_number'       => 'char',
            'copy_mail_name'            => 'char',
            'number_sent_through'       => 'int',
            'number_sent_mail'          => 'int',
            'open'                      => 'int',
            'open_pourcentage'          => 'double',
        );

        return $filterable_columns;
    }

    /******************************
     * New Pages_jaunes insert into t_pages_jaunes table
     ******************************/
    public function nouveau($data)
    {
        return $this->_insert('t_pages_jaunes', $data);
    }

    public function nouveau_child($data)
    {
        return $this->_insert('t_pages_jaunes_child', $data);
    }

    /******************************
     * Detail d'une pages_jaunes
     ******************************/
    public function detail($id)
    {
        $this->load->helper("calcul_factures");
        $table = 't_pages_jaunes';

        //$date_envoi                = formatte_sql_date("date_envoi");
        $date_limite_de_fin        = formatte_sql_date("date_limite_de_fin");
        $client                    = "ctc_nom";
        $client_name               = $client . " AS client_name";
        $commande                  = "CASE WHEN commande = -1 THEN 'Pas de Commande' ELSE cmd_reference END";
        $commande_name             = $commande . " AS commande_name";
        $operateur_qui_envoie      = "t_operator.emp_nom";
        $operateur_qui_envoie_name = $operateur_qui_envoie . " AS operateur_qui_envoie_name";
        $copy_mail                 = "t_production_mails.mail";
        $copy_mail_alias           = $copy_mail . " AS copy_mail_name";

        $message_name       = "t_message.name as message_name";
        $message_lien       = "t_message.lien_pour_telecharger as message_lien";
        $message_object     = "t_message.object as message_object";
        $message_type       = "t_message.type as message_type";
        $message_telephone  = "t_message.telephone as message_telephone";
        $famille            = "vf.vfm_famille";
        $message_famille    = $famille . " AS message_famille";
        $societe            = "ts.scv_nom";
        $message_societe    = $societe . " AS message_societe";
        $message_view       = "t_message.message as message_view";
        $message_commercial = "t_salesman.emp_nom as message_commercial";
        $message_email      = "t_message.email as message_email";

        $this->db->select($table . ".*,
                pages_jaunes_id as checkbox,
                $client_name,
                $commande_name,
                fac_reference as facture,
                fac_id,
                fac_tva,
                $operateur_qui_envoie_name,
                $date_limite_de_fin,
                $copy_mail_alias,
                $message_name,
                $message_view,
                $message_lien,
                $message_object,
                $message_type,
                $message_famille,
                $message_societe,
                $message_telephone,
                $message_commercial,
                $message_email
            ", false);

        $this->db->join('t_contacts', 'ctc_id=client', 'left');
        $this->db->join('t_commandes', 'cmd_id=commande', 'left');
        $this->db->join('t_employes as t_operator', 't_operator.emp_id=operateur_qui_envoie', 'left');
        $this->db->join('t_factures', 'commande=fac_commande', 'left');
        $this->db->join('t_message_list as t_message', 't_message.message_list_id=' . $table . '.message', 'left');
        $this->db->join('v_familles as vf', 'vf.vfm_id = t_message.famille_darticles', 'left');
        $this->db->join('t_societes_vendeuses as ts', 'ts.scv_id = t_message.societe', 'left');
        $this->db->join('t_production_mails', 't_production_mails.production_mails_id=copy_mail', 'left');
        $this->db->join('t_utilisateurs as t_util', 't_message.salesman = t_util.utl_id', 'left');
        $this->db->join('t_employes as t_salesman', 't_salesman.emp_id = t_util.utl_id', 'left');

        $this->db->where('pages_jaunes_id = "' . $id . '"');
        $q = $this->db->get($table);

        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            //get facture ht
            $facture          = new stdClass;
            $facture->fac_id  = $resultat->fac_id;
            $facture->fac_tva = $resultat->fac_tva;
            $data_factures    = calcul_factures($facture);
            $resultat->ht     = $data_factures->fac_montant_ht;

            $resultat->openemm_number_of_open  = 0; //$openemm_data['openemm_number_of_open'];
            $resultat->openemm_open_rate       = 0; //($openemm_data['openemm_open_rate']*100).'%';
            $resultat->openemm_number_of_click = 0; //$openemm_data['openemm_number_of_click'];
            $resultat->openemm_click_rate      = 0; //($openemm_data['openemm_click_rate']*100).'%';

            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Detail d'une pages_jaunes for form update
     ******************************/
    public function detail_for_form($id)
    {
        $this->db->select("*");
        $this->db->where('pages_jaunes_id = "' . $id . '"');
        $q = $this->db->get('t_pages_jaunes as tpj');

        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function detail_for_form_child($id)
    {
        $this->db->select("*");
        $this->db->where('pages_jaunes_child_id = "' . $id . '"');
        $q = $this->db->get('t_pages_jaunes_child as tpj');

        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Updating pages_jaunes data
     ******************************/
    public function maj($data, $id)
    {
        return $this->_update('t_pages_jaunes', $data, $id, 'pages_jaunes_id');
    }

    public function maj_child($data, $id)
    {
        return $this->_update('t_pages_jaunes_child', $data, $id, 'pages_jaunes_child_id');
    }

    /******************************
     * Archive pages_jaunes data
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_pages_jaunes', $id, 'pages_jaunes_id', 'inactive');
    }

    public function archive_child($id)
    {
        return $this->_delete('t_pages_jaunes_child', $id, 'pages_jaunes_child_id', 'inactive');
    }

    /******************************
     * Archive pages_jaunes data
     ******************************/
    public function remove($id)
    {
        return $this->_delete('t_pages_jaunes', $id, 'pages_jaunes_id', 'deleted');
    }

    public function remove_child($id)
    {
        return $this->_delete('t_pages_jaunes_child', $id, 'pages_jaunes_child_id', 'deleted');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('deleted' => null, 'inactive' => null);
        return $this->_update('t_pages_jaunes', $data, $id, 'pages_jaunes_id');
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

    public function parent_option()
    {
        $query = $this->db->select("pages_jaunes_id as id, CONCAT(pages_jaunes_id,'-',t_message.name) as value")
            ->join('t_message_list as t_message', 't_message.message_list_id=t_pages_jaunes.message', 'left')
            ->where('t_pages_jaunes.inactive is NULL')
            ->where('t_pages_jaunes.deleted is NULL')
            ->get('t_pages_jaunes');

        return $query->result();
    }

    public function yes_no_option()
    {
        $result = array();
        $values = array('Non', 'Oui');
        return $this->form_option($values, true);
        return $result;
    }

    public function liste_production_mails()
    {
        $this->db->select("*");
        $this->db->order_by('production_mails_id', 'ASC');
        $this->db->where('inactive is NULL');
        $this->db->where('deleted is NULL');
        return $this->db->get('t_production_mails')->result();
    }

    /*public function software_option()
    {

    return $this->db->select('software_id as id,software_nom as value')->order_by('software_nom', 'ASC')->get('t_softwares')->result();
    }*/

    public function segment_part_option()
    {
        $values = range(1, 100);
        return $this->form_option($values);
    }

    public function commande($id)
    {
        $this->db->select("
                tc.cmd_id,
                tc.cmd_reference
            ");
        $this->db->join('t_devis as td', 'td.dvi_client = tmb.client', 'inner');
        $this->db->join('t_commandes as tc', 'tc.cmd_devis = td.dvi_id');
        $this->db->where('tmb.pages_jaunes_id = "' . $id . '"');
        $q = $this->db->get('t_pages_jaunes tmb');
        return $q->result();
    }

    public function client_option()
    {
        $this->db->order_by('ctc_nom', 'ASC');
        $q = $this->db->get('t_contacts');
        return $q->result();
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

    public function verification_number_option()
    {
        $values = array('yes', 'no');
        return $this->form_option($values);
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
