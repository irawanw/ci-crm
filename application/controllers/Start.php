<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 20/06/15
 * Time: 11:56
 */
class Start extends CI_Controller {

    /******************************
     * Page d'accueil
     ******************************/
    public function index() {
        $pseudo = pseudo();
        if (isset($pseudo)) {
            redirect('utilisateurs/accueil');
        }
        $data = array(
            'title' => '',
            'page' => 'accueil',
            'menu' => "",
            'values' => array(
                'message' => ""
            )
        );
        $layout="layouts/standard";
        $this->load->view($layout,$data);
    }

    /******************************
     * Effacement de contexte
     ******************************/
    public function efface_contexte($rang) {
        redirect($this->contexte->efface($rang));
    }

    /******************************
     * Migration apres normalisation
     ******************************/
    public function m_devis() {
        $this->load->model('m_devis');
        $i = 0;
        $q = $this->db->select('dvi_id')
            ->get('t_devis');
        foreach($q->result() as $r) {
            $this->m_devis->trigger_articles_devis($r->dvi_id);
            $i++;
        }
        echo "$i rows updated";
    }

    /******************************
     * Migration apres normalisation
     ******************************/
    public function m_factures() {
        $this->load->model('m_factures');
        $i = 0;
        $q = $this->db->select('fac_id')
            ->get('t_factures');
        foreach($q->result() as $r) {
            $this->m_factures->trigger_lignes_factures($r->fac_id);
            $this->m_factures->trigger_imputations($r->fac_id);
            $this->m_factures->trigger_avoirs($r->fac_id);
            $i++;
        }
        echo "$i rows updated";
    }

    /******************************
     * Modification de la structure de la base de données
     ******************************/
    public function update($code) { die('Nothing to do');
        $requetes = array(
            "ALTER TABLE t_telephones CHANGE numero_internet numero_de_compte_internet VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;",
            "ALTER TABLE t_telephones ADD numero_de_tel_internet VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER numero_de_compte_internet ;",
            "ALTER TABLE t_telephones ADD forfait_ligne_fixe VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER fornisseur ,
ADD forfait_portable VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER forfait_ligne_fixe ,
ADD options VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER forfait_portable ;"
        );
        if ($code == 150) {
            foreach($requetes as $q) {
                $this->db->query($q);
            }
            echo "OK";
        }
        else {
            die('Nothing to do');
        }
    }

}
// EOF