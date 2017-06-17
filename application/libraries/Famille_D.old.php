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

        // récupération des villes et secteurs
        $villes = $this->villes();

        // contrôle des données
        foreach($data as $ligne) {
            $code_article = $ligne[0];
            if (array_key_exists($code_article,$codes)) {
                $codes[$code_article] += 1;
            }
            else {
                $codes[$code_article] = 1;
            }
            if (count($ligne) < 9) {
                $erreurs[] = "Article $code_article : le catalogue Distribution doit avoir au moins 9 colonnes";
                return $erreurs;
            }
            if (!is_numeric($ligne[2]) OR $ligne[2] < 0) {
                $erreurs[] = "Article $code_article : le prix doit être un nombre positif ou null";
            }
            if ($ligne[3] != 'non' AND $ligne[3] != 'oui') {
                $erreurs[] = "Article $code_article : la colonne 'Sélectionnable' doit être 'oui' ou 'non'";
            }
            if ($ligne[5] != '' AND $ligne[5] != 'HLM' AND $ligne[5] != 'RES' AND $ligne[5] != 'PAV') {
                $erreurs[] = "Article $code_article : la colonne 'BAL' doit être 'HLM', 'RES', 'PAV' ou être vide";
            }
            $ville = $ligne[6];
            if ($ville != '' and !array_key_exists($ville,$villes)) {
                $erreurs[] = "Article $code_article : la colonne 'ville' doit être une ville existante ou être vide";
            }
            elseif ($ville != '') {
                if ($ligne[7] != '' and !array_key_exists($ligne[7],$villes[$ville])) {
                    $erreurs[] = "Article $code_article : la colonne 'secteur' doit être un secteur de la ville ou être vide";
                }
            }
            $infos = array(
                $ligne[5], // BAL
                $ligne[6], // ville
                $ligne[7], // secteur
                $ligne[8] // commentaire
            );

            // préparation de l'insertion
            $article = array(
                'art_code' => $ligne[0],
                'art_description' => $ligne[1],
                'art_prix' => $ligne[2],
                'art_selection' => ($ligne[3]=='oui')?1:0,
                'art_prod' => $ligne[4],
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
        $en_tete[] = 'BAL';
        $en_tete[] = 'Ville';
        $en_tete[] = 'Secteur';
        $en_tete[] = 'Commentaires';
        return $en_tete;
    }

    /******************************
     * Liste des villes et des secteurs
     ******************************/
    private function villes() {
        $q = $this->CI->db->select("sec_nom,vil_nom")
            ->join('t_villes','vil_id=sec_ville','left')
            ->get('t_secteurs');
        $villes = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $r) {
                $villes[$r->vil_nom][$r->sec_nom] = 0;
            }
        }
        return $villes;
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
                    $article->art_bal = $data[0];
                    $article->art_ville = $data[1];
                    $article->art_secteur = $data[2];
                }
                //return $catalogue;
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