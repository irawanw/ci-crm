<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date:
* Time:
*/
class M_ged extends MY_Model {

    public function __construct() {
        parent::__construct();
		$this->db = $this->load->database('owncloud', TRUE);
    }


    /******************************
    * Liste test mails Data
    ******************************/
    public function liste($void=NULL, $limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 'oc_filecache fc';
        // première partie du select, mis en cache
        $this->db->start_cache();

		$this->db->select("		
					fc.`storage`,
                    fm.`format_file`,
                    fm.`id` as `RowID`,
                    fm.`created_by`,
                    fm.`uploaded_by`,
                    fm.`format_file`,
                    mm.`mimetype` as `type`,
                    `path`,
                    `fc`.`size`,
                    `mtime` as `modified`,
                    '' as mime_type,
                    `permissions`,
                    `fileid` as `id`,
                    `name`,
                    `path` as `link`,
                    fm.`client_name`,
                    fm.`devis` as `devis`,
                    fm.`facture` as `facture`,
                    fm.`sous_type`,
                    fm.`format_ouvert`,
                    fm.`format_ferme`,
                    fm.`largeur`,
                    fm.`longueur`,
                    fm.`nombre_de_pages`,
                    fm.`campagne`,
                    fm.`objet`,
                    fm.`nom_de_domaine`,
                    fm.`origine`,
                    fm.`sujet`,
                    fm.`ville`,
                    fm.`numero`,
                    fm.`mois`,
                    fm.`societe`,
                    fm.`banque`,
                    fm.`nom`,
                    fm.`piece`,
                    fm.`statuts`,
                    fm.`assemblees`,
                    fm.`descriptif`,
                    fm.`format`,
                    fm.`message_id`,
                    fm.`message_name`,
                    fm.`bass_de_donnee`,
                    fm.`salarie`,
                    fm.`annee`,
                    fm.`article`,
                    fm.`departement`,
                    fm.`recruitment_type`,
                    fm.`statuts_assemblees_type`,
                    fm.`nom_organisme`,
                    fm.`sous_traitants_type`,
                    fm.`date_creation`,
                    fm.`modified_date`,
                    mm.`mimetype` as `mime`");
        $this->db->join('oc_ownnotes_notes fm', 'fc.fileid = fm.object_id', 'left');
		$this->db->join('oc_mimetypes mm', 'fc.mimetype = mm.id', 'left');
		$this->db->where('path LIKE "%.%"');
		$this->db->where('path NOT LIKE "thumbnail%"');
		$this->db->where('path NOT LIKE "%trash%"');

        if ($this->input->get('filter') == 1) {
            $document_type_filter = $this->input->get('document_type');

            $this->db->like('fm.document_type', $document_type_filter);
        }

		$this->db->stop_cache();
        // aliases
        $aliases = array(

        );
        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
			$link = $resultat['data'][$i]->link;
			$link = preg_replace("#files/#", "", $link);
			$storage = $resultat['data'][$i]->storage;
            //$resultat['data'][$i]->link = '<a href="'.site_url('ged/download/'.$storage.'/?path='.$link).'">Download</a>';

            //add btn preview
            
            $image_type = array('png', 'jpeg', 'jpg', 'bmp', 'pdf');
           	$name = str_replace(" ", "_", $resultat['data'][$i]->name);
            $name_arr = explode(".", $name);
            $length_name = count($name_arr);
            $format_file = $name_arr[$length_name - 1];
            $link_preview = str_replace(" ", "%20", $link);
           	if(in_array($format_file, $image_type)) {
                $resultat['data'][$i]->preview = '<a class="preview-image" data-fancybox-type="ajax" href="' . site_url('ged/get_preview/' . $storage . '/?path=' . $link_preview.'&format='.$format_file.'&name='.$name) . '">Preview</a>';    
            } else {
                $resultat['data'][$i]->preview = '<a class="preview-file" href="' . site_url('ged/get_preview/' . $storage . '/?path=' . $link.'&format='.$format_file.'&name='.$name) . '">Preview</a>'; 
            }   
        }  

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
			'format_file'     => 'char',
            'created_by'      => 'char',
            'uploaded_by'     => 'char',
            'client_name'     => 'char',
            'name'            => 'char',
            'devis'           => 'char',
            'facture'         => 'char',
            'sous_type'       => 'char',
            'format_ouvert'   => 'char',
            'format_ferme'    => 'char',
            'largeur'         => 'char',
            'longueur'        => 'char',
            'nombre_de_pages' => 'char',
            'campagne'        => 'char',
            'objet'           => 'char',
            'nom_de_domaine'  => 'char',
            'origine'         => 'char',
            'sujet'           => 'char',
            'ville'           => 'char',
            'numero'          => 'char',
            'mois'            => 'char',
            'societe'         => 'char',
            'banque'          => 'char',
            'nom'             => 'char',
            'piece'           => 'char',
            'statuts'         => 'char',
            'assemblees'      => 'char',
            'descriptif'      => 'char',
            'format'          => 'char',
            'bass_de_donnee'  => 'char',
            'date_creation'  => 'date',
            'modified_date'  => 'date',
        );

