<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 07/07/15
 * Time: 11:37
 */
/**
 * Famille de catalogue impressions
 */
class Famille_I extends Famille_catalogue {
    const CODE = 'I';

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

        // contrôle des données
        foreach($data as $ligne) {
            $code_article = $ligne[0];
            if (array_key_exists($code_article,$codes)) {
                $codes[$code_article] += 1;
            }
            else {
                $codes[$code_article] = 1;
            }
            if (count($ligne) < 22) {
                $erreurs[] = "Article $code_article : le catalogue Impression doit avoir au moins 21 colonnes";
                return $erreurs;
            }
            if (!is_numeric($ligne[3]) OR $ligne[3] < 0) {
                $erreurs[] = "Article $code_article : le prix doit être un nombre positif ou null";
            }
            if ($ligne[4] != 'non' AND $ligne[4] !='oui') {
                $erreurs[] = "Article $code_article : la colonne 'Sélectionnable' doit être 'oui' ou 'non'";
            }
            $infos = array(
                $ligne[6], // imprimé
                $ligne[7], // format
                $ligne[8], // taille
                $ligne[9], // papier
                $ligne[10], // grammage
                $ligne[11], // finition
                $ligne[12], // couleurs
                $ligne[13], // recto/verso
                $ligne[14], // quantité
                $ligne[15], // fournisseur 1
                $ligne[16], // prix fournisseur 1
                $ligne[17], // URL fournisseur 1
                $ligne[18], // fournisseur 2
                $ligne[19], // prix fournisseur 2
                $ligne[20], // URL fournisseur 2
                $ligne[21] // commentaire
            );

            // préparation de l'insertion
            $article = array(
                'art_code' => $ligne[0],
                'art_description' => $ligne[1],
                'art_libelle' => $ligne[2],
                'art_prix' => $ligne[3],
                'art_selection' => ($ligne[4]=='oui')?1:0,
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
        $en_tete[] = 'Imprimé';
        $en_tete[] = 'Format';
        $en_tete[] = 'Taille';
        $en_tete[] = 'Papier';
        $en_tete[] = 'Grammage';
        $en_tete[] = 'Finition';
        $en_tete[] = 'Couleurs';
        $en_tete[] = 'Recto/verso';
        $en_tete[] = 'Quantité';
        $en_tete[] = 'Fournisseur 1';
        $en_tete[] = 'Prix fournisseur 1';
        $en_tete[] = 'URL fournisseur 1';
        $en_tete[] = 'Fournisseur 2';
        $en_tete[] = 'Prix fournisseur 2';
        $en_tete[] = 'URL fournisseur 2';
        $en_tete[] = 'Commentaires';
        return $en_tete;
    }

    /******************************
     * Catalogue en service
     ******************************/
    public function catalogue() {
        $catalogue = parent::catalogue_en_service($this::CODE);
        if ($catalogue === false) {
            return false;
        }
        foreach ($catalogue as $article) {
            $data = unserialize($article->art_data);
            $article->art_imprime = $data[0];
            $article->art_format = $data[1];
            $article->art_taille = $data[2];
            $article->art_papier = $data[3];
            $article->art_grammage = $data[4];
            $article->art_finitions = $data[5];
            $article->art_couleurs = $data[6];
            $article->art_recto_verso = $data[7];
            $article->art_quantite = $data[8];
        }
        return $catalogue;
    }

    /******************************
     * Préparation des impressions
     ******************************/
    public function preparation() {
        $data = array(
            'title' => "disponible",
            'page' => "_production/impression/preparation",
            'values' => array(
            )
        );
        return $data;
    }

    /******************************
     * Suivi de production des impmressions
     ******************************/
    public function suivi() {
        $data = array(
            'title' => "disponible",
            'page' => "_production/impression/suivi",
            'values' => array(
            )
        );
        return $data;
    }

}
// EOF