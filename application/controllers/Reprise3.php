<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 10/03/17
 * Time: 17:09
 */
require 'application/third_party/PHPExcel/IOFactory.php';

/**
 * Reprise des clients
 *
 * @property M_societes_vendeuses $m_societes_vendeuses
 * @property CI_DB_query_builder $db
 */
class Reprise3 extends CI_Controller {
    private $cache = 'mem';
    private $enseignes;
    private $fichiers;
    private $repertoire_reprise = FCPATH.'fichiers/reprise/';

    private $columns = array(
        'A' => 'Code',
        'B' => 'Nom',
        'C' => 'Nom du contact',
        'D' => 'Fax',
        'E' => 'Téléphone',
        'F' => 'Portable',
        'G' => 'Adresse 1 Livraison',
        'H' => 'Code Postal Livraison',
        'I' => 'Ville Livraison',
        'J' => 'E-mail',
        'K' => 'URL',
        'L' => 'Adresse 1',
        'M' => 'Adresse 2',
        'N' => 'Adresse 3',
        'O' => 'Code Postal',
        'P' => 'Ville',
        'Q' => 'Pays',
        'R' => 'Mode de paiement',
        'S' => 'Risque',
        'T' => 'Représentant',
        'U' => 'Nom du contact',
        'V' => 'Téléphone  du contact',
        'W' => 'Portable du contact',
        'X' => 'Fax du contact',
        'Y' => 'E-mail du contact',
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
        $this->load->view('reprise3', $data);
    }

