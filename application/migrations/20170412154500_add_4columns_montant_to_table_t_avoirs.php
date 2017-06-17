<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Add_4columns_montant_to_table_t_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'avr_montant_htnr' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => false,
                'default' => '0.00',
            ),
            'avr_montant_ht' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => false,
                'default' => '0.00',
            ),
            'avr_montant_tva' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => false,
                'default' => '0.00',
            ),
            'avr_tva' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,4',
                'null' => false,
                'default' => '0.0000',
            ),
        );

        $this->dbforge->add_column('t_avoirs', $fields);

        // Get the missing TVA information from the original "facture" record
        // and update amounts
        $sql = '
UPDATE t_avoirs 
INNER JOIN t_factures ON (avr_facture = fac_id)
SET avr_tva = fac_tva,
    avr_montant_ht = avr_montant_ttc / (1 + fac_tva),
    avr_montant_tva = avr_montant_ttc - avr_montant_ttc / (1 + fac_tva)
WHERE avr_tva <> fac_tva
';
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_column('t_avoirs', 'avr_tva');
        $this->dbforge->drop_column('t_avoirs', 'avr_montant_tva');
        $this->dbforge->drop_column('t_avoirs', 'avr_montant_htnr');
        $this->dbforge->drop_column('t_avoirs', 'avr_montant_ht');
    }
}

// EOF