<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_Add_2columns_contact_to_table_t_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'avr_correspondant' => array(
                'type' => 'INT',
                'constraint' => '9',
                'null' => FALSE,
                'unsigned' => TRUE,
                'default' => 0,
            ),
            'avr_client' => array(
                'type' => 'INT',
                'constraint' => '9',
                'null' => FALSE,
                'unsigned' => TRUE,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column('t_avoirs', $fields);

        // Initialize with existing data from facture, commande, and devis records
        $sql = '
UPDATE t_avoirs 
INNER JOIN t_factures ON (avr_facture = fac_id)
INNER JOIN t_commandes ON (fac_commande = cmd_id)
INNER JOIN t_devis ON (cmd_devis = dvi_id)
SET avr_correspondant = dvi_correspondant,
    avr_client = dvi_client
WHERE avr_correspondant = 0 OR avr_client = 0
';
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_column('t_avoirs', 'avr_client');
        $this->dbforge->drop_column('t_avoirs', 'avr_correspondant');
    }
}

// EOF