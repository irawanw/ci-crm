<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
 * Property M_gestion_heures m_gestion_heures 
 */
class Gestion_heures extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Main" => array(
            array(
                "Creer Une<br>Feuille D'Heures" => array('#', 'plus', true, 'gestion_heures_nouveau'),
            ),
            array(
                //"Valider<br>La Feuille D'Heures"   => array('#', 'ok', true, 'gestion_heures_valider'),
                "Valider<br>La Feuille D'Heures"   => array('#', 'ok', true, 'gestion_heures_valides'),
                "Devalider<br>La Feuille D'Heures" => array('#', 'remove', true, 'gestion_heures_devalider'),
            ),
            array(
                "Liste des<br>Feuille D'Heures" => array('gestion_heures/index', 'th-list', true, 'gestion_heures_group_liste'),
            ),
            array(
            	"Tableau<br>IK URSSAF" => array('#', 'list-alt', true, 'gestion_heures_tableau_ik_urssaf'),
            ),
            array(
                "Export xlsx"                     => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"                      => array('#', 'book', true, 'export_pdf'),
                "Imprimer<br>La Feuille D'Heures" => array('#', 'print', true, 'print_list'),
            ),
        ),
        "Group_nonvalid" => array(
            array(
                "Ajouter<br> une ligne" => array('gestion_heures/nouveau', 'plus', true, 'group_gestion_heures_ajouter', null, array('form')),
            ),
            array(
                "Consulter/Modifier<br> une ligne"  => array('gestion_heures/modification', 'pencil', false, 'group_gestion_heures_modification',null, array('form')),
            ),
            array(
                "Supprimer<br> une ligne" => array('gestion_heures/remove', 'trash', false, 'group_gestion_heures_supprimer',"Veuillez confirmer la suppression du amalgame", array('confirm-delete' => array('gestion_heures/group'))),
            ),
            array(
                "Voir la liste<br>complete des ligne" => array('#', 'th-list', true, 'gestion_heures_voir_liste'),
            ),
        ),
        "Group_valid" => array(
            array(
                "Voir la liste<br>complete des ligne" => array('#', 'th-list', true, 'gestion_heures_voir_liste'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_gestion_heures');
    }

    private function get_customize_export_data($data, $nombreDeHeures=0)
    {
        $champs = $this->m_gestion_heures->get_champs('read','child');
        $totalUrbain = 0;
        $totalRural = 0;
        $totalHeuresDeDistributionUrbain = 0;
        $totalHeuresDeDistributionRural = 0;
        $totalControleAutres = 0;
        $totalFraisKilometres = 0;
        $nombreAbsence = 0;
        $nombreSupplementaires = 0;

        $totalBoitesDistribuees = 0;
        $totalHeuresTravaillees = 0;
        foreach($data as $row) {
            $totalUrbain += $row->urbain;
            $totalRural += $row->rural;
            $totalHeuresDeDistributionUrbain += $row->heures_de_distribution_urbain;
            $totalHeuresDeDistributionRural += $row->heures_de_distribution_rural;
            $totalControleAutres += $row->controle_autres;
            $totalFraisKilometres += $row->frais_kilometres;
        }

        $totalBoitesDistribuees = $totalUrbain + $totalRural;
        $totalHeuresTravaillees = $totalHeuresDeDistributionUrbain + $totalHeuresDeDistributionRural + $totalControleAutres;

        if($nombreDeHeures != 0) {
            $result = $nombreDeHeures - $totalHeuresTravaillees;
            if($result > 0) {
                $nombreAbsence = $result;
            } else {
                $nombreSupplementaires = $result;
            }
        }

        $footer1 = new stdClass;
        $footer2 = new stdClass;
        $footer3 = new stdClass;
        $footer4 = new stdClass;
        $footer5 = new stdClass;
        $footer6 = new stdClass;

        foreach($champs as $champ) {
            if($champ[0] == 'gestion_heures_id') {
                $footer1->gestion_heures_id = "Total";
                $footer2->gestion_heures_id = "Total Boites Distribuees";
                $footer3->gestion_heures_id = "Total Heures Travaillees";
                $footer4->gestion_heures_id = "Nombre D'heures Devant Etre Travaillees Dans Le Mois";
                $footer5->gestion_heures_id = "Nombre D'heures Absence";
                $footer6->gestion_heures_id = "Nombre D'heures Supplementaires";
            } else if($champ[0] == 'urbain') {
                $footer1->urbain = $totalUrbain;
                $footer2->urbain = $totalBoitesDistribuees;
                $footer3->urbain = $totalHeuresTravaillees;
                $footer4->urbain = $nombreDeHeures;
                $footer5->urbain = $nombreAbsence;
                $footer6->urbain = $nombreSupplementaires;
            } else {
                $footer1->$champ[0] = null;
                $footer2->$champ[0] = null;
                $footer3->$champ[0] = null;
                $footer4->$champ[0] = null;
                $footer5->$champ[0] = null;
                $footer6->$champ[0] = null;
            }
        }

        $footer1->heures_de_distribution_urbain = $totalHeuresDeDistributionUrbain;
        $footer1->rural = $totalRural;
        $footer1->heures_de_distribution_rural = $totalHeuresDeDistributionRural;
        $footer1->controle_autres = $totalControleAutres;
        $footer1->frais_kilometres = $totalFraisKilometres;

        array_push($data, $footer1);
        array_push($data, $footer2);
        array_push($data, $footer3);
        array_push($data, $footer4);
        array_push($data, $footer5);
        array_push($data, $footer6);

        return $data;
    }

    /******************************
     * List of Livraisons Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {		
		$this->liste();
    }

    public function salarie($employe = null, $annee = null, $mois = null, $option = '')
    {
        $id = 0;
        if($option != '') {
            $id = $option;
        }

        if ($employe && $annee && $mois) {
            $id .= '/'.$employe.'-'.$annee.'-'.$mois;
            $this->liste_group($id, '');
        } else {
            redirect('gestion_heures');
        }
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
	
    public function view_group()
    {       
        $this->liste();
    }		

    public function liste_group($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            //array("Nouvelle livraison","livraisons/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'gestion_heures/index',
            'detail'             => array('gestion_heures/detail', 'gestion_heures_id', 'description'),
            'archive'            => array('gestion_heures/archive', 'gestion_heures_id', 'archive'),
            'champs'             => $this->m_gestion_heures->get_champs('read','child'),
            'filterable_columns' => $this->m_gestion_heures->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: livraisons/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'gestion_heures/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'gestion_heures/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'gestion_heures/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        //get employes list
        $this->db->order_by('emp_nom', 'ASC');
        $q                                               = $this->db->get('t_employes');
        $external_toolbar_data['employee_list']          = $q->result();
        $external_toolbar_data['urbain_div']             = $this->get_detail_group('urbain_div');
        $external_toolbar_data['rural_div']              = $this->get_detail_group('rural_div');        
        $external_toolbar_data['indemnite_kilometrique'] = $this->get_indemnite_kilometrique_salarie();
        $external_toolbar_data['list_valides']           = $this->m_gestion_heures->get_valider(true);
        $external_toolbar_data['list_non_valides']       = $this->m_gestion_heures->get_valider(false);

        if ($this->uri->segment(2) == 'salarie') {
            $this->db->select('emp_nom');
            $this->db->where('emp_id', $this->uri->segment(3));
            $employe                          = $this->db->get('t_employes')->row();
            $external_toolbar_data['emp_nom'] = $employe->emp_nom;

            $employes = $this->uri->segment(3);
            $annee    = $this->uri->segment(4);
            $mois     = $this->uri->segment(5);

            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $group_id                             = $this->m_gestion_heures->check_group($criteria);
            $is_group_valid = $this->m_gestion_heures->is_group_valid($group_id);
            $barre_action   =  $is_group_valid == 0 ? $this->barre_action['Group_nonvalid'] : $this->barre_action['Group_valid']; 
            
            $external_toolbar_data['group_id']    = $group_id;
            $external_toolbar_data['group_valid'] = $is_group_valid;
            $external_toolbar_data['barre_action'] = $barre_action;
        }

        $common_data['employee_list'] = $q->result();
        $common_data['group_id']      = '';
        $common_data['nombre_de_heures'] = $this->get_detail_group('nombre_de_heures');

        if ($this->uri->segment(2) == 'salarie') {
            $employes = $this->uri->segment(3);
            $annee    = $this->uri->segment(4);
            $mois     = $this->uri->segment(5);

            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $group_id                   = $this->m_gestion_heures->check_group($criteria);
            $common_data['group_id']    = $group_id;
            $common_data['group_valid'] = $this->m_gestion_heures->is_group_valid($group_id);
			$common_data['group_name'] 	= $this->get_detail_group('controle');
        }

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'gestion_heures',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => $external_toolbar_data,
            ), true);
        $scripts[] = $this->load->view("gestion_heures/liste-group-js", array(
            'data' => $common_data,
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('gestion_heures', $this->session->id);
        $data = array(
            'title'        => "Comptes-rendus Salariés",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Gestion heures",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action["Main"], //enable sage bar action
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
	
    public function liste($id = 0, $mode = "")
    {
        // commandes globales
        $cmd_globales = array(           
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'gestion_heures/view_group',
            'detail'             => array('gestion_heures/detail', 'gestion_group_id', 'description'),
            'archive'            => array('gestion_heures/archive', 'gestion_group_id', 'archive'),
            'champs'             => $this->m_gestion_heures->get_champs('read','parent'),
            'filterable_columns' => $this->m_gestion_heures->liste_filterable_columns2(),
        );

        //determine json script that will be loaded
        //for eg: livraisons/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archived':
                $descripteur['datasource'] = 'gestion_heures/view_group_archived';
                break;
            case 'deleted':
                $descripteur['datasource'] = 'gestion_heures/view_group_deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'gestion_heures/view_group_all';
                break;
            default:
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();
		
		$external_toolbar_data['list_valides']           = $this->m_gestion_heures->get_valider(true);
        $external_toolbar_data['list_non_valides']       = $this->m_gestion_heures->get_valider(false);
        $external_toolbar_data['is_liste_group']         = TRUE;

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'gestion_heures',
                'methode'               => 'index',
				'external_toolbar'		=> 'custom-toolbar',
				'external_toolbar_data'	=> $external_toolbar_data,
            ), true);
		
		$this->db->order_by('emp_nom', 'ASC');
        $q                              = $this->db->get('t_employes');
        $common_data['employee_list']	= $q->result();
        $common_data['group_name']      = "";
        $scripts[] = $this->load->view("gestion_heures/liste-js", 
			array(
				'data' => $common_data
			), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('gestion_heures', $this->session->id);
        $data = array(
            'title'        => "Comptes-rendus Salariés",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Gestion Heures",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action["Main"], //enable sage bar action
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

    public function index_json($id = '', $group=null)
    {
        $salarie = "";

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
            $resultat = $this->m_gestion_heures->liste($id, $group, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'gestion_heures_id' => 't_gestion_heures',
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

                $resultat = $this->m_gestion_heures->liste($id, $group, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_gestion_heures->liste($id, $group, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $group_arr = explode("-", $group);
            $nombreDeHeures = $this->input->post('nombreDeHeures');
            $employes = $group_arr[0];
            $annee = $group_arr[1];
            $mois = $group_arr[2];

            $group_name = $this->db->query("SELECT `controle` FROM `t_gestion_group` WHERE `employes`=$employes AND `annee`=$annee AND `mois`=$mois")->row()->controle;

            $champs = $this->m_gestion_heures->get_champs('read','child');
            $params = array(
                'records' => $this->get_customize_export_data($resultat['data'], $nombreDeHeures), 
                'columns' => $champs,
                'filename' => 'Comptes_rendus_salaries',
                'headers' => array(
                    array('text' => "Comptes rendus salariés"),
                    array('text' => $group_name)
                )
            );

            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }
	
    public function view_group_json($id = '')
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
            $resultat = $this->m_gestion_heures->liste_group($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'gestion_group_id' => 't_gestion_group',
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
				
                $resultat = $this->m_gestion_heures->liste_group($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_gestion_heures->liste_group($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_gestion_heures->get_champs('read','parent');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Comptes_rendus_salarie_groups'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }	

    public function archived_json($id = 0)
    {
        $resultat = $this->m_gestion_heures->liste('archived');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function deleted_json($id = 0)
    {
        $resultat = $this->m_gestion_heures->liste('deleted');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    public function all_json($id = 0)
    {
        $resultat = $this->m_gestion_heures->liste('all');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function view_group_archived_json($id = 0)
    {
        $this->view_group_json("archived");
    }

    public function view_group_deleted_json($id = 0)
    {
        $this->view_group_json("deleted");
    }

    public function view_group_all_json($id = 0)
    {
        $this->view_group_json("all");
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id=0, $group=null, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        if ($group == null) {
            $this->session->set_flashdata('danger', "Please select salarie before create new");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }

        //check if group already exist, created if not
        $group_array = explode("-", $group);
        $salarie = array(
            'employes' => $group_array[0],
            'annee'    => $group_array[1],
            'mois'     => $group_array[2],
        );
        $group_id = $this->m_gestion_heures->check_group($salarie);

        // règles de validation
        $config = array(
            //array('field'=>'employes','label'=>"Employes",'rules'=>'trim|required'),
            //array('field'=>'annee','label'=>"Annee",'rules'=>'trim|required|integer'),
            //array('field'=>'mois','label'=>"Mois",'rules'=>'trim'),
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim|required'),
            array('field' => 'urbain', 'label' => "Urbain", 'rules' => 'trim'),
            array('field' => 'rural', 'label' => "Rural", 'rules' => 'trim'),
            array('field' => 'controle_autres', 'label' => "Heures de controle ou autres", 'rules' => 'trim'),
            array('field' => 'kilometres', 'label' => "Kilometres", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                //'employes' => $this->input->post('employes'),
                //'annee' => $this->input->post('annee'),
                //'mois' => $this->input->post('mois'),
                'ville'                => $this->input->post('ville'),
                'urbain'               => $this->input->post('urbain'),
                'rural'                => $this->input->post('rural'),
                'controle_autres'      => $this->input->post('controle_autres'),
                'kilometres'           => $this->input->post('kilometres'),
                'gestion_heures_group' => $group_id,
            );
            $id = $this->m_gestion_heures->nouveau($valeurs);

            if($id === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Gestion heure a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();
            //$valeurs->employes = $this->input->post('employes');
            //$valeurs->annee = $this->input->post('annee');
            //$valeurs->mois = $this->input->post('mois');
            $valeurs->ville           = $this->input->post('ville');
            $valeurs->urbain          = $this->input->post('urbain');
            $valeurs->rural           = $this->input->post('rural');
            $valeurs->controle_autres = $this->input->post('controle_autres');
            $valeurs->kilometres      = $this->input->post('kilometres');

            $this->db->order_by('emp_nom', 'ASC');
            $q                        = $this->db->get('t_employes');
            $listes_valeurs->employes = $q->result();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_gestion_heures->get_champs('write','parent'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Gestion heures",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Gestion heures",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'gestion_heures',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Livraisons Data
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
            $valeurs = $this->m_gestion_heures->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'gestion_heures/modification', 'primary'),
                array("Archiver", 'gestion_heures/archive', 'warning'),
                array("Supprimer", 'gestion_heures/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'employee_name'    => array("Employes", 'VARCHAR 50', 'text', 'employee_name'),
                    'annee'            => array("Annee", 'VARCHAR 50', 'text', 'annee'),
                    'mois'             => array("Mois", 'VARCHAR 50', 'text', 'mois'),
                    'ville'            => array("Ville", 'VARCHAR 50', 'text', 'ville'),
                    'urbain'           => array("Urbain", 'VARCHAR 50', 'text', 'urbain'),
                    'rural'            => array("Rural", 'VARCHAR 50', 'text', 'rural'),
                    'controle_autres'  => array("Heures de controle ou autres", 'VARCHAR 50', 'text', 'controle_autres'),
                    'kilometres'       => array("Kilometres", 'VARCHAR 50', 'text', 'kilometres'),
                    'frais_kilometres' => array("Frais kilométriques", 'DATE', 'text', 'frais_kilometres'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of suivi des Gestion heures",
                'page'         => "templates/detail",
                'menu'         => "Extra|Gestion_heures",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'gestion_heures',
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
     * Edit function for Livraisons Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {

        if ($this->m_gestion_heures->is_valider($id)) {
            //$this->session->set_flashdata('danger', "Sorry you can't edit validated data");
            //$temp = $this->m_gestion_heures->detail($id);
            //redirect('gestion_heures/salarie/' . ($temp->employes) . '/' . ($temp->annee) . '/' . ($temp->mois));

            $message = "Sorry you can't edit validated data";
            $this->my_set_action_response($ajax, false, $message);
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            //array('field'=>'employes','label'=>"Employes",'rules'=>'trim|required'),
            //array('field'=>'annee','label'=>"Annee",'rules'=>'trim|required|integer'),
            //array('field'=>'mois','label'=>"Mois",'rules'=>'trim'),
            array('field' => 'ville', 'label' => "Ville", 'rules' => 'trim|required'),
            array('field' => 'urbain', 'label' => "Urbain", 'rules' => 'trim'),
            array('field' => 'rural', 'label' => "Rural", 'rules' => 'trim'),
            array('field' => 'controle_autres', 'label' => "Heures de controle ou autres", 'rules' => 'trim'),
            array('field' => 'kilometres', 'label' => "Kilometres", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                //'employes' => $this->input->post('employes'),
                //'annee' => $this->input->post('annee'),
                //'mois' => $this->input->post('mois'),
                'ville'           => $this->input->post('ville'),
                'urbain'          => $this->input->post('urbain'),
                'rural'           => $this->input->post('rural'),
                'controle_autres' => $this->input->post('controle_autres'),
                'kilometres'      => $this->input->post('kilometres'),
            );

            $resultat = $this->m_gestion_heures->maj($valeurs, $id);
            
            $redirection = 'gestion_heures/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {

                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Gestion heure a été modifié";
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
            $valeurs = $this->m_gestion_heures->detail($id);

            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('employes');
            if (isset($valeur)) {
                $valeurs->employes = $valeur;
            }
            $this->db->order_by('emp_nom', 'ASC');
            $q                        = $this->db->get('t_employes');
            $listes_valeurs->employes = $q->result();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_gestion_heures->get_champs('write','parent'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Modifier Gestion heures",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Gestion heures",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'gestion_heures',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_gestion_heures->archive($id);
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
            $this->session->set_flashdata('success', "Gestion heures a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Livraisons Data
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

        $resultat = $this->m_gestion_heures->remove($id);

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
            $this->my_set_action_response($ajax, true, "Gestion heure a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->unremove($id);
        }
    }

    public function set_valider()
    {
        $data = $this->input->post();
        $this->session->set_flashdata('success', "Gestion heures have been successfully validated");
        $this->m_gestion_heures->valider($data);
        redirect('gestion_heures');
    }

    public function valides($employes = '', $annee = '', $mois = '')
    {
        $data = array(
            'employes' => $employes,
            'annee'    => $annee,
            'mois'     => $mois,
        );
        $this->session->set_flashdata('success', "Gestion heures have been successfully validated");
        $this->m_gestion_heures->valides($data);
        redirect('gestion_heures/salarie/' . $employes . '/' . $annee . '/' . $mois);
    }

    public function unset_valider($employes = '', $annee = '', $mois = '')
    {
        $data = array(
            'employes' => $employes,
            'annee'    => $annee,
            'mois'     => $mois,
        );
        $this->session->set_flashdata('success', "Gestion heures have been successfully revalidated");
        $this->m_gestion_heures->revalider($data);
        redirect('gestion_heures/salarie/' . $employes . '/' . $annee . '/' . $mois);
    }

    public function set_urbain_group($employes = null, $annee = null, $mois = null)
    {

        if ($employes != null && $annee != null && $mois != null) {
            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $urbain_div = $this->input->post('urbain_div');
            $resultat   = $this->m_gestion_heures->set_urbain_group($criteria, $urbain_div);
            $response   = array('status' => $resultat);

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function set_rural_group($employes = null, $annee = null, $mois = null)
    {
        if ($employes != null && $annee != null && $mois != null) {
            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $rural_div = $this->input->post('rural_div');
            $resultat  = $this->m_gestion_heures->set_rural_group($criteria, $rural_div);
            $response  = array('status' => $resultat);

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function set_nombre_de_heures($employes = null, $annee = null, $mois = null)
    {
        if ($employes != null && $annee != null && $mois != null) {
            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $data = array(
                'nombre_de_heures' => $this->input->post('nombre_de_heures')
            );

            $resultat  = $this->m_gestion_heures->set_value_group($criteria, $data);
            $response  = array('status' => $resultat);

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function get_detail_group($selector)
    {
        if ($this->router->fetch_method() == 'salarie') {
            $employes = $this->uri->segment(3);
            $annee    = $this->uri->segment(4);
            $mois     = $this->uri->segment(5);

            $criteria = array(
                'employes' => $employes,
                'annee'    => $annee,
                'mois'     => $mois,
            );

            $resultat = $this->m_gestion_heures->get_group($criteria);

            if ($resultat) {
                return $resultat->$selector;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function get_indemnite_kilometrique_salarie()
    {
        if ($this->router->fetch_method() == 'salarie') {
            $employes = $this->uri->segment(3);

            $resultat = $this->m_gestion_heures->get_indemnite_kilometrique_salarie($employes);

            return $resultat;
        } else {
            return null;
        }
    }
	
	public function get_group_list(){
		$resultat = $this->m_gestion_heures->get_group_list($this->input->post());		
		foreach($resultat as $row){
			echo "<a href='".site_url('gestion_heures')."/salarie/".$row->employes."/".$row->annee."/".$row->mois."'>".$row->controle."</a><br>";
		}
	}

    /**
     * Get Data to showing on Table IK URSSAF
     * @return [type] [description]
     */
	public function get_tableau_ik_urssaf()
	{
		$data = array(
			array("3 cv et moins", "d x 0,410", "(d x 0,245) + 824", "d x 0,286"),
			array("4 cv", "d x 0,493", "(d x 0,277) + 1 082", "d x 0,323"),
			array("5 cv", "d x 0,543", "(d x 0,305) + 1 188", "d x 0,364"),
			array("6 cv", "d x 0,568", "(d x 0, 320) + 1 244", "d x 0,382"),
			array("7 cv et plus", "d x 0,595", "(d x 0,337) + 1 288", "d x 0, 401")
		);

		echo json_encode($data);
	}
    /******************************
     * Mass Archiver Group Data
     ******************************/
    public function mass_archiver_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->archive_group($id);
        }
    }
    /**
     * Mass remove group
     * @return [type] [description]
     */
    public function mass_remove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->remove_group($id);
        }
    }
    /**
     * Mass unremove group
     * @return [type] [description]
     */
    public function mass_unremove_group()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_gestion_heures->unremove_group($id);
        }
    }


    public  function calculate_indem_kilo($employes, $kilometres)
    {
        $employes = (int) $employes;
        $kilometres = (int) $kilometres;
        $result = $this->m_gestion_heures->calculate_indem_kilo($employes, $kilometres);

        echo json_encode(array('indem_kilo' => $result));
    }
}
// EOF
