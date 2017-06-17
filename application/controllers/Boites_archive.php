<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
 *
 * @property M_boites_archive m_boites_archive
*/
class Boites_archive extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('boites_archive/nouveau','plus',true,'boites_archive_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*boites_archive/detail','eye-open',false,'boites_archive_detail',null,array('view')),
                    "Modifier" => array('boites_archive/modification','pencil',false,'boites_archive_modification',null,array('form')),
                    "Supprimer" => array('boites_archive/remove','trash',false,'boites_archive_remove',"Veuillez confirmer la suppression de la boîte archive",array('confirm-modify')),
            ),
            array(
					"Export XLS" => array('#', 'list-alt', true, 'export_xls'),
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('boites_archive/detail','eye-open',true,'boites_archive_detail',null,array('view','default-view')),
                    "Modifier" => array('boites_archive/modification','pencil',true,'boites_archive_modification',null,array('form')),
                    "Supprimer" => array('boites_archive/remove','trash',false,'boites_archive_remove',"Veuillez confirmer la suppression de la boîte archive",array('confirm-modify')),
            ),
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_boites_archive');
    }

    /******************************
    * Liste des boîtes archive
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'boites_archive/index',
            'detail' => array('boites_archive/detail','bar_id','bar_code'),
            'champs' => $this->m_boites_archive->get_champs('read'),
            'filterable_columns' => $this->m_boites_archive->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
		/*	
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'boites_archive',
                'methode' => __FUNCTION__,
            ),true);
		*/
		$scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'boites_archive',
                'methode'               => __FUNCTION__,
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,
                'external_toolbar_data' => array(
				'controleur' => 'boites_archive',
                ),
            ), true);
		
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('boites_archive',$this->session->id);
        $data = array(
            'title' => "Liste des boîtes archive",
            'page' => "templates/datatables",
            'menu' => "GED|Boites archive",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'boites_archive',
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
    * Liste des boîtes archive (datasource)
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
            $resultat = $this->m_boites_archive->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'bar_code' => 't_boites_archive'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_boites_archive->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_boites_archive->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        
        if($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_boites_archive->get_champs('read');
            $params = array(
                'records' => $resultat['data'], 
                'columns' => $champs,
                'filename' => 'Boites_archives'
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
    }

    /******************************
    * Nouvelle boîte archive
    * support AJAX
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'bar_code','label'=>"Code",'rules'=>'trim|required'),
            array('field'=>'bar_commentaire','label'=>"Commentaire",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'bar_code' => $this->input->post('bar_code'),
                'bar_commentaire' => $this->input->post('bar_commentaire')
            );
            $resultat = $this->m_boites_archive->nouveau($valeurs);
			if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
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
                $this->my_set_action_response($ajax, true, "Le boîte a été créé",'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = new stdClass();
            $listes_valeurs = new stdClass();
            $valeurs->bar_code = $this->input->post('bar_code');
            $valeurs->bar_commentaire = $this->input->post('bar_commentaire');

            // descripteur
            $descripteur = array(
                'champs' => $this->m_boites_archive->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle boîte archive",
                'page' => "templates/form",
                'menu' => "GED|Nouvelle boîte archive",
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'boites_archive',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action  array             Barre d'action à modifier
     * @param $boite_archive M_boites_archive  Les infos de la boîte archive
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $boite_archive) {
        $etats = array(
            'boites_archive/suppression'       => $boite_archive->bar_nb_docs == 0,
        );

        return modifie_etats_barre_action($barre_action,$etats);
    }

    /******************************
    * Détail d'une boîte archive
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
            $valeurs = $this->m_boites_archive->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'boites_archive/modification','primary'),
            //    array("Supprimer",'boites_archive/suppression','danger',($valeurs->bar_nb_docs == 0))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'bar_code' => array("Code",'VARCHAR 10','text','bar_code'),
                   'bar_nb_docs' => array("Nombre de documents",'SQL','text','bar_nb_docs'),
                   'bar_nb_factures' => array("Nombre de factures",'SQL','text','bar_nb_factures'),
                   'bar_commentaire' => array("Commentaire",'VARCHAR 100','textarea','bar_commentaire')
                ),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Détail d'une boîte archive",
                'page' => "templates/detail",
                'menu' => "GED|Boîte archive",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'boites_archive',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une boîte archive
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'bar_code','label'=>"Code",'rules'=>'trim|required'),
            array('field'=>'bar_commentaire','label'=>"Commentaire",'rules'=>'trim'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'bar_code' => $this->input->post('bar_code'),
                'bar_commentaire' => $this->input->post('bar_commentaire')
            );
            $resultat = $this->m_boites_archive->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
				 if ($resultat == 0) {
                    $message = "Pas de modification demandée";
					$ajaxData = null;
                }
                else {
                    $message = "Le boîte archive a été modifié";
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
            $redirection = 'boites_archive/detail/'.$id;
			if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_boites_archive->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('bar_code');
            if (isset($valeur)) {
                $valeurs->bar_code = $valeur;
            }
            $valeur = $this->input->post('bar_commentaire');
            if (isset($valeur)) {
                $valeurs->bar_commentaire = $valeur;
            }

            // descripteur
            $descripteur = array(
                'champs' => $this->m_boites_archive->get_champs('write'),
                'onglets' => array(
                )
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Element"],$valeurs);

            $data = array(
                'title' => "Mise à jour d'une boîte archive",
                'page' => "templates/form",
                'menu' => "GED|Mise à jour de boîte archive",
                'barre_action' => $barre_action,
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'boites_archive',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'une boîte archive
    * support AJAX
    ******************************/
    public function remove($id,$ajax=false) {
		$redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }
        $data = $this->m_boites_archive->detail($id);
        if (! ($data->bar_nb_docs == 0)) {
            $this->my_set_action_response($ajax,false,"Opération non autorisée");
        }
        else {
            $resultat = $this->m_boites_archive->remove($id);
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
            $this->my_set_action_response($ajax, true, "Le boîte archive a été supprimé", 'info',$ajaxData);
			}
		}
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

}

// EOF