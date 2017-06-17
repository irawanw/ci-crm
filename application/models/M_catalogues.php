<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_catalogues extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('vfm_famille','ref',"Famille",'v_familles'),
                array('cat_version','text',"Version"),
                array('cat_date','date',"Date de mise en service"),
                array('cat_etat','text',"Etat"),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'cat_famille' => array("Famille",'select',array('cat_famille','vfm_id','vfm_famille'),false),
               'cat_version' => array("Version",'text','cat_version',true),
               'cat_date' => array("Date de mise en service",'date','cat_date',true)
            )
        );

        return array_key_exists($type, $champs) ? $champs[$type] : array();
    }

    /******************************
    * Liste des catalogues
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $table = 't_catalogues';
        $this->db->start_cache();

        // lecture des informations
        $cat_version = formatte_sql_lien('catalogues/detail','cat_id','cat_version');
        $cat_date = formatte_sql_date('cat_date');
        $cat_etat = "IF (cat_date > CURDATE(),'futur',IF(cat_date=(SELECT max(A.cat_date) FROM t_catalogues A where A.cat_date <= CURDATE() AND A.cat_famille=t_catalogues.cat_famille),'en service','périmé'))";
        $cat_etat2 = $cat_etat ." AS cat_etat";
        $this->db->select("cat_id AS RowID,cat_id,$cat_version,vfm_famille,$cat_date,$cat_etat2",false);
        $this->db->join('v_familles','vfm_id=cat_famille','left');
        //$this->db->where('cat_inactif is null');
        switch($void){
            case 'archived':
                $this->db->where($table.'.cat_archiver is NOT NULL');
                break;
            case 'deleted':
                $this->db->where($table.'.cat_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table.'.cat_archiver is NULL');
                $this->db->where($table.'.cat_inactif is NULL');
                break;
        }

        $id = intval($void);
        if ($id > 0) {
         $this->db->where('cat_id', $id);
        }
        
        $this->db->stop_cache();

        // aliases
        $aliases = array(
            'cat_etat'=>$cat_etat
        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->RowID.'">';
        }

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'vfm_famille'=>'char',
            'cat_version'=>'char',
            'cat_date'=>'date',
            'cat_etat'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau catalogue
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_catalogues', $data);
        return $id;
    }

    /******************************
    * Détail d'un catalogue
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("cat_id,cat_famille,vfm_famille,cat_version,cat_date,IF (cat_date > CURDATE(),'futur',IF(cat_date=(SELECT max(A.cat_date) FROM t_catalogues A where A.cat_date <= CURDATE() AND A.cat_famille=t_catalogues.cat_famille),'en service','périmé')) AS cat_etat",false);
        $this->db->join('v_familles','vfm_id=cat_famille','left');
        $this->db->where('cat_id',$id);
        $this->db->where('cat_inactif is null');
        $q = $this->db->get('t_catalogues');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un catalogue
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('cat_id',$id)->get('t_catalogues');
        $res =  $this->_update('t_catalogues',$data,$id,'cat_id');
        return $res;
    }

     /******************************
    * 
    ******************************/
    public function archive($id) {
        return $this->_delete('t_catalogues',$id,'cat_id','cat_archiver');
    }

    /******************************
    * 
    ******************************/
    public function remove($id) {
        return $this->_delete('t_catalogues',$id,'cat_id','cat_inactif');
    }

    /******************************
    * 
    ******************************/
    public function unremove($id) {
        $data = array('cat_inactif' => null, 'cat_archiver' => null);
        return $this->_update('t_catalogues',$data, $id,'cat_id');
    }

    /******************************
     * Exportation d'un catalogue
     ******************************/
    public function exportation($id) {

        // récupération des informations du catalogue
        $q = $this->db->select("cat_version,vfm_code,vfm_nom")
            ->join('v_familles','vfm_id=cat_famille','left')
            ->where('cat_id',$id)
            ->get('t_catalogues');
        if ($q->num_rows() > 0) {
            $catalogue = $q->row();
            $famille = 'Famille_'.$catalogue->vfm_code;
            $nom_catalogue = 'catalogue_'.$catalogue->vfm_nom.'_'.$catalogue->cat_version.'.xlsx';
        }
        else {
            return false;
        }

        // chargement des bibliothèques PHPExcel famille
        require 'application/third_party/PHPExcel/IOFactory.php';
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library($famille,NULL,'famille');

        // récupération des données
        $q = $this->db->select("art_code,art_description,art_libelle,art_prix,IF (art_selection=1,'oui','non'),art_prod,art_data")
            ->where('art_catalogue',$id)
            ->where('art_inactif is null')
            ->order_by('art_id','ASC')
            ->get('t_articles');
        if ($q->num_rows() > 0) {
            $data = $q->result_array();
        }
        else {
            $data = array();
        }

        // enregistrement des données
        PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        $PHPExcel = new PHPExcel();
        $sheet = $PHPExcel->getActiveSheet();

        // en-tete
        $ent_tete =  $this->famille->en_tete();
        $row = 1;
        $col = 0;
        foreach($ent_tete as $c) {
            $sheet->setCellValueByColumnAndRow($col, $row, $c);
            $col++;
        }
        $row++;

        // articles
        foreach ($data as $ligne) {
            $col = 0;
            $infos = unserialize($ligne['art_data']);
            unset ($ligne['art_data']);
            $ligne = array_merge($ligne,$infos);
            foreach($ligne as $c) {
                $sheet->setCellValueByColumnAndRow($col, $row, $c);
                $col++;
            }
            $row++;
        }

        // écriture du fichier
        if (!file_exists('tmp')) {
            mkdir('tmp',0777);
        }
        $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('tmp/'.$nom_catalogue);
        //$sheet = false;
        $PHPExcel->disconnectWorksheets();
        unset($PHPExcel);

        return 'tmp/'.$nom_catalogue;
    }

    /******************************
     * Importation d'un catalogue
     ******************************/
    public function importation($id,$fichier) {

        // récupération de la famille du catalogue
        $q = $this->db->select("vfm_code")
            ->join('v_familles','vfm_id=cat_famille','left')
            ->where('cat_id',$id)
            ->get('t_catalogues');
        if ($q->num_rows() > 0) {
            $famille = 'Famille_'.$q->row()->vfm_code;
        }
        else {
            return false;
        }

        // chargement des bibliothèques PHPExcel famille
        require 'application/third_party/PHPExcel/IOFactory.php';
        require 'application/libraries/Famille_catalogue.php';
        $this->load->library($famille,NULL,'famille');

        // ouverture du fichier
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(
            'memoryCacheSize' => '512k'
        );
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        /*$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod);*/

        // lecture du fichier avec saut de la première ligne
        $lecteur = $objReader->load($fichier);
        $feuille = $lecteur->getSheet(0);
        $highestRow = $feuille->getHighestRow();
        $highestColumn = $feuille->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $data = array();
        for ($row=2; $row <= $highestRow; $row++) {
            if ($feuille->getCellByColumnAndRow(0, $row) == '') continue;
            $ligne = array();
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $cell = $feuille->getCellByColumnAndRow($col, $row);
                $cellule = $cell->getValue();
                if (!isset($cellule)) {
                    $cellule = '';
                }
                $ligne[] = $cellule;
            }
            $data[] = $ligne;
        }

        // exploitation du fichier
        $resultat = $this->famille->exploite($id,$data);
        $lecteur->disconnectWorksheets();
        unset($lecteur);
        unlink($fichier);
        return $resultat;
    }

}
// EOF
