<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 17/01/16
 * Time: 17:15
 */
require 'application/third_party/PHPExcel/IOFactory.php';
class Reprise extends CI_Controller {
    private $cache = 'mem';

    public function index() {
        $message = "Prêt !";
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Vidage des tables
     ******************************/
    public function raz() {

        // vidage des tables
        $this->db->query('truncate r_contacts');
        $this->db->query('truncate r_correspondants');
        $message = "Tables vidées";
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Vidage des tables devis
     ******************************/
    public function raz_devis() {

        // vidage des tables
        $this->db->query('truncate r_articles_devis');
        $this->db->query('truncate r_devis');
        $message = "Tables vidées";
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Importation des clients
     ******************************/
    public function clients() {

        // reprise des clients
        $message = $this->_reprise_clients();
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Importation des prospects
     ******************************/
    public function prospects() {

        // reprise des prospects
        $message = $this->_reprise_prospects();
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Importation des devis
     ******************************/
    public function devis($enseigne) {

        // reprise des devis
        $message = $this->_reprise_devis($enseigne);
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Importation des factures
     ******************************/
    public function factures() {

        // reprise des devis
        $message = $this->_reprise_factures();
        $this->load->view('reprise',array('message'=>$message));
    }

    /******************************
     * Importation des clients
     ******************************/
    private function _reprise_clients() {

        // initialisation
        //$fichier = "reprise/liste des clients BAL IDF.xlsx";
        $fichier = "reprise/liste des clients PUBLIBOXE.xlsx";
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(false);
        if ($this->cache == "mem") {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(
                'memoryCacheSize' => '512k'
            );
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        } else {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        }
        $PHPExcel = $objReader->load($fichier);
        $sheet = $PHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        // parcours du fichier
        for ($i = 2; $i <= $highestRow; $i++) {
            $contact = array(
                'ctc_id_comptable' => $this->_cellule($sheet,"A$i"),
                'ctc_nom' => $this->_cellule($sheet,"B$i"),
                'ctc_fax' => $this->_telephone($this->_cellule($sheet,"D$i")),
                'ctc_telephone' => $this->_telephone($this->_cellule($sheet,"E$i")),
                'ctc_mobile' => $this->_telephone($this->_cellule($sheet,"F$i")),
                'ctc_livr_adresse' => $this->_cellule($sheet,"G$i"),
                'ctc_livr_cp' => $this->_cellule($sheet,"H$i"),
                'ctc_livr_ville' => $this->_cellule($sheet,"I$i"),
                'ctc_email' => $this->_cellule($sheet,"J$i"),
                'ctc_adresse' => $this->_adresse($this->_cellule($sheet,"K$i"), $this->_cellule($sheet,"L$i"), $this->_cellule($sheet,"M$i")),
                'ctc_cp' => $this->_cellule($sheet,"N$i"),
                'ctc_ville' => $this->_cellule($sheet,"O$i"),
                'ctc_site' => $this->_cellule($sheet,"T$i"),
                'ctc_commercial' => $this->_commercial($this->_cellule($sheet,"u$i")),
                'ctc_client_prospect' => 2
            );
            $this->db->insert('t_contacts', $contact);

            $correspondant = array(
                'cor_nom' => $this->_cellule($sheet,"V$i"),
                'cor_telephone1' => $this->_telephone($this->_cellule($sheet,"W$i")),
                'cor_telephone2' => $this->_telephone($this->_cellule($sheet,"X$i")),
                'cor_fax' => $this->_telephone($this->_cellule($sheet,"Y$i")),
                'cor_email' => $this->_cellule($sheet,"Z$i"),
                'cor_contact' => $this->db->insert_id()
            );
            if ($correspondant['cor_nom'] != '') {
                $this->db->insert('t_correspondants', $correspondant);
            }
        }

        $message = "<p>Importation de ".($highestRow-1)." lignes</p>";
        $PHPExcel->disconnectWorksheets();
        return $message;
    }

    /******************************
     * Importation des prospects
     ******************************/
    private function _reprise_prospects() {

        // initialisation
        $fichier = "reprise/liste des prospects publiboxe.xlsx";
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(false);
        if ($this->cache == "mem") {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(
                'memoryCacheSize' => '512k'
            );
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        } else {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        }
        $PHPExcel = $objReader->load($fichier);
        $sheet = $PHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        // parcours du fichier
        for ($i = 2; $i <= $highestRow; $i++) {
            $contact = array(
                'ctc_id_prospect' => $this->_cellule($sheet,"A$i"),
                'ctc_nom' => $this->_cellule($sheet,"B$i"),
                'ctc_fax' => $this->_telephone($this->_cellule($sheet,"N$i")),
                'ctc_telephone' => $this->_telephone($this->_cellule($sheet,"L$i")),
                'ctc_mobile' => $this->_telephone($this->_cellule($sheet,"M$i")),
                'ctc_email' => $this->_cellule($sheet,"O$i"),
                'ctc_adresse' => $this->_adresse($this->_cellule($sheet,"F$i"), $this->_cellule($sheet,"G$i"), $this->_cellule($sheet,"H$i")),
                'ctc_cp' => $this->_cellule($sheet,"K$i"),
                'ctc_ville' => $this->_cellule($sheet,"J$i"),
                'ctc_site' => $this->_cellule($sheet,"P$i"),
                'ctc_commercial' => $this->_commercial($this->_cellule($sheet,"Q$i")),
                'ctc_client_prospect' => 1
            );
            $this->db->insert('t_contacts', $contact);

            $correspondant = array(
                'cor_nom' => $this->_cellule($sheet,"S$i"),
                'cor_telephone1' => $this->_telephone($this->_cellule($sheet,"U$i")),
                'cor_telephone2' => $this->_telephone($this->_cellule($sheet,"V$i")),
                'cor_fax' => $this->_telephone($this->_cellule($sheet,"W$i")),
                'cor_email' => $this->_cellule($sheet,"Z$i"),
                'cor_contact' => $this->db->insert_id()
            );
            if ($correspondant['cor_nom'] != '') {
                $this->db->insert('t_correspondants', $correspondant);
            }
        }

        $message = "<p>Importation de ".($highestRow-1)." lignes</p>";
        $PHPExcel->disconnectWorksheets();
        return $message;
    }

    /******************************
     * Importation des devis
     ******************************/
    private function _reprise_devis($enseigne) {

        /*$compte = $this->db->count_all('r_devis');
        if ($compte > 0) {
            $message = "Table non vide";
            error_log($message);
            return $message;
        }*/

        // initialisation
        $fichier = "reprise/devis.xlsx";
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(false);
        if ($this->cache == "mem") {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(
                'memoryCacheSize' => '512k'
            );
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        } else {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        }
        $PHPExcel = $objReader->load($fichier);
        $sheet = $PHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        // parcours du fichier
        $message = '';
        $nb_devis = 0;
        $num_devis = '';
        for ($i = 2; $i <= $highestRow; $i++) {
            $numero = $this->_cellule($sheet,"A$i");
            if ($numero != '') {
                $devis = array(
                    'dvi_reference' => $this->_cellule($sheet,"A$i"),
                    'dvi_date' => $this->_date($this->_cellule($sheet,"B$i")),
                    'dvi_tva' => 0.2,
                    'dvi_societe_vendeuse' => $enseigne,
                    'dvi_etat' => 3
                );
                $devis['dvi_numero'] = intval(substr($devis['dvi_reference'],2));

                // recherche du contact
                $contact = $this->_cellule($sheet,"C$i");
                $id_client = $this->_contact($contact);
                if ($id_client == 0) {
                    $message .= "<br />Contact $contact non trouvé";
                    $num_facture = '*';
                    continue;
                }

                $devis['dvi_client'] = $id_client;
                $this->db->insert('t_devis', $devis);
                $num_devis = $this->db->insert_id();
                $nb_devis++;
            }
            else {
                if ($num_devis == '') {
                    $message .="<br />Pas de devis associé en ligne $i";
                    break;
                }
                if ($num_devis == '*') {
                    continue;
                }
                    $article = array(
                    'ard_article' => 'G',
                    'ard_prix' => $this->_valeur($this->_cellule($sheet,"D$i")),
                    'ard_quantite' => $this->_cellule($sheet,"E$i"),
                    'ard_description' => $this->_cellule($sheet,"F$i"),
                    'ard_devis' => $num_devis
                );
                if ($article['ard_description'] != '') {
                    $this->db->insert('t_articles_devis', $article);
                }
            }
        }

        $message .= "<p>Importation de ".($nb_devis)." devis</p>";
        $PHPExcel->disconnectWorksheets();
        return $message;
    }

    /******************************
     * Importation des factures
     ******************************/
    private function _reprise_factures() {

        // initialisation
        $fichier = "reprise/LISTE FACTURES PUBLIBOX.xlsx";
        $inputFileType = PHPExcel_IOFactory::identify($fichier);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(false);
        if ($this->cache == "mem") {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(
                'memoryCacheSize' => '512k'
            );
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        } else {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        }
        $PHPExcel = $objReader->load($fichier);
        $sheet = $PHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        // parcours du fichier
        $message = '';
        $nb_factures = 0;
        $num_facture = '';
        for ($i = 2; $i <= $highestRow; $i++) {
            $numero = $this->_cellule($sheet,"A$i");
            if ($numero != '') {

                // sauvegarde de la dernière ligne de facture
                if (isset($article)) {
                    $this->db->insert('t_lignes_factures', $article);
                    $article = null;
                }
                $facture = array(
                    'fac_reference' => $this->_cellule($sheet,"A$i"),
                    'fac_date' => $this->_date($this->_cellule($sheet,"B$i")),
                    'fac_tva' => 0.2,
                    'fac_etat' => 2
                );
                $facture['fac_numero'] = intval(substr($facture['fac_reference'],2));

                // recherche du contact
                $contact = $this->_cellule($sheet,"C$i");
                $id_client = $this->_contact($contact);
                if ($id_client == 0) {
                    $message .= "<br />Contact $contact non trouvé";
                    $num_facture = '*';
                    continue;
                }

                // recherche du devis
                $q = $this->db->where('dvi_client',$id_client)
                    ->get('t_devis');
                $nb_devis = $q->num_rows();
                if ($nb_devis == 0) {

                    // création d'un devis pour servir de support à la facture
                    $devis = array(
                        'dvi_date' => $this->_date($this->_cellule($sheet,"B$i")),
                        'dvi_tva' => 0.2,
                        'dvi_societe_vendeuse' => 3, // TODO attention, ajuster cette valeur
                        'dvi_etat' => 3,
                        'dvi_client' => $id_client
                    );
                    $this->db->insert('t_devis', $devis);
                    $id_devis = $this->db->insert_id();


                }
                else {

                    // sélection du premier devis disponible
                    $id_devis = $q->row()->dvi_id;
                }

                // passage du devis à l'état accepté
                $this->db->where('dvi_id',$id_devis)
                    ->update('t_devis',array('dvi_etat'=>4));

                // passage du client à l'état client
                $this->db->where('ctc_id',$id_client)
                    ->update('t_contacts',array('ctc_client_prospect'=>2));

                // création de la commande validée
                $cmd = array(
                    'cmd_numero' => 0,
                    'cmd_date' => $facture['fac_date'],
                    'cmd_devis' => $id_devis,
                    'cmd_etat' => 4
                );
                $this->db->insert('t_commandes',$cmd);
                $id_cmd = $this->db->insert_id();

                // création de la facture
                $facture['fac_commande'] = $id_cmd;
                $this->db->insert('t_factures', $facture);
                $num_facture = $this->db->insert_id();
                $nb_factures++;
            }
            else {
                if ($num_facture == '') {
                    $message .="<br />Pas de facture associée en ligne $i";
                    break;
                }
                if ($num_facture == '*') {
                    continue;
                }
                if (! isset($article)) {
                    $article = array(
                        'lif_code' => 'G',
                        'lif_prix' => $this->_valeur($this->_cellule($sheet, "D$i")),
                        'lif_quantite' => $this->_cellule($sheet, "E$i"),
                        'lif_description' => $this->_cellule($sheet, "F$i"),
                        'lif_facture' => $num_facture
                    );
                }
                else {
                    if (intval($this->_valeur($this->_cellule($sheet, "D$i")))) {
                        $this->db->insert('t_lignes_factures', $article);
                        $article = array(
                            'lif_code' => 'G',
                            'lif_prix' => $this->_valeur($this->_cellule($sheet, "D$i")),
                            'lif_quantite' => $this->_cellule($sheet, "E$i"),
                            'lif_description' => $this->_cellule($sheet, "F$i"),
                            'lif_facture' => $num_facture
                        );
                    }
                    else {
                        $article['lif_description'] .= '<br />'.$this->_cellule($sheet, "F$i");
                    }
                }
            }
        }
        // sauvegarde de la dernière ligne de facture
        if (isset($article)) {
            $this->db->insert('t_lignes_factures', $article);
        }

        $message .= "<p>Importation de ".($nb_factures)." factures</p>";
        $PHPExcel->disconnectWorksheets();
        return $message;
    }

    private function _contact($contact) {
        $contact = str_replace('O','',$contact);
        $id_client = $this->_trouve_contact($contact);
        return $id_client;
    }

    private function _trouve_contact($contact) {
        $q = $this->db->where('ctc_id_comptable', $contact)
            ->where('ctc_id >',246)
            ->get('t_contacts');
        if ($q->num_rows() != 1) return 0;
        return $q->row()->ctc_id;
    }

    private function _adresse($l1,$l2,$l3) {
        $adresse = $l1;
        if (trim($l2) != '') {
            $adresse .= "\n$l2";
        }
        if (trim($l3) != '') {
            $adresse .= "\n$l3";
        }
        return $adresse;
    }

    private function _commercial($code) {
        switch($code) {
            case 'REP0001':
                return 1;
            case 'REP0002':
                return 2;
            default:
                return $code;
                break;
        }
    }

    private function _telephone($numero) {
        $numero = str_replace(array('.',' '),'',$numero);
        if (strlen($numero) >8){
            return str_pad($numero, 10, '0', STR_PAD_LEFT);
        }
        return $numero;
    }

    private function _valeur($valeur) {
        $valeur = str_replace(',','.',$valeur);
        return $valeur;
    }

    private function _date($date) {
        $dateExcel = $date-1;
        $dateExcel = date("y-m-d", mktime(0,0,0,1, $dateExcel, 1900));
        return $dateExcel;
    }

    private function _cellule($sheet,$adresse) {
        $valeur = $sheet->getCell($adresse)->getValue();
        if (! isset($valeur)) return '';
        return $valeur;
    }
}
// EOF