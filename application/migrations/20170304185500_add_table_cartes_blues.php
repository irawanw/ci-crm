<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_cartes_blues extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'carte_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'banque' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'premiers_chiffres' => array(
                'type' => 'VARCHAR',
                'constraint' => 4,
            ),
            'derniers_chiffres' => array(
                'type' => 'VARCHAR',
                'constraint' => 4,
            ),
            'societe' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'inactive' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'deleted' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('carte_id', TRUE);
        $this->dbforge->create_table('t_carte_blues');
    }

    public function down() {
        $this->dbforge->drop_table('t_carte_blues');
    }
}

// EOF