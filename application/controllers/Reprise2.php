<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 17/01/16
 * Time: 17:15
 */
require 'application/third_party/PHPExcel/IOFactory.php';

/**
 * Reprise des factures
 *
 * @property M_societes_vendeuses $m_societes_vendeuses
 * @property CI_DB_query_builder  $db
 */
class Reprise2 extends CI_Controller {
    private $cache = 'mem';
    private $enseignes;
    private $fichiers;
    private $repertoire_reprise = FCPATH.'fichiers/reprise/';

    private $columns = array(
        'A' => 'Numéro Facture',
        'B' => 'Date',
        'C' => 'Code client',
        'D' => 'Discount',
        'E' => 'PUHT',
        'F' => 'Quantité',
        'G' => 'Libellé',
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('m_societes_vendeuses');
        $this->load->helper(array('form','directory'));
        $this->enseignes = $this->_enseignes();
        $this->fichiers = $this->_fichiers();
        //$this->output->enable_profiler(TRUE);
    }

    public function index() {
        $messages = array();
        $data = array(
            'messages' => $messages,
            'enseignes' => $this->enseignes['data'],
            'fichiers' => $this->fichiers,
            'format' => 'Excel',
            'colonnes' => $this->columns,
        );
        $this->load->view('reprise2',$data);
    }

    private function _enseignes() {
        $enseignes = $this->m_societes_vendeuses->liste(null);

        foreach ($enseignes['data'] as $_i => $e) {
            $scv_id = $e->scv_id;
            if ($scv_id != 4 && $scv_id != 5 && $scv_id != 6) {
                unset($enseignes['data'][$_i]);
                --$enseignes['recordsTotal'];
                --$enseignes['recordsFiltered'];
            }
        }
        return $enseignes;
    }

    private function _fichiers() {
        $files = directory_map($this->repertoire_reprise);
        foreach ($files as $i => $file) {
            if (!is_string($file) || preg_match('/index\.html?$/i', $file)) {
                unset($files[$i]);
            }
        }
        return $files;
    }

    /******************************
     * Importation des factures
     ******************************/
    public function factures() {
        $enseigne = $this->input->post('enseigne');
        $fichier = $this->input->post('fichier');
        $action = $this->input->post('action');

        // reprise des factures
        if ($this->_enseigne($enseigne) !== null) {
            $test = ($action != 'effectuer');
            $messages = $this->_reprise_factures($enseigne, $fichier, $test);
        }
        else {
            $messages = array($this->_message('danger',"Veuillez indiquer un nom de fichier et une enseigne"));
        }
        $data = array(
            'messages' => $messages,
            'enseignes' => $this->enseignes['data'],
            'enseigne' => $enseigne,
            'fichiers' => $this->fichiers,
            'fichier' => $fichier,
            'colonnes' => $this->columns,
        );
        $this->load->view('reprise2', $data);
    }

    /**
     * @param $fichier string
     *
     * @return bool
     */
    private function _fichier_existe($fichier) {
        $fichiers = $this->fichiers;
        $chemin = preg_split('![/\\\\]+!', $fichier);
        while (count($chemin) > 1) {
            $repertoire = array_shift($chemin).DIRECTORY_SEPARATOR;
            if (!isset($fichiers[$repertoire]) || !is_array($fichiers[$repertoire])) {
                return false;
            }
            $fichiers = $fichiers[$repertoire];
        }
        $fichier = array_pop($chemin);
        return in_array($fichier, $fichiers);
    }

    /**
     * @param $enseigne integer
     * @return M_societes_vendeuses|NULL
     */
    private function _enseigne($enseigne) {
        foreach ($this->enseignes['data'] as $e) {
            if ($e->scv_id == $enseigne) {
                return $e;
            }
        }
        return null;
    }

