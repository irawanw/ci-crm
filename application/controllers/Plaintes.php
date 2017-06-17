<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Plaintes extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('plaintes/nouveau','plus',true,'plaintes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('*plaintes/detail','eye-open',false,'plaintes_detail',null,array('view')),
                    "Modifier" => array('plaintes/modification','pencil',false,'plaintes_modification',null,array('form')),
                    "Supprimer" => array('plaintes/suppression','trash',true,'plaintes_supprimer',"Veuillez confirmer la suppression de la plainte",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Nouveau" => array('plaintes/nouveau','plus',true,'plaintes_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('plaintes/detail','eye-open',true,'plaintes_detail',null,array('view','default-view')),
                    "Modifier" => array('plaintes/modification','pencil',true,'plaintes_modification',null,array('form')),
                    "Supprimer" => array('plaintes/suppression','trash',true,'plaintes_supprimer',"Veuillez confirmer la suppression de la plainte",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_plaintes');
    }

     /******************************
     * List of plaintes Data
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
    * Liste des plaintes
    ******************************/
    public function liste($id=0,$mode=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'plaintes/index',
            'detail' => array('plaintes/detail','pla_id','pla_date'),
            'champs' => $this->m_plaintes->get_champs('read'),
            'filterable_columns' => $this->m_plaintes->liste_filterable_columns()
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'plaintes/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'plaintes/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'plaintes/all';
                break;
        }

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'plaintes',
                'methode' => 'index',
                'mass_action_toolbar' => true,
                'view_toolbar' => true
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('plaintes',$this->session->id);
        $data = array(
            'title' => "Liste des plaintes",
            'page' => "templates/datatables",
            'menu' => "Production|Plaintes",
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
    * Liste des plaintes (datasource)
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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_plaintes->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'pla_date' => 't_plaintes',
                'sec_nom' => 't_secteurs'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_plaintes->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_plaintes->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
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
    * Nouvelle plainte
    ******************************/
    public function nouveau($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'pla_date','label'=>"Date de la plainte",'rules'=>'trim|required'),
            array('field'=>'pla_description','label'=>"Teneur de la plainte",'rules'=>'trim|required'),
            array('field'=>'pla_secteur','label'=>"Secteur concerné",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'pla_date' => formatte_date_to_bd($this->input->post('pla_date')),
                'pla_description' => $this->input->post('pla_description'),
                'pla_secteur' => $this->input->post('pla_secteur')
            );
            $id = $this->m_plaintes->nouveau($valeurs);
            if ($id === false) {
                $this->my_set_display_response($ajax,false);
            }
            else {
                $this->my_set_action_response($ajax,true,"La plainte a été créée");
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
            $valeurs->pla_date = $this->input->post('pla_date');
            $valeurs->pla_description = $this->input->post('pla_description');
            $valeurs->pla_secteur = $this->input->post('pla_secteur');
            $this->db->order_by('sec_nom','ASC');
            $q = $this->db->get('t_secteurs');
            $listes_valeurs->pla_secteur = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#pla_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => $this->m_plaintes->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Nouvelle plainte",
                'page' => "templates/form",
                'menu' => "Production|Nouvelle plainte",
                'scripts' => $scripts,
                'values' => $valeurs,
                'action' => "création",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'plaintes',
                'methode' => 'nouveau',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Détail d'une plainte
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_plaintes->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'pla_date' => array("Date de la plainte",'DATE','date','pla_date'),
                   'pla_description' => array("Teneur de la plainte",'VARCHAR 400','textarea','pla_description'),
                   'pla_secteur' => array("Secteur concerné",'REF','ref',array('secteurs','pla_secteur','sec_nom'))
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'une plainte",
                'page' => "templates/detail",
                'menu' => "Production|Plainte",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'plaintes',
                'methode' => 'detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'une plainte
    ******************************/
    public function modification($id=0,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field'=>'pla_date','label'=>"Date de la plainte",'rules'=>'trim|required'),
            array('field'=>'pla_description','label'=>"Teneur de la plainte",'rules'=>'trim|required'),
            array('field'=>'pla_secteur','label'=>"Secteur concerné",'rules'=>'required'),
            array('field'=>'__form','label'=>'Témoin','rules'=>'required')
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie
            $valeurs = array(
                'pla_date' => formatte_date_to_bd($this->input->post('pla_date')),
                'pla_description' => $this->input->post('pla_description'),
                'pla_secteur' => $this->input->post('pla_secteur')
            );
            $resultat = $this->m_plaintes->maj($valeurs,$id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax,false);
            }
            else {
                if ($resultat == 0) {
                    $message = "Pas de modification demandée";
                }
                else {
                    $message = "La plainte a été modifiée";
                }
                $this->my_set_action_response($ajax,true,$message);
            }
            if ($ajax) {
                return;
            }
            redirect('plaintes/detail/'.$id);
        }
        else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs = $this->m_plaintes->detail($id);
            $listes_valeurs = new stdClass();
            $valeur = $this->input->post('pla_date');
            if (isset($valeur)) {
                $valeurs->pla_date = $valeur;
            }
            $valeur = $this->input->post('pla_description');
            if (isset($valeur)) {
                $valeurs->pla_description = $valeur;
            }
            $valeur = $this->input->post('pla_secteur');
            if (isset($valeur)) {
                $valeurs->pla_secteur = $valeur;
            }
            $this->db->order_by('sec_nom','ASC');
            $q = $this->db->get('t_secteurs');
            $listes_valeurs->pla_secteur = $q->result();
            $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#pla_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs' => $this->m_plaintes->get_champs('write'),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Mise à jour d'une plainte",
                'page' => "templates/form",
                'menu' => "Production|Mise à jour de plainte",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'plaintes',
                'methode' => 'modification',
                'descripteur' => $descripteur,
                'listes_valeurs' => $listes_valeurs
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'une plainte
    ******************************/
    public function suppression($id,$ajax=false) {
        $resultat = $this->m_plaintes->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"La plainte a été supprimée");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_plaintes->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_plaintes->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_plaintes->unremove($id);
        }
    }

}
// EOF
