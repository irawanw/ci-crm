<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class Commandes_trier extends CI_Controller
{
    private $profil;

    private $barre_action_ville = array(
        array(
            "Valider ce tri" => array('commandes_trier/valider_ce_tri', 'ok', true, 'commandes_trier_valider'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_commandes_trier');
    }

    public function test()
    {
        $lists = $this->m_commandes_trier->liste();

        //echo print_r($lists); exit();

        echo '<table border="1">';

        echo '<thead><tr>';
        foreach ($lists[0] as $i => $row) {
            echo '<th>' . $i . '</th>';
        }
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($lists as $list) {
            echo '<tr>';
            foreach ($list as $val) {
                echo '<td>' . $val . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    /******************************
     * Liste des catalogues
     ******************************/
    public function index()
    {
        $lists = $this->m_commandes_trier->liste();
        // commandes globales
        $cmd_globales = array();

        // toolbar
        $toolbar = '';

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes_trier', $this->session->id);
        $data = array(
            'title'   => "Liste des Commandes Trier",
            'page'    => "commandes_trier/table",
            'menu'    => "",
            'scripts' => $scripts,
            'values'  => array(
                'id'           => 0,
                'vues'         => $vues,
                'cmd_globales' => $cmd_globales,
                'toolbar'      => $toolbar,
                'descripteur'  => array(),
                'lists'        => $lists,
            ),
        );
        $layout = "layouts/datatables";
        $this->load->view($layout, $data);
    }

    /******************************
     * Détail d'un commande trier
     ******************************/
    public function detail($id)
    {

        $valeurs = $this->m_commandes_trier->detail($id);
        // commandes globales
        $cmd_globales = array();

        // commandes locales
        $cmd_locales = array();      

        $data = array(
            'title'        => "Détail d'un commande trier",
            'page'         => "commandes_trier/detail",
            'menu'         => "",
            'id'           => $id,
            'values'       => $valeurs,
            'controleur'   => 'commandes_trier',
            'methode'      => 'detail',
            'cmd_globales' => $cmd_globales,
            'cmd_locales'  => $cmd_locales,
            'descripteur'  => array(),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }  

    public function ville($ville_id)
    {
        $option = '';
        $param = '?';

        if ($ville_id) {
            $param .= 'ville_id='.$ville_id;            

            $this->ville_liste($option . $param, '');
        } else {
            redirect('commandes_trier');
        }
    }

    public function ville_liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            // array("Ajouter un e-mailing pages jaunes","owners/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'commandes_trier/ville',
            // 'detail'             => array(),
            // 'archive'            => array(),
            'champs'             => array(                                
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
				array('dvi_date', 'date', "Date", 'dvi_date'),
				array('secteur', 'text', 'Secteur', 'sectuer'),                
                array('ctc_nom', 'text', "Client", 'ctc_nom'),                
                array('cmd_reference', 'text', "Commande Ref", 'cmd_reference'),                            
                array('hlm', 'text', "HLM", 'hlm'),                            
                array('res', 'text', "RES", 'res'),                            
                array('pav', 'text', "PAV", 'pav'),                            
            ),
            'filterable_columns' => $this->m_commandes_trier->ville_liste_filterable_columns(),
        );

        //determine json script that will be loaded
        //for eg: owners/archived_json in kendo_grid-js
        // switch ($mode) {
        //     case 'archiver':
        //         $descripteur['datasource'] = 'commandes_trier/archived';
        //         break;
        //     case 'supprimees':
        //         $descripteur['datasource'] = 'commandes_trier/deleted';
        //         break;
        //     case 'all':
        //         $descripteur['datasource'] = 'commandes_trier/all';
        //         break;
        // }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts = array();

        $scripts[] = $this->load->view("commandes_trier/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'commandes_trier',
                'methode'               => 'index',
                'mass_action_toolbar'   => false,
                'view_toolbar'          => false,  
                'external_toolbar'      => 'ville-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'commandes_trier',
                ),              
            ), true);
        $scripts[] = $this->load->view("commandes_trier/ville-js", array(), true);
        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('commandes_trier', $this->session->id);
        $data = array(
            'title'        => "Liste commande ville",
            'page'         => "templates/datatables",
            'menu'         => "",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action_ville,
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
	
    public function ville_json($id = 0)
    {
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
            $resultat = $this->m_commandes_trier->ville_liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ard_id' => 't_articles_devis',
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

                $resultat = $this->m_commandes_trier->ville_liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_commandes_trier->ville_liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function valider_ce_tri($ville_id)
    {
    	if($ville_id) {
    		$result = $this->m_commandes_trier->valider_ce_tri($ville_id);						
			
    		if($result) {
    			$this->session->set_flashdata('success', "Succès valider ce tri");
    		} else {
				$this->session->set_flashdata('danger', "Un problème valider ce tri");    			
    		}
			
			echo $result;
    		//redirect('feuilles_de_tri/?new_id='.$result, 'refresh');
    	} else {
    		redirect('commandes_trier', 'refresh');
    	}
    }

    public function ville_test($id)
    {

        $valeurs = $this->m_commandes_trier->testville($id);
        echo "<pre>";
        print_r($valeurs);
        echo "</pre>";
    }  

    public function detail_test($id)
    {

        $valeurs = $this->m_commandes_trier->detail($id);
        echo "<pre>";
        print_r($valeurs);
        echo "</pre>";
    }  
}
