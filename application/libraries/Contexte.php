<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 23/07/15
 * Time: 12:12
 */
class Contexte {
    protected $CI;
    const NB_LIENS = 5;
    private $onglets = array("Contacts|Contacts","Produits|Catalogues","Ventes|Devis","Ventes|Commandes","Ventes|Factures",
        "Ventes|Avoirs","Ventes|Règlements","Ventes|Enseignes","Ventes|Taux de TVA","Production|Ordres de production",
        "Production|Villes","Production|Plaintes");

    public function __construct() {

        // Super-objet CodeIgniter
        $this->CI =& get_instance();
    }

    /******************************
     * Initialisation du contexte
     ******************************/
    public function init() {
        $nb_onglets = count($this->onglets);
        $contexte = array_fill(0,$nb_onglets,array('',''));
        $this->CI->session->set_userdata('contexte', $contexte);
    }

    /******************************
     * Effacement du contexte
     ******************************/
    public function efface($rang) {
        if ($rang >= 0) {
            $contexte = $this->CI->session->contexte;
            if (! isset($contexte)) return '';
            $contexte[$rang] = array('','');
            $this->CI->session->set_userdata('contexte', $contexte);
            while (--$rang >= 0) {
                if ($contexte[$rang][0] != '') return $contexte[$rang][1];
            }
        }
        return '';
    }

    /******************************
     * Renvoie le chemin de fer à afficher // <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
     ******************************/
    public function chemin_de_fer($menu) {
        $contexte = $this->CI->session->contexte;
        if (! isset($contexte)) return '';
        $uri = $this->CI->uri->uri_string();
        $menus1 = explode('|',$menu);
        if (count($menus1) == 2) {
            $rang = array_search($menu,$this->onglets);
            if ($rang !== false) {
                $contexte[$rang] = array($menus1[1],$uri);
                $this->CI->session->set_userdata('contexte', $contexte);
            }
            else {
                $rang = -1;
            }
        }
        else {
            $rang = -1;
        }
        $onglets = '';
        foreach($contexte as $k=>$c) {
            if ($c[0] != '') {
                $class = 'cdf-inactif';
                if ($rang == $k) {
                    $class = 'cdf-actif';
                }
                $onglets .= '<span class="'.$class.'">&nbsp;&nbsp;'.anchor($c[1],$c[0]).'&nbsp;';
                if ($rang == $k) {
                    $onglets .= anchor('start/efface_contexte/'.$k,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>').'&nbsp;';
                }
                $onglets .= '</span>';
            }
        }
        return $onglets;
    }

}
// EOF