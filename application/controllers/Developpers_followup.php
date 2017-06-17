<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_developpers_followup m_developpers_followup
*/
class Developpers_followup extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('developpers_followup/nouveau', 'plus', true, 'developpers_followup_nouveau', null, array('form')),
        ),
        array(
            //"Consulter" => array('developpers_followup/detail', 'eye-open', false, 'developpers_followup_detail'),
            "Consulter/Modifier"  => array('developpers_followup/modification', 'pencil', false, 'developpers_followup_modification',null, array('form')),
            "Archiver" => array('developpers_followup/archive', 'folder-close', false, 'developpers_followup_archive',"Veuillez confirmer la archive du developpeur followup", array('confirm-modify' => array('developpers_followup/index'))),
            "Supprimer" => array('developpers_followup/remove', 'trash', false, 'developpers_followup_supprimer',"Veuillez confirmer la suppression du owner", array('confirm-delete' => array('developpers_followup/index'))),
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
        $this->load->model('m_developpers_followup');
    }

    /******************************
     * List of developpers_followup Data
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
            
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'developpers_followup/index',
            'detail'             => array('developpers_followup/detail', 'dev_id', 'description'),
            'archive'            => array('developpers_followup/archive', 'dev_id', 'archive'),
            'champs'             => $this->m_developpers_followup->get_champs('read'),
            'filterable_columns' => $this->m_developpers_followup->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: developpers_followup/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'developpers_followup/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'developpers_followup/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'developpers_followup/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'developpers_followup',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        //$scripts[] = $this->load->view("developpers_followup/liste-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('developpers_followup', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Developpers Followup",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Developpers Followup",
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_developpers_followup->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'dev_id' => 't_developpers_followup',
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

                $resultat = $this->m_developpers_followup->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_developpers_followup->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_developpers_followup->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Developpers_followup'
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
            array('field' => 'cor_tiket', 'label' => "Cor Tiket", 'rules' => 'trim|required'),
            array('field' => 'priorite', 'label' =>  "Priorité", 'rules' => 'trim'),
            array('field' => 'name', 'label' =>  "Name", 'rules' => 'trim'),
            array('field' => 'descriptif', 'label' =>  "Descriptif", 'rules' => 'trim'),
            array('field' => 'developpeur', 'label' =>  "Date demande", 'rules' => 'trim'),
            array('field' => 'date_demande', 'label' =>  "Date demandée", 'rules' => 'trim'),
            array('field' => 'date_de_fin_souhaitee', 'label' =>  "Date de fin souhaitée", 'rules' => 'trim'),
            array('field' => 'etat', 'label' =>  "État", 'rules' => 'trim'),
            array('field' => 'type', 'label' =>  "Type", 'rules' => 'trim'),
            array('field' => 'url', 'label' =>  "Url", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'cor_tiket'                      => $this->input->post('cor_tiket'),
                'priorite'                       => $this->input->post('priorite'),
                'name'                           => $this->input->post('name'),
                'descriptif'                     => $this->input->post('descriptif'),
                'developpeur'                    => $this->input->post('developpeur'),
                'date_demande'                   => formatte_date_to_bd($this->input->post('date_demande')),
                'date_de_fin_souhaitee'          => formatte_date_to_bd($this->input->post('date_de_fin_souhaitee')),
                'etat'                           => $this->input->post('etat'),
                'type'                           => $this->input->post('type'),
                'url'                           => $this->input->post('url'),
            );

            $resultat = $this->m_developpers_followup->nouveau($valeurs);

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
                $this->my_set_action_response($ajax, true, "Developpers Followup a été enregistré avec succès",'info',$ajaxData);
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

            $valeurs->cor_tiket     = $this->input->post('cor_tiket');
            $valeurs->priorite      = $this->input->post('priorite');
            $valeurs->name          = $this->input->post('name');
            $valeurs->descriptif    = $this->input->post('descriptif');
            $valeurs->developpeur   = $this->input->post('developpeur');  
            $valeurs->date_demande   = $this->input->post('date_demande');
            $valeurs->date_de_fin_souhaitee   = $this->input->post('date_de_fin_souhaitee');
            $valeurs->etat   = $this->input->post('etat');
            $valeurs->type   = $this->input->post('type');
            $valeurs->url   = $this->input->post('url');

            $listes_valeurs->priorite = $this->m_developpers_followup->priorite_liste();
            $listes_valeurs->developpeur = $this->m_developpers_followup->developpeur_liste();
            $listes_valeurs->etat = $this->m_developpers_followup->etat_liste();
            $listes_valeurs->type = $this->m_developpers_followup->type_liste();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_developpers_followup->get_champs('write'),
                'onglets' => array(),
            );
     

            $data = array(
                'title' => "Ajouter un nouveau Developpers Followup",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Developpers Followup",              
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'developpers_followup',
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
            array('field' => 'cor_tiket', 'label' => "Cor Tiket", 'rules' => 'trim|required'),
            array('field' => 'priorite', 'label' =>  "Priorité", 'rules' => 'trim'),
            array('field' => 'name', 'label' =>  "Name", 'rules' => 'trim'),
            array('field' => 'descriptif', 'label' =>  "Descriptif", 'rules' => 'trim'),
            array('field' => 'developpeur', 'label' =>  "Date demande", 'rules' => 'trim'),
            array('field' => 'date_demande', 'label' =>  "Date demandée", 'rules' => 'trim'),
            array('field' => 'date_de_fin_souhaitee', 'label' =>  "Date de fin souhaitée", 'rules' => 'trim'),
            array('field' => 'etat', 'label' =>  "État", 'rules' => 'trim'),
            array('field' => 'type', 'label' =>  "Type", 'rules' => 'trim'),
            array('field' => 'url', 'label' =>  "Url", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'cor_tiket'                      => $this->input->post('cor_tiket'),
                'priorite'                       => $this->input->post('priorite'),
                'name'                           => $this->input->post('name'),
                'descriptif'                     => $this->input->post('descriptif'),
                'developpeur'                    => $this->input->post('developpeur'),
                'date_demande'                   => formatte_date_to_bd($this->input->post('date_demande')),
                'date_de_fin_souhaitee'          => formatte_date_to_bd($this->input->post('date_de_fin_souhaitee')),
                'etat'                           => $this->input->post('etat'),
                'type'                           => $this->input->post('type'),
                'url'                            => $this->input->post('url'),
            );

            $resultat = $this->m_developpers_followup->maj($valeurs, $id);
            
            $redirection = 'developpers_followup/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                 if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = null;
                 }
                 else {
                     $message = "Developpers Followup a été modifié";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $resultat,
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
            $valeurs = $this->m_developpers_followup->detail($id);
            $listes_valeurs = new stdClass;

            $listes_valeurs->priorite = $this->m_developpers_followup->priorite_liste();
            $listes_valeurs->developpeur = $this->m_developpers_followup->developpeur_liste();
            $listes_valeurs->etat = $this->m_developpers_followup->etat_liste();
            $listes_valeurs->type = $this->m_developpers_followup->type_liste();

            $scripts = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_developpers_followup->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title' => "Modifier Developpers Followup",
                'page' => "templates/form",
                'menu' => "Extra|Edit Developpers Followup",
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'listes_valeurs' => $listes_valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'developpers_followup',
                'methode' => 'modification',
                'descripteur' => $descripteur,                
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = 'developpers_followup/detail/'.$id;
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_developpers_followup->archive($id);
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
              $this->my_set_action_response($ajax, true, "Developpers followup a été archive", 'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
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

        $resultat = $this->m_developpers_followup->remove($id);

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
            $this->my_set_action_response($ajax, true, "Developpers Followup a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_developpers_followup->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_developpers_followup->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_developpers_followup->unremove($id);
        }
    }
}
// EOF
