<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Newvigik extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                "Nouveau" => array('newvigik/create','plus',true,'vigik_nouveau',null,array('form')),
                "Upload" => array('newvigik/upload','upload',true,'vigik_upload'),
            ),
            array(
                "Consulter" => array('newvigik/detail','eye-open',false,'vigik_detail',null,array('view')),
                "Modifier" => array('newvigik/modification','pencil',false,'vigik_modification',null,array('form')),
                "Supprimer" => array('newvigik/suppression','trash',false,'vigik_supprimer','Veuillez confirmer la suppression du VIGIK', array('confirm-modify')),
            ),
            /*
            array(
                    "Export Excel" => array('newvigik/exportation','book',true,'export_excel'),
                    "Export PDF" => array('newvigik/exportpdf','book',true,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),
            */
            array(
                "VIGIK" => array('newvigik/index[]','hand-right',true,'newvigik_detail'),
                "Bornes" => array('newbornes/index[]','book',true,'newbornes_detail'),
            ),
            array(
                "Adresses de livraison" => array('newadresse/index[]','home',true,'adresse_detail'),
                "Tournées" => array('newtournee/index[]','map-marker',true,'tournee_detail'),
                "Tournées journalières" => array('newtournee_journalieres/index[]','calendar',true,'tourneejourn_detail'),
            ),
        ),
        "Element" => array(
            array(
                "Consulter" => array('newvigik/detail','eye-open',true,'vigik_detail',null,array('view')),
                "Modifier" => array('newvigik/modification','pencil',true,'vigik_modification',null,array('form')),
                "Supprimer" => array('newvigik/suppression','trash',true,'vigik_supprimer','Veuillez confirmer la suppression du VIGIK', array('confirm-modify')),
            ),
            array(
                "Export Excel" => array('newvigik/exportation','list-alt',true,'export_excel'),
                "Export PDF" => array('newvigik/exportpdf','book',true,'export_pdf'),
                "Impression" => array('newvigik/importation','print',false,'impression'),
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_newvigik');
    }

    /******************************
    * Liste des newvigik
    ******************************/

	
	 public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une bornes","newbornes/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newvigik/index',
            'detail' => array('newvigik/detail','vigik_id','vigik_numero'),
            'champs' => array(
                array('check','',"<input type='checkbox' name='check-all' id='check-all'>"),
                array('sno','text',"S.No"),
                array('vigik_numero','text',"Numéro"),
                array('borne_numero','ref',"Borne",'t_bornes'),
				array('scv_nom','ref',"Société",'t_societes_vendeuses'),
                array('type','text',"Type"),
				array('ctc_nom','ref',"Client Nom",'t_contacts'),
				array('fac_reference','ref',"Facture",'t_factures'),
                array('dvi_reference','ref',"Devis","t_devis"),
                array('etat','text',"État"),
                array('adresse_numero','ref',"Adresse",'t_adressevigik'),
                array('tournee_numero','ref',"Tournée",'t_tourneevigik'),
                array('ouvertures','text',"Nbre d'ouvertures"),
                array('chargements','text',"Nbre de chargements")
				
            ),
            'filterable_columns' => $this->m_newvigik->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("vigiks/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'newvigik',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newvigik',$this->session->id);
	
        $data = array(
            'title' => "Liste des VIGIK",
            'page' => "vigiks/datatablesvigik",
            'menu' => "Vigik|Vigik",
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

    public function index_json($id=0)
    {
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
            $resultat = $this->m_newvigik->liste($id,$pagelength, $pagestart, $filters);
			
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'vigik_numero' => 't_vigik'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newvigik->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newvigik->liste($id,$pagelength, $pagestart, $filters);
            }
        }      
if (!empty($resultat['data'])) {

$json = array();	
foreach($resultat['data'] as $row){
	
	    $json['check'] = "<input type='checkbox' name='chk[]' id='chk' class='checkbox' value='".$row->vigik_id."'>";	
        $json['sno'] = $row->sno;	
        $json['vigik_id'] = $row->vigik_id;		
	    $json['vigik_numero'] = $row->vigik_numero;		
	    $json['borne_numero'] = $row->borne_numero;		
	    $json['scv_nom'] = $row->scv_nom;		
	    $json['type'] = $row->type;		
	    $json['ctc_nom'] = $row->ctc_nom;		
	    $json['fac_reference'] = $row->fac_reference;		
	    $json['dvi_reference'] = $row->dvi_reference;		
	    $json['etat'] = $row->etat;		
	    $json['adresse_numero'] = $row->adresse_numero;
	    $json['tournee_numero'] = $row->tournee_numero;
	    $json['ouvertures'] = $row->ouvertures;
	    $json['chargements'] = $row->chargements;
        $data[] = (object)$json;
}
$resultat['data']=$data;
}

	$this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

	public function mass_action() {
        if (! $this->input->is_ajax_request()) die('');
		       $valeurs = array(
                'etat' => $this->input->post('type')
				);
		$v_id=$this->input->post('id');
        $resultat = $this->m_newvigik->mass_action($valeurs,$v_id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }

	 public function upload() {
		$this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');      
	  
        // règles de validation
         $config = array(

           array('field'=>'ctc_test','label'=>"Upload",'rules'=>'trim|required')

        );



        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

            // validation réussie

        $file = $_FILES['ctc_upload']['tmp_name'];
        //load the excel library
        $this->load->library('excel');
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        //get only the Cell Collection
     /*   $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
        //extract to a PHP readable array format
		$i=0;
        foreach ($cell_collection as $cell) {
			$i++;			
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
           echo $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
			//echo $data_value;
            //header will/should be in row 1 only.
			//echo $row ;
			
            if ($row == 1) {
                $header[$row][$column] = $data_value;
            } else {
                $arr_data[$row][$column] = $data_value;
			    	//echo $data_value;
					//exit;
                $this->m_newvigik->upload($data_value,$row);

            }
			
			
			
        }*/

			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			if(isset($allDataInSheet[2]["D"])){
				$wrong_format="yes";
			}
			else{
				$wrong_format="";
			}
			
			$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
			if($wrong_format!='yes'){
			for($i=2;$i<=$arrayCount;$i++){
				 $vigik_number = trim($allDataInSheet[$i]["A"]);
			
				 $u1 = trim($allDataInSheet[$i]["B"]);	
				 $u2 = trim($allDataInSheet[$i]["C"]);
				 
				$valeurs = array(

					'vigik_numero' => $vigik_number,

					'chargements' => $u1,

					'ouvertures' => $u2
				);
				 $this->m_newvigik->upload($valeurs,$vigik_number);

			}

                $this->session->set_flashdata('success',"Le vigik a upload success");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
			}
			else{
				 $this->session->set_flashdata('danger',"Le vigik a upload file unsuccessful.format not correct");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
			}///for wrong xls format upload

        }

        else {



            // validation en échec ou premier appel : affichage du formulaire

            $valeurs = new stdClass();

            $listes_valeurs = new stdClass();

            $valeurs->ctc_upload = $this->input->post('ctc_upload');
            

 

            $data = array(

                'title' => "upload un vigik",
				
				'id' => 0,

                'page' => "vigiks/vigikuploadform",

                'menu' => "newvigik|upload",

                'barre_action' => $this->barre_action["Liste"],

                'values' => $valeurs,

                'action' => "upload",

                'multipart' => true,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newvigik',

                'methode' => 'upload'

               

            );

            $layout="layouts/standard";

            $this->load->view($layout,$data);

     

	 }	
	 }	 
		
	public function create($id=0,$ajax=false) {
        $cmd_globales = array(
        //    array("Nouvelle Vigik","newvigik/create",'default')
        );
        $this->load->helper(array('form','ctrl'));

        $this->load->library('form_validation');

     

        // règles de validation

        $config = array(

            array('field'=>'ctc_numero','label'=>"Numero",'rules'=>'trim|required'),

            array('field'=>'ctc_borne','label'=>"Borne",'rules'=>'trim|required'),

            array('field'=>'ctc_societe','label'=>"Societe",'rules'=>'trim|required'),

            array('field'=>'ctc_type','label'=>"Type",'rules'=>'trim|required'),

            array('field'=>'ctc_client','label'=>"Client",'rules'=>'trim|required')

        );



        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

            // validation réussie

            $valeurs = array(

                'vigik_numero' => $this->input->post('ctc_numero'),

                'borne' => $this->input->post('ctc_borne'),

                'societe' => $this->input->post('ctc_societe'),

                'type' => $this->input->post('ctc_type'),

                'client' => $this->input->post('ctc_client'),

                'facture' => $this->input->post('ctc_facture'),

                'devis' => $this->input->post('ctc_devis'),
				
                'etat' => $this->input->post('ctc_etat'),

                'cl_adresse' => $this->input->post('ctc_adresse'),
				
				'tournee' => $this->input->post('ctc_tournee'),
				
				'ouvertures' => $this->input->post('ctc_ouvertures'),
				
				'chargements' => $this->input->post('ctc_chargements')

               

            );

           

            $id = $this->m_newvigik->vigik_form($valeurs);

                $this->my_set_action_response($ajax,true,"Le VIGIK a été créé");

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

            $valeurs->ctc_numero = $this->input->post('ctc_numero');

            $valeurs->ctc_borne = $this->input->post('ctc_borne');

            $valeurs->ctc_societe = $this->input->post('ctc_societe');
			
            $valeurs->ctc_type = $this->input->post('ctc_type');
			
            $valeurs->ctc_adresse = $this->input->post('ctc_adresse');
			
            $valeurs->ctc_etat = $this->input->post('ctc_etat');
			
            $valeurs->ctc_tournee = $this->input->post('ctc_tournee');
			
            $valeurs->ctc_ouvertures = $this->input->post('ctc_ouvertures');
			
            $valeurs->ctc_chargements = $this->input->post('ctc_chargements');
			
            $soc_id = $this->m_newvigik->society_list($valeurs->ctc_societe);
			
			$born_id = $this->m_newvigik->bornes_list($valeurs->ctc_borne);
			
			$adresse_id = $this->m_newvigik->adresse_list($valeurs->ctc_adresse);
			
			$tournee_id = $this->m_newvigik->tournee_list($valeurs->ctc_tournee);

            

 

            $data = array(

                'title' => "Ajouter un VIGIK",
				
				'id' => 0,
				
                'soc_id' => $soc_id,
				
                'born_id' => $born_id,
				
                'adresse_id' => $adresse_id,
				
                'tournee_id' => $tournee_id,

                'page' => "vigiks/vigikform",

                'menu' => "newvigik|create",

                //'barre_action' => $this->barre_action["Element"],

                'values' => $valeurs,

                'action' => "create",

                'multipart' => false,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newvigik',

                'methode' => 'create'

            );
            $this->my_set_form_display_response($ajax,$data);

     

		}

    }



  public function ajaxctype() {

	  if (! $this->input->is_ajax_request()) die('');

	  $dt=$this->input->post('id');

	  if($this->input->post('id')=="client"){

		   $id = $this->m_newvigik->client_list($dt);

		   

		   $data = array(

                'id' => $id,    
                'type' => $dt
				

            );

           return $this->load->view("vigiks/ajax/vigik_type",$data);

		   

	  }

  }

  public function ajaxclient() {

	  if (! $this->input->is_ajax_request()) die('');

	       $dt=$this->input->post('id');

		   $cl_address = $this->m_newvigik->client_data($dt);		   

		   $facture= $this->m_newvigik->client_facture($dt,0);

		   $devis = $this->m_newvigik->client_devis($dt,0);
		   $adresse = $this->m_newvigik->client_adresse($dt,0);
		
		   

		   $data = array(
                'client' => $dt,
                'cl_address' => $cl_address,
                'facture' => $facture,
                'devis' => $devis,
                'adresse' => $adresse                   

            );

           return $this->load->view("vigiks/ajax/vigik_client",$data);

  }

  
    /******************************
    * Détail d'un vigik
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_newvigik->detail($id);
			
            // commandes globales
           /* $cmd_globales = array(
                array("Articles",'articles/articles_cat','default'),
                array("Import articles",'newvigik/importation','default'),
                array("Export articles",'newvigik/exportation','default')
            );
			*/

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'newvigik/modification','primary'),
            //    array("Supprimer",'newvigik/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'vigik_numero' => array("Numéro",'VARCHAR 30','text','vigik_numero'),
                   'borne' => array("Borne",'VARCHAR 30','text','borne_numero'),
                   'societe' => array("Société",'VARCHAR 30','text','scv_nom'),
                   'type' => array("Type",'VARCHAR 30','text','type'),
                   'client' => array("Client",'VARCHAR 30','text','ctc_nom'),
                   'facture' => array("Facture",'VARCHAR 30','text','fac_reference'),
                   'devis' => array("Devis",'VARCHAR 30','text','dvi_reference'),
                   'etat' => array("etat",'VARCHAR 30','text','etat'),
                   'cl_adresse' => array("Adresse",'VARCHAR 30','text','adresse_numero'),
                   'tournee' => array("Tournée",'VARCHAR 30','text','tournee_numero'),
                   'ouvertures' => array("Nbre d'ouvertures",'VARCHAR 30','text','ouvertures'),
                   'chargements' => array("Nbre de chargements",'VARCHAR 30','text','chargements')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un vigik",
                'page' => "templates/detail",
                'menu' => "Produits|vigik",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newvigik',
                'methode' => 'detail',
              //  'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );

            $this->my_set_display_response($ajax,$data);
        }
    }
	 public function societe_detail($id,$ajax=false) {
		
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
			
            $valeurs = $this->m_newvigik->societe_detail($id);

            // commandes globales
            $cmd_globales = array(
            //    array("Articles",'articles/articles_cat','default'),
            //    array("Import articles",'newvigik/importation','default'),
            //    array("Export articles",'newvigik/exportation','default')
            );

            // commandes locales
            $cmd_locales = array(
                array("Modifier",'newvigik/modification','primary',($valeurs->cat_etat == 'futur')),
                array("Supprimer",'newvigik/suppression','danger',($valeurs->cat_etat == 'futur'))
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'scv_nom' => array("Nom",'VARCHAR 30','text','Nom')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un vigik",
                'page' => "templates/detail",
                'menu' => "Produits|vigik",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newvigik',
                'methode' => 'societe_detail',
                'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }

    /******************************
    * Mise à jour d'un vigik
    ******************************/
    public function modification($id=0,$ajax=false) {
        
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            
			array('field'=>'ctc_numero','label'=>"Numero",'rules'=>'trim|required'),

            array('field'=>'ctc_borne','label'=>"Borne",'rules'=>'trim|required'),

            array('field'=>'ctc_societe','label'=>"Societe",'rules'=>'trim|required'),

            array('field'=>'ctc_type','label'=>"Type",'rules'=>'trim|required')

        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie

            $valeurs = array(		
				
                'vigik_numero' => $this->input->post('ctc_numero'),

                'borne' => $this->input->post('ctc_borne'),

                'societe' => $this->input->post('ctc_societe'),

                'type' => $this->input->post('ctc_type'),

                'client' => $this->input->post('ctc_client'),

                'facture' => $this->input->post('ctc_facture'),

                'devis' => $this->input->post('ctc_devis'),
				
                'etat' => $this->input->post('ctc_etat'),

                'cl_adresse' => $this->input->post('ctc_adresse'),

                'tournee' => $this->input->post('ctc_tournee'),

                'ouvertures' => $this->input->post('ctc_ouvertures'),

                'chargements' => $this->input->post('ctc_chargements')

            );

           

            $id = $this->m_newvigik->vigik_editform($valeurs,$id);

                $this->my_set_action_response($ajax,true,"Le VIGIK a été créé");

                if ($ajax) {
                    return;
                }
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);

        }
		else{			
            // validation en échec ou premier appel : affichage du formulaire
			
            $valeurs = $this->m_newvigik->edit_detail($id);
			
            
			if($this->input->post('ctc_numero')!=""){
            $valeurs->ctc_numero = $this->input->post('ctc_numero');
			}
			else{
			$valeurs->ctc_numero = $valeurs->vigik_numero;
			}
			
			if($this->input->post('ctc_borne')!=""){
            $valeurs->ctc_borne = $this->input->post('ctc_borne');
			}
			else{
			$valeurs->ctc_borne = $valeurs->borne;
			}
			
			if($this->input->post('ctc_societe')!=""){
            $valeurs->ctc_societe = $this->input->post('ctc_societe');
			}
			else{
			$valeurs->ctc_societe = $valeurs->societe;
			}
			
			if($this->input->post('ctc_type')!=""){
            $valeurs->ctc_type = $this->input->post('ctc_type');
			}
			else{
			$valeurs->ctc_type = $valeurs->type;
			}
			
			
			if($this->input->post('ctc_client')!=""){
            $valeurs->ctc_client = $this->input->post('ctc_client');
			}
			else{
			$valeurs->ctc_client = $valeurs->client;
			}
			if($this->input->post('ctc_facture')!=""){
            $valeurs->ctc_facture = $this->input->post('ctc_facture');
			}
			else{
			$valeurs->ctc_facture = $valeurs->facture;
			}
			if($this->input->post('ctc_devis')!=""){
            $valeurs->ctc_devis = $this->input->post('ctc_devis');
			}
			else{
			$valeurs->ctc_devis = $valeurs->devis;
			}
			if($this->input->post('ctc_etat')!=""){
            $valeurs->ctc_etat = $this->input->post('ctc_etat');
			}
			else{
			$valeurs->ctc_etat = $valeurs->etat;
			}
			if($this->input->post('ctc_adresse')!=""){
            $valeurs->ctc_adresse = $this->input->post('ctc_adresse');
			}
			else{
			$valeurs->ctc_adresse = $valeurs->cl_adresse;
			}
			if($this->input->post('ctc_tournee')!=""){
            $valeurs->ctc_tournee = $this->input->post('ctc_tournee');
			}
			else{
			$valeurs->ctc_tournee = $valeurs->tournee;
			}
			if($this->input->post('ctc_ouvertures')!=""){
            $valeurs->ctc_ouvertures = $this->input->post('ctc_ouvertures');
			}
			else{
			$valeurs->ctc_ouvertures = $valeurs->ouvertures;
			}
			if($this->input->post('ctc_chargements')!=""){
            $valeurs->ctc_chargements = $this->input->post('ctc_chargements');
			}
			else{
			$valeurs->ctc_chargements = $valeurs->chargements;
			}
			
			$soc_id = $this->m_newvigik->society_list($valeurs->ctc_societe);
			$born_id = $this->m_newvigik->bornes_list($valeurs->ctc_borne);
			$adresse_id = $this->m_newvigik->adresse_list($valeurs->ctc_adresse);
			$tournee_id = $this->m_newvigik->tournee_list($valeurs->ctc_tournee);
			
           ////
		   $client_name = $this->m_newvigik->client_list($valeurs->ctc_client);
		   $facture_name= $this->m_newvigik->client_facture($valeurs->ctc_client,$valeurs->ctc_facture);
		   $devis_name = $this->m_newvigik->client_devis($valeurs->ctc_client,$valeurs->ctc_devis);
		    $adresse_name = $this->m_newvigik->client_adresse($valeurs->ctc_client,$valeurs->ctc_adresse);
     	   ///
		   


            $data = array(

                'title' => "Mise à jour d'un VIGIK",
				
                'soc_id' => $soc_id,
				
                'born_id' => $born_id,
				
                'client_name' => $client_name,
				
                'tournee_id' => $tournee_id,
				
                'adresse_id' => $adresse_id,
				
                'cl_address' => $valeurs->ctc_adresse,
				
                'facture_name' => $facture_name,
				
                'devis_name' => $devis_name,
                'adresse_name' => $adresse_name,

                'page' => "vigiks/vigikform",

                'menu' => "Extra|Mise à jour de VIGIK",

                'barre_action' => $this->barre_action["Element"],
                
                'id' => $id,
				
                'values' => $valeurs,

                'action' => "modif",

                'multipart' => false,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newvigik',

                'methode' => 'modification'
				

            );

            $this->my_set_form_display_response($ajax,$data);
		}
		
       
    }

    /******************************
    * Suppression d'un vigik
    ******************************/
    public function suppression($id,$ajax=false) {
		
      /*  $data = $this->m_newvigik->detail($id);
        if (! ($data->cat_etat == 'futur')) {
            $this->session->set_flashdata("danger","Opération non autorisée");
            redirect();
        }*/
        $resultat = $this->m_newvigik->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"Le VIGIK a été supprimé");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Exportation d'un vigik
     ******************************/
    public function exportation($id) {
		
		$this->load->library('excel');
		$data = $this->m_newvigik->exportation($id);
		
     	
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Add some data



	$objPHPExcel->getActiveSheet()->insertNewRowBefore(1,1);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Numero')
	                              ->setCellValue('B1', "Borne")
	                              ->setCellValue('C1', "Societe")
	                              ->setCellValue('D1', "Type")
	                              ->setCellValue('E1', "Client")							  
	                              ->setCellValue('F1', "Facture")							  
	                              ->setCellValue('G1', "Devis")								  
	                              ->setCellValue('G1', "Etat")						  
	                              ->setCellValue('H1', "Adresse")				  
	                              ->setCellValue('I1', "Tournee");
$baseRow = 2;								  
foreach($data as $r => $dataRow) {
	
	$row = $baseRow + $r;
$adresse=$dataRow['adresse_numero']."-".$dataRow['rue']."-".$dataRow['ville']."-".$dataRow['code'];

	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['vigik_numero'])
	                              ->setCellValue('B'.$row, $dataRow['borne_numero'])
	                              ->setCellValue('C'.$row, $dataRow['societe'])
	                              ->setCellValue('D'.$row, $dataRow['type'])
	                              ->setCellValue('E'.$row, $dataRow['client'])
	                              ->setCellValue('F'.$row, $dataRow['fac_reference'])
	                              ->setCellValue('G'.$row, $dataRow['dvi_reference'])
	                              ->setCellValue('G'.$row, $dataRow['etat'])
	                              ->setCellValue('H'.$row, $adresse)								  
	                              ->setCellValue('I'.$row, $dataRow['tournee_numero']);
								  
}

