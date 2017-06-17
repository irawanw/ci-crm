<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_objectifs m_objectifs
*/
class Objectifs extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('objectifs/nouveau','plus',true,'objectifs_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*objectifs/detail','eye-open',false,'objectifs_detail',null,array('view')),
                    "Modifier" => array('objectifs/modification','pencil',false,'objectifs_modification',null,array('form')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('objectifs/detail','eye-open',true,'objectifs_detail',null,array('view')),
                    "Modifier" => array('objectifs/modification','pencil',true,'objectifs_modification',null,array('form')),
                    "Supprimer" => array('objectifs/suppression','trash',true,'objectifs_supprimer',"Veuillez confirmer la suppression de l'objectif",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_objectifs');
    }

    /******************************
    * Liste des objectifs d'un employé
    ******************************/
    public function objectifs_employe($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'objectifs/objectifs_employe',
            'detail' => array('objectifs/detail','obj_id','obj_date'),
            'champs' => array(
                array('obj_id','id',"Identifiant"),
                array('vcr_critere','ref',"Critère",'v_criteres'),
                array('emp_nom','ref',"Employé",'employes','obj_employe','emp_nom'),
                array('obj_date','date',"Date de l'objectif (mois)"),
                array('obj_prevu','number',"Valeur prévue"),
                array('obj_realise','number',"Valeur réalisée"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'filterable_columns' => $this->m_objectifs->liste_par_employe_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'objectifs',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('objectifs',$this->session->id);
        $data = array(
            'title' => "Liste des objectifs d'un employé",
            'page' => "templates/datatables",
            'menu' => "Personnel|Objectifs",
            'scripts' => $scripts,
            'barre_action' => $this->barre_action["Liste"],
            'controleur' => 'objectifs',
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
    * Liste des objectifs d'un employé (datasource)
    ******************************/
    public function objectifs_employe_json($id=0) {
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
            $resultat = $this->m_objectifs->liste_par_employe($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'obj_date' => 't_objectifs',
                'emp_nom' => 't_employes'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_objectifs->liste_par_employe($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_objectifs->liste_par_employe($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Nouvel objectif
    * support AJAX
    ******************************/
    public function nouveau($pere=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'obj_critere','label'=>"Critère",'rules'=>'required'),
            array('field'=>'obj_date','label'=>"Date de l'objectif (mois)",'rules'=>'trim|required'),
            array('field'=>'obj_prevu','label'=>"Valeur prévue",'rules'=>'trim|required|is_natural'),
            array('field'=>'obj_realise','label'=>"Valeur réalisée",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'obj_critere' => $this->input->post('obj_critere'),
                'obj_date' => formatte_date_to_bd($this->input->post('obj_date')),
                'obj_prevu' => $this->input->post('obj_prevu'),
                'obj_realise' => $this->input->post('obj_realise'),
                'obj_employe' => $pere
            );
            $id = $this->m_objectifs->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"L'objectif a été créé");
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
            $valeurs->obj_critere = $this->input->post('obj_critere');
            $valeurs->obj_date = $this->input->post('obj_date');
            $valeurs->obj_prevu = $this->input->post('obj_prevu');
            $valeurs->obj_realise = $this->input->post('obj_realise');
            $this->db->order_by('vcr_critere','ASC');
            $q = $this->db->get('v_criteres');
            $listes_valeurs->obj_critere = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#obj_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'obj_critere' => array("Critère",'select',array('obj_critere','vcr_id','vcr_critere'),true),
                   'obj_date' => array("Date de l'objectif (mois)",'date','obj_date',true),
                   'obj_prevu' => array("Valeur prévue",'number','obj_prevu',true),
                   'obj_realise' => array("Valeur réalisée",'number','obj_realise',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvel objectif",
                'page' => "templates/form",
                'menu' => "Personnel|Nouvel objectif",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'objectifs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'un objectif
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
            $valeurs = $this->m_objectifs->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'objectifs/modification','primary'),
            //    array("Supprimer",'objectifs/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'obj_critere' => array("Critère",'REF','text','vcr_critere'),
                   'obj_employe' => array("Employé",'REF','ref',array('employes','obj_employe','emp_nom')),
                   'obj_date' => array("Date de l'objectif (mois)",'DATE','date','obj_date'),
                   'obj_prevu' => array("Valeur prévue",'INT 9','number','obj_prevu'),
                   'obj_realise' => array("Valeur réalisée",'INT 9','number','obj_realise')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un objectif",
                'page' => "templates/detail",
                'menu' => "Personnel|Objectif",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'objectifs',
                'methode' => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un objectif
    * support AJAX
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'obj_critere','label'=>"Critère",'rules'=>'required'),
            array('field'=>'obj_date','label'=>"Date de l'objectif (mois)",'rules'=>'trim|required'),
            array('field'=>'obj_prevu','label'=>"Valeur prévue",'rules'=>'trim|required|is_natural'),
            array('field'=>'obj_realise','label'=>"Valeur réalisée",'rules'=>'trim|is_natural'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'obj_critere' => $this->input->post('obj_critere'),
                'obj_date' => formatte_date_to_bd($this->input->post('obj_date')),
                'obj_prevu' => $this->input->post('obj_prevu'),
                'obj_realise' => $this->input->post('obj_realise')
            );
            $resultat = $this->m_objectifs->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
                $redirection = 'objectifs/detail/'.$id;
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "L'objectif a été modifié";
                }
                $this->my_set_action_response($ajax,true,$message);
                $redirection = 'objectifs/detail/'.$id;
            }
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_objectifs->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('obj_critere');
            if (isset($valeur)) {
                $valeurs->obj_critere = $valeur;
            }
            $valeur = $this->input->post('obj_date');
            if (isset($valeur)) {
                $valeurs->obj_date = $valeur;
            }
            $valeur = $this->input->post('obj_prevu');
            if (isset($valeur)) {
                $valeurs->obj_prevu = $valeur;
            }
            $valeur = $this->input->post('obj_realise');
            if (isset($valeur)) {
                $valeurs->obj_realise = $valeur;
            }
            $this->db->order_by('vcr_critere','ASC');
            $q = $this->db->get('v_criteres');
            $listes_valeurs->obj_critere = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#obj_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'obj_critere' => array("Critère",'select',array('obj_critere','vcr_id','vcr_critere'),true),
                   'obj_date' => array("Date de l'objectif (mois)",'date','obj_date',true),
                   'obj_prevu' => array("Valeur prévue",'number','obj_prevu',true),
                   'obj_realise' => array("Valeur réalisée",'number','obj_realise',false)
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'un objectif",
                'page' => "templates/form",
                'menu' => "Personnel|Mise à jour d'objectif",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'objectifs',
                'methode' => __FUNCTION__,
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un objectif
    * support AJAX
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_objectifs->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"L'objectif a été supprimé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

}

// EOF