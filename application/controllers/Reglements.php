<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_reglements m_reglements
*/
class Reglements extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('*reglements/nouveau/0','plus',true,'reglements_nouveau',null,array('form'))
            ),
            array(
                    "Consulter" => array('*reglements/detail','eye-open',false,'reglements_detail',null,array('view', 'dblclick')),
                    //"Modifier" => array('*reglements/modification','pencil',false,'reglements_modification',null,array('form')),
                    "Supprimer" => array('reglements/suppression','trash',false,'reglements_supprimer',"Veuillez confirmer la suppression du règlement",array('confirm-delete' => array('reglements/index'))),
            ),
            array(
                "Ventilation" => array('*imputations/imputations_client','stats',false,'imputations_client'),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression')
            )
        ),
        "Edition" => array(
            array(
                "Consulter" => array('reglements/detail','eye-open',true,'reglements_detail',null,array('view')),
                //"Modifier" => array('*reglements/modification','pencil',true,'reglements_modification',null,array('form')),
                "Supprimer" => array('reglements/suppression','trash',true,'reglements_supprimer',"Veuillez confirmer la suppression du règlement",array('confirm-delete' => array('reglements/index'))),
            ),
            array(
                "Ventilation" => array('imputations/imputations_client[]','stats',true,'imputations_client'),
            ),
        ),
        "Reglements_Detail" => array(
            array(
                "Consulter" => array('reglements/detail','eye-open',true,'reglements_detail',null,array('view')),
                //"Modifier" => array('reglements/modification','pencil',true,'reglements_modification',null,array('form')),
                "Supprimer" => array('reglements/suppression','trash',true,'reglements_supprimer',"Veuillez confirmer la suppression du règlement",array('confirm-delete' => array('reglements/index'))),
            ),
            array(
                "Ventilation" => array('imputations/imputations_client[]','stats',true,'imputations_client'),
            ),
        ),
        "Reglements_Contact" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                "Nouveau" => array('reglements/nouveau','plus',true,'reglements_nouveau',null,array('form')),
            ),
            array(
                "Consulter" => array('reglements/detail','eye-open',false,'reglements_detail',null,array('view', 'dblclick')),
                //"Modifier" => array('reglements/modification','pencil',false,'reglements_modification',null,array('form')),
            ),
            array(
                "Devis" => array('devis/devis_client[]','list-alt',true,'devis'),
                "Commandes" => array('commandes/commandes_client[]','shopping-cart',true,'commandes'),
                "Factures" => array('factures/factures_client[]','folder-open',true,'factures'),
                "Avoirs" => array('avoirs/avoirs_client[]','retweet',true,'avoirs'),
                "Réglements" => array('reglements/reglements_client[]','euro',true,'reglements'),
            ),
            array(
                //"Créer document" => array('documents_contacts/nouveau','paperclip',true,'nouveau_document'),
                "Documents" => array('documents_contacts/documents_contact[]','paperclip',true,'documents'),
            ),
            array(
                "Evènements" => array('evenements/evenements_client[]','calendar',true,'evenements'),
            ),
            array(
                "Correspondants" => array('correspondants/correspondants_contact[]','user',true,'correspondants'),
                //"Envoyer email" => array('evenements/email_contact','send',true,'envoi_email'),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_reglements');
    }

    /******************************
     * List of ville Data
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

    /******************************
    * Liste des reglements
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'reglements/index',
            'detail' => array('reglements/detail','rgl_id','rgl_reference'),
            'champs' => $this->m_reglements->get_champs('read'),
            'filterable_columns' => $this->m_reglements->get_filterable_columns(),
            'select_types' => $this->m_reglements->get_types()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'reglements/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'reglements/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'reglements/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'reglements',
                'methode' => 'index',
                'mass_action_toolbar' => true,
                'external_toolbar_data' => array(
                    'custom_mass_action_toolbar' => array('archiver')
                )
            ),true);
        $scripts[] = $this->load->view("reglements/liste-js",array(),true);
        $scripts[] = $this->load->view('reglements/liste',array(),true);
        $scripts[] = $this->load->view('reglements/form-js',array(),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('reglements',$this->session->id);
        $data = array(
            'title' => "Liste des reglements",
            'page' => "templates/datatables",
            'menu' => "Ventes|Règlements",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'reglements',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des reglements (datasource)
    ******************************/
    public function index_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        
        //$pagelength = $this->input->post('length');
        $pagelength = 100;
        $pagestart  = 0+$this->input->post('start' );
        if ( $pagestart < 2)
            $pagelength = 50;
        
        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns))

            //list with default ordering
            $resultat = $this->m_reglements->liste($id, $pagelength, $pagestart, $filters, array("`rgl_date`", "`rgl_id`"), array("DESC", "DESC"));
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];
            if ( $order_col_id>=0 && $order_col_id<=count($columns))
            {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_reglements->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else
                $resultat = $this->m_reglements->liste($id, $pagelength, $pagestart, $filters);
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_reglements->get_champs('read');
            $params = array(
                'records' => $resultat['data'],
                'columns' => $champs,
                'filename' => 'Reglements'
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
    * Liste des contacts (DataTables CLIENT mode)
    ******************************/
    public function index_json_client($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        
        $resultat = $this->m_reglements->liste_chunk(0, 1, NULL);   // limit=0 i.e. get all records
                                                                    // start=1 will be ignored
                                                                    // filters=NULL i.e. filtering happening on client side

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));            
    }    
    

    /******************************
    * Règlements du contact [CONTACT]
    ******************************/
    public function reglements_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'reglements/reglements_client',
            'detail' => array('reglements/detail','rgl_id','rgl_reference'),
            'champs' => array(
                array('rgl_reference','text',"Référence"),
                array('rgl_date','date',"Date règlement"),
                array('rgl_montant','number',"Montant payé"),
                array('vtr_type','ref',"Type de règlement",'v_types_reglements'),
                array('rgl_cheque','text',"Numéro de chèque"),
                array('rgl_banque','text',"Banque"),
                array('ctc_nom','ref',"Client",'contacts','rgl_client','ctc_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_reglements->liste_par_client_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'reglements',
                'methode' => __FUNCTION__,
            ),true);
        $scripts[] = $this->load->view('reglements/form-js',array(),true);

        $barre_contact = modifie_action_barre_action($this->barre_action["Reglements_Contact"], 'reglements/nouveau', 'reglements/nouveau/'.$id);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('reglements',$this->session->id);
        $data = array(
            'title' => "Règlements du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Ventes|Règlements",
            'scripts' => $scripts,
            'barre_action' => $barre_contact,
            'controleur' => 'reglements',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Règlements du contact [CONTACT] (datasource)
    ******************************/
    public function reglements_client_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $pagelength = $this->input->post('length');
        $pagestart  = $this->input->post('start' );

        $order      = $this->input->post('order' );
        $columns    = $this->input->post('columns' );
        $filters    = $this->input->post('filters' );
        if ( empty($filters) ) $filters=NULL;
        $filter_global = $this->input->post('filter_global' );
        if ( !empty($filter_global) ) {

            // Ignore all other filters by resetting array
            $filters = array("_global"=>$filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_reglements->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'rgl_reference' => 't_reglements',
                'rgl_date' => 't_reglements',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_reglements->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_reglements->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Détail d'un règlement
    * support AJAX
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';

        if (count($_POST) > 0) {
            redirect($redirection);
        }
        //else {
            $valeurs = $this->m_reglements->detail($id);
            if (!$valeurs) {
                $this->my_set_action_response($ajax, false, "Pas de règlement trouvé avec cette référence");
                if ($ajax) {
                    return;
                }
                redirect($redirection);
            }

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'rgl_reference' => array("Référence",'VARCHAR 11','text','rgl_reference'),
                   'rgl_date' => array("Date règlement",'DATE','date','rgl_date'),
                   'rgl_client' => array("Client",'REF','ref',array('contacts','rgl_client','ctc_nom')),
                   'rgl_type' => array("Type de règlement",'REF','text','vtr_type'),
                   'rgl_montant' => array("Montant payé",'DECIMAL 8,2','number','rgl_montant'),
                   'rgl_banque' => array("Banque",'VARCHAR 80','text','rgl_banque'),
                   'rgl_cheque' => array("Numéro de chèque",'VARCHAR 30','text','rgl_cheque')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->barre_action["Reglements_Detail"];

            $data = array(
                'title' => "Détail d'un règlement",
                'page' => "templates/detail",
                'menu' => "Ventes|Règlement",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'reglements',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );

            $this->my_set_display_response($ajax, $data);
        //}
    }

    /******************************
    * Mise à jour d'un règlement
    * support AJAX
    * @deprecated Règlements ne peuvent pas être modifiés.
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'rgl_client','label'=>"Client",'rules'=>'required'),
            array('field'=>'rgl_type','label'=>"Type de règlement",'rules'=>'required'),
            array('field'=>'rgl_montant','label'=>"Montant payé",'rules'=>'trim|numeric|required|greater_than_equal_to[0]'),
            array('field'=>'rgl_banque','label'=>"Banque",'rules'=>'trim'),
            array('field'=>'rgl_cheque','label'=>"Numéro de chèque",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'rgl_date' => formatte_date_to_bd($this->input->post('rgl_date')),
                'rgl_client' => $this->input->post('rgl_client'),
                'rgl_type' => $this->input->post('rgl_type'),
                'rgl_montant' => $this->input->post('rgl_montant'),
                'rgl_banque' => $this->input->post('rgl_banque'),
                'rgl_cheque' => $this->input->post('rgl_cheque')
            );
            $resultat = $this->m_reglements->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "Le réglement a été modifié";
                    $ajaxData = array(
                         'event' => array(
                             'controleur' => $this->my_controleur_from_class(__CLASS__),
                             'type' => 'recordchange',
                             'id' => $id,
                             'timeStamp' => round(microtime(true) * 1000),
                         ),
                    );
                }
                $this->my_set_action_response($ajax,true,$message,'info',$ajaxData);
            }
            if ($ajax) {
                return;
            }
            redirect('reglements/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_reglements->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('rgl_date');
            if (isset($valeur)) {
                $valeurs->rgl_date = $valeur;
            }
            $valeur = $this->input->post('rgl_client');
            if (isset($valeur)) {
                $valeurs->rgl_client = $valeur;
            }
            $valeur = $this->input->post('rgl_type');
            if (isset($valeur)) {
                $valeurs->rgl_type = $valeur;
            }
            $valeur = $this->input->post('rgl_montant');
            if (isset($valeur)) {
                $valeurs->rgl_montant = $valeur;
            }
            $valeur = $this->input->post('rgl_banque');
            if (isset($valeur)) {
                $valeurs->rgl_banque = $valeur;
            }
            $valeur = $this->input->post('rgl_cheque');
            if (isset($valeur)) {
                $valeurs->rgl_cheque = $valeur;
            }
            $this->db->where("ctc_client_prospect=2");
            $this->db->order_by('ctc_nom','ASC');
            $q = $this->db->get('t_contacts');
            $listes_valeurs->rgl_client = $q->result();
            $this->db->order_by('vtr_type','ASC');
            $q = $this->db->get('v_types_reglements');
            $listes_valeurs->rgl_type = $q->result();
            $scripts = array();

            if (!$ajax) {
                $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#rgl_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;
            }

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'rgl_date' => array("Date règlement",'date','rgl_date',false),
                   'rgl_client' => array("Client",'select',array('rgl_client','ctc_id','ctc_nom'),true),
                   'rgl_type' => array("Type de règlement",'select',array('rgl_type','vtr_id','vtr_type'),true),
                   'rgl_montant' => array("Montant payé",'number','rgl_montant',true),
                   'rgl_banque' => array("Banque",'text','rgl_banque',false),
                   'rgl_cheque' => array("Numéro de chèque",'text','rgl_cheque',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un règlement",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour de règlement",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Edition"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'reglements',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /**
     * Returns a list of IDs for factures, avoirs, and profits tied to the imputation
     *
     * @param integer $id imputation ID
     * @return array
     */
    protected function _relatedRecords($id)
    {
        $factures = array();
        $avoirs = array();
        $profits = array();

        if ($id > 0) {
            // Liste des imputations pour ce règlement
            $this->load->model('m_imputations');
            $imputations = $this->m_imputations->liste($id, 100000, 0);

            foreach ($imputations['data'] as $imputation) {
                if ($imputation->ipu_facture > 0) {
                    $factures[] = $imputation->ipu_facture;
                }
                if ($imputation->ipu_avoir > 0) {
                    $avoirs[] = $imputation->ipu_avoir;
                }
                if ($imputation->ipu_profits > 0) {
                    $profits[] = $imputation->ipu_profits;
                }
            }
        }

        return array(
            'm_factures' => $factures,
            'm_avoirs' => $avoirs,
            'm_profits_et_pertes' => $profits,
        );
    }

    protected function _recordChangesToPseudoEvents($records, $ignored_controllers = null)
    {
        $pseudo_events = array();
        if (!$ignored_controllers) {
            $ignored_controllers = array();
        } elseif (!is_array($ignored_controllers)) {
            $ignored_controllers = array($ignored_controllers);
        }
        foreach ($records as $type => $models) {
            switch ($type) {
                case 'deleted':
                    $event = 'recorddelete';
                    break;
                case 'updated':
                    $event = 'recordchange';
                    break;
                case 'added':
                    $event = 'recordadd';
                    break;
                default:
                    continue 2;
            }
            foreach ($models as $model => $ids) {
                if (preg_match('/^[Mm]_(.+)$/', $model, $matches)) {
                    $controleur = $matches[1];
                } else {
                    $controleur = $model;
                }
                if (in_array($controleur, $ignored_controllers)) {
                    // Don't include events for ignored controllers
                    continue;
                }
                $ids = array_unique($ids);
                foreach ($ids as $id) {
                    if ($id > 0) {
                        $pseudo_events[] = array(
                            'controleur' => $controleur,
                            'type'       => $event,
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                        );
                    }
                }
            }
        }
        return $pseudo_events;
    }

    /******************************
    * Suppression d'un règlement
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');

        $recordChanges = $this->m_reglements->suppression($id);

        if ($recordChanges === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $_ajaxData = array();
            if ($ajax) {
                $_ajaxData = $this->_recordChangesToPseudoEvents($recordChanges, $this->my_controleur_from_class(__CLASS__));
            }
            $_ajaxData[] = array(
                'controleur' => $this->my_controleur_from_class(__CLASS__),
                'type'       => 'recorddelete',
                'id'         => $id,
                'timeStamp'  => round(microtime(true) * 1000),
                'redirect'   => $redirection,
            );

            $ajaxData = array(
                'event' => $_ajaxData,
            );
            $this->my_set_action_response($ajax,true,"Le règlement a été supprimé",'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
       
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Nouveau règlement
     * support AJAX
     ******************************/
    public function nouveau($contact=0,$facture_id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        $contact = filter_var($contact, FILTER_VALIDATE_INT);
        $facture_id = filter_var($facture_id, FILTER_VALIDATE_INT);
        $facture = null;
        if ($facture_id > 0) {
            $this->load->model('m_factures');
            $facture = $this->m_factures->detail($facture_id);
        }

        // règles de validation
        $config = array(
            array('field'=>'rgl_societe_vendeuse','label'=>"Client",'rules'=>'required'),
            array('field'=>'rgl_client','label'=>"Client",'rules'=>'required'),
            array('field'=>'rgl_type','label'=>"Type de règlement",'rules'=>'required'),
            array('field'=>'rgl_montant','label'=>"Montant versé",'rules'=>'trim|numeric|required|greater_than_equal_to[0]'),
            array('field'=>'rgl_banque','label'=>"Banque",'rules'=>'trim'),
            array('field'=>'rgl_cheque','label'=>"Numéro de chèque",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'rgl_date' => formatte_date_to_bd($this->input->post('rgl_date')),
                'rgl_client' => ($contact > 0) ? $contact : $this->input->post('rgl_client'),
                'rgl_societe_vendeuse' => $this->input->post('rgl_societe_vendeuse'),
                'rgl_type' => $this->input->post('rgl_type'),
                'rgl_montant' => $this->input->post('rgl_montant'),
                'rgl_banque' => $this->input->post('rgl_banque'),
                'rgl_cheque' => $this->input->post('rgl_cheque'),
                'pieces' => $this->input->post('pieces'),
                'trop_verse' => $this->input->post('trop_verse'),
                'compensation' => $this->input->post('compensation')
            );

            try {
                $id = $this->m_reglements->nouveau($valeurs);
                if ($id === false) {
                    $this->my_set_action_response($ajax, false);
                    $redirection = $this->session->userdata('_url_retour');
                } else {
                    // Get the pseudo-event for the factures, avoirs, etc...
                    $_ajaxData = array();
                    if ($ajax) {
                        $recordChanges = array(
                            'updated' => $this->_relatedRecords($id),
                        );
                        $_ajaxData = $this->_recordChangesToPseudoEvents($recordChanges);
                    }
                    $_ajaxData[] = array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type' => 'recordadd',
                        'id' => $id,
                        'timeStamp' => round(microtime(true) * 1000),
                    );

                    $ajaxData = array(
                        'event' => $_ajaxData,
                    );
                    $this->my_set_action_response($ajax, true, "Le règlement a été pris en compte", 'info', $ajaxData);
                    $redirection = "reglements";
                }
            } catch (MY_Exceptions_AccountingDiscrepancy $e) {
                $this->my_set_action_response($ajax, false, $e->getMessage());
            }
            if ($ajax) {
                return;
            }
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();

            $valeurs->compensation = "avoir";

            // Seulement pour l'UI
            $valeurs->rgl_du = '';
            $valeurs->rgl_avoirs = '';
            $valeurs->rgl_regle = '0.00';

            $valeurs->rgl_date = $this->input->post('rgl_date');
            if (!$valeurs->rgl_date) {
                $valeurs->rgl_date = date('Y-m-d');
            }
            if ($contact) {
                $valeurs->rgl_client = $contact;
            } else {
                $valeurs->rgl_client = $this->input->post('rgl_client');
            }
            $valeurs->rgl_societe_vendeuse = $this->input->post('rgl_societe_vendeuse');
            if (!$valeurs->rgl_societe_vendeuse && $facture) {
                $valeurs->rgl_societe_vendeuse = $facture->dvi_societe_vendeuse;
            }
            $valeurs->rgl_type = $this->input->post('rgl_type');
            if (!$valeurs->rgl_type) {
                $valeurs->rgl_type = 1;     // Chèque
            }
            $valeurs->rgl_montant = $this->input->post('rgl_montant');
            $valeurs->rgl_banque = $this->input->post('rgl_banque');
            $valeurs->rgl_cheque = $this->input->post('rgl_cheque');

            $valeurs->trop_verse = $this->input->post('trop_verse');
            $valeurs->pieces = $this->input->post('pieces');

            $listes_valeurs->rgl_client = $this->m_reglements->client_option($contact);
            $listes_valeurs->rgl_type = $this->m_reglements->type_option();
            $listes_valeurs->compensation = $this->m_reglements->compensation_option();
            $scripts = array();

            $this->load->model('m_societes_vendeuses');
            $listes_valeurs->rgl_societe_vendeuse = $this->m_societes_vendeuses->liste_option();

            $scripts[] = $this->load->view('reglements/form-js',array(),true);

            $readonly = array(
                'readonly' => 'readonly',
                'disabled' => 'disabled',
                'tabindex' => -1,
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                    'pieces' => array("Pieces",'hidden','pieces',false),
                    'rgl_date' => array("Date règlement",'date','rgl_date',false),
                    'rgl_societe_vendeuse' => array("Enseigne",'select',array(null,'id','value'),true),
                    'rgl_client' => array("Client",'select',array('rgl_client','ctc_id','ctc_nom'),true),
                    'rgl_type' => array("Type de règlement",'select',array('rgl_type','vtr_id','vtr_type'),true),
                    'rgl_du' => array("Solde dû TTC",'number','rgl_du',false,$readonly),
                    'rgl_montant' => array("Montant versé TTC",'number','rgl_montant',true),
                    'rgl_avoirs' => array("Avoir utilisé TTC",'number','rgl_avoirs',false,($valeurs->rgl_type == 4) ? $readonly : array_merge(array('class' => "hidden"), $readonly)),
                    //'rgl_regle' => array("Montant réglé TTC",'number','rgl_regle',false,$readonly),
                    'rgl_regle' => array("Montant réglé TTC",'hidden','rgl_regle',false,$readonly),
                    'rgl_banque' => array("Banque",'text','rgl_banque',false),
                    'rgl_cheque' => array("Numéro de chèque",'text','rgl_cheque',false),
                    'trop_verse' => array("Exédant TTC",'text','trop_verse',false),
                    'compensation' => array("Compensation",'radio-h',array('compensation','id','value'),false),
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouveau règlement",
                'page' => "templates/form",
                'menu' => "Ventes|Nouveau règlement",
                'scripts' => $scripts,
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'values' => $valeurs,
                'action' => "creation",
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs,
                'controleur' => 'reglements',
                'methode' => __FUNCTION__,
            );

            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
     * Liste des factures et avoirs (datasource)
     ******************************/
    public function nouveau_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        if ($id == 0) return array();
        $resultat = $this->m_reglements->factures_et_avoirs($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function get_factures($clientId, $enseigneId = null)
    {
        $resultat = $this->m_reglements->factures_et_avoirs($clientId, $enseigneId);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_reglements->archive($id);
        }
    }
}

// EOF