<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Emm_followup extends CI_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('emm_followup/nouveau', 'plus', true, 'emm_followup_nouveau'),
        ),
        array(
            //"Consulter" => array('*emm_followup/detail', 'eye-open', false, 'emm_followup_detail'),
            "Consulter/Modifier"  => array('emm_followup/modification', 'pencil', false, 'emm_followup_modification'),
            "Supprimer" => array('emm_followup/remove', 'trash', false, 'emm_followup_supprimer'),
        ),
        array(
            "Show Deliverance" => array('#', 'th-list', true, 'emm_followup_show_deliverance'),
            "Show Technical"   => array('#', 'th-list', true, 'emm_followup_show_technical'),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'pages_jaunes_voir_liste'),
		),
		array(
            "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"    => array('#', 'book', true, 'export_pdf'),
			"Imprimer"		=> array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_emm_followup');
    }

    public function openemm($id)
    {
        /*
        $emailing = $this->m_emm_followup->get_mailing();
        echo     "<form action='' class='form-control' method='post'>
        <select name='mailing_id'>";
        foreach($emailing as $row){
        echo "<option value='".$row->mailing_id."'>".$row->shortname."</option>";
        }
        echo "<input type='submit' value='Get Data'>";
        echo "</select>";
        echo "</form>";
         */

        $detail = $this->m_emm_followup->get_mailing_detail($id);
        echo json_encode($detail);
    }

    /******************************
     * List of Emm_followup Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archiver');
    }

    public function supprimees()
    {
        $this->liste($id = 0, 'supprimees');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","emm_followup/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'emm_followup/index',
            'detail'             => array('emm_followup/detail', 'emm_followup_id', 'description'),
            'archive'            => array('emm_followup/archive', 'emm_followup_id', 'archive'),
            'champs'             => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('emm_followup_id', 'ref', "#", 'emm_followup', 'emm_followup_id', 'emm_followup_id'),
                array('client_name', 'text', "Client", 'client_name'),
                array('commande_name', 'text', "Commande", 'commande_name'),
                array('message', 'picture', "Message", 'message', 'emm_followup', 'emm_followup_id', 'uploadfile'),
                array('quantite_totale_a_envoyer', 'text', "Quantite totale a envoyer", 'quantite_totale_a_envoyer'),
                array('quantite_envoyee', 'text', "Quantite Envoyee", 'quantite_envoyee'),
                array('type', 'text', "Type", 'type'),
                array('logiciel_utilise', 'text', "Commande", 'logiciel_utilise'),
                array('number_of_opens', 'text', "Nombre d'ouvertures", 'number_of_opens'),
                array('open_rate', 'text', "Open Rate", 'open_rate'),
                array('bounce_rate', 'text', "Bounce Rate", 'bounce_rate'),
                array('hard_bounce_rate', 'text', "Hard Bounce Rate", 'hard_bounce_rate'),
                array('soft_bounce_rate', 'text', "Soft Bounce Rate", 'soft_bounce_rate'),
                array('number_of_clicks', 'text', "Number Of Clicks", 'number_of_clicks'),
                array('click_rate', 'text', "Click Rate", 'click_rate'),
                array('deliverance', 'text', "Deliverance", 'deliverance'),
                array('percentage_delivery', 'text', "Percentage Delivery", 'percentage_delivery'),
                array('percentage_spam', 'text', "Percentage Spam", 'percentage_spam'),
                array('percentage_not_delivered', 'text', "Percentage Not Delivered", 'percentage_not_delivered'),
                array('ip_blacklist', 'text', "Ip Blacklist", 'ip_blacklist'),
                array('message_blacklist', 'text', "Message Blacklist", 'message_blacklist'),
                array('domain_blacklist', 'text', "Domain Blacklist", 'domain_blacklist'),
                array('sender_blacklist', 'text', "Sender Blacklist", 'sender_blacklist'),
                array('server', 'text', "Server", 'server'),
                array('smtp', 'text', "SMTP", 'smtp'),
                array('rotation', 'text', "Rotation", 'rotation'),
            ),
            'filterable_columns' => $this->m_emm_followup->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: emm_followup/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'emm_followup/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'emm_followup/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'emm_followup/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'emm_followup',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'emm_followup',
                ),
            ), true);
        $scripts[] = $this->load->view('emm_followup/liste-js', array(), true);

        // $scripts[]  = $this->load->view("emm_followup/common-js",
        //     array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('emm_followup', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des emm followups",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Emm_followup",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
            'values'       => array(
                'id'           => $id,
                'vues'         => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'      => $toolbar,
                'descripteur'  => $descripteur,
            ),
        );
        $layout = "layouts/datatables";
        $this->load->view($layout, $data);
    }

    /******************************
     * Ajax call for Livraison List
     ******************************/
    public function index_json($id = 0)
    {
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'emm_followup_id' => 't_emm_followup',
            );
            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function archived_json($id = 0)
    {
        $id         = 'archived';
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'emm_followup_id' => 't_emm_followup',
            );
            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function deleted_json($id = 0)
    {
        $id         = 'deleted';
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'emm_followup_id' => 't_emm_followup',
            );
            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    public function all_json($id = 0)
    {
        $id         = 'all';
        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start');

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'emm_followup_id' => 't_emm_followup',
            );
            if ($order_col_id >= 0 && $order_col_id <= count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if (empty($order_col)) {
                    $order_col = 2;
                }

                if (isset($tables[$order_col])) {
                    $order_col = $tables[$order_col] . '.' . $order_col;
                }

                if (!in_array($ordering, array("asc", "desc"))) {
                    $ordering = "asc";
                }

                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_emm_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_emm_followup->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Passe de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    /******************************
     * New Emm Followup
     ******************************/
    public function nouveau()
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'open_rate', 'label' => "Open Rate", 'rules' => 'trim'),
            array('field' => 'quantite_totale_a_envoyer', 'label' => "Quantite Totale a envoyer", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantite Envoyee", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'logiciel_utilise', 'label' => "Logiciel Utilise", 'rules' => 'trim'),
            array('field' => 'bounce_rate', 'label' => "Bounce Rate", 'rules' => 'trim'),
            array('field' => 'hard_bounce_rate', 'label' => "Hard Bounce Rate", 'rules' => 'trim'),
            array('field' => 'soft_bounce_rate', 'label' => "Soft Bounce Rate", 'rules' => 'trim'),
            array('field' => 'click_rate', 'label' => "Click Rate", 'rules' => 'trim'),
            array('field' => 'number_of_clicks', 'label' => "Number Of Clicks", 'rules' => 'trim'),
            array('field' => 'number_of_opens', 'label' => "Nombre of d'ouvertures", 'rules' => 'trim'),
            array('field' => 'deliverance', 'label' => "Deliverance", 'rules' => 'trim'),
            array('field' => 'percentage_delivery', 'label' => "Percentage Delivery", 'rules' => 'trim'),
            array('field' => 'percentage_spam', 'label' => "Percentage Spam", 'rules' => 'trim'),
            array('field' => 'percentage_not_delivered', 'label' => "Percentage Not Delivered", 'rules' => 'trim'),
            array('field' => 'ip_blacklist', 'label' => "Ip Blacklist", 'rules' => 'trim'),
            array('field' => 'message_blacklist', 'label' => "Message Blacklist", 'rules' => 'trim'),
            array('field' => 'domain_blacklist', 'label' => "Domain Blacklist", 'rules' => 'trim'),
            array('field' => 'sender_blacklist', 'label' => "Sender Blacklist", 'rules' => 'trim'),
            array('field' => 'server', 'label' => "Server", 'rules' => 'trim'),
            array('field' => 'smtp', 'label' => "SMTP", 'rules' => 'trim'),
            array('field' => 'rotation', 'label' => "Rotation", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        //upload file when it have value
        if (isset($_FILES['message']['name']) && $_FILES['message']['name'] != '') {
            $result_upload = $this->doupload();
            if ($result_upload['error'] != '') {
                $validation = false;
            }

        }

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'client'                    => $this->input->post('client'),
                'commande'                  => $this->input->post('commande'),
                'quantite_totale_a_envoyer' => $this->input->post('quantite_totale_a_envoyer'),
                'quantite_envoyee'          => $this->input->post('quantite_envoyee'),
                'type'                      => $this->input->post('type'),
                'logiciel_utilise'          => $this->input->post('logiciel_utilise'),
                'open_rate'                 => $this->input->post('open_rate'),
                'bounce_rate'               => $this->input->post('bounce_rate'),
                'hard_bounce_rate'          => $this->input->post('hard_bounce_rate'),
                'soft_bounce_rate'          => $this->input->post('soft_bounce_rate'),
                'click_rate'                => $this->input->post('click_rate'),
                'number_of_clicks'          => $this->input->post('number_of_clicks'),
                'number_of_opens'           => $this->input->post('number_of_opens'),
                'deliverance'               => $this->input->post('deliverance'),
                'percentage_delivery'       => $this->input->post('percentage_delivery'),
                'percentage_spam'           => $this->input->post('percentage_spam'),
                'percentage_not_delivered'  => $this->input->post('percentage_not_delivered'),
                'ip_blacklist'              => $this->input->post('ip_blacklist'),
                'message_blacklist'         => $this->input->post('message_blacklist'),
                'domain_blacklist'          => $this->input->post('domain_blacklist'),
                'sender_blacklist'          => $this->input->post('sender_blacklist'),
                'server'                    => $this->input->post('server'),
                'smtp'                      => $this->input->post('smtp'),
                'rotation'                  => $this->input->post('rotation'),
            );

            //upload file when it have value
            if ($_FILES['message']['name'] != '') {
                $valeurs['message'] = $result_upload['file_name'];
            } else {
                $valeurs['message'] = 0;
            }

            $id = $this->m_emm_followup->nouveau($valeurs);
            if ($id === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            } else {
                $this->session->set_flashdata('success', "Emm_followup a été enregistré avec succès");
                $redirection = $this->session->userdata('_url_retour');
                if (!$redirection) {
                    $redirection = '';
                }

                redirect($redirection);
            }
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                            = new stdClass();
            $listes_valeurs                     = new stdClass();
            $valeurs->client                    = $this->input->post('client');
            $valeurs->commande                  = $this->input->post('commande');
            $valeurs->quantite_totale_a_envoyer = $this->input->post('quantite_totale_a_envoyer');
            $valeurs->quantite_envoyee          = $this->input->post('quantite_envoyee');
            $valeurs->type                      = $this->input->post('type');
            $valeurs->logiciel_utilise          = $this->input->post('logiciel_utilise');
            $valeurs->mailing                   = $this->input->post('mailing');
            $valeurs->open_rate                 = $this->input->post('open_rate');
            $valeurs->bounce_rate               = $this->input->post('bounce_rate');
            $valeurs->hard_bounce_rate          = $this->input->post('hard_bounce_rate');
            $valeurs->soft_bounce_rate          = $this->input->post('soft_bounce_rate');
            $valeurs->click_rate                = $this->input->post('click_rate');
            $valeurs->number_of_clicks          = $this->input->post('number_of_clicks');
            $valeurs->number_of_opens           = $this->input->post('number_of_opens');
            $valeurs->deliverance               = $this->input->post('deliverance');
            $valeurs->percentage_delivery       = $this->input->post('percentage_delivery');
            $valeurs->percentage_spam           = $this->input->post('percentage_spam');
            $valeurs->percentage_not_delivered  = $this->input->post('percentage_not_delivered');
            $valeurs->ip_blacklist              = $this->input->post('ip_blacklist');
            $valeurs->message_blacklist         = $this->input->post('message_blacklist');
            $valeurs->domain_blacklist          = $this->input->post('domain_blacklist');
            $valeurs->sender_blacklist          = $this->input->post('sender_blacklist');
            $valeurs->server                    = $this->input->post('server');
            $valeurs->smtp                      = $this->input->post('smtp');
            $valeurs->rotation                  = $this->input->post('rotation');

            $this->db->order_by('ctc_nom', 'ASC');
            $q                      = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();

            //get commandes that belongs to client
            $commande                  = $this->m_emm_followup->commande(0);
            $new_object                = new stdClass;
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);

            $listes_valeurs->commande         = $commande;
            $listes_valeurs->type             = $this->m_emm_followup->type_option();
            $listes_valeurs->logiciel_utilise = $this->m_emm_followup->logiciel_utilise_option();
            $listes_valeurs->deliverance      = $this->m_emm_followup->deliverance_option();
            $listes_valeurs->rotation         = $this->m_emm_followup->rotation_option();
            $listes_valeurs->mailing          = $this->m_emm_followup->get_mailing();

            $scripts   = array();
            $scripts[] = $this->load->view("emm_followup/form-js",
                array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'client'                    => array("Client", 'select', array('client', 'ctc_id', 'ctc_nom'), false),
                    'commande'                  => array("Commande", 'select', array('commande', 'cmd_id', 'cmd_reference'), false),
                    'message'                   => array("Message", 'upload', 'message', false),
                    'quantite_totale_a_envoyer' => array("Quantite Total A Envoyer", 'text', 'quantite_totale_a_envoyer', false),
                    'quantite_envoyee'          => array("Quantite Envoyee", 'text', 'quantite_envoyee', false),
                    'type'                      => array("Type", 'select', array('type', 'id', 'value'), false),
                    'logiciel_utilise'          => array("Logiciel Utilise", 'select', array('logiciel_utilise', 'id', 'value'), false),
                    'mailing'                   => array("Mailing Name", 'select', array('mailing', 'mailing_id', 'shortname'), false),
                    'open_rate'                 => array("Open Rate", 'text', 'open_rate', false),
                    'bounce_rate'               => array("Bounce Rate", 'text', 'bounce_rate', false),
                    'hard_bounce_rate'          => array("Hard Bounce Rate", 'text', 'hard_bounce_rate', false),
                    'soft_bounce_rate'          => array("Soft Bounce Rate", 'text', 'soft_bounce_rate', false),
                    'click_rate'                => array("Click Rate", 'text', 'click_rate', false),
                    'number_of_clicks'          => array("Number of Clicks", 'text', 'number_of_clicks', false),
                    'number_of_opens'           => array("Nombre of d'ouvertures", 'text', 'number_of_opens', false),
                    'deliverance'               => array("Deliverance", 'select', array('deliverance', 'id', 'value'), false),
                    'percentage_delivery'       => array("Percentage Delivery", 'text', 'percentage_delivery', false),
                    'percentage_spam'           => array("Percentage Spam", 'text', 'percentage_spam', false),
                    'percentage_not_delivered'  => array("Percentage Not Delivery", 'text', 'percentage_not_delivered', false),
                    'ip_blacklist'              => array("Ip Blacklist", 'text', 'ip_blacklist', false),
                    'message_blacklist'         => array("Message Blacklist", 'textarea', 'message_blacklist', false),
                    'domain_blacklist'          => array("Domain Blacklist", 'text', 'domain_blacklist', false),
                    'sender_blacklist'          => array("Sender Blacklist", 'text', 'sender_blacklist', false),
                    'server'                    => array("Server", 'text', 'server', false),
                    'smtp'                      => array("SMTP", 'text', 'smtp', false),
                    'rotation'                  => array("Rotation", 'select', array('rotation', 'id', 'value'), false),
                    //'status' => array("Status",'select',array('status','id','value'),false),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau emmp followup",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Emm_followup",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'emm_followup',
                'methode'        => 'create',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    /******************************
     * Detail of Emm_followup Data
     ******************************/
    public function detail($id)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_emm_followup->detail($id);

            //echo print_r($valeurs); exit();

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'emm_followup/modification', 'primary'),
                array("Archiver", 'emm_followup/archive', 'warning'),
                array("Supprimer", 'emm_followup/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'client'                    => array("Client", 'VARCHAR 50', 'text', 'client_name'),
                    'commande'                  => array("Commande", 'VARCHAR 50', 'text', 'commande_reference'),
                    'message'                   => array("Message", 'VARCHAR 255', 'text', 'message'),
                    'quantite_totale_a_envoyer' => array("Quantite Totale A Envoyer", 'VARCHAR 50', 'text', 'quantite_totale_a_envoyer'),
                    'quantite_envoyee'          => array("Quantite Envoyee", 'VARCHAR 50', 'text', 'quantite_envoyee'),
                    'type'                      => array("Type", 'VARCHAR 50', 'text', 'type'),
                    'logiciel_utilise'          => array("Logiciel Utilise", 'VARCHAR 50', 'text', 'logiciel_utilise'),
                    'open_rate'                 => array("Open Rate", 'VARCHAR 50', 'text', 'open_rate'),
                    'bounce_rate'               => array("Bounce Rate", 'VARCHAR 50', 'text', 'bounce_rate'),
                    'hard_bounce_rate'          => array("Hard Bounce Rate", 'VARCHAR 50', 'text', 'hard_bounce_rate'),
                    'soft_bounce_rate'          => array("Soft Bounce Rate", 'VARCHAR 50', 'text', 'soft_bounce_rate'),
                    'click_rate'                => array("Click Rate", 'VARCHAR 50', 'text', 'click_rate'),
                    'number_of_clicks'          => array("Number of Clicks", 'VARCHAR 50', 'text', 'number_of_clicks'),
                    'number_of_opens'           => array("Nombre d'ouvertures", 'VARCHAR 50', 'text', 'number_of_opens'),
                    'deliverance'               => array("Deliverance", 'VARCHAR 50', 'text', 'deliverance'),
                    'percentage_delivery'       => array("Percentage Delivery", 'VARCHAR 50', 'text', 'percentage_delivery'),
                    'percentage_spam'           => array("Percentage Spam", 'VARCHAR 50', 'text', 'percentage_spam'),
                    'percentage_not_delivered'  => array("Percentage Not Delivered", 'VARCHAR 50', 'text', 'percentage_not_delivered'),
                    'ip_blacklist'              => array("Ip Blacklist", 'VARCHAR 50', 'text', 'ip_blacklist'),
                    'message_blacklist'         => array("Message Blacklist", 'VARCHAR 50', 'text', 'message_blacklist'),
                    'domain_blacklist'          => array("Domain Blacklist", 'VARCHAR 50', 'text', 'domain_blacklist'),
                    'sender_blacklist'          => array("Sender Blacklist", 'VARCHAR 50', 'text', 'sender_blacklist'),
                    'server'                    => array("Server", 'VARCHAR 50', 'text', 'server'),
                    'smtp'                      => array("SMTP", 'VARCHAR 50', 'text', 'smtp'),
                    'rotation'                  => array("Rotation", 'VARCHAR 50', 'text', 'rotation'),

                ),
                'onglets' => array(
                    array("Emm Emailing", array('client', 'commande', 'message', 'quantite_totale_a_envoyer', 'quantite_envoyee', 'type', 'logiciel_utilise', 'open_rate', 'bounce_rate', 'hard_bounce_rate', 'soft_bounce_rate', 'click_rate', 'number_of_clicks', 'number_of_opens', 'deliverance', 'percentage_delivery', 'percentage_spam', 'percentage_not_delivered', 'ip_blacklist', 'message_blacklist', 'domain_blacklist', 'sender_blacklist', 'server', 'smtp', 'rotation')),
                ),
            );

            $data = array(
                'title'        => "Détail of Pages Jaunes",
                'page'         => "templates/detail",
                'menu'         => "Extra|Emm_followup",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'emm_followup',
                'methode'      => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    /******************************
     * Edit function for Emm_followup Data
     ******************************/
    public function modification($id = 0)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'open_rate', 'label' => "Open Rate", 'rules' => 'trim'),
            array('field' => 'quantite_totale_a_envoyer', 'label' => "Quantite Totale a envoyer", 'rules' => 'trim'),
            array('field' => 'quantite_envoyee', 'label' => "Quantite Envoyee", 'rules' => 'trim'),
            array('field' => 'type', 'label' => "Type", 'rules' => 'trim'),
            array('field' => 'logiciel_utilise', 'label' => "Logiciel Utilise", 'rules' => 'trim'),
            array('field' => 'bounce_rate', 'label' => "Bounce Rate", 'rules' => 'trim'),
            array('field' => 'hard_bounce_rate', 'label' => "Hard Bounce Rate", 'rules' => 'trim'),
            array('field' => 'soft_bounce_rate', 'label' => "Soft Bounce Rate", 'rules' => 'trim'),
            array('field' => 'click_rate', 'label' => "Click Rate", 'rules' => 'trim'),
            array('field' => 'number_of_clicks', 'label' => "Number Of Clicks", 'rules' => 'trim'),
            array('field' => 'number_of_opens', 'label' => "Nombre d'ouvertures", 'rules' => 'trim'),
            array('field' => 'deliverance', 'label' => "Deliverance", 'rules' => 'trim'),
            array('field' => 'percentage_delivery', 'label' => "Percentage Delivery", 'rules' => 'trim'),
            array('field' => 'percentage_spam', 'label' => "Percentage Spam", 'rules' => 'trim'),
            array('field' => 'percentage_not_delivered', 'label' => "Percentage Not Delivered", 'rules' => 'trim'),
            array('field' => 'ip_blacklist', 'label' => "Ip Blacklist", 'rules' => 'trim'),
            array('field' => 'message_blacklist', 'label' => "Message Blacklist", 'rules' => 'trim'),
            array('field' => 'domain_blacklist', 'label' => "Domain Blacklist", 'rules' => 'trim'),
            array('field' => 'sender_blacklist', 'label' => "Sender Blacklist", 'rules' => 'trim'),
            array('field' => 'server', 'label' => "Server", 'rules' => 'trim'),
            array('field' => 'smtp', 'label' => "SMTP", 'rules' => 'trim'),
            array('field' => 'rotation', 'label' => "Rotation", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        //upload file when it have value
        if (isset($_FILES['message']['name']) && $_FILES['message']['name'] != '') {
            $result_upload = $this->doupload();
            if ($result_upload['error'] != '') {
                $validation = false;
            }

        }

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'client'                    => $this->input->post('client'),
                'commande'                  => $this->input->post('commande'),
                'quantite_totale_a_envoyer' => $this->input->post('quantite_totale_a_envoyer'),
                'quantite_envoyee'          => $this->input->post('quantite_envoyee'),
                'type'                      => $this->input->post('type'),
                'logiciel_utilise'          => $this->input->post('logiciel_utilise'),
                'open_rate'                 => $this->input->post('open_rate'),
                'bounce_rate'               => $this->input->post('bounce_rate'),
                'hard_bounce_rate'          => $this->input->post('hard_bounce_rate'),
                'soft_bounce_rate'          => $this->input->post('soft_bounce_rate'),
                'click_rate'                => $this->input->post('click_rate'),
                'number_of_clicks'          => $this->input->post('number_of_clicks'),
                'number_of_opens'           => $this->input->post('number_of_opens'),
                'deliverance'               => $this->input->post('deliverance'),
                'percentage_delivery'       => $this->input->post('percentage_delivery'),
                'percentage_spam'           => $this->input->post('percentage_spam'),
                'percentage_not_delivered'  => $this->input->post('percentage_not_delivered'),
                'ip_blacklist'              => $this->input->post('ip_blacklist'),
                'message_blacklist'         => $this->input->post('message_blacklist'),
                'domain_blacklist'          => $this->input->post('domain_blacklist'),
                'sender_blacklist'          => $this->input->post('sender_blacklist'),
                'server'                    => $this->input->post('server'),
                'smtp'                      => $this->input->post('smtp'),
                'rotation'                  => $this->input->post('rotation'),
            );

            //upload file when it have value
            if ($_FILES['message']['name'] != '') {
                $valeurs['message'] = $result_upload['file_name'];
            }

            $resultat = $this->m_emm_followup->maj($valeurs, $id);
            if ($resultat === false) {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('emm_followup/detail/' . $id);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Emm_followup a été modifié";
                }
                $this->session->set_flashdata('success', $message);
                redirect('emm_followup');
            }
        } else {
            $valeurs          = $this->m_emm_followup->detail($id);
            $valeurs->mailing = $this->input->post('mailing');
            // validation en échec ou premier appel : affichage du formulaire
            $listes_valeurs = new stdClass();

            $this->db->order_by('ctc_nom', 'ASC');
            $q                      = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();

            //get commandes that belongs to client
            $commande                  = $this->m_emm_followup->commande($id);
            $new_object                = new stdClass;
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);

            $listes_valeurs->commande         = $commande;
            $listes_valeurs->type             = $this->m_emm_followup->type_option();
            $listes_valeurs->logiciel_utilise = $this->m_emm_followup->logiciel_utilise_option();
            $listes_valeurs->deliverance      = $this->m_emm_followup->deliverance_option();
            $listes_valeurs->rotation         = $this->m_emm_followup->rotation_option();
            $listes_valeurs->mailing          = $this->m_emm_followup->get_mailing();

            $scripts   = array();
            $scripts[] = $this->load->view("emm_followup/form-js",
                array(), true);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'client'                    => array("Client", 'select', array('client', 'ctc_id', 'ctc_nom'), false),
                    'commande'                  => array("Commande", 'select', array('commande', 'cmd_id', 'cmd_reference'), false),
                    'message'                   => array("Message", 'upload', 'message', false),
                    'quantite_totale_a_envoyer' => array("Quantite Total A Envoyer", 'text', 'quantite_totale_a_envoyer', false),
                    'quantite_envoyee'          => array("Quantite Envoyee", 'text', 'quantite_envoyee', false),
                    'type'                      => array("Type", 'select', array('type', 'id', 'value'), false),
                    'logiciel_utilise'          => array("Logiciel Utilise", 'select', array('logiciel_utilise', 'id', 'value'), false),
                    'mailing'                   => array("Mailing Name", 'select', array('mailing', 'mailing_id', 'shortname'), false),
                    'open_rate'                 => array("Open Rate", 'text', 'open_rate', false),
                    'bounce_rate'               => array("Bounce Rate", 'text', 'bounce_rate', false),
                    'hard_bounce_rate'          => array("Hard Bounce Rate", 'text', 'hard_bounce_rate', false),
                    'soft_bounce_rate'          => array("Soft Bounce Rate", 'text', 'soft_bounce_rate', false),
                    'click_rate'                => array("Click Rate", 'text', 'click_rate', false),
                    'number_of_clicks'          => array("Number of Clicks", 'text', 'number_of_clicks', false),
                    'number_of_opens'           => array("Nombre d'ouvertures", 'text', 'number_of_opens', false),
                    'deliverance'               => array("Deliverance", 'select', array('deliverance', 'id', 'value'), false),
                    'percentage_delivery'       => array("Percentage Delivery", 'text', 'percentage_delivery', false),
                    'percentage_spam'           => array("Percentage Spam", 'text', 'percentage_spam', false),
                    'percentage_not_delivered'  => array("Percentage Not Delivery", 'text', 'percentage_not_delivered', false),
                    'ip_blacklist'              => array("Ip Blacklist", 'text', 'ip_blacklist', false),
                    'message_blacklist'         => array("Message Blacklist", 'textarea', 'message_blacklist', false),
                    'domain_blacklist'          => array("Domain Blacklist", 'text', 'domain_blacklist', false),
                    'sender_blacklist'          => array("Sender Blacklist", 'text', 'sender_blacklist', false),
                    'server'                    => array("Server", 'text', 'server', false),
                    'smtp'                      => array("SMTP", 'text', 'smtp', false),
                    'rotation'                  => array("Rotation", 'select', array('rotation', 'id', 'value'), false),
                    //'status' => array("Status",'select',array('status','id','value'),false),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier emm followup",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Emm_followup",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'emm_followup',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_emm_followup->archive($id);
        if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Emm_followup a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Emm_followup Data
     ******************************/
    public function remove($id)
    {
        $resultat = $this->m_emm_followup->remove($id);
        if ($resultat === false) {
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $this->session->set_flashdata('success', "Emm_followup a été supprimé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_emm_followup->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_emm_followup->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_emm_followup->unremove($id);
        }
    }

    public function doupload()
    {
        $upPath = FCPATH . '/fichiers/emm_followup/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "doc|docx|txt|pdf|csv|xls|xlsx|odt|word",
            'overwrite'     => false,
            'max_size'      => "20480000",
            'max_height'    => "768",
            'max_width'     => "1024",
        );
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('message')) {
            $data['file_name'] = '';
            $data['error']     = $this->upload->display_errors();
            $this->session->set_flashdata('warning', $data['error']);
        } else {
            $data_upload       = $this->upload->data();
            $data['file_name'] = $data_upload['file_name'];
            $data['error']     = '';
        }

        return $data;
    }

    public function upload_message()
    {
        $result = $this->doupload();
        if ($result['error'] == '') {
            $valeurs = array(
                'message' => $result['file_name'],
            );
            $this->m_emm_followup->maj($valeurs, $this->input->post('id'));
        }
        $redirection = $this->session->userdata('_url_retour');
        redirect($redirection);
    }
}
// EOF
