<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Add_column_avr_transmis_to_table_t_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'avr_transmis' => array(
                'type' => 'VARCHAR',
                'constraint' => 1000,
                'null' => false,
                'default' => '',
            ),
        );

        $this->dbforge->add_column('t_avoirs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('t_avoirs', 'avr_transmis');
    }
}

// EOF