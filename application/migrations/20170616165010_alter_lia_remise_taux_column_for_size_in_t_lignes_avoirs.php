<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_Alter_lia_remise_taux_column_for_size_in_t_lignes_avoirs extends CI_Migration {

    public function up() {
        $fields = array(
            'lia_remise_taux' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.0,
            ),
        );

        $this->dbforge->modify_column('t_lignes_avoirs', $fields);
    }

    public function down() {
        // Do nothing, or we might lose info
    }
}

// EOF