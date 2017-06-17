<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_articles_distributions extends MY_Model {

	private $article_distribution_option = array(
        'HABITAT'      => 'habitat',
        'DOCUMENT'     => 'document',
        'DISTRIBUTION' => 'type_distribution',
        'DELAI'        => 'delai',
        'CONTROLE'     => 'controle',
    );
	
    public function __construct() {
        parent::__construct();
    }
	
public function generate_distribution_articles()
    {
        $this->load->model('m_catalogues');
        //insert new version catalogues distribution
        $version         = 1;
        $query_catalogue = $this->db->select('MAX(cat_version) as version')->get_where('t_catalogues', array('cat_famille' => 3));

        if ($query_catalogue->num_rows() > 0) {
            $row_catalogue = $query_catalogue->row();
            $last_version  = $row_catalogue->version;
            $version       = $last_version + 1;
        }

        $valeurs = array(
            'cat_famille' => 3,
            'cat_version' => $version,
            'cat_date'    => date("Y-m-d"),
        );
        $catalogue_id = $this->m_catalogues->nouveau($valeurs);

        $prix_debase      = array();
        $data_prix_debase = $this->m_articles_distributions->get_articles_distribution_base_price();
        $i                = 0;
        foreach ($data_prix_debase as $row) {
            $prix_debase[$row->adb_secteur]['baseprice']    = $row->adb_baseprice;
            $prix_debase[$row->adb_secteur]['type_secteur'] = $row->vts_type;
            $i++;
        }

        $options     = $this->m_articles_distributions->get_articles_distribution_option();
        $resultat    = $this->m_articles_distributions->get_articles_distribution_price();
        $option_data = $resultat['option_data'];
        $price_inc   = $resultat['price_inc'];

        $habitat           = $option_data[$options['HABITAT']];
        $document          = $option_data[$options['DOCUMENT']];
        $type_distribution = $option_data[$options['DISTRIBUTION']];
        $delai             = $option_data[$options['DELAI']];
        $controle          = $option_data[$options['CONTROLE']];

        $total_combination = count($prix_debase) *
        count($habitat) *
        count($document) *
        count($type_distribution) *
        count($delai) *
        count($controle);

        $z           = 1;
        $data_insert = array();
        foreach ($prix_debase as $secteur_id => $secteur) {
            $i = 1;
            foreach ($habitat as $habitat_row) {
                foreach ($document as $document_row) {
                    foreach ($type_distribution as $type_distribution_row) {
                        foreach ($delai as $delai_row) {
                            foreach ($controle as $controle_row) {
                                $price = 0;

                                if ($i < 10) {
                                    $code = '00' . $i;
                                } elseif ($i < 100) {
                                    $code = '0' . $i;
                                } else {
                                    $code = $i;
                                }

                                //prix final calculation
                                //will move this into database
                                $price += $price_inc[$options['HABITAT']][$habitat_row];
                                $price += $price_inc[$options['DOCUMENT']][$document_row];
                                $price += $price_inc[$options['DISTRIBUTION']][$type_distribution_row];
                                $price += $price_inc[$options['DELAI']][$delai_row];
                                $price += $price_inc[$options['CONTROLE']][$controle_row];
                                $final_prix = (1 + ($price / 100)) * $secteur['baseprice'];

                                $code = 'DS' . $secteur_id . '-' . $code;

                                $data    = array();
                                $data[0] = $habitat_row;
                                $data[1] = $document_row;
                                $data[2] = $type_distribution_row;
                                $data[3] = $delai_row;
                                $data[4] = $controle_row;

                                $article                    = array();
                                $article['art_code']        = $code;
                                $article['art_description'] = 'Prix unitaire BAL ' . $habitat_row . ' ' .
                                    $secteur['type_secteur'] .
                                    ', Document ' . $document_row .
                                    ', Type Distribution ' . $type_distribution_row .
                                    ', Delai ' . $delai_row .
                                    ', Controle ' . $controle_row;
                                $article['art_libelle']   = $article['art_description'];
                                $article['art_data']      = serialize($data);
                                $article['art_prix']      = $final_prix;
                                $article['art_selection'] = 1;
                                $article['art_catalogue'] = $catalogue_id;

                                //slow performance
                                //need to group insert
                                //$this->m_articles->nouveau($article);

                                $data_insert[] = "( '" . $article['art_code'] . "',
                                                        '" . $article['art_description'] . "',
                                                        '" . $article['art_libelle'] . "',
                                                        '" . $article['art_data'] . "',
                                                        '" . $article['art_prix'] . "',
                                                        '" . $article['art_selection'] . "',
                                                        '" . $article['art_catalogue'] . "'
                                                    )";

                                if ($i % 100 == 0 || $z == $total_combination) {
                                    $data_insert = implode(',', $data_insert);
                                    $this->db->query("INSERT INTO t_articles (  art_code,
                                                                            art_description,
                                                                            art_libelle,
                                                                            art_data,
                                                                            art_prix,
                                                                            art_selection,
                                                                            art_catalogue
                                                                        ) VALUES " . $data_insert);
                                    $data_insert = array();
                                }

                                $i++;
                                $z++;
                            }
                        }
                    }
                }
            }
        }
    }	
	
    public function get_articles_distribution_option()
    {
        return $this->article_distribution_option;
    }

    public function get_articles_distribution_price()
    {
        $rows = $this->db->query('select * from t_articles_distribution_price')->result();

        $options     = $this->article_distribution_option;
        $option_data = array();
        $price_inc   = array();

        foreach ($rows as $row) {
            switch ($row->adp_option) {
                case $options['HABITAT']:
                    $option_data[$options['HABITAT']][]              = $row->adp_value;
                    $price_inc[$options['HABITAT']][$row->adp_value] = $row->adp_percentage;
                    continue;
                case $options['DOCUMENT']:
                    $option_data[$options['DOCUMENT']][]              = $row->adp_value;
                    $price_inc[$options['DOCUMENT']][$row->adp_value] = $row->adp_percentage;
                    continue;
                case $options['DISTRIBUTION']:
                    $option_data[$options['DISTRIBUTION']][]              = $row->adp_value;
                    $price_inc[$options['DISTRIBUTION']][$row->adp_value] = $row->adp_percentage;
                    continue;
                case $options['DELAI']:
                    $option_data[$options['DELAI']][]              = $row->adp_value;
                    $price_inc[$options['DELAI']][$row->adp_value] = $row->adp_percentage;
                    continue;
                case $options['CONTROLE']:
                    $option_data[$options['CONTROLE']][]              = $row->adp_value;
                    $price_inc[$options['CONTROLE']][$row->adp_value] = $row->adp_percentage;
                    continue;
                default:
                    # code...
                    break;
            }
        }

        $resultat = array(
            'option_data' => $option_data,
            'price_inc'   => $price_inc,
        );

        return $resultat;
    }

    public function get_articles_distribution_base_price()
    {
        // $this->db->select('*');
        // $this->db->join('v_types_secteurs', 'adb_secteur = vts_id');
        // $rows = $this->db->get('t_articles_distribution_base_price')->result();

        $rows = $this->db->query('select * from t_articles_distribution_base_price left join v_types_secteurs on adb_secteur=vts_id')->result();

        $result = array();
        foreach ($rows as $row) {
            $result[] = $row;
        }
        return $result;
    }

    public function catalogues_distribution_liste($void, $code, $quantites = null, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $options          = $this->get_articles_distribution_option();
        $resultat         = $this->get_articles_distribution_price();
        $price_inc        = $resultat['price_inc'];
        $prix_debase      = array();
        $data_prix_debase = $this->get_articles_distribution_base_price();
        foreach ($data_prix_debase as $row) {
            $prix_debase[$row->adb_secteur]['baseprice'] = $row->adb_baseprice;
        }

        $q = $this->db->query("SELECT vfm_id FROM v_familles WHERE vfm_code='$code'");
        if ($q->num_rows() == 0) {
            return false;
        }

        $famille = $q->row()->vfm_id;

        $q = $this->db->query("SELECT cat_id FROM t_catalogues WHERE cat_date=(SELECT max(A.`cat_date`) FROM t_catalogues A where A.`cat_date` <= CURDATE() AND A.`cat_famille`=$famille) AND cat_famille=$famille");
        if ($q->num_rows() == 0) {
            return false;
        }
        $catalogue = $q->row()->cat_id;

        $table = 't_articles';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select($table . ".*,art_id as RowID");
        $this->db->like('art_code', 'DS', 'after');
        $this->db->where('art_catalogue', $catalogue);
        $this->db->where('art_selection', 1);
        $this->db->stop_cache();
        // aliases
        $aliases = array(
            'art_habitat'      => "art_data",
            'art_document'     => "art_data",
            'art_distribution' => "art_data",
            'art_delai'        => "art_data",
            'art_controle'     => "art_data",
        );

        $resultat = $this->_filtre($table, $this->liste_catalogues_distribution_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        foreach ($resultat['data'] as $key => $article) {
            $price        = 0;
            $data         = unserialize($article->art_data);
            $code         = $article->art_code;
            $codeArr      = explode("-", $code);
            $codeType     = $codeArr[0];
            $secteur_type = substr($codeType, 2);

            $art_habitat      = $data[0];
            $art_document     = $data[1];
            $art_distribution = $data[2];
            $art_delai        = $data[3];
            $art_controle     = $data[4];
            $price += $price_inc[$options['HABITAT']][$art_habitat] ? $price_inc[$options['HABITAT']][$art_habitat] : 0;
            $price += $price_inc[$options['DOCUMENT']][$art_document] ? $price_inc[$options['DOCUMENT']][$art_document] : 0;
            $price += $price_inc[$options['DISTRIBUTION']][$art_distribution] ? $price_inc[$options['DISTRIBUTION']][$art_distribution] : 0;
            $price += $price_inc[$options['DELAI']][$art_delai] ? $price_inc[$options['DELAI']][$art_delai] : 0;
            $price += $price_inc[$options['CONTROLE']][$art_controle] ? $price_inc[$options['CONTROLE']][$art_controle] : 0;
            $final_prix = (1 + ($price / 100)) * $prix_debase[$secteur_type]['baseprice'];

            $habitats = array();

            if (strpos($art_habitat, "+")) {
                $habitats = explode("+", $art_habitat);
            } else {
                if ($art_habitat == "TOUT") {
                    array_push($habitats, "HLM", "RES", "PAV");
                } else {
                    $habitats[] = $art_habitat;
                }
            }

            //count total prix
            if ($quantites == null || $quantites == "") {
                $art_prix_total = "";
            } else {
                $quantitesObj   = json_decode($quantites);
                $art_prix_total = 0;

                if (count($quantitesObj) > 0) {
                    foreach ($habitats as $habitat) {
                        if (isset($quantitesObj->$habitat)) {
                            $art_prix_total += $quantitesObj->$habitat * $final_prix;
                        }

                    }
                }
            }

            $resultat['data'][$key]->art_habitat      = $art_habitat;
            $resultat['data'][$key]->art_document     = $art_document;
            $resultat['data'][$key]->art_distribution = $art_distribution;
            $resultat['data'][$key]->art_delai        = $art_delai;
            $resultat['data'][$key]->art_controle     = $art_controle;
            $resultat['data'][$key]->art_prix         = $final_prix;
            $resultat['data'][$key]->art_prix_total   = $art_prix_total;
        }

        return $resultat;
    }

    public function liste_catalogues_distribution_filterable_columns()
    {
        $filterable_columns = array(
            'art_code'         => 'char',
            'art_description'  => 'char',
            'art_habitat'      => 'char',
            'art_document'     => 'char',
            'art_distribution' => 'char',
            'art_delai'        => 'char',
            'art_controle'     => 'char',
            'art_prix'         => 'double',
        );

        return $filterable_columns;
    }

    public function commande_distribution($data)
    {
        return $this->_insert('t_commande_distributions', $data);
    }

    public function check_is_type_distribution($devis_id)
    {
        $query = $this->db->like('ard_code', 'D', 'after')->where('ard_devis', $devis_id)->limit(1)->get('t_articles_devis');

        if ($query->num_rows() != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function detail_for_form_commande_distribution($devis_id)
    {

        $this->db->select("dvi_id,
                dvi_reference as devis,
                ctc_nom as client,
                dvi_montant_ht as montant_ht,
                ", false);
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');

        $this->db->where('dvi_id', $devis_id);
        $q = $this->db->get('t_devis');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        return null;
    }

    public function get_commande_distribution_ard_data($devis_id)
    {
        $query = $this->db->select('ard_code,GROUP_CONCAT(ard_info) as infos,GROUP_CONCAT(ard_quantite) as quantites,GROUP_CONCAT(ard_prix) as prixes,ard_article,art_data')
            ->join('t_articles', 'art_id=ard_article', 'left')
            ->like('ard_code', 'D', 'after')
            ->where('ard_devis', $devis_id)
            ->where('ard_inactif', null)
            ->group_by('ard_article')
            ->group_by('ard_devis')
            ->order_by('ard_id')
            ->get('t_articles_devis');

        $data = array();
        $secteur_ids  = array();
        $articles_ids = array();

        if ($query->num_rows() > 0) {
            
            foreach ($query->result() as $row) {
                $infos        = explode(",", $row->infos);
                $quantites    = explode(",", $row->quantites);
                $prixes       = explode(",", $row->prixes);
                $detail       = unserialize($row->art_data);
                $document     = $detail[1];
                $distribution = $detail[2];
                $delai        = $detail[3];
                $controle     = $detail[4];
                $article      = $row->ard_article;

                foreach ($infos as $key => $info) {
                    $habitat    = substr($info, 0, 3);
                    $infoString = explode(":", substr($info, 3));
                    $ville_id   = $infoString[0];
                    $secteur_id = $infoString[1];
                    $qty        = $quantites[$key];
                    $prix       = $prixes[$key];

                    if ($secteur_id != 0) {
                        $secteur                           = $this->db->select('sec_id as id, sec_nom as name')->where('sec_id', $secteur_id)->get('t_secteurs')->row();
                        $data[$secteur_id][$habitat]       = $qty * $prix;
                        $data[$secteur_id]['secteur_id']   = $secteur->id;
                        $data[$secteur_id]['secteur_name'] = $secteur->name;
                        $data[$secteur_id]['document']     = $document;
                        $data[$secteur_id]['distribution'] = $distribution;
                        $data[$secteur_id]['delai']        = $delai;
                        $data[$secteur_id]['controle']     = $controle;
                        $data[$secteur_id]['article']      = $article;

                        $secteur_ids[]  = $secteur_id;
                        $articles_ids[] = $article;
                    } else {
                        $secteurs = $this->db->select('sec_id as id, sec_nom as name')->where('sec_ville', $ville_id)->get('t_secteurs')->result();

                        foreach ($secteurs as $secteur) {
                            $data[$secteur->id][$habitat]       = $qty * $prix;
                            $data[$secteur->id]['secteur_id']   = $secteur->id;
                            $data[$secteur->id]['secteur_name'] = $secteur->name;
                            $data[$secteur->id]['document']     = $document;
                            $data[$secteur->id]['distribution'] = $distribution;
                            $data[$secteur->id]['delai']        = $delai;
                            $data[$secteur->id]['controle']     = $controle;
                            $data[$secteur->id]['article']      = $article;

                            $secteur_ids[]  = $secteur->id;
                            $articles_ids[] = $article;
                        }
                    }
                }
            }
        }

        return array(
            'data'     => $data,
            'secteurs' => implode(",", $secteur_ids),
            'articles' => implode(",", $articles_ids),
        );
    }

    public function cdb_type_commande_option()
    {
        $options = array(
            "générale",
            "opération",
            "standard",
            "opération recto verso",
            "standard recto verso",
        );

        return $this->form_option($options);
    }

    public function cdb_envelement_demand_option()
    {
        $options = array('Non', 'Oui');
        return $this->form_option($options, true);
    }

    public function cdb_etat_option()
    {
        $options = array(
            "archivé",
            "supprimée",
            "en cours",
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
}
