<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_devis    $m_devis
* @property M_contacts $m_contacts
*/
class Devis extends MY_Controller {
    private $profil;
    private $barre_action = array(
        'Devis' => array(
            array(
                "Nouveau<br>devis" => array('devis/nouveau','plus',true,'devis_nouveau',null,array('form')),
            ),
            array(
                "Consulter<br>Modifier" => array('*devis/lignes','eye-open',false,'devis_modification',null,array('dblclick')),
                "Dupliquer" => array('devis/dupliquer','duplicate',false,'devis_dupliquer',"Veuillez confirmer la duplication du devis",array('confirm-modify')),
                "Supprimer" => array('devis/suppression','trash',false,'devis_supprimer',"Veuillez confirmer la suppression du devis",array('confirm-delete'))
            ),
            array(
                "Aperçu<br>devis" => array('devis/exporter_pdf','download',false,'devis_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>devis" => array('devis/imprimer_pdf','print',false,'devis_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('devis/envoyer_email','send',false,'devis_envoyer_email',null,array('modify')),
                //"Transmis par<br>courrier" => array('devis/marquer_transmis','envelope',false,'devis_marquer_transmis',null,array('modify')),
            ),
            array(
                "Marquer<br>refusé" => array('devis/marquer_refus','remove',false,'devis_marquer_refus',null,array('modify', 'negative')),
                "Transformer en<br>commande" => array('devis/passer_commande','ok',false,'devis_passer_commande',null,array('modify', 'positive')),
            ),
            array(
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                //"Imprimer<br>liste" => array('#','print',false,'impression'),
            ),
        ),
        'Nouveau' => array(
            array(
                "Nouveau<br>Correspondant" => array('correspondants/nouveau','user',true,'correspondants_nouveau', null, array('form')),
            ),
        ),
        'Element' => array(
            array(
                "Consulter<br>Modifier" => array('#','eye-open',true,'devis_modification'),
                "Dupliquer" => array('devis/dupliquer','duplicate',true,'devis_dupliquer',"Veuillez confirmer la duplication du devis",array('confirm-modify')),
                "Supprimer" => array('devis/suppression','trash',false,'devis_supprimer',"Veuillez confirmer la suppression du devis",array('confirm-delete'))
            ),
            array(
                "Aperçu<br>devis" => array('devis/exporter_pdf','download',true,'devis_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>devis" => array('devis/imprimer_pdf','print',true,'devis_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('devis/envoyer_email','send',true,'devis_envoyer_email',null,array('modify')),
                //"Transmis par<br>courrier" => array('devis/marquer_transmis','envelope',true,'devis_marquer_transmis',null,array('modify')),
            ),
            array(
                "Marquer<br>refusé" => array('devis/marquer_refus','remove',false,'devis_marquer_refus',null,array('modify', 'negative')),
                "Transformer en<br>commande" => array('devis/passer_commande','ok',false,'devis_passer_commande',null,array('modify', 'positive')),
            ),
        ),
        'Edition' => array(
            array(
                "Consulter<br>Modifier" => array('#','pencil',true,'devis_modification'),
                "Dupliquer" => array('devis/dupliquer','duplicate',false,'devis_dupliquer',"Veuillez confirmer la duplication du devis",array('confirm-modify')),
                "Supprimer" => array('devis/suppression','trash',false,'devis_supprimer',"Veuillez confirmer la suppression du devis",array('confirm-delete')),
            ),
            array(
                "Enregistrer<br>articles" => array('#','save',false,'enregistrerLignes',null,array('positive')),
            ),
            array(
                "Aperçu<br>devis" => array('devis/exporter_pdf','download',false,'devis_exporter_pdf', null, array('download', 'download-pdf')),
                "Imprimer<br>devis" => array('devis/imprimer_pdf','print',false,'devis_imprimer_pdf', null, array('print', 'print-pdf')),
            ),
            array(
                "Envoyer par<br>email" => array('devis/envoyer_email','send',false,'devis_envoyer_email',null,array('modify')),
                //"Transmis par<br>courrier" => array('devis/marquer_transmis','envelope',false,'devis_marquer_transmis',null,array('modify')),
            ),
            array(
                "Marquer<br>refusé" => array('devis/marquer_refus','remove',false,'devis_marquer_refus',null,array('modify', 'negative')),
                "Transformer en<br>commande" => array('devis/passer_commande','ok',false,'devis_passer_commande',null,array('modify', 'positive')),
            ),
        ),
        'Devis_Client' => array(
            array(
                "Fiche Contact" => array('contacts/detail','user',true,'contacts_detail',null,array('view')),
            ),
            array(
                "Nouveau<br>devis" => array('devis/nouveau','plus',true,'devis_nouveau',null,array('form')),
            ),
            array(
                "Consulter<br>Modifier" => array('*devis/lignes','eye-open',false,'devis_detail2',array('dblclick')),
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
                "Envoyer email" => array('evenements/email_contact','send',true,'envoi_email',null,array('form')),
                //"Courrier type" => array('documents_contacts/nouveau','envelope',true,'courrier_type'),
            ),
            array(
                "Liste<br>Excel"   => array('#', 'list-alt', true, 'export_xls'),
                "Liste<br>PDF" => array('#','book',false,'export_pdf'),
                //"Imprimer<br>liste" => array('#','print',false,'impression')
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_devis');
        $this->load->model('m_contacts');
        $this->load->model('m_articles');
    }

    /******************************
    * Devis du contact [CONTACT]
    ******************************/
    public function devis_client($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'devis/devis_client',
            'detail' => array('devis/detail','dvi_id','dvi_reference'),
            'champs' => array(
                array('dvi_numero','number',"Numéro"),
                array('dvi_reference','text',"Référence"),
                array('dvi_date','date',"Date devis"),
                array('dvi_tva','number',"Taux de TVA"),
                array('vch_degre','ref',"Degré de chaleur",'v_chaleur'),
                array('cor_nom','ref',"Correspondant",'correspondants','dvi_correspondant','cor_nom'),
                array('ctc_nom','ref',"Client",'contacts','dvi_client','ctc_nom'),
                array('scv_nom','ref',"Enseigne",'societes_vendeuses','dvi_societe_vendeuse','scv_nom'),
                array('dvi_montant_ht','number',"Montant devis HT"),
                array('dvi_montant_ttc','number',"Montant devis TTC"),
                array('ved_etat','ref',"État",'v_etats_devis'),
                //array('dvi_fichier','fichier',"PDF"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_devis->liste_par_client_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/kendo_grid-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'devis',
                'methode' => 'devis_client'
            ),true);
        $scripts[] = $this->load->view('devis/nouveau-js',array(),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('devis',$this->session->id);
        $data = array(
            'title' => "Devis du contact [CONTACT]",
            'page' => "templates/kendo_grid",
            'menu' => "Ventes|Devis",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action['Devis_Client'],
            'controleur' => 'devis',
            'methode' => __FUNCTION__,
            'values' => array(
                'id' => $id,
                'vues' => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'=>$toolbar,
                'descripteur' => $descripteur
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
    * Devis du contact [CONTACT] (datasource)
    ******************************/
    public function devis_client_json($id=0) {
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
            $resultat = $this->m_devis->liste_par_client($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'dvi_reference' => 't_devis',
                'dvi_date' => 't_devis',
                'cor_nom' => 't_correspondants',
                'ctc_nom' => 't_contacts',
                'scv_nom' => 't_societes_vendeuses'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_devis->liste_par_client($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_devis->liste_par_client($id,$pagelength, $pagestart, $filters);
            }
        }
        /*foreach($resultat['data'] as $v) {
            $v->dvi_fichier = construit_lien_fichier("",$v->dvi_fichier);
        }*/
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Passer commande
    * support AJAX
    ******************************/
    public function passer_commande($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $data = $this->m_devis->detail($id);
        if (! (in_array($data->dvi_etat,array(1,2,3,7,9)))) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $cmd_id = $this->m_devis->passer_commande($id);
            if ($cmd_id === false) {
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
                        array(
                            'controleur' => 'commandes',
                            'type' => 'recordadd',
                            'id' => $cmd_id,
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                        array(
                            'controleur' => 'contacts',
                            'type' => 'recordchange',
                            'timeStamp' => round(microtime(true) * 1000),
                        ),
                        'redirect' => site_url('commandes/detail/'.$cmd_id),
                    ),
                );
                $this->my_set_action_response($ajax,true,"Le devis a été transformé en commande",'info',$ajaxData);
                $redirection = "commandes/detail/$id";
            }
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Marquer refus
    * support AJAX
    ******************************/
    public function marquer_refus($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $data = $this->m_devis->detail($id);
        if (! (in_array($data->dvi_etat,array(1,2,3,7,9)))) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_devis->marquer_refus($id);
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
                $this->my_set_action_response($ajax,true,"Le devis a été mis à l'état refusé",'info',$ajaxData);
                $redirection = "devis";
            }
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
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
            $resultat = $this->m_devis->envoyer_email($id);
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
                $this->my_set_action_response($ajax,true,"Le devis a été envoyé par email",'info',$ajaxData);
                $redirection = "devis";
            }
        } catch (MY_Exceptions_NoEmailAddress $e) {
            $this->my_set_action_response($ajax,false,"Le contact n'a pas d'adresse email");

        } catch (MY_Exceptions_NoSuchFile $e) {
            // Attempt to generate the PDF to try again to send the email
            try {
                if ($this->m_devis->generer_pdf($id)) {
                    return $this->envoyer_email($id, $ajax);
                }
            } catch (Exception $e) {
                // This is hopeless. Give up!
            }
            $this->my_set_action_response($ajax,false,"Le devis PDF n'a pas pu être généré");

        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"Le devis n'a pas été trouvé");

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
     * Exporter ou imprimer PDF
     * support AJAX
     ******************************/
    protected function _pdf($controleur,$id=0,$ajax=false) {
        $redirection = $this->session->userdata('_url_retour');
        try {
            $pdf = $this->m_devis->pdf($id);
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
                    $this->my_set_action_response($ajax, true, "Le devis a été généré", 'info', $ajaxData);
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
            $this->my_set_action_response($ajax,false,"Le devis n'a pas été trouvé");
            $redirection = 'devis';

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de modèle existant pour générer le devis PDF");
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
    * Marquer transmis par courrier
    * support AJAX
    ******************************/
    public function marquer_transmis($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $resultat = $this->m_devis->marquer_transmis($id);
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
            $this->my_set_action_response($ajax,true,"Le devis a été marqué transmis par courrier",'info',$ajaxData);
            $redirection = "devis";
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Dupliquer
    * support AJAX
    ******************************/
    public function dupliquer($id=0,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $id = $this->m_devis->dupliquer($id);
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
            $this->my_set_action_response($ajax,true,"Le devis a été dupliqué",'info',$ajaxData);
            $redirection = "devis";
        }
        if ($ajax) {
            return;
        }
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
    * Détail d'un devis
    * support AJAX
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_devis->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action['Element'],$valeurs);

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'dvi_reference' => array("Référence",'VARCHAR 30','text','dvi_reference'),
                   'dvi_date' => array("Date devis",'DATE','date','dvi_date'),
                   'dvi_chaleur' => array("Degré de chaleur",'REF','text','vch_degre'),
                   'dvi_client' => array("Client",'REF','ref',array('contacts','dvi_client','ctc_nom')),
                   'dvi_correspondant' => array("Correspondant",'REF','ref',array('correspondants','dvi_correspondant','cor_nom')),
                   'dvi_societe_vendeuse' => array("Enseigne",'REF','ref',array('societes_vendeuses','dvi_societe_vendeuse','scv_nom')),
                   'dvi_montant_ht' => array("Montant devis HT",'DECIMAL 8,2','number','dvi_montant_ht'),
                   'dvi_montant_ttc' => array("Montant devis TTC",'DECIMAL 8,2','number','dvi_montant_ttc'),
                   'dvi_etat' => array("État",'REF','text','ved_etat'),
                   'dvi_notes' => array("Remarques",'VARCHAR 1000','textarea','dvi_notes'),
                   //'dvi_fichier' => array("PDF",'FICHIER','text','dvi_fichier'),
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un devis",
                'page' => "templates/detail",
                'menu' => "Ventes|Détail devis",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'devis',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $devis        M_devis    Les infos du devis
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $devis) {
        $dvi_etat = $devis->dvi_etat;
        $etats = array(
            'devis/detail'                => $dvi_etat > 0,
            'devis/lignes'                => $dvi_etat < 2,
            'devis/dupliquer'             => $dvi_etat > 0,
            'devis/suppression'           => $dvi_etat > 0 && $dvi_etat < 3,
            'devis/genere_pdf'            => true,
            'devis/exporter_pdf'          => $dvi_etat > 0,
            'devis/imprimer_pdf'          => $dvi_etat > 0,
            'devis/envoyer_email'         => $dvi_etat > 0,
            'devis/marquer_transmis'      => $dvi_etat > 0,
            'devis/marquer_refus'         => in_array($dvi_etat,array(1,2,3,7,9)),
            'devis/passer_commande'       => in_array($dvi_etat,array(1,2,3,7,9)),
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Suppression d'un devis
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';

        $data = $this->m_devis->detail($id);
        if (! ($data->dvi_etat < 3)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
            $redirection = '';
        }
        else {
            $resultat = $this->m_devis->suppression($id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax,true,"Le devis a été supprimé", 'info', $ajaxData);
            }
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Liste des devis
     ******************************/
    public function index($id=0,$liste=0) {
        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'devis/index',
            'detail' => array('devis/detail','ctc_id','ctc_nom'),
            'champs' => $this->m_devis->get_champs('read'),
            'filterable_columns' => $this->m_devis->get_filterable_columns(),
            'select_etats' => $this->m_devis->get_etats()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        //$scripts[] = $this->load->view("templates/datatables-js-client",
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'devis',
                'methode' => 'index'
            ),true);
        $scripts[] = $this->load->view('devis/liste-js',array(),true);
        $scripts[] = $this->load->view('devis/liste',array(),true);

        $this->load->model('m_societes_vendeuses');
        $enseignes = $this->m_societes_vendeuses->liste_id_comptable(true);
        $enseignes_auto_id_comptables = array();
        foreach ($enseignes as $enseigne) {
            if ($enseigne->scv_id_comptable) {
                $enseignes_auto_id_comptables[] = $enseigne->scv_id;
            }
        }
        $scripts[] = $this->load->view('devis/nouveau-js', array(
            'enseignes' => $enseignes_auto_id_comptables,
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('devis',$this->session->id);
        $data = array(
            'title' => "Devis",
            'page' => "templates/datatables",
            'menu' => "Ventes|Devis",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action['Devis'],
            'controleur' => 'devis',
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
    * Liste des contacts (DataTables)
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
            $resultat = $this->m_devis->liste($id,$pagelength, $pagestart, $filters, array("`dvi_date`", "`dvi_reference`"), array("DESC", "DESC"));
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];
            if ( $order_col_id>=0 && $order_col_id<=count($columns))
            {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_devis->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else
                $resultat = $this->m_devis->liste($id,$pagelength, $pagestart, $filters);
        }

        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_devis->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Devis'
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

        $resultat = $this->m_devis->liste_chunk($id, 0, 1, NULL);    // limit=0 i.e. get all records
                                                                // start=1 will be ignored
                                                                // filters=NULL i.e. filtering happening on client side

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
    /******************************
     * Liste des devis en cours (appelé en AJAX par le composant Grid)
     ******************************/
    public function encours($commande) {
        if (! $this->input->is_ajax_request()) die('');
        if ( empty($commande) ) {
            log_message('DEBUG', 'In Devis/encours/ commande empty: '.$commande);
            return false;
        }
        $resultat = $this->m_devis->encours($commande);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Création d'un devis
     * support AJAX
     ******************************/
    public function nouveau($contact=0,$ajax=false) {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');
        $this->load->model('m_societes_vendeuses');
        $this->load->model('m_contacts');

        /**
         * @var M_taux_tva $m_taux_tva
         */
        $this->load->model('m_taux_tva');
        $m_taux_tva = $this->m_taux_tva;
        $taux = (date('Y-m-d') >= '2017-07-01') ? array(tva()) : $m_taux_tva->taux_historiques();

        // règles de validation
        $config = array(
            array('field' => 'dvi_societe_vendeuse', 'label' => "Enseigne", 'rules' => 'trim|required'),
            array('field' => 'dvi_client', 'label' => "Client ou prospect", 'rules' => 'trim|required'),
            array('field' => 'dvi_correspondant', 'label' => "Contact client", 'rules' => 'trim'),
            array('field' => 'dvi_notes', 'label' => "Remarques", 'rules' => 'trim'),
            array('field' => 'dvi_tva', 'label' => 'Taux de TVA (%)', 'rules' => 'required|in_list['.implode(',', $taux).']'),
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {

            // validation réussie
            $valeurs = array(
                'dvi_societe_vendeuse'     => $this->input->post('dvi_societe_vendeuse'),
                'dvi_client'               => $this->input->post('dvi_client'),
                'dvi_correspondant'        => $this->input->post('dvi_correspondant'),
                'dvi_notes'                => $this->input->post('dvi_notes'),
                'dvi_tva'                  => $this->input->post('dvi_tva'),
            );

            $id = $this->m_devis->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordadd',
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                        ),
                    ),
                    'url' => site_url('devis/lignes/'.$id),
                );

                $this->my_set_action_response($ajax, true, "Le devis a été initialisé", 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            redirect('devis/lignes/'.$id);
        }

        // validation en échec ou premier appel : affichage du formulaire
        $valeurs                           = new stdClass();
        $listes_valeurs                    = new stdClass();

        $valeurs->dvi_societe_vendeuse     = $this->input->post('dvi_societe_vendeuse');
        $valeurs->dvi_client               = $this->input->post('dvi_client');
        $valeurs->dvi_correspondant        = $this->input->post('dvi_correspondant');
        $valeurs->dvi_notes                = $this->input->post('dvi_notes');
        $valeurs->dvi_tva                  = (!empty($this->input->post('dvi_tva'))) ? $this->input->post('dvi_tva') : tva();

        $taux_pct = array();
        foreach ($taux as $tva) {
            $option = new stdClass();
            $option->tva = $tva;
            $option->tva_pct = ($tva * 100).'%';
            $taux_pct[] = $option;
        }
        $listes_valeurs->dvi_tva = $taux_pct;
        $listes_valeurs->dvi_societe_vendeuse = $this->m_societes_vendeuses->liste_option();

        if (!$valeurs->dvi_societe_vendeuse) {
            $scv_first = current($listes_valeurs->dvi_societe_vendeuse);
            $valeurs->dvi_societe_vendeuse = $scv_first->id;
        }
        $listes_valeurs->dvi_client = $this->m_contacts->liste_option();
        if (!$valeurs->dvi_client) {
            if ($contact > 0) {
                $valeurs->dvi_client = $contact;
            } else {
                $ctc_first = current($listes_valeurs->dvi_client);
                $valeurs->dvi_client = $ctc_first->id;
            }
        }
        $this->load->model('m_correspondants');
        $listes_valeurs->dvi_correspondant = $this->m_correspondants->liste_option($valeurs->dvi_client);
        if (!$valeurs->dvi_correspondant && count($listes_valeurs->dvi_correspondant) == 1) {
            $cor_first = current($listes_valeurs->dvi_correspondant);
            $valeurs->dvi_correspondant = $cor_first->id;
        }

        // descripteur
        $descripteur = array(
            'champs'  => array(
                'dvi_societe_vendeuse' => array("Enseigne",'select',array(null, 'id', 'value'),true),
                'dvi_client' => array("Client ou prospect",'select',array(null, 'id', 'value'),true),
                'dvi_correspondant' => array("Contact client",'select',array(null, 'id', 'value'),false),
                'dvi_notes' => array("Remarques",'textarea',null,false),
                'dvi_tva' => array("Taux de TVA (%)",'select',array(null,'tva','tva_pct'),true),
            ),
            'onglets' => array(
            ),
        );
        $scripts = array();
        if (!$ajax) {
            $scripts[] = $this->load->view('devis/nouveau-js', array(), true);
        }

        $barre_action = initialise_action_barre_action($this->barre_action["Nouveau"], $valeurs->dvi_client, 'correspondants/nouveau');

        $data = array(
            'title'          => "Nouvel avoir",
            'page'           => "templates/form",
            'menu'           => "Ventes|Nouvel avoir",
            'scripts'        => $scripts,
            'barre_action'   => $barre_action,
            'values'         => $valeurs,
            'action'         => "création",
            'multipart'      => false,
            'confirmation'   => 'Continuer',
            'controleur'     => 'devis',
            'methode'        => __FUNCTION__,
            'descripteur'    => $descripteur,
            'listes_valeurs' => $listes_valeurs,
        );
        $this->my_set_form_display_response($ajax, $data);
    }

    /******************************
     * Nouveau devis
     *
     * @deprecated
     ******************************/
    public function nouveau_deprecated($contact='') {
        $this->session->unset_userdata('devis');
        $this->load->helper(array('form','ctrl'));
        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_familles');
        $familles = $q->result();
        $id = 0;
        $tva = tva();
        $scripts = array();
        $scripts[] = $this->load->view('devis/mise_a_jour-js',array('id'=>$id,'tva'=>$tva,'familles' => $familles),true);
        foreach ($familles as $f) {
            $scripts[] = $this->load->view('_catalogues/'.$f->vfm_nom.'-js',array(),true);
        }
        $listes_valeurs = new stdClass();
        $q = $this->db->order_by('ctc_nom','ASC')
            ->get('t_contacts');
        $listes_valeurs->dvi_client = $q->result();
        $q = $this->db->order_by('cor_nom','ASC')
            ->get('t_correspondants');
        $listes_valeurs->dvi_correspondant = $q->result();
        $q = $this->db->order_by('scv_nom','ASC')
            ->get('t_societes_vendeuses');
        $listes_valeurs->dvi_societe_vendeuse = $q->result();
        $data = array(
            'title' => "Devis",
            'page' => "devis/nouveau",
            'menu' => "Ventes|Nouveau devis",
            'barre_action' => $this->barre_action['Edition'],
            'controleur' => 'devis',
            'methode' => __FUNCTION__,
            'scripts' => $scripts,
            'values' => array(
                'listes_valeurs' => $listes_valeurs,
                'id' => $id,
                'contact' => $contact,
                'familles' => $familles,
                'tva' => $tva
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Consultation de devis
     ******************************/
    public function detail2($id,$ajax=false) {
        $devis = $this->m_devis->detail($id);
        $this->load->model('m_societes_vendeuses');
        $devis->enseigne = $this->m_societes_vendeuses->detail($devis->dvi_societe_vendeuse);

        $scripts = array();
        $scripts[] = $this->load->view('devis/detail2-js',array('id'=>$id),true);

        $barre_action = $this->_masque_demasque_actions($this->barre_action['Element'],$devis);

        $data = array(
            'title' => "Détail d'un devis",
            'page' => "devis/detail2",
            'menu' => "Ventes|Détail devis",
            'barre_action' => $barre_action,
            'id' => $id,
            'scripts' => $scripts,
            'values' => $devis,
            'controleur' => 'devis',
            'methode' => __FUNCTION__,
        );
        $this->my_set_display_response($ajax,$data);
    }

    /******************************
     * Mise à jour de devis
     ******************************/
    public function lignes($id) {
        $this->session->unset_userdata('devis');
        $valeurs = $this->m_devis->detail($id);
        if (!$valeurs) {
            redirect("devis/");
        }
        if ($valeurs->dvi_etat == 4) {
            redirect("devis/detail2/$id");
        }

        $barre_action = $this->_masque_demasque_actions($this->barre_action['Edition'],$valeurs);

        $this->load->helper(array('form','ctrl'));
        $this->session->set_userdata('_url_retour',current_url());
        $q = $this->db->get('v_familles');
        $familles = $q->result();
        $tva = tva();
        $scripts = array();
        $scripts[] = $this->load->view('devis/mise_a_jour-js',array('id'=>$id,'tva'=>$tva,'familles' => $familles,'values'=>$valeurs),true);
        foreach ($familles as $f) {
            $scripts[] = $this->load->view('_catalogues/'.$f->vfm_nom.'-js',array(),true);
        }
        $listes_valeurs = new stdClass();
        $q = $this->db->order_by('ctc_nom','ASC')
            //->where('ctc_id', $valeurs->dvi_client)
            ->where('ctc_inactif IS NULL')
            ->get('t_contacts');
        $listes_valeurs->dvi_client = $q->result();
        $q = $this->db->order_by('cor_nom','ASC')
            ->where('cor_contact', $valeurs->dvi_client)
            ->get('t_correspondants');
        $listes_valeurs->dvi_correspondant = $q->result();
        $q = $this->db->order_by('scv_nom','ASC')
            ->where('scv_id', $valeurs->dvi_societe_vendeuse)
            ->get('t_societes_vendeuses');
        $listes_valeurs->dvi_societe_vendeuse = $q->result();

        $this->load->model('m_societes_vendeuses');
        $valeurs->enseigne = $this->m_societes_vendeuses->detail($valeurs->dvi_societe_vendeuse);

        $data = array(
            'title' => "Devis",
            'page' => "devis/mise_a_jour",
            'menu' => "Ventes|Mise à jour devis",
            'barre_action' => $barre_action,
            'controleur' => 'devis',
            'methode' => __FUNCTION__,
            'scripts' => $scripts,
            'values' => array(
                'listes_valeurs' => $listes_valeurs,
                'id' => $id,
                'familles' => $familles,
                'tva' => $tva,
                'values' => $valeurs
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }


    /******************************
     * Mise à jour d'un devis (info de base)
     * support AJAX
     ******************************/
    public function modification($id=0, $ajax=false) {
        $devis = $this->m_devis->detail($id);
        if ($devis->dvi_etat != 1) {
            $this->my_set_action_response($ajax,false, 'Opération non autorisée');
            if ($ajax) {
                return;
            }
            redirect("devis/detail2/$id");
        }
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        $db = $this->db;
        $input = $this->input;

        // règles de validation
        $config = array(
            array('field'=>'dvi_date','label'=>"Date",'rules'=>'required'),
            array('field'=>'dvi_notes','label'=>"Remarques",'rules'=>'trim'),
            array(
                'field' => 'dvi_reference',
                'label' => "Numéro de pièce",
                'rules' => array(
                    'trim',
                    'max_length[30]',
                    array(
                        'dvi_reference_unique',
                        function ($str) use ($db, $devis) {
                            return (0 == $db->where('dvi_reference', $str)
                                    ->where('dvi_inactif IS NULL')
                                    ->where_not_in('dvi_id', array($devis->dvi_id))
                                    ->where('dvi_societe_vendeuse', $devis->dvi_societe_vendeuse)
                                    ->count_all_results('t_devis'));
                        }
                    ),
                )
            ),
            array(
                'field' => 'dvi_numero',
                'label' => "Numéro série",
                'rules' => array(
                    'trim',
                    'numeric',
                    'greater_than[0]',
                    array(
                        'dvi_numero_unique',
                        function ($str) use ($devis, $db) {
                            return (0 == $db->where('dvi_numero', $str)
                                    ->where('dvi_inactif IS NULL')
                                    ->where_not_in('dvi_id', array($devis->dvi_id))
                                    ->where('dvi_societe_vendeuse', $devis->dvi_societe_vendeuse)
                                    ->count_all_results('t_devis'));
                        }
                    ),
                )
            ),
            array(
                'field' => 'dvi_client',
                'label' => 'Client',
                'rules' => array(
                    'required',
                    array(
                        'dvi_client_exists',
                        function ($dvi_client) use ($db) {
                            return (1 == $db->where('ctc_id', $dvi_client)
                                    ->where('ctc_inactif IS NULL')
                                    ->count_all_results('t_contacts'));
                        }
                    ),
                ),
            ),
            array(
                'field' => 'dvi_correspondant',
                'label' => 'Correspondant',
                'rules' => array(
                    'numeric',
                    array(
                        'dvi_correspondant_exists',
                        function ($dvi_correspondant) use ($db, $input, $devis) {
                            if ($devis->dvi_client != $input->post('dvi_client')) {
                                // The client was changed, reset the correspondant
                                return 0;
                            }
                            if (empty($dvi_correspondant)) {
                                return true;
                            }
                            return (1 == $db->where('cor_id', $dvi_correspondant)
                                    ->where('cor_inactif IS NULL')
                                    ->where('cor_contact', $devis->dvi_client)
                                    ->count_all_results('t_correspondants'));
                        }
                    ),
                ),
            ),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        $this->form_validation->set_message('dvi_reference_unique', 'Ce numéro de pièce est déjà utilisé par un autr devis');
        $this->form_validation->set_message('dvi_numero_unique', 'Ce numéro série est déjà utilisé par un autre devis');
        $this->form_validation->set_message('dvi_client_exists', 'Choisissez un client parmis ceux proposés');
        $this->form_validation->set_message('dvi_correspondant_exists', 'Choisissez un correspondant parmis ceux proposés');

        // validation des fichiers chargés
        //$validation = true;
        $this->form_validation->set_rules($config);

        //if ($this->form_validation->run() AND $validation) {
        if ($this->form_validation->run()) {

            // validation réussie
            $valeurs = array(
                'dvi_date' => formatte_date_to_bd($this->input->post('dvi_date')),
                'dvi_notes' => $this->input->post('dvi_notes'),
                'dvi_client' => $this->input->post('dvi_client'),
                'dvi_correspondant' => $this->input->post('dvi_correspondant'),
            );
            if (strlen($this->input->post('dvi_reference')) > 0) {
                $valeurs['dvi_reference'] = $this->input->post('dvi_reference');
            }
            if (strlen($this->input->post('dvi_numero')) > 0) {
                $dvi_numero = intval($this->input->post('dvi_numero'), 10);
                $valeurs['dvi_numero'] = $dvi_numero;
            } else {
                $dvi_numero = intval($devis->dvi_numero, 10);
            }

            $resultat = $this->m_devis->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = 'devis/detail2/'.$id;
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message = "Le devis a été modifié";
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
                    if ($valeurs['dvi_client'] != $devis->dvi_client) {
                        // The "client" was changed;
                        $new_client = $this->m_contacts->detail($valeurs['dvi_client']);
                        $new_correspondants = $this->db->select('cor_id, cor_nom')
                            ->where('cor_contact', $new_client->ctc_id)
                            ->where('cor_inactif IS NULL')
                            ->get('t_correspondants')
                            ->result();

                        $freshData = array(
                            'ctc_nom' => html_escape($new_client->ctc_nom),
                            'ctc_id' => $new_client->ctc_id,
                            'ctc_adresse' => html_escape($new_client->ctc_adresse),
                            'ctc_cp' => html_escape($new_client->ctc_cp),
                            'ctc_ville' => html_escape($new_client->ctc_ville),
                            'ctc_commercial' => $new_client->ctc_commercial_charge,
                            'emp_nom' => html_escape($new_client->vcv_civilite.' '.$new_client->emp_nom.' '.$new_client->emp_prenom),
                            'dvi_correspondant_options' => $new_correspondants,
                        );
                        $ajaxData['freshData'] = $freshData;
                    }
                    if (!empty($dvi_numero)) {
                        // On mets à jour le compteur de devis si le numéro de devis a changé
                        if ($dvi_numero != intval($devis->dvi_numero, 10)) {
                            $max_dvi_numero = $this->m_devis->plus_grand_numero($devis->dvi_societe_vendeuse);
                            $this->db->where('scv_id', $devis->dvi_societe_vendeuse)
                                ->update(
                                    't_societes_vendeuses',
                                    array(
                                        'scv_no_devis' => $max_dvi_numero,
                                    )
                                );
                            $message .= "\n<br>Le compteur de devis à été mis à ".$max_dvi_numero." pour ".$devis->scv_nom;
                            $ajaxData['event'][] = array(
                                'controleur' => 'societes_vendeuses',
                                'type' => 'recordchange',
                                'id' => $devis->dvi_societe_vendeuse,
                                'timeStamp' => round(microtime(true) * 1000),
                            );
                        }
                    }
                }
                $this->my_set_action_response($ajax,true,$message, 'info', $ajaxData);
                $redirection = "devis";
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {
            // validation en échec ou premier appel : affichage du formulaire

            $this->load->model('m_societes_vendeuses');
            $devis->enseigne = $this->m_societes_vendeuses->detail($devis->dvi_societe_vendeuse);

            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('dvi_notes');
            if (isset($valeur)) {
                $devis->dvi_notes = $valeur;
            }
            $valeur = $this->input->post('dvi_reference');
            if (isset($valeur)) {
                $devis->dvi_reference = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => array(
                    'dvi_reference' => array("Référence",'text','dvi_reference',true),
                    'dvi_notes' => array("Remarques",'textarea','dvi_notes',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un devis",
                'page' => "templates/form",
                'menu' => "Ventes|Mise à jour d'un devis",
                'barre_action' => $this->barre_action['Element'],
                'id' => $id,
                'values' => $devis,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'devis',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Manipulation des articles du devis (appelé en AJAX par le composant Grid)
     ******************************/
    public function manipulation($id,$commande) {
        if (! $this->input->is_ajax_request()) die('');
        if ($commande == 'add') {
            $data = $_POST;
            unset($data['dvi_id_comptable']);
            unset($data['dvi_id']);
            if ($id == 0) {
                $id = $this->m_devis->nouveau($data);
                $this->session->set_userdata('devis', $id);
            }
            else {
                $this->m_devis->maj($data,$id);
            }
            $resultat = $id;
        }
        else {
            if ($id == 0) {
                $id = $this->session->devis;
            }
            $resultat = $this->m_devis->constitution($id, $commande);
            if ($resultat === false) {
                log_message('error', '===== Erreur dans M_devis::constitution()');
                die();
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Lecture des catalogues
     ******************************/
    public function lecture_catalogue($code) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_devis->lecture_catalogue($code);
        if ($resultat == false) {
            die();
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
     * Renvoi des devis (appelé en AJAX par le composant Grid)
     ******************************/
    public function renvoi() {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_devis->renvoi($this->input->post('type'),$this->input->post('devis'));
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
                    'tac_titre' => 'Rappel relance devis',
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
                    'tac_titre' => 'Rappel relance devis',
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
     * Apercu
     ******************************/
    public function apercu($id) {
        $data = $this->m_devis->detail($id);
        if ($data->dvi_etat == 1) {
            $filename = $this->m_devis->generer_pdf($id);
            if ($filename !== false) {
                $content = file_get_contents($filename);
                header("Content-Disposition: inline; filename=$filename");
                header("Content-type: application/pdf");
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
            }
            else {
                $content = "Un problème technique a empêché la genération du devis en pdf.";
            }
            echo $content;
        }
        else {
            echo '';
        }
    }

    /******************************
     * Aperçu du devis en HTML
     ******************************/
    public function apercu_html($id) {
        $contenu = $this->m_devis->generer_pdf($id,false);
        $data = array(
            'title' => "Devis",
            'values' => $contenu
        );
        $layout="layouts/vide";
        $this->load->view($layout,$data);
    }

    /******************************
     * Génération de PDF
     * support AJAX
     ******************************/
    public function genere_pdf($id,$ajax=false) {
        $redirection = $this->session->userdata('_url_retour');
        try {
            $chemin = $this->m_devis->generer_pdf($id);
            if ($chemin === false) {
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
                $this->my_set_action_response($ajax,true,"Le devis PDF a été généré", 'info', $ajaxData);
                $redirection = construit_url_fichier('', $chemin);
            }
        } catch (MY_Exceptions_NoSuchRecord $e) {
            $this->my_set_action_response($ajax,false,"Le devis n'a pas été trouvé");
            $redirection = 'devis';

        } catch (MY_Exceptions_NoSuchTemplate $e) {
            $this->my_set_action_response($ajax,false,"Pas de modèle existant pour générer le devis PDF");
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Merge pdf
     * Author: Kanav
     ******************************/
    public function merge_pdfs() {
        $this->load->library('pdfconcat');
        $path = $this->input->post('path');
        $pdfs = array_map(function($v) use ($path){
            return $path.$v;
        }, $this->input->post('pdfs'));

        $this->pdfconcat->setFiles($pdfs);
        $this->pdfconcat->concat();

        $randomName = $this->rand_str().".pdf";
        $this->pdfconcat->Output($path.$randomName, 'F');
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

    /******************************
     * Mailing counter
     * Author: Irawan Wijanarko
     ******************************/
    public function emailing_comptage(){
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library('Famille_E',NULL,'famille');
        return $this->famille->comptage($this->input->post());
    }


    public function catalogues_distribution_json($id=0)
    {
        $code  = "D";
        $pagelength = 100;
        $pagestart  = 0+$this->input->post('start' );
        if ( $pagestart < 2)
            $pagelength = 50;

        $order   = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');
        if (empty($filters)) {
            $filters = null;
        }

        $quantites = $this->input->post('quantites');

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_devis->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'art_id' => 't_articles',
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

                $resultat = $this->m_devis->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_devis->catalogues_distribution_liste($id, $code, $quantites, $pagelength, $pagestart, $filters);
            }
        }

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($resultat));
    }

    public function generate_distribution_articles()
    {
        $this->load->model('m_catalogues');
      	//insert new version catalogues distribution
        $version = 1;
        $query_catalogue = $this->db->select('MAX(cat_version) as version')->get_where('t_catalogues',array('cat_famille'=>3));

        if($query_catalogue->num_rows() > 0) {
            $row_catalogue = $query_catalogue->row();
            $last_version = $row_catalogue->version;
            $version = $last_version + 1;
        }

        $valeurs = array(
                'cat_famille' => 3,
                'cat_version' => $version,
                'cat_date' => date("Y-m-d")
        );
        $catalogue_id = $this->m_catalogues->nouveau($valeurs);

		$prix_debase = array();
		$data_prix_debase = $this->m_devis->get_articles_distribution_base_price();
		$i = 0;
		foreach($data_prix_debase as $row){
			$prix_debase[$row->adb_secteur]['baseprice'] = $row->adb_baseprice;
			$prix_debase[$row->adb_secteur]['type_secteur'] = $row->vts_type;
			$i++;
		}

        $options = $this->m_devis->get_articles_distribution_option();
        $resultat = $this->m_devis->get_articles_distribution_price();
        $option_data = $resultat['option_data'];
        $price_inc = $resultat['price_inc'];

        $habitat = $option_data[$options['HABITAT']];
        $document = $option_data[$options['DOCUMENT']];
        $type_distribution = $option_data[$options['DISTRIBUTION']];
        $delai = $option_data[$options['DELAI']];
        $controle = $option_data[$options['CONTROLE']];

		$total_combination = count($prix_debase)*
								count($habitat)*
								count($document)*
								count($type_distribution)*
								count($delai)*
								count($controle);

        $z = 1;
		$data_insert = array();
        foreach ($prix_debase as $secteur_id => $secteur) {
            $i = 1;
            foreach ($habitat as $habitat_row) {
                foreach ($document as $document_row) {
                    foreach ($type_distribution as $type_distribution_row) {
                        foreach ($delai as $delai_row) {
                            foreach ($controle as $controle_row) {
                                $price = 0;

                                if ($i < 10) {
                                    $code = '00' . $i;
                                } elseif ($i < 100) {
                                    $code = '0' . $i;
                                } else {
                                    $code = $i;
                                }

                                //prix final calculation
                                //will move this into database
                                $price += $price_inc[$options['HABITAT']][$habitat_row];
                                $price += $price_inc[$options['DOCUMENT']][$document_row];
                                $price += $price_inc[$options['DISTRIBUTION']][$type_distribution_row];
                                $price += $price_inc[$options['DELAI']][$delai_row];
                                $price += $price_inc[$options['CONTROLE']][$controle_row];
                                $final_prix = (1 + ($price / 100)) * $secteur['baseprice'];

                                $code = 'DS' . $secteur_id . '-' . $code;

								$data = array();
								$data[0] = $habitat_row;
								$data[1] = $document_row;
								$data[2] = $type_distribution_row;
								$data[3] = $delai_row;
								$data[4] = $controle_row;

								$article = array();
								$article['art_code'] = $code;
								$article['art_description'] = 'Prix unitaire BAL '.$habitat_row.' '.
																				$secteur['type_secteur'].
																				', Document '.$document_row.
																				', Type Distribution '.$type_distribution_row.
																				', Delai '.$delai_row.
																				', Controle '.$controle_row;
								$article['art_libelle'] = $article['art_description'];
								$article['art_data'] = serialize($data);
								$article['art_prix'] = $final_prix;
								$article['art_selection'] = 1;
								$article['art_catalogue'] = $catalogue_id;

								//slow performance
								//need to group insert
								//$this->m_articles->nouveau($article);



								$data_insert[]	=	"(	'".$article['art_code']."',
														'".$article['art_description']."',
														'".$article['art_libelle']."',
														'".$article['art_data']."',
														'".$article['art_prix']."',
														'".$article['art_selection']."',
														'".$article['art_catalogue']."'
													)";

								if($i % 100 == 0 || $z == $total_combination)
								{
									$data_insert = implode(',', $data_insert);
									$this->db->query("INSERT INTO t_articles ( 	art_code, 
																			art_description, 
																			art_libelle, 
																			art_data, 
																			art_prix, 
																			art_selection,
																			art_catalogue
																		) VALUES ".$data_insert);
									$data_insert = array();
								}

                                $i++;
								$z++;
                            }
                        }
                    }
                }
            }
        }
    }

}
// EOF
