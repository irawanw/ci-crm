<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Date:
 * Time:
 */
class Ged extends CI_Controller
{
    private $profil;
    private $barre_action = array(
        array(
            "Upload File" => array('ged/nouveau', 'plus', true, 'ged_nouveau'),
        ),
        array(
            "Rename"    => array('ged/modification', 'pencil', false, 'ged_modification'),
            "Supprimer" => array('#', 'trash', false, 'ged_supprimer'),
            "Preview"   => array('#', 'eye-open', false, 'ged_preview'),
            "Download"  => array('#', 'download-alt', false, 'ged_download'),
        ),
        array(
            "Voir la liste" => array('#', 'th-list', true, 'voir_liste'),
        ),
        array(
            "Export xlsx" => array('#', 'list-alt', true, 'export_xls'),
            "Export pdf"  => array('#', 'book', true, 'export_pdf'),
            "Imprimer"    => array('#', 'print', true, 'print_list'),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('download');
        $this->load->model('m_ged');
    }

    public function index($id = 0, $liste = 0)
    {
        $this->liste($id = 0, '');
    }

    public function document($type = null, $option = '')
    {
        if ($type == null) {
            redirect('ged');
        } else {
            $param = '?';
            $type  = $type != "E-Mailing" ? str_replace("-", " ", $type) : $type;

            $param .= 'filter=1';
            $param .= '&document_type=' . $type;

            if ($option != '') {
                $option = $option . '/';
            }

            $this->liste($option . $param, '');
        }
    }

    public function liste($id = 0, $mode = 0)
    {
        // commandes globales
        $cmd_globales = array(
            //array("Ajouter une lignes","telephones/nouveau",'default')
        );

        // toolbar
        $toolbar = '';

        // descripteur
        $descripteur = array(
            'datasource'         => 'ged/index',
            'detail'             => array('ged/detail', 'id', 'description'),
            'archive'            => array('ged/archive', 'id', 'archive'),
            'champs'             => array(
                array('id', 'text', "ID", 'id'),
                array('format_file', 'txt', "Format File", 'format_file'),
                array('created_by', 'txt', "Created by", 'created_by'),
                array('uploaded_by', 'txt', "Uploaded by", 'uploaded_by'),
                array('name', 'text', "Name", 'name'),
                array('date_creation', 'date', "Upload Date", 'date_creation'),
                array('modified_date', 'date', "Modified Date", 'modified_date'),
                /*array('preview','text', "Preview", 'preview'),*/
            ),
            'filterable_columns' => $this->m_ged->liste_filterable_columns(),
        );

        $document_type_filter = $this->uri->segment(3) ? $this->uri->segment(3) : "All";

        if ($document_type_filter) {
            $descripteur['champs'] = $this->get_sub_document($document_type_filter, $descripteur['champs']);
        }

        $this->session->set_userdata('_url_retour', current_url());
        $scripts   = array();
        $scripts[] = $this->load->view("templates/datatables-js",
            array(
                'id'                    => $id,
                'descripteur'           => $descripteur,
                'toolbar'               => $toolbar,
                'controleur'            => 'ged',
                'methode'               => 'index',
                'mass_action_toolbar'   => true,
                'view_toolbar'          => true,
                'external_toolbar'      => 'custom-toolbar',
                'external_toolbar_data' => array(
                    'controleur' => 'ged',
                ),
            ), true);

        $scripts[] = $this->load->view("ged/liste-js", array(), true);
        //$scripts[] = $this->load->view("telephones/liste-js",array(),true);

        // listes personnelles
        $vues = $this->m_vues->vues_ctrl('ged', $this->session->id);
        $data = array(
            'title'        => "Liste suivi des GED",
            'page'         => "templates/datatables",
            'menu'         => "Extra|GED",
            'scripts'      => $scripts,
            'barre_action' => $this->barre_action,
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

    public function index_json($id = 0)
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
            $resultat = $this->m_ged->liste($id, $pagelength, $pagestart, $filters);
        } else {

            //list with requested ordering
            $order_col_id = $order[0]['column'];
            $ordering     = $order[0]['dir'];

            // tables for LINK columns
            $tables = array(
                'fileid' => 'oc_filecache',
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

                $resultat = $this->m_ged->liste($id, $pagelength, $pagestart, $filters, $order_col, $ordering);
            } else {
                $resultat = $this->m_ged->liste($id, $pagelength, $pagestart, $filters);
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($resultat));
    }

    public function nouveau()
    {
        $this->load->model('m_message_list');
        $document_types = $this->m_ged->document_type_options();
        $messages       = $this->m_message_list->simple_list();

        $scripts   = array();
        $scripts[] = $this->load->view("ged/form-add-js", array(), true);
        $data      = array(
            'title'        => "Ajouter un nouveau Owncloud Ged",
            'page'         => "ged/form-add",
            'menu'         => "Extra|Create Owncloud Ged",
            'action'       => "création",
            'values'       => array('document_types' => $document_types, 'messages' => $messages),
            'scripts'      => $scripts,
            'multipart'    => true,
            'confirmation' => 'Enregistrer',
            'controleur'   => 'Ged',
            'methode'      => 'create',
        );

        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

    public function modification($id)
    {
        $this->load->model('m_message_list');
        $document_types = $this->m_ged->document_type_options();
        $messages       = $this->m_message_list->simple_list();

        $scripts        = array();
        $scripts[]      = $this->load->view("ged/form-update-js", array('id' => $id), true);
        $data           = array(
            'title'        => "Ajouter un modification Owncloud Ged",
            'page'         => "ged/form-update",
            'menu'         => "Extra|Create Owncloud Ged",
            'action'       => "création",
            'values'       => array('document_types' => $document_types, 'messages' => $messages),
            'scripts'      => $scripts,
            'multipart'    => true,
            'confirmation' => 'Enregistrer',
            'controleur'   => 'Ged',
            'methode'      => 'create',
        );

        $layout = "layouts/standard";
        $this->load->view($layout, $data);
    }

    public function download($userid)
    {
        $filepath   = $this->input->get('path');
        $filepath   = preg_replace('# #', '%20', $filepath);
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        $url        = $domainName . "owncloud/remote.php/webdav/" . $filepath;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($userid == 1) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
        }

        if ($userid == 3) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        $temp     = preg_split('#/#', $filepath);
        $filename = end($temp);
        force_download($filename, $output);
    }

    public function supprimer($userid)
    {
        $filepath = $this->input->get('path');
        $filepath = preg_replace('# #', '%20', $filepath);
        $var      = array();
        $test     = '';
        if (strstr($filepath, '/')) {
            $var  = preg_split("#/#", $filepath);
            $test = 'document/' . $var[0];
        }
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        $url        = $domainName . "owncloud/remote.php/webdav/" . $filepath;
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($userid == 1) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
        }

        if ($userid == 3) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        //redirect('ged/document'.$url, 'refresh');
        redirect('ged/' . $test, 'location');
        /*$temp = preg_split('#/#', $filepath);
    $filename = end($temp);
    force_download($filename, $output);*/
    }

    // public function modification($userid)
    // {
    //     $filepath = $this->input->get('path');
    //     $filepath = preg_replace('# #', '%20', $filepath);
    //     $domainName = $_SERVER['HTTP_HOST'] . '/';
    //     $url = $domainName."owncloud/remote.php/webdav/".$filepath;
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     if($userid == 1)
    //         curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
    //     if($userid == 3)
    //         curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
    //     curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //     $output = curl_exec($ch);
    //     $info = curl_getinfo($ch);
    //     curl_close($ch);

    //     echo "<pre>";
    //     print_r($output);
    //     print_r($info);
    //     echo "</pre>";
    // }

    public function upload($userid = 1)
    {
        $status = false;

        if ($this->upload_validation()) {
            $total         = count($_FILES['file']['name']);
            $document_type = $this->input->post('document-type');
            $document_type = preg_replace('# #', '%20', $document_type);
            $date          = date("Y-m-d");
            $success       = 0;

            // Loop through each file
            for ($i = 0; $i < $total; $i++) {
                $filename     = $_FILES['file']['name'][$i];
                $filename     = preg_replace('# #', '%20', $filename);
                $format_file  = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                $file_content = file_get_contents($_FILES['file']['tmp_name'][$i]);
                $size_file    = $_FILES['file']['size'][$i];

                $domainName = $_SERVER['HTTP_HOST'] . '/';
                $url        = $domainName . "owncloud/remote.php/webdav/" . $document_type . "/" . $filename;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($userid == 1) {
                    curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
                }

                if ($userid == 3) {
                    curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
                }

                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                $info = curl_getinfo($ch);
                curl_close($ch);

                $headers  = explode(PHP_EOL, $result);
                $idString = $headers[13];

                if (preg_match("/OC-FileId/", $idString)) {
                    /**
                     * Create Metadata File
                     */
                    $idString  = str_replace(" ", "", $idString);
                    $idStrings = explode(":", $idString);
                    $fileId    = substr($idStrings[1], 0, 8);
                    $fileId    = ltrim($fileId, '0');

                    $data = array(
                        "action"       => "create",
                        "userId"       => "admin",
                        "objectId"     => $fileId,
                        "formatFile"   => $format_file,
                        "dateCreation" => $date,
                        "sizeFile"     => $size_file,
                        "createdBy"    => $this->input->post('createdby-name'),
                        "uploadedBy"   => $this->input->post('uploadedby-name'),
                        "description"  => $this->input->post('description'),
                        "documentType" => $this->input->post('document-type'),
                    );

                    $metadata_string = "";
                    $metadata        = $this->get_metadata($data);
                    $url_metadata    = $domainName . "owncloud/index.php/apps/ownnotes/api/0.1/metadata";

                    foreach ($metadata as $key => $value) {$metadata_string .= $key . '=' . $value . '&';}
                    rtrim($metadata_string, '&');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url_metadata);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    if ($userid == 1) {
                        curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
                    }

                    if ($userid == 3) {
                        curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
                    }

                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $metadata);

                    $result_metadata = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    }

                    $info_metadata = curl_getinfo($ch);
                    curl_close($ch);

                    $success++;

                } else {

                }
            }

