<?php
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 22/07/15
 * Time: 20:09
 */

/******************************
 * Login de l'utilisateur
 ******************************/
if ( ! function_exists('pseudo')) {
    function pseudo() {
        $CI =& get_instance();
        $pseudo = $CI->session->utl_login;
        return $pseudo;
    }
}

/******************************
 * Messages non lus
 ******************************/
if ( ! function_exists('messages_non_lus')) {
    function messages_non_lus() {
        $CI =& get_instance();
        $CI->load->model('m_messages');
        $id = $CI->session->id;
        if (isset($id)) {
            return $CI->m_messages->nb_non_lus($id);
        }
        else {
            return 0;
        }
    }
}

/******************************
 * Nouveles tâches
 ******************************/
if ( ! function_exists('nouvelles_taches')) {
    function nouvelles_taches() {
        $CI =& get_instance();
        $CI->load->model('m_taches');
        $id = $CI->session->id;
        if (isset($id)) {
            return $CI->m_taches->nb_nouvelles($id);
        }
        else {
            return 0;
        }
    }
}

/******************************
 * Nouveles alertes
 ******************************/
if ( ! function_exists('nouvelles_alertes')) {
    function nouvelles_alertes() {
        $CI =& get_instance();
        $CI->load->model('m_alertes');
        $id = $CI->session->id;
        if (isset($id)) {
            return $CI->m_alertes->nb_non_acquittees($id);
        }
        else {
            return 0;
        }
    }
}

/******************************
 * Vérification des droits d'accès
 ******************************/
if ( ! function_exists('verifie_droits')) {
    function verifie_droits($droits,$profil,$cible) {
        if (substr($cible,0,4) == 'http') {
            $cible = 'extra';
        }
        $segments = explode('/',$cible);
        if (count($segments) == 1) {
            $cle = $segments[0].'.index';
        }
        else {
            $cle = $segments[0].'.'.$segments[1];
        }
        $cle = rtrim(ltrim($cle, '*'), '[]');
        if (array_key_exists($cle,$droits)) {
            if (! in_array($profil,$droits[$cle])) return false;
        }
        return true;
    }
}

/******************************
 * Vérification des droits d'accès (extra menu)
 ******************************/

if ( ! function_exists('verifie_droits_extra')) {
    function verifie_droits_extra($droits,$profil,$cible) {
        $segments   = explode('/',$cible);
        $cle        = $segments[count($segments)-1].'.index';       
        if (array_key_exists($cle,$droits)) {
            if (! in_array($profil,$droits[$cle])) return false;
        }
        return true;
    }
}

/******************************
 * Active ou désactive une option de la barre d'action
 ******************************/
if ( ! function_exists('modifie_etat_barre_action')) {
    /**
     * Active ou désactive une option de la barre d'action
     *
     * @param $barre_action array   Définition de barre d'action
     * @param $texte        string  Texte du bouton
     * @param $etat         boolean TRUE (actif) ou FALSE (inactif)
     *
     * @return array    Une nouvelle barre d'action
     */
    function modifie_etat_barre_action($barre_action,$texte,$etat) {
        foreach ($barre_action as $i => $bloc) {
            foreach ($bloc as $bouton => $params) {
                if ($bouton == $texte) {
                    $barre_action[$i][$bouton][2] = (bool)$etat;
                }
            }
        }
        return $barre_action;
    }
}

/******************************
 * Modifie une action (URL) de la barre d'action
 ******************************/
if ( ! function_exists('modifie_action_barre_action')) {
    /**
     * Modifie une action (URL) de la barre d'action
     *
     * @param $barre_action array   Définition de barre d'action
     * @param $action       string  URL contrôleur
     * @param $nouveau      string  Nouvel URL contrôleur
     *
     * @return array Une nouvelle barre d'action
     */
    function modifie_action_barre_action($barre_action,$action,$nouveau) {
        foreach ($barre_action as $i => $bloc) {
            foreach ($bloc as $bouton => $params) {
                if ($params[0] == $action) {
                    $barre_action[$i][$bouton][0] = $nouveau;
                }
                elseif ($params[0] == '*'.$action) {
                    $barre_action[$i][$bouton][0] = '*'.ltrim($nouveau, '*');
                }
                elseif ($params[0] == $action.'[]') {
                    $barre_action[$i][$bouton][0] = rtrim($nouveau, '[]').'[]';
                }
                elseif ($params[0] == '*'.$action.'[]') {
                    $barre_action[$i][$bouton][0] = '*'.rtrim(ltrim($nouveau, '*'), '[]').'[]';
                }
            }
        }
        return $barre_action;
    }
}

