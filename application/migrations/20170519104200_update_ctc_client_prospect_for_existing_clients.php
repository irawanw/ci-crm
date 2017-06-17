<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_forge $forge
 * @property CI_DB_query_builder $db
 */
class Migration_update_ctc_client_prospect_for_existing_clients extends CI_Migration {

    public function up() {
        $sql = '
UPDATE `t_contacts`
    INNER JOIN `t_devis` ON (`dvi_client` = `ctc_id`)
    INNER JOIN `t_commandes` ON (`cmd_devis` = `dvi_id`)
    INNER JOIN `t_factures` ON (`fac_commande` = `cmd_id`)
SET `ctc_client_prospect` = 2
WHERE `fac_inactif` IS NULL
    AND `dvi_inactif` IS NULL
    AND `cmd_inactif` IS NULL
';
        $this->db->query($sql);
    }

    public function down() {
        // Can't undo
    }

}
