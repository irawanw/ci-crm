<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_demande_devis_quick_followup m_demande_devis_quick_followup
 */
class Demande_devis_quick_followup extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_demande_devis_quick_followup','m_utilisateurs'));
    }

    public function index($id = 0)
    {
        $valeurs = array();
        $scripts = array();

        $scripts[] = $this->load->view('demande_devis_quick_followup/liste-js', array(), true);

        $data = array(
            'title'      => "Demande devis quick followup",
            'page'       => "demande_devis_quick_followup/liste",
            'menu'       => "Ventes|Détail Demande devis quick followup",
            'id'         => $id,
            'scripts'    => $scripts,
            'values'     => $valeurs,
            'controleur' => 'demande_devis_quick_followup',
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

    public function get_data()
    {
    	$rangeDate = $this->input->post('rangeDate');
    	
    	$valeurs = array();
    	$valeurs['list_commercial']	= $this->m_utilisateurs->liste_option();
    	$valeurs['list_generale'] = array(    		
    		'2' => 'Adwords',
    		'1' => 'E-mailing',    		
    		'all' => 'All Origins',	
    	);

    	$valeurs['months']       = $this->m_demande_devis_quick_followup->get_month_range($rangeDate);
    	$valeurs['weeks']		 = $this->m_demande_devis_quick_followup->get_week_range($rangeDate);
    	$valeurs['data_monthly'] = $this->m_demande_devis_quick_followup->get_monthly_report($rangeDate);
    	$valeurs['data_weekly'] = $this->m_demande_devis_quick_followup->get_weekly_report($rangeDate);
    	$this->load->view('demande_devis_quick_followup/table',$valeurs);
    }

    public function test()
    {
    	$res = $this->m_demande_devis_quick_followup->get_weekly_report();


    }
}
