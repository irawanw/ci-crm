<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 07/07/15
 * Time: 11:37
 */
/**
 * Famille de catalogue plan marketing
 */
class Famille_P extends Famille_catalogue {
    const CODE = 'P';

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
            if (count($ligne) < 7) {
                $erreurs[] = "Article $code_article : le catalogue Plan marketing doit avoir au moins 7 colonnes";
                return $erreurs;
            }
            if (!is_numeric($ligne[3]) OR $ligne[3] < 0) {
                $erreurs[] = "Article $code_article : le prix doit être un nombre positif ou null";
            }
            if ($ligne[4] != 'non' AND $ligne[4] !='oui') {
                $erreurs[] = "Article $code_article : la colonne 'Sélectionnable' doit être 'oui' ou 'non'";
            }
            $infos = array(
                $ligne[6] // commentaire
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
        return $catalogue;
    }

}
// EOF