<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 31/03/15
 * Time: 16:35
 */

/******************************
 * Formatte les dates
 ******************************/
if ( ! function_exists('formatte_date')) {
    function formatte_date($date) {
        if (strlen($date) >= 10) {
            return substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4);
        }
        else {
            return $date;
        }
    }
}
if ( ! function_exists('formatte_dateheure')) {
    function formatte_dateheure($date) {
        if(strlen($date) ==19) {
            return substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4).substr($date,10);
        }
        else {
            return $date;
        }
    }
}
if ( ! function_exists('formatte_date_to_bd')) {
    function formatte_date_to_bd($date) {
        if (strlen($date) >= 10) {
            return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2).substr($date,10);
        }
        else {
            return $date;
        }
    }
}

/******************************
 * Formatte les nombres decimaux
 ******************************/
if ( ! function_exists('formatte_decimal')) {
    function formatte_decimal($nombre) {
        return str_replace('.',',',$nombre);
    }
}

/******************************
 * Formatte les booléens
 ******************************/
if ( ! function_exists('formatte_booleen')) {
    function formatte_booleen($nombre) {
        return $nombre==0?'non':'oui';
    }
}

/******************************
 * Formatte les textes longs
 ******************************/
if ( ! function_exists('formatte_texte_long')) {
    function formatte_texte_long($texte) {
        return str_replace("\n","<br />",$texte);
    }
}

/******************************
 * Formatte les textes down
 * For replace Group Concat list
 ******************************/
if ( ! function_exists('formatte_texte_down')) {
	function formatte_texte_down($texte) {
		return str_replace(",","<br/>",$texte);
	}
}
/******************************
 * Formatte les URL
 ******************************/
if ( ! function_exists('formatte_url')) {
    function formatte_url($texte) {
        return strlen($texte)>0?'<a href="http://'.$texte.'" target="_blank">'.$texte.'</a>':'';
    }
}

/******************************
 * Formatte les numeraux de téléphone
 ******************************/
if ( ! function_exists('formatte_tel')) {
    function formatte_tel($numero) {
        if (strlen($numero == 10)) {
            return substr($numero,0,2).' '.substr($numero,2,2).' '.substr($numero,4,2).' '.substr($numero,6,2).' '.substr($numero,8,2);
        }
        return $numero;
    }
}

/******************************
 * Formatte les champs de la base de données
 ******************************/
if ( ! function_exists('formatte_sql_xedit')) {
	function formatte_sql_xedit($cible, $champ_id, $champ_texte, $attributs = '') {
		if (!empty($attributs)) {
			$attributs = _stringify_attributes($attributs);
		}
		else {
			$attributs = '';
		}
		if($champ_texte != '')
		{
			return "CONCAT('<a href=\"".$cible."\"".$attributs." onclick=\"getModal(\'',$champ_id,'_',$champ_texte,'\')\">',$champ_texte,'</a>') as $champ_texte";
		}
		else
		{
			return "CONCAT('<a href=\"".$cible."\"".$attributs." onclick=\"getModal(\''0_',$champ_texte,'\')\">Ajouter Commentaire</a>') as $champ_texte";
		}
	}
}
if ( ! function_exists('formatte_sql_lien')) {
    function formatte_sql_lien($cible, $champ_id, $champ_texte, $attributs = 'class="view-detail"') {
        if (!empty($attributs)) {
            $attributs = _stringify_attributes($attributs);
        }
        else {
            $attributs = '';
        }
        return "CONCAT('<a href=\"".site_url($cible)."/',$champ_id,'\"".$attributs.">',$champ_texte,'</a>') as $champ_texte";
    }
}
if ( ! function_exists('formatte_sql_date')) {
    function formatte_sql_date($champ, $format="'%d/%m/%Y'") {
        return "DATE_FORMAT($champ,$format) AS $champ";
        return "";
    }
}
if ( ! function_exists('formatte_sql_date_heure')) {
    function formatte_sql_date_heure($champ, $format="'%d/%m/%Y %H:%i'") {
        return "DATE_FORMAT($champ,$format) AS $champ";
    }
}
if ( ! function_exists('formatte_sql_date_heure_lien')) {
    function formatte_sql_date_heure_lien($cible, $champ_id, $champ_texte, $attributs = 'class="view-detail"', $format="'%d/%m/%Y %H:%i'") {
        if (!empty($attributs)) {
            $attributs = _stringify_attributes($attributs);
        }
        else {
            $attributs = '';
        }
        return "CONCAT('<a href=\"".site_url($cible)."/',$champ_id,'\"".$attributs.">',DATE_FORMAT($champ_texte,$format),'</a>') as $champ_texte";
    }
}

