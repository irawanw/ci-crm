<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 *
 * @property M_contacts m_contacts
 */

class Contacts extends MY_Controller
{
    private $profil;
    private $barre_action = array(
        "Contacts" => array(
            array(
                // Texte => array( URL, icône, actif, "id" HTML, texte confirmation, array(type action, ...) )
                "Nouveau" => array('contacts/nouveau', 'plus', true, 'contacts_nouveau', null, array('form')),
            ),
            array(
                // "Consulter" => array('contacts/detail', 'eye-open', false, 'contacts_detail', null, array('view', 'dblclick')),
                "Consulter/Modifier"  => array('contacts/modification', 'pencil', false, 'contacts_modification', null, array('form')),
                "Archiver" => array('contacts/archive', 'folder-close', false, 'contacts_archive',"Veuillez confirmer la archive du contact", array('confirm-modify' => array('contacts/index'))),
                "Supprimer" => array('contacts/suppression', 'trash', false, 'contacts_supprimer', "Veuillez confirmer la suppression du contact", array('confirm-delete' => array('contacts/index'))),
            ),
            array(
                "Créer devis" => array('*devis/nouveau', 'list-alt', false, 'nouveau_devis'),
				// "Créer document" => array('contacts/document_generate', 'list-alt', false, 'contacts_document_generate', null, array('form')),
				"Créer document" => array('contacts/document_generate', 'list-alt', false, 'contacts_document_generate'),
            ),
            array(
                "Envoyer email" => array('evenements/email_contact', 'send', false, 'email_type'),
                "Courrier type" => array('documents_contacts/nouveau', 'envelope', false, 'courrier_type'),
            ),
            array(
                "Export PDF"  => array('#', 'book', false, 'export_pdf'),
                "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
                "Impression"  => array('#', 'print', false, 'impression'),
            ),
        ),
        "Contact"  => array(
            array(
                "Consulter" => array('contacts/detail', 'eye-open', true, 'contacts_detail', null, array('view', 'default-view')),
                "Modifier"  => array('contacts/modification', 'pencil', true, 'contacts_modification', null, array('form')),
                "Supprimer" => array('contacts/suppression', 'trash', true, 'contact_supprimer', "Veuillez confirmer la suppression du contact", array('confirm-delete' => array('contacts/index'))),
            ),
            array(
                "Créer devis" => array('*devis/nouveau', 'list-alt', true, 'nouveau_devis'),
                "Devis"       => array('devis/devis_client[]', 'list-alt', true, 'devis'),
                "Commandes"   => array('commandes/commandes_client[]', 'shopping-cart', true, 'commandes'),
                "Factures"    => array('factures/factures_client[]', 'folder-open', true, 'factures'),
                "Avoirs"      => array('avoirs/avoirs_client[]', 'retweet', true, 'avoirs'),
                "Réglements"  => array('reglements/reglements_client[]', 'euro', true, 'reglements'),
            ),
            array(
                "Créer document" => array('documents_contacts/nouveau', 'paperclip', true, 'nouveau_document', null, array('form')),
                "Documents"      => array('documents_contacts/documents_contact[]', 'paperclip', true, 'documents'),
            ),
            array(
                "Evènements" => array('evenements/evenements_client[]', 'calendar', true, 'evenements'),
            ),
            array(
                "Correspondants" => array('correspondants/correspondants_contact[]', 'user', true, 'correspondants'),
                "Envoyer email"  => array('evenements/email_contact', 'send', true, 'email_type', null, array('form')),
                "Courrier type"  => array('documents_contacts/nouveau', 'envelope', true, 'courrier_type', null, array('form')),
            ),
            array(
                "Export PDF"  => array('#', 'book', false, 'export_pdf'),
                "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
                "Impression"  => array('#', 'print', false, 'impression'),
            ),
        ),
        "Edition"  => array(
            array(
                "Consulter" => array('contacts/detail', 'eye-open', true, 'contacts_detail', null, array('view', 'default-view')),
                "Modifier"  => array('contacts/modification', 'pencil', true, 'contacts_modification', null, array('form')),
                "Supprimer" => array('contacts/suppression', 'trash', true, 'contact_supprimer', "Veuillez confirmer la suppression du contact", array('confirm-delete' => array('contacts/index'))),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_contacts','m_utilisateurs'));
    }
	
	public function document_generate($id = 0, $ajax=false, $combine = false)
	{
		$this->config->load('export');
		$this->load->helper(array('form', 'ctrl','download','file','support_helper'));
		$this->load->library(array('form_validation','PHPWord','parser','word','zip'));
		
        $config = array(
            array('field' => 'tpl_nom', 'label' => "Template Nom", 'rules' => 'trim|required'),
            array('field' => 'content', 'label' => "Content", 'rules' => 'required'),
        );

        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) 
		{
            $ctc_id		= $this->input->post('ctc_id');
			$tpl_nom	= $this->input->post('tpl_nom');
			$content	= $this->input->post('content');
			
			/**
			 * Generate One Document with error handling using library Word
			 */
			if(count($ctc_id) == 1) 
			{
				try {
		            $resultat = $this->word->create_document_contact($tpl_nom, $ctc_id[0], $content, $combine);
		            
            		$ajaxData = array(
	                     'event' => array(
	                         'controleur' => $this->my_controleur_from_class(__CLASS__),
	                         'type' => 'recordchange',
	                         'timeStamp' => round(microtime(true) * 1000),
	                         'redirect' => $resultat['fileUrl']
	                     ),
	                 );
	                $this->my_set_action_response($ajax, true, $resultat['message'], 'info', $ajaxData);
		               
		        } catch (MY_Exceptions_NoSuchFolder $e) {
		            $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

		        } catch (MY_Exceptions_NoSuchFile $e) {
		            $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

		        } catch (MY_Exceptions_NoSuchRecord $e) {
		            $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');
		        }
			} else {
				/**
				* Generate Many Document
				*/
				try 
				{
					$resultat = $this->word->create_document_contact($tpl_nom, $ctc_id, $content, $combine);
					$ajaxData = array(
								'event' => array(
								'controleur' => $this->my_controleur_from_class(__CLASS__),
								'type' => 'recordchange',
								'timeStamp' => round(microtime(true) * 1000),
								'redirect' => $resultat['fileUrl']
								),
					);
					$this->my_set_action_response($ajax, true, $resultat['message'], 'info', $ajaxData);

				} catch (MY_Exceptions_NoSuchFolder $e) {
				$this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

				} catch (MY_Exceptions_NoSuchFile $e) {
				$this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

				} catch (MY_Exceptions_NoSuchRecord $e) {
				$this->my_set_action_response($ajax,false,$e->getMessage(),'warning');
				}
			}

            if ($ajax) {return;}
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {$redirection = '';}
            redirect($redirection);
        }
		else 
		{
			$selectedIds                = (array) json_decode($this->input->post('selectedIds'));
			$valeurs					= new stdClass();
			$listes_valeurs				= new stdClass();
			$listes_valeurs->tpl_nom	= $this->m_contacts->liste_template();
			$listes_valeurs->ctc_id     = $this->m_contacts->get_selected($selectedIds);

			$valeurs->ctc_id			= $this->input->post('ctc_id');
			$valeurs->tpl_nom			= $this->input->post('tpl_nom');
			$valeurs->content			= $this->input->post('content');

			$descripteur = array(
				'champs'  => array(
					'ctc_id'		=> array("Contacts", 'select-multiple',array('ctc_id','id','value'), false),
					'tpl_nom'		=> array("Nom", 'select',array('tpl_nom','id','value'), false),
					'content'		=> array("Content", 'textarea', 'content', true),
				),
				'onglets' => array(),	
			);

            $data = array(
                'title'          => "Nouveau Document Contact",
                'page'           => "templates/form",
                'menu'           => "Contacts|Nouveau Document Contact",
				'values'         => $valeurs,
				'action'         => "création",
				'multipart'      => false,
				'confirmation'   => 'Generate Docx',
				'controleur'     => 'contacts',
				'methode'        => __FUNCTION__,
				'descripteur'    => $descripteur,
				'listes_valeurs' => $listes_valeurs,
			);
			$this->my_set_form_display_response($ajax, $data);
		}
	}
	
	function save_html_to_file($tempDir,$content,$path)
	{
		$fullpath	= $tempDir.$path;
		return (bool) file_put_contents($fullpath, $content);
	}

    /******************************
     * List of contacts Data
     ******************************/
    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function archiver()
    {
        $this->liste($id = 0, 'archiver');
    }

    public function supprimees()
    {
        $this->liste($id = 0, 'supprimees');
    }

    public function all()
    {
        $this->liste($id = 0, 'all');
    }

    /******************************
     * Liste des contacts
     ******************************/
    public function liste($id = 0, $mode = 0)
    {

        // commandes globales
        $cmd_globales = array(
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'contacts/index',
            'detail'             => array('contacts/detail', 'ctc_id', 'ctc_nom'),
            'champs'             => $this->m_contacts->get_champs('read'),
            'filterable_columns' => $this->m_contacts->liste_filterable_columns(),
        );

        switch ($mode) {
            case 'archiver':
                $descripteur['datasource'] = 'contacts/archived';
                break;
            case 'supprimees':
                $descripteur['datasource'] = 'contacts/deleted';
                break;
            case 'all':
                $descripteur['datasource'] = 'contacts/all';
                break;
        }

        $barre_action = $this->barre_action["Contacts"];

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                  => $id,
                'descripteur'         => $descripteur,
                'toolbar'             => $toolbar,
                'controleur'          => 'contacts',
                'methode'             => __FUNCTION__,
                'mass_action_toolbar' => true,
                'view_toolbar'        => true,
            ), true);
        $scripts[] = $this->load->view('contacts/form-js', array(), true);
        $scripts[] = $this->load->view('contacts/liste-js', array(
            'barre_action' => $barre_action,
        ), true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('contacts', $this->session->id);
        $data = array(
            'title'                  => "Liste des contacts",
            'page'                   => "templates/datatables",
            'menu'                   => "Contacts|Contacts",
            'scripts'                => $scripts,
            'barre_action'           => $barre_action,
            'controleur'             => 'contacts',
            'methode'                => __FUNCTION__,
            'animation_barre_action' => false,
            'values'                 => array(
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

    /******************************
     * Liste des contacts (datasource)
     ******************************/
    public function index_json($id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            die('');
        }

        //$pagelength = $this->input->post('length');
        //$pagestart  = $this->input->post('start' );
        $pagelength = 100;
        $pagestart  = 0 + $this->input->post('start');
        if ($pagestart < 2) {
            $pagelength = 50;
        }

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

        if ($this->input->post('export')) {
            $pagelength = false;
            $pagestart  = 0;
        }

        if (isset($filters['ctc_marche']['input'])) {
            $find = strtolower($filters['ctc_marche']['input']);
            $c    = strlen($find);
            if ($c == 1) {
                if (strpos($find, 'n') !== false) {
                    $ctc_filter = 0;
                } else if (strpos($find, 'o') !== false or strpos($find, 'i') !== false) {
                    $ctc_filter = 1;
                }
            } else if ($c == 2) {
                if (strpos($find, 'no') !== false or strpos($find, 'on') !== false) {
                    $ctc_filter = 0;
                } else if (strpos($find, 'ou') !== false or strpos($find, 'ui') !== false) {
                    $ctc_filter = 1;
                }
            } else if ($c == 3) {
                if (strpos($find, 'non') !== false) {

                    $ctc_filter = 0;
                } else if (strpos($find, 'oui') !== false) {
                    $ctc_filter = 1;
                }
            }
            $filters['ctc_marche']['input'] = $ctc_filter;
        }

        if (isset($filters['ctc_alerte']['input'])) {
            $find = strtolower($filters['ctc_alerte']['input']);
            $c    = strlen($find);
            if ($c == 1) {
                if (strpos($find, 'n') !== false) {
                    $ctc_filter = 0;
                } else if (strpos($find, 'o') !== false or strpos($find, 'i') !== false) {
                    $ctc_filter = 1;
                }
            } else if ($c == 2) {
                if (strpos($find, 'no') !== false or strpos($find, 'on') !== false) {

                    $ctc_filter = 0;
                } else if (strpos($find, 'ou') !== false or strpos($find, 'ui') !== false) {
                    $ctc_filter = 1;
                }
            } else if ($c == 3) {
                if (strpos($find, 'non') !== false) {

                    $ctc_filter = 0;
                } else if (strpos($find, 'oui') !== false) {
                    $ctc_filter = 1;
                }
            }
            $filters['ctc_alerte']['input'] = $ctc_filter;
        }

        if (empty($order) || empty($columns)) {

            //list with default ordering
            $resultat = $this->m_contacts->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'ctc_nom'           => 't_contacts',
                'ctc_date_creation' => 't_contacts',
                'emp_nom'           => 't_employes',
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

                $resultat = $this->m_contacts->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_contacts->liste($id, $pagelength, $pagestart, $filters);
            }
        }

        if ($this->input->post('export')) {
            //action export data xls
            $champs = $this->m_contacts->get_champs('read');
            $params = array(
                'records'  => $resultat['data'],
                'columns'  => $champs,
                'filename' => 'Contacts',
            );
            $this->_export_xls($params);
        } else {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
    }

    public function archived_json($id = 0)
    {
        $this->index_json('archived');
    }

    public function deleted_json($id = 0)
    {
        $this->index_json('deleted');
    }

    public function all_json($id = 0)
    {
        $this->index_json('all');
    }

    /******************************
     * Nouveau contact
     ******************************/
    public function nouveau($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            //array('field' => 'ctc_id_comptable', 'label' => "Id compta", 'rules' => 'trim'),
            array('field' => 'ctc_nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'ctc_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'ctc_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'ctc_complement', 'label' => "Complément adresse", 'rules' => 'trim'),
            array('field' => 'ctc_telephone', 'label' => "Téléphone", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_fax', 'label' => "Fax", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_mobile', 'label' => "Mobile", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'ctc_site', 'label' => "Site internet", 'rules' => 'trim'),
            array('field' => 'ctc_origine', 'label' => "Origine détaillée", 'rules' => 'trim'),
            array('field' => 'ctc_notes', 'label' => "Remarques", 'rules' => 'trim'),
            array('field' => 'ctc_qual_comm', 'label' => "Qualité commerciale", 'rules' => 'trim'),
            array('field' => 'ctc_prospection', 'label' => "Statut prospection", 'rules' => 'trim'),
            array('field' => 'ctc_marche', 'label' => "marché", 'rules' => 'trim'),
            array('field' => 'ctc_date_marche', 'label' => "Date marché", 'rules' => 'trim'),
            array('field' => 'ctc_alerte', 'label' => "Alerte", 'rules' => 'trim'),
            array('field' => 'ctc_date_alerte', 'label' => "Date Alerte", 'rules' => 'trim'),
            array('field' => 'ctc_remarques_sur_marche', 'label' => "Remarques sur marché", 'rules' => 'trim'),
            array('field' => 'ctc_url', 'label' => "URL espace client", 'rules' => 'trim|valid_url'),
            array('field' => 'ctc_info_connexion', 'label' => "Informations de connexion", 'rules' => 'trim'),
            array('field' => 'ctc_login', 'label' => "Login", 'rules' => 'trim'),
            array('field' => 'ctc_dist_hlm', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_dist_res', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_dist_pav', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_stock', 'label' => "Stock actuel", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_del_avant_distrib', 'label' => "Délai avant prochaine distribution", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_poids', 'label' => "Poids", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_prix_rural', 'label' => "Prix rural", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_urbain', 'label' => "Prix urbain", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_urgent', 'label' => "Prix urgent", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_cible', 'label' => "Prix cible", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_commercial', 'label' => "Commercial", 'rules' => 'required'),
            array('field' => 'ctc_livr_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'ctc_livr_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_livr_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'ctc_livr_horaire', 'label' => "Horaires", 'rules' => 'trim'),
            array('field' => 'ctc_livr_info', 'label' => "Autres informations", 'rules' => 'trim'),
            array('field' => 'ctc_commercial_charge', 'label' => "Commercial en charge", 'rules' => 'trim'),
            array('field' => 'ctc_enseigne', 'label' => "Enseigne", 'rules' => 'trim'),
            array('field' => 'ctc_statistiques', 'label' => "Statistiques", 'rules' => 'trim'),
            array('field' => 'ctc_signe', 'label' => "Signe", 'rules' => 'trim'),           
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                //'ctc_id_comptable'         => $this->input->post('ctc_id_comptable'),
                'ctc_nom'                  => $this->input->post('ctc_nom'),
                'ctc_adresse'              => $this->input->post('ctc_adresse'),
                'ctc_cp'                   => $this->input->post('ctc_cp'),
                'ctc_ville'                => $this->input->post('ctc_ville'),
                'ctc_complement'           => $this->input->post('ctc_complement'),
                'ctc_telephone'            => $this->input->post('ctc_telephone'),
                'ctc_fax'                  => $this->input->post('ctc_fax'),
                'ctc_mobile'               => $this->input->post('ctc_mobile'),
                'ctc_email'                => $this->input->post('ctc_email'),
                'ctc_site'                 => $this->input->post('ctc_site'),
                'ctc_activite'             => $this->input->post('ctc_activite'),
                'ctc_origine'              => $this->input->post('ctc_origine'),
                'ctc_notes'                => $this->input->post('ctc_notes'),
                'ctc_client_prospect'      => $this->input->post('ctc_client_prospect'),
                'ctc_qual_comm'            => $this->input->post('ctc_qual_comm'),
                'ctc_prospection'          => $this->input->post('ctc_prospection'),
                'ctc_marche'               => $this->input->post('ctc_marche'),
                'ctc_date_marche'          => formatte_date_to_bd($this->input->post('ctc_date_marche')),
                'ctc_alerte'               => $this->input->post('ctc_alerte'),
                'ctc_date_alerte'          => formatte_date_to_bd($this->input->post('ctc_date_alerte')),
                'ctc_remarques_sur_marche' => $this->input->post('ctc_remarques_sur_marche'),
                'ctc_fournisseur'          => $this->input->post('ctc_fournisseur'),
                'ctc_url'                  => $this->input->post('ctc_url'),
                'ctc_info_connexion'       => $this->input->post('ctc_info_connexion'),
                'ctc_login'                => $this->input->post('ctc_login'),
                'ctc_type_distribution'    => $this->input->post('ctc_type_distribution'),
                'ctc_dist_hlm'             => $this->input->post('ctc_dist_hlm'),
                'ctc_dist_res'             => $this->input->post('ctc_dist_res'),
                'ctc_dist_pav'             => $this->input->post('ctc_dist_pav'),
                'ctc_stock'                => $this->input->post('ctc_stock'),
                'ctc_cycle_complet'        => $this->input->post('ctc_cycle_complet'),
                'ctc_del_avant_distrib'    => $this->input->post('ctc_del_avant_distrib'),
                'ctc_poids'                => $this->input->post('ctc_poids'),
                'ctc_doc_manuel'           => $this->input->post('ctc_doc_manuel'),
                'ctc_sonner'               => $this->input->post('ctc_sonner'),
                'ctc_prix_rural'           => $this->input->post('ctc_prix_rural'),
                'ctc_prix_urbain'          => $this->input->post('ctc_prix_urbain'),
                'ctc_prix_urgent'          => $this->input->post('ctc_prix_urgent'),
                'ctc_prix_cible'           => $this->input->post('ctc_prix_cible'),
                'ctc_commercial'           => $this->input->post('ctc_commercial'),
                'ctc_livr_adresse'         => $this->input->post('ctc_livr_adresse'),
                'ctc_livr_cp'              => $this->input->post('ctc_livr_cp'),
                'ctc_livr_ville'           => $this->input->post('ctc_livr_ville'),
                'ctc_livr_horaire'         => $this->input->post('ctc_livr_horaire'),
                'ctc_livr_info'            => $this->input->post('ctc_livr_info'),
                'ctc_commercial_charge'    => $this->input->post('ctc_commercial_charge'),
                'ctc_enseigne'             => $this->input->post('ctc_enseigne'),
                'ctc_statistiques'         => $this->input->post('ctc_statistiques'),
                'ctc_signe'                => $this->input->post('ctc_signe'),              
                'ctc_client_prospect'      => "1",
                'ctc_date_creation'        => date('Y-m-d H:i:s'),
            );

            if (!isset($valeurs['ctc_marche'])) {
                $valeurs['ctc_marche'] = 0;
            }
            if (!isset($valeurs['ctc_alerte'])) {
                $valeurs['ctc_alerte'] = 0;
            }
            if (!isset($valeurs['ctc_fournisseur'])) {
                $valeurs['ctc_fournisseur'] = 0;
            }
            if (!isset($valeurs['ctc_cycle_complet'])) {
                $valeurs['ctc_cycle_complet'] = 0;
            }
            if (!isset($valeurs['ctc_doc_manuel'])) {
                $valeurs['ctc_doc_manuel'] = 0;
            }
            if (!isset($valeurs['ctc_sonner'])) {
                $valeurs['ctc_sonner'] = 0;
            }
            // start patch by Emile Jerome 
            if (!isset($valeurs['ctc_date_marche'])) {
                $valeurs['ctc_date_marche'] = 0;
            }
            if (!isset($valeurs['ctc_date_alerte'])) {
                $valeurs['ctc_date_alerte'] = 0;
            }
            // end patch by Emile Jerome
            $id = $this->m_contacts->nouveau_contact($valeurs);
            if ($id === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                $ajaxData = array(
                    'event' => array(
                        'controleur' => $this->my_controleur_from_class(__CLASS__),
                        'type'       => 'recordadd',
                        'id'         => $id,
                        'timeStamp'  => round(microtime(true) * 1000),
                    ),
                );
                $this->my_set_action_response($ajax, true, "Le contact a été créé", 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);

        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs                           = new stdClass();
            $listes_valeurs                    = new stdClass();
            //$valeurs->ctc_id_comptable         = $this->input->post('ctc_id_comptable');
            $valeurs->ctc_nom                  = $this->input->post('ctc_nom');
            $valeurs->ctc_adresse              = $this->input->post('ctc_adresse');
            $valeurs->ctc_cp                   = $this->input->post('ctc_cp');
            $valeurs->ctc_ville                = $this->input->post('ctc_ville');
            $valeurs->ctc_complement           = $this->input->post('ctc_complement');
            $valeurs->ctc_telephone            = $this->input->post('ctc_telephone');
            $valeurs->ctc_fax                  = $this->input->post('ctc_fax');
            $valeurs->ctc_mobile               = $this->input->post('ctc_mobile');
            $valeurs->ctc_email                = $this->input->post('ctc_email');
            $valeurs->ctc_site                 = $this->input->post('ctc_site');
            $valeurs->ctc_activite             = $this->input->post('ctc_activite');
            $valeurs->ctc_origine              = $this->input->post('ctc_origine');
            $valeurs->ctc_notes                = $this->input->post('ctc_notes');
            $valeurs->ctc_client_prospect      = $this->input->post('ctc_client_prospect');
            $valeurs->ctc_qual_comm            = $this->input->post('ctc_qual_comm');
            $valeurs->ctc_prospection          = $this->input->post('ctc_prospection');
            $valeurs->ctc_marche               = $this->input->post('ctc_marche');
            $valeurs->ctc_date_marche          = $this->input->post('ctc_date_marche');
            $valeurs->ctc_alerte               = $this->input->post('ctc_alerte');
            $valeurs->ctc_date_alerte          = $this->input->post('ctc_date_alerte');
            $valeurs->ctc_remarques_sur_marche = $this->input->post('ctc_remarques_sur_marche');
            $valeurs->ctc_fournisseur          = $this->input->post('ctc_fournisseur');
            $valeurs->ctc_url                  = $this->input->post('ctc_url');
            $valeurs->ctc_info_connexion       = $this->input->post('ctc_info_connexion');
            $valeurs->ctc_login                = $this->input->post('ctc_login');
            $valeurs->ctc_type_distribution    = $this->input->post('ctc_type_distribution');
            $valeurs->ctc_dist_hlm             = $this->input->post('ctc_dist_hlm');
            $valeurs->ctc_dist_res             = $this->input->post('ctc_dist_res');
            $valeurs->ctc_dist_pav             = $this->input->post('ctc_dist_pav');
            $valeurs->ctc_stock                = $this->input->post('ctc_stock');
            $valeurs->ctc_cycle_complet        = $this->input->post('ctc_cycle_complet');
            $valeurs->ctc_del_avant_distrib    = $this->input->post('ctc_del_avant_distrib');
            $valeurs->ctc_poids                = $this->input->post('ctc_poids');
            $valeurs->ctc_doc_manuel           = $this->input->post('ctc_doc_manuel');
            $valeurs->ctc_sonner               = $this->input->post('ctc_sonner');
            $valeurs->ctc_prix_rural           = $this->input->post('ctc_prix_rural');
            $valeurs->ctc_prix_urbain          = $this->input->post('ctc_prix_urbain');
            $valeurs->ctc_prix_urgent          = $this->input->post('ctc_prix_urgent');
            $valeurs->ctc_prix_cible           = $this->input->post('ctc_prix_cible');
            $valeurs->ctc_commercial           = $this->input->post('ctc_commercial');
            $valeurs->ctc_livr_adresse         = $this->input->post('ctc_livr_adresse');
            $valeurs->ctc_livr_cp              = $this->input->post('ctc_livr_cp');
            $valeurs->ctc_livr_ville           = $this->input->post('ctc_livr_ville');
            $valeurs->ctc_livr_horaire         = $this->input->post('ctc_livr_horaire');
            $valeurs->ctc_livr_info            = $this->input->post('ctc_livr_info');
            $valeurs->ctc_commercial_charge    = $this->input->post('ctc_commercial_charge');
            $valeurs->ctc_enseigne             = $this->input->post('ctc_enseigne');
            $valeurs->ctc_statistiques         = $this->input->post('ctc_statistiques');
            $valeurs->ctc_signe                = $this->input->post('ctc_signe');        

            $listes_valeurs->ctc_origine = $this->m_contacts->liste_origine_prospect();
            $this->db->order_by('vac_activite', 'ASC');
            $q                            = $this->db->get('v_activites');
            $listes_valeurs->ctc_activite = $q->result();
            $this->db->order_by('vcp_type', 'ASC');
            $q                                   = $this->db->get('v_clients_prospects');
            $listes_valeurs->ctc_client_prospect = $q->result();
            $this->db->order_by('vtdi_type', 'ASC');
            $q                                     = $this->db->get('v_types_distributions');
            $listes_valeurs->ctc_type_distribution = $q->result();
            $this->db->where("emp_fonction=1");
            $this->db->order_by('emp_nom', 'ASC');
            $q                                     = $this->db->get('t_employes');
            $listes_valeurs->ctc_commercial        = $q->result();
            $listes_valeurs->ctc_commercial_charge = $this->m_utilisateurs->liste_option();
            $listes_valeurs->ctc_enseigne          = $this->m_contacts->enseigne_liste();
            $listes_valeurs->ctc_statistiques      = $this->m_contacts->statistiques_liste();
            $listes_valeurs->ctc_signe             = $this->m_contacts->signe_liste();       

            // descripteur
            $descripteur = array(
                'champs'  => $this->m_contacts->get_champs('write'),
                'onglets' => array(
                    array("Contact", array(/*'ctc_id_comptable',*/ 'ctc_nom', 'ctc_adresse', 'ctc_cp', 'ctc_ville', 'ctc_complement', 'ctc_telephone', 'ctc_fax', 'ctc_mobile', 'ctc_email', 'ctc_site', 'ctc_activite', 'ctc_commercial_charge', 'ctc_origine', 'ctc_enseigne', 'ctc_notes', 'ctc_client_prospect', 'ctc_qual_comm', 'ctc_prospection', 'ctc_marche', 'ctc_date_marche', 'ctc_alerte', 'ctc_date_alerte', 'ctc_remarques_sur_marche','ctc_statistiques','ctc_signe','ctc_fournisseur', 'ctc_url', 'ctc_info_connexion')),
                    array("Distribution", array('ctc_login', 'ctc_type_distribution', 'ctc_dist_hlm', 'ctc_dist_res', 'ctc_dist_pav', 'ctc_stock', 'ctc_cycle_complet', 'ctc_del_avant_distrib', 'ctc_poids', 'ctc_doc_manuel', 'ctc_sonner', 'ctc_prix_rural', 'ctc_prix_urbain', 'ctc_prix_urgent', 'ctc_prix_cible', 'ctc_commercial')),
                    array("Livraisons", array('ctc_livr_adresse', 'ctc_livr_cp', 'ctc_livr_ville', 'ctc_livr_horaire', 'ctc_livr_info')),
                ),
            );
			
            $data = array(
                'title'          => "Nouveau contact",
                'page'           => "templates/form",
                'menu'           => "Contacts|Nouveau contact",
                'barre_action'   => $this->barre_action["Contact"], 
				'values'         => $valeurs,
                'action'         => "création",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'contacts',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

	function getTemplate()
	{
		$this->load->library('parser');
		$template_id 	= $this->input->post('template_id');
		$contact_ids    = $this->input->post('contact_ids');
		$template 		= $this->m_contacts->template_detail($template_id);
		$data	 		= $this->m_contacts->detail($contact_ids[0]);
		foreach($data as $key => $val)
		{
			if(count($contact_ids) == 1) {
				$contact[$key] = $val;
			} else {
				$contact[$key] = "{".$key."}";
			}
		}
		$contact['message_date'] = date("d/m/Y");
		$this->parser->parse_string($template->tpl_content, $contact);
	}
	
    /******************************
     * Mise à jour d'un contact
     * support AJAX
     ******************************/
    public function modification($id = 0, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        $this->load->library('form_validation');

        // règles de validation
        $config = array(
            array('field' => 'ctc_nom', 'label' => "Nom", 'rules' => 'trim|required'),
            array('field' => 'ctc_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'ctc_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'ctc_complement', 'label' => "Complément adresse", 'rules' => 'trim'),
            array('field' => 'ctc_telephone', 'label' => "Téléphone", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_fax', 'label' => "Fax", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_mobile', 'label' => "Mobile", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_email', 'label' => "Email", 'rules' => 'trim|valid_email'),
            array('field' => 'ctc_site', 'label' => "Site internet", 'rules' => 'trim'),
            array('field' => 'ctc_origine', 'label' => "Origine détaillée", 'rules' => 'trim'),
            array('field' => 'ctc_notes', 'label' => "Remarques", 'rules' => 'trim'),
            array('field' => 'ctc_qual_comm', 'label' => "Qualité commerciale", 'rules' => 'trim'),
            array('field' => 'ctc_prospection', 'label' => "Statut prospection", 'rules' => 'trim'),
            array('field' => 'ctc_marche', 'label' => "marché", 'rules' => 'trim'),
            array('field' => 'ctc_date_marche', 'label' => "Date marché", 'rules' => 'trim'),
            array('field' => 'ctc_alerte', 'label' => "Alerte", 'rules' => 'trim'),
            array('field' => 'ctc_date_alerte', 'label' => "Date Alerte", 'rules' => 'trim'),
            array('field' => 'ctc_remarques_sur_marche', 'label' => "Remarques sur marché", 'rules' => 'trim'),
            array('field' => 'ctc_url', 'label' => "URL espace client", 'rules' => 'trim|valid_url'),
            array('field' => 'ctc_info_connexion', 'label' => "Informations de connexion", 'rules' => 'trim'),
            array('field' => 'ctc_login', 'label' => "Login", 'rules' => 'trim'),
            array('field' => 'ctc_dist_hlm', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_dist_res', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_dist_pav', 'label' => "Distribuer HLM", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_stock', 'label' => "Stock actuel", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_del_avant_distrib', 'label' => "Délai avant prochaine distribution", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_poids', 'label' => "Poids", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_prix_rural', 'label' => "Prix rural", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_urbain', 'label' => "Prix urbain", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_urgent', 'label' => "Prix urgent", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_prix_cible', 'label' => "Prix cible", 'rules' => 'trim|numeric|greater_than_equal_to[0]'),
            array('field' => 'ctc_commercial', 'label' => "Commercial", 'rules' => 'required'),
            array('field' => 'ctc_livr_adresse', 'label' => "Adresse", 'rules' => 'trim'),
            array('field' => 'ctc_livr_cp', 'label' => "Code postal", 'rules' => 'trim|is_natural'),
            array('field' => 'ctc_livr_ville', 'label' => "Ville", 'rules' => 'trim'),
            array('field' => 'ctc_livr_horaire', 'label' => "Horaires", 'rules' => 'trim'),
            array('field' => 'ctc_livr_info', 'label' => "Autres informations", 'rules' => 'trim'),
            array('field' => 'ctc_commercial_charge', 'label' => "Commercial en charge", 'rules' => 'trim'),
            array('field' => 'ctc_enseigne', 'label' => "Enseigne", 'rules' => 'trim'),
            array('field' => 'ctc_statistiques', 'label' => "Statistiques", 'rules' => 'trim'),
            array('field' => 'ctc_signe', 'label' => "Signe", 'rules' => 'trim'),            
            array('field' => '__form', 'label' => 'Témoin', 'rules' => 'required'),
        );

        // validation des fichiers chargés
        $validation = true;
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() and $validation) {

            // validation réussie
            $valeurs = array(
                'ctc_nom'                  => $this->input->post('ctc_nom'),
                'ctc_adresse'              => $this->input->post('ctc_adresse'),
                'ctc_cp'                   => $this->input->post('ctc_cp'),
                'ctc_ville'                => $this->input->post('ctc_ville'),
                'ctc_complement'           => $this->input->post('ctc_complement'),
                'ctc_telephone'            => $this->input->post('ctc_telephone'),
                'ctc_fax'                  => $this->input->post('ctc_fax'),
                'ctc_mobile'               => $this->input->post('ctc_mobile'),
                'ctc_email'                => $this->input->post('ctc_email'),
                'ctc_site'                 => $this->input->post('ctc_site'),
                'ctc_activite'             => $this->input->post('ctc_activite'),
                'ctc_origine'              => $this->input->post('ctc_origine'),
                'ctc_notes'                => $this->input->post('ctc_notes'),
                'ctc_qual_comm'            => $this->input->post('ctc_qual_comm'),
                'ctc_prospection'          => $this->input->post('ctc_prospection'),
                'ctc_marche'               => $this->input->post('ctc_marche'),
                'ctc_date_marche'          => formatte_date_to_bd($this->input->post('ctc_date_marche')),
                'ctc_alerte'               => $this->input->post('ctc_alerte'),
                'ctc_date_alerte'          => formatte_date_to_bd($this->input->post('ctc_date_alerte')),
                'ctc_remarques_sur_marche' => $this->input->post('ctc_remarques_sur_marche'),
                'ctc_fournisseur'          => $this->input->post('ctc_fournisseur'),
                'ctc_url'                  => $this->input->post('ctc_url'),
                'ctc_info_connexion'       => $this->input->post('ctc_info_connexion'),
                'ctc_login'                => $this->input->post('ctc_login'),
                'ctc_type_distribution'    => $this->input->post('ctc_type_distribution'),
                'ctc_dist_hlm'             => $this->input->post('ctc_dist_hlm'),
                'ctc_dist_res'             => $this->input->post('ctc_dist_res'),
                'ctc_dist_pav'             => $this->input->post('ctc_dist_pav'),
                'ctc_stock'                => $this->input->post('ctc_stock'),
                'ctc_cycle_complet'        => $this->input->post('ctc_cycle_complet'),
                'ctc_del_avant_distrib'    => $this->input->post('ctc_del_avant_distrib'),
                'ctc_poids'                => $this->input->post('ctc_poids'),
                'ctc_doc_manuel'           => $this->input->post('ctc_doc_manuel'),
                'ctc_sonner'               => $this->input->post('ctc_sonner'),
                'ctc_prix_rural'           => $this->input->post('ctc_prix_rural'),
                'ctc_prix_urbain'          => $this->input->post('ctc_prix_urbain'),
                'ctc_prix_urgent'          => $this->input->post('ctc_prix_urgent'),
                'ctc_prix_cible'           => $this->input->post('ctc_prix_cible'),
                'ctc_commercial'           => $this->input->post('ctc_commercial'),
                'ctc_livr_adresse'         => $this->input->post('ctc_livr_adresse'),
                'ctc_livr_cp'              => $this->input->post('ctc_livr_cp'),
                'ctc_livr_ville'           => $this->input->post('ctc_livr_ville'),
                'ctc_livr_horaire'         => $this->input->post('ctc_livr_horaire'),
                'ctc_livr_info'            => $this->input->post('ctc_livr_info'),
                'ctc_commercial_charge'    => $this->input->post('ctc_commercial_charge'),
                'ctc_enseigne'             => $this->input->post('ctc_enseigne'),
                'ctc_statistiques'         => $this->input->post('ctc_statistiques'),
                'ctc_signe'                => $this->input->post('ctc_signe'),                
            );
            $resultat = $this->m_contacts->maj($valeurs, $id);
            if ($resultat === false) {
                $this->my_set_action_response($ajax, false);
            } else {
                if ($resultat == 0) {
                    $message  = "Pas de modification demandée";
                    $ajaxData = null;
                } else {
                    $message  = "Le contact a été modifié";
                    $ajaxData = array(
                        'event' => array(
                            'controleur' => $this->my_controleur_from_class(__CLASS__),
                            'type'       => 'recordchange',
                            'id'         => $id,
                            'timeStamp'  => round(microtime(true) * 1000),
                        ),
                    );
                }
                $this->my_set_action_response($ajax, true, $message, 'info', $ajaxData);
            }
            if ($ajax) {
                return;
            }
            $redirection = 'contacts/detail/' . $id;
            redirect($redirection);
        } else {

            // validation en échec ou premier appel : affichage du formulaire
            $valeurs        = $this->m_contacts->detail($id);
            $listes_valeurs = new stdClass();
            $valeur         = $this->input->post('ctc_nom');
            if (isset($valeur)) {
                $valeurs->ctc_nom = $valeur;
            }
            $valeur = $this->input->post('ctc_adresse');
            if (isset($valeur)) {
                $valeurs->ctc_adresse = $valeur;
            }
            $valeur = $this->input->post('ctc_cp');
            if (isset($valeur)) {
                $valeurs->ctc_cp = $valeur;
            }
            $valeur = $this->input->post('ctc_ville');
            if (isset($valeur)) {
                $valeurs->ctc_ville = $valeur;
            }
            $valeur = $this->input->post('ctc_complement');
            if (isset($valeur)) {
                $valeurs->ctc_complement = $valeur;
            }
            $valeur = $this->input->post('ctc_telephone');
            if (isset($valeur)) {
                $valeurs->ctc_telephone = $valeur;
            }
            $valeur = $this->input->post('ctc_fax');
            if (isset($valeur)) {
                $valeurs->ctc_fax = $valeur;
            }
            $valeur = $this->input->post('ctc_mobile');
            if (isset($valeur)) {
                $valeurs->ctc_mobile = $valeur;
            }
            $valeur = $this->input->post('ctc_email');
            if (isset($valeur)) {
                $valeurs->ctc_email = $valeur;
            }
            $valeur = $this->input->post('ctc_site');
            if (isset($valeur)) {
                $valeurs->ctc_site = $valeur;
            }
            $valeur = $this->input->post('ctc_activite');
            if (isset($valeur)) {
                $valeurs->ctc_activite = $valeur;
            }
            $valeur = $this->input->post('ctc_origine');
            if (isset($valeur)) {
                $valeurs->ctc_origine = $valeur;
            }
            $valeur = $this->input->post('ctc_notes');
            if (isset($valeur)) {
                $valeurs->ctc_notes = $valeur;
            }
            $valeur = $this->input->post('ctc_qual_comm');
            if (isset($valeur)) {
                $valeurs->ctc_qual_comm = $valeur;
            }
            $valeur = $this->input->post('ctc_prospection');
            if (isset($valeur)) {
                $valeurs->ctc_prospection = $valeur;
            }
            $valeur = $this->input->post('ctc_fournisseur');
            if (isset($valeur)) {
                $valeurs->ctc_fournisseur = $valeur;
            }
            $valeur = $this->input->post('ctc_url');
            if (isset($valeur)) {
                $valeurs->ctc_url = $valeur;
            }
            $valeur = $this->input->post('ctc_info_connexion');
            if (isset($valeur)) {
                $valeurs->ctc_info_connexion = $valeur;
            }
            $valeur = $this->input->post('ctc_login');
            if (isset($valeur)) {
                $valeurs->ctc_login = $valeur;
            }
            $valeur = $this->input->post('ctc_type_distribution');
            if (isset($valeur)) {
                $valeurs->ctc_type_distribution = $valeur;
            }
            $valeur = $this->input->post('ctc_dist_hlm');
            if (isset($valeur)) {
                $valeurs->ctc_dist_hlm = $valeur;
            }
            $valeur = $this->input->post('ctc_dist_res');
            if (isset($valeur)) {
                $valeurs->ctc_dist_res = $valeur;
            }
            $valeur = $this->input->post('ctc_dist_pav');
            if (isset($valeur)) {
                $valeurs->ctc_dist_pav = $valeur;
            }
            $valeur = $this->input->post('ctc_stock');
            if (isset($valeur)) {
                $valeurs->ctc_stock = $valeur;
            }
            $valeur = $this->input->post('ctc_cycle_complet');
            if (isset($valeur)) {
                $valeurs->ctc_cycle_complet = $valeur;
            }
            $valeur = $this->input->post('ctc_del_avant_distrib');
            if (isset($valeur)) {
                $valeurs->ctc_del_avant_distrib = $valeur;
            }
            $valeur = $this->input->post('ctc_poids');
            if (isset($valeur)) {
                $valeurs->ctc_poids = $valeur;
            }
            $valeur = $this->input->post('ctc_doc_manuel');
            if (isset($valeur)) {
                $valeurs->ctc_doc_manuel = $valeur;
            }
            $valeur = $this->input->post('ctc_sonner');
            if (isset($valeur)) {
                $valeurs->ctc_sonner = $valeur;
            }
            $valeur = $this->input->post('ctc_prix_rural');
            if (isset($valeur)) {
                $valeurs->ctc_prix_rural = $valeur;
            }
            $valeur = $this->input->post('ctc_prix_urbain');
            if (isset($valeur)) {
                $valeurs->ctc_prix_urbain = $valeur;
            }
            $valeur = $this->input->post('ctc_prix_urgent');
            if (isset($valeur)) {
                $valeurs->ctc_prix_urgent = $valeur;
            }
            $valeur = $this->input->post('ctc_prix_cible');
            if (isset($valeur)) {
                $valeurs->ctc_prix_cible = $valeur;
            }
            $valeur = $this->input->post('ctc_commercial');
            if (isset($valeur)) {
                $valeurs->ctc_commercial = $valeur;
            }
            $valeur = $this->input->post('ctc_livr_adresse');
            if (isset($valeur)) {
                $valeurs->ctc_livr_adresse = $valeur;
            }
            $valeur = $this->input->post('ctc_livr_cp');
            if (isset($valeur)) {
                $valeurs->ctc_livr_cp = $valeur;
            }
            $valeur = $this->input->post('ctc_livr_ville');
            if (isset($valeur)) {
                $valeurs->ctc_livr_ville = $valeur;
            }
            $valeur = $this->input->post('ctc_livr_horaire');
            if (isset($valeur)) {
                $valeurs->ctc_livr_horaire = $valeur;
            }
            $valeur = $this->input->post('ctc_livr_info');
            if (isset($valeur)) {
                $valeurs->ctc_livr_info = $valeur;
            }
            $listes_valeurs->ctc_origine = $this->m_contacts->liste_origine_prospect();
            $this->db->order_by('vac_activite', 'ASC');
            $q                            = $this->db->get('v_activites');
            $listes_valeurs->ctc_activite = $q->result();
            $this->db->order_by('vtdi_type', 'ASC');
            $q                                     = $this->db->get('v_types_distributions');
            $listes_valeurs->ctc_type_distribution = $q->result();
            $this->db->where("emp_fonction=1");
            $this->db->order_by('emp_nom', 'ASC');
            $q                                     = $this->db->get('t_employes');
            $listes_valeurs->ctc_commercial        = $q->result();
            $listes_valeurs->ctc_commercial_charge = $this->m_utilisateurs->liste_option();
            $listes_valeurs->ctc_enseigne          = $this->m_contacts->enseigne_liste();
            $listes_valeurs->ctc_statistiques      = $this->m_contacts->statistiques_liste();
            $listes_valeurs->ctc_signe             = $this->m_contacts->signe_liste($id);

            // descripteur
            $descripteur = array(
                'champs'  => array(
                    'ctc_nom'                  => array("Nom", 'text', 'ctc_nom', true),
                    'ctc_adresse'              => array("Adresse", 'textarea', 'ctc_adresse', false),
                    'ctc_cp'                   => array("Code postal", 'number', 'ctc_cp', false),
                    'ctc_ville'                => array("Ville", 'text', 'ctc_ville', false),
                    'ctc_complement'           => array("Complément adresse", 'text', 'ctc_complement', false),
                    'ctc_telephone'            => array("Téléphone", 'number', 'ctc_telephone', false),
                    'ctc_fax'                  => array("Fax", 'number', 'ctc_fax', false),
                    'ctc_mobile'               => array("Mobile", 'number', 'ctc_mobile', false),
                    'ctc_email'                => array("Email", 'email', 'ctc_email', false),
                    'ctc_site'                 => array("Site internet", 'url', 'ctc_site', false),
                    'ctc_activite'             => array("Activité", 'select', array('ctc_activite', 'vac_id', 'vac_activite'), false),
                    'ctc_commercial_charge'    => array("Commercial en charge", 'select', array('ctc_commercial_charge', 'utl_id', 'utl_login'), false),
                    'ctc_origine'              => array("Origine détaillée", 'select', array('ctc_origine', 'id', 'value'), false),                   
                    'ctc_enseigne'             => array("Enseigne", 'select', array('ctc_enseigne', 'id', 'value'), false),
                    'ctc_notes'                => array("Remarques", 'textarea', 'ctc_notes', false),
                    'ctc_qual_comm'            => array("Qualité commerciale", 'text', 'ctc_qual_comm', false),
                    'ctc_prospection'          => array("Statut prospection", 'text', 'ctc_prospection', false),
                    'ctc_marche'               => array("Marché", 'checkbox', 'ctc_marche', false),
                    'ctc_date_marche'          => array("Date marché", 'date', 'ctc_date_marche', false),
                    'ctc_alerte'               => array("Alerte", 'checkbox', 'ctc_alerte', false),
                    'ctc_date_alerte'          => array("Date alerte", 'date', 'ctc_date_alerte', false),
                    'ctc_remarques_sur_marche' => array("Remarques sur marché", 'textarea', 'ctc_remarques_sur_marche', false),
                    'ctc_statistiques'         => array("Statistiques", 'select', array('ctc_statistiques', 'id', 'value'), false),
                    'ctc_signe'         	   => array("Signe", 'select', array('ctc_signe', 'id', 'value'), false),
                    'ctc_fournisseur'          => array("Est fournisseur", 'checkbox', 'ctc_fournisseur', false),
                    'ctc_url'                  => array("URL espace client", 'url', 'ctc_url', false),
                    'ctc_info_connexion'       => array("Informations de connexion", 'textarea', 'ctc_info_connexion', false),
                    'ctc_login'                => array("Login", 'text', 'ctc_login', false),
                    'ctc_type_distribution'    => array("Type de distribution", 'select', array('ctc_type_distribution', 'vtdi_id', 'vtdi_type'), false),
                    'ctc_dist_hlm'             => array("Distribuer HLM", 'number', 'ctc_dist_hlm', false),
                    'ctc_dist_res'             => array("Distribuer HLM", 'number', 'ctc_dist_res', false),
                    'ctc_dist_pav'             => array("Distribuer HLM", 'number', 'ctc_dist_pav', false),
                    'ctc_stock'                => array("Stock actuel", 'number', 'ctc_stock', false),
                    'ctc_cycle_complet'        => array("Cycle complet", 'checkbox', 'ctc_cycle_complet', false),
                    'ctc_del_avant_distrib'    => array("Délai avant prochaine distribution", 'number', 'ctc_del_avant_distrib', false),
                    'ctc_poids'                => array("Poids", 'number', 'ctc_poids', false),
                    'ctc_doc_manuel'           => array("Document à ajouter à la main", 'checkbox', 'ctc_doc_manuel', false),
                    'ctc_sonner'               => array("Sonner aux interphones", 'checkbox', 'ctc_sonner', false),
                    'ctc_prix_rural'           => array("Prix rural", 'number', 'ctc_prix_rural', false),
                    'ctc_prix_urbain'          => array("Prix urbain", 'number', 'ctc_prix_urbain', false),
                    'ctc_prix_urgent'          => array("Prix urgent", 'number', 'ctc_prix_urgent', false),
                    'ctc_prix_cible'           => array("Prix cible", 'number', 'ctc_prix_cible', false),
                    'ctc_commercial'           => array("Commercial", 'select', array('ctc_commercial', 'emp_id', 'emp_nom'), true),
                    'ctc_livr_adresse'         => array("Adresse", 'textarea', 'ctc_livr_adresse', false),
                    'ctc_livr_cp'              => array("Code postal", 'number', 'ctc_livr_cp', false),
                    'ctc_livr_ville'           => array("Ville", 'text', 'ctc_livr_ville', false),
                    'ctc_livr_horaire'         => array("Horaires", 'text', 'ctc_livr_horaire', false),
                    'ctc_livr_info'            => array("Autres informations", 'textarea', 'ctc_livr_info', false),
                ),
                'onglets' => array(
                    array("Contact", array('ctc_nom', 'ctc_adresse', 'ctc_cp', 'ctc_ville', 'ctc_complement', 'ctc_telephone', 'ctc_fax', 'ctc_mobile', 'ctc_email', 'ctc_site', 'ctc_activite', 'ctc_commercial_charge', 'ctc_origine', 'ctc_enseigne', 'ctc_notes', 'ctc_qual_comm', 'ctc_prospection', 'ctc_marche', 'ctc_date_marche', 'ctc_alerte', 'ctc_date_alerte', 'ctc_remarques_sur_marche', 'ctc_statistiques', 'ctc_signe', 'ctc_fournisseur', 'ctc_url', 'ctc_info_connexion')),
                    array("Distribution", array('ctc_login', 'ctc_type_distribution', 'ctc_dist_hlm', 'ctc_dist_res', 'ctc_dist_pav', 'ctc_stock', 'ctc_cycle_complet', 'ctc_del_avant_distrib', 'ctc_poids', 'ctc_doc_manuel', 'ctc_sonner', 'ctc_prix_rural', 'ctc_prix_urbain', 'ctc_prix_urgent', 'ctc_prix_cible', 'ctc_commercial')),
                    array("Livraisons", array('ctc_livr_adresse', 'ctc_livr_cp', 'ctc_livr_ville', 'ctc_livr_horaire', 'ctc_livr_info')),
                ),
            );

            $data = array(
                'title'          => "Mise à jour d'un contact",
                'page'           => "templates/form",
                'menu'           => "Contacts|Mise à jour de contact",
                'barre_action'   => $this->barre_action["Edition"],
                'id'             => $id,
                'values'         => $valeurs,
                'action'         => "modif",
                'multipart'      => false,
                'confirmation'   => 'Enregistrer',
                'controleur'     => 'contacts',
                'methode'        => __FUNCTION__,
                'descripteur'    => $descripteur,
                'listes_valeurs' => $listes_valeurs,
            );
            $this->my_set_form_display_response($ajax, $data);
        }
    }

    /******************************
     * Détail du contact [CONTACT]
     ******************************/
    public function detail($id, $ajax = false)
    {
        $this->load->helper(array('form', 'ctrl'));
        if (count($_POST) > 0) {
            $redirection = $this->session->userdata('_url_retour');
            if (!$redirection) {
                $redirection = '';
            }

            redirect($redirection);
        } else {
            $valeurs = $this->m_contacts->detail($id);

            // contexte
            $this->session->set_userdata("CONTACT", "$valeurs->ctc_nom");

            // commandes globales
            $cmd_globales = array(
            );

            // commandes locales
            $cmd_locales = array(
            );

            $id_comptables = array();
            $this->load->model('m_id_comptable');
            foreach ($this->m_id_comptable->liste_par_contact($id) as $row) {
                $scv_id = $row->idc_societe_vendeuse;
                $id_comptables['ctc_id_comptable_'.$scv_id] = array('Id comptable pour '.$row->scv_nom, 'VARCHAR 30', 'text', 'ctc_id_comptable_'.$scv_id);
                $valeurs->{'ctc_id_comptable_'.$scv_id} = $row->idc_id_comptable;
            }

            // descripteur
            $descripteur = array(
                'champs'  => array_merge(array(
                    'ctc_nom'                  => array("Nom", 'VARCHAR 100', 'text', 'ctc_nom'),
                    'ctc_adresse'              => array("Adresse", 'VARCHAR 400', 'textarea', 'ctc_adresse'),
                    'ctc_cp'                   => array("Code postal", 'VARCHAR 5', 'number', 'ctc_cp'),
                    'ctc_ville'                => array("Ville", 'VARCHAR 50', 'text', 'ctc_ville'),
                    'ctc_complement'           => array("Complément adresse", 'VARCHAR 40', 'text', 'ctc_complement'),
                    'ctc_telephone'            => array("Téléphone", 'TELEPHONE', 'number', 'ctc_telephone'),
                    'ctc_fax'                  => array("Fax", 'VARCHAR 10', 'number', 'ctc_fax'),
                    'ctc_mobile'               => array("Mobile", 'TELEPHONE', 'number', 'ctc_mobile'),
                    'ctc_email'                => array("Email", 'EMAIL', 'email', 'ctc_email'),
                    'ctc_site'                 => array("Site internet", 'URL', 'url', 'ctc_site'),
                    'ctc_activite'             => array("Activité", 'REF', 'text', 'vac_activite'),
                    'origine_name'             => array("Origine détaillée", 'REF', 'text', 'origine_name'),
                    'ctc_origine_generale_nom' => array("Origine générale", 'VARCHAR 100', 'text', 'ctc_origine_generale_nom'),
                    'ctc_notes'                => array("Remarques", 'VARCHAR 1000', 'textarea', 'ctc_notes'),
                    'ctc_date_creation'        => array("Date de création", 'DATETIME', 'datetime', 'ctc_date_creation'),
                    'ctc_client_prospect'      => array("Type", 'REF', 'text', 'vcp_type'),
                    'ctc_qual_comm'            => array("Qualité commerciale", 'VARCHAR 40', 'text', 'ctc_qual_comm'),
                    'ctc_prospection'          => array("Statut prospection", 'VARCHAR 40', 'text', 'ctc_prospection'),
                    'ctc_marche'               => array("Marché", 'BOOL', 'checkbox', 'ctc_marche'),
                    'ctc_date_marche'          => array("Date Marché", 'DATETIME', 'datetime', 'ctc_date_marche'),
                    'ctc_alerte'               => array("Alerte", 'BOOL', 'checkbox', 'ctc_alerte'),
                    'ctc_date_alerte'          => array("Date alerte", 'DATETIME', 'datetime', 'ctc_date_alerte'),
                    'ctc_remarques_sur_marche' => array("Remarques sur marché", 'VARCHAR 500', 'textarea', 'ctc_remarques_sur_marche'),
                    'ctc_fournisseur'          => array("Est fournisseur", 'BOOL', 'checkbox', 'ctc_fournisseur'),
                    'ctc_url'                  => array("URL espace client", 'URL', 'url', 'ctc_url'),
                    'ctc_info_connexion'       => array("Informations de connexion", 'VARCHAR 200', 'textarea', 'ctc_info_connexion'),
                    'ctc_login'                => array("Login", 'VARCHAR 20', 'text', 'ctc_login'),
                    'ctc_type_distribution'    => array("Type de distribution", 'REF', 'text', 'vtdi_type'),
                    'ctc_dist_hlm'             => array("Distribuer HLM", 'INT 3', 'number', 'ctc_dist_hlm'),
                    'ctc_dist_res'             => array("Distribuer HLM", 'INT 3', 'number', 'ctc_dist_res'),
                    'ctc_dist_pav'             => array("Distribuer HLM", 'INT 3', 'number', 'ctc_dist_pav'),
                    'ctc_stock'                => array("Stock actuel", 'INT 6', 'number', 'ctc_stock'),
                    'ctc_cycle_complet'        => array("Cycle complet", 'BOOL', 'checkbox', 'ctc_cycle_complet'),
                    'ctc_del_avant_distrib'    => array("Délai avant prochaine distribution", 'INT 3', 'number', 'ctc_del_avant_distrib'),
                    'ctc_poids'                => array("Poids", 'INT 4', 'number', 'ctc_poids'),
                    'ctc_doc_manuel'           => array("Document à ajouter à la main", 'BOOL', 'checkbox', 'ctc_doc_manuel'),
                    'ctc_sonner'               => array("Sonner aux interphones", 'BOOL', 'checkbox', 'ctc_sonner'),
                    'ctc_prix_rural'           => array("Prix rural", 'DECIMAL 6,2', 'number', 'ctc_prix_rural'),
                    'ctc_prix_urbain'          => array("Prix urbain", 'DECIMAL 6,2', 'number', 'ctc_prix_urbain'),
                    'ctc_prix_urgent'          => array("Prix urgent", 'DECIMAL 6,2', 'number', 'ctc_prix_urgent'),
                    'ctc_prix_cible'           => array("Prix cible", 'DECIMAL 6,2', 'number', 'ctc_prix_cible'),
                    'ctc_commercial'           => array("Commercial", 'REF', 'ref', array('employes', 'ctc_commercial', 'emp_nom')),
                    'ctc_livr_adresse'         => array("Adresse", 'VARCHAR 400', 'textarea', 'ctc_livr_adresse'),
                    'ctc_livr_cp'              => array("Code postal", 'VARCHAR 5', 'number', 'ctc_livr_cp'),
                    'ctc_livr_ville'           => array("Ville", 'VARCHAR 50', 'text', 'ctc_livr_ville'),
                    'ctc_livr_horaire'         => array("Horaires", 'VARCHAR 40', 'text', 'ctc_livr_horaire'),
                    'ctc_livr_info'            => array("Autres informations", 'VARCHAR 400', 'textarea', 'ctc_livr_info'),
					'ctc_statistiques'         => array("Statistiques", 'BOOL', 'checkbox', 'ctc_statistiques'),
                    //'ctc_id_comptable'         => array("Id compta", 'INT 9', 'text', 'ctc_id_comptable'),
                ), $id_comptables),
                'onglets' => array(
                    array("Contact", array('ctc_nom', 'ctc_adresse', 'ctc_cp', 'ctc_ville', 'ctc_complement', 'ctc_telephone', 'ctc_fax', 'ctc_mobile', 'ctc_email', 'ctc_site', 'ctc_activite', 'origine_name', 'ctc_origine_generale_nom','ctc_notes', 'ctc_date_creation', 'ctc_client_prospect', 'ctc_qual_comm', 'ctc_prospection', 'ctc_marche', 'ctc_date_marche', 'ctc_alerte', 'ctc_date_alerte', 'ctc_remarques_sur_marche', 'ctc_fournisseur', 'ctc_url', 'ctc_info_connexion','ctc_statistiques')),
                    array("Comptabilité", array_keys($id_comptables)),
                    array("Distribution", array('ctc_login', 'ctc_type_distribution', 'ctc_dist_hlm', 'ctc_dist_res', 'ctc_dist_pav', 'ctc_stock', 'ctc_cycle_complet', 'ctc_del_avant_distrib', 'ctc_poids', 'ctc_doc_manuel', 'ctc_sonner', 'ctc_prix_rural', 'ctc_prix_urbain', 'ctc_prix_urgent', 'ctc_prix_cible', 'ctc_commercial')),
                    array("Livraisons", array('ctc_livr_adresse', 'ctc_livr_cp', 'ctc_livr_ville', 'ctc_livr_horaire', 'ctc_livr_info')),
                ),
            );

            $barre_action = $this->_masque_demasque_actions($this->barre_action["Contact"], $valeurs);

            $data = array(
                'title'        => "Détail du contact [CONTACT]",
                'page'         => "templates/detail",
                'menu'         => "Contacts|Contact",
                'barre_action' => $barre_action,
                'id'           => $id,
                'values'       => $valeurs,
                'controleur'   => 'contacts',
                'methode'      => __FUNCTION__,
                'cmd_globales' => $cmd_globales,
                'cmd_locales'  => $cmd_locales,
                'descripteur'  => $descripteur,
                'id_parent'    => 'ctc_id',
            );
            $this->my_set_display_response($ajax, $data);
        }
    }

    /**
     * Masque / démasque les actions dans la barre d'action
     *
     * @param $barre_action array      Barre d'action à modifier
     * @param $contact      M_contact  Les infos du contact
     *
     * @return array Nouvelle barre d'action
     */
    private function _masque_demasque_actions($barre_action, $contact)
    {
        $ctc_email = $contact->ctc_email;
        $etats     = array(
            'evenements/email_contact' => $ctc_email != '',
        );

        return modifie_etats_barre_action($barre_action, $etats);
    }

    /******************************
     * Archive Purchase Data
     ******************************/
    public function archive($id,$ajax=false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = 'contacts/detail/'.$id;
        if (!$redirection) {
            $redirection = '';
        }
        $resultat = $this->m_contacts->archive($id);
        if ($resultat === false) {
           $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                 'event' => array(
                     'controleur' => $this->my_controleur_from_class(__CLASS__),
                     'type'       => 'recorddelete',
                     'id'         => $id,
                     'timeStamp'  => round(microtime(true) * 1000),
                     'redirect'   => $redirection,
                 ),
             );
              $this->my_set_action_response($ajax, true, "Contact a été archive", 'info',$ajaxData);
        }
        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Suppression d'un contact
     * support AJAX
     ******************************/
    public function suppression($id, $ajax = false)
    {
        if ($this->input->method() != 'post') {
            die;
        }
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {
            $redirection = '';
        }

        $resultat = $this->m_contacts->remove($id);
        if ($resultat === false) {
            $this->my_set_action_response($ajax, false);
        } else {
            $ajaxData = array(
                'event' => array(
                    'controleur' => $this->my_controleur_from_class(__CLASS__),
                    'type'       => 'recorddelete',
                    'id'         => $id,
                    'timeStamp'  => round(microtime(true) * 1000),
                    'redirect'   => $redirection,
                ),
            );
            $this->my_set_action_response($ajax, true, "Le contact a été supprimé", 'info', $ajaxData);
        }

        if ($ajax) {
            return;
        }
        redirect($redirection);
    }

    /******************************
     * Non disponible
     ******************************/
    public function fichiers()
    {
        $data = array(
            'title'  => "Non disponible",
            'page'   => "non_implemente",
            'menu'   => "Contacts|Mise à jour de contact",
            'values' => array(
                'message' => "Cet écran permet d'effectuer les opérations de marketing (cf. base de gestion des contacts).",
            ),
        );
        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

	
    public function mass_archiver()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_contacts->archive($id);
        }
    }

    public function mass_remove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_contacts->remove($id);
        }
    }

    public function mass_unremove()
    {
        $ids = json_decode($this->input->post('ids'), true); //convert json into array
        foreach ($ids as $id) {
            $resultat = $this->m_contacts->unremove($id);
        }
    }

    public function generale_option($id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $resultat = $this->m_contacts->liste_option_by_generale($id);
        $results  = json_decode(json_encode($resultat), true);
        echo '<option value="0" selected="selected">(choisissez)</option>';
        foreach ($results as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
        }
    }

    public function origine_option($id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }
        $resultat = $this->m_contacts->liste_option_by_origine($id);
        $results  = json_decode(json_encode($resultat['result']), true);
        echo '<option value="0">(choisissez)</option>';
        foreach ($results as $row) {
            if ($resultat['sid'] == $row['id']) {
                echo "<option value='" . $row['id'] . "' selected='selected'>" . $row['value'] . "</option>";
            } else {
                echo "<option value='" . $row['id'] . "'>" . $row['value'] . "</option>";
            }
        }
    }

    public function document_generate_html($ajax)
    {
    	$this->load->library(array('html_doc','form_validation'));
        $config = array(
            array('field' => 'tpl_nom', 'label' => "Template Nom", 'rules' => 'trim|required'),
            array('field' => 'content', 'label' => "Content", 'rules' => 'required'),
        );


        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) 
        {
        	$ctc_ids    = $this->input->post('ctc_id');
    		$tpl_nom	= $this->input->post('tpl_nom');
    		$content	= $this->input->post('content');
    		
    		/**
    		 * Generate One Document with error handling using library Word
    		 */
    		try {
                $resultat = $this->html_doc->generate_contact_document($tpl_nom, $ctc_ids, $content);
                
        		$ajaxData = array(
                     'event' => array(
                         'controleur' => $this->my_controleur_from_class(__CLASS__),
                         'type' => 'recordchange',
                         'timeStamp' => round(microtime(true) * 1000),
                         'redirect' => $resultat['filename']
                     ),
                 );
                $this->my_set_action_response($ajax, true, $resultat['message'], 'info', $ajaxData);
                   
            } catch (MY_Exceptions_NoSuchFolder $e) {
                $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

            } catch (MY_Exceptions_NoSuchFile $e) {
                $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');

            } catch (MY_Exceptions_NoSuchRecord $e) {
                $this->my_set_action_response($ajax,false,$e->getMessage(),'warning');
            }
        } else {
            $this->my_set_action_response($ajax,false,validation_errors(),'warning');
        }
		
        if ($ajax) {return;}
        $redirection = $this->session->userdata('_url_retour');
        if (!$redirection) {$redirection = '';}
        redirect($redirection);
    }

    public function print_document($filename)
    {
    	$this->load->library('html_doc');
    	$data['path_file'] = $this->html_doc->get_path_document_contact($filename);
    	$this->load->view('contacts/print-document.php', $data);
    }

    public function id_comptables_json($contact, $enseigne = null)
    {
        if (!$this->input->is_ajax_request()) {
            die('');
        }

        $this->load->model('m_id_comptable');

        $filters = array(
            'idc_contact' => array('input' => $contact, 'type' => 'eq'),
        );
        if (!empty($enseigne)) {
            $filters['idc_societe_vendeuse'] = array('input' => $enseigne, 'type' => 'eq');
        }

        $filter_global = $this->input->post('filter_global');
        if (!empty($filter_global)) {

            // Ignore all other filters by resetting array
            $filters = array("_global" => $filter_global);
        }
        $resultat = $this->m_id_comptable->liste($limit = 1, $offset = 0, $filters);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

	public function is_having_factures($id){
		echo $this->m_contacts->is_having_factures($id);
	}
}
// EOF
