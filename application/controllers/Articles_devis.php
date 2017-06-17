<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time:
*
* @property M_articles_devis m_articles_devis
*/
class Articles_devis extends MY_Controller {
    private $profil;

    public function __construct() {
        parent::__construct();
        $this->load->model('m_articles_devis');
    }

    /******************************
    * Liste des articles d'un devis
    ******************************/
    public function articles_devis($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'articles_devis/articles_devis',
            'detail' => array('articles_devis/detail','ard_id','ard_code'),
            'champs' => array(
                array('ard_code','text',"Code article"),
                array('ard_description','text',"Description"),
                array('ard_info','text',"Informations spécifiques"),
                array('ard_prix','number',"PUHT"),
                array('ard_quantite','number',"Quantité"),
                array('cat_version','ref',"Catalogue",'catalogues','art_catalogue','cat_version'),
            )
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("articles_devis/liste-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'articles_devis',
                'methode' => __FUNCTION__,
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('articles_devis',$this->session->id);
        $data = array(
            'title' => "Liste des articles d'un devis",
            'page' => "articles_devis/liste",
            'menu' => "Ventes|Articles de devis",
            'controleur' => 'articles_devis',
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
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
    * Liste des articles d'un devis (datasource)
    ******************************/
    public function articles_devis_json($id=0) {
        if (! $this->input->is_ajax_request()) die('');
        $resultat = $this->m_articles_devis->liste($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));

    }

    /******************************
    * Détail d'un article de devis
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
            $valeurs = $this->m_articles_devis->detail($id);

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'ard_code' => array("Code article",'VARCHAR 30','text','ard_code'),
                   'ard_article' => array("Article",'REF','ref',array('articles','ard_article','art_description')),
                   'ard_description' => array("Description",'VARCHAR 400','text','ard_description'),
                   'ard_info' => array("Informations spécifiques",'VARCHAR 100','text','ard_info'),
                   'ard_prix' => array("PUHT",'DECIMAL 7,4','number','ard_prix'),
                   'ard_quantite' => array("Quantité",'INT 5','number','ard_quantite'),
                   'art_catalogue' => array("Catalogue",'REF','ref',array('catalogues','art_catalogue','cat_version')),
                   'ard_devis' => array("Devis",'REF','ref',array('devis','ard_devis','dvi_reference')),
                   'ard_remise_taux' => array("Remise (taux)",'DECIMAL 4,2','number','ard_remise_taux'),
                   'ard_remise_ht' => array("Remise (HT)",'DECIMAL 6,2','number','ard_remise_ht'),
                   'ard_remise_ttc' => array("Remise (TTC)",'DECIMAL 6,2','number','ard_remise_ttc')
                ),
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un article de devis",
                'page' => "templates/detail",
                'menu' => "Ventes|Article de devis",
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'articles_devis',
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