/******************************
 * Construit un lien vers la téléphonie
 ******************************/
if ( ! function_exists('construit_lien_tel')) {
    function construit_lien_tel($id,$texte) {
        return strlen($texte)>0?anchor('evenements/appel/'.$id,$texte,'class="open-form"'):'';
    }
}

/******************************
 * Construit un lien vers la messagerie
 ******************************/
if ( ! function_exists('construit_lien_mail')) {
    function construit_lien_mail($id,$texte) {
        return strlen($texte)>0?anchor('evenements/email_contact/'.$id,$texte,'class="open-form"'):'';
    }
}

/******************************
 * Construit l'URL vers un fichier
 ******************************/
if ( ! function_exists('construit_url_fichier')) {
    function construit_url_fichier($chemin, $fichier) {
        if (strlen($chemin == 0)) {
            $pos = strrpos($fichier,'/');
            if ($pos !== false) {
                $chemin = substr($fichier,0,$pos);
                $fichier = substr($fichier, $pos + 1);
            }
        }
        return strlen($fichier) > 0 ? base_url("$chemin/$fichier") : null;
    }
}

/******************************
 * Construit un lien vers un fichier
 ******************************/
if ( ! function_exists('construit_lien_fichier')) {
    function construit_lien_fichier($chemin,$fichier) {
        $url = construit_url_fichier($chemin, $fichier);
        return $url ? '<a href="'.$url.'" target="_blank">'.basename($url).'</a>' : '';
    }
}

/******************************
 * Construit un lien vers le formulaire de détail
 ******************************/
if ( ! function_exists('construit_lien_detail')) {
    function construit_lien_detail($controleur,$id,$texte) {
        return strlen($texte)>0?anchor($controleur.'/detail/'.$id,$texte,'class="view-detail" target="_blank"'):'';
    }
}

/******************************
 * Renvoie le taux de TVA actuel
 ******************************/
if ( ! function_exists('tva')) {
    function tva() {
        $CI =& get_instance();
        $CI->load->model("m_taux_tva");
        $tva = $CI->m_taux_tva->tva_en_service();
        $taux = 0;
        if (is_array($tva) AND count($tva) == 1) {
            $taux = $tva[0]->tva_taux;
        }
        return $taux;
    }
}

/******************************
 * valide un fichier téléchargé
 ******************************/
if ( ! function_exists('valide_chargement')) {
    function valide_chargement($fichier) {
        $resultat = new stdClass();
        if (array_key_exists($fichier,$_FILES)){
            $f = $_FILES[$fichier];
            if ($f['error'] == 0) {
                $extension = strrchr($f['name'], '.');
                $nom_fichier = $f['tmp_name'].$extension;
                rename($f['tmp_name'],$nom_fichier);
                $resultat->valide = true;
                $resultat->nom = $nom_fichier;
                $resultat->extension = $extension;
            }
            else {
                switch($f['error']) {
                    case 1:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 2:
                        $erreur = 'Le fichier '.$f['name'].' est trop volumineux.';
                        break;
                    case 3:
                        $erreur = 'Le fichier '.$f['name']." n'a été que partiellement téléchargé.";
                        break;
                    case 4:
                        if (file_exists($f['tmp_name'])) {
                            unlink($f['tmp_name']);
                            $erreur = "Un fichier de même nom existait. Veuillez recommencer.";
                        }
                        else {
                            $resultat->valide = true;
                            $resultat->nom = '';
                            return $resultat;
                        }
                        break;
                    case 7:
                        $erreur = 'Le fichier '.$f['name']." n'a pu être enregistré.";
                        break;
                    default:
                        $erreur = 'Erreur lors du téléchargement du fichier '.$f['name'];
                }
                $resultat->valide = false;
                $resultat->erreur = $erreur;
            }
        }
        else {
            $resultat->valide = true;
            $resultat->nom = '';
        }
        return $resultat;
    }
}

function debug($data,$exit='1')
{
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
	if($exit ==1)
		exit();
}