<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_demande_devis_commerciaux m_demande_devis_commerciaux
 */
class Demande_devis_commerciaux extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model(array('m_demande_devis_commerciaux','m_contacts','m_societes_vendeuses','m_employes','m_utilisateurs'));
		$this->load->library('table');
	}

	function index($id = 0)
	{
		$valeurs['list_commercial']	= $this->m_utilisateurs->liste_option();
		//$valeurs['list_generale']	= $this->m_contacts->origine_generale_liste();
		//$valeurs['list_origine']	= $this->m_contacts->liste_origine_prospect_per_group();
		//$valeurs['list_enseignes']	= $this->m_societes_vendeuses->liste_option();

		$scripts = array();
		$scripts[] = $this->load->view('demande_devis_commerciaux/form-js',array(),true);

		$data = array(
				'title' => "Demande Devis Commerciaux",
				'page' => "demande_devis_commerciaux/header_filter",
				'menu' => "Ventes|Détail Demande Devis Commerciaux",
				'id' => $id,
				'scripts' => $scripts,
				'values' => $valeurs,
				'controleur' => 'demande_devis_commerciaux',
		);
		$layout="layouts/standard";
		$this->load->view($layout,$data);
	}

	function getFilter()
	{
		$header = array(
				'week' 	=> 'Semaine',
				'month' => 'Mois En Cours',
				'day30' => '30 Jours',
				'day90' => '90 Jours',
				'month6'=> '6 Mois',
				'year' 	=> '1 An',
		);
		$commercial = $this->input->post('commercial');
		$periods 	= $this->input->post('periods');
		$find = strpos($periods,' - ');
		//debug($cnt_c,0);
		//debug($periods,0);
		if($periods == "all")
		{
			$valeurs['header'] = $header;
		}
		elseif($find !== false)
		{
			
		}
		else
		{
			$valeurs['header'] = array($periods => $header[$periods]);
		}
		$adwords		= $this->m_demande_devis_commerciaux->getAdwords($commercial,$periods);
		foreach($adwords as $key => $val)
		{
			$valeurs['data']['adwords']['Adwords'][$key] = $val['origine'];
			$valeurs['data']['adwords']['nombre_signe'][$key] = $val['nombre'];
			
			if($val['origine'] == 0)
				$valeurs['data']['adwords']['%_signe'][$key] = 0;
			else		
				$valeurs['data']['adwords']['%_signe'][$key] = round($val['nombre']/$val['origine']*100)."%";
			
			//$valeurs['data']['adwords']['ca_signe'][$key] = $val['total_ht'];
		}
		$emailing		= $this->m_demande_devis_commerciaux->getEmailing($commercial,$periods);
		foreach($emailing as $key => $val)
		{
			$valeurs['data']['emailing']['E-mailing'][$key] = $val['origine'];
			$valeurs['data']['emailing']['nombre_signe'][$key] = $val['nombre'];
			
			if($val['origine'] == 0)
				$valeurs['data']['emailing']['%_signe'][$key] = 0;
			else		
				$valeurs['data']['emailing']['%_signe'][$key] = round($val['nombre']/$val['origine']*100)."%";
			
			//$valeurs['data']['emailing']['ca_signe'][$key] = $val['total_ht'];
		}
		$autre_origine = $this->m_demande_devis_commerciaux->getAutre($commercial,$periods);
		foreach($autre_origine as $key => $val)
		{
			$valeurs['data']['autre_origine']['Autre_Origine'][$key] = $val['origine'];
			$valeurs['data']['autre_origine']['nombre_signe'][$key] = $val['nombre'];
			
			if($val['origine'] == 0)
				$valeurs['data']['autre_origine']['%_signe'][$key] = 0;
			else		
				$valeurs['data']['autre_origine']['%_signe'][$key] = round($val['nombre']/$val['origine']*100)."%";
			
			//$valeurs['data']['autre_origine']['ca_signe'][$key] = $val['total_ht'];
		}

		//$valeurs['list_origine']	= $this->m_demande_devis_commerciaux->getOrigine($commercial,$periods,$generale,$origine,$enseignes);
		$this->load->view('demande_devis_commerciaux/table_filter',$valeurs);
	}
}