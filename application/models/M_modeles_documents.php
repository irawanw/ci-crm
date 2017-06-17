<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_modeles_documents extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('mod_nom','text',"Nom"),
                array('vtm_type_modele','ref',"Type de modèle",'v_types_modeles_documents'),
                array('mod_description','text',"Description"),
                array('mod_fichier','fichier',"Fichier"),
                array('vfd_famille_modele','ref',"Famille de modèle",'v_familles_modeles_documents'),
                array('dsq_nom','ref',"Disque de stockage",'disques_archivage','mod_disque','dsq_nom'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'mod_nom' => array("Nom",'text','mod_nom',true),
               'mod_type' => array("Type de modèle",'select',array('mod_type','vtm_id','vtm_type_modele'),true),
               'mod_famille' => array("Famille de modèle",'radio-h',array('mod_famille','vfd_id','vfd_famille_modele'),true),
               'mod_description' => array("Description",'textarea','mod_description',true),
               'mod_sujet' => array("Sujet",'text','mod_sujet',false),
               'mod_texte' => array("Texte",'textarea','mod_texte',false),
               'mod_fichier' => array("Fichier",'upload','.doc,.docx',false)
            )
        );

        return $champs[$type];
    }
    /******************************
    * Liste des modèles de documents
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {
		
		$table = 't_modeles_documents';
        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $mod_nom = formatte_sql_lien('modeles_documents/detail','mod_id','mod_nom');
        $dsq_nom = formatte_sql_lien('disques_archivage/detail','dsq_id','dsq_nom');
        $this->db->select("mod_id AS RowID,mod_id,$mod_nom,vtm_type_modele,mod_description,mod_fichier,vfd_famille_modele,$dsq_nom,dsq_chemin",false);
        $this->db->join('v_types_modeles_documents','vtm_id=mod_type','left');
        $this->db->join('v_familles_modeles_documents','vfd_id=mod_famille','left');
        $this->db->join('t_disques_archivage','dsq_id=mod_disque','left');
        $this->db->where('mod_inactif is null');
        //$this->db->order_by("mod_nom asc");
		$id = intval($void);
        if ($id > 0) {
         $this->db->where('mod_id', $id);
        }
        $this->db->stop_cache();
        // aliases
        $aliases = array();

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'mod_nom'=>'char',
            'vtm_type_modele'=>'char',
            'mod_description'=>'char',
            'mod_fichier'=>'char',
            'vfd_famille_modele'=>'char',
            'dsq_nom'=>'char',
            'dsq_chemin'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un modèle de documents
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("mod_id,mod_nom,mod_type,vtm_type_modele,mod_famille,vfd_famille_modele,mod_description,mod_sujet,mod_texte,mod_fichier,mod_disque,dsq_nom,dsq_chemin",false);
        $this->db->join('v_types_modeles_documents','vtm_id=mod_type','left');
        $this->db->join('v_familles_modeles_documents','vfd_id=mod_famille','left');
        $this->db->join('t_disques_archivage','dsq_id=mod_disque','left');
        if (is_numeric($id)) {
            $this->db->where('mod_id',$id);
        } elseif (is_string($id)) {
            $this->db->where('mod_nom',$id);
        } elseif (is_array($id)) {
            foreach ($id as $column => $value) {
                $this->db->where($column, $value);
            }
        }
        $this->db->where('mod_inactif is null');
        $q = $this->db->get('t_modeles_documents');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
     * Liste des documents générés
     ******************************/
    public function documents_generes($commande) {
        switch ($commande) {
            case 'get':
                $this->load->helper('directory');
                $map = directory_map("fichiers/generes",1);
                rsort($map);
                $liste = array();
                foreach ($map as $nom_fichier) {
                    if ($nom_fichier != "index.html") {
                        $fichier = explode('.',$nom_fichier);
                        $champs = explode('_',$fichier[0]);
                        if (count($champs) == 6) {
                            $data = new StdClass();
                            $data->type = $champs[0];
                            $data->client = $champs[1];
                            $data->piece = $champs[2];
                            $date = $champs[3];
                            $data->date = substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6);
                            $heure = $champs[4];
                            $data->heure = substr($heure,0,2).':'.substr($heure,2,2).':'.substr($heure,4);
                            $data->utilisateur = $champs[5];
                            $url = base_url("fichiers/generes/$nom_fichier");
                            $data->fichier = "<a href=\"$url\" target=\"_blank\">$nom_fichier</a>";
                            $liste[] = $data;
                        }
                    }
                }
                return $liste;
                break;
            default: return false;
        }
    }

    /******************************
     * Mise à jour d'un modèle de documents
     ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('mod_id',$id)
            ->get('t_modeles_documents');
        if ($q->num_rows() > 0) {
            $data2 = $q->row();

            // prise en charge du fichier
            $ancien = $data2->mod_fichier;
            $nouveau = $data['mod_fichier']->nom;
            if ($ancien != '' AND $nouveau != '') {

                // mise à jour
                $nom_modele = $this->mise_a_jour($ancien,$nouveau);
                $data['mod_fichier'] = $nom_modele;
            }
            else if ($ancien == '' AND $nouveau != '') {

                // insertion
                $nom_modele = $this->importation($nouveau,$data['mod_fichier']->extension);
                $data['mod_fichier'] = $nom_modele;
            }
            else {
                unset ($data['mod_fichier']);
            }
            return $this->_update('t_modeles_documents',$data,$id,'mod_id');
        }
        return false;
    }

    /******************************
     * Ajout d'un modèle de documents
     ******************************/
    public function nouveau($data) {
        $nom_modele = '';

        // transfert éventuel du fichier
        if ($data['mod_fichier']->nom != '') {
            $nom_modele = $this->importation($data['mod_fichier']->nom,$data['mod_fichier']->extension);
        }
        $data['mod_fichier'] = $nom_modele;
        $this->load->model('m_disques_archivage');
        $data['mod_disque'] = $this->m_disques_archivage->id_modeles();
        return $this->_insert('t_modeles_documents',$data);
    }

    /******************************
     * Importation de modèle
     ******************************/
    public function importation($fichier,$extension) {
        $this->load->model('m_disques_archivage');
        $rep_modeles = $this->m_disques_archivage->rep_modeles();
        $nom = $this->genere_nom($extension);
        copy($fichier,$rep_modeles.$nom);
        return $nom;
    }

    /******************************
     * Mise à jour de modèle
     ******************************/
    public function mise_a_jour($nom,$fichier) {
        $this->load->model('m_disques_archivage');
        $rep_modeles = $this->m_disques_archivage->rep_modeles();
        copy($fichier,$rep_modeles.$nom);
        return $nom;
    }

    /******************************
     * Fusion texte
     ******************************/
    public function fusion_texte($modele,$fichier,$data,$email='',$sujet='') {
        $data->date = date('d/m/Y');
        $this->load->model('m_disques_archivage');
        $rep_generes = $this->m_disques_archivage->rep_generes();
        foreach($data as $k=>$v) {
            $modele = str_replace('${'.$k.'}',$data->$k,$modele);
            $sujet = str_replace('${'.$k.'}',$data->$k,$sujet);
        }
        if ($email != '') {
            $this->load->library('email');
            $resultat = $this->email->send_one($email,'',$sujet,$modele);
        }
        else {
            file_put_contents($rep_generes.$fichier,$modele);
            $resultat = true;
        }
        return $resultat;
    }

    /******************************
     * Fusion word
     ******************************/
    public function fusion_word($modele,$fichier,$data) {
        $data->date = date('d/m/Y');
        $this->load->model('m_disques_archivage');
        $rep_generes = $this->m_disques_archivage->rep_generes();
        $rep_modeles = $this->m_disques_archivage->rep_modeles();
        if ($modele == '') return false;
        if (! file_exists($rep_modeles.$modele)) return false;
        require 'application/third_party/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($rep_modeles.$modele);
        foreach($data as $k=>$v) {
            $templateProcessor->setValue($k,$data->$k);
        }
        $templateProcessor->saveAs($rep_generes.$fichier);
        /*
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(getcwd().'/application/third_party/mpdf60');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('MPDF');
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($rep_generes.$fichier);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');
        $xmlWriter->save('result.pdf');
         */
        return true;
    }

    /******************************
     * Fusion word et pdf
     ******************************/
    public function fusion_word_pdf($modele,$fichier,$data) {
        $data->date = date('d/m/Y');
        $this->load->model('m_disques_archivage');
        $rep_generes = $this->m_disques_archivage->rep_generes();
        $rep_modeles = $this->m_disques_archivage->rep_modeles();
        if ($modele == '') return false;
        if (! file_exists($rep_modeles.$modele)) return false;
        require 'application/third_party/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($rep_modeles . $modele);
        foreach ($data as $k => $v) {
            $templateProcessor->setValue($k, $data->$k);
        }
        $fichier_doc = $rep_generes.$fichier.'.docx';
        $fichier_pdf = $rep_generes.$fichier.'.pdf';
        $templateProcessor->saveAs($fichier_doc);
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(getcwd() . '/application/third_party/mpdf60');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('MPDF');
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($fichier_doc);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $xmlWriter->save($fichier_pdf);
        return array($fichier_doc,$fichier_pdf);
    }

    /******************************
     * Génération d'un nom de fichier
     ******************************/
    private function genere_nom($extension) {
        $user = $this->session->id;
        $prefixe = 'mdl_'.$user.'_';
        return uniqid($prefixe).$extension;
    }

	/******************************
    * Archive test mails data
    ******************************/
    public function remove($id) {
        return $this->_delete('t_modeles_documents',$id,'mod_id','mod_inactif');
    }

}
// EOF
