<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_statistiques_prospection m_statistiques_prospection
 */
class Statistiques_prospection extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model(array('m_statistiques_prospection','m_contacts','m_societes_vendeuses','m_employes','m_utilisateurs'));
		$this->load->library('table');
	}

	function index($id = 0)
	{
		$valeurs['list_commercial']	= $this->m_utilisateurs->liste_option();
		$valeurs['list_generale']	= $this->m_contacts->origine_generale_liste();
		$valeurs['list_origine']	= $this->m_contacts->liste_origine_prospect_per_group();
		$valeurs['list_enseignes']	= $this->m_societes_vendeuses->liste_option();

		$scripts = array();
		$scripts[] = $this->load->view('statistiques_prospection/form-js',array(),true);

		$data = array(
				'title' => "Statistiques Prospection",
				'page' => "statistiques_prospection/detail",
				'menu' => "Ventes|Détail Statistiques Prospection",
				'id' => $id,
				'scripts' => $scripts,
				'values' => $valeurs,
				'controleur' => 'statistiques_prospection',
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
		$commercial = $this->input->post('commercial'); //KETO
		$periods 	= $this->input->post('periods'); // MOIS
		$generale   = $this->input->post('generale'); // ORIGINE GENERALE
		$origine 	= $this->input->post('origine'); // ADWORDS PUBLIMAIL
		$enseignes 	= $this->input->post('enseignes'); // PUBLIMAIL
		if($periods == "all")
		{
			$valeurs['header'] = $header;
		}
		else
		{
			$valeurs['header'] = array($periods => $header[$periods]);
		}
		$devis		= $this->m_statistiques_prospection->getTotalDevis($commercial,$periods,$generale,$origine,$enseignes);
		$factures	= $this->m_statistiques_prospection->getTotalFactures($commercial,$periods,$generale,$origine,$enseignes);		
		
		foreach($devis as $key => $val)
		{
			$valeurs['data']['total_demandes_de_devis'][$key] = $val['value'];
			$valeurs['data']['nombre_signe'][$key] = $factures[$key]['value'];
			
			if($val['value'] == 0)
				$valeurs['data']['%_signe'][$key] = 0;
			else		
				$valeurs['data']['%_signe'][$key] = round($factures[$key]['value']/$val['value']*100)."%";
			
			$valeurs['data']['ca_signe'][$key] = $factures[$key]['total_ht'];
		}
		
		// echo "<pre>";
		// print_r($devis);
		// print_r($factures);
		// print_r($valeurs);
		// echo "</pre>";
		// exit();		

		$valeurs['list_origine']	= $this->m_statistiques_prospection->getOrigine($commercial,$periods,$generale,$origine,$enseignes);		
		$this->load->view('statistiques_prospection/table_filter',$valeurs);
	}
}