    /**
     * Prepare un message
     *
     * @param $type Type de message
     * <ul>
     *  <li>success</li>
     *  <li>info</li>
     *  <li>warning</li>
     *  <li>danger</li>
     * </ul>
     * @param $text
     * @param bool $html TRUE si le $text est de l'HTML
     * @return array
     */
    private function _message($type, $text, $html = false) {
        $message = array(
            'type' => $type,
        );
        if ($html) {
            $message['html'] = $text;
        } else {
            $message['text'] = $text;
        }
        return $message;
    }

    /**
     * Verifies that the spreadsheet has the expected column headers
     *
     * @param $sheet
     * @return array|bool TRUE on valid, an array with 2 values on error.
     *  First value is column that failed, second value is the expected header.
     */
    protected function _verification_format_fichier($sheet)
    {
        foreach ($this->columns as $column => $header) {
            if (strcasecmp(trim($this->_cellule($sheet,$column.'1')), $header) != 0) {
                return array($column, $header);
            }
        }

        return true;
    }

    /**
     * Donne l'id CRM du contact pour l'id comptable et pour l'enseigne donnés
     *
     * @param string  $id_comptable
     * @param integer $enseigne
     *
     * @return integer|null L'id du contact ou NULL si pas trouvé
     */
    protected function _client_id($id_comptable, $enseigne) {

        $contact = $this->db->select('t_contacts.*')
            ->join('t_id_comptable', 'ctc_id = idc_contact')
            ->where('idc_id_comptable', $id_comptable)
            ->where('idc_societe_vendeuse', $enseigne)
            ->get('t_contacts')
            ->row();

        if (!empty($contact)) {
            return $contact->ctc_id;
        }
        return null;
    }

    /**
     * @param string $fac_reference
     * @param integer $enseigne
     *
     * @return integer|NULL
     */
    protected function _facture_existante($fac_reference, $enseigne) {
        $facture = $this->db->where('fac_reference', $fac_reference)
            ->join('t_commandes', 'fac_commande = cmd_id')
            ->join('t_devis', 'cmd_devis = dvi_id')
            ->where('dvi_societe_vendeuse', $enseigne)
            ->where('fac_inactif IS NULL')
            ->where('dvi_inactif IS NULL')
            ->where('cmd_inactif IS NULL')
            ->limit(1)
            ->get('t_factures')
            ->row();
        if (empty($facture)) {
            return null;
        }
        return (int)$facture->fac_id;
    }

    /******************************
     * Importation des factures
     *
     * @return array Liste de messages
     ******************************/
    private function _reprise_factures($enseigne,$fichier,$test=true) {
        $messages = array();
        if ($test) {
            $messages[] = $this->_message('danger', "<strong>Simulation de la reprise !</strong>", 'html');
            $texte_importees = "valides à créer";
            $texte_ajoutees = 'seraient ajoutées (numéros CRM fictifs)';
            $texte_nonimportees = "invalides";
            $texte_nonimportee = "invalide";
        } else {
            $texte_importees = "importées";
            $texte_ajoutees = 'ajoutées';
            $texte_nonimportees = "invalides (non-importées)";
            $texte_nonimportee = "invalide (non-importée)";
        }

        $messages[] = $this->_message(
            'info',
            "Fichier : ".$fichier."\n"
                ."Enseigne : ".strip_tags($this->_enseigne($enseigne)->scv_nom)." (n° ".$enseigne.")"
        );

        // initialisation
        if (!$this->_fichier_existe($fichier)) {
            $messages[] = $this->_message('danger', "Fichier non trouvé");
            return $messages;
        }
        $chemin_fichier = $this->repertoire_reprise.$fichier;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($chemin_fichier);
        } catch (PHPExcel_Reader_Exception $e) {
            $messages[] = $this->_message('danger', "Fichier non compatible");
            return $messages;
        }

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
        $PHPExcel = $objReader->load($chemin_fichier);
        $sheet = $PHPExcel->getSheet(0);

        // Format verification
        $validation = $this->_verification_format_fichier($sheet);
        if ($validation !== true) {
            $messages[] = $this->_message('danger', "Fichier ne contient pas les données attendues. Colonne [ ".$validation[0]." ] n'est pas \"".$validation[1].'"');
            return $messages;
        }

