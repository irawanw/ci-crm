<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Newadresse extends MY_Controller {
    private $profil;
    private $barre_action = array(
        "Liste" => array(
            array(
                    "Nouveau" => array('newadresse/create','plus',true,'adresse_nouveau',null,array('form')),
            ),
            array(
                    "Consulter" => array('newadresse/detail','eye-open',false,'adresse_detail',null,array('view')),
                    "Modifier" => array('newadresse/modification','pencil',false,'adresse_modification',null,array('form')),
                    "Supprimer" => array('newadresse/suppression','trash',false,'adresse_supprimer',"Veuillez confirmer la suppression de l'adresse",array('confirm-modify')),
            ),
           /* array(
                    "Export Excel" => array('#','list-alt',false,'export_excel'),
					"Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),*/
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
                    "Consulter" => array('newadresse/detail','eye-open',true,'adresse_detail',null,array('view')),
                    "Modifier" => array('newadresse/modification','pencil',true,'tournee_modification',null,array('form')),
                    "Supprimer" => array('newadresse/suppression','trash',true,'tournee_supprimer',"Veuillez confirmer la suppression de l'adresse",array('confirm-modify')),
            ),
            array(
                    "Export PDF" => array('#','book',false,'export_pdf'),
                    "Impression" => array('#','print',false,'impression')
            )
        )
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_newadresse');
    }

    /******************************
    * Liste des newadresse
    ******************************/
    public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une adresse","newadresse/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newadresse/index',
            'detail' => array('newadresse/detail','adresse_id','adresse_numero'),
            'champs' => array(
                array('sno','text',"S.No"),
                array('adresse_nom','text',"Nom"),
                array('adresse_numero','text',"Numero"),
    			array('rue','text',"Rue"),
    			array('type_voie','text',"Type de voie"),
				array('ville','text',"Ville"),
    			array('code','text',"Code"),				
				array('tournee_numero','ref',"Tournee",'t_tourneevigik'),	
				array('ordre_tournee','text',"Ordre dans la tournée"),			
				array('ctc_nom','ref',"Client",'t_contacts'),
				array('heure_de_livraison','text',"Heure de livraison"),
    			array('type_de_livraison','text',"Type de livraison"),
				array('horaires_de_livraison','text',"Horaires de livraison"),			
    			array('contact','text',"Contact"),				
				array('telephone_contact','text',"Telephone du contact"),	
    			array('derniere_facture','text',"Derniere facture"),	
    			array('derniere_facture_impayee','text',"Derniere facture impayee"),	
    			array('avant_derniere','text',"Avant derniere facture impayee"),	
    			array('bloque','text',"Bloque")	
				
            ),
            'filterable_columns' => $this->m_newadresse->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("vigiks/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'newadresse',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newadresse',$this->session->id);
		
        $data = array(
            'title' => "Liste des Adresse",
            'page' => "vigiks/datatables",
            'menu' => "Vigik|Adresse",
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
            $resultat = $this->m_newadresse->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
		
            $tables = array(
                'adresse_numero' => 't_adressevigik'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newadresse->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newadresse->liste($id,$pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }


	
	public function create($id=0,$ajax=false) {
		
      $cmd_globales = array(
        //    array("Nouvelle Adresse","newadresse/create",'default')
        );
        $this->load->helper(array('form','ctrl'));

        $this->load->library('form_validation');


        // règles de validation

        $config = array(

            array('field'=>'adr_numero','label'=>"Numero",'rules'=>'trim|required'),
            array('field'=>'adr_rue','label'=>"Rue",'rules'=>'trim|required'),
            array('field'=>'adr_ville','label'=>"Ville",'rules'=>'trim|required'),
            array('field'=>'adr_code','label'=>"Code Postal",'rules'=>'trim|required')
			

        );



        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

          

            // validation réussie

            $valeurs = array(

                'adresse_numero' => $this->input->post('adr_numero'),
                'rue' => $this->input->post('adr_rue'),
                'type_voie' => $this->input->post('adr_voie'),
                'ville' => $this->input->post('adr_ville'),
                'code' => $this->input->post('adr_code'),
                'tournee' => $this->input->post('adr_tournee'),
                'ordre_tournee' => $this->input->post('adr_ordretournee'),
                'client' => $this->input->post('adr_client'),
                'heure_de_livraison' => $this->input->post('adr_heure'),
                'type_de_livraison' => $this->input->post('adr_livraison'),
                'horaires_de_livraison' => $this->input->post('adr_horaires'),				
                'contact' => $this->input->post('adr_contact'),
                'telephone_contact' => $this->input->post('adr_telcontact'),
                'derniere_facture' => $this->input->post('adr_derniere'),
                'derniere_facture_impayee' => $this->input->post('adr_impayee'),
                'avant_derniere' => $this->input->post('adr_avant'),
                'bloque' => $this->input->post('adr_bloque')

            );

               $id = $this->m_newadresse->form($valeurs);

                $this->my_set_action_response($ajax,true,"L'adresse a été créée");
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

            $valeurs->adr_numero = $this->input->post('adr_numero');
            $valeurs->adr_rue = $this->input->post('adr_rue');
            $valeurs->adr_voie = $this->input->post('adr_voie');
            $valeurs->adr_ville = $this->input->post('adr_ville');
            $valeurs->adr_code = $this->input->post('adr_code');
            $valeurs->adr_tournee = $this->input->post('adr_tournee');
            $valeurs->adr_ordretournee = $this->input->post('adr_ordretournee');
            $valeurs->adr_client = $this->input->post('adr_client');
            $valeurs->adr_heure = $this->input->post('adr_heure');
            $valeurs->adr_livraison = $this->input->post('adr_livraison');
            $valeurs->adr_horaires = $this->input->post('adr_horaires');
			
			
            $valeurs->adr_contact = $this->input->post('adr_contact');
            $valeurs->adr_telcontact = $this->input->post('adr_telcontact');
            $valeurs->adr_derniere = $this->input->post('adr_derniere');
            $valeurs->adr_impayee = $this->input->post('adr_impayee');
            $valeurs->adr_avant = $this->input->post('adr_avant');
            $valeurs->adr_bloque = $this->input->post('adr_bloque');
            
            $tournee_id = $this->m_newadresse->tournee_list($valeurs->adr_tournee);
			
            $client_id = $this->m_newadresse->client_list($valeurs->adr_client);
 

            $data = array(

                'title' => "Ajouter une Adresse",
				'id' => 0,
				
                'tournee_id' => $tournee_id,
                'client_id' => $client_id,

                'page' => "vigiks/adresseform",

                'menu' => "Vigik|Adresse",

                //'barre_action' => $this->barre_action["Element"],

                'values' => $valeurs,

                'action' => "create",

                'multipart' => false,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newadresse',

                'methode' => 'create'

            );

            $this->my_set_form_display_response($ajax,$data);
		}

    }

	public function ajaxclient() {

	  if (! $this->input->is_ajax_request()) die('');

	       $dt=$this->input->post('id');   

		   $derniere= $this->m_newadresse->client_derniere($dt,0);
		   $impayee = $this->m_newadresse->client_impayee($dt,0);
		   $avant = $this->m_newadresse->client_avant($dt,0);
		   //$impayee="";
		   //$avant="";
		   
		   $data = array(
                'client' => $dt,
                'derniere' => $derniere,
                'impayee' => $impayee,
                'avant' => $avant                   

            );

           return $this->load->view("vigiks/ajax/adrvigik_client",$data);

  }

    /******************************
    * Détail d'un tournee
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_newadresse->detail($id);

            // commandes globales
          /*  $cmd_globales = array(
                array("Articles",'articles/articles_cat','default'),
                array("Import articles",'newadresse/importation','default'),
                array("Export articles",'newadresse/exportation','default')
            );
			*/

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'newadresse/modification','primary'),
            //    array("Supprimer",'newadresse/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'adresse_numero' => array("Numero",'VARCHAR 30','text','adresse_numero'),
				   'rue' => array("Rue",'VARCHAR 30','text','rue'),
				   'type_voie' => array("Type de voie",'VARCHAR 30','text','type_voie'),
                   'ville' => array("Ville",'VARCHAR 30','text','ville'),
				   'code' => array("Code Postal",'VARCHAR 30','text','code'),
                   'tournee' => array("Tournee",'VARCHAR 30','text','tnumero'),
                   'ordre_tournee' => array("Tournee",'VARCHAR 30','text','ordre_tournee'),
				   'client' => array("Client nom",'VARCHAR 30','text','ctc_nom'),
                   'heure_de_livraison' => array("Heure de livraison",'VARCHAR 30','text','heure_de_livraison'),
                   'type_de_livraison' => array("Type de livraison",'VARCHAR 30','text','type_de_livraison'),
                   'horaires_de_livraison' => array("Horaires de livraison",'VARCHAR 30','text','horaires_de_livraison'),
				   'contact' => array("Contact",'VARCHAR 30','text','contact'),
				   'telephone_contact' => array("Telephone du contact",'VARCHAR 30','text','telephone_contact'),
				   'derniere_facture' => array("Derniere facture",'VARCHAR 30','text','derniere_facture'),
                   'derniere_facture_impayee' => array("Derniere facture impayee",'VARCHAR 30','text','derniere_facture_impayee'),
				   'avant_derniere' => array("Avant derniere facture impayee",'VARCHAR 30','text','avant_derniere'),
                   'bloque' => array("Bloque",'VARCHAR 30','text','bloque')
				   
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un Adresse",
                'page' => "templates/detail",
                'menu' => "Vigik|Adresse",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newadresse',
                'methode' => 'detail',
               // 'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }
	 /******************************
    * Mise à jour d'un tournee
    ******************************/
    public function modification($id=0,$ajax=false) {
        
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(

            array('field'=>'adr_numero','label'=>"Numero",'rules'=>'trim|required'),
            array('field'=>'adr_rue','label'=>"Rue",'rules'=>'trim|required'),
            array('field'=>'adr_ville','label'=>"Ville",'rules'=>'trim|required'),
            array('field'=>'adr_code','label'=>"Code Postal",'rules'=>'trim|required')
            

        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie

            $valeurs = array(		
				'adresse_numero' => $this->input->post('adr_numero'),
                'rue' => $this->input->post('adr_rue'),
                'type_voie' => $this->input->post('adr_voie'),
                'ville' => $this->input->post('adr_ville'),
                'code' => $this->input->post('adr_code'),
                'tournee' => $this->input->post('adr_tournee'),
                'ordre_tournee' => $this->input->post('adr_ordretournee'),
                'client' => $this->input->post('adr_client'),
                'heure_de_livraison' => $this->input->post('adr_heure'),
                'type_de_livraison' => $this->input->post('adr_livraison'),
                'horaires_de_livraison' => $this->input->post('adr_horaires'),
                'contact' => $this->input->post('adr_contact'),
                'telephone_contact' => $this->input->post('adr_telcontact'),
                'derniere_facture' => $this->input->post('adr_derniere'),
                'derniere_facture_impayee' => $this->input->post('adr_impayee'),
                'avant_derniere' => $this->input->post('adr_avant'),
                'bloque' => $this->input->post('adr_bloque')

            );

           

            $id = $this->m_newadresse->editform($valeurs,$id);

                $this->my_set_action_response($ajax,true,"L'adresse a été modifiée");
                if ($ajax) {
                    return;
                }
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);

        }
        else {

           $valeurs = $this->m_newadresse->edit_detail($id);
			
            
			if($this->input->post('adr_numero')!=""){
            $valeurs->adr_numero = $this->input->post('adr_numero');
			}
			else{
			$valeurs->adr_numero = $valeurs->adresse_numero;
			}			
			if($this->input->post('adr_rue')!=""){
            $valeurs->adr_rue = $this->input->post('adr_rue');
			}
			else{
			$valeurs->adr_rue = $valeurs->rue;
			}
			if($this->input->post('adr_voie')!=""){
            $valeurs->adr_voie = $this->input->post('adr_voie');
			}
			else{
			$valeurs->adr_voie = $valeurs->type_voie;
			}
			if($this->input->post('adr_ville')!=""){
            $valeurs->adr_ville = $this->input->post('adr_ville');
			}
			else{
			$valeurs->adr_ville = $valeurs->ville;
			}
			if($this->input->post('adr_code')!=""){
            $valeurs->adr_code = $this->input->post('adr_code');
			}
			else{
			$valeurs->adr_code = $valeurs->code;
			}
			if($this->input->post('adr_tournee')!=""){
            $valeurs->adr_tournee = $this->input->post('adr_tournee');
			}
			else{
			$valeurs->adr_tournee = $valeurs->tournee;
			}
			if($this->input->post('adr_ordretournee')!=""){
            $valeurs->adr_ordretournee = $this->input->post('adr_ordretournee');
			}
			else{
			$valeurs->adr_ordretournee = $valeurs->ordre_tournee;
			}
			if($this->input->post('adr_client')!=""){
            $valeurs->adr_client = $this->input->post('adr_client');
			}
			else{
			$valeurs->adr_client = $valeurs->client;
			}
			if($this->input->post('adr_heure')!=""){
            $valeurs->adr_heure = $this->input->post('adr_heure');
			}
			else{
			$valeurs->adr_heure = $valeurs->heure_de_livraison;
			}
			if($this->input->post('adr_livraison')!=""){
            $valeurs->adr_livraison = $this->input->post('adr_livraison');
			}
			else{
			$valeurs->adr_livraison = $valeurs->type_de_livraison;
			}
			if($this->input->post('adr_horaires')!=""){
            $valeurs->adr_horaires = $this->input->post('adr_horaires');
			}
			else{
			$valeurs->adr_horaires = $valeurs->horaires_de_livraison;
			}
			if($this->input->post('adr_contact')!=""){
            $valeurs->adr_contact = $this->input->post('adr_contact');
			}
			else{
			$valeurs->adr_contact = $valeurs->contact;
			}
			if($this->input->post('adr_telcontact')!=""){
            $valeurs->adr_telcontact = $this->input->post('adr_telcontact');
			}
			else{
			$valeurs->adr_telcontact = $valeurs->telephone_contact;
			}
			if($this->input->post('adr_derniere')!=""){
            $valeurs->adr_derniere = $this->input->post('adr_derniere');
			}
			else{
			$valeurs->adr_derniere = $valeurs->derniere_facture;
			}
			if($this->input->post('adr_impayee')!=""){
            $valeurs->adr_impayee = $this->input->post('adr_impayee');
			}
			else{
			$valeurs->adr_impayee = $valeurs->derniere_facture_impayee;
			}
			if($this->input->post('adr_avant')!=""){
            $valeurs->adr_avant = $this->input->post('adr_avant');
			}
			else{
			$valeurs->adr_avant = $valeurs->avant_derniere;
			}
			if($this->input->post('adr_bloque')!=""){
            $valeurs->adr_bloque = $this->input->post('adr_bloque');
			}
			else{
			$valeurs->adr_bloque = $valeurs->bloque;
			} 
			
		
			$tournee_id = $this->m_newadresse->tournee_list($valeurs->adr_tournee);
			$client_id = $this->m_newadresse->client_list($valeurs->adr_client);


                $data = array(
                'title' => "Mise à jour d'un Adresse",				
                'tournee_id' => $tournee_id,			
                'client_id' => $client_id,
                'page' => "vigiks/adresseform",
                'menu' => "Vigik|Adresse",
                'barre_action' => $this->barre_action["Element"],
                'id' => $id,
				
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newadresse',
                'methode' => 'modification'
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un tournee
    ******************************/
    public function suppression($id,$ajax=false) {
		
      /*  $data = $this->m_newadresse->detail($id);
        if (! ($data->cat_etat == 'futur')) {
            $this->session->set_flashdata("danger","Opération non autorisée");
            redirect();
        }*/
        $resultat = $this->m_newadresse->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"La tournée a été supprimée");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    
}

// EOF
