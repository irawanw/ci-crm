<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Alter_column_avr_reference_in_table_t_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'avr_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => '',
            ),
        );
        $this->dbforge->modify_column('t_avoirs', $fields);
    }

    public function down() {
        $fields = array(
            'avr_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'default' => '',
            ),
        );
        $this->dbforge->modify_column('t_avoirs', $fields);
    }
}

// EOF