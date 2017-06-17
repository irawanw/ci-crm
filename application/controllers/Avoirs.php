<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_avoirs $m_avoirs
*/
class Avoirs extends MY_Controller {
    private $profil;
    private $barre_action = array(
        'Avoirs' => array(
            array(
                "Nouveau" => array('avoirs/nouveau','plus',true,'avoir_nouveau',null,array('form')),
            ),
            array(
                "Consulter<br>Modifier" => array('*avoirs/lignes','eye-open',false,'avoir_lignes',null,array('dblclick')),
                "Dupliquer" => array('avoirs/dupliquer','duplicate',false,'avoir_dupliquer',"Veuillez confirmer la duplication de l'avoir",array('confirm-modify')),
                "Supprimer" => array('avoirs/suppression','trash',false,'avoir_supprimer',"Veuillez confirmer la suppression de l'avoir",array('confirm-delete')),
            ),
            array(
                "Valider" => array('avoirs/valider','ok',false,'avoir_valider',null,array('modify', 'positive')),
            ),
            array(
                "Aperçu<br>avoir" => array('avoirs/exporter_pdf','download',false,'avoir_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>avoir" => array('avoirs/imprimer_pdf','print',false,'avoir_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('avoirs/envoyer_email','send',false,'avoir_envoyer_email',null,array('modify')),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression')
            ),
        ),
        'Avoirs_Client' => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                "Consulter<br>Modifier" => array('*avoirs/lignes','eye-open',false,'avoir_lignes',null,array('default-view')),
                "Supprimer" => array('avoirs/suppression','trash',false,'avoir_supprimer',"Veuillez confirmer la suppression de l'avoir",array('confirm-delete')),
            ),
            array(
                "Devis" => array('*devis/devis_client[]','list-alt',true,'devis'),
                "Commandes" => array('*commandes/commandes_client[]','shopping-cart',true,'commandes'),
                "Factures" => array('*factures/factures_client[]','folder-open',true,'factures'),
                "Avoirs" => array('*avoirs/avoirs_client[]','retweet',true,'avoirs'),
                "Réglements" => array('*reglements/reglements_client[]','euro',true,'reglements'),
            ),
            array(
                "Créer document" => array('documents_contacts/nouveau','paperclip',true,'nouveau_document',null,array('form')),
                "Documents" => array('*documents_contacts/documents_contact[]','paperclip',true,'documents'),
            ),
            array(
                "Evènements" => array('*evenements/evenements_client[]','calendar',true,'evenements'),
            ),
            array(
                "Correspondants" => array('*correspondants/correspondants_contact[]','user',true,'correspondants'),
                "Envoyer email" => array('evenements/email_contact','send',true,'envoi_email',null,array('form')),
                "Courrier type" => array('documents_contacts/nouveau','envelope',true,'courrier_type',null,array('form')),
            ),
            array(
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Imprimer<br>liste" => array('#','print',false,'impression')
            ),
        ),
        'Element' => array(
            array(
                "Consulter<br>Modifier" => array('#','eye-open',false,'avoir_lignes'),
                "Dupliquer" => array('avoirs/dupliquer','duplicate',false,'avoir_dupliquer',"Veuillez confirmer la duplication de l'avoir",array('confirm-modify')),
                "Supprimer" => array('avoirs/suppression','trash',true,'avoir_supprimer',"Veuillez confirmer la suppression de l'avoir",array('confirm-delete' => array('avoirs/index'))),
            ),
            array(
                "Valider" => array('avoirs/valider','ok',false,'avoir_valider',null,array('modify', 'positive')),
            ),
            array(
                "Envoyer par<br>email" => array('avoirs/envoyer_email','send',false,'avoir_envoyer_email',null,array('modify')),
            ),
            array(
                "Aperçu<br>avoir" => array('avoirs/exporter_pdf','download',true,'avoir_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>avoir" => array('avoirs/imprimer_pdf','print',true,'avoir_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
        ),
        'Edition' => array(
            array(
                "Consulter<br>Modifier" => array('#','pencil',true,'avoir_lignes'),
                "Dupliquer" => array('avoirs/dupliquer','duplicate',false,'avoir_dupliquer',"Veuillez confirmer la duplication de l'avoir",array('confirm-modify')),
                "Supprimer" => array('avoirs/suppression','trash',true,'avoir_supprimer',"Veuillez confirmer la suppression de l'avoir",array('confirm-delete' => array('avoirs/index'))),
            ),
            array(
                "Enregistrer" => array('#','save',false,'enregistrerLignes',null,array('positive')),
            ),
            array(
                "Valider" => array('avoirs/valider','ok',false,'avoir_valider',null,array('post', 'positive')),
            ),
            array(
                "Envoyer par<br>email" => array('avoirs/envoyer_email','send',false,'avoir_envoyer_email',null,array('modify')),
            ),
            array(
                "Aperçu<br>avoir" => array('avoirs/exporter_pdf','download',true,'avoir_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>avoir" => array('avoirs/imprimer_pdf','print',true,'avoir_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
        ),
        'Nouveau' => array(
            array(
                "Nouveau" => array('#','plus',false,'avoir_nouveau'),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_avoirs');
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $avoir        M_avoirs   Les infos de l'avoir
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $avoir) {
        $avr_etat = $avoir->avr_etat;
        $etats = array(
            'avoirs/detail'                => $avr_etat > 0,
            'avoirs/modification'          => $avr_etat < 2,
            'avoirs/dupliquer'             => $avr_etat > 0,
            'avoirs/suppression'           => $avr_etat < 2,
            'avoirs/valider'               => $avr_etat == 1,
            'avoirs/genere_pdf'            => true,
            'avoirs/exporter_pdf'          => true,
            'avoirs/imprimer_pdf'          => true,
            'avoirs/envoyer_email'         => $avr_etat > 1,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

     /******************************
     * List of avoirs Data
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
    * Liste des avoirs
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'avoirs/index',
            'detail' => array('avoirs/detail','avr_id','avr_reference'),
            'champs' => $this->m_avoirs->get_champs('read'),
            'filterable_columns' => $this->m_avoirs->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'avoirs/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'avoirs/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'avoirs/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'avoirs',
                'methode' => __FUNCTION__,
                'mass_action_toolbar' => true,
                'view_toolbar' => true,
            ),true);
        $scripts[] = $this->load->view('avoirs/liste-js.php', array(), true);
        $scripts[] = $this->load->view('avoirs/nouveau-js', array(), true);
        $scripts[] = $this->load->view('reglements/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('avoirs',$this->session->id);
        $data = array(
            'title' => "Liste des avoirs",
            'page' => "templates/datatables",
            'menu' => "Ventes|Avoirs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action['Avoirs'],
            'controleur' => 'avoirs',
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
    * Liste des avoirs (datasource)
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
            $resultat = $this->m_avoirs->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'avr_reference' => 't_avoirs',
                'avr_date' => 't_avoirs',
                'fac_reference' => 't_factures',
                'cmd_reference' => 't_commandes',
                'dvi_reference' => 't_devis',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_avoirs->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_avoirs->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_avoirs->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Avoirs'
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
    * Avoirs du contact [CONTACT]
    ******************************/
    public function avoirs_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'avoirs/avoirs_client',
            'detail' => array('avoirs/detail','avr_id','avr_reference'),
            'champs' => array(
                array('avr_numero','number',"Numéro"),
                array('avr_reference','text',"Référence"),
                array('avr_date','date',"Date avoir"),
                array('avr_montant_ttc','number',"Montant TTC"),
                array('vev_etat','ref',"État de l'avoir",'v_etats_avoirs'),
                array('vta_type','ref',"Type d'avoir",'v_types_avoirs'),
                array('avr_justification','text',"Justification de l'avoir"),
                //array('avr_fichier','hreffile','PDF', '/'),
                array('fac_reference','ref',"Facture associée",'factures','avr_facture','fac_reference'),
                array('ctc_nom','ref',"Client",'contacts','avr_client','ctc_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_avoirs->liste_par_client_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'avoirs',
                'methode' => __FUNCTION__,
            ),true);
        $scripts[] = $this->load->view('avoirs/liste-js.php', array(), true);
        $scripts[] = $this->load->view('avoirs/nouveau-js', array(), true);
        $scripts[] = $this->load->view('reglements/form-js', array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('avoirs',$this->session->id);
        $data = array(
            'title' => "Avoirs du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Ventes|Avoirs",
            'barre_action' => $this->barre_action['Avoirs_Client'],
            'controleur' => 'avoirs',
            'methode' => __FUNCTION__,
            'scripts' => $scripts,
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
     * Envoyer par email
     * support AJAX
     ******************************/
    public function envoyer_email($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        try {
            $resultat = $this->m_avoirs->envoyer_email($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax,true,"L'avoir a été envoyé par email",'info',$ajaxData);
                $redirection = "avoirs";
            }
        } catch (MY_Exceptions_NoEmailAddress $e) {
            $this->my_set_action_response($ajax,false,"Le contact ou l'enseigne n'a pas d'adresse email");

        } catch (MY_Exceptions_NoSuchFile $e) {
            $this->my_set_action_response($ajax,false,"L'avoir PDF n'a pas pu être généré");

        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"L'avoir n'a pas été trouvé");

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de message type disponible pour envoyer le mail");
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Avoirs du contact [CONTACT] (datasource)
    ******************************/
    public function avoirs_client_json($id=0) {
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
            $resultat = $this->m_avoirs->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'avr_reference' => 't_avoirs',
                'avr_date' => 't_avoirs',
                'fac_reference' => 't_factures',
                'cmd_reference' => 't_commandes',
                'dvi_reference' => 't_devis',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_avoirs->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_avoirs->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Exporter ou imprimer PDF
     * support AJAX
     ******************************/
    protected function _pdf($controleur,$id=0,$ajax=false) {
        $redirection = $this->session->userdata('_url_retour');
        try {
            $pdf = $this->m_avoirs->pdf($id);
            if (!$pdf) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $url = site_url($controleur.'?ref='.$pdf);
                $redirection = $url;
                if ($ajax) {
                    $ajaxData = array(
                        'url' => $url,
                    );
                    $this->my_set_action_response($ajax, true, "L'avoir PDF a été généré", 'info', $ajaxData);
                }
            }
        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"L'avoir n'a pas été trouvé");
            $redirection = 'avoirs';

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de modèle existant pour générer l'avoir PDF");
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Exporter PDF
     * support AJAX
     ******************************/
    public function exporter_pdf($id=0,$ajax=false) {
        return $this->_pdf('fichiers/telecharger', $id, $ajax);
    }

    /******************************
     * Imprimer PDF
     * support AJAX
     ******************************/
    public function imprimer_pdf($id=0,$ajax=false) {
        return $this->_pdf('fichiers/pdf',$id,$ajax);
    }

    /******************************
     * Dupliquer
     * AJAX
     ******************************/
    public function dupliquer($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $id = $this->m_avoirs->dupliquer($id);
        if ($id === false) {
            $this->my_set_action_response($ajax,false);
            $redirection = $this->session->userdata('_url_retour');
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
            $this->my_set_action_response($ajax,true,"L'avoir a été dupliqué",'info',$ajaxData);
            $redirection = "avoirs";
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Détail d'un avoir
    * support AJAX
    * @deprecated
    ******************************/
    public function detail($id,$ajax=false) {
        if ($ajax) {
            $this->my_set_action_response($ajax, false, 'Fonctionnalité obsolète');
        }
        redirect('avoirs/detail2/'.$id);

        /*
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_avoirs->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'avr_reference' => array("Référence",'VARCHAR 10','text','avr_reference'),
                   'avr_date' => array("Date avoir",'DATE','date','avr_date'),
                   'avr_facture' => array("Facture associée",'REF','ref',array('factures','avr_facture','fac_reference')),
                   'avr_client' => array("Client",'REF','ref',array('contacts','avr_client','ctc_nom')),
                   'avr_montant_ttc' => array("Montant TTC",'DECIMAL 8,2','number','avr_montant_ttc'),
                   'avr_etat' => array("État de l'avoir",'REF','text','vev_etat'),
                   'avr_type' => array("Type d'avoir",'REF','text','vta_type'),
                   'avr_justification' => array("Justification de l'avoir",'VARCHAR 400','textarea','avr_justification'),
                   //'avr_fichier' => array("PDF",'FICHIER','text','avr_fichier'),
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un avoir",
                'page' => "templates/detail",
                'menu' => "Ventes|Avoir",
                'barre_action' => $this->_masque_demasque_actions($this->barre_action['Element'], $valeurs),
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'avoirs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
        */
    }

    /******************************
     * Détail d'un avoir
     * support AJAX
     ******************************/
    public function nouveau($contact=0,$ajax=false) {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'avr_date', 'label' => "Date", 'rules' => 'trim'),
            array('field' => 'avr_societe_vendeuse', 'label' => "Enseigne", 'rules' => 'trim|required'),
            array('field' => 'avr_client', 'label' => "Client", 'rules' => 'trim|required'),
            array('field' => 'avr_correspondant', 'label' => "Correspondant", 'rules' => 'trim'),
            array('field' => 'avr_type', 'label' => "Type d'avoir", 'rules' => 'trim|required'),
            array('field' => 'avr_justification', 'label' => "Justification de l'avoir", 'rules' => 'trim'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'avr_date'                 => formatte_date_to_bd($this->input->post('avr_date')),
                'avr_societe_vendeuse'     => $this->input->post('avr_societe_vendeuse'),
                'avr_client'               => $this->input->post('avr_client'),
                'avr_correspondant'        => $this->input->post('avr_correspondant'),
                'avr_type'                 => $this->input->post('avr_type'),
                'avr_justification'        => $this->input->post('avr_justification'),
            );

            $id = $this->m_avoirs->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $id,
                        'timeStamp'  => round(microtime(true) * 1000),
                    ),
                    'url' => site_url('avoirs/lignes/'.$id),
                );
                $this->my_set_action_response($ajax, true, "L'avoir a été créé", 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                           = new stdClass();
            $listes_valeurs                    = new stdClass();

            $valeurs->avr_date = $this->input->post('avr_date');
            if (!$valeurs->avr_date) {
                $valeurs->avr_date = date('Y-m-d');
            }
            $valeurs->avr_societe_vendeuse     = $this->input->post('avr_societe_vendeuse');
            $valeurs->avr_client               = $this->input->post('avr_client');
            $valeurs->avr_correspondant        = $this->input->post('avr_correspondant');
            $valeurs->avr_type                 = $this->input->post('avr_type');
            if (!$valeurs->avr_type) {
                $valeurs->avr_type = 3; // Compensation
            }
            $valeurs->avr_justification = $this->input->post('avr_justification');

            $this->load->model('m_societes_vendeuses');
            $listes_valeurs->avr_societe_vendeuse = $this->m_societes_vendeuses->liste_option();
            $listes_valeurs->avr_type = $this->m_avoirs->liste_types_avoirs();

            if (!$valeurs->avr_societe_vendeuse) {
                $scv_first = current($listes_valeurs->avr_societe_vendeuse);
                $valeurs->avr_societe_vendeuse = $scv_first->id;
            }
            $this->load->model('m_contacts');
            $listes_valeurs->avr_client = $this->m_contacts->liste_option();
            if (!$valeurs->avr_client) {
                if ($contact > 0) {
                    $valeurs->avr_client = $contact;
                } else {
                    $ctc_first = current($listes_valeurs->avr_client);
                    $valeurs->avr_client = $ctc_first->id;
                }
            }
            $this->load->model('m_correspondants');
            $listes_valeurs->avr_correspondant = $this->m_correspondants->liste_option($valeurs->avr_client);
            if (!$valeurs->avr_correspondant && count($listes_valeurs->avr_correspondant) == 1) {
                $cor_first = current($listes_valeurs->avr_correspondant);
                $valeurs->avr_correspondant = $cor_first->id;
            }

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_avoirs->get_champs('write'),
                'onglets' => array(
                ),
            );
            $scripts   = array();
            $scripts[] = $this->load->view('avoirs/nouveau-js', array(), true);

            $data = array(
                'title'          => "Nouvel avoir",
                'page'           => "templates/form",
                'menu'           => "Ventes|Nouvel avoir",
                'scripts'        => $scripts,
                'barre_action'   => $this->barre_action["Nouveau"],
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Continuer',
                'controleur'     => 'avoirs',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /**
     * @param string $barre
     * @param M_avoirs $avoir
     * @return array
     */
    protected function _get_barre_action($barre, $avoir) {

        $barre_action = $this->barre_action[$barre];
        $avr_etat = $avoir->avr_etat;
        if ($avr_etat == 1) {
            $barre_action = modifie_action_barre_action($barre_action,'avoirs/detail2','avoirs/lignes');
        }
        $barre_action = $this->_masque_demasque_actions($barre_action, $avoir);

        return $barre_action;
    }

    /******************************
     * Manipulation des articles du devis (appelé en AJAX par le composant Grid)
     ******************************/
    public function manipulation($id,$commande) {
        if (! $this->input->is_ajax_request()) die('');
        if ($commande == 'add') {
            if ($id == 0) {
                $id = $this->m_devis->nouveau($_POST);
                $this->session->set_userdata('devis', $id);
            }
            else {
                $this->m_devis->maj($_POST,$id);
            }
            $resultat = $id;
        }
        else {
            if ($id == 0) {
                $id = $this->session->devis;
            }
            $resultat = $this->m_devis->constitution($id, $commande);
            if ($resultat === false) {
                log_message('error', '===== Erreur dans m_devis->constitution');
                die();
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Consultation de avoir
     ******************************/
    public function detail2($id) {
        $avoir = $this->m_avoirs->detail($id);
        if (!$avoir) {
            redirect('avoirs/');
        }

        $this->load->model('m_societes_vendeuses');
        $avoir->enseigne = $this->m_societes_vendeuses->detail($avoir->avr_societe_vendeuse);

        $scripts = array();
        $scripts[] = $this->load->view('avoirs/detail2-js',array('id'=>$id,'tva'=>$avoir->avr_tva),true);

        $barre_action = $this->_get_barre_action('Edition', $avoir);

        $data = array(
            'title' => "Détail d'un avoir",
            'page' => "avoirs/detail2",
            'menu' => "Ventes|Détail avoir",
            'barre_action' => $barre_action,
            'id' => $id,
            'scripts' => $scripts,
            'values' => $avoir,
            'controleur' => 'avoirs',
            'methode' => __FUNCTION__,
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Mise à jour d'un avoir
     ******************************/
    public function lignes($id=0) {
        $avoir = $this->m_avoirs->detail($id);
        if (! ($avoir->avr_etat == 1)) {
            redirect("avoirs/detail2/$id");
        }

        /**
         * @var M_societes_vendeuses $m_societes_vendeuses
         */
        $this->load->model('m_societes_vendeuses');
        $m_societes_vendeuses = $this->m_societes_vendeuses;
        $avoir->enseigne = $m_societes_vendeuses->detail($avoir->avr_societe_vendeuse);

        $this->load->helper(array('form','ctrl'));
        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_familles');
        $familles = $q->result();
        $tva = tva();
        $scripts = array();
        $scripts[] = $this->load->view('avoirs/mise_a_jour-js',array('id'=>$id,'tva'=>$tva,'familles' => $familles),true);
        foreach ($familles as $f) {
            $scripts[] = $this->load->view('_catalogues/'.$f->vfm_nom.'-js',array(),true);
        }

        $barre_action = $this->_get_barre_action('Edition', $avoir);

        $listes_valeurs = new stdClass();
        $listes_valeurs->avr_type = $this->m_avoirs->liste_types_avoirs();

        $this->load->model('m_correspondants');
        $listes_valeurs->avr_correspondant = $this->m_correspondants->liste_option($avoir->avr_client);

        $data = array(
            'title' => "Mise à jour d'un avoir",
            'page' => "avoirs/mise_a_jour",
            'menu' => "Ventes|Mise à jour d'avoir",
            'barre_action' => $barre_action,
            'controleur' => 'avoirs',
            'methode' => __FUNCTION__,
            'scripts' => $scripts,
            'values' => array(
                'id' => $id,
                'familles' => $familles,
                'tva' => $tva,
                'values' => $avoir
            ),
            'listes_valeurs' => $listes_valeurs,
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
    * Mise à jour d'un avoir
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'avr_date','label'=>"Date",'rules'=>'required'),
            array('field'=>'avr_type','label'=>"Type d'avoir",'rules'=>'required'),
            array('field'=>'avr_correspondant','label'=>"Correspondant",'rules'=>'trim'),
            array('field'=>'avr_justification','label'=>"Justification de l'avoir",'rules'=>'trim'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'avr_date' => formatte_date_to_bd($this->input->post('avr_date')),
                'avr_type' => $this->input->post('avr_type'),
                'avr_correspondant' => $this->input->post('avr_correspondant'),
                'avr_justification' => $this->input->post('avr_justification'),
            );
            $resultat = $this->m_avoirs->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                }
                else {
                    $message = "L'avoir a été modifié";
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
            redirect('avoirs/lignes/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_avoirs->detail($id);
            $listes_valeurs = new stdClass();

            $valeur = $this->input->post('avr_date');
            if (isset($valeur)) {
                $valeurs->avr_date = $valeur;
            }
            $valeur = $this->input->post('avr_type');
            if (isset($valeur)) {
                $valeurs->avr_type = $valeur;
            }
            $valeur = $this->input->post('avr_correspondant');
            if (isset($valeur)) {
                $valeurs->avr_correspondant = $valeur;
            }
            $valeur = $this->input->post('avr_justification');
            if (isset($valeur)) {
                $valeurs->avr_justification = $valeur;
            }
            $scripts = array();
            $listes_valeurs->avr_type = $this->m_avoirs->liste_types_avoirs();

            $this->load->model('m_correspondants');
            $listes_valeurs->avr_correspondant = $this->m_correspondants->liste_option($valeurs->avr_client);

            // descripteur
            $descripteur = array(
                'champs' => array(
                    'avr_date' => array($label = "Date de l'avoir", $type = 'date',null, $required = true),
                    'avr_type' => array("Type d'avoir",'select',array(null,'id','value'),true),
                    'avr_correspondant' => array("Correspondant",'select',array(null,'id','value'),false),
                    'avr_justification' => array("Justification de l'avoir",'textarea',null,false),
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un avoir",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour d'avoir",
                'barre_action' => $this->_masque_demasque_actions($this->barre_action['Edition'], $valeurs),
                'scripts' => $scripts,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'avoirs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    public function valider($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');

        $valeurs = array(
            'avr_etat' => 2,
        );
        $resultat = $this->m_avoirs->maj($valeurs,$id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            if ($resultat == 0) {
                $message = "Pas de modification demandée";
                $ajaxData = null;
            }
            else {
                $message = "L'avoir a été validé";
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type' => 'recordchange',
                        'id' => $id,
                        'timeStamp' => round(microtime(true) * 1000),
                    ),
                    'url' => site_url('avoirs/detail2/'.$id),
                );
            }

            $this->my_set_action_response($ajax,true,$message,'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        redirect('avoirs/details2/'.$id);
    }

    /******************************
    * Suppression d'un avoir
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');

        try {
            $resultat = $this->m_avoirs->suppression($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recorddelete',
                        'id'         => $id,
                        'timeStamp'  => round(microtime(true) * 1000),
                        'redirect'   => $redirection,
                    ),
                );
                $this->my_set_action_response($ajax,true,"L'avoir a été supprimé",'info',$ajaxData);
            }
        } catch (MY_Exceptions_OperationNotAllowed $e) {
            $this->my_set_action_response($ajax,false, 'Opération non autorisée');
        }
        if ($ajax) {
            return;
        }

        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_avoirs->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_avoirs->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_avoirs->unremove($id);
        }
    }

}

// EOF