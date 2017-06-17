<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_articles m_articles
*/
class Articles extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Consulter" => array('*articles/detail','eye-open',false,'articles_detail',null,array('view')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        ),
        "Element" => array(
            array(
                    "Consulter" => array('articles/detail','eye-open',true,'articles_detail',null,array('view'))
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_articles');
    }

    /******************************
    * Liste des articles d'un catalogue
    ******************************/
    public function articles_cat($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'articles/articles_cat',
            'detail' => array('articles/detail','art_id','art_code'),
            'champs' => $this->m_articles->get_champs('read'),
            'filterable_columns' => $this->m_articles->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'articles',
                'methode' => 'articles_cat'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('articles',$this->session->id);
        $data = array(
            'title' => "Liste des articles d'un catalogue",
            'page' => "templates/datatables",
            'menu' => "Produits|Articles",
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
    * Liste des articles d'un catalogue (datasource)
    ******************************/
    public function articles_cat_json($id=0) {
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
            $resultat = $this->m_articles->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'art_code' => 't_articles',
                'cat_version' => 't_catalogues'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_articles->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
            }
            else {
                $resultat = $this->m_articles->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    /******************************
    * Détail d'un article
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
            $valeurs = $this->m_articles->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'art_code' => array("Code article",'VARCHAR 30','text','art_code'),
                   'art_prod' => array("Code production",'VARCHAR 5','text','art_prod'),
                   'art_prix' => array("PUHT",'DECIMAL 7,4','number','art_prix'),
                   'art_description' => array("Description",'VARCHAR 200','text','art_description'),
                   'art_libelle' => array("Libellé devis",'VARCHAR 400','text','art_libelle'),
                   'art_catalogue' => array("Catalogue",'REF','ref',array('catalogues','art_catalogue','cat_version')),
                   'cat_famille' => array("Famille",'REF','text','vfm_famille'),
                   'art_data' => array("Données spécifiques",'VARCHAR 1000','textarea','art_data')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un article",
                'page' => "templates/detail",
                'menu' => "Produits|Article",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'articles',
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