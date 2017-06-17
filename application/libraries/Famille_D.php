<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 07/07/15
 * Time: 11:37
 */
/**
 * Famille de catalogue distribution
 */
class Famille_D extends Famille_catalogue {
    const CODE = 'D';

    public function __construct() {
        parent::__construct();
    }

    /******************************
     * Exploitation du catalogue téléchargé
     ******************************/
    public function exploite($id,$data) {
        $erreurs = array();
        $articles = array();
        $codes = array();

        // récupération des codes habitat
        $q = $this->CI->db->get('v_types_secteurs');
        $habitat = array();
        foreach($q->result() as $r) {
            $habitat[$r->vts_type] = $r->vts_id;
        }

        // contrôle des données
        foreach($data as $ligne) {
            $code_article = $ligne[0];
            if (array_key_exists($code_article, $codes)) {
                $codes[$code_article] += 1;
            } else {
                $codes[$code_article] = 1;
            }
            if (count($ligne) < 10) {
                $erreurs[] = "Article $code_article : le catalogue Distribution doit avoir au moins 10 colonnes";
                return $erreurs;
            }
            if (!is_numeric($ligne[3]) OR $ligne[3] < 0) {
                $erreurs[] = "Article $code_article : le prix doit être un nombre positif ou null";
            }
            if ($ligne[4] != 'non' AND $ligne[4] != 'oui') {
                $erreurs[] = "Article $code_article : la colonne 'Sélectionnable' doit être 'oui' ou 'non'";
            }
            if (!array_key_exists($ligne[6], $habitat)) {
                $code_habitat = 0;
                $erreurs[] = "Article $code_article : l'habitat " . $ligne[6] . " n'est pas reconnu";
            } else {
                $code_habitat = $habitat[$ligne[6]];
            }

            $infos = array(
                $ligne[6], // habitat
                $ligne[7], // type
                $ligne[8], // mairie
                $ligne[9], // commentaire
                $code_habitat // code habitat
            );

            // préparation de l'insertion
            $article = array(
                'art_code' => $ligne[0],
                'art_description' => $ligne[1],
                'art_libelle' => $ligne[2],
                'art_prix' => $ligne[3],
                'art_selection' => ($ligne[4] == 'oui') ? 1 : 0,
                'art_prod' => $ligne[5],
                'art_data' => serialize($infos),
                'art_catalogue' => $id
            );
            $articles[] = $article;
        }

        // contrôle des doublons
        foreach ($codes as $c=>$n) {
            if ($n != 1) {
                $erreurs[] = "Le code article $c est présent $n fois";
            }
        }

        // sortie en erreur
        if (count($erreurs) > 0) {
            return $erreurs;
        }

        // suppression des éventuels articles précédents
        $this->CI->db->where('art_catalogue',$id)
            ->delete('t_articles');

        // insertion
        foreach ($articles as $article) {
            $this->CI->db->insert('t_articles',$article);
        }

        if ($this->CI->db->insert_id() == 0) {
            return false;
        }
        return true;
    }

    /******************************
     * En-tête pour l'exportation
     ******************************/
    public function en_tete() {
        $en_tete = parent::en_tete();
        $en_tete[] = 'Habitat';
        $en_tete[] = 'Type';
        $en_tete[] = 'Mairie';
        $en_tete[] = 'Commentaires';
        return $en_tete;
    }

    /******************************
     * Catalogue en service
     ******************************/
    public function catalogue($code) {
        switch ($code) {
            case 'D':
                $catalogue = parent::catalogue_en_service($this::CODE);
                if ($catalogue === false) {
                    return false;
                }
                foreach ($catalogue as $article) {
                    $data = unserialize($article->art_data);
                    $article->art_habitat = $data[0];
                    $article->art_document = $data[1];
                    $article->art_distribution = $data[2];
					$article->art_delai = $data[3];
					$article->art_controle = $data[4];
                }
                return $catalogue;
                break;
            case 'Dv':
                $v = $this->CI->db->get('t_villes');
                return $v->result();
                break;
            case 'Ds':
                $s = $this->CI->db->get('t_secteurs');
                return $s->result();
            default:
                return false;
        }
    }

    /******************************
     * Préparation des distributions
     ******************************/
    public function preparation() {
        $data = array(
            'title' => "Non disponible",
            'page' => "_production/distribution/preparation",
            'values' => array(
            )
        );
        return $data;
    }

    /******************************
     * Suivi de production des distributions
     ******************************/
    public function suivi() {
        $data = array(
            'title' => "disponible",
            'page' => "_production/distribution/suivi",
            'values' => array(
            )
        );
        return $data;
    }

}
// EOF