<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_commandes m_commandes
*/
class Commandes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            //      Texte => array( URL, icône, actif, "id" HTML, texte confirmation, array(type action, ...) )
            //array(
            //    "Nouveau" => array('commandes/nouveau','plus',true,'commandes_nouveau')
            //),
            array(
                "Consulter" => array('commandes/detail','eye-open',false,'commandes_detail',null,array('view', 'dblclick')),
            ),
            array(
                "Facturer" => array('commandes/facturer','ok',false,'commandes_facturer',null,array('form', 'positive')),
                "Lancer" => array('commandes/lancer','play',false,'commandes_lancer',null,array('action', 'positive')),
                "Ordres de<br>production" => array('ordres_production/op_commande[]','transfer',false,'op_commande'),
                "Annuler<br>commande" => array('commandes/annuler','trash',false,'commandes_annuler',"Confirmer l'annulation de la commande",array('confirm-action', 'negative')),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                "Consulter" => array('commandes/detail','eye-open',false,'commandes_detail',null,array('view', 'default-view')),
            ),
            array(
                "Facturer" => array('commandes/facturer','ok',false,'commandes_facturer',null,array('form', 'positive')),
                "Lancer" => array('commandes/lancer','play',false,'commandes_lancer',null,array('action', 'positive')),
                "Ordres de<br>production" => array('ordres_production/op_commande[]','transfer',false,'op_commande'),
                "Annuler<br>commande" => array('commandes/annuler','trash',false,'commandes_annuler',"Confirmer l'annulation de la commande",array('confirm-action', 'negative')),
            ),
        ),
        "Facturer" => array(
            array(
                "Facturer" => array('#','ok',false,'commandes_facturer'),
            ),
        ),
        "Commandes_Client" => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail'),
            ),
            array(
                "Consulter" => array('commandes/detail','eye-open',false,'commandes_detail')
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
                "Envoyer email" => array('evenements/email_contact','send',true,'envoi_email'),
                //"Courrier type" => array('documents_contacts/nouveau','envelope',true,'courrier_type'),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression'),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_commandes');
    }

    /******************************
    * Liste des commandes
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        $menu_extra = array(
            array(
                'name' => "Distribution",
                'url' => "#"
            ),
            array(
                'name' => "Impression",
                'url' => "#"
            ),
            array(
                'name' => "E-mailing",
                'url' => site_url('global_list')
            ),
            array(
                'name' => "Sites internet",
                'url' => "#"
            ),
            array(
                'name' => "Pass",
                'url' => "#"
            ),
            array(
                'name' => "Enlevements",
                'url' => "#"
            ),
            array(
                'name' => "Livraisons",
                'url' => site_url('livraisons')
            ),
            array(
                'name' => "Divers",
                'url' => "#"
            ),

        );

        // descripteur
        $descripteur = array(
            'datasource' => 'commandes/index',
            'detail' => array('commandes/detail','cmd_id','cmd_reference'),
            'champs' => $this->m_commandes->get_champs('read'),
            'filterable_columns' => $this->m_commandes->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'commandes',
                'methode' => 'index'
            ),true);

        $scripts[] = $this->load->view("commandes/liste-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes',$this->session->id);
        $data = array(
            'title' => "Liste des commandes",
            'page' => "templates/datatables",
            'menu' => "Ventes|Commandes",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'menu_extra'   => $menu_extra,
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
    * Liste des commandes (datasource)
    ******************************/
    public function index_json($id=0) {
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

        if($this->input->post('export')) {
            $pagelength = false;
            $pagestart = 0;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_commandes->liste($id,$pagelength, $pagestart, $filters, 'cmd_date', 'desc');
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cmd_reference' => 't_commandes',
                'cmd_date' => 't_commandes',
                'dvi_reference' => 't_devis',
                'cor_nom' => 't_correspondants',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_commandes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_commandes->liste($id,$pagelength, $pagestart, $filters);
            }
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_commandes->get_champs('read');
            $params = array(
                'records' => $resultat['data'],
                'columns' => $champs,
                'filename' => 'Commandes'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Commandes du contact [CONTACT]
    ******************************/
    public function commandes_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'commandes/commandes_client',
            'detail' => array('commandes/detail','cmd_id','cmd_reference'),
            'champs' => array(
                array('cmd_numero','number',"Numéro"),
                array('cmd_reference','text',"Référence"),
                array('cmd_date','date',"Date commande"),
                array('dvi_reference','ref',"Devis associé",'devis','cmd_devis','dvi_reference'),
                array('vec_etat','ref',"Etat",'v_etats_commandes'),
                array('ctc_nom','ref',"Client",'contacts','dvi_client','ctc_nom'),
                array('dvi_montant_ht','number',"Montant devis HT"),
                array('dvi_montant_ttc','number',"Montant devis TTC"),
                array('cmd_p_facture','text',"% facturé"),
                array('cmd_p_regle','text',"% réglé"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_commandes->liste_par_client_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'commandes',
                'methode' => 'commandes_client'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes',$this->session->id);
        $data = array(
            'title' => "Commandes du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Ventes|Commandes",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Commandes_Client"],
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'cmd_masque_specifiques' => 0,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Commandes du contact [CONTACT] (datasource)
    ******************************/
    public function commandes_client_json($id=0) {
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
            $resultat = $this->m_commandes->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cmd_reference' => 't_commandes',
                'cmd_date' => 't_commandes',
                'dvi_reference' => 't_devis',
                'cor_nom' => 't_correspondants',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_commandes->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_commandes->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Liste des commandes passées
    ******************************/
    public function commandes_escli($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'commandes/commandes_escli',
            'detail' => array('commandes/detail_escli','cmd_id','cmd_reference'),
            'champs' => array(
                array('cmd_reference','text',"Référence"),
                array('cmd_date','date',"Date commande"),
                array('dvi_montant_ht','number',"Montant devis HT"),
                array('dvi_montant_ttc','number',"Montant devis TTC"),
                array('vec_etat','ref',"Etat",'v_etats_commandes'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_commandes->liste_escli_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'commandes',
                'methode' => 'commandes_escli'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes',$this->session->id);
        $data = array(
            'title' => "Liste des commandes passées",
            'page' => "templates/datatables",
            'menu' => "Espace client|Commandes",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
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
    * Liste des commandes passées (datasource)
    ******************************/
    public function commandes_escli_json($id=0) {
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
            $resultat = $this->m_commandes->liste_escli($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'cmd_reference' => 't_commandes',
                'cmd_date' => 't_commandes',
                'dvi_reference' => 't_devis'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_commandes->liste_escli($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_commandes->liste_escli($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Facturer une commande
     *
     * support AJAX
     ******************************/
    public function facturer($id=0,$ajax=false) {
        $commande = $this->m_commandes->detail($id);
        if (! ($commande->cmd_etat < 5)) {
            $this->my_set_action_response($ajax, false, "Opération non autorisée");
            if ($ajax) {
                return;
            }
            redirect('commandes/detail/'.$id);
        }

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');
        $this->load->model('m_societes_vendeuses');
        $this->load->model('m_contacts');
        $this->load->model('m_id_comptable');

        $enseigne = $this->m_societes_vendeuses->detail($commande->dvi_societe_vendeuse);

        $id_comptable_rules = array(
            'trim'
        );
        if (!$enseigne->scv_id_comptable) {
            $id_comptable_rules[] = 'required';
        }

        /**
         * @var M_id_comptable $m_id_comptable
         */
        $m_id_comptable = $this->m_id_comptable;
        $id_comptable_rules[] = array(
            'id_comptable',
            function($id_comptable) use ($m_id_comptable, $commande) {
                $result = $m_id_comptable->liste_par_id_comptable($id_comptable, $commande->dvi_societe_vendeuse);
                foreach ($result as $row) {
                    if ($commande->dvi_client != $row->idc_contact) {
                        // It's not good if a different client is already using that id comptable.
                        return false;
                    }
                }
                return true;
            }
        );

        // règles de validation
        $config = array(
            array(
                'field' => 'dvi_id_comptable',
                'label' => "Id comptable",
                'rules' => $id_comptable_rules,
                'errors' => array(
                    'id_comptable' => 'Id comptable déjà attribué à un autre client',
                ),
            ),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            $ajaxData = array(
                'event' => array(),
            );

            // Handle id comptable creation only if needed
            $result = $m_id_comptable->liste_par_contact($commande->dvi_client, $commande->dvi_societe_vendeuse);
            $id_comptable = null;
            foreach ($result as $row) {
                $id_comptable = $row->idc_id_comptable;
                break;
            }
            if (empty($id_comptable)) {
                $data = array(
                    'idc_contact' => $commande->dvi_client,
                    'idc_societe_vendeuse' => $commande->dvi_societe_vendeuse,
                );
                if (!$enseigne->scv_id_comptable) {
                    $data['idc_id_comptable'] = $this->input->post('dvi_id_comptable');
                }
                $id_comptable = $m_id_comptable->nouveau($data);
                if ($id_comptable) {
                    $ajaxData['event'][] = array(
                        'controleur' => 'contacts',
                        'type'       => 'recordchange',
                        'id'         => $commande->dvi_client,
                        'timeStamp'  => round(microtime(true) * 1000),
                    );
                } else {
                    $this->my_set_action_response($ajax, false, "Un problème avec l'id comptable a empêché la création de la facture");
                }
            }

            if ($id_comptable) {
                $fac_id = $this->m_commandes->facturer($id);
                if ($fac_id === false) {
                    $this->my_set_action_response($ajax, false);
                    $redirection = $this->session->userdata('_url_retour');
                }
                else {
                    $ajaxData['event'][] = array(
                        'controleur' => 'factures',
                        'type' => 'recordadd',
                        'id' => $fac_id,
                        'timeStamp' => round(microtime(true) * 1000),
                    );
                    $ajaxData['event']['redirect'] = site_url("factures/lignes/".$fac_id);
                    $this->my_set_action_response($ajax, true, "La facture a été créée", 'info', $ajaxData);
                    $redirection = "factures/lignes/".$fac_id;
                }
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        }

        // validation en échec ou premier appel : affichage du formulaire
        $valeurs                           = new stdClass();
        $listes_valeurs                    = new stdClass();

        $valeurs->dvi_id_comptable         = $this->input->post('dvi_id_comptable');

        $id_comptables = $m_id_comptable->liste_par_contact($commande->dvi_client, $commande->dvi_societe_vendeuse);
        foreach ($id_comptables as $row) {
            $valeurs->dvi_id_comptable = $row->idc_id_comptable;
        }

        $dvi_id_comptable_attrs = array();
        if ($enseigne->scv_id_comptable) {
            $dvi_id_comptable_attrs['placeholder'] = 'Géré par le système. Ne rien saisir.';
            $dvi_id_comptable_attrs['disabled'] = true;
        } else {
            $dvi_id_comptable_attrs['placeholder'] = "Saisir l'id comptable du client pour cette enseigne.";
        }

        // descripteur
        $descripteur = array(
            'champs'  => array(
                'dvi_id_comptable' => array("Id comptable pour ".$enseigne->scv_nom,'text',null,$required = false, $dvi_id_comptable_attrs),
            ),
            'onglets' => array(
            ),
        );
        $scripts = array();

        $commande = array(
            'title'          => "Facturer la commande",
            'page'           => "templates/form",
            'menu'           => "Ventes|Commandes",
            'scripts'        => $scripts,
            'barre_action'   => $this->barre_action['Facturer'],
            'values'         => $valeurs,
            'action'         => "création",
            'multipart'      => false,
            'confirmation'   => 'Facturer',
            'controleur'     => 'commandes',
            'methode'        => __FUNCTION__,
            'descripteur'    => $descripteur,
            'listes_valeurs' => $listes_valeurs,
        );
        $this->my_set_form_display_response($ajax, $commande);
    }

    /******************************
    * Lancer commande
    * support AJAX
    ******************************/
    public function lancer($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $data = $this->m_commandes->detail($id);
        if (! ($data->cmd_etat == 1)) {
            $this->my_set_action_response($ajax, false, "Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_commandes->lancer($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                    'event' => array(
                        array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type' => 'recordchange',
                            'id' => $id,
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                        array(
                            'controleur' => 'ordres_production',
                            'type' => 'recordadd',
                            'timeStamp' => round(microtime(true) * 1000),
                        )
                    ),
                );
                $this->my_set_action_response($ajax, true, "La commande a été lancée", 'info', $ajaxData);
            }
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
    * Annuler commande
    * support AJAX
    ******************************/
    public function annuler($id=0,$ajax=false) {
        $data = $this->m_commandes->detail($id);
        if (! ($data->cmd_etat < 4)) {
            $this->my_set_action_response($ajax, false, "Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_commandes->annuler($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type' => 'recordchange',
                        'id' => $id,
                        'timeStamp' => round(microtime(true) * 1000),
                    ),
                );
                $this->my_set_action_response($ajax, true, "La commande a été annulée", 'info', $ajaxData);
            }
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
    * Détail d'une commande
    ******************************/
    public function detail($id, $ajax = false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        //else {
            $valeurs = $this->m_commandes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cmd_reference' => array("Référence",'VARCHAR 10','text','cmd_reference'),
                   'cmd_date' => array("Date commande",'DATE','date','cmd_date'),
                   'cmd_devis' => array("Devis associé",'REF','ref',array('devis','cmd_devis','dvi_reference')),
                   'dvi_client' => array("Client",'REF','ref',array('contacts','dvi_client','ctc_nom')),
                   'cor_nom' => array("Nom",'VARCHAR 50','text','cor_nom'),
                   'dvi_montant_ht' => array("Montant devis HT",'DECIMAL 8,2','number','dvi_montant_ht'),
                   'dvi_montant_ttc' => array("Montant devis TTC",'DECIMAL 8,2','number','dvi_montant_ttc'),
                   'cmd_p_facture' => array("% facturé",'SQL','text','cmd_p_facture'),
                   'cmd_p_regle' => array("% réglé",'SQL','text','cmd_p_regle'),
                   'cmd_etat' => array("État",'REF','text','vec_etat')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Détail d'une commande",
                'page' => "templates/detail",
                'menu' => "Ventes|Commande",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'commandes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        //}
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array       Barre d'action à modifier
     * @param $facture      M_commandes Les infos de la commande
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $commande) {
        $cmd_etat = $commande->cmd_etat;
        $etats = array(
            'commandes/detail'              => true,
            'commandes/lancer'              => $cmd_etat == 1,
            'commandes/facturer'            => $cmd_etat < 5,
            'commandes/annuler'             => $cmd_etat < 4,
            'ordres_production/op_commande' => $cmd_etat > 1,
        );
        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'une commande passée
    * support AJAX
    ******************************/
    public function detail_escli($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_commandes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'cmd_reference' => array("Référence",'VARCHAR 10','text','cmd_reference'),
                   'cmd_date' => array("Date commande",'DATE','date','cmd_date'),
                   'dvi_montant_ht' => array("Montant devis HT",'DECIMAL 8,2','number','dvi_montant_ht'),
                   'dvi_montant_ttc' => array("Montant devis TTC",'DECIMAL 8,2','number','dvi_montant_ttc'),
                   'cmd_etat' => array("État",'REF','text','vec_etat')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une commande passée",
                'page' => "templates/detail",
                'menu' => "Ventes|Commande",
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'commandes',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

}

// EOF