<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * Property M_amalgame m_amalgame
 */
class Amalgame extends MY_Controller
{
    private $profil;
    private $barre_action = array(
    	"Main" => array(
	        array(
	            "Ajouter<br>un Amalgame" => array('#', 'plus', true, 'amalgame_ajouter'),
	        ),
	        array(
	            "Valider<br>l'Amalgame"   => array('#', 'ok', true, 'amalgame_valider'),
	            "Devalider<br>l'Amalgame" => array('#', 'remove', true, 'amalgame_devalider'),
	        ),
	        array(
	            "Liste des<br>Amalgames" => array('#', 'th-list', true, 'amalgame_liste'),
	        ),
	        array(
	            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
	            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
	            "Imprimer"    => array('#', 'print', true, 'print_list'),
	        ),
        ),
        "Group_nonvalid" => array(
        	array(
	            "Ajouter<br> un document" => array('amalgame/nouveau', 'plus', true, 'group_amalgame_ajouter', null, array('form')),
	        ),
	        array(
	        	"Consulter/Modifier<br> un document"  => array('amalgame/modification', 'pencil', false, 'group_amalgame_modification',null, array('form')),
	        ),
	        array(
	        	"Supprimer<br> un document" => array('amalgame/remove', 'trash', false, 'group_amalgame_supprimer',"Veuillez confirmer la suppression du amalgame", array('confirm-delete' => array('amalgame/group'))),
	       	),
	       	array(
	            "Voir la liste<br>des tous les documents" => array('#', 'th-list', true, 'amalgame_voir_liste'),
	        ),
        ),
        "Group_valid" => array(
	       	array(
	            "Voir la liste<br>des tous les documents" => array('#', 'th-list', true, 'amalgame_voir_liste'),
	        ),
        ),

    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_amalgame');
    }

    private function get_customize_export_data($data)
    {
        $champs = $this->m_amalgame->get_champs('read','child');
        $footer = new stdClass;
        $total_qty = 0;
        $total_eq_t50_a5 = 0;

        foreach($data as $row) {
            $total_qty += (int) $row->qty;
            $total_eq_t50_a5 += (int) $row->eq_t50_a5;
        }

        foreach($champs as $i => $champ) {
            if($champ[0] == 'name') {
                $footer->name = "Total";
            }else if($champ[0] == 'qty') {
                $footer->qty = $total_qty;
            } else if($champ[0] == 'eq_t50_a5') {
                $footer->eq_t50_a5 = $total_eq_t50_a5;
            } else {
                $footer->$champ[0] = null;
            }
        }

        array_push($data, $footer);

        return $data;
    }

