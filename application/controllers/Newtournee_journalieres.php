<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class Newtournee_journalieres extends MY_Controller {
    private $profil;
    private $barre_action = array(
       "Liste" => array(
            array(
                    "Nouveau" => array('*newtournee_journalieres/create','plus',true,'newtournee_journalieres_nouveau'),
            ),
            array(
                    "Tournées" => array('*newtournee_journalieres/listnew[]','calendar',false,'tourneejourn_tournees'),
            ),
            array(
                    "Consulter" => array('newtournee_journalieres/detail','eye-open',false,'bornes_detail',null,array('view')),
                    "Modifier" => array('newtournee_journalieres/modification','pencil',false,'newtournee_journalieres_modification',null,array('form')),
                    "Supprimer" => array('newtournee_journalieres/suppression','trash',false,'newtournee_journalieres','Confirmez la suppression de la tournée journalière', array('confirm-modify')),
            ),
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
        "Liste_2" => array(
            array(
                    "Nouveau" => array('*newtournee_journalieres/create','plus',true,'tournee_journalieres_nouveau'),
            ),
            array(
                // ATTENION !!!
                // Si vous modifier le texte de ces boutons, il faudra aussi modifier la méthode Newtournee_journalieres::listnew()
                    "Valider" => array('newtournee_journalieres/validate','ok',false,'valider_modify','Vous modifiez la de tournee journalieres',array('confirm-action')),
                    "Invalider" => array('newtournee_journalieres/devalidate','remove',false,'devalider_modify','Vous demodifiez la de tournee journalieres',array('confirm-action')),
            ), 
            array(
                    "Export PDF" => array('newtournee_journalieres/exportation','book',true,'export_excel'),
					"Export Detail PDF" => array('newtournee_journalieres/export_detail','book',true,'exportdetail_pdf'),
                    "Impression" => array('#','print',false,'impression')
            ),
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
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_newtournee_journalieres');
    }

    /******************************
    * Liste des newtournee_journalieres
    ******************************/
	  public function index($id=0,$liste=0) {

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une bornes","newtournee_journalieres/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newtournee_journalieres/index',
            'detail' => array('newtournee_journalieres/detail','tourneejouern_id','tourneejouern_numero'),
            'champs' => array(
                array('sno','text',"S.No</br>&nbsp;"),	
				array('tournee_nom','ref',"Nom de la tournee</br>&nbsp;","t_tourneevigik"),					
                array('tourneejouern_date','text',"Date de la tournee</br>&nbsp;"),
				array('tourneejouern_numero','text',"Numero de la tournee</br>&nbsp;"),
				array('tourneejouern_livreur','text',"Livreur</br>&nbsp;")
				
            ),
            'filterable_columns' => $this->m_newtournee_journalieres->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("vigiks/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'newtournee_journalieres',
                'methode' => 'index'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newtournee_journalieres',$this->session->id);
		
        $data = array(
            'title' => "liste des tournées journalières",
            'page' => "vigiks/datatables",
            'menu' => "Vigik|liste des tournées journalières",
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
            $resultat = $this->m_newtournee_journalieres->index_liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tourneejouern_numero' => 't_tourneejouern'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newtournee_journalieres->index_liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newtournee_journalieres->index_liste($id,$pagelength, $pagestart, $filters);
            }
        }
		

if (!empty($resultat['data'])) {
	

$json = array();
$i=0;	
foreach($resultat['data'] as $row){
	$i++;
	   	
        $json['sno'] = $i;	
	    $json['tourneejouern_id'] = $row->tourneejouern_id;			
	    $json['tournee_nom'] = $row->tournee_nom;		
	    $json['tourneejouern_date'] = $row->tourneejouern_date;		
	    $json['tourneejouern_numero'] = $row->tourneejouern_numero;		
	    $json['tourneejouern_livreur'] = $row->tourneejouern_livreur;	
		
        $data[] = (object)$json;
}  
$resultat['data']=$data;
}		


		
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }
	public function sel_adresse() {

	  if (! $this->input->is_ajax_request()) die('');

	       $dt=$this->input->post('id');   
		   $data = array(		   
                'aid' => $dt
            );
           return $this->load->view("vigiks/ajax/journaliere_adresse",$data);

  }

    public function listnew($id=0,$liste=0) {
		
       $journ_date=$this->m_newtournee_journalieres->string_date($id);

        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une bornes","newtournee_journalieres/create",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource' => 'newtournee_journalieres/listnew',
            'detail' => array('newtournee_journalieres/detail','tourneejouern_id','tourneejouern_numero'),
            'champs' => array(
                array('sno','text',"Action </br>de masse"),	
                array('tourneejouern_resultat','text',"Résultat</br>de la livraison"),
                array('adresse_nom','text',"Nom</br>&nbsp;"),
				array('adresse_numero','text',"Numero</br>&nbsp;"),
    			array('rue','text',"Rue</br>&nbsp;"),
    			array('type_voie','text',"Type </br>de voie"),
				array('ville','text',"Ville</br>&nbsp;"),
    			array('code','text',"Code</br>&nbsp;"),				
				array('tournee_numero','ref',"Tournee</br>&nbsp;",'t_tourneevigik'),	
				array('ordre_tournee','text',"Ordre dans</br> la tournée"),			
				array('ctc_nom','ref',"Client</br>&nbsp;",'t_contacts'),
				array('heure_de_livraison','text',"Heure</br> de livraison"),
    			array('type_de_livraison','text',"Type </br>de livraison"),
				array('horaires_de_livraison','text',"Horaires </br>de livraison"),			
    			array('contact','text',"Contact</br>&nbsp;"),				
				array('telephone_contact','text',"Telephone</br> du contact"),	
    			array('derniere_facture','text',"Derniere facture</br>&nbsp;"),	
    			array('derniere_facture_impayee','text',"Derniere facture</br>&nbsp; impayee"),	
    			array('avant_derniere','text',"Avant derniere facture</br>&nbsp; impayee"),	
    			array('bloque','text',"Bloque</br>&nbsp;"),
				array('remarques','text',"Remarques</br>&nbsp;")
				
            ),
            'journ_date' =>  $journ_date[0],
			'journ_nom'  =>  $journ_date[2],
			'journ_id'  =>  $journ_date[3],
            'filterable_columns' => $this->m_newtournee_journalieres->liste_filterable_columns()
        );

        $this->session->set_userdata('_url_retour',current_url());
        $scripts = array();
        $scripts[] = $this->load->view("vigiks/datatables-js",
            array(
                'id'=>$id,
                'descripteur'=>$descripteur,
                'toolbar'=>$toolbar,
                'controleur' => 'newtournee_journalieres',
                'methode' => 'listnew'
            ),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('newtournee_journalieres',$this->session->id);

        if($journ_date[1]=="yes" || $journ_date[1]==""){
            $barre_action = modifie_etat_barre_action($this->barre_action["Liste_2"],'Invalider',true);
            $valider="yes";
        }
        else{
            $barre_action = modifie_etat_barre_action($this->barre_action["Liste_2"],'Valider',true);
            $valider="no";
        }



        $data = array(
            'title' => "liste des adresses de la tournée",
            'page' => "vigiks/datatables_journaliere",
            'menu' => "Vigik|liste des tournées journalières",
            'scripts' => $scripts,
            'barre_action' => $barre_action,
            'valider' => $valider,
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
	    public function listnew_json($id=0) {
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
            $resultat = $this->m_newtournee_journalieres->liste($id,$pagelength, $pagestart, $filters);
        }
        else {

            //list with requested ordering
            $order_col_id   = $order[0]['column'];
            $ordering       = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'tourneejouern_numero' => 't_tourneejouern'
            );
            if ( $order_col_id>=0 && $order_col_id<=count($columns)) {
                $order_col = $columns[$order_col_id]['data'];
                if ( empty($order_col) ) $order_col=2;
                if (isset($tables[$order_col])) $order_col = $tables[$order_col].'.'.$order_col;
                if ( !in_array($ordering, array("asc", "desc")) ) $ordering="asc";
                $resultat = $this->m_newtournee_journalieres->liste($id,$pagelength, $pagestart, $filters, $order_col, $ordering);
			  
            }
            else {
                $resultat = $this->m_newtournee_journalieres->liste($id,$pagelength, $pagestart, $filters);
            }
        }
		if (!empty($resultat['data'])) {
			

		$json = array();
		$i=0;	
		foreach($resultat['data'] as $row){
			$i++;
	   	
        $json['sno'] = "<input type='checkbox' name='tourn_journ_chk[]' id='tourn_journ_chk' class='checkbox' value='".$row->adresse_id."'>";

	    $json['tourneejouern_id'] = $row->adresse_id;			
	    $json['tourneejouern_resultat'] = $row->tourneejouern_resultat;		
	    $json['adresse_nom'] = $row->adresse_nom;	
	    $json['adresse_numero'] = $row->adresse_numero;		
	    $json['rue'] = $row->rue;		
	    $json['type_voie'] = $row->type_voie;		
	    $json['ville'] = $row->ville;		
	    $json['code'] = $row->code;
	    $json['tournee_numero'] = $row->tournee_numero;
	    $json['ordre_tournee'] = $row->ordre_tournee;
	    $json['ctc_nom'] = $row->ctc_nom;
	    $json['heure_de_livraison'] = $row->heure_de_livraison;
		
	    $json['type_de_livraison'] = $row->type_de_livraison;		
	    $json['horaires_de_livraison'] = $row->horaires_de_livraison;		
	    $json['contact'] = $row->contact;		
	    $json['telephone_contact'] = $row->telephone_contact;
	    $json['derniere_facture'] = $row->derniere_facture;
	    $json['derniere_facture_impayee'] = $row->derniere_facture_impayee;
	    $json['avant_derniere'] = $row->avant_derniere;
	    $json['bloque'] = $row->bloque;
	    $json['remarques'] = $row->remarques;		
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
                'tourneejouern_resultat' => $this->input->post('type')
				);
		$v_id=$this->input->post('id');
        $resultat = $this->m_newtournee_journalieres->mass_action($valeurs,$v_id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
        }
		public function validate($v_id,$ajax=false) {
			
				$type_mode="yes";
                $valeurs = array(
                'valider' => $type_mode
				);
        $resultat = $this->m_newtournee_journalieres->validate($valeurs,$v_id);			
			    $this->my_set_action_response($ajax,true,"La tournee journalieres validater confirmed ");
			    if ($ajax) {
			        return;
                }
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
        }		
		public function devalidate($v_id,$ajax=false) {
			
				$type_mode="no";
			
			
               $valeurs = array(
                'valider' => $type_mode
				);
        $resultat = $this->m_newtournee_journalieres->validate($valeurs,$v_id);			
			    $this->my_set_action_response($ajax,true,"La tournee journalieres devalidater confirmed ");
			    if ($ajax) {
			        return;
                }
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
        }		
	public function adresse_error($id=0) {
		 $this->session->set_flashdata('danger',"pour ajouter une adresse, vous devez dévalider la tournée journalière");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
	}
	public function modify_adresse_error($id=0) {
		if($id==0){
		        $this->session->set_flashdata('danger',"La tournee journalieres validater");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
		}
		else{
			    $this->session->set_flashdata('danger',"Choose List des Adresse");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);
			
		}
	}
	public function new_adresse($tour_id=0) {
     
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');
        // règles de validation

        $config = array(
            array('field'=>'sel_adresse','label'=>"Adresse",'rules'=>'trim|required')
        );



        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

            // validation réussie
            $valeurs = array(				
                'tournee' => $this->input->post('tournee_id')
            );
			$tourid=$this->input->post('sel_adresse');
            $id = $this->m_newtournee_journalieres->new_adresse($valeurs,$tourid);

                $this->session->set_flashdata('success',"Le Adresse a été added");
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);

        }

        else {

            // validation en échec ou premier appel : affichage du formulaire

            $valeurs = new stdClass();

            $listes_valeurs = new stdClass();

            $valeurs->sel_adresse = $this->input->post('sel_adresse');

            $valeurs->tournee_id = $tour_id;

            $adresse = $this->m_newtournee_journalieres->adresse_list($valeurs->sel_adresse);
			
			$scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tr_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            

 

            $data = array(

                'title' => "ajouter une Adresse",
				'id' => 0,
				
                'adresse' => $adresse,

                'page' => "vigiks/tourneejourn_adresseform",

                'menu' => "newtournee_journalieres|new_adresse",
				
                'scripts' => $scripts,

                'barre_action' => $this->barre_action["Liste"],

                'values' => $valeurs,

                'action' => "new_adresse",

                'multipart' => false,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newtournee_journalieres',

                'methode' => 'new_adresse'

               

            );

            $layout="layouts/standard";

            $this->load->view($layout,$data);

     

		}

    }
	public function create($id=0,$ajax=false) {
      $cmd_globales = array(
        //    array("Nouvelle tournées journalières","newtournee_journalieres/create",'default')
        );
        $this->load->helper(array('form','ctrl'));

        $this->load->library('form_validation');

     

        // règles de validation

        $config = array(

            array('field'=>'tr_nom','label'=>"Nom",'rules'=>'trim|required'),

            array('field'=>'tr_date','label'=>"Date",'rules'=>'trim|required')
            

        );



        // validation des fichiers chargés

        $validation = true;

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() && $validation) {

          

            // validation réussie
			$exist_id=$this->input->post('tr_existing_id');			
			$valeursdate = array(				
                'tourneejouern_date' => $this->input->post('tr_date')

            );

            $valeurs = array(

                'tourneejouern_nom' => $this->input->post('tr_nom'),

                'tourneejouern_numero' => $this->input->post('tr_numero'),

                'tourneejouern_livreur' => $this->input->post('tr_livreur'),
				
                'tourneejouern_date' => $this->input->post('tr_date')

            );
			
             

            $id = $this->m_newtournee_journalieres->tournjouern_form($valeurs,$exist_id,$valeursdate);

                $this->my_set_action_response($ajax,true,"La tournée journalière a été créée");
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

            $valeurs->tr_nom = $this->input->post('tr_nom');

            $valeurs->tr_numero = $this->input->post('tr_numero');

            $valeurs->tr_livreur = $this->input->post('tr_livreur');

            $valeurs->tr_date = $this->input->post('tr_date');
			
            $valeurs->tr_resultat = $this->input->post('tr_resultat');

            $tournee = $this->m_newtournee_journalieres->tournee_list($valeurs->tr_nom);
			
			$scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tr_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

            

 

            $data = array(

                'title' => "ajouter une tournées journalières",
				'id' => 0,
				
                'tournee' => $tournee,

                'page' => "vigiks/tournee_journalieresform",

                'menu' => "newtournee_journalieres|create",
				
                'scripts' => $scripts,

                //'barre_action' => $this->barre_action["Liste"],

                'values' => $valeurs,

                'action' => "create",

                'multipart' => false,

                'confirmation' => 'Enregistrer',

                'controleur' => 'newtournee_journalieres',

                'methode' => 'create'

               

            );
            $this->my_set_form_display_response($ajax,$data);

     

		}

    }
	public function ajaxtournee() {

	  if (! $this->input->is_ajax_request()) die('');

	       $dt=$this->input->post('id');   

		   $tournee_new= $this->m_newtournee_journalieres->tournee_ajxdetail($dt,0);
		  
		   //$impayee="";
		   //$avant="";
		   
            $tournee_nom = $this->m_newtournee_journalieres->tournee_list($dt);
			
		   
		   $data = array(		   
                'tournee' => $dt, 
                'tournee_journid' => $tournee_new[0], 
                'tournee_date' => $tournee_new[1], 
                'tournee_nom' => $tournee_nom,  
                'tournee_numero' => $tournee_new[3], 
                'tournee_livreur' => $tournee_new[4]           

            );
			 

           return $this->load->view("vigiks/ajax/tournee_journaliere",$data);

  }


    /******************************
    * Détail d'un bornes
    ******************************/
    public function detail($id,$ajax=false) {
        $this->load->helper(array('form','ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (! $redirection) $redirection = '';
            redirect($redirection);
        }
        else {
            $valeurs = $this->m_newtournee_journalieres->detail($id);

            // commandes globales
            $cmd_globales = array(
            //    array("Articles",'articles/articles_cat','default'),
            //    array("Import articles",'newtournee_journalieres/importation','default'),
            //    array("Export articles",'newtournee_journalieres/exportation','default')
            );

            // commandes locales
            $cmd_locales = array(
            //    array("Modifier",'newtournee_journalieres/modification','primary'),
            //    array("Supprimer",'newtournee_journalieres/suppression','danger')
            );

            // descripteur
            $descripteur = array(
                'champs' => array(
                   'tournee_nom' => array("Nom de la tournee",'VARCHAR 30','text','tournee_nom'),
                   'tourneejouern_numero' => array("Numero de la tournee",'VARCHAR 30','text','tourneejouern_numero'),
                   'tourneejouern_livreur' => array("Numero de la livreur",'VARCHAR 30','text','tourneejouern_livreur'),
                   'tourneejouern_date' => array("Date",'VARCHAR 30','text','tourneejouern_date'),
                   'tourneejouern_resultat' => array("Résultat de la livraison",'VARCHAR 30','text','tourneejouern_resultat')
                ),
				
                'onglets' => array(
                )
            );

            $data = array(
                'title' => "Détail d'un tournées journalières",
                'page' => "templates/detail",
                'menu' => "Produits|Tournées journalières",
                'barre_action' => $this->barre_action["Liste"],
                'id' => $id,
                'values' => $valeurs,
                'controleur' => 'newtournee_journalieres',
                'methode' => 'detail',
               // 'cmd_globales' => $cmd_globales,
                'cmd_locales' => $cmd_locales,
                'descripteur' => $descripteur
            );
            $this->my_set_display_response($ajax,$data);
        }
    }
	
    public function modification($id=0,$ajax=false) {
        
        $this->load->helper(array('form','ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            
			  array('field'=>'tr_nom','label'=>"nom",'rules'=>'trim|required'),

              array('field'=>'tr_date','label'=>"Date",'rules'=>'trim|required')
            

        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() AND $validation) {

            // validation réussie

            $valeurs = array(		
				
                'tourneejouern_nom' => $this->input->post('tr_nom'),

                'tourneejouern_numero' => $this->input->post('tr_numero'),

                'tourneejouern_livreur' => $this->input->post('tr_livreur'),
				
                'tourneejouern_date' => $this->input->post('tr_date')
               

            ); 
			
           

            $id = $this->m_newtournee_journalieres->tournjouern_editform($valeurs,$id);

                $this->my_set_action_response($ajax,true,"La tournée journalière a été modifiée");
                if ($ajax) {
                    return;
                }
                $redirection = $this->session->userdata('_url_retour');
                if (! $redirection) $redirection = '';
                redirect($redirection);

        }
        else {

            $valeurs = $this->m_newtournee_journalieres->edit_detail($id);
		   
            
			if($this->input->post('tr_nom')!=""){
            $valeurs->tr_nom = $this->input->post('tr_nom');
			}
			else{
			$valeurs->tr_nom = $valeurs->tourneejouern_nom;
			}
			
			if($this->input->post('tr_numero')!=""){
            $valeurs->tr_numero = $this->input->post('tr_numero');
			}
			else{
			$valeurs->tr_numero = $valeurs->tourneejouern_numero;
			}
			
			if($this->input->post('tr_livreur')!=""){
            $valeurs->tr_livreur = $this->input->post('tr_livreur');
			}
			else{
			$valeurs->tr_livreur = $valeurs->tourneejouern_livreur;
			}
			if($this->input->post('tr_date')!=""){
            $valeurs->tr_date = $this->input->post('tr_date');
			}
			else{
			$valeurs->tr_date = $valeurs->tourneejouern_date;
			}
			if($this->input->post('tr_resultat')!=""){
            $valeurs->tr_resultat = $this->input->post('tr_resultat');
			}
			else{
			$valeurs->tr_resultat = "";
			}
		
			$tournee = $this->m_newtournee_journalieres->tournee_list($valeurs->tr_nom);

           $scripts = array();
            $scripts[] = <<<'EOT'
<script>
    $(document).ready(function(){
        $("#tr_date").kendoDatePicker({format: "dd/MM/yyyy"});
    });
</script>
EOT;

                $data = array(
                'title' => "Mise à jour d'un tournées journalières",				
                'tournee' => $tournee,
                'page' => "vigiks/tournee_journalieresform",
                'menu' => "Vigik|Mise à jour de tournées journalières",
                'scripts' => $scripts,
                'barre_action' => $this->barre_action["Liste"],
                'id' => $id,
				
                'values' => $valeurs,
                'action' => "modif",
                'multipart' => false,
                'confirmation' => 'Enregistrer',
                'controleur' => 'newtournee_journalieres',
                'methode' => 'modification'
            );
            $this->my_set_form_display_response($ajax,$data);
        }
    }

    /******************************
    * Suppression d'un bornes
    ******************************/
    public function suppression($id,$ajax=false) {
		
      /*  $data = $this->m_newtournee_journalieres->detail($id);
        if (! ($data->cat_etat == 'futur')) {
            $this->session->set_flashdata("danger","Opération non autorisée");
            redirect();
        }*/
        $resultat = $this->m_newtournee_journalieres->suppression($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax,false);
        }
        else {
            $this->my_set_action_response($ajax,true,"La tournée journalière a été supprimée");
        }
        if ($ajax) {
            return;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (! $redirection) $redirection = '';
        redirect($redirection);
    }

    /******************************
     * Exportation d'un tournées journalières
     ******************************/
   public function exportation($id) {
	 $this->load->library('pdfConcat');
   	$data = $this->m_newtournee_journalieres->exportation($id);
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
$pdf->SetHeaderData('', '2', 'TOURNEES JOURNALIERES','');

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
$pdf->SetFont('times', '', 7);
$pdf->SetFillColor(255, 255, 255);


$html = '';
$ri=0;
foreach($data as $r => $dataRow) {
$ri++;
//$pdf->AddPage();

if( $dataRow['type_de_livraison'] !="boite_aux"){	
	$type_livraison="or mains propres";
}	
else{
	$type_livraison="boite aux lettres or sous la porte";
}
	
$adresse=$dataRow['adresse_numero']."-".$dataRow['rue']."-".$dataRow['type_voie']."-".$dataRow['ville'];



$html .= '<table>';
if($ri==1){
$html .= '<tr><td style="border:1px solid #ccc;font-weight:bold;"> Nom de la tournee</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Numero</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Livreur</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Date</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Client</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Contact</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Telephone du contact</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Type de livraison</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Adresse</td>
<td style="border:1px solid #ccc;font-weight:bold;"> Remarques</td></tr>';
}
$html .= '<tr><td style="border:1px solid #ccc;"> '.$dataRow['tournee_nom'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['tourneejouern_numero'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['tourneejouern_livreur'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['tourneejouern_date'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['ctc_nom'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['contact'].'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['telephone_contact'].'</td>
<td style="border:1px solid #ccc;"> '.$type_livraison.'</td>
<td style="border:1px solid #ccc;"> '.$adresse.'</td>
<td style="border:1px solid #ccc;"> '.$dataRow['remarques'].'</td></tr>';
$html .='</table>';
// output the HTML content
			  
}

$pdf->writeHTML($html, true, 0, true, 0);

$pdf->lastPage();	

// set color for background
//$pdf->SetFillColor(215, 235, 255);

// set color for text
//$pdf->SetTextColor(127, 31, 0);



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tournees_journalieres'.time().'.pdf', 'D');
//$pdf->Output(FCPATH.'tmp\vigik_'.time().'.pdf', 'F');

  
//============================================================+
// END OF FILE
//============================================================+

	}
	public function export_detail($id) {
	 $this->load->library('pdfConcat');
   	$data = $this->m_newtournee_journalieres->exportation($id);
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
$pdf->SetHeaderData('', '2', 'TOURNEES JOURNALIERES','');

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
//$pdf->AddPage();
$pdf->SetFont('times', '', 12);
$pdf->SetFillColor(255, 255, 255);

$html = '';

$pi=0;
foreach($data as $r => $dataRow) {
	$pi++;
$pdf->AddPage();
$html="";
if( $dataRow['type_de_livraison'] !="boite_aux"){	
	$type_livraison="or mains propres";
}	
else{
	$type_livraison="boite aux lettres or sous la porte";
}
if($pi=='1'){	
$adresse=$dataRow['adresse_numero']."-".$dataRow['rue']."-".$dataRow['type_voie']."-".$dataRow['ville'];
$html .= '<table style="width:80%;padding:15px;"><tr><td>Nom de la tournee</td><td>:'.$dataRow['tournee_nom'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Numero de la tournee</td><td>:'.$dataRow['tourneejouern_numero'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Livreur</td><td>:'.$dataRow['tourneejouern_livreur'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Date de la tournee</td><td>:'.$dataRow['tourneejouern_date'].'</td></tr></table>';
 } 
$html .= '<table style="width:80%;padding:15px;"><tr><td>Numero adresse</td><td>:'.$dataRow['adresse_numero'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Ordre dans la tournee</td><td>:'.$dataRow['ordre_tournee'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Bloque</td><td>:'.$dataRow['bloque'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Client</td><td>:'.$dataRow['ctc_nom'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Contact</td><td>:'.$dataRow['contact'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Telephone du contact</td><td>:'.$dataRow['telephone_contact'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Type de livraison</td><td>:'.$type_livraison.'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Adresse</td><td>:'.$adresse.'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Horaires de livraison</td><td>:'.$dataRow['horaires_de_livraison'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Remarques</td><td>:'.$dataRow['remarques'].'</td></tr></table>';
$html .= '<table style="width:80%;padding:15px;"><tr><td>Page</td><td>:'.$dataRow['sno'].'</td></tr></table>';

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

				  
}


$pdf->lastPage();	

// set color for background
//$pdf->SetFillColor(215, 235, 255);

// set color for text
//$pdf->SetTextColor(127, 31, 0);



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tournees_journalieres'.time().'.pdf', 'D');
//$pdf->Output(FCPATH.'tmp\vigik_'.time().'.pdf', 'F');

  
//============================================================+
// END OF FILE
//============================================================+

	}
    /******************************
     * Importation d'un tournées journalières
     ******************************/
    public function importation($id) {
        $this->load->helper(array('form','ctrl'));
        $resultat = false;
        if (array_key_exists('bornes',$_FILES)){
            $f = $_FILES['bornes'];
            if ($f['error'] == 0) {
                $extension = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'].$extension;
                rename($f['tmp_name'],$nom_fichier);
                $resultat = $this->m_newtournee_journalieres->importation($id,$nom_fichier);
                if ($resultat === false) {
                    $this->session->set_flashdata('danger','Un problème technique est survenu - veuillez reessayer ultérieurement');
                }
                elseif ($resultat === true) {
                    $this->session->set_flashdata('success','Le tournées journalières a été chargé');
                    redirect('newtournee_journalieres/detail/'.$id);
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
            'title' => "Importation de tournées journalières",
            'page' => "newtournee_journalieres/importation",
            'menu' => "Produits|Importation de tournées journalières",
            'values' => array (
                'resultat' => $resultat
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

}

// EOF