        return $filterable_columns;
    }

    public function detail($id)
    {
        $table = 'oc_filecache fc';
        // première partie du select, mis en cache
        $this->db->start_cache();

        $this->db->select("     
                    fm.`id`,
                    fc.`storage`,
                    fm.`format_file`,
                    fm.`created_by`,
                    fm.`uploaded_by`,
                    fm.`format_file`,
                    mm.`mimetype` as `type`,
                    `path`,
                    `fc`.`size`,
                    `mtime` as `modified`,
                    '' as mime_type,
                    `permissions`,
                    `fileid`,
                    `name`,
                    `path`,
                    fm.`description`,
                    fm.`document_type`,
                    fm.`client_id`,
                    fm.`client_name`,
                    fm.`devis_id`,
                    fm.`devis` as `devis`,
                    fm.`facture_id`,
                    fm.`facture` as `facture`,
                    fm.`sous_type`,
                    fm.`format_ouvert`,
                    fm.`format_ferme`,
                    fm.`largeur`,
                    fm.`longueur`,
                    fm.`nombre_de_pages`,
                    fm.`campagne`,
                    fm.`objet`,
                    fm.`nom_de_domaine`,
                    fm.`origine`,
                    fm.`sujet`,
                    fm.`ville`,
                    fm.`numero`,
                    fm.`mois`,
                    fm.`societe`,
                    fm.`banque`,
                    fm.`nom`,
                    fm.`piece`,
                    fm.`statuts`,
                    fm.`assemblees`,
                    fm.`descriptif`,
                    fm.`format`,
                    fm.`message_id`,
                    fm.`message_name`,
                    fm.`bass_de_donnee`,
                    fm.`date_creation`,
                    fm.`salarie`,
                    fm.`annee`,
                    fm.`article`,
                    fm.`departement`,
                    fm.`recruitment_type`, 
                    fm.`statuts_assemblees_type`,
                    fm.`nom_organisme`,
                    fm.`sous_traitants_type`,                           
                    fm.`modified_date`,
                    mm.`mimetype` as `mime`");
        $this->db->join('oc_ownnotes_notes fm', 'fc.fileid = fm.object_id', 'left');
        $this->db->join('oc_mimetypes mm', 'fc.mimetype = mm.id', 'left');
        $this->db->where('path LIKE "%.%"');
        $this->db->where('path NOT LIKE "thumbnail%"');
        $this->db->where('path NOT LIKE "%trash%"');
        $this->db->where('fileid', $id);
        $q = $this->db->get($table);

        return $q->row();
    }

    public function document_type_options()
    {
        $options = array(
            "Infographie",
            "Plan",
            "Piece Comptable",
            "Facture Client",
            "Devis Client",
            "E-Mailing",
            "Site Internet",
            "Administratif Divers",
            "Developpement",
            "Ressources Humaines",
            "Commercial",            
        );

        return $this->form_option($options);
    }

    public function sous_type_options()
    {
        $options = array(
            "Flyer",
            "Depliant",
            "Affiche",
            "Bache",
            "Carte de Visite",
            "Akilux",
            "Panneau",
            "Logo",
            "Brochure",
            "Enveloppes",
            "Drapeau",
            "Blocs",
            "Auto-Collant",
            "Chemise",
            "E-Mailing",
            "Site Internet",
            "Fax",
            "Photo",
        );

        return $this->form_option($options);
    }

    public function form_option($values, $inc_index = false)
    {
        for ($i = 0; $i < count($values); $i++) {
            $val = new stdClass();
            if ($inc_index) {
                $val->id = $i;
            } else {
                $val->id = $values[$i];
            }

            $val->value = $values[$i];
            $result[$i] = $val;
        }
        return $result;
    }

    public function  update($id)
    {
        
    }
}
// EOF
