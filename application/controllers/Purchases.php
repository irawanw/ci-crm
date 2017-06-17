<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
/**
* @property M_purchases m_purchases
*/
class Purchases extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Purchases" => array(
            array(
                "Nouveau" => array('purchases/nouveau','plus',true,'purchases_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('*purchases/detail', 'eye-open', false, 'achat_detail'),
                "Modifier"  => array('purchases/modification', 'pencil', false, 'achat_modification',null, array('form')),
                "Supprimer" => array('purchases/remove', 'trash', false, 'achat_supprimer', "Veuillez confirmer la suppression du telephones", array('confirm-modify' => array('purchases/index'))),
            ),
            array(
                "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
            ),
            array(
                "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
                "Export pdf"  => array('#', 'book', true, 'export_pdf'),
                "Imprimer"    => array('#', 'print', true, 'print_list'),
            ),
        ),
        "Purchase" => array(
            array(
                "Nouveau" => array('purchases/nouveau','plus',true,'purchases_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('*purchases/detail', 'eye-open', false, 'achat_detail'),
                "Modifier"  => array('purchases/modification', 'pencil', false, 'achat_modification'),
                "Supprimer" => array('purchases/remove', 'trash', false, 'achat_supprimer', "Veuillez confirmer la suppression du telephones", array('confirm-modify' => array('purchases/index'))),
            ),          
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_purchases');
    }

   
    /******************************
     * List of Purchase Data
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
            //array("Nouvel achat","purchases/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'purchases/index',
            'detail'             => array('purchases/detail', 'purchase_id', 'description'),
            'archive'            => array('purchases/archive', 'purchase_id', 'archive'),
            'champs'             => $this->m_purchases->get_champs('read'),
            'filterable_columns' => $this->m_purchases->liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: pages_jaunes/archived_json in kendo_grid-js
        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'purchases/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'purchases/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'purchases/all';
                break;
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'purchases',
                'methode'               => 'index',
				'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
            ), true);
        $scripts[] = $this->load->view("purchases/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('purchases', $this->session->id);
        $data = array(
            'title'        => "Liste d'achat Item",
            'page'         => "templates/datatables",
            'barre_action' => $this->barre_action['Purchases'],
            'menu'         => "Agenda|Purchases",
            'scripts'      => $scripts,
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
     * Ajax call for Purchase List
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
            $resultat = $this->m_purchases->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'purchase_id' => 't_purchases',
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

                $resultat = $this->m_purchases->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_purchases->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_purchases->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Purchases'
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
     * New purchase
     ******************************/
    public function nouveau($id=0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'description', 'label' => "Achat", 'rules' => 'trim|required'),
            array('field' => 'delivery', 'label' => "Lieu de livraison", 'rules' => 'trim'),
            array('field' => 'date_limit', 'label' => "Date limite", 'rules' => 'trim'),
            array('field' => 'sponsor', 'label' => "Commanditaire", 'rules' => 'trim'),
            array('field' => 'person', 'label' => "Personne devant passer la commande", 'rules' => 'trim'),
            array('field' => 'beneficiary', 'label' => "Bénéficiaire", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'description' => $this->input->post('description'),
                'delivery'    => $this->input->post('delivery'),
                'date_limit'  => formatte_date_to_bd($this->input->post('date_limit')),
                'sponsor'     => $this->input->post('sponsor'),
                'person'      => $this->input->post('person'),
                'beneficiary' => $this->input->post('beneficiary'),
            );

            $resultat = $this->m_purchases->nouveau($valeurs);
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
                $this->my_set_action_response($ajax, true, "Les données d'achat a été enregistré avec succès","info",$ajaxData);
            }
            if ($ajax) {
                return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);            
        } else {
            // validation en échec ou premier appel : affichage du formulaire
            $valeurs              = new stdClass();
            $listes_valeurs       = new stdClass();
            $valeurs->description = $this->input->post('description');
            $valeurs->delivery    = $this->input->post('delivery');
            $valeurs->date_limit  = $this->input->post('date_limit');
            $valeurs->sponsor     = $this->input->post('sponsor');
            $valeurs->person      = $this->input->post('person');
            $valeurs->beneficiary = $this->input->post('beneficiary');

            $this->db->order_by('emp_nom', 'ASC');
            $q                           = $this->db->get('t_employes');
            $listes_valeurs->sponsor     = $q->result();
            $listes_valeurs->person      = $q->result();
            $listes_valeurs->beneficiary = $q->result();
            $scripts                     = array();
            $scripts[]                   = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#date_limit").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_purchases->get_champs('write'),
                'onglets' => array(
                    array("Achat", array('description', 'delivery', 'date_limit', 'sponsor', 'person', 'beneficiary')),
                    //array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_travail_prevu'))
                ),
            );          

            $data = array(
                'title' => "Ajouter un nouvel achat",
                'page' => "templates/form",
                'menu' => "Agenda|Nouveau Purchase",
                'barre_action' => $this->barre_action["Purchase"],
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'purchases',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Detail of Purchase Data
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
            $valeurs = $this->m_purchases->detail($id);

            // commandes globales
            $cmd_globales = array(
                //array("Historique",'evenements_taches/evenements_tache','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier", 'purchases/modification', 'primary'),
                array("Archiver", 'purchases/archive', 'warning'),
                array("Supprimer", 'purchases/remove', 'danger'),
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'purchase_id' => array("Purchase #", 'VARCHAR 50', 'text', 'purchase_id'),
                    'description' => array("Achat", 'VARCHAR 50', 'text', 'description'),
                    'delivery'    => array("Lieu de livraison", 'VARCHAR 50', 'text', 'delivery'),
                    'date_limit'  => array("Date Limite", 'DATE', 'DATE', 'date_limit'),
                    'sponsor'     => array("Commanditaire", 'VARCHAR 50', 'text', 'sponsor_name'),
                    'person'      => array("Personne devant passer la commande", 'VARCHAR 50', 'text', 'person_name'),
                    'beneficiary' => array("Bénéficiaire", 'VARCHAR 50', 'text', 'beneficiary_name'),
                ),
                'onglets' => array(
                    array("Achat", array('purchase_id', 'description', 'delivery', 'date_limit', 'sponsor', 'person', 'beneficiary')),
                    //array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                ),
            );

            $data = array(
                'title'        => "Détail of purchase",
                'page'         => "templates/detail",
                'menu'         => "Agenda|Purchase",
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'purchases',
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
     * Edit function for Purchase Data
     ******************************/
    public function modification($id = 0, $ajax=false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'description', 'label' => "Achat", 'rules' => 'trim'),
            array('field' => 'delivery', 'label' => "Lieu de livraison", 'rules' => 'trim'),
            array('field' => 'date_limit', 'label' => "Date limite", 'rules' => 'trim'),
            array('field' => 'sponsor', 'label' => "Commanditaire", 'rules' => 'trim'),
            array('field' => 'person', 'label' => "Personne devant passer la commande", 'rules' => 'trim'),
            array('field' => 'beneficiary', 'label' => "Bénéficiaire", 'rules' => 'trim'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'description' => $this->input->post('description'),
                'delivery'    => $this->input->post('delivery'),
                'date_limit'  => formatte_date_to_bd($this->input->post('date_limit')),
                'sponsor'     => $this->input->post('sponsor'),
                'person'      => $this->input->post('person'),
                'beneficiary' => $this->input->post('beneficiary'),
            );
            $resultat = $this->m_purchases->maj($valeurs, $id);
            if ($resultat === false) {
				/*
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }
                redirect('purchases/detail/' . $id);
				*/
				 $this->my_set_action_response($ajax,false);
            } else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                } else {
                    $message = "Les données d'achat a été modifié";
					$ajaxData = array(
                         'event' => array(
                             'controleur' 	=> $this->my_controleur_from_class(__CLASS__),
                             'type' 		=> 'recordchange',
                             'id' 			=> $id,
                             'timeStamp' 	=> round(microtime(true) * 1000),
                         ),
                     );
                }
				$this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
                //$this->session->set_flashdata('success', $message);
				 if ($ajax) {
					return;
				}
				redirect('purchases/detail/' . $id);
				redirect($redirection);
            }
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = $this->m_purchases->detail($id);
            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('person');
            if (isset($valeur)) {
                $valeurs->person = $valeur;
            }
            $this->db->order_by('emp_nom', 'ASC');
            $q                       = $this->db->get('t_employes');
            $listes_valeurs->sponsor = $q->result();
            $this->db->order_by('emp_nom', 'ASC');
            $q                      = $this->db->get('t_employes');
            $listes_valeurs->person = $q->result();
            $this->db->order_by('emp_nom', 'ASC');
            $q                           = $this->db->get('t_employes');
            $listes_valeurs->beneficiary = $q->result();

            $scripts   = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#date_limit").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_purchases->get_champs('write'),
                'onglets' => array(
                    array("Achat", array('description', 'delivery', 'date_limit', 'sponsor', 'person', 'beneficiary')),
                    //array("Planning", array('tac_debut_prevu','tac_fin_prevue','tac_debut_real','tac_fin_real','tac_travail_prevu','tac_travail_real'))
                ),
            );

            $data = array(
                'title'          => "Modifier les données d'achat",
                'page'           => "templates/form",
                'menu'           => "Agenda|Edit Purchase Data",
                'scripts'        => $scripts,
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'purchases',
                'methode'        => 'modification',
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
			$this->my_set_form_display_response($ajax,$data);
			/*
            $layout = "layouts/standard";
            $this->load->view($layout, $data);
			*/
        }
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id)
    {
		if ($this->input->method() != 'post') {
            die;
        }
		$redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }
			
        $resultat = $this->m_purchases->archive($id);
        if ($resultat === false) {
			 $this->my_set_action_response($ajax, false);
			/*
            if (null === $this->session->flashdata('danger')) {
                $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            }
            redirect($redirection);
			*/
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
            $this->my_set_action_response($ajax, true, "Propriétaire a été supprimé", 'info',$ajaxData);
			
            $this->session->set_flashdata('success', "Les données d'achat a été archivé");
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }
           // redirect($redirection);
        }
		if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Delete Purchase Data
     ******************************/
    public function remove($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_purchases->remove($id);

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
            $this->my_set_action_response($ajax, true, "Le contact a été supprimé", 'info', $ajaxData);
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
            $resultat = $this->m_purchases->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_purchases->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_purchases->unremove($id);
        }
    }
}
// EOF
