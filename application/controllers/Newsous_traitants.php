<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Newsous_traitants extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Liste"   => array(
            array(
                "Nouveau" => array('newsous_traitants/create', 'plus', true, 'sous_traitants_nouveau', null, array('form')),
            ),
            array(
                "Consulter" => array('newsous_traitants/detail', 'eye-open', false, 'sous_traitants_detail', null, array('view')),
                "Dupliquer" => array('newsous_traitants/dupliquer', 'duplicate', false, 'newsous_traitants_dupliquer', "Veuillez confirmer la duplique du sous traitants", array('confirm-modify' => array('newsous_traitants/index'))),
                "Modifier"  => array('newsous_traitants/modification', 'pencil', false, 'sous_traitants_modification', null, array('form')),
                "Supprimer" => array('newsous_traitants/suppression', 'trash', false, 'sous_traitants_supprimer', 'Confirmez la suppression de la sous_traitants', array('confirm-delete')),
            ),

        ),
        "Element" => array(
            array(
                "Consulter" => array('newsous_traitants/detail', 'eye-open', true, 'sous_traitants_detail', null, array('view')),
                "Dupliquer" => array('newsous_traitants/dupliquer', 'duplicate', true, 'newsous_traitants_dupliquer', "Veuillez confirmer la duplique du sous traitants", array('confirm-modify' => array('newsous_traitants/index'))),
                "Modifier"  => array('newsous_traitants/modification', 'pencil', true, 'sous_traitants_modification', null, array('form')),
                "Supprimer" => array('newsous_traitants/suppression', 'trash', true, 'sous_traitants_supprimer', 'Confirmez la suppression de la sous_traitants', array('confirm-modify')),
            ),
            array(
                "Export PDF" => array('#', 'book', false, 'export_pdf'),
                "Impression" => array('#', 'print', false, 'impression'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_newsous_traitants','m_societes_vendeuses'));
    }

    /******************************
     * List of owners Data
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
     * Liste des newsous
     ******************************/
    public function liste($id = 0, $liste = 0)
    {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une bornes","newsous/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'newsous_traitants/index',
            'detail'             => array('newsous_traitants/detail', 'sous_traitants_id', 'societe'),
            'champs'             => array(
                array('sno', 'text', "S.No"),
                array('societe_name', 'text', "Societe"),
                array('ctc_nom', 'ref', "Client", 't_contacts'),
                array('prix_max', 'text', 'Prix max proposé'),
                array('vil_nom', 'ref', "Villes", 't_villes'),
                array('pavillons', 'text', "Pavillons"),
                array('total_distribuer', 'text', "Total a Distribuer"),
                array('residences', 'text', "Residences"),
                array('hlm', 'text', "Hlm"),
                array('type_doc', 'text', "Type Doc"),
                array('type_client', 'text', "Type Client"),
                array('date_limite', 'text', "Date Limite"),
                array('semaine_prevue', 'text', "Semaine Prevue"),
                array('emp_nom', 'text', "Sous Traitant Demande"),
                array('tel_sous_traitant', 'text', "Tel Sous Traitant"),
                array('mail', 'text', "Mail"),

            ),
            'filterable_columns' => $this->m_newsous_traitants->liste_filterable_columns(),
        );

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'          => $id,
                'descripteur' => $descripteur,
                'toolbar'     => $toolbar,
                'controleur'  => 'newsous_traitants',
                'methode'     => 'index',
            ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newsous_traitants', $this->session->id);

        $data = array(
            'title'        => "Liste des Sous Traitants",
            'page'         => "templates/datatables-wtoolbar",
            'menu'         => "Sous|Sous Traitant",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action["Liste"],
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
    public function index_json($id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            die('');
        }

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

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_newsous_traitants->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'societe' => 't_sous_traitants',
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

                $resultat = $this->m_newsous_traitants->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);

            } else {
                $resultat = $this->m_newsous_traitants->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function create($id = 0, $ajax = false)
    {
        $cmd_globales = array(
            //    array("Nouvelle Bornes","newbornes/create",'default')
        );
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation

        $config = array(
            array('field' => 'sous_ville', 'label' => "Ville", 'rules' => 'trim|required'),

            array('field' => 'sous_societe', 'label' => "Societe", 'rules' => 'trim|required'),
        );

        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

            // validation réussie
            $valeurs = array(
                'societe'               => $this->input->post('sous_societe'),
                'ville'                 => $this->input->post('sous_ville'),
                'total_distribuer'      => $this->input->post('sous_distribuer'),
                'pavillons'             => $this->input->post('sous_pavillons'),
                'residences'            => $this->input->post('sous_residences'),
                'hlm'                   => $this->input->post('sous_hlm'),
                'client'                => $this->input->post('sous_client'),
                'prix_max'              => $this->input->post('sous_prix'),
                'type_doc'              => $this->input->post('sous_doc'),
                'type_client'           => $this->input->post('sous_typeclient'),
                'date_limite'           => $this->input->post('sous_date'),
                'semaine_prevue'        => $this->input->post('sous_prevue'),
                'sous_traitant_demande' => $this->input->post('sous_demande'),
                'tel_sous_traitant'     => $this->input->post('sous_tel'),
                'mail'                  => $this->input->post('sous_mail'),
            );

            $id = $this->m_newsous_traitants->sous_form($valeurs);

            $this->my_set_action_response($ajax, true, "Le Sous Traitant a été créé");

            if ($ajax) {
                //  return;
            }

            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire

            $valeurs        = new stdClass();
            $listes_valeurs = new stdClass();

            $valeurs->sous_societe    = $this->input->post('sous_societe');
            $valeurs->sous_distribuer = $this->input->post('sous_distribuer');
            $valeurs->sous_pavillons  = $this->input->post('sous_pavillons');
            $valeurs->sous_residences = $this->input->post('sous_residences');
            $valeurs->sous_hlm        = $this->input->post('sous_hlm');
            $valeurs->sous_doc        = $this->input->post('sous_doc');
            $valeurs->sous_typeclient = $this->input->post('sous_typeclient');
            $valeurs->sous_date       = $this->input->post('sous_date');
            $valeurs->sous_prevue     = $this->input->post('sous_prevue');
            $valeurs->sous_tel        = $this->input->post('sous_tel');
            $valeurs->sous_mail       = $this->input->post('sous_mail');
            $valeurs->sous_ville      = $this->input->post('sous_ville');
            $valeurs->sous_client     = $this->input->post('sous_client');
            $valeurs->sous_prix       = $this->input->post('sous_prix');
            $valeurs->sous_demande    = $this->input->post('sous_demande');

            $sous_societe = $this->m_societes_vendeuses->liste_option();
            $sous_ville   = $this->m_newsous_traitants->ville_list($valeurs->sous_ville);
            $sous_client  = $this->m_newsous_traitants->client_list($valeurs->sous_client);
            $sous_demande = $this->m_newsous_traitants->employe_list($valeurs->sous_demande);
            $scripts      = array();
            $scripts[]    = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#sous_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'sous_societe' => array("Societe", 'text', 'sous_societe', false),
                ),
                'onglets' => array(),
            );

            $data = array(
                'title'          => "Ajouter un nouveau Sous Traitant",
                'id'             => 0,
                'societe'        => $sous_societe,
                'ville'          => $sous_ville,
                'client'         => $sous_client,
                'demande'        => $sous_demande,
                'scripts'        => $scripts,
                'page'           => "sous_traitants/sous_traitantsform",
                'menu'           => "Sous Traitants|Create",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'newsous_traitants',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);

        }

    }

    /******************************
     * Détail d'un bornes
     ******************************/
    public function detail($id, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_newsous_traitants->detail($id);

            // commandes globales
            /*  $cmd_globales = array(
            array("Articles",'articles/articles_cat','default'),
            array("Import articles",'newbornes/importation','default'),
            array("Export articles",'newbornes/exportation','default')
            );
             */

            // commandes locales
            $cmd_locales = array(
                //    array("Modifier",'newbornes/modification','primary'),
                //    array("Supprimer",'newbornes/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'societe_name'      => array("Societe", 'VARCHAR 30', 'text', 'societe_name'),
                    'ctc_nom'           => array("Client", 'VARCHAR 30', 'text', 'ctc_nom'),
                    'prix_max'          => array("Prix max proposé", 'VARCHAR 30', 'text', 'prix_max'),
                    'vil_nom'           => array("Villes", 'VARCHAR 30', 'text', 'vil_nom'),
                    'pavillons'         => array("Pavillons", 'VARCHAR 30', 'text', 'pavillons'),
                    'total_distribuer'  => array("Total a Distribuer", 'VARCHAR 30', 'text', 'total_distribuer'),
                    'residences'        => array("Residences", 'VARCHAR 30', 'text', 'residences'),
                    'hlm'               => array("Hlm", 'VARCHAR 30', 'text', 'hlm'),
                    'type_doc'          => array("Type Doc", 'VARCHAR 30', 'text', 'type_doc'),
                    'type_client'       => array("Type Client", 'VARCHAR 30', 'text', 'type_client'),
                    'date_limite'       => array("Date Limite", 'VARCHAR 30', 'text', 'date_limite'),
                    'semaine_prevue'    => array("Semaine Prevue", 'VARCHAR 30', 'text', 'semaine_prevue'),
                    'emp_nom'           => array("sous_traitant_demande", 'VARCHAR 30', 'text', 'emp_nom'),
                    'tel_sous_traitant' => array("tel_sous_traitant", 'VARCHAR 30', 'text', 'tel_sous_traitant'),
                    'mail'              => array("Mail", 'VARCHAR 30', 'text', 'mail'),
                ),
                'onglets' => array(
                ),
            );

            $data = array(
                'title'        => "Détail d'un Sous Traitant",
                'page'         => "templates/detail",
                'menu'         => "Produits|Sous Traitant",
                'barre_action' => $this->barre_action["Element"],
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'newsous_traitants',
                'methode'      => 'detail',
                // 'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
            );
            $this->my_set_display_response($ajax, $data);
        }
    }
    /******************************
     * Mise à jour d'un bornes
     ******************************/
    public function modification($id = 0, $ajax = false)
    {

        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'sous_societe', 'label' => "Societe", 'rules' => 'trim|required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'societe'               => $this->input->post('sous_societe'),
                'ville'                 => $this->input->post('sous_ville'),
                'total_distribuer'      => $this->input->post('sous_distribuer'),
                'pavillons'             => $this->input->post('sous_pavillons'),
                'residences'            => $this->input->post('sous_residences'),
                'hlm'                   => $this->input->post('sous_hlm'),
                'client'                => $this->input->post('sous_client'),
                'prix_max'              => $this->input->post('sous_prix'),
                'type_doc'              => $this->input->post('sous_doc'),
                'type_client'           => $this->input->post('sous_typeclient'),
                'date_limite'           => $this->input->post('sous_date'),
                'semaine_prevue'        => $this->input->post('sous_prevue'),
                'sous_traitant_demande' => $this->input->post('sous_demande'),
                'tel_sous_traitant'     => $this->input->post('sous_tel'),
                'mail'                  => $this->input->post('sous_mail'),
            );

            // $id = $this->m_newsous_traitants->sous_editform($valeurs,$id);
            $resultat = $this->m_newsous_traitants->sous_editform($valeurs, $id);

            $redirection = 'newsous_traitants/detail/' . $id;
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification sous traitant";
                    $ajaxData = null;
                } else {
                    $message  = "Le Sous Traitant a été modifié";
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordchange',
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                        ),
                    );
                }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }

            if ($ajax) {
                //  return;
            }

            redirect($redirection);
        } else {

            $valeurs        = $this->m_newsous_traitants->edit_detail($id);
            $listes_valeurs = new stdClass();

            if ($this->input->post('sous_societe') != "") {
                $valeurs->sous_societe = $this->input->post('sous_societe');
            } else {
                $valeurs->sous_societe = $valeurs->societe;
            }

            $sous_societe = $this->m_societes_vendeuses->liste_option();

            if ($this->input->post('sous_distribuer') != "") {
                $valeurs->sous_distribuer = $this->input->post('sous_distribuer');
            } else {
                $valeurs->sous_distribuer = $valeurs->total_distribuer;
            }

            if ($this->input->post('sous_pavillons') != "") {
                $valeurs->sous_pavillons = $this->input->post('sous_pavillons');
            } else {
                $valeurs->sous_pavillons = $valeurs->pavillons;
            }
            if ($this->input->post('sous_residences') != "") {
                $valeurs->sous_residences = $this->input->post('sous_residences');
            } else {
                $valeurs->sous_residences = $valeurs->residences;
            }
            if ($this->input->post('sous_hlm') != "") {
                $valeurs->sous_hlm = $this->input->post('sous_hlm');
            } else {
                $valeurs->sous_hlm = $valeurs->hlm;
            }
            if ($this->input->post('sous_doc') != "") {
                $valeurs->sous_doc = $this->input->post('sous_doc');
            } else {
                $valeurs->sous_doc = $valeurs->type_doc;
            }
            if ($this->input->post('sous_typeclient') != "") {
                $valeurs->sous_typeclient = $this->input->post('sous_typeclient');
            } else {
                $valeurs->sous_typeclient = $valeurs->type_client;
            }
            if ($this->input->post('sous_date') != "") {
                $valeurs->sous_date = $this->input->post('sous_date');
            } else {
                $valeurs->sous_date = $valeurs->date_limite;
            }
            if ($this->input->post('sous_prevue') != "") {
                $valeurs->sous_prevue = $this->input->post('sous_prevue');
            } else {
                $valeurs->sous_prevue = $valeurs->semaine_prevue;
            }
            if ($this->input->post('sous_tel') != "") {
                $valeurs->sous_tel = $this->input->post('sous_tel');
            } else {
                $valeurs->sous_tel = $valeurs->tel_sous_traitant;
            }
            if ($this->input->post('sous_mail') != "") {
                $valeurs->sous_mail = $this->input->post('sous_mail');
            } else {
                $valeurs->sous_mail = $valeurs->mail;
            }
            if ($this->input->post('sous_ville') != "") {
                $sous_ville = $this->m_newsous_traitants->ville_list($this->input->post('sous_ville'));
            } else {
                $sous_ville = $this->m_newsous_traitants->ville_list($valeurs->ville);

            }
            if ($this->input->post('sous_prix') != "") {
                $valeurs->sous_prix = $this->input->post('sous_prix');
            } else {
                $valeurs->sous_prix = $valeurs->prix_max;
            }
            if ($this->input->post('sous_client') != "") {
                $sous_client = $this->m_newsous_traitants->client_list($this->input->post('sous_client'));
            } else {
                $sous_client = $this->m_newsous_traitants->client_list($valeurs->client);

            }
            if ($this->input->post('sous_demande') != "") {
                $sous_demande = $this->m_newsous_traitants->employe_list($this->input->post('sous_client'));
            } else {
                $sous_demande = $this->m_newsous_traitants->employe_list($valeurs->sous_traitant_demande);
            }
            $descripteur = array(
                'champs'  => array(
                    'sous_societe' => array("Societe", 'text', 'sous_societe', false),
                ),
                'onglets' => array(),
            );
            $scripts   = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#sous_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            $data = array(
                'title'          => "Modify Sous Traitant",
                'id'             => $id,
                'edit_societe'   => $sous_societe,
                'edit_ville'     => $sous_ville,
                'edit_client'    => $sous_client,
                'edit_demande'   => $sous_demande,
                'scripts'        => $scripts,
                'page'           => "sous_traitants/sous_traitantsform",
                'menu'           => "Sous Traitants|Modification",
                'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'newsous_traitants',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );

            $this->my_set_form_display_response($ajax, $data);
        }
    }