/******************************
 * Initialise les actions (URL) de la barre d'action
 ******************************/
if ( ! function_exists('initialise_action_barre_action')) {
    /**
     * Initialise la barre d'action pour un élément cible
     *
     * @param $barre_action array   Définition de barre d'action
     * @param $valeur_init  string  Texte à ajouter aux URLs
     * @param $action       string  URL contrôleur spécifique (optionel)
     *
     * @return array Une nouvelle barre d'action
     */
    function initialise_action_barre_action($barre_action, $valeur_init, $action = null) {
        $convertisseur = function($url, $action) use ($valeur_init) {
            if ($url == $action) {
                return $url.'/{'.$valeur_init.'}';
            }
            elseif ($url == '*'.$action) {
                return $url.'/{'.$valeur_init.'}';
            }
            elseif ($url == $action.'[]') {
                return rtrim($url, '[]').'/{'.$valeur_init.'}[]';
            }
            elseif ($url == '*'.$action.'[]') {
                return rtrim($url, '[]').'/{'.$valeur_init.'}[]';
            }
        };

        foreach ($barre_action as $i => $bloc) {
            foreach ($bloc as $bouton => $params) {
                if ($action !== null) {
                    $barre_action[$i][$bouton][0] = $convertisseur($params[0], $action);
                } elseif (substr($params[0], -2) == '[]') {
                    $barre_action[$i][$bouton][0] = substr($params[0], 0, -2).'/{'.$valeur_init.'}[]';
                } else {
                    $barre_action[$i][$bouton][0] .= '/{'.$valeur_init.'}';
                }
            }
        }
        return $barre_action;
    }
}

/******************************
 * Active ou désactive plusieurs options de la barre d'action
 ******************************/
if ( ! function_exists('modifie_etats_barre_action')) {
    /**
     * Active ou désactive plusieurs options de la barre d'action
     *
     * @param $barre_action array   Définition de barre d'action
     * @param $actions      array   Liste d'action (URL contrôleur => état (TRUE / FALSE))
     *
     * @return array    Une nouvelle barre d'action
     */
    function modifie_etats_barre_action($barre_action,$actions) {
        foreach ($actions as $action => $etat) {
            foreach ($barre_action as $i => $bloc) {
                foreach ($bloc as $bouton => $params) {
                    if ($params[0] == $action || $params[0] == '*'.$action
                        || $params[0] == '*'.$action.'[]' || $params[0] == $action.'[]') {
                        $barre_action[$i][$bouton][2] = (bool)$etat;
                    }
                }
            }
        }
        return $barre_action;
    }
}

/******************************
 * Enlève les actions non-autorisées
 ******************************/
if ( ! function_exists('filtre_barre_action_par_droits')) {
    /**
     * Envlève les actions de la barre si l'utilisateur n'a pas accès
     *
     * @param $barre_action array  Définition de barre d'action
     * @param $droits       array  Liste des droits
     * @param $profil       string Profil utilisateur
     *
     * @return array Une nouvelle barre d'action
     */
    function filtre_barre_action_par_droits($barre_action,$droits,$profil) {
        $b_a = array();
        foreach ($barre_action as $bloc) {
            $section = array();
            foreach ($bloc as $texte => $params) {
                if (verifie_droits($droits,$profil,$params[0])) {
                    $section[$texte] = $params;
                }
            }
            if (!empty($section)) {
                $b_a[] = $section;
            }
        }
        return $b_a;
    }
}

/******************************
 * Vérification des droits pour le menu extra
 ******************************/
if ( ! function_exists('verifie_droits_extra')) {
    function verifie_droits_extra($droits,$profil,$cible) {
        $segments   = explode('/',$cible);
        $cle        = $segments[count($segments)-1].'.index';
        if (array_key_exists($cle,$droits)) {
            if (! in_array($profil,$droits[$cle])) return false;
        }
        return true;
    }
}

