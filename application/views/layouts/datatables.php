<?php
if (!empty($barre_action)) {
    if (!isset($droits)) {
        include "application/config/droits.php";
    }
    if (!isset($profil)) {
        $profil = $this->session->profil;
        if (!isset($profil)) {
            $profil = 'public';
        }
    }
    $barre_action = filtre_barre_action_par_droits($barre_action, $droits, $profil);
}

$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
$this->output->set_header("Cache-Control: post-check=0, pre-check=0 no-store, no-cache, must-revalidate");
$this->output->set_header("Pragma: no-cache");

$this->load->view('pieces/header_dt');
$this->load->view($page,$values);
$this->load->view('pieces/modal');
$this->load->view('pieces/footer_dt');

// EOF