    private function _enseignes() {
        $enseignes = $this->m_societes_vendeuses->liste(null);
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
     * Importation des clients
     ******************************/
    public function clients() {
        $enseigne = $this->input->post('enseigne');
        $fichier = $this->input->post('fichier');
        $action = $this->input->post('action');

        // reprise des factures
        if ($this->_enseigne($enseigne) !== null) {
            $test = ($action != 'effectuer');
            $messages = $this->_reprise_clients($enseigne, $fichier, $test);
        }
        else {
            $messages = array($this->_message('danger',"Veuillez sélectionner un fichier et une enseigne"));
        }
        $data = array(
            'messages' => $messages,
            'enseignes' => $this->enseignes['data'],
            'fichiers' => $this->fichiers,
            'enseigne' => $enseigne,
            'fichier' => $fichier,
            'colonnes' => $this->columns,
        );
        $this->load->view('reprise3',$data);
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
     * @param string $l1
     * @param string $l2
     * @param string $l3
     *
     * @return string
     */
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

    /**
     * @param string $code
     * @return int
     */
    private function _commercial($code) {
        switch($code) {
            case 'REP0001':
                return 1;
            case 'REP0002':
                return 2;
            case 'REP0005':
                return 5;
        }
        return $code;
    }

    /******************************
     * Importation des clients
     *
     * @return array Liste de messages
     ******************************/
    private function _reprise_clients($enseigne,$fichier,$test=true) {
        $messages = array();
        if ($test) {
            $messages[] = $this->_message('danger', "<strong>Simulation de la reprise !</strong>", 'html');
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

        // Id comptables existants
        $id_comptables = array();
        $q = $this->db->select('idc_id_comptable, idc_contact')
            ->where('idc_societe_vendeuse', $enseigne)
            ->get('t_id_comptable');
        foreach ($q->result() as $row) {
            $id_comptables[(string)$row->idc_id_comptable] = (int)$row->idc_contact;
        }

        // parcours du fichier
        $nb_reconciliations = 0;
        $nb_creations = 0;
        //$nb_devis = 0;

        $crees = array();
        $reconcilies = array();
        $doublons = array();
        $conflits = array();
        $problemes = array();
        $id_comptables_crees = array();

        for ($i = 2; $i <= $highestRow; $i++) {
            $id_comptable = (string)$this->_cellule($sheet,"A$i");

            //$client = $this->_cellule($sheet,"B$i");

            $ctc_nom = $this->_cellule($sheet,"B$i");

            $contact = array(
                'ctc_nom' => $ctc_nom,
                'ctc_id_comptable' => $id_comptable,
                'ctc_origine' => date('Y-m-d').' ('.$fichier.')',
                'ctc_date_creation' => date('Y-m-d H:i:s'),
                'ctc_fax' => $this->_telephone($this->_cellule($sheet,"D$i")),
                'ctc_telephone' => $this->_telephone($this->_cellule($sheet,"E$i")),
                'ctc_mobile' => $this->_telephone($this->_cellule($sheet,"F$i")),
                'ctc_livr_adresse' => $this->_cellule($sheet,"G$i"),
                'ctc_livr_cp' => $this->_cellule($sheet,"H$i"),
                'ctc_livr_ville' => $this->_cellule($sheet,"I$i"),
                'ctc_email' => $this->_cellule($sheet,"J$i"),
                'ctc_adresse' => $this->_adresse($this->_cellule($sheet,"L$i"), $this->_cellule($sheet,"M$i"), $this->_cellule($sheet,"N$i")),
                'ctc_cp' => $this->_cellule($sheet,"O$i"),
                'ctc_ville' => $this->_cellule($sheet,"P$i"),
                'ctc_site' => $this->_cellule($sheet,"K$i"),
                'ctc_commercial' => $this->_commercial($this->_cellule($sheet,"T$i")),
                'ctc_client_prospect' => 2,
            );

            // Recherche d'un contact existant
            $id_client = $this->_contact($contact, $cree, $id_comptables, $test);
            $contact['ligne'] = $i;
            if ($id_client == -1) {
                $messages[] = $this->_message(
                    'warning',
                    "Plusieurs clients existants possibles pour l'id comptable " . $id_comptable . ", ligne " . $i . "\r\n"
                    . $ctc_nom
                );
                $doublons[] = array(
                    'ligne' => $i,
                    'ctc_id_comptable' => $id_comptable,
                    'ctc_nom' => $ctc_nom,
                );
                continue;
            } elseif ($id_client == -2) {
                $id_contact = $id_comptables[$id_comptable];
                $contact_existant = $this->db->where('ctc_id', $id_contact)
                    ->get('t_contacts')
                    ->row();

                if (empty($contact_existant) && $test) {
                    $messages[] = $this->_message(
                        'warning',
                        "Un autre client \"".$crees[$id_contact]['ctc_nom']."\" sera aussi créé avant pour l'id comptable ".$id_comptable.", ligne ".$i."\r\n"
                        .$ctc_nom
                    );
                } else {
                    $messages[] = $this->_message(
                        'warning',
                        "Un autre client \"".$contact_existant->ctc_nom."\" (id CRM ".$id_contact.") existe déjà pour l'id comptable ".$id_comptable.", ligne ".$i."\r\n"
                        .$ctc_nom
                    );
                }
                $doublons[] = array(
                    'ligne' => $i,
                    'ctc_id_comptable' => $id_comptable,
                    'ctc_nom' => $ctc_nom,
                );
                continue;
            } elseif ($id_client === false) {
                $messages[] = $this->_message(
                    'danger',
                    "Impossible de créer contact entrée pour ".$id_comptable.", ligne ".$i
                );
                $problemes[$i] = $contact;
                continue;
            }
            if ($cree) {
                ++$nb_creations;
                $crees[$id_client] = array(
                    'ligne' => $i,
                    'ctc_id_comptable' => $id_comptable,
                    'ctc_nom' => $ctc_nom,
                );
            } else {
                ++$nb_reconciliations;
                $reconcilies[$id_client] = array(
                    'ligne' => $i,
                    'ctc_id_comptable' => $id_comptable,
                    'ctc_nom' => $ctc_nom,
                );
            }

            // Insertion de l'id comptable qui n'existe pas encore dans la base de données.
            if (!isset($id_comptables[$id_comptable])) {
                $data = array(
                    'idc_id_comptable' => $id_comptable,
                    'idc_societe_vendeuse' => $enseigne,
                    'idc_contact' => $id_client,
                );
                if (!$test) {
                    //$sql_insert = $this->db->insert_string('t_id_comptable',$data);
                    //$sql_insert_ignore = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sql_insert);
                    //$this->db->query($sql_insert_ignore);
                    $this->db->insert('t_id_comptable',$data);
                }
                $id_comptables[$id_comptable] = $id_client;
                $id_comptables_crees[$id_client] = $contact;
            }
        }

        $crees_texte = ($test) ? "à créer" : "créés";
        $messages[] = $this->_message(
            'success',
            "Dernière ligne analysée : ".$i."\n"
            ."Clients réconciliés : ".$nb_reconciliations."\n"
            ."Clients $crees_texte : ".$nb_creations."\n"
        );

        if (count($doublons) + count($conflits) > 0) {
            $doublons_texte = ($test) ? "non-importables" : "non-importés";
            $messages[] = $this->_message(
                'danger',
                "Clients $doublons_texte : ".(count($doublons) + count($conflits))."\n"
            );
        }

        $audit = '';
        if (!empty($crees)) {
            $ajoutes = ($test) ? "seront ajoutés" : "ajoutés";
            $etat = ($test) ? "Sera créé" : "Créé";

            $audit .= PHP_EOL
                .PHP_EOL
                .count($crees)." clients $ajoutes à la base de données:".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tCompta\tCRM\tNom".PHP_EOL;
            foreach ($crees as $id_client => $contact) {
                $audit .= $contact['ligne']."\t$etat\t".$contact['ctc_id_comptable']."\t".$id_client."\t".$contact['ctc_nom'].PHP_EOL;
            }
        }
        if (!empty($reconcilies)) {
            $audit .= PHP_EOL
                .PHP_EOL
                .count($reconcilies)." clients reconciliés avec la base de données:".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tCompta\tCRM\tNom".PHP_EOL;
            foreach ($reconcilies as $id_client => $contact) {
                $audit .= $contact['ligne']."\tTrouvé\t".$contact['ctc_id_comptable']."\t".$id_client."\t".$contact['ctc_nom'].PHP_EOL;
            }
        }

        if (!empty($doublons) || !empty($conflits)) {
            $audit .= PHP_EOL
                .PHP_EOL
                .(count($doublons) + count($conflits))." doublons à traiter manuellement :".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tCompta\tCRM\tNom".PHP_EOL;
            foreach ($doublons as $ligne => $contact) {
                $audit .= $contact['ligne']."\tDoublon\t".$contact['ctc_id_comptable']."\tMultiples\t".$contact['ctc_nom'].PHP_EOL;
            }
            foreach ($conflits as $ligne => $contact) {
                $audit .= $contact['ligne']."\tDoublon\t".$contact['ctc_id_comptable']."\t".$id_comptables[$contact['ctc_id_comptable']]."\t".$contact['ctc_nom'].PHP_EOL;
            }
        }

        if (!empty($id_comptables_crees)) {
            $ajoutes = ($test) ? "seront ajoutés" : "ajoutés";
            $etat = ($test) ? "Sera lié" : "Lié";

            $audit .= PHP_EOL
                .PHP_EOL
                .count($id_comptables_crees)." id comptables $ajoutes à la base de données :".PHP_EOL
                .PHP_EOL
                ."Ligne\tÉtat\tCompta\tCRM\tNom".PHP_EOL;
            foreach ($id_comptables_crees as $id_client => $contact) {
                $audit .= $contact['ligne']."\t$etat\t".$contact['ctc_id_comptable']."\t".$id_client."\t".$contact['ctc_nom'].PHP_EOL;
            }
        }

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
     * @param array   $data
     * @param boolean $cree Output variable
     * @param array   $id_comptables
     * @param bool    $test
     *
     * @return int|boolean Valeur possible :
     * <ul>
     *  <li>L'id d'un contact si on trouve un contact,</li>
     *  <li>-1 si plusieurs contacts correspondent,</li>
     *  <li>-2 si un autre contact utilise le même id comptable,</li>
     *  <li>FALSE en cas d'erreur.</li>
     * </ul>
     * $cree est mis à TRUE si un contact a été créé.
     */
    protected function _contact($data, &$cree, $id_comptables = array(), $test = true)
    {
        // Pour le mode test, on a besoin d'un faux numéro de client
        static $fake_ctc_id = 1000000;

        $cree = false;
        $id_comptable = $data['ctc_id_comptable'];
        unset($data['ctc_id_comptable']);

        /*
         | Préparation d'une RegExp sur le nom pour être flexible sur le nombre d'espaces
         | autour des caractères non-alphabétiques et entre les mots.
         */
        // Espaces multiples -> un espace dans le nom du client
        $nom_1espace = preg_replace('/\s+/', ' ', trim($data['ctc_nom']));
        // Caractères non-alphabétiques à considérer
        $char_non_alpha = str_replace('/', '\/', preg_quote('.,+;:<>=()\'@#$&/!~*%_-'));
        // On enlève tous les espaces autour des caractères non-alphabétiques
        $nom_non_alpha = preg_replace('/\s*(['.$char_non_alpha.']+)\s*/', '\1', $nom_1espace);
        // On spécifie que les espaces sont possibles autour des caractéres non-alphabétiques et en début de nom
        $nom_regexp_non_alpha = '^\s*'.preg_replace('/(['.$char_non_alpha.'])/', '\s*\1\s*', $nom_non_alpha).'$';
        // On spécifie que les espaces peuvent être multiples
        $nom_regexp = str_replace(' ', '\s+', $nom_regexp_non_alpha);
        // On convertit les \s en espace pour la compatibilité avec MySQL
        $nom_regexp_mysql = str_replace('\s', ' ', $nom_regexp);

        $query = $this->db->select('ctc_id, ctc_email, ctc_telephone, ctc_mobile, ctc_fax, ctc_livr_adresse')
            ->group_start()
                ->where('ctc_nom', $data['ctc_nom'])
                ->or_where('ctc_nom REGEXP "'.$nom_regexp_mysql. '"')
            ->group_end()
            ->where('ctc_inactif IS NULL')
            ->get('t_contacts');
        $candidats = $query->result();

        // A-t'on une entrée existante unique ?
        if (count($candidats) == 1) {
            // On vérifie que l'id comptable est le même ou qu'il n'existe pas
            // encore dans la base de données.
            // Si ce n'est pas le cas, il faudra créer un nouveau contact plus bas.
            $candidat = array_pop($candidats);

            $ctc_id_comptable = array_search($candidat->ctc_id, $id_comptables);
            if ($ctc_id_comptable == $id_comptable) {
                return $candidat->ctc_id;
            }
            elseif ($ctc_id_comptable === false
                    && (!isset($id_comptables[$id_comptable]) || intval($candidat->ctc_id, 10) == $id_comptables[$id_comptable])) {
                return $candidat->ctc_id;
            }
        }

        // Si, on n'a pas trouvé de contact par nom dans la base de données,
        // mais que l'id comptable existe pourtant bien, cela veut dire
        // qu'un client de nom différent existe pour de même id compta
        if (count($candidats) == 0 && isset($id_comptables[$id_comptable])) {
            return -2;
        }

        if (count($candidats) == 0) {
            // Si on a rien du tout ou l'id comptable ne correspondait pas au-dessus.
            // Dans ce cas là on crée une entrée contact dans la base de données.
            if ($test) {
                $cree = true;
                return ++$fake_ctc_id;
            }
            $this->db->insert('t_contacts', $data);
            if ($this->db->affected_rows() == 1) {
                $cree = true;
                return $this->db->insert_id();
            }
            return false;
        }

        // Si l'id compta est déjà dans la base de données, alors
        // on aurait dû trouver le contact, sinon ça veut dire
        // qu'un autre contact l'utilise sous un autre nom.
        if (isset($id_comptables[$id_comptable])) {
            foreach ($candidats as $candidat) {
                if ($id_comptables[$id_comptable] == $candidat->ctc_id) {
                    return $candidat->ctc_id;
                }
            }
            return -2;
        }

        // Maintenant on enlève les candidats pour lesquelles on a un id
        // comptable, ce dernier qui est bien sûr différent de celui recherché
        // sinon on l'aurait déjà récupéré ci-dessus.
        foreach ($candidats as $i => $candidat) {
            if (in_array($candidat->ctc_id, $id_comptables)) {
                unset($candidats[$i]);
            }
        }

        if (count($candidats) == 1) {
            $candidat = array_pop($candidats);
            return $candidat->ctc_id;

        } elseif (count($candidats) == 0) {
            // S'il ne nous reste plus de candidat, on crée une entrée contact
            // dans la base de données.
            if ($test) {
                $cree = true;
                return ++$fake_ctc_id;
            }
            $this->db->insert('t_contacts', $data);
            if ($this->db->affected_rows() == 1) {
                $cree = true;
                return $this->db->insert_id();
            }
            return false;
        }

        // Si on a plusieurs entrées, on regarde si on a une seule égalité
        // avec les adresses emails.
        if (strlen($data['ctc_email']) > 0) {
            $egale = array();
            foreach ($candidats as $candidat) {
                if (strcasecmp($candidat->ctc_email, $data['ctc_email']) == 0) {
                    $egale[] = $candidat->ctc_id;
                }
            }
            if (count($egale) == 1) {
                return $egale[0];
            }
        }

        // Si on a toujours plusieurs entrées, on regarde si on a une seule égalité
        // avec les numéros de téléphones
        if (strlen($data['ctc_telephone']) > 0) {
            $egale = array();
            $ctc_telephone = preg_replace('/[^\d]+/', '', $data['ctc_telephone']);
            foreach ($candidats as $candidat) {
                if (preg_replace('/[^\d]+/','', $candidat->ctc_telephone) == $ctc_telephone) {
                    $egale[] = $candidat->ctc_id;
                }
            }
            if (count($egale) == 1) {
                return $egale[0];
            }
        }

        // Si on a toujours plusieurs entrées, on regarde si on a une seule égalité
        // avec les numéros de téléphones mobiles
        if (strlen($data['ctc_mobile']) > 0) {
            $egale = array();
            $ctc_mobile = preg_replace('/[^\d]+/', '', $data['ctc_mobile']);
            foreach ($candidats as $candidat) {
                if (preg_replace('/[^\d]+/','', $candidat->ctc_mobile) == $ctc_mobile) {
                    $egale[] = $candidat->ctc_id;
                }
            }
            if (count($egale) == 1) {
                return $egale[0];
            }
        }

        // Si on a toujours plusieurs entrées, on regarde si on a une seule égalité
        // avec les numéros de fax
        if (strlen($data['ctc_fax']) > 0) {
            $egale = array();
            $ctc_fax = preg_replace('/[^\d]+/', '', $data['ctc_fax']);
            foreach ($candidats as $candidat) {
                if (preg_replace('/[^\d]+/','', $candidat->ctc_fax) == $ctc_fax) {
                    $egale[] = $candidat->ctc_id;
                }
            }
            if (count($egale) == 1) {
                return $egale[0];
            }
        }

        // Si on a toujours plusieurs entrées, on regarde si on a une seule égalité
        // avec l'adresse de livraison
        if (strlen($data['ctc_livr_adresse']) > 0) {
            $egale = array();
            foreach ($candidats as $candidat) {
                if (strcasecmp($candidat->ctc_livr_adresse, $data['ctc_livr_adresse']) == 0) {
                    $egale[] = $candidat->ctc_id;
                }
            }
            if (count($egale) == 1) {
                return $egale[0];
            }
        }

        // On abandonne
        return -1;
    }

    private function _email($texte) {
        return strtolower(preg_replace('/^\s*([a-z0-9_.-]+@[a-z0-9.-]+).*$/i', '\1', $texte));
    }

    private function _telephone($numero) {
        $numero = preg_replace('/[^0-9]+/','',$numero);
        if (strlen($numero) > 8){
            return str_pad($numero, 10, '0', STR_PAD_LEFT);
        }
        return $numero;
    }

    private function _cellule($sheet,$adresse) {
        $valeur = $sheet->getCell($adresse)->getValue();
        if (! isset($valeur)) return '';
        return $valeur;
    }
}
// EOF