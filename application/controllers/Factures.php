<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_Factures $m_factures
* @property CI_Input $input
* @property CI_DB_query_builder $db
*/
class Factures extends MY_Controller {

    private $profil;
    private $barre_action = array(
        'Liste' => array(
            array(
                // Texte => array( URL, icône, actif, "id" HTML, texte confirmation, array(type action, ...) )

                "Consulter<br>Modifier" => array('*factures/lignes','eye-open',false,'facture_detail',null,array('dblclick')),
                "Dupliquer" => array('factures/dupliquer','duplicate',false,'facture_dupliquer',"Confirmer la duplication de cette facture",array('confirm-modify')),
                "Supprimer" => array('factures/suppression','trash',false,'facture_supprimer',"Confirmer la suppression de cette facture",array('confirm-delete')),
            ),
            array(
                "Valider" => array('factures/valider','ok',false,'facture_valider',null,array('modify', 'positive')),
            ),
            array(
                "Aperçu<br>facture" => array('factures/exporter_pdf','download',false,'facture_exporter_pdf',null,array('download', 'download-pdf')),
                "Imprimer<br>facture" => array('factures/imprimer_pdf','print',false,'facture_imprimer_pdf',null,array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('factures/envoyer_email','send',false,'facture_envoyer_email',null,array('modify')),
                //"Transmise par<br>courrier" => array('factures/marquer_transmise','envelope',false,'facture_marquer_transmise',null,array('modify')),
            ),
            array(
                "Saisir<br>règlement" => array('reglements/nouveau','euro',false,'facture_saisir_reglement',null,array('form', 'positive')),
                "Transférer<br>en avoir" => array('*factures/transferer_avoir','retweet',false,'facture_transferer_avoir',null,array('post', 'negative')),
            ),
            array(
				"Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Imprimer<br>liste" => array('#','print',false,'impression'),
            ),
        ),
        'Element' => array(
            array(
                "Consulter<br>Modifier" => array('*factures/lignes','eye-open',false,'facture_lignes'),
                "Dupliquer" => array('factures/dupliquer','duplicate',false,'facture_dupliquer',"Confirmer la duplication de cette facture",array('confirm-modify')),
                "Supprimer" => array('factures/suppression','trash',false,'facture_supprimer',"Confirmer la suppression de cette facture",array('confirm-delete')),
            ),
            array(
                "Valider" => array('factures/valider','ok',false,'facture_valider',null,array('modify', 'positive')),
            ),
            array(
                "Aperçu<br>facture" => array('factures/exporter_pdf','download',false,'facture_exporter_pdf',null,array('download', 'download-pdf')),
                "Imprimer<br>facture" => array('factures/imprimer_pdf','print',false,'facture_imprimer_pdf',null,array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('factures/envoyer_email','send',false,'facture_envoyer_email',null,array('modify')),
                //"Transmise par<br>courrier" => array('factures/marquer_transmise','envelope',false,'facture_marquer_transmise',null,array('modify')),
            ),
            array(
                "Saisir<br>règlement" => array('reglements/nouveau','euro',false,'facture_saisir_reglement',null,array('form', 'positive')),
                "Transférer<br>en avoir" => array('*factures/transferer_avoir','retweet',false,'facture_transferer_avoir',null,array('post', 'negative')),
            ),
        ),
        'Edition' => array(
            array(
                "Consulter<br>Modifier" => array('#','pencil',true,'facture_lignes'),
                "Dupliquer" => array('factures/dupliquer','duplicate',false,'facture_dupliquer',"Confirmer la duplication de cette facture",array('confirm-modify')),
                "Supprimer" => array('factures/suppression','trash',false,'facture_supprimer',"Confirmer la suppression de cette facture",array('confirm-delete')),
            ),
            array(
                "Enregistrer<br>articles" => array('#','save',false,'enregistrerLignes',null,array('positive')),
            ),
            array(
                "Valider" => array('factures/valider','ok',false,'facture_valider',null,array('modify', 'positive')),
            ),
            array(
                "Aperçu<br>facture" => array('factures/exporter_pdf','download',false,'facture_exporter_pdf',null,array('download', 'download-pdf')),
                "Imprimer<br>facture" => array('factures/imprimer_pdf','print',false,'facture_imprimer_pdf',null,array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('factures/envoyer_email','send',false,'facture_envoyer_email',null,array('modify')),
                //"Transmise par<br>courrier" => array('factures/marquer_transmise','envelope',false,'facture_marquer_transmise',null,array('modify')),
            ),
            array(
                "Saisir<br>règlement" => array('reglements/nouveau','euro',false,'facture_saisir_reglement',null,array('form', 'positive')),
                "Transférer<br>en avoir" => array('*factures/transferer_avoir','retweet',false,'facture_transferer_avoir',null,array('post', 'negative')),
            ),
        ),
        'Factures_Client' => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail'),
            ),
            array(
                "Consulter<br>Modifier" => array('*factures/lignes','eye-open',false,'facture_detail',null,array('dblclick')),
            ),
            array(
                "Devis" => array('devis/devis_client[]','list-alt',true,'devis'),
                "Commandes" => array('commandes/commandes_client[]','shopping-cart',true,'commandes'),
                "Factures" => array('factures/factures_client[]','folder-open',true,'factures'),
                "Avoirs" => array('avoirs/avoirs_client[]','retweet',true,'avoirs'),
                "Réglements" => array('reglements/reglements_client[]','euro',true,'reglements'),
            ),
            array(
                "Documents" => array('documents_contacts/documents_contact[]','paperclip',true,'documents'),
            ),
            array(
                "Evènements" => array('evenements/evenements_client[]','calendar',true,'evenements'),
            ),
            array(
                "Correspondants" => array('correspondants/correspondants_contact[]','user',true,'correspondants'),
            ),
            array(
				"Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                "Imprimer<br>liste" => array('#','print',false,'impression'),
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_factures');
    }

    /******************************
    * Factures du contact [CONTACT]
    ******************************/
    public function factures_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'factures/factures_client',
            'detail' => array('factures/detail','fac_id','fac_reference'),
            'champs' => array(
                array('fac_numero','number',"Numéro"),
                array('fac_reference','text',"Référence"),
                array('fac_date','date',"Date facture"),
                array('fac_tva','number',"Taux de TVA"),
                array('fac_montant_ht','number',"Montant HT"),
                array('fac_montant_ttc','number',"Montant TTC"),
                array('fac_delai_paiement','number',"Délai de paiement"),
                array('fac_date_paiement','text',"Date de paiement"),
                array('vtf_type','ref',"Type de facture",'v_types_factures'),
                array('vef_etat','ref',"Etat",'v_etats_factures'),
                array('cmd_reference','ref',"Commande associée",'commandes','fac_commande','cmd_reference'),
                array('ctc_nom','ref',"Client",'contacts','dvi_client','ctc_nom'),
                array('fac_regle','number',"Montant réglé"),
                array('fac_reste','number',"Montant restant à régler"),
                //array('fac_fichier','hreffile','Pdf', '/'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_factures->liste_par_client_filterable_columns(),
            'select_etats' => $this->m_factures->get_etats(),
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'factures',
                'methode' => 'factures_client'
            ),true);
		*/
		
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'factures',
                'methode'               => 'factures_client',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar_data' => array(
				'controleur' => 'factures',
                ),
            ), true);
        $scripts[] = $this->load->view("factures/liste-js", array(), true);
        $scripts[] = $this->load->view("reglements/form-js", array(), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('factures',$this->session->id);
        $data = array(
            'title' => "Factures du contact [CONTACT]",
            'page' => "templates/datatables",
            'menu' => "Ventes|Factures",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action['Factures_Client'],
            'controleur' => 'factures',
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
    * Factures du contact [CONTACT] (datasource)
    ******************************/
    public function factures_client_json($id=0) {
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
            $resultat = $this->m_factures->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'fac_reference' => 't_factures',
                'fac_date' => 't_factures',
                'cmd_reference' => 't_commandes',
                'dvi_reference' => 't_devis',
                'ctc_nom' => 't_contacts'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_factures->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_factures->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        //foreach($resultat['data'] as $v) {
        //    $v->fac_fichier = construit_lien_fichier("",$v->fac_fichier);
        //}
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Transférer en avoir
    * support AJAX
    ******************************/
    public function transferer_avoir($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';

        $data = $this->m_factures->detail($id);
        if (! ($data->fac_etat > 1 AND $data->fac_etat < 9)) {
            $this->my_set_action_response($ajax, false, "Opération non autorisée");
        }
        else {
            $avoir_id = $this->m_factures->transferer_avoir($id);
            if ($avoir_id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $redirection = 'avoirs/lignes/'.$avoir_id;
                $ajaxData = array(
                    'event' => array(
                        array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type' => 'recordchange',
                            'id' => $id,
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                        array(
                            'controleur' => 'avoirs',
                            'type' => 'recordadd',
                            'id' => $avoir_id,
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                    ),
                    'url' => site_url($redirection),
                );
                $this->my_set_action_response($ajax,true,"La facture a été transférée en avoir", 'info', $ajaxData);
            }
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
    * Envoyer par email
    * AJAX
    ******************************/
    public function envoyer_email($id=0) {
        $ajax = true;
        $data = $this->m_factures->detail($id);
        if (!$data) {
            $this->my_set_action_response($ajax,false,"La facture n'a pas été trouvée");
        }
        elseif (! ($data->fac_etat > 1)) {
            $this->my_set_action_response($ajax,false, "Opération non autorisée");
        } else {
            try {
                $resultat = $this->m_factures->envoyer_email($id);
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
                    $this->my_set_action_response($ajax,true, "La facture a été envoyée par email", 'info', $ajaxData);
                }
            } catch (MY_Exceptions_NoEmailAddress $e) {
                $this->my_set_action_response($ajax,false,"Le contact n'a pas d'adresse email");

            } catch (MY_Exceptions_NoSuchFile $e) {
                // Attempt to generate the PDF to try again to send the email
                try {
                    if ($this->m_factures->generer_pdf($id)) {
                        return $this->envoyer_email($id, $ajax);
                    }
                } catch (Exception $e) {
                    // This is hopeless. Give up!
                }
                $this->my_set_action_response($ajax,false,"La facture PDF n'a pas pu être générée");

            } catch (MY_Exceptions_NoSuchUser $e) {
                $this->my_set_action_response($ajax,false,"La facture n'a pas de correspondant, ni de client associé");

            } catch (MY_Exceptions_NoSuchRecord $e) {
                $this->my_set_action_response($ajax,false,"La facture n'a pas été trouvée");

            } catch (MY_Exceptions_NoSuchTemplate $e) {
                $this->my_set_action_response($ajax,false,"Pas de message type disponible pour envoyer le mail");
            }
        }
    }

    /******************************
     * Exporter ou imprimer PDF
     * support AJAX
     ******************************/
    protected function _pdf($controleur,$id=0,$ajax=false) {
        $redirection = $this->session->userdata('_url_retour');
        try {
            $pdf = $this->m_factures->pdf($id);
            if (!$pdf) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $url = site_url($controleur.'?ref='.$pdf['path']);
                $redirection = $url;
                if ($pdf['created'] === true) {
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type' => 'recordchange',
                            'id' => $id,
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                        'url' => $url,
                    );
                    $this->my_set_action_response($ajax, true, "La facture a été générée", 'info', $ajaxData);
                } elseif ($ajax) {
                    $payload = array(
                        'success' => true,
                        'data' => array(
                            'url' => $url,
                        ),
                    );
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($payload));
                }
            }
        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"La facture n'a pas été trouvée");
            $redirection = 'factures';

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de modèle existant pour générer la facture PDF");
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
    * Marquer transmise
    * AJAX
    ******************************/
    public function marquer_transmise($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $data = $this->m_factures->detail($id);
        if (! ($data->fac_etat > 1)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_factures->marquer_transmise($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
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
                $this->my_set_action_response($ajax,true,"La facture a été marquée transmise par courrier",'info',$ajaxData);
                $redirection = "factures";
            }
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Valider
    * AJAX
    ******************************/
    public function valider($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $data = $this->m_factures->detail($id);
        if (! ($data->fac_etat == 1)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_factures->valider($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = $this->session->userdata('_url_retour');
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
                        'redirect' => site_url('factures/detail2/'.$id),
                    ),
                );
                $this->my_set_action_response($ajax,true,"La facture a été validée",'info',$ajaxData);
                $redirection = "factures";
            }
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Dupliquer
    * AJAX
    ******************************/
    public function dupliquer($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $id = $this->m_factures->dupliquer($id);
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
            $this->my_set_action_response($ajax,true,"La facture a été dupliquée",'info',$ajaxData);
            $redirection = "factures";
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Détail d'une facture
    ******************************/
    public function detail($id, $ajax = false) {
        $this->load->helper(array('form','ctrl'));

        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';

        if (count($_POST) > 0) {
            redirect($redirection);
        }
        //else {
            $valeurs = $this->m_factures->detail($id);
            if (!$valeurs) {
                $this->my_set_action_response($ajax, false, "Pas de facture trouvée avec cette référence");
                if ($ajax) {
                    return;
                }
                redirect($redirection);
            }

            if (empty($valeurs->cmd_reference)) {
                $valeurs->cmd_reference = 'Commande sans numéro de référence';
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
                   'fac_reference' => array("Référence",'VARCHAR 30','text','fac_reference'),
                   'fac_date' => array("Date facture",'DATE','date','fac_date'),
                   'fac_commande' => array("Commande associée",'REF','ref',array('commandes','fac_commande','cmd_reference')),
                   'dvi_client' => array("Client",'REF','ref',array('contacts','dvi_client','ctc_nom')),
                   'dvi_societe_vendeuse' => array("Enseigne",'REF','ref',array('societes_vendeuses','dvi_societe_vendeuse','scv_nom')),
                   'dvi_correspondant' => array("Correspondant",'REF','ref',array('correspondants','dvi_correspondant','cor_nom')),
                   'fac_tva' => array("Taux de TVA",'DECIMAL 6,4','number','fac_tva'),
                   'fac_montant_ht' => array("Montant HT",'DECIMAL 8,2','number','fac_montant_ht'),
                   'fac_montant_tva' => array("Montant TVA",'DECIMAL 8,2','number','fac_montant_tva'),
                   'fac_montant_ttc' => array("Montant TTC",'DECIMAL 8,2','number','fac_montant_ttc'),
                   'fac_delai_paiement' => array("Délai de paiement",'INT 3','number','fac_delai_paiement'),
                   'fac_date_paiement' => array("Date de paiement",'SQL','date','fac_date_paiement'),
                   'fac_regle' => array("Montant réglé",'DECIMAL 8,2','number','fac_regle'),
                   'fac_reste' => array("Montant restant à régler",'DECIMAL 8,2','number','fac_reste'),
                   'fac_type' => array("Type de facture",'REF','text','vtf_type'),
                   'fac_etat' => array("État",'REF','text','vef_etat'),
                   'fac_notes' => array("Remarques",'VARCHAR 1000','textarea','fac_notes'),
                   //'fac_fichier' => array("PDF",'FICHIER','text','fac_fichier'),
                   'fac_reprise' => array("Reprise",'INT 1','number','fac_reprise')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->barre_action['Element'];
            $fac_etat = $valeurs->fac_etat;
            if ($fac_etat > 1) {
                $barre_action = modifie_action_barre_action($barre_action,'lignes_factures/constitution','lignes_factures/lignes_facture');
            } elseif ($fac_etat == 1) {
                $barre_action = modifie_action_barre_action($barre_action,'factures/detail2','factures/lignes');
                $barre_action = modifie_action_barre_action($barre_action,'lignes_factures/lignes_facture','lignes_factures/constitution');
            }

            $barre_action = $this->_masque_demasque_actions($barre_action, $valeurs);
            $barre_action = modifie_action_barre_action($barre_action,'reglements/nouveau','reglements/nouveau/'.$valeurs->dvi_client.'/');

            $data = array(
                'title' => "Détail d'une facture",
                'page' => "templates/detail",
                'menu' => "Ventes|Facture",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'factures',
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
     * @param $barre_action array      Barre d'action à modifier
     * @param $facture      M_factures Les infos de la facture
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $facture) {
        $fac_etat = $facture->fac_etat;
        $etats = array(
            'factures/detail'                => true,
            'factures/genere_pdf'            => true,
            'factures/exporter_pdf'          => true,
            'factures/imprimer_pdf'          => true,
            'factures/modification'          => $fac_etat == 1,
            'factures/dupliquer'             => true,
            'factures/valider'               => $fac_etat == 1,
            'factures/suppression'           => $fac_etat == 1,
            'factures/envoyer_email'         => $fac_etat != 1,
            'factures/marquer_transmise'     => $fac_etat != 1,
            'reglements/nouveau'             => $fac_etat != 1,
            'factures/transferer_avoir'      => $fac_etat == 2,
            'lignes_factures/lignes_facture' => $fac_etat > 1,
            'factures/lignes'                => $fac_etat == 1,
            'lignes_factures/constitution'   => $fac_etat == 1,
            'factures/apercu_html'           => true,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Suppression d'une facture
    * Support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';

        $data = $this->m_factures->detail($id);
        if (! ($data->fac_etat == 1)) {
            $this->my_set_action_response($ajax, false, "Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_factures->suppression($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            }
            else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type' => 'recorddelete',
                        'id' => $id,
                        'timeStamp' => round(microtime(true) * 1000),
                        'redirect' => $redirection,
                    ),
                );
                $this->my_set_action_response($ajax, true, "La facture a été supprimée", 'info', $ajaxData);
            }
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Liste des factures
     ******************************/
    public function index() {
        // descripteur
        $descripteur = array(
            'datasource' => 'factures/index',
            //'detail' => array('factures/detail','ctc_id','ctc_nom'),
            'champs' => $this->m_factures->get_champs('read'),
            'filterable_columns' => $this->m_factures->get_filterable_columns(),
            'select_etats' => $this->m_factures->get_etats()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_referes');
        $referes = $q->result();
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                //'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>'',
                'controleur' => 'factures',
                'methode' => __FUNCTION__,
            ),true);
        $scripts[] = $this->load->view('factures/liste-js',array(),true);
        $scripts[] = $this->load->view('factures/liste',array('referes' => $referes),true);
        $scripts[] = $this->load->view('reglements/form-js',array(),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('factures',$this->session->id);
        $data = array(
            'title' => "Factures",
            //'page' => "factures/liste",
            'page' => "templates/datatables",
            'menu' => "Ventes|Factures",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action['Liste'],
            'controleur' => 'factures',
            'methode' => __FUNCTION__,
            'values' => array(
                //'id' => $id,
                'cmd_globales' => array(),
                'toolbar'=>'',
                'descripteur' => $descripteur,
                'vues' => $vues
            )
        );
        //$layout="layouts/standard";
        $layout="layouts/datatables";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des contacts (DataTables)
    ******************************/
    public function index_json($id=0, $export = false) {
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
            $resultat = $this->m_factures->liste($id,$pagelength, $pagestart, $filters, array("`fac_date`", "`fac_reference`"), array("DESC", "DESC"));
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];
            if ( $order_col_id>=0 && $order_col_id<=count($columns))
            {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_factures->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else
                $resultat = $this->m_factures->liste($id,$pagelength, $pagestart, $filters);
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_factures->get_champs('read');
            $params = array(
                'records' => $resultat['data'],
                'columns' => $champs,
                'filename' => 'Factures'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Liste des contacts (DataTables CLIENT mode)
    ******************************/
    public function index_json_client($id=0) {
        if (! $this->input->is_ajax_request()) die('');

        $resultat = $this->m_factures->liste_chunk(0, 1, NULL);    // limit=0 i.e. get all records
                                                                // start=1 will be ignored
                                                                // filters=NULL i.e. filtering happening on client side

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /**
     * @param string $barre
     * @param M_factures $facture
     * @return array
     */
    protected function _get_barre_action($barre, $facture) {

        $barre_action = $this->barre_action[$barre];
        $fac_etat = $facture->fac_etat;
        if ($fac_etat > 1) {
            $barre_action = modifie_action_barre_action($barre_action,'lignes_factures/constitution','lignes_factures/lignes_facture');
        } elseif ($fac_etat == 1) {
            $barre_action = modifie_action_barre_action($barre_action,'factures/detail2','factures/lignes');
            $barre_action = modifie_action_barre_action($barre_action,'lignes_factures/lignes_facture','lignes_factures/constitution');
        }

        $barre_action = $this->_masque_demasque_actions($barre_action, $facture);
        $barre_action = modifie_action_barre_action($barre_action,'reglements/nouveau','reglements/nouveau/'.$facture->dvi_client.'/');

        return $barre_action;
    }

    /******************************
     * Consultation de facture
     ******************************/
    public function detail2($id) {
        $facture = $this->m_factures->detail($id);
        $this->load->model('m_societes_vendeuses');
        $facture->enseigne = $this->m_societes_vendeuses->detail($facture->dvi_societe_vendeuse);

        $scripts = array();
        $scripts[] = $this->load->view('factures/detail2-js',array('id'=>$id,'tva'=>$facture->fac_tva),true);
        $scripts[] = $this->load->view('reglements/form-js',array(),true);

        $barre_action = $this->_get_barre_action('Element', $facture);

        $data = array(
            'title' => "Détail d'une facture",
            'page' => "factures/detail2",
            'menu' => "Ventes|Détail facture",
            'barre_action' => $barre_action,
            'id' => $id,
            'scripts' => $scripts,
            'values' => $facture,
            'controleur' => 'factures',
            'methode' => __FUNCTION__,
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Mise à jour d'une facture
     ******************************/
    public function lignes($id=0) {
        $facture = $this->m_factures->detail($id);
        if (! ($facture->fac_etat == 1)) {
            redirect("factures/detail2/$id");
        }

        /**
         * @var M_societes_vendeuses $m_societes_vendeuses
         */
        $this->load->model('m_societes_vendeuses');
        $m_societes_vendeuses = $this->m_societes_vendeuses;
        $facture->enseigne = $m_societes_vendeuses->detail($facture->dvi_societe_vendeuse);

        $this->load->helper(array('form','ctrl'));
        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_familles');
        $familles = $q->result();
        $tva = tva();
        $scripts = array();
        $scripts[] = $this->load->view('factures/mise_a_jour-js',array('id'=>$id,'tva'=>$tva,'familles' => $familles),true);
        foreach ($familles as $f) {
            $scripts[] = $this->load->view('_catalogues/'.$f->vfm_nom.'-js',array(),true);
        }

        $barre_action = $this->_get_barre_action('Edition', $facture);

        $listes_valeurs = new stdClass();
        $listes_valeurs->fac_type = $this->m_factures->liste_types_factures();

        $this->load->model('m_correspondants');
        $listes_valeurs->avr_correspondant = $this->m_correspondants->liste_option($facture->dvi_client);

        $data = array(
            'title' => "Mise à jour d'une facture",
            'page' => "factures/mise_a_jour",
            'menu' => "Ventes|Mise à jour de facture",
            'barre_action' => $barre_action,
            'controleur' => 'factures',
            'methode' => __FUNCTION__,
            'scripts' => $scripts,
            'values' => array(
                'id' => $id,
                'familles' => $familles,
                'tva' => $tva,
                'values' => $facture
            ),
            'listes_valeurs' => $listes_valeurs,
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Mise à jour d'une facture
     * support AJAX
     ******************************/
    public function modification($id=0, $ajax=false) {
        $facture = $this->m_factures->detail($id);
        if ($facture->fac_etat != 1) {
            $this->my_set_action_response($ajax,false, 'Opération non autorisée');
            if ($ajax) {
                return;
            }
            redirect("factures/detail2/$id");
        }
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        $db = $this->db;

        // règles de validation
        $config = array(
            array('field'=>'fac_date','label'=>"Date",'rules'=>'required'),
            array('field'=>'fac_delai_paiement','label'=>"Délai de paiement",'rules'=>'trim|numeric|required|greater_than_equal_to[0]'),
            array('field'=>'fac_type','label'=>"Type de facture",'rules'=>'required'),
            array('field'=>'fac_notes','label'=>"Remarques",'rules'=>'trim'),
            array(
                'field' => 'fac_reference',
                'label' => "Numéro de pièce",
                'rules' => array(
                    'trim',
                    'max_length[30]',
                    array(
                        'fac_reference_unique',
                        function ($str) use ($db, $facture) {
                            return $db->where('fac_reference', $str)
                                ->where('fac_inactif IS NULL')
                                ->where_not_in('fac_id', array($facture->fac_id))
                                ->where('dvi_societe_vendeuse', $facture->dvi_societe_vendeuse)
                                ->join('t_commandes', 'fac_commande = cmd_id')
                                ->join('t_devis', 'cmd_devis = dvi_id')
                                ->count_all_results('t_factures') == 0;
                        }
                    ),
                )
            ),
            array(
                'field' => 'fac_numero',
                'label' => "Numéro comptable",
                'rules' => array(
                    'trim',
                    'numeric',
                    'greater_than[0]',
                    array(
                        'fac_numero_unique',
                        function ($str) use ($facture, $db) {
                            return $db->where('fac_numero', $str)
                                    ->where('fac_inactif IS NULL')
                                    ->where_not_in('fac_id', array($facture->fac_id))
                                    ->where('dvi_societe_vendeuse', $facture->dvi_societe_vendeuse)
                                    ->join('t_commandes', 'fac_commande = cmd_id')
                                    ->join('t_devis', 'cmd_devis = dvi_id')
                                    ->count_all_results('t_factures') == 0;
                        }
                    ),
                )
            ),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        $this->form_validation->set_message('fac_reference_unique', 'Ce numéro de pièce est déjà utilisé par une autre facture');
        $this->form_validation->set_message('fac_numero_unique', 'Ce numéro est déjà utilisé par une autre facture');

        // validation des fichiers chargés
        //$validation = true;
        $this->form_validation->set_rules($config);

        //if ($this->form_validation->run() AND $validation) {
        if ($this->form_validation->run()) {

            // validation réussie
            $valeurs = array(
                'fac_date' => formatte_date_to_bd($this->input->post('fac_date')),
                'fac_delai_paiement' => $this->input->post('fac_delai_paiement'),
                'fac_type' => $this->input->post('fac_type'),
                'fac_notes' => $this->input->post('fac_notes')
            );
            if (strlen($this->input->post('fac_reference')) > 0) {
                $valeurs['fac_reference'] = $this->input->post('fac_reference');
            }
            if (strlen($this->input->post('fac_numero')) > 0) {
                $fac_numero = intval($this->input->post('fac_numero'), 10);
                $valeurs['fac_numero'] = $fac_numero;
            } else {
                $fac_numero = intval($facture->fac_numero, 10);
            }

            $resultat = $this->m_factures->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = 'factures/detail2/'.$id;
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message = "La facture a été modifiée";
                    $ajaxData = array(
                        'event' => array(
                            array(
                                'controleur' => $this->my_controleur_from_class(__CLASS__),
                                'type' => 'recordchange',
                                'id' => $id,
                                'timeStamp' => round(microtime(true) * 1000),
                            ),
                        ),
                    );
                    if (!empty($fac_numero)) {
                        // On mets à jour le compteur de factures si le numéro de facture a changé
                        if ($fac_numero != intval($facture->fac_numero, 10)) {
                            $max_fac_numero = $this->m_factures->plus_grand_numero($facture->dvi_societe_vendeuse);
                            $this->db->where('scv_id', $facture->dvi_societe_vendeuse)
                                ->update(
                                    't_societes_vendeuses',
                                    array(
                                        'scv_no_facture' => $max_fac_numero,
                                    )
                                );
                            $message .= "\n<br>Le compteur de factures à été mis à ".$max_fac_numero." pour ".$facture->scv_nom;
                            $ajaxData['event'][] = array(
                                'controleur' => 'societes_vendeuses',
                                'type' => 'recordchange',
                                'id' => $facture->dvi_societe_vendeuse,
                                'timeStamp' => round(microtime(true) * 1000),
                            );
                        }
                    }
                }
                $this->my_set_action_response($ajax,true,$message, 'info', $ajaxData);
                $redirection = "factures";
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {
            // validation en échec ou premier appel : affichage du formulaire

            $this->load->model('m_societes_vendeuses');
            $facture->enseigne = $this->m_societes_vendeuses->detail($facture->dvi_societe_vendeuse);

            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('fac_delai_paiement');
            if (isset($valeur)) {
                $facture->fac_delai_paiement = $valeur;
            }
            $valeur = $this->input->post('fac_notes');
            if (isset($valeur)) {
                $facture->fac_notes = $valeur;
            }
            $valeur = $this->input->post('fac_reference');
            if (isset($valeur)) {
                $facture->fac_reference = $valeur;
            }
            $valeur = $this->input->post('fac_type');
            if (isset($valeur)) {
                $facture->fac_type = $valeur;
            }
            $this->db->order_by('vtf_type','ASC');
            $q = $this->db->get('v_types_factures');
            $listes_valeurs->fac_type = $q->result();

            // descripteur
            $descripteur = array(
                'champs' => $this->m_factures->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = $this->_get_barre_action('Element', $facture);

            $data = array(
                'title' => "Mise à jour d'une facture",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour de facture",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $facture,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'factures',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Liste des factures impayées
     * AJAX
     ******************************/
    public function impayees($commande) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_factures->liste_commande($commande);
        if ($resultat == false) {
            $this->my_set_action_response(true,false);
            return;
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Envoi des relances (appelé en AJAX par le composant Grid)
     ******************************/
    public function relance() {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_factures->relance($this->input->post('type'),$this->input->post('facture'));
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Mise en place d'un rappel (appelé en AJAX par le composant Grid)
     ******************************/
    public function rappel($commande) {
        if (! $this->input->is_ajax_request()) die('');
        $this->load->model('m_taches');
        switch($commande) {
            case 0: // création de la tâche
                $date_rappel = formatte_date_to_bd($this->input->post('rappel'));
                $valeurs = array(
                    'tac_titre' => 'Rappel relance facture',
                    'tac_info' => $this->input->post('comment'),
                    'tac_description' => $this->input->post('info'),
                    'tac_debut_prevu' => $date_rappel,
                    'tac_etat' => 1,
                    'tac_type' => 9,
                    'tac_emetteur' => $this->session->id,
                    'tac_employe' => $this->session->id
                );
                $id = $this->m_taches->nouveau_utilisateur($valeurs);
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($id));
                break;
            case 1: // suppression de la tâche
                $resultat = $this->m_taches->annuler($this->input->post('id'));
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($resultat));
                break;
            case 2: // modification de la tâche
                $id = $this->input->post('id');
                $date_rappel = formatte_date_to_bd($this->input->post('rappel'));
                $valeurs = array(
                    'tac_titre' => 'Rappel relance facture',
                    'tac_info' => $this->input->post('comment'),
                    'tac_description' => $this->input->post('info'),
                    'tac_debut_prevu' => $date_rappel,
                    'tac_etat' => 1,
                    'tac_type' => 9,
                    'tac_emetteur' => $this->session->id,
                    'tac_employe' => $this->session->id
                );
                $resultat = $this->m_taches->maj($valeurs,$id);
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($resultat));
                break;
            default:
        }
    }

    /******************************
     * Aperçu de la facture en HTML
     ******************************/
    public function apercu_html($id) {
        $contenu = $this->m_factures->generer_pdf($id,false);
        $data = array(
            'title' => "Facture",
            'values' => $contenu
        );
        $layout="layouts/vide";
        $this->load->view($layout,$data);
    }

    /******************************
     * Génération de pdf
     * support AJAX
     ******************************/
    public function genere_pdf($id, $ajax=false) {
        $redirection = $this->session->userdata('_url_retour');
        try {
            $chemin = $this->m_factures->generer_pdf($id);
            if ($chemin === false) {
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
                $this->my_set_action_response($ajax, true, "Le PDF a été généré", 'info', $ajaxData);
                $redirection = construit_url_fichier('', $chemin);
            }
        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"La facture n'a pas été trouvée");
            $redirection = 'factures';

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de modèle existant pour générer la facture PDF");
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Merge pdf
     * Author: Kanav
     ******************************/
    public function merge_pdfs() {
        /**
         * @var PdfConcat $pdfconcat
         */
        $this->load->library('pdfconcat');
        $pdfconcat = $this->pdfconcat;
        $path = $this->input->post('path');
        $pdfs = array_map(function($v) use ($path){
            return $path.$v;
        }, $this->input->post('pdfs'));

        $pdfconcat->setFiles($pdfs);
        $pdfconcat->concat();

        $randomName = $this->rand_str().".pdf";
        $pdfconcat->Output($path.$randomName, 'F');
        echo $randomName;
    }

    private function rand_str($length=30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// EOF