    /******************************
     * List of Amalgame Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste();
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archived');
    }

    public function deleted()
    {
        $this->liste($id = 0, 'deleted');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    public function valider($valider_name, $option = '')
    {
        //we will list valider so lets set mode to "valider" it will call valider_json in grid
        //$option is used to view mode in current group
        //for eg: 12-October-2016/deleted means we listed 12-October-2016 which is already suppressed
        $this->liste_group(urldecode($valider_name) . '/' . $option, 'valider');
    }

    public function group($group_name = null, $option = '')
    {
    	$id = 0;
        $id = 0;
        if($option != '') {
            $id = $option;
        }

        $this->liste_group($id.'/'.urldecode($group_name),'group');
    }

    public function view_group()
    {
        $this->liste();
    }

    public function filter_date_livraisons($date = null, $option = '')
    {
        $this->liste_group(urldecode($date) . '/' . $option, 'filter_date_livraisons');
    }

    public function liste($id = 0, $mode = "")
    {
        // commandes globales
        $cmd_globales = array(
            //array("Ajouter un flyer dans l'amalgame","amalgame/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'amalgame/view_group',
            'detail'             => array('amalgame/detail', 'amalgame_group_id', 'description'),
            'archive'            => array('amalgame/archive', 'amalgame_group_id', 'archive'),
            'champs'             => $this->m_amalgame->get_champs('read','parent'),
            'filterable_columns' => $this->m_amalgame->liste_filterable_columns2(),
        );

        //determine json script that will be loaded
        switch ($mode) {
            case 'archived':
                $descripteur['datasource'] = 'amalgame/view_group_archived';
                break;
            case 'deleted':
                $descripteur['datasource'] = 'amalgame/view_group_deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'amalgame/view_group_all';
                break;
            default:
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $external_toolbar_data = array(
            'list_valides'     => $this->m_amalgame->get_valider_name(true),
            'list_non_valides' => $this->m_amalgame->get_valider_name(false),
            'is_liste_group'   => true,
        );

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'amalgame',
                'methode'               => 'index',
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => $external_toolbar_data,
            ), true);

        $scripts[] = $this->load->view("amalgame/liste-js", array(
            'url_upload' => site_url('amalgame/upload_fischiers_imprimer'),
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('amalgame', $this->session->id);
        $data = array(
            'title'        => "Liste des Amalgames",
            'page'         => "amalgame/datatables",
            'menu'         => "Extra|Amalgame",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action['Main'], //enable sage bar action
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

    public function liste_group($id = 0, $mode = 0)
    {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter un flyer dans l'amalgame","amalgame/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'amalgame/group',
            'detail'             => array('amalgame/detail', 'amalgame_id', 'description'),
            'archive'            => array('amalgame/archive', 'amalgame_id', 'archive'),
            'champs'             => $this->m_amalgame->get_champs('read','child'),
            'filterable_columns' => $this->m_amalgame->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        switch ($mode) {
            case 'deleted':
                $descripteur['datasource'] = 'amalgame/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'amalgame/all';
                break;
            case 'valider':
                $descripteur['datasource'] = 'amalgame/valider';
                break;
            case 'group':
                $descripteur['datasource'] = 'amalgame/group';
                break;
            case 'filter_date_livraisons':
                $descripteur['datasource'] = 'amalgame/filter_date_livraisons';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $is_group_valid = $this->m_amalgame->is_group_valid($this->uri->segment(3));
        $barre_action   =  $is_group_valid == 0 ? $this->barre_action['Group_nonvalid'] : $this->barre_action['Group_valid']; 

        $external_toolbar_data = array(
            'list_valides'     => $this->m_amalgame->get_valider_name(true),
            'list_non_valides' => $this->m_amalgame->get_valider_name(false),
            'group_valid'      => $is_group_valid,
            'barre_action'     => $barre_action
        );

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'amalgame',
                'methode'               => 'index',
                'mass_action_checkbox'  => true,
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'recherche_toolbar'     => false,
                'external_toolbar'      => 'custom-toolbar', //amalgame use custom-toolbor.php
                'external_toolbar_data' => $external_toolbar_data, //data to be passed for custom-toolbar
                'aggregates'            => array(
                    array(
                        'field'  => 'eq_t50_a5',
                        'mode'   => 'sum',
                        'footer' => 'sum',
                    ),
                ),
            ), true);

        $data = array(
            'group_valid' => $this->m_amalgame->is_group_valid($this->uri->segment(3)),
        );
        $scripts[] = $this->load->view("amalgame/liste-group-js", array(
            'data'       => $data,
            'url_upload' => site_url('amalgame/upload_fischiers_imprimer'),
        ), true);
        $scripts[] = $this->load->view("amalgame/common-js", array(), true);
        $scripts[] = $this->load->view("amalgame/form-js", array(
            'field_name' => 'fischiers_imprimer',
            'url_upload' => site_url('amalgame/doupload_multiple'),
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('amalgame', $this->session->id);
        $data = array(
            'title'        => "Liste des Amalgames",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Amalgame",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action['Main'], //enable sage bar action
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

    public function index_json($id = 0, $group = '')
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_amalgame->liste($id, $pagelength, $pagestart, $filters, $group);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'amalgame_id' => 't_amalgame',
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

                $resultat = $this->m_amalgame->liste($id, $pagelength, $pagestart, $filters, $group, $order_col, $ordering);
            } else {
                $resultat = $this->m_amalgame->liste($id, $pagelength, $pagestart, $filters, $group);
            }
        }

        if($this->input->post('export')) {
            $customize_data = $this->get_customize_export_data($resultat['data']);
            //action export data xls
            $amalgame_name  = $this->uri->segment(4);
            $champs = $this->m_amalgame->get_champs('read','child');
            $params = array(
                'records' => $customize_data, 
                'columns' => $champs,
                'filename' => 'Amalgames',
                'headers' => array(
                    array('text' => "Listes de Amalgame"),
                    array('text' => $amalgame_name),
                ),
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    public function deleted_json($id = 0)
    {
        $this->index_json('deleted');
    }
    public function all_json($id = 0)
    {
        $this->index_json('all');
    }

    public function valider_json()
    {
        $this->index_json();
    }

    public function group_json($id = 0, $group = null)
    {
        $this->index_json($id, $group);
    }

    public function filter_date_livraisons_json($date)
    {
        $this->index_json('filter_date_livraisons_json/' . $date);
    }

    public function view_group_json($id = 0, $group_name = '')
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_amalgame->liste_group($id, $pagelength, $pagestart, $filters, $group_name);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'amalgame_id' => 't_amalgame_group',
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

                $resultat = $this->m_amalgame->liste_group($id, $pagelength, $pagestart, $filters, $group_name, $order_col, $ordering);
            } else {
                $resultat = $this->m_amalgame->liste_group($id, $pagelength, $pagestart, $filters, $group_name);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_amalgame->get_champs('read','parent');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Amalgame_groups'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    public function view_group_deleted_json($id = 0)
    {
        $this->view_group_json('deleted');
    }

    public function view_group_all_json($id = 0)
    {
        $this->view_group_json('all');
    }

    public function view_group_archived_json($id = 0)
    {
        $this->view_group_json('archived');
    }

    public function commande_option($id = 0)
    {
        //if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_amalgame->commande_by_client($id);
        $results  = json_decode(json_encode($resultat), true);
        echo "<option value='0' selected='selected'>(choisissez)</option>";
        echo "<option value='-1'>Pas de Commande</option>";
        foreach ($results as $row) {
            echo "<option value='" . $row['cmd_id'] . "'>" . $row['cmd_reference'] . "</option>";
        }
    }

    /******************************
     * New Amalgame
     ******************************/
    public function nouveau($id=0,$group_name = null, $ajax=false)
    {
        if ($group_name == null) {
            $this->session->set_flashdata('warning', "No selected amalgame name");
            redirect('amalgame');
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'qty', 'label' => "Quantité", 'rules' => 'trim'),
            array('field' => 'type_document', 'label' => "Type de Document", 'rules' => 'trim'),
            array('field' => 'denomination_taille', 'label' => "Dénomination Taille", 'rules' => 'trim'),
            array('field' => 'largeur', 'label' => "Largeur", 'rules' => 'trim'),
            array('field' => 'longueur', 'label' => "Longueur", 'rules' => 'trim'),
            array('field' => 'eq_t50_a5', 'label' => "Equivalen T 50000 A5", 'rules' => 'trim'),
            array('field' => 'bat', 'label' => "Bat", 'rules' => 'trim'),
            array('field' => 'lien_fichier', 'label' => "Lien pour télécharger le fichier", 'rules' => 'trim'),
        );