// Miscellaneous glyphs, UTF-8

// Rename worksheet

$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);;
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="vigik_'.time().'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
 $objWriter->save('tmp/vigik_'.time().'.xls');
exit;
    
    }
	public function exportpdf($id) {
		 $this->load->library('pdfConcat');
    	$data = $this->m_newvigik->exportation($id);
      //  require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 007');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 007', PDF_HEADER_STRING);
$pdf->SetHeaderData('', '2', 'Vigik','');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins

//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins('7', '20', '7');
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// set font


// add a page


// create columns content

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// get current vertical position
$y = $pdf->getY();
$x = $pdf->getX();

// set color for background


// set color for text
//$pdf->SetTextColor(0, 63, 127);

// write the first columns
$pdf->AddPage();
$pdf->SetFont('times', '', 12);
$pdf->SetFillColor(255, 255, 255);
$pdf->writeHTMLCell(20, 10, '', '', '<b>Numero</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(20, 10, '', '', '<b>Borne</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(30, 10, '', '', '<b>Societe</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(15, 10, '', '', '<b>Type</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(25, 10, '', '', '<b>Client</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(13, 10, '', '', '<b>Facture</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(13, 10, '', '', '<b>Devis</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(13, 10, '', '', '<b>Etat</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(25, 10, '', '', '<b>Adresse</b>', 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(20, 10, '', '', '<b>Tournee</b>', 1, 1, 1, true, 'J', true);

foreach($data as $r => $dataRow) {
	
$adresse=$dataRow['adresse_numero']."-".$dataRow['rue']."-".$dataRow['ville']."-".$dataRow['code'];
$pdf->SetFont('times', '', 10);
$pdf->SetFillColor(255, 255, 255);
$pdf->writeHTMLCell(20, 20, '', '', trim($dataRow['vigik_numero']), 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(20, 20, '', '', $dataRow['borne_numero'], 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 9);
$pdf->writeHTMLCell(30, 20, '', '', strtolower($dataRow['societe']), 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 10);
$pdf->writeHTMLCell(15, 20, '', '', $dataRow['type'], 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 9);
$pdf->writeHTMLCell(25, 20, '', '', strtolower(trim($dataRow['client'])), 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 8);
$pdf->writeHTMLCell(13, 20, '', '', $dataRow['fac_reference'], 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(13, 20, '', '', $dataRow['dvi_reference'], 1, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(13, 20, '', '', $dataRow['etat'], 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 8);
$pdf->writeHTMLCell(25, 20, '', '', strtolower(trim($adresse)), 1, 0, 1, true, 'J', true);
$pdf->SetFont('times', '', 10);
$pdf->writeHTMLCell(20, 20, '', '', strtolower(trim($dataRow['tournee_numero'])), 1, 1, 1, true, 'J', true);
$pdf->SetFont('times', '', 10);
							  
}


$pdf->lastPage();	

// set color for background
//$pdf->SetFillColor(215, 235, 255);

// set color for text
//$pdf->SetTextColor(127, 31, 0);



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('vigik_'.time().'.pdf', 'D');
//$pdf->Output(FCPATH.'tmp\vigik_'.time().'.pdf', 'F');

  
//============================================================+
// END OF FILE
//============================================================+

	}

    /******************************
     * Importation d'un vigik
     ******************************/
    public function importation($id) {
        $this->load->helper(array('form','ctrl'));
        $resultat = false;
        if (array_key_exists('vigik',$_FILES)){
            $f = $_FILES['vigik'];
            if ($f['error'] == 0) {
                $extension = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'].$extension;
                rename($f['tmp_name'],$nom_fichier);
                $resultat = $this->m_newvigik->importation($id,$nom_fichier);
                if ($resultat === false) {
                    $this->session->set_flashdata('danger','Un problème technique est survenu - veuillez reessayer ultérieurement');
                }
                elseif ($resultat === true) {
                    $this->session->set_flashdata('success','Le vigik a été chargé');
                    redirect('newvigik/detail/'.$id);
                }
            }
            else {
                switch($f['error']) {
                    case 1:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 2:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 3:
                        $erreur = 'Le fichier '.$f['name']." n'a été que partiellement téléchargé.";
                        break;
                    case 4:
                        if (file_exists($f['tmp_name'])) {
                            unlink($f['tmp_name']);
                            $erreur = "Un fichier de même nom existait. Veuillez recommencer.";
                        }
                        else {
                            $erreur = "Vous n'avez pas désigné le fichier";
                        }
                        break;
                    case 7:
                        $erreur = 'Le fichier '.$f['name']." n'a pu être enregistré.";
                        break;
                    default:
                        $erreur = 'Erreur lors du téléchargement du fichier '.$f['name'];
                }
                $this->session->set_flashdata('danger',$erreur);
            }
        }
        $data = array(
            'title' => "Importation de vigik",
            'page' => "newvigik/importation",
            'menu' => "Produits|Importation de vigik",
            'values' => array (
                'resultat' => $resultat
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}

// EOF
