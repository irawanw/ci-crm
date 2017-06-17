<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 21/01/16
 * Time: 17:28
 */
class Listes_valeurs extends CI_Controller {

    /******************************
     * Liste des valeurs (appelé en AJAX par les filtres de colonnes)
     ******************************/
    public function get($table) {
        if (! $this->input->is_ajax_request()) die('');
        $q = $this->db->query("desc $table");
        if ($q->num_rows() > 0) {
            $champs = $q->result();
            $champ_valeur = $champs[1]->Field;
            $q = $this->db->select("$champ_valeur as id,$champ_valeur as valeur")
                ->get($table);
            $resultat = $q->result();
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($resultat));
        }
        else {
            die('');
        }
    }

}
// EOF