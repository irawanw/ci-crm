<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Update_column_formats_for_table_t_societes_vendeuses extends CI_Migration {

    public function up() {

        $sql =  '
UPDATE `t_societes_vendeuses`
SET `scv_format_avoir` = "AVR%-#"
WHERE `scv_format_avoir` IS NULL OR `scv_format_avoir` = ""
';
        $this->db->query($sql);

        $sql =  '
UPDATE `t_societes_vendeuses`
SET `scv_format_devis` = "DEV%-#"
WHERE `scv_format_devis` IS NULL OR `scv_format_devis` = ""
';
        $this->db->query($sql);

        $sql =  '
UPDATE `t_societes_vendeuses`
SET `scv_format_facture` = "FAC%-#"
WHERE `scv_format_facture` IS NULL OR `scv_format_facture` = ""
';
        $this->db->query($sql);

    }

    public function down() {
    }
}

// EOF