        // Fichier d'audit de l'importation
        $fichier_audit = $fichier.'.traitement_'.date('Y-m-d').'_'.time().(($test) ? '_test' : '').'.txt';
        if (!is_dir($this->repertoire_reprise.'/audit/')) {
            if (!mkdir($this->repertoire_reprise.'/audit/')) {
                $messages[] = $this->_message('danger', 'Impossible de créer le répertoire d\'audit');
                return $messages;
            }
        }

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        // parcours du fichier
        $nb_factures = 0;
        $nb_factures_x = 0;
        $nb_factures_existantes = 0;
        $derniere_ligne = 0;

        $articles = array();
        $article = null;
        $facture = null;
        $devis = null;
        $cmd = null;
        $importer = false;

        $id_comptables = array();
        $fac_references = array();
        $reconciliees = array();
        $factures_invalides = array();

        $fac_id_fictif = 1000000;

        // Listes des numéros et références de factures trouvés dans le fichier
        // pour détecter les doublons dans le même fichier
        $factures = array();
        $numeros = array();

        for ($i = 2; $i <= $highestRow; $i++) {
            $numero = $this->_cellule($sheet,"A$i");
            if ($numero != '') {
                // Nouvelle facture détectée

                // Finalisation de la facture précédente
                if (isset($article)) {
                    if ($importer) {
                        $articles[] = $article;

                        $fac_reference = $facture['fac_reference'];
                        $facture_existante = $this->_facture_existante($fac_reference, $enseigne);
                        if (!$facture_existante) {
                            if (!$test) {
                                $fac_references[$fac_reference] = $this->_finaliser_import_facture($devis, $cmd, $facture, $articles);
                            } else {
                                $fac_references[$fac_reference] = $fac_id_fictif++;
                            }
                            ++$nb_factures;
                        } else {
                            $reconciliees[$fac_reference] = $facture_existante;
                            ++$nb_factures_existantes;
                        }
                        $derniere_ligne = $i - 1;
                    }
                }

                $articles = array();
                $article = null;
                $facture = null;
                $devis = null;
                $cmd = null;
                $importer = false;
                $id_contact = null;

                $id_comptable = ltrim(trim((string)$this->_cellule($sheet,"C$i")), '0');
                if (!empty($id_comptable)) {
                    if (!array_key_exists($id_comptable, $id_comptables)) {
                        $id_comptables[$id_comptable] = $this->_client_id($id_comptable, $enseigne);
                    }
                    $id_contact = $id_comptables[$id_comptable];
                }

                if (!empty($id_contact)) {
                    // Début de l'import de la facture commençant sur cette ligne $i
                    $date = $this->_date($this->_cellule($sheet,"B$i"));

                    if ($date < '2014-01-01') {
                        $tva = 0.196;
                    }
                    else {
                        $tva = 0.2;
                    }
                    $reference = $this->_cellule($sheet,"A$i");
                    $numero = $this->_numero($reference,$enseigne);

                    $compatible_annee = $this->_compatible_annee($reference,$enseigne,$date);
                    $annee_devis = $this->_annee_devis($reference,$enseigne,$date);

                    $facture = array(
                        'fac_reference' => $reference,
                        'fac_date' => $date,
                        'fac_tva' => $tva,
                        'fac_etat' => 2,
                        'fac_numero' => $numero,
                        'fac_reprise' => 1
                    );

                    // création d'un devis pour servir de support à la facture
                    $devis = array(
                        'dvi_date' => $date,
                        'dvi_tva' => $tva,
                        'dvi_societe_vendeuse' => $enseigne,
                        'dvi_etat' => 4,
                        'dvi_client' => $id_contact,
                        'dvi_id_comptable' => $id_comptable,
                        'dvi_numero' => $numero,
                        'dvi_reference' => 'd_'.$reference
                    );

                    // création de la commande validée
                    $cmd = array(
                        'cmd_numero' => $numero,
                        'cmd_date' => $date,
                        'cmd_etat' => 4
                    );

                    // extaction de l'article qui est sur la ligne de début de facture
                    $article = $this->_article($i,$sheet,$tva);
                }

                if (empty($id_contact)) {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture " . $texte_nonimportee . ", ligne " . $i . "\n"
                        . "Raison : Id comptable inconnu " . $id_comptable . "\n"
                        . "Suggestion pour la résolution : Faites d'abord une reprise des clients."
                    );
                } elseif ($date == '1970-01-01') {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture ".$texte_nonimportee.", ligne ".$i."\n"
                        ."Raison : Format de date non-reconnu : ".$this->_cellule($sheet,"B$i")
                    );
                } elseif (!$numero) {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture " . $texte_nonimportee . ", ligne " . $i . "\n"
                        . "Raison : Format de numéro de facture non reconnu : " . $reference
                    );
                } elseif (!$compatible_annee) {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture " . $texte_nonimportee . ", ligne " . $i . "\n"
                        . "Raison : Année de facture ne correspond pas au numéro de facture : " . $reference."\n"
                        . "Date de facture : ".$date
                    );
                } elseif (isset($factures[$reference])) {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture ".$texte_nonimportee.", ligne ".$i."\n"
                        ."Raison : Facture déjà déclarée sur ligne ".$factures[$reference]."\n"
                        ."Numéros de factures : ".$reference." / ".$reference
                    );
                } elseif (isset($numeros[$annee_devis.$numero])) {
                    $importer = false;
                    ++$nb_factures_x;
                    $messages[] = $this->_message(
                        'warning',
                        "Facture ".$texte_nonimportee.", ligne ".$i."\n"
                        ."Raison : Facture déjà déclarée sur ligne ".$numeros[$annee_devis.$numero]['ligne']."\n"
                        ."Numéros de factures : ".$numeros[$annee_devis.$numero]['reference']." / ".$reference."\n"
                        ."Numéro extrait : ".$numero
                    );
                } else {
                    $importer = true;
                    $factures[$reference] = $i;
                    $numeros[$annee_devis.$numero] = array(
                        'ligne'=>$i,
                        'reference'=>$reference,
                    );
                }
            }
            else {
                // Pas de nouvelle facture détectée, donc normalement on doit
                // avoir des articles pour la facture précédente

                // Pas de facture précédente = fichier corrompu ?
                if (!isset($facture)) {
                    $messages[] = $this->_message('warning', "Pas de facture associée en ligne $i");
                    $importer = false;
                    continue;
                }

                // Est-ce la suite du texte pour l'article trouvé sur la ligne
                // au-dessus ou un nouvel article ?

                // Si on a un prix dans la colonne "E", alors c'est un nouvel article
                if ($this->_cellule($sheet, "E$i") != '') {

                    // On sauvegarde l'article précédent pour la facture courante
                    $articles[] = $article;

                    // On crée le nouvel article pour la même facture
                    $article = $this->_article($i,$sheet,$facture['fac_tva']);
                }
                else {
                    // On n'a pas de prix dans la colonne "E", donc c'est
                    // la suite de la description de l'article courant
                    $article['lif_description'] .= ' '.$this->_cellule($sheet, "G$i");
                }
            }
        }

        // sauvegarde de la dernière ligne de facture, c'est à dire finaliser le dernier
        // article de la dernière facture
        if (isset($article) && $importer) {
            $articles[] = $article;
            $fac_reference = $facture['fac_reference'];
            $facture_existante = $this->_facture_existante($fac_reference, $enseigne);
            if (!$facture_existante) {
                if (!$test) {
                    $fac_references[$fac_reference] = $this->_finaliser_import_facture($devis, $cmd, $facture, $articles);
                } else {
                    $fac_references[$fac_reference] = $fac_id_fictif++;
                }
                ++$nb_factures;
            } else {
                $reconciliees[$fac_reference] = $facture_existante;
                ++$nb_factures_existantes;
            }
            $derniere_ligne = $i;
        }

        $messages[] = $this->_message(
            'success',
            "Dernière ligne analysée : ".$i."\n"
                ."Dernière ligne valide : ".$derniere_ligne."\n"
                ."Factures ".$texte_importees." : ".$nb_factures."\n"
                ."Factures existantes : ".$nb_factures_existantes."\n"
        );

        if ($nb_factures_x) {
            $messages[] = $this->_message(
                'danger',
                "Factures ".$texte_nonimportees." : ".$nb_factures_x
            );
        }

        $audit = '';
        if ($nb_factures > 0) {
            $audit .= PHP_EOL
                .PHP_EOL
                .count($factures)." factures ".$texte_ajoutees." à la base de données:".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tRéférence\tCRM".PHP_EOL;
            foreach ($fac_references as $fac_reference => $fac_id) {
                $audit .= $factures[$fac_reference]."\tCréé\t".$fac_reference."\t".$fac_id.PHP_EOL;
            }
        }
        if (!empty($reconciliees)) {
            $audit .= PHP_EOL
                .PHP_EOL
                .count($reconciliees)." factures reconciliées avec la base de données:".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tRéférence\tCRM".PHP_EOL;
            foreach ($reconciliees as $fac_reference => $fac_id) {
                $audit .= $factures[$fac_reference]."\tTrouvé\t".$fac_reference."\t".$fac_id.PHP_EOL;
            }
        }

        /*
        if ($nb_factures_x > 0) {
            $audit .= PHP_EOL
                .PHP_EOL
                .$nb_factures_x." factures invalides à traiter manuellement :".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tRéférence\tCRM".PHP_EOL;
            foreach ($doublons as $ligne => $contact) {
                $audit .= $contact['ligne']."\tDoublon\t".$contact['ctc_id_comptable']."\tMultiples\t".$contact['ctc_nom'].PHP_EOL;
            }
            foreach ($conflits as $ligne => $contact) {
                $audit .= $contact['ligne']."\tDoublon\t".$contact['ctc_id_comptable']."\t".$id_comptables[$contact['ctc_id_comptable']]."\t".$contact['ctc_nom'].PHP_EOL;
            }
        }
        */

        if ($audit) {
            $audit = "Fichier traité : \"".$fichier.'"'.PHP_EOL
                ."Date : ".date('Y-m-d H:i:s').PHP_EOL
                .$audit;

            $resultat = file_put_contents($this->repertoire_reprise.'/audit/'.$fichier_audit, $audit);
            if (!$resultat) {
                $messages[] = $this->_message(
                    'danger',
                    "Le fichier d'audit n'a pu être écrit sur disque."
                );
            } else {
                $messages[] = $this->_message(
                    'info',
                    "Un fichier d'audit a été créé : \"".$fichier_audit.'"'
                );
            }
        }

        $PHPExcel->disconnectWorksheets();

        return $messages;
    }

    /**
     * Enregistre le devis dans la base de données et
     * obtient son numéro (dvi_id)
     *
     * @param $devis array
     * @return array
     */
    private function _enregistrer_devis($devis) {
        $existant = $this->db
            ->select('dvi_id')
            ->where('dvi_reference', $devis['dvi_reference'])
            ->where('dvi_societe_vendeuse', $devis['dvi_societe_vendeuse'])
            ->get('t_devis')
            ->row();

        if ($existant) {
            $id_devis = $existant->dvi_id;
            $this->db->where('dvi_id', $id_devis)->update('t_devis', $devis);
        } else {
            $this->db->insert('t_devis', $devis);
            $id_devis = $this->db->insert_id();
        }
        $devis['dvi_id'] = $id_devis;

        return $devis;
    }

    /**
     * Enregistre la commande dans la base de données et
     * obtient son numéro (cmd_id)
     *
     * @param $cmd array
     * @return array
     */
    private function _enregistrer_commande($cmd) {
        $existant = $this->db
            ->select('cmd_id')
            ->where('cmd_devis', $cmd['cmd_devis'])
            ->get('t_commandes')
            ->row();

        if ($existant) {
            $id_cmd = $existant->cmd_id;
            $this->db->where('cmd_id', $id_cmd)->update('t_commandes', $cmd);
        } else {
            $this->db->insert('t_commandes', $cmd);
            $id_cmd = $this->db->insert_id();
        }
        $cmd['cmd_id'] = $id_cmd;

        return $cmd;
    }

    /**
     * Enregistre la facture dans la base de données et
     * obtient son numéro (fac_id)
     *
     * @param $facture array
     * @return array
     */
    private function _enregistrer_facture($facture) {
        $existant = $this->db
            ->select('fac_id')
            ->where('fac_commande', $facture['fac_commande'])
            ->get('t_factures')
            ->row();

        if ($existant) {
            $id_facture = $existant->fac_id;
            $this->db->where('fac_id', $id_facture)->update('t_factures', $facture);
        } else {
            $this->db->insert('t_factures', $facture);
            $id_facture = $this->db->insert_id();
        }
        $facture['fac_id'] = $id_facture;

        return $facture;
    }

    /**
     * Enregistre la ligne de facture dans la base de données et
     * obtient son numéro (lif_id)
     *
     * @param $article array
     * @return array
     */
    private function _enregistrer_ligne_facture($article) {
        /*$existant = $this->db
            ->select('lif_id')
            ->where('lif_facture', $article['lif_facture'])
            ->get('t_lignes_factures')
            ->row();*/
        $existant = false;

        if ($existant) {
            $id_ligne_facture = $existant->lif_id;
            $this->db->where('lif_id', $id_ligne_facture)->update('t_lignes_factures', $article);
        } else {
            $this->db->insert('t_lignes_factures', $article);
            $id_ligne_facture = $this->db->insert_id();
        }
        $article['lif_id'] = $id_ligne_facture;

        return $article;
    }

    /**
     * Calcul des montants HT et TTC
     *
     * @param $articles array
     * @param $tva float
     *
     * @return array
     */
    private function _calcul_montants($articles, $tva) {
        $ht = 0;
        $remise = 0;
        foreach($articles as $article) {
            if ($article['lif_code'] == 'R') {
                $remise +=  $article['lif_prix'];
            }
            else {
                $ht += $article['lif_prix'] * $article['lif_quantite'] - $article['lif_remise_ht'];
            }
        }
        $montant_htnr = $ht;
        $ht = $ht * (1 - $remise);
        $tva = $ht * $tva;
        $ttc = $ht + $tva;
        $montant_ht = $ht;
        $montant_tva = $tva;
        $montant_ttc = $ttc;

        return array(
            'htnr' => $montant_htnr,
            'ht'   => $montant_ht,
            'tva'  => $montant_tva,
            'ttc'  => $montant_ttc,
        );
    }

    /**
     * Sauvegarde les informations dans la base de données
     *
     * @param $devis array
     * @param $cmd array
     * @param $facture array
     * @param $articles array
     *
     * @return integer L'id de la facture dans le CRM
     */
    private function _finaliser_import_facture($devis, $cmd, $facture, $articles) {

        $montant = $this->_calcul_montants($articles, $facture['fac_tva']);

        $devis['dvi_montant_htnr'] = $montant['htnr'];
        $devis['dvi_montant_ht']   = $montant['ht'];
        $devis['dvi_montant_ttc']  = $montant['ttc'];

        $facture['fac_montant_htnr'] = $montant['htnr'];
        $facture['fac_montant_ht']   = $montant['ht'];
        $facture['fac_montant_tva']  = $montant['tva'];
        $facture['fac_montant_ttc']  = $montant['ttc'];
        $facture['fac_regle']        = 0;
        $facture['fac_reste']        = $montant['ttc'];

        $this->db->trans_start();

        $devis = $this->_enregistrer_devis($devis);

        $cmd['cmd_devis'] = $devis['dvi_id'];

        $cmd = $this->_enregistrer_commande($cmd);

        $facture['fac_commande'] = $cmd['cmd_id'];

        $facture = $this->_enregistrer_facture($facture);

        foreach ($articles as $_i => $article) {
            $article['lif_facture'] = $facture['fac_id'];
            $articles[$_i] = $this->_enregistrer_ligne_facture($article);
        }

        $this->db->trans_complete();

        return $facture['fac_id'];
    }

    private function _article($i,$sheet,$tva) {
        $article = array(
            'lif_code' => 'G',
            'lif_prix' => $this->_valeur($this->_cellule($sheet, "E$i")),
            'lif_quantite' => $this->_cellule($sheet, "F$i"),
            'lif_description' => $this->_cellule($sheet, "G$i"),
        );
        if ($this->_cellule($sheet, "D$i") > 0) {
            $article['lif_remise_taux'] = $this->_valeur($this->_cellule($sheet, "D$i")) * 100;
        }
        else {
            $article['lif_remise_taux'] = 0;
        }
        $article['lif_remise_ht'] = $article['lif_remise_taux'] / 100 * $article['lif_prix'] * $article['lif_quantite'];
        $article['lif_remise_ttc'] =   $article['lif_remise_ht'] * $tva;
        return $article;
    }

    private function _valeur($valeur) {
        $valeur = str_replace(',','.',$valeur);
        return $valeur;
    }

    private function _annee_devis($reference,$enseigne,$date) {
        $annee = intval(substr($date,0,4),10);
        $annee_prec = $annee - 1;
        $annee_suiv = $annee + 1;
        switch($enseigne) {
            case 4:
                // Le Colporteur
                // Ça dépend de l'année de la facture
                if (preg_match('/^(?:FAC|FA|fa)('.$annee.'|'.$annee_prec.'|'.$annee_suiv.')\d+/',$reference,$matches)) {
                    return intval($matches[1],10);
                }
                break;
            case 5:
                // Publimail
                return $annee;
            case 6:
                // BAL-IDF
                return $annee;
        }
        return $annee;
    }

    private function _compatible_annee($reference,$enseigne,$date) {
        $annee = intval(substr($date,0,4),10);
        $annee_prec = $annee - 1;
        $annee_suiv = $annee + 1;
        switch($enseigne) {
            case 4:
                // Le Colporteur
                $regexp = '/^(FAC|FA|fa)('.$annee.'|'.$annee_prec.'|'.$annee_suiv.')\d+/';
                break;
            case 5:
                // Publimail
                return true;
            case 6:
                // BAL-IDF
                return true;
        }
        return (bool)preg_match($regexp,trim($reference));
    }

    private function _numero($reference,$enseigne) {
        switch($enseigne) {
            case 4:
                // Le Colporteur
                // Années 2000 à 2017
                $regexp = '/^(?:FAC|FA|fa)(?:200\d|201[0-7])(\d{2,3})(?: OK| ok| OL|OK2|OKO|OK)?$/';
                break;
            case 5:
                // Publimail
                $regexp = '/^FA(\d{4})$/';
                break;
            case 6:
                // BAL-IDF
                $regexp = '/^(?:FBAL|FA|FABL|FBL|FBA|FAL|fbal)(\d{4})$/';
                break;
            default:
                return null;
        }
        if (!preg_match($regexp, trim($reference), $numero)) {
            return null;
        }
        return intval($numero[1], 10);
    }

    private function _date($date) {
        // La date est-elle du texte ou une valeur date ?
        if (preg_match('/^(0\d|[12]\d|30|31)\.(0\d|1[0-2])\.?(200\d|201[0-7])$/', $date, $matches)) {
            $dateExcel = $matches[3].'-'.$matches[2].'-'.$matches[1];
        } else {
            $dateExcel = $date-1;
            $dateExcel = date("Y-m-d", mktime(0,0,0,1, $dateExcel, 1900));
        }
        return $dateExcel;
    }

    private function _cellule($sheet,$adresse) {
        $valeur = $sheet->getCell($adresse)->getValue();
        if (! isset($valeur)) return '';
        return $valeur;
    }
}
// EOF