/******************************
 * Dupliquer Data
 ******************************/
    public function dupliquer($id, $ajax = false)
    {

        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = '';
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_newsous_traitants->dupliquer($id);

        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recordadd',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Propriétaire a été dupliquer", 'info', $ajaxData);
            if ($ajax) {
                return;
            }
            redirect($redirection);
        }
    }
    /******************************
     * Suppression d'un bornes
     ******************************/
    public function suppression($id, $ajax = false)
    {

        if ($this->input->method() != 'post') {
            die;
        }

        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_newsous_traitants->suppression($id);

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
            $this->my_set_action_response($ajax, true, "La borne a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }

        redirect($redirection);
    }

    public function ajaxemployee()
    {
        if (!$this->input->is_ajax_request()) {
            die('');
        }

        $dt        = $this->input->post('id');
        $emp_data  = $this->m_newsous_traitants->employee_data($dt);
        echo $html = '<div class="row guide">
            <div class="form-group">
            <label class="col-sm-3 control-label" >Tel Sous-Traitant</label>
            <div class="col-sm-9">
            <input id="sous_tel" class="form-control" name="sous_tel" value="' . $emp_data[0] . '" placeholder="Tel Sous-Traitant" type="text">
            </div>
            </div>
            </div>
            <div class="row guide">
            <div class="form-group">
            <label class="col-sm-3 control-label" >Mail</label>
            <div class="col-sm-9">
            <input id="sous_mail" class="form-control" name="sous_mail" value="' . $emp_data[1] . '" placeholder="Mail" type="text">
            </div>
            </div>
            </div>';

        // $emp_data = $this->m_newvigik->client_data($dt);
    }
    /******************************
     * Exportation d'un bornes
     ******************************/
    public function exportation($id)
    {
        $export = $this->m_newsous_traitants->exportation($id);
        $data   = array(
            'title'  => "Exportation d'un bornes",
            'page'   => "newbornes/exportation",
            'menu'   => "Produits|Exportation de bornes",
            'values' => array(
                'export' => $export,
            ),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

    /******************************
     * Importation d'un bornes
     ******************************/
    public function importation($id)
    {
        $this->load->helper(array('form', 'ctrl'));
        $resultat = false;
        if (array_key_exists('bornes', $_FILES)) {
            $f = $_FILES['bornes'];
            if ($f['error'] == 0) {
                $extension   = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'] . $extension;
                rename($f['tmp_name'], $nom_fichier);
                $resultat = $this->m_newsous_traitants->importation($id, $nom_fichier);
                if ($resultat === false) {
                    $this->session->set_flashdata('danger', 'Un problème technique est survenu - veuillez reessayer ultérieurement');
                } elseif ($resultat === true) {
                    $this->session->set_flashdata('success', 'Le bornes a été chargé');
                    redirect('newbornes/detail/' . $id);
                }
            } else {
                switch ($f['error']) {
                    case 1:
                        $erreur = 'Le fichier ' . $f['name'] . ' est trop volumineux.';
                        break;
                    case 2:
                        $erreur = 'Le fichier ' . $f['name'] . ' est trop volumineux.';
                        break;
                    case 3:
                        $erreur = 'Le fichier ' . $f['name'] . " n'a été que partiellement téléchargé.";
                        break;
                    case 4:
                        if (file_exists($f['tmp_name'])) {
                            unlink($f['tmp_name']);
                            $erreur = "Un fichier de même nom existait. Veuillez recommencer.";
                        } else {
                            $erreur = "Vous n'avez pas désigné le fichier";
                        }
                        break;
                    case 7:
                        $erreur = 'Le fichier ' . $f['name'] . " n'a pu être enregistré.";
                        break;
                    default:
                        $erreur = 'Erreur lors du téléchargement du fichier ' . $f['name'];
                }
                $this->session->set_flashdata('danger', $erreur);
            }
        }
        $data = array(
            'title'  => "Importation de bornes",
            'page'   => "newbornes/importation",
            'menu'   => "Produits|Importation de bornes",
            'values' => array(
                'resultat' => $resultat,
            ),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

}

// EOF
