<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Add_2columns_for_id_comptables_to_table_t_societes_vendeuses extends CI_Migration {

    public function up() {
        $fields = array(
            'scv_no_id_comptable' => array(
                'type' => 'INT',
                'constraint' => 9,
                'null' => FALSE,
                'unsigned' => TRUE,
                'default' => 0,
            ),
            'scv_format_id_comptable' => array(
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => FALSE,
                'default' => '%',
            ),
        );

        $this->dbforge->add_column('t_societes_vendeuses', $fields);

    }

    public function down() {
        $this->dbforge->drop_column('t_societes_vendeuses', 'scv_format_id_comptable');
        $this->dbforge->drop_column('t_societes_vendeuses', 'scv_no_id_comptable');
    }
}

// EOF