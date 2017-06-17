<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_t_lignes_avoirs extends CI_Migration {

    public function up() {
        $fields= array(
            'lia_id' => array(
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'lia_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => FALSE,
                'default' => '',
            ),
            'lia_prix' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => FALSE,
                'default' => '0.000',
            ),
            'lia_quantite' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
            ),
            'lia_description' => array(
                'type' => 'VARCHAR',
                'constraint' => '400',
                'null' => FALSE,
                'default' => '',
            ),
            'lia_remise_taux' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,2',
                'unsigned' => FALSE,
                'null' => FALSE,
                'default' => '0.00',
            ),
            'lia_remise_ht' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'unsigned' => FALSE,
                'null' => FALSE,
                'default' => '0.00',
            ),
            'lia_remise_ttc' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'unsigned' => FALSE,
                'null' => FALSE,
                'default' => '0.00',
            ),
            'lia_avoir' => array(
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'lia_inactif' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => null,
            ),
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('lia_id', TRUE);
        $this->dbforge->add_key('lia_avoir');
        $this->dbforge->create_table('t_lignes_avoirs');

        // Popuplate
        $sql = '
INSERT INTO `t_lignes_avoirs` (`lia_code`, `lia_prix`, `lia_quantite`, `lia_description`, `lia_remise_taux`, `lia_remise_ht`, `lia_remise_ttc`, `lia_inactif`, `lia_avoir`)
SELECT `lif_code`, `lif_prix`, `lif_quantite`, `lif_description`, `lif_remise_taux`, `lif_remise_ht`, `lif_remise_ttc`, `lif_inactif`, `avr_id`
FROM `t_lignes_factures`
  INNER JOIN `t_avoirs` ON `lif_facture` = `avr_facture`
';
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table('t_lignes_avoirs');
    }
}

// EOF