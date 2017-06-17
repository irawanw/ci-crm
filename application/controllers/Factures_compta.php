<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_factures_compta m_factures_compta
*/
class Factures_compta extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Nouveau" => array('factures_compta/nouveau', 'plus', true, 'factures_compta_nouveau', null, array('form')),
        ),
        array(
            "Consulter/Modifier" => array('factures_compta/modification', 'pencil', false, 'factures_compta_modification', null, array('form')),
            "Supprimer"          => array('factures_compta/remove', 'trash', false, 'factures_compta_supprimer', "Veuillez confirmer la suppression de cette facture", array('confirm-delete' => array('factures_compta/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
        ),
        array(
            "Export Excel" => array('#', 'list-alt', true, 'export_xls'),
            "Export PDF"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_factures_compta');
    }

    

    /******************************
     * List of factures_compta Data
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
            // array("Ajouter un e-mailing pages jaunes","factures_compta/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'factures_compta/index',
            'detail'             => array('factures_compta/detail', 'factures_compta_id', 'description'),
            'archive'            => array('factures_compta/archive', 'factures_compta_id', 'archive'),
            'champs'             => $this->m_factures_compta->get_champs('read'),
            'filterable_columns' => $this->m_factures_compta->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: factures_compta/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'factures_compta/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'factures_compta/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'factures_compta/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'factures_compta',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("factures_compta/liste-js", array(
            'url_upload' => site_url('factures_compta/upload_facture'),
        ), true);
        $scripts[] = $this->load->view('factures_compta/form-js', array(
                'field_name' => 'facture',
        ), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('factures_compta', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Factures à Récupérer Compta",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Factures compta",
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
            $resultat = $this->m_factures_compta->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'factures_compta_id' => 't_factures_compta',
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

                $resultat = $this->m_factures_compta->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_factures_compta->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_factures_compta->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Factures_compta'
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
            array('field' => 'nom_fournisseur', 'label' => 'Nom Fournisseur', 'rules' => 'trim|required'),
            array('field' => 'intitule_sur_compte', 'label' => 'intitulé sur compte', 'rules' => 'trim'),
            array('field' => 'montant_debit', 'label' => 'Montant débit', 'rules' => 'trim'),
            array('field' => 'date_debit', 'label' => 'date débit', 'rules' => 'trim'),
            array('field' => 'type', 'label' => 'type', 'rules' => 'trim'),
            array('field' => 'lien_vers_la_facture', 'label' => 'lien vers la facture', 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'nom_fournisseur'      => $this->input->post('nom_fournisseur'),
                'intitule_sur_compte'  => $this->input->post('intitule_sur_compte'),
                'montant_debit'        => $this->input->post('montant_debit'),
                'date_debit'           => formatte_date_to_bd($this->input->post('date_debit')),
                'type'                 => $this->input->post('type'),
                'lien_vers_la_facture' => $this->input->post('lien_vers_la_facture'),
            );

			
            $id = $this->m_factures_compta->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                //upload file when it have value
                if ($this->input->post('facture') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('facture'));
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
                $this->my_set_action_response($ajax, true, "Factures à Récupérer Compta a été enregistré avec succès",'info',$ajaxData);
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

            $valeurs->nom_fournisseur      = $this->input->post('nom_fournisseur');
            $valeurs->intitule_sur_compte  = $this->input->post('intitule_sur_compte');
            $valeurs->montant_debit        = $this->input->post('montant_debit');
            $valeurs->date_debit           = $this->input->post('date_debit');
            $valeurs->type                 = $this->input->post('type');
            $valeurs->lien_vers_la_facture = $this->input->post('lien_vers_la_facture');
            $valeurs->facture              = $this->input->post('facture');

            $listes_valeurs->type = $this->m_factures_compta->liste_option_type();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_factures_compta->get_champs('write'),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Factures à Récupérer Compta",
                'page'           => "templates/form",
                'menu'           => "Extra|Create Factures compta",
                'scripts'        => $scripts,
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'factures_compta',
                'methode'        => 'create',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            //$layout = "layouts/standard";
            //$this->load->view($layout, $data);
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
            array('field' => 'nom_fournisseur', 'label' => 'Nom Fournisseur', 'rules' => 'trim|required'),
            array('field' => 'intitule_sur_compte', 'label' => 'intitulé sur compte', 'rules' => 'trim'),
            array('field' => 'montant_debit', 'label' => 'Montant débit', 'rules' => 'trim'),
            array('field' => 'date_debit', 'label' => 'date débit', 'rules' => 'trim'),
            array('field' => 'type', 'label' => 'type', 'rules' => 'trim'),
            array('field' => 'lien_vers_la_facture', 'label' => 'lien vers la facture', 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'nom_fournisseur'      => $this->input->post('nom_fournisseur'),
                'intitule_sur_compte'  => $this->input->post('intitule_sur_compte'),
                'montant_debit'        => $this->input->post('montant_debit'),
                'date_debit'           => formatte_date_to_bd($this->input->post('date_debit')),
                'type'                 => $this->input->post('type'),
                'lien_vers_la_facture' => $this->input->post('lien_vers_la_facture'),
            );

			
            $resultat = $this->m_factures_compta->maj($valeurs, $id);
            
            $redirection = 'factures_compta/detail/'.$id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                //upload file when it have value
                if ($this->input->post('facture') != "") {
                    $this->load->model('m_files');

                    $file_ids = explode(",", $this->input->post('facture'));
                    $this->m_files->update_row($id, $file_ids);
                }

                if ($resultat == 0) {
                     $message = "Pas de modification demandée";
                     $ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'id' => $id,
                         'timeStamp' => round(microtime(true) * 1000),
                     ),
                     );
                 }
                 else {
                     $message = "Factures à Récupérer Compta a été modifié";
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
            $listes_valeurs = new stdClass();
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_factures_compta->detail($id);
            $valeurs->facture = $this->input->post('facture');

            $listes_valeurs->type = $this->m_factures_compta->liste_option_type();

            $scripts   = array();

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_factures_compta->get_champs('write'),
                'onglets' => array(),
            );
            $data = array(
                'title'          => "Modifier Factures à Récupérer Compta",
                'page'           => "templates/form",
                'menu'           => "Extra|Edit Factures_compta",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => true,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'factures_compta',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            
			//$layout = "layouts/standard";
            //$this->load->view($layout, $data);
			$this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_factures_compta->archive($id);
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
            $this->session->set_flashdata('success', "Factures à Récupérer Compta a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Factures_compta Data
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

        $resultat = $this->m_factures_compta->remove($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                 'event' => array(
                     'controleur' => 'factures_compta',
                     'type'       => 'recorddelete',
                     'id'         => $id,
                     'timeStamp'  => round(microtime(true) * 1000),
                     'redirect'   => $redirection,
                 ),
            );
            //$this->my_set_action_response($ajax, true, "Factures à Récupérer Compta a été supprimé", 'info', $ajaxData);
			$this->my_set_action_response($ajax, true, "Factures à Récupérer Compta a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_factures_compta->archive($id);
        }
    }

    public function mass_unarchiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_factures_compta->unarchive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_factures_compta->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_factures_compta->unremove($id);
        }
    }

    public function mass_hard_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_factures_compta->hard_remove($id);
        }
    }

    public function do_upload_multiple()
    {
        $this->load->model('m_files');
        $upPath = FCPATH . 'fichiers/factures_compta/';
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
            $error = $this->upload->display_errors();
            echo json_encode(array('status' => false, 'error' => $error));
        } else {
            $upload_data = $this->upload->data();

            $data = array(
                'name'     => 'facturescompta_facture',
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

    public function doupload_files()
    {
        $upPath = FCPATH . 'fichiers/factures_compta/';
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
            $data = $this->upload->data();
            $data['error'] = null;

        }
        return $data;
    }

    public function upload_facture()
    {
        $this->load->model('m_files');
        $data = $this->doupload_files();
        if ($data['error'] == null) {
            $valeurs = array(
                'row_id'   => $this->input->post('upload_id'),
                'name'     => 'facturescompta_facture',
                'filename' => $data['file_name'],
                'path'     => $data['full_path'],
            );

            $resultat = $this->m_files->nouveau($valeurs);

            echo json_encode(array('status' => true, 'id' => $resultat));
        }
    }
	
	public function get_facture_files($id){
		$files = $this->m_factures_compta->get_facture_files($id);
		foreach ($files as $file) {
                echo '<div id="file-container-' . $file->file_id . '">
                        <button type="button" onclick="showConfirmRemoveFile(' . $file->file_id . ')" class="btn btn-warning btn-xs btn-delete-file">x</button>
                        <a target="_blank" href="' . base_url('fichiers/factures_compta') . '/' . $file->filename . '">' . $file->filename . '</a>
                      </div>';
        }

            echo '<div class="alert alert-danger" style="display:none;" id="confirm-remove-file">
                        <p>Etes-vous certain de vouloir supprimer le fichier?</p>
                        <button onclick="hideConfirmRemoveFile()" type="button" class="btn btn-default">Non</button>
                        <button class="btn btn-warning" type="button" id="btn-remove-file-ok">Oui</button>
                      </div>';
	}
}
// EOF
