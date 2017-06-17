<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This migration changes how "id comptables" are managed in the application.
 * It takes care of converting data all the while correcting them because
 * the previous system abused the "id comptables".
 *
 * @property CI_DB_Forge $dbforge
 */
class Migration_Add_2columns_ids_to_table_t_id_comptable extends CI_Migration {

    /**
     * Migration upgrade method
     *
     * @return void
     */
    public function up() {

        // Data currently in database is inaccurate
        $sql = 'DELETE FROM `t_id_comptable`';
        $this->db->query($sql);

        // We add the 2 columns to properly relate "id comptables" and
        // "societe vendeuse"
        $fields = array(
            'idc_societe_vendeuse' => array(
                'type' => 'INT',
                'constraint' => 9,
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
            'idc_id_comptable' => array(
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => FALSE,
            ),
        );

        $this->dbforge->add_column('t_id_comptable', $fields);

        // Only one "id comptable" per contact per "societe vendeuse"
        $sql = '
ALTER TABLE `t_id_comptable`
ADD UNIQUE `idc_unique_societe_vendeuse_contact`
(`idc_societe_vendeuse`, `idc_contact`)
';
        $this->db->query($sql);

        // Unique "id comptable" within "societe vendeuse"
        $sql = '
ALTER TABLE `t_id_comptable`
ADD UNIQUE `idc_unique_societe_vendeuse_id_comptable`
(`idc_societe_vendeuse`, `idc_id_comptable`)
';
        $this->db->query($sql);

        // Create a trigger to number the "id comptable" per
        // "societe vendeuse"
        $sql = "
CREATE TRIGGER `numeroteur_id_comptable` BEFORE INSERT ON `t_id_comptable` FOR EACH ROW BEGIN
    IF (NEW.idc_id_comptable IS NULL OR NEW.idc_id_comptable = \"0\" OR TRIM(NEW.idc_id_comptable) = \"\" ) THEN
        SELECT scv_no_id_comptable, scv_format_id_comptable
        INTO @numero, @format
        FROM t_societes_vendeuses
        WHERE scv_id = NEW.idc_societe_vendeuse;
        
        UPDATE t_societes_vendeuses
        SET scv_no_id_comptable = @numero + 1
        WHERE scv_id = NEW.idc_societe_vendeuse;
        
        SET NEW.idc_id_comptable = REPLACE(REPLACE(@format,'%',@numero + 1),'#',DATE_FORMAT(NOW(),'%Y'));
    END IF;
END
";
        $this->db->query($sql);

        // Insert the records for existing "id comptables" from devis,
        // which have associated "commande" and "facture" records
        // however, we exclude contacts that have 2 or more different
        // "id comptables" for the same "societe vendeuse" by way of most
        // likely corrupted "devis" records.
        // 1 contact = 1 "id comptable" per "societe vendeuse"
        $sql = '
INSERT IGNORE INTO `t_id_comptable` (`idc_id_comptable`, `idc_societe_vendeuse`, `idc_contact`)
SELECT DISTINCT CAST(`dvi_id_comptable` AS CHAR(30)), `dvi_societe_vendeuse`, `dvi_client` 
FROM `t_devis`
  INNER JOIN `t_commandes` ON (`cmd_devis` = `dvi_id`)
  INNER JOIN `t_factures` ON (`fac_commande` = `cmd_id`)
  INNER JOIN `t_contacts` ON (`dvi_client` = `ctc_id`)
  INNER JOIN `t_societes_vendeuses` ON (`dvi_societe_vendeuse` = `scv_id`)
WHERE `dvi_societe_vendeuse` <> 0
  AND `dvi_id_comptable` <> 0
  AND `dvi_client` <> 0
  AND CONCAT(`dvi_client`,"-",`dvi_societe_vendeuse`) NOT IN
          (SELECT DISTINCT CONCAT(`dvi_client`,"-",`dvi_societe_vendeuse`) 
           FROM `t_devis`
           WHERE `dvi_client` <> 0
           GROUP BY `dvi_client`, `dvi_societe_vendeuse`
           HAVING COUNT(DISTINCT `dvi_id_comptable`) > 1)
';
        $this->db->query($sql);

        // We handle differently the problematic entries that we excluded above.
        // For "Le Colporteur" (id 4) and "Publimail" (id 5), we use the most
        // recent "id comptable" as the official value.
        $sql = '
INSERT IGNORE INTO `t_id_comptable` (`idc_contact`, `idc_id_comptable`, `idc_societe_vendeuse`)
SELECT DISTINCT `dvi_client`, CAST(MAX(`dvi_id_comptable`) AS CHAR(30)), `dvi_societe_vendeuse`
FROM `t_devis`
  INNER JOIN `t_commandes` ON (`cmd_devis` = `dvi_id`)
  INNER JOIN `t_factures` ON (`fac_commande` = `cmd_id`)
  INNER JOIN `t_contacts` ON (`dvi_client` = `ctc_id`)
  INNER JOIN `t_societes_vendeuses` ON (`dvi_societe_vendeuse` = `scv_id`)
  LEFT OUTER JOIN `t_id_comptable` ON (`dvi_client` = `idc_contact`
                                       AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
WHERE `dvi_client` <> 0
  AND `dvi_id_comptable` <> 0
  AND `dvi_societe_vendeuse` IN (4, 5)
  AND `idc_contact` IS NULL
GROUP BY `dvi_client`, `dvi_societe_vendeuse`
HAVING COUNT(DISTINCT `dvi_id_comptable`) > 1
';
        $this->db->query($sql);

        // For "Boitauxlettres IDF" (id 6), we duplicate clients for the cases
        // where we had several "id comptable" for the same one.
        $sql = '
SELECT DISTINCT `dvi_client`, CAST(`dvi_id_comptable` AS CHAR(30)) AS `ordered_id_comptable`, `dvi_societe_vendeuse`
FROM `t_devis`
  INNER JOIN `t_commandes` ON (`cmd_devis` = `dvi_id`)
  INNER JOIN `t_factures` ON (`fac_commande` = `cmd_id`)
  INNER JOIN `t_contacts` ON (`dvi_client` = `ctc_id`)
  INNER JOIN `t_societes_vendeuses` ON (`dvi_societe_vendeuse` = `scv_id`)
  LEFT OUTER JOIN `t_id_comptable` ON (`dvi_client` = `idc_contact`
                                       AND `dvi_societe_vendeuse` = `idc_societe_vendeuse`)
WHERE `dvi_client` <> 0
  AND `dvi_id_comptable` <> 0
  AND `dvi_societe_vendeuse` IN (6)
  AND `idc_contact` IS NULL
GROUP BY `dvi_client`, `dvi_societe_vendeuse`, `dvi_id_comptable`
ORDER BY dvi_client, COUNT(DISTINCT `dvi_id`) DESC
';
        $q = $this->db->query($sql);
        $copy_contacts = array();
        foreach ($q->result() as $row) {
            if (empty($copy_contacts[$row->dvi_client])) {
                $copy_contacts[$row->dvi_client] = array($row->ordered_id_comptable);
            } else {
                $copy_contacts[$row->dvi_client][] = $row->ordered_id_comptable;
            }
        }
        foreach ($copy_contacts as $from_ctc_id => $id_comptables) {
            // Create the first id comptable with original client
            $first = array_pop($id_comptables);
            $data = array(
                'idc_id_comptable' => $first,
                'idc_contact' => $from_ctc_id,
                'idc_societe_vendeuse' => 6,        // "Boitauxlettres IDF"
            );
            $sql_insert = $this->db->insert_string('t_id_comptable', $data);
            $sql_insert_ignore = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sql_insert);
            $this->db->query($sql_insert_ignore);

            // For all the other id comptables, duplicate the client and
            // its correspondants, then update devis and avoirs.
            $contact = $this->db->where('ctc_id', $from_ctc_id)
                ->get('t_contacts')->row();
            unset($contact->ctc_id);
            unset($contact->login);

            $correspondants = $this->db->where('cor_contact', $from_ctc_id)
                ->get('t_correspondants')->result();

            foreach ($id_comptables as $id_comptable) {
                $this->db->insert('t_contacts', $contact);
                $to_ctc_id = $this->db->insert_id();

                // Copy correspondants
                foreach ($correspondants as $correspondant) {
                    $correspondant->cor_contact = $to_ctc_id;
                    unset($correspondant->cor_id);
                    $this->db->insert('t_correspondants', $correspondant);
                }

                // Record id comptable with new client
                $data = array(
                    'idc_id_comptable' => $id_comptable,
                    'idc_contact' => $to_ctc_id,
                    'idc_societe_vendeuse' => 6,        // "Boitauxlettres IDF"
                );
                $this->db->insert('t_id_comptable', $data);

                // Update avoirs which are linked to the devis
                $sql = 'UPDATE `t_avoirs`
                        INNER JOIN `t_factures` ON (`avr_facture` = `fac_id`)
                        INNER JOIN `t_commandes` ON (`fac_commande` = `cmd_id`)
                        INNER JOIN `t_devis` ON (`cmd_devis` = `dvi_id`)
                        SET `avr_client` = '.$to_ctc_id.'
                        WHERE `dvi_id_comptable` = '.$id_comptable.'
                              AND `avr_societe_vendeuse` = 6
                              AND `avr_client` = '.$from_ctc_id.'
                        ';
                $this->db->query($sql);

                // Update devis
                $data = array(
                    'dvi_client' => $to_ctc_id,
                );
                $this->db->where('dvi_client', $from_ctc_id)
                    ->where('dvi_societe_vendeuse', 6)
                    ->where('dvi_id_comptable', $id_comptable)
                    ->update('t_devis', $data);
            }
        }

        // We consider that at least "Publimail" and "Boitauxlettres IDF" have
        // real "id comptables".
        // First we save the current settting
        $current_settings = $this->db->select('scv_id, scv_id_comptable')
            ->where_in('scv_id', array(5, 6))
            ->get('t_societes_vendeuses');

        // Then we force the setting off so we do not generate the
        // "id comptables" for those "societes vendeuses".
        $data = array(
            'scv_id_comptable' => 0,
        );
        $this->db->where_in('scv_id', array(5, 6))
            ->update('t_societes_vendeuses', $data);

        // Initialize the "id comptable" counters for "societes vendeuses"
        // that don't really care about them.
        $sql = '
UPDATE `t_societes_vendeuses`
SET `scv_no_id_comptable` = (SELECT MAX(CAST(`idc_id_comptable` AS UNSIGNED INTEGER))
                             FROM `t_id_comptable`
                             WHERE `idc_societe_vendeuse` = `scv_id`)
WHERE `scv_id_comptable` = 1
';
        $this->db->query($sql);

        // Create missing "id comptables" for "societes vendeuses" that
        // don't actually rely on them. Their values are not important, but
        // should be unique within a "societe vendeuse".
        // See definition of numeroteur_id_comptable trigger
        $sql = '
SELECT DISTINCT `dvi_societe_vendeuse`, `dvi_client` 
FROM `t_devis`
  INNER JOIN `t_commandes` ON (`cmd_devis` = `dvi_id`)
  INNER JOIN `t_factures` ON (`fac_commande` = `cmd_id`)
  INNER JOIN `t_contacts` ON (`dvi_client` = `ctc_id`)
  INNER JOIN `t_societes_vendeuses` ON (`dvi_societe_vendeuse` = `scv_id`)
  LEFT OUTER JOIN `t_id_comptable` ON (`dvi_societe_vendeuse` = `idc_societe_vendeuse` AND `dvi_client` = `idc_contact`)
WHERE `scv_id_comptable` = 1
      AND `dvi_id_comptable` = 0
      AND `dvi_client` <> 0
      AND `idc_id` IS NULL
';
        $to_create = $this->db->query($sql);
        foreach ($to_create->result() as $devis) {
            $data = array(
                'idc_societe_vendeuse' => $devis->dvi_societe_vendeuse,
                'idc_contact' => $devis->dvi_client,
            );
            $this->db->insert('t_id_comptable', $data);
        }

        // We restore the state of "Publimail" and "Boitauxlettres IDF"
        foreach ($current_settings->result() as $societe_vendeuse) {
            $data = array(
                'scv_id_comptable' => $societe_vendeuse->scv_id_comptable,
            );
            $this->db->where('scv_id', $societe_vendeuse->scv_id)
                ->update('t_societes_vendeuses', $data);
        }

    }

    /**
     * Migration rollback method
     *
     * @return void
     */
    public function down() {
        $sql = 'DROP TRIGGER IF EXISTS `numeroteur_id_comptable`';
        $this->db->query($sql);

        $sql = '
ALTER TABLE `t_id_comptable`
DROP INDEX `idc_unique_societe_vendeuse_contact`,
DROP INDEX `idc_unique_societe_vendeuse_id_comptable`
';
        $this->db->query($sql);

        $this->dbforge->drop_column('t_id_comptable', 'idc_id_comptable');
        $this->dbforge->drop_column('t_id_comptable', 'idc_societe_vendeuse');
    }
}

// EOF