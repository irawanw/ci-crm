<?php
$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
$this->output->set_header("Cache-Control: post-check=0, pre-check=0 no-store, no-cache, must-revalidate");
$this->output->set_header("Pragma: no-cache");

$this->load->view('global_list/header_dt');
$this->load->view($page,$values);
$this->load->view('global_list/footer_dt');
// EOF