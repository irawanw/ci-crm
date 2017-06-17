<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Add_column_avr_societe_vendeuse_to_table_t_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'avr_societe_vendeuse' => array(
                'type' => 'INT',
                'constraint' => 9,
                'null' => FALSE,
                'unsigned' => TRUE,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column('t_avoirs', $fields);

        $sql = '
UPDATE t_avoirs
INNER JOIN t_factures ON (avr_facture = fac_id)
INNER JOIN t_commandes ON (fac_commande = cmd_id)
INNER JOIN t_devis ON (cmd_devis = dvi_id)
SET avr_societe_vendeuse = dvi_societe_vendeuse
WHERE avr_societe_vendeuse = 0
';
        $this->db->query($sql);

        // Remove the default value. From now on we don't want to accept avoir records
        // without a proper avr_societe_vendeuse value.
        $fields = array(
            'avr_societe_vendeuse' => array(
                'type' => 'INT',
                'constraint' => 9,
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->modify_column('t_avoirs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('t_avoirs', 'avr_societe_vendeuse');
    }
}

// EOF