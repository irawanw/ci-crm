<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Dashboard extends CI_Controller
{
    private $profil;
    private $barre_action = array(
   //      array(
			// "Creer" => array('#', 'plus', true, 'feuille_de_route_creer'),
   //          "Consulter" => array('*feuille_de_route/detail', 'eye-open', false, 'feuille_de_route_detail'),			
   //          "Supprimer" => array('#', 'trash', false, 'feuille_de_route_supprimer'),
   //      ),
   //      array(
   //          "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
   //          "Export pdf"  => array('#', 'book', true, 'export_pdf'),
   //          "Imprimer"    => array('#', 'print', true, 'print_list'),
   //      ),
    );
   

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_dashboard');
    }

    /******************************
     * 
     ******************************/
    public function index()
    {
        $values = new StdClass();
        $values->liste_secteur_type = $this->m_dashboard->liste_secteur_type();
         
        // commandes globales
        $cmd_globales = array();

        // commandes locales
        $cmd_locales = array();      

        $data = array(
            'title'        => "Dashboard",
            'page'         => "dashboard/index",
            'menu'         => "",
            'id'           => null,
            'values'       => $values,
            'controleur'   => 'dashboard',
            'methode'      => 'index',
            'cmd_globales' => $cmd_globales,
            'cmd_locales'  => $cmd_locales,
            'descripteur'  => array(),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }  
}
// EOF