            //set success message
            if ($success > 0) {
                $this->session->set_flashdata('success', $success . " files Owncloud Ged a été enregistré avec succès");
                $status = true;
            } else {
                if (null === $this->session->flashdata('danger')) {
                    $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
                }

                $status = false;
            }
        } else {
            $status = false;
        }

        echo json_encode(array('status' => $status));
    }

    public function get_metadata($data)
    {
        switch ($this->input->post('document-type')) {
            case 'Infographie':
                $data['clientId']      = $this->input->post('client');
                $data['clientName']    = $this->input->post('client-name');
                $data['devisId']       = $this->input->post('devis');
                $data['devis']         = $this->input->post('devis-name');
                $data['factureId']     = $this->input->post('facture');
                $data['facture']       = $this->input->post('facture-name');
                $data['sousType']      = $this->input->post('sous-type');
                $data['formatOuvert']  = $this->input->post('format-ouvert');
                $data['formatFerme']   = $this->input->post('format-ferme');
                $data['largeur']       = $this->input->post('largeur');
                $data['longueur']      = $this->input->post('longueur');
                $data['nombreDePages'] = $this->input->post('nombre-pages');
                $data['campagne']      = $this->input->post('campagne');
                $data['objet']         = $this->input->post('objet');
                $data['nomDeDomaine']  = $this->input->post('nomde-domaine');
                $data['origine']       = $this->input->post('origine');
                $data['sujet']         = $this->input->post('sujet');
                break;
            case 'Plan':
                $data['sousType'] = $this->input->post('soustype-plan');
                $data['ville']    = $this->input->post('ville');
                $data['numero']   = $this->input->post('numero');
                $data['mois']     = $this->input->post('mois');
                break;
            case 'Piece Comptable':
                $data['sousType'] = $this->input->post('soustype-piece-comptable');
                $data['societe']  = $this->input->post('societe');
                $data['banque']   = $this->input->post('banque');
                $data['numero']   = $this->input->post('numero');
                $data['mois']     = $this->input->post('mois');
                break;
            case 'Facture Client':
                $data['societe'] = $this->input->post('societe');
                $data['numero']  = $this->input->post('numero');
                break;
            case 'Devis Client':
                $data['societe'] = $this->input->post('societe');
                $data['numero']  = $this->input->post('numero');
                break;
            case 'E-Mailing':
                $data['messageId']    = $this->input->post('message');
                $data['messageName']  = $this->input->post('message-name');
                $data['bassDeDonnee'] = $this->input->post('bass-de-donnee');
                break;
            case 'Site Internet':
                $data['sousType']   = $this->input->post('soustype-site-internet');
                $data['clientId']   = $this->input->post('client');
                $data['clientName'] = $this->input->post('client-name');
                $data['devisId']    = $this->input->post('devis');
                $data['devis']      = $this->input->post('devis-name');
                $data['factureId']  = $this->input->post('facture');
                $data['facture']    = $this->input->post('facture-name');
                break;
            case 'Administratif Divers':
                $data['sousType']              = $this->input->post('soustype-administratifs-divers');
                $data['annee']                 = $this->input->post('annee');
                $data['societe']               = $this->input->post('societe');
                $data['nomOrganisme']          = $this->input->post('nom-organisme');
                $data['assemblees']            = $this->input->post('assemblees');
                $data['statutsAssembleesType'] = $this->input->post('statuts-assemblees-type');
                $data['sousTraitantsType']     = $this->input->post('sous-traitants-type');

                break;
            case 'Developpement':
                $data['clientId']   = $this->input->post('client');
                $data['clientName'] = $this->input->post('client-name');
                $data['sousType']   = $this->input->post('soustype-development');
                $data['societe']    = $this->input->post('societe');
                break;
            case 'Ressources Humaines':
                $data['sousType']        = $this->input->post('soustype-ressources-humaines');
                $data['salarie']         = $this->input->post('salarie');
                $data['societe']         = $this->input->post('societe');
                $data['annee']           = $this->input->post('annee');
                $data['recruitmentType'] = $this->input->post('recruitment-type');
                break;
            case 'Commercial':
                $data['sousType']    = $this->input->post('soustype-commercial');
                $data['clientId']    = $this->input->post('client');
                $data['clientName']  = $this->input->post('client-name');
                $data['article']     = $this->input->post('article');
                $data['departement'] = $this->input->post('departement');
                break;
            default:
                break;
        }

        return $data;
    }

    public function upload_validation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('createdby', 'Created by', 'trim|required');
        $this->form_validation->set_rules('uploadedby', 'Uploaded by', 'trim|required');
        $this->form_validation->set_rules('document-type', 'Type document', 'trim|required');

        if ($this->form_validation->run() == true) {
            if (count($_FILES['file']['name']) == 0) {
                $this->session->set_flashdata('danger', "No Selected File");
                return false;
            } else {
                return true;
            }
        } else {
            $this->session->set_flashdata('danger', validation_errors());
            return false;
        }
    }

    public function get_preview($userid)
    {

        $filepath    = $this->input->get('path');
        $filepath    = preg_replace('# #', '%20', $filepath);
        $domainName  = $_SERVER['HTTP_HOST'] . '/';
        $url         = $domainName . "owncloud/remote.php/webdav/" . $filepath;
        $format_file = $this->input->get('format');
        $file_name   = $this->input->get('name');
        $file_name   = preg_replace('# #', '%20', $file_name);
        $image_type  = array('png', 'jpeg', 'jpg', 'bmp');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($userid == 1) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
        }

        if ($userid == 3) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);

        $file_result = base64_encode($output);

        if (in_array($format_file, $image_type)) {
            echo '<img src="data:image/jpeg;base64,' . $file_result . '"/>';
        } else if ($format_file == 'pdf') {
            //echo '<iframe height="768" width="1024" src="data:application/pdf;base64,'.$file_result.'"></iframe>';
            echo '<embed src="data:application/pdf;base64,' . $file_result . '" width="800px" height="2100px" />';
            /*$files = glob('./tmp/oc/*'); // get all file names
            foreach($files as $file){ // iterate files
            if(is_file($file))
            unlink($file); // delete file
            }

            $this->load->helper('file');

            $path = FCPATH."tmp/oc/";*/
            //echo '<embed src="'.base_url().'temp/oc/'.$file_name.'" width="800px" height="2100px" />;'
            //echo $path.$file_name;exit();

            /*if (!is_dir($path)) {
            echo "is dir";
            }else{

            if (mkdir($path, 0777, TRUE)) {
            echo "berhasil membuat folder";
            }else{
            echo "gagal membuat folder";
            }
            }

            exit();*/

            /*if ( write_file($path.$file_name, $output, 'x'))
        {
        echo '<iframe height="768" width="1024" src="'.base_url().'tmp/oc/'.$file_name.'"></iframe>';
        }else{
        echo "could not upload file";
        }*/
        } else {
            $temp     = preg_split('#/#', $filepath);
            $filename = end($temp);

            force_download($filename, $output);
        }
    }

    public function get_sub_document($type, $champs)
    {
        $sub_champs = array();

        switch ($type) {
            case 'Infographie':
                $champs[] = array('client_name', 'text', "Client Name", 'client_name');
                $champs[] = array('devis', 'text', "Devis", 'devis');
                $champs[] = array('facture', 'text', "Facture", 'facture');
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('format_ouvert', 'text', "Format Ouvert", 'format_ouvert');
                $champs[] = array('format_ferme', 'text', "Format Ferme", 'format_ferme');
                $champs[] = array('largeur', 'text', "Largeur", 'largeur');
                $champs[] = array('longueur', 'text', "Longueur", 'longueur');
                $champs[] = array('nombre_de_pages', 'text', "Nombre De Pages", 'nombre_de_pages');
                $champs[] = array('campagne', 'text', "Campagne", 'campagne');
                $champs[] = array('objet', 'text', "Objet", 'objet');
                $champs[] = array('nom_de_domaine', 'text', "Nom De Domaines", 'nom_de_domaine');
                $champs[] = array('origine', 'text', "Origine", 'origine');
                $champs[] = array('sujet', 'text', "Sujet", 'sujet');

                break;
            case 'Plan':
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('ville', 'text', "Ville", 'ville');
                $champs[] = array('numero', 'text', "Numero", 'numero');
                $champs[] = array('mois', 'text', "Mois", 'mois');
                break;
            case 'Piece-Comptable':
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('banque', 'text', "Banque", 'banque');
                $champs[] = array('mois', 'text', "Mois", 'mois');
                break;
            case 'Facture-Client':
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('numero', 'text', "Numero", 'numero');
                break;
            case 'Devis-Client':
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('numero', 'text', "Numero", 'numero');
                break;
            case 'E-Mailing':                
                $champs[] = array('message_name', 'text', "Message", 'message_name');
                $champs[] = array('bass_de_donnee', 'text', "Bass de donnee", 'bass_de_donnee');
                break;
            case 'Site-Internet':
                $champs[] = array('client_name', 'text', "Client Name", 'client_name');
                $champs[] = array('devis', 'text', "Devis", 'devis');
                $champs[] = array('facture', 'text', "Facture", 'facture');
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                break;
            case 'Administratif-Divers':
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('annee', 'text', "Annee", 'annee');
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('nom_organisme', 'text', "Nom Organisme", 'nom_organisme');
                $champs[] = array('statuts_assemblees_type', 'text', "Statuts assemblees type", 'statuts_assemblees_type');
                $champs[] = array('sous_traitants_type', 'text', "Sous traitants type", 'sous_traitants_type');
                $champs[] = array('assemblees', 'text', "Assemblees", 'assemblees');

                break;
            case 'Developpement':
                $champs[] = array('client_name', 'text', "Client Name", 'client_name');
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('societe', 'text', "Societe", 'societe');
                break;
            case 'Ressources-Humaines':
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('salarie', 'text', "Salarie", 'salarie');
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('recruitment_type', 'text', "Recruitment Type", 'recruitment_type');
                $champs[] = array('annee', 'text', "Annee", 'annee');
                break;
            case 'Commercial':
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('client_name', 'text', "Client Name", 'client_name');
                $champs[] = array('article', 'text', "Article", 'article');
                $champs[] = array('departement', 'text', "Departement", 'departement');
                break;
            case 'All':
                $champs[] = array('client_name', 'text', "Client Name", 'client_name');
                $champs[] = array('devis', 'text', "Devis", 'devis');
                $champs[] = array('facture', 'text', "Facture", 'facture');
                $champs[] = array('sous_type', 'text', "Sous Type", 'sous_type');
                $champs[] = array('format_ouvert', 'text', "Format Ouvert", 'format_ouvert');
                $champs[] = array('format_ferme', 'text', "Format Ferme", 'format_ferme');
                $champs[] = array('largeur', 'text', "Largeur", 'largeur');
                $champs[] = array('longueur', 'text', "Longueur", 'longueur');
                $champs[] = array('nombre_de_pages', 'text', "Nombre De Pages", 'nombre_de_pages');
                $champs[] = array('campagne', 'text', "Campagne", 'campagne');
                $champs[] = array('objet', 'text', "Objet", 'objet');
                $champs[] = array('nom_de_domaine', 'text', "Nom De Domaines", 'nom_de_domaine');
                $champs[] = array('origine', 'text', "Origine", 'origine');
                $champs[] = array('sujet', 'text', "Sujet", 'sujet');
                $champs[] = array('ville', 'text', "Ville", 'ville');
                $champs[] = array('numero', 'text', "Numero", 'numero');
                $champs[] = array('mois', 'text', "Mois", 'mois');
                $champs[] = array('societe', 'text', "Societe", 'societe');
                $champs[] = array('banque', 'text', "Banque", 'banque');
                $champs[] = array('nom', 'text', "Nom", 'nom');
                $champs[] = array('piece', 'text', "Piece", 'piece');
                $champs[] = array('statuts', 'text', "Statuts", 'statuts');
                $champs[] = array('assemblees', 'text', "Assemblees", 'assemblees');
                $champs[] = array('descriptif', 'text', "Descriptif", 'descriptif');
                $champs[] = array('format', 'text', "Format", 'format');
                $champs[] = array('bass_de_donnee', 'text', "Bass de donnee", 'bass_de_donnee');
                break;
            default:
                # code...
                break;
        }

        return $champs;
    }

    public function get_user()
    {
        $this->load->model('m_utilisateurs');
        $users = $this->m_utilisateurs->get_all(null, "asc");

        echo json_encode($users);
    }

    public function get_client()
    {
        $this->load->model('m_contacts');
        $users = $this->m_contacts->get_all(null, "asc");

        echo json_encode($users);
    }

    public function get_devis($clientId)
    {
        $this->load->model('m_devis');
        $clientId = (int) $clientId;
        $devises  = $this->m_devis->get_all_by_client(null, "asc", $clientId);

        echo json_encode($devises);
    }

    public function get_facture($devisId)
    {
        $this->load->model('m_factures');
        $devisId  = (int) $devisId;
        $factures = $this->m_factures->get_all_by_devis(null, "asc", $devisId);

        echo json_encode($factures);
    }

    public function get_detail($id)
    {
        $data = $this->m_ged->detail($id);

        if ($data) {
            echo json_encode(array('status' => true, 'data' => $data));
        } else {
            echo json_encode(array('status' => false));
        }
    }

    public function update($id)
    {
        $status        = false;
        $domainName    = $_SERVER['HTTP_HOST'] . '/';
        $userid        = 1;
        $time          = strtotime($this->input->post('date-creation'));
        $date          = date('Y-m-d', $time);
        $modified_date = date("Y-m-d");

        $detail = $this->m_ged->detail($this->input->post('file-id'));

        /** Update File Metadata **/
        $data = array(
            "action"       => "update",
            "userId"       => $this->input->post('user-id'),
            "objectId"     => $this->input->post('file-id'),
            "formatFile"   => $this->input->post('format-file'),
            "dateCreation" => $date,
            "sizeFile"     => $this->input->post('size-file'),
            "createdBy"    => $this->input->post('createdby-name'),
            "uploadedBy"   => $this->input->post('uploadedby-name'),
            "description"  => $this->input->post('description'),
            "documentType" => $this->input->post('document-type'),
            "modifiedDate" => $modified_date,
        );

        $metadata_string = "";
        $metadata        = $this->get_metadata($data);
        $url_metadata    = $domainName . "owncloud/index.php/apps/ownnotes/api/0.1/metadata";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_metadata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($userid == 1) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
        }

        if ($userid == 3) {
            curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $metadata);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
            $status = false;

            echo "Error curl";
        }

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status_code == 200) {
            $status = true;
        } else {
            echo "Error status code";
            $status = false;
        }
        /** Eof Update File Metadata **/

        /** Rename File if filename changed **/
        $document_type = preg_replace('# #', '%20', $data['documentType']);
        $filepath      = preg_replace("#files/#", "", $detail->path);
        $filepath      = preg_replace("# #", "%20", $filepath);
        $file_name     = $this->input->post('file-name');
        $file_ext      = $this->input->post('file-extension');
        $new_filename  = $file_name . $file_ext;

        if ($detail->name != $new_filename || $detail->document_type != $data['documentType']) {
            $url_rename_file = $domainName . "owncloud/remote.php/webdav/" . $filepath;
            $destination     = $domainName . "owncloud/remote.php/webdav/" . $document_type . "/" . $new_filename;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_rename_file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "MOVE");
            if ($userid == 1) {
                curl_setopt($ch, CURLOPT_USERPWD, "admin:123456");
            }
            if ($userid == 3) {
                curl_setopt($ch, CURLOPT_USERPWD, "admin.123456");
            }
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $headers = array(
                'Destination: http://' . $destination,
                'Overwrite: F',
            );

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result      = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status_code == 201) {
                $status = true;
            } else {
                $status = false;
            }
        }
        /** EofRename File if filename changed **/

        if ($status == true) {
            $this->session->set_flashdata('success', "file Owncloud Ged a été enregistré avec succès");
        } else {
            $this->session->set_flashdata('danger', "Un problème technique est survenu - veuillez reessayer ultérieurement");
        }

        echo json_encode(array('status' => $status));

    }

}
