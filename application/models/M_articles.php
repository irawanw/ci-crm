<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_articles extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('art_code','text',"Code article"),
                array('art_prod','text',"Code production"),
                array('art_description','text',"Description"),
                array('art_prix','number',"PUHT"),
                array('art_selection','text',"Sélectionnable"),
                array('cat_version','ref',"Catalogue",'catalogues','art_catalogue','cat_version'),
                array('vfm_famille','ref',"Famille",'v_familles'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array()
        );

        return array_key_exists($type, $champs) ? $champs[$type] : array();
    }

    /******************************
    * Liste des articles d'un catalogue
    ******************************/
    public function liste($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $art_code = formatte_sql_lien('articles/detail','art_id','art_code');
        $cat_version = formatte_sql_lien('catalogues/detail','cat_id','cat_version');
        $this->db->select("art_id AS RowID,art_id,$art_code,art_prod,art_description,art_prix,art_selection,$cat_version,vfm_famille",false);
        $this->db->join('t_catalogues','cat_id=art_catalogue','left');
        $this->db->join('v_familles','vfm_id=cat_famille','left');
        $this->db->where("art_catalogue",$pere);
        $this->db->where('art_inactif is null');
        //$this->db->order_by("art_code asc");
        $this->db->stop_cache();

        $table = 't_articles';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
    $filterable_columns = array(
            'art_code'=>'char',
            'art_prod'=>'char',
            'art_description'=>'char',
            'art_prix'=>'decimal',
            'art_selection'=>'char',
            'cat_version'=>'char',
            'vfm_famille'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Détail d'un article
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("art_id,art_code,art_prod,art_prix,art_description,art_libelle,art_catalogue,cat_version,cat_famille,vfm_famille,art_data",false);
        $this->db->join('t_catalogues','cat_id=art_catalogue','left');
        $this->db->join('v_familles','vfm_id=cat_famille','left');
        $this->db->where('art_id',$id);
        $this->db->where('art_inactif is null');
        $q = $this->db->get('t_articles');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }
	
	public function nouveau($data)
    {
        return $this->_insert('t_articles', $data);
    }

}
// EOF
