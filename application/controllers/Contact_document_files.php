<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_document_table m_document_table
*/
class Contact_document_files extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Supprimer" => array('contact_document_files/remove', 'trash', false, 'contact_document_files_supprimer',"Veuillez confirmer la suppression du contact document files", array('confirm-delete' => array('contact_document_files/index'))),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
		),
		array(
            "Export xlsx"   => array('#', 'list-alt', true, 'export_xls'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_document_table');
    }

    protected function get_champs($type)
    {
        $champs = array(
            'list' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('filename', 'text', "Filename", 'filename'),
                array('template_name', 'text', "Template", 'template_name'),
                //array('content', 'text', "Content", 'content'),
                array('client_name', 'text', "Client", 'client_name'),
                array('date_generate', 'date', "Date Generate", 'date_generate'),           
            )
        );

        return $champs[$type];
    }

    /******************************
     * List of contact_document_files Data
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
            // array("Ajouter un e-mailing pages jaunes","contact_document_files/nouveau",'default')
        );

        // toolbar
        $toolbar = '';


        // descripteur
        $descripteur = array(
            'datasource'         => 'contact_document_files/index',
            'detail'             => array('contact_document_files/detail', 'owner_id', 'description'),
            'archive'            => array('contact_document_files/archive', 'owner_id', 'archive'),
            'champs'             => $this->get_champs('list'),
            'filterable_columns' => $this->m_document_table->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: contact_document_files/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'contact_document_files/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'contact_document_files/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'contact_document_files/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'contact_document_files',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("contact_document_files/liste-js", array('url_download' => site_url('contact_document_files/download/ajax')), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('contact_document_files', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des Contact document files",
            'page'         => "templates/datatables",
            'menu'         => "Extra|Contact document files",
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
            $resultat = $this->m_document_table->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'owner_id' => 't_document_table',
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

                $resultat = $this->m_document_table->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_document_table->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->get_champs('list');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Contact_document_files'
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
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
        $resultat = $this->m_document_table->archive($id);
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
            $this->session->set_flashdata('success', "Contact document file a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }
    }

    /******************************
     * Delete Contact_document_files Data
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

        $resultat = $this->m_document_table->remove($id);

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
            $this->my_set_action_response($ajax, true, "Contact document file a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }        

        redirect($redirection);         
    }

    public function download($ajax = false)
    {
        if(!$this->input->is_ajax_request()) {
            $this->my_set_action_response($ajax, false);
        }

        $id = $this->input->post('id');

        if($id == null ) {
            $this->my_set_action_response($ajax, false);
        }

        try {
            $resultat = $this->m_document_table->download($id);

            $ajaxData = array(
                 'event' => array(
                     'controleur' => $this->my_controleur_from_class(__CLASS__),
                     'type' => 'recordchange',
                     'timeStamp' => round(microtime(true) * 1000),
                     'redirect' => $resultat['fileUrl']
                 ),
            );
            $this->my_set_action_response($ajax, true, $resultat['message'], 'info', $ajaxData);
        } catch (MY_Exceptions_NoSuchFile $e) {
            $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');
        }
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_document_table->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_document_table->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_document_table->unremove($id);
        }
    }
}
// EOF
