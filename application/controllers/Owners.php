<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_owners m_owners
*/
class Owners extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('owners/nouveau', 'plus', true, 'owners_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('owners/detail', 'eye-open', false, 'owners_detail'),
            "Consulter/Modifier"  => array('owners/modification', 'pencil', false, 'owners_modification',null, array('form')),
            "Supprimer" => array('owners/remove', 'trash', false, 'owners_supprimer',"Veuillez confirmer la suppression du owner", array('confirm-delete' => array('owners/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
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
        $this->load->model('m_owners');
    }

    /******************************
     * List of owners Data
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
            // array("Ajouter un e-mailing pages jaunes","owners/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        $menu_extra = array(
            array(
                'name' => "Serveurs",
                'url' => site_url('servers')
            ),
            array(
                'name' => "Hebergeur",
                'url' => site_url('hosts')
            ),
            array(
                'name' => "Propriétaire",
                'url' => site_url('owners')
            ),
            array(
                'name' => "Domains",
                'url' => site_url('domains')
            ),
            array(
                'name' => "IPS",
                'url' => site_url('ips')
            ),
            array(
                'name' => "Cartes Bleues",
                'url' => site_url('cartes_blues')
            ),
        );

        // descripteur
        $descripteur = array(
            'datasource'         => 'owners/index',
            'detail'             => array('owners/detail', 'owner_id', 'description'),
            'archive'            => array('owners/archive', 'owner_id', 'archive'),
            'champs'             => $this->m_owners->get_champs('read'),
            'filterable_columns' => $this->m_owners->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: owners/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'owners/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'owners/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'owners/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'owners',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("owners/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('owners', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Propriétaires",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Owners",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
            'menu_extra'   => $menu_extra,
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_owners->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'owner_id' => 't_owners',
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

                $resultat = $this->m_owners->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_owners->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_owners->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Owners'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    public function archived_json($id = 0)
    {
        $this->index_json('archived');
    }

    public function deleted_json($id = 0)
    {
		$this->index_json('deleted');
    }
	
    public function all_json($id = 0)
    {
		$this->index_json('all');
    }

    /******************************
     * New Livraison
     ******************************/
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'email', 'label' =>  "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'telephone', 'label' =>  "Téléphone", 'rules' => 'trim'),
            array('field' => 'adresse', 'label' =>  "Adresse", 'rules' => 'trim'),
            array('field' => 'contact', 'label' =>  "Contact", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'nom'                           => $this->input->post('nom'),
                'email'                         => $this->input->post('email'),
                'telephone'                     => $this->input->post('telephone'),
                'adresse'                       => $this->input->post('adresse'),
                'contact'                       => $this->input->post('contact'),             
            );

            $resultat = $this->m_owners->nouveau($valeurs);

            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordadd',
                         'id' => $resultat,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                 );
                $this->my_set_action_response($ajax, true, "Propriétaire a été enregistré avec succès",'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection); 
            
           
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                = new stdClass();
            $listes_valeurs         = new stdClass();

            $valeurs->nom                           = $this->input->post('nom');
            $valeurs->email                         = $this->input->post('email');
            $valeurs->telephone                     = $this->input->post('telephone');
            $valeurs->adresse                       = $this->input->post('adresse');
            $valeurs->contact                       = $this->input->post('contact');       

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_owners->get_champs('write'),
                'onglets' => array(),
            );
     

            $data = array(
                'title' => "Ajouter un nouveau Propriétaire",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Owners",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'owners',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Edit function for Pages_jaunes Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'email', 'label' =>  "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'telephone', 'label' =>  "Téléphone", 'rules' => 'trim'),
            array('field' => 'adresse', 'label' =>  "Adresse", 'rules' => 'trim'),
            array('field' => 'contact', 'label' =>  "Contact", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'nom'                           => $this->input->post('nom'),
                'email'                         => $this->input->post('email'),
                'telephone'                     => $this->input->post('telephone'),
                'adresse'                       => $this->input->post('adresse'),
                'contact'                       => $this->input->post('contact'),              
            );

            $resultat = $this->m_owners->maj($valeurs, $id);
            
            $redirection = 'owners/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Propriétaire a été modifié";
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
            $valeurs = $this->m_owners->detail($id);
            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_owners->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Modifier Propriétaire",
                'page' => "templates/form",
                'menu' => "Extra|Edit Owners",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'owners',
                'methode' => 'modification',
                'descripteur' => $descripteur,                
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Detail of Pages_jaunes Data
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
            $valeurs = $this->m_owners->detail($id);

            $cmd_globales = array();

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'owners/modification', 'primary'),
                array("Archiver", 'owners/archive', 'warning'),
                array("Supprimer", 'owners/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'nom' => array("Nom", 'VARCHAR 50', 'text', 'nom'),
                    'email' => array("Email", 'VARCHAR 50', 'text', 'email'),
                    'telephone' => array("Téléphone", 'VARCHAR 50', 'text', 'telephone'),
                    'adresse' => array("Adresse", 'VARCHAR 50', 'text', 'adresse'),
                    'contact' => array("Contact", 'VARCHAR 50', 'text', 'contact'),
                    // 'quatre_premiers_chiffres' => array("Quatre premiers chiffres", 'VARCHAR 50', 'text', 'quatre_premiers_chiffres'),
                    // 'les_quatre_derniers_chiffres' => array("Les quatre derniers chiffres", 'VARCHAR 50', 'text', 'les_quatre_derniers_chiffres'),
                    // 'banque' => array("Banque", 'VARCHAR 50', 'text', 'banque'),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'        => "Détail of Propriétaire",
                'page'         => "templates/detail",
                'menu'         => "Extra|Owners",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'owners',
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
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_owners->archive($id);
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
            $this->session->set_flashdata('success', "Propriétaire a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Owners Data
     ******************************/
    public function remove($id, $ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_owners->remove($id);

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
            $this->my_set_action_response($ajax, true, "Propriétaire a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_owners->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_owners->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_owners->unremove($id);
        }
    }
}
// EOF