        //when type document is depliant
        //type_plus and plis become mandatory
        if ($this->input->post('type_document') == 'Dépliant') {
            $config[] = array('field' => 'plis', 'label' => "Nombre Plis", 'rules' => 'trim|required');
            $config[] = array('field' => 'type_plis', 'label' => "Type Plis", 'rules' => 'trim|required');
            $config[] = array('field' => 'denomination_taille_ferme', 'label' => "Dénomination Taille Format Fermé", 'rules' => 'trim|required');
            $config[] = array('field' => 'largeur_ferme', 'label' => "Largeur Format Fermé", 'rules' => 'trim|required');
            $config[] = array('field' => 'longueur_ferme', 'label' => "Longueur Format Fermé", 'rules' => 'trim|required');
        }

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'client'                    => $this->input->post('client'),
                'commande'                  => $this->input->post('commande'),
                'denomination_taille'       => $this->input->post('denomination_taille'),
                'type_document'             => $this->input->post('type_document'),
                'qty'                       => $this->input->post('qty'),
                'plis'                      => $this->input->post('plis'),
                'type_plis'                 => $this->input->post('type_plis'),
                'eq_t50_a5'                 => $this->input->post('eq_t50_a5'),
                'bat'                       => $this->input->post('bat'),
                'largeur'                   => $this->input->post('largeur'),
                'longueur'                  => $this->input->post('longueur'),
                'denomination_taille_ferme' => $this->input->post('denomination_taille_ferme'),
                'largeur_ferme'             => $this->input->post('largeur_ferme'),
                'longueur_ferme'            => $this->input->post('longueur_ferme'),
                'lien_fichier'              => $this->input->post('lien_fichier'),
            );

            if ($group_name) {
                $result_group = $this->m_amalgame->get_group(array('name' => $group_name));

                $valeurs['amalgame_group_id'] = $result_group ? $result_group->amalgame_group_id : 0;
            }

            $id = $this->m_amalgame->nouveau($valeurs);

            if($id === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
            	//upload file when it have value
                if ($this->input->post('fischiers_imprimer') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('fischiers_imprimer'));
                    $this->m_files->update_row($id, $file_ids);
                }

                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Amalgame a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                            = new stdClass();
            $listes_valeurs                     = new stdClass();
            $valeurs->client                    = $this->input->post('client');
            $valeurs->commande                  = $this->input->post('commande');
            $valeurs->denomination_taille       = $this->input->post('denomination_taille');
            $valeurs->type_document             = $this->input->post('type_document');
            $valeurs->qty                       = $this->input->post('qty');
            $valeurs->plis                      = $this->input->post('plis');
            $valeurs->type_plis                 = $this->input->post('type_plis');
            $valeurs->eq_t50_a5                 = $this->input->post('eq_t50_a5');
            $valeurs->bat                       = $this->input->post('bat');
            $valeurs->fischiers_imprimer        = $this->input->post('fischiers_imprimer');
            $valeurs->largeur                   = $this->input->post('largeur');
            $valeurs->longueur                  = $this->input->post('longueur');
            $valeurs->denomination_taille_ferme = $this->input->post('denomination_taille_ferme');
            $valeurs->largeur_ferme             = $this->input->post('largeur_ferme');
            $valeurs->longueur_ferme            = $this->input->post('longueur_ferme');
            $valeurs->lien_fichier              = $this->input->post('lien_fichier');

            $this->db->order_by('ctc_nom', 'ASC');
            $q                      = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();

            $new_object                = new stdClass;
            $commande                  = $this->m_amalgame->commande(0);
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);
            $listes_valeurs->commande = $commande;

            $listes_valeurs->bat                       = $this->m_amalgame->yes_no_option();
            $listes_valeurs->type_document             = $this->m_amalgame->type_document_option();
            $listes_valeurs->denomination_taille       = $this->m_amalgame->denomination_taille_option();
            $listes_valeurs->denomination_taille_ferme = $this->m_amalgame->denomination_taille_option();
            $listes_valeurs->type_plis                 = $this->m_amalgame->type_plis_option();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_amalgame->get_champs('write','child'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un flyer dans l'amalgame",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Amalgame",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'amalgame',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Amalgame Data
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
            $valeurs = $this->m_amalgame->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'amalgame/modification', 'primary'),
                array("Archiver", 'amalgame/archive', 'warning'),
                array("Supprimer", 'amalgame/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'client'                    => array("Client", 'VARCHAR 50', 'text', 'client_name'),
                    'commande'                  => array("Commande", 'VARCHAR 50', 'text', 'commande_reference'),
                    'denomination_taille'       => array("Dénomination Taille Formet Ouvert", 'VARCHAR 50', 'text', 'denomination_taille'),
                    'largeur'                   => array("Largeur Formet Ouvert", 'VARCHAR 50', 'text', 'largeur'),
                    'longueur'                  => array("Longueur<br>Formet Ouvert", 'VARCHAR 50', 'text', 'longueur'),
                    'denomination_taille_ferme' => array("Dénomination Taille Formet Fermé", 'VARCHAR 50', 'text', 'denomination_taille_ferme'),
                    'largeur_ferme'             => array("Largeur Formet Fermé", 'VARCHAR 50', 'text', 'largeur_ferme'),
                    'longueur_ferme'            => array("Longueur Formet Fermé", 'VARCHAR 50', 'text', 'longueur_ferme'),
                    'type_document'             => array("Type de Document", 'VARCHAR 50', 'text', 'type_document'),
                    'qty'                       => array("Quantité", 'VARCHAR 50', 'text', 'qty'),
                    'plis'                      => array("Nombre Plis", 'VARCHAR 50', 'text', 'plis'),
                    'type_plis'                 => array("Type Plis", 'VARCHAR 50', 'text', 'type_plis'),
                    'eq_t50_a5'                 => array("Equivalen T 50000 A5", 'VARCHAR 50', 'text', 'eq_t50_a5'),
                    'bat'                       => array("Bat", 'VARCHAR 50', 'text', 'bat_text'),
                    'fischiers_imprimer'        => array("Fischiers Imprimer", 'VARCHAR 50', 'text', 'fischiers_imprimer'),
                    'lien_fichier'              => array("Lien pour télécharger le fichier", 'VARCHAR 50', 'text', 'lien_fichier'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of suivi des amalgame",
                'page'         => "templates/detail",
                'menu'         => "Extra|Amalgame",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'amalgame',
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
     * Edit function for Amalgame Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $amalgame = $this->m_amalgame->detail($id);
        if ($this->m_amalgame->is_valider($id)) {
            $this->session->set_flashdata('danger', "Désolé, vous ne pouvez pas modifier les données qui ont été validées.");
            redirect('amalgame/group/' . ($amalgame->group_name));
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'commande', 'label' => "Commande", 'rules' => 'trim|required'),
            array('field' => 'qty', 'label' => "Quantité", 'rules' => 'trim'),
            array('field' => 'type_document', 'label' => "Type de Document", 'rules' => 'trim'),
            array('field' => 'denomination_taille', 'label' => "Dénomination Taille", 'rules' => 'trim'),
            array('field' => 'largeur', 'label' => "Largeur", 'rules' => 'trim'),
            array('field' => 'longueur', 'label' => "Longueur", 'rules' => 'trim'),
            array('field' => 'eq_t50_a5', 'label' => "Equivalen T 50000 A5", 'rules' => 'trim'),
            array('field' => 'bat', 'label' => "Bat", 'rules' => 'trim'),
            array('field' => 'lien_fichier', 'label' => "lien pour télécharger le fichier", 'rules' => 'trim'),
        );

        //when type document is depliant
        //type_plus and plis become mandatory
        if ($this->input->post('type_document') == 'Dépliant') {
            $config[] = array('field' => 'plis', 'label' => "Nombre Plis", 'rules' => 'trim|required');
            $config[] = array('field' => 'type_plis', 'label' => "Type Plis", 'rules' => 'trim|required');
            $config[] = array('field' => 'denomination_taille_ferme', 'label' => "Dénomination Taille Format Fermé", 'rules' => 'trim|required');
            $config[] = array('field' => 'largeur_ferme', 'label' => "Largeur Format Fermé", 'rules' => 'trim|required');
            $config[] = array('field' => 'longueur_ferme', 'label' => "Longueur Format Fermé", 'rules' => 'trim|required');
        }

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'client'                    => $this->input->post('client'),
                'commande'                  => $this->input->post('commande'),
                'denomination_taille'       => $this->input->post('denomination_taille'),
                'largeur'                   => $this->input->post('largeur'),
                'longueur'                  => $this->input->post('longueur'),
                'denomination_taille_ferme' => $this->input->post('denomination_taille_ferme'),
                'largeur_ferme'             => $this->input->post('largeur_ferme'),
                'longueur_ferme'            => $this->input->post('longueur_ferme'),
                'type_document'             => $this->input->post('type_document'),
                'qty'                       => $this->input->post('qty'),
                'plis'                      => $this->input->post('plis'),
                'type_plis'                 => $this->input->post('type_plis'),
                'eq_t50_a5'                 => $this->input->post('eq_t50_a5'),
                'bat'                       => $this->input->post('bat'),
                'lien_fichier'              => $this->input->post('lien_fichier'),
            );


            $resultat = $this->m_amalgame->maj($valeurs, $id);

            $redirection = 'amalgame/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
            	//upload file when it have value
                if ($this->input->post('fischiers_imprimer') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('fischiers_imprimer'));
                    $this->m_files->update_row($id, $file_ids);
                }

                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Amalgame a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                     );
                 }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                return;
            }

            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_amalgame->detail($id);

            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('client');
            if (isset($valeur)) {
                $valeurs->client = $valeur;
            }

            $valeur_fischiers_imprimer       = $this->input->post('fischiers_imprimer');
            if (isset($valeur)) {
                $valeurs->fischiers_imprimer = $valeur_fischiers_imprimer;
            }

            $this->db->order_by('ctc_nom', 'ASC');
            $q                      = $this->db->get('t_contacts');
            $listes_valeurs->client = $q->result();

            //get commandes that belongs to client
            $commande                  = $this->m_amalgame->commande($id);
            $new_object                = new stdClass;
            $new_object->cmd_id        = "-1";
            $new_object->cmd_reference = 'Pas de Commande';
            array_unshift($commande, $new_object);

            $listes_valeurs->commande                  = $commande;
            $listes_valeurs->bat                       = $this->m_amalgame->yes_no_option();
            $listes_valeurs->type_document             = $this->m_amalgame->type_document_option();
            $listes_valeurs->denomination_taille       = $this->m_amalgame->denomination_taille_option();
            $listes_valeurs->denomination_taille_ferme = $this->m_amalgame->denomination_taille_option();
            $listes_valeurs->type_plis                 = $this->m_amalgame->type_plis_option();

            $scripts   = array();
         
            $descripteur = array(
                'champs'  => $this->m_amalgame->get_champs('write','child'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier amalgame",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Amalgame",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'amalgame',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    public function doupload_multiple()
    {
        $this->load->model('m_files');
        $upPath = FCPATH . '/fichiers/amalgame/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "*",
            'overwrite'     => false,
            'max_size'      => "20480000",
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('files')) {
            echo json_encode(array('status' => false, 'error' => $error));
        } else {
            $upload_data = $this->upload->data();

            $data = array(
                'name'     => 'amalgame_fischiers_imprimer',
                'filename' => $upload_data['file_name'],
                'path'     => $upload_data['full_path'],
            );

            $resultat = $this->m_files->nouveau($data);

            if ($resultat) {
                echo json_encode(array('status' => true, 'id' => $resultat));
            } else {
                echo json_encode(array('status' => false, 'error' => "Failed insert files"));
            }

        }
    }

    public function doupload_file()
    {
        $upPath = FCPATH . '/fichiers/amalgame/';
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }
        $config = array(
            'upload_path'   => $upPath,
            'allowed_types' => "*",
            'overwrite'     => false,
            'max_size'      => "20480000",
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('files')) {
            $data['file_name'] = '';
            $data['error']     = $this->upload->display_errors();
        } else {
            $data          = $this->upload->data();
            $data['error'] = null;
        }
        return $data;
    }

    public function upload_fischiers_imprimer()
    {
        $this->load->model('m_files');
        $data = $this->doupload_file();
        if ($data['error'] == '') {
            $valeurs = array(
                'row_id'   => $this->input->post('upload_id'),
                'name'     => 'amalgame_fischiers_imprimer',
                'filename' => $data['file_name'],
                'path'     => $data['full_path'],
            );

            $resultat = $this->m_files->nouveau($valeurs);

            echo json_encode(array('status' => true, 'id' => $resultat));
        } else {
            echo json_encode(array('status' => false, 'error' => $data['error']));
        }

    }

    /******************************
     * Archive Amalgame Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_amalgame->archive($id);
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
            $this->session->set_flashdata('success', "Amalgame a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Amalgame Data
     ******************************/
    public function remove($id,$ajax=false)
    {
    	if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_amalgame->remove($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Amalgame a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    /******************************
     * Mass Action Amalgame Data
     ******************************/
    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->unremove($id);
        }
    }

    /******************************
     * Valider-Devalider Amalgame
     ******************************/
    public function set_valider($valider_name = null)
    {
        if ($valider_name == null) {
            redirect('amalgame');
        } else {
            $valider_name = urldecode($valider_name);
            $data         = array('name' => $valider_name);
            $this->session->set_flashdata('success', "Amalgame ont été validées avec succès comme " . $valider_name);
            $this->m_amalgame->valider($data);
            redirect('amalgame/group/' . $valider_name);
        }
    }
    public function unset_valider($valider_name = null)
    {
        if ($valider_name == null) {
            redirect('amalgame');
        } else {
            $valider_name = urldecode($valider_name);
            $data         = array('name' => $valider_name);
            $this->session->set_flashdata('success', "Amalgame n'ont pas été validées comme as " . $valider_name);
            $this->m_amalgame->revalider($data);
            redirect('amalgame/group/' . $valider_name);
        }
    }

    public function set_group()
    {
        $data = array(
            'name'                           => $this->input->post('amalgame_name'),
            'date_de_livraison_del_amalgame' => formatte_date_to_bd($this->input->post('date_de_livraison_del_amalgame')),
            'date_envoi_bat_global'          => formatte_date_to_bd($this->input->post('date_envoi_bat_global')),
            'date_livraison_reelle'          => formatte_date_to_bd($this->input->post('date_livraison_reelle')),
        );

        $this->m_amalgame->set_group($data);

        $this->session->set_flashdata('success', "Amalgame have been successfully saved as " . $data['name']);
        redirect('amalgame/group/' . $data['name']);
    }

    /******************************
     * Mass Archiver Group Data
     ******************************/
    public function mass_archiver_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->archive_group($id);
        }
    }

    /******************************
     * Mass Action Group Amalgame Data
     ******************************/
    public function mass_remove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->remove_group($id);
        }
    }

    public function mass_unremove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_amalgame->unremove_group($id);
        }
    }

    public function get_fischiers_imprimer_files($id){
        $files = $this->m_amalgame->get_fischiers_imprimer_files($id);
        foreach($files as $file){
            echo '<div id="file-container-'.$file->file_id.'">
                    <button type="button" onclick="showConfirmRemoveFile(' . $file->file_id . ')" class="btn btn-warning btn-xs btn-delete-file">x</button>
                    <a target="_blank" href="'.base_url('fichiers/amalgame').'/'.$file->filename.'">'.$file->filename.'</a></div>';
        }

        echo '<div class="alert alert-danger" style="display:none;" id="confirm-remove-file">
                        <p>Etes-vous certain de vouloir supprimer le fichier?</p>
                        <button onclick="hideConfirmRemoveFile()" type="button" class="btn btn-default">Non</button>
                        <button class="btn btn-warning" type="button" id="btn-remove-file-ok">Oui</button>
                      </div>';
    }
}
// EOF
