<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Enable_scv_id_comptable_for_scv_id_2_3_4_7 extends CI_Migration {

    /**
     * Migration upgrade method
     *
     * @return void
     */
    public function up() {
        $data = array(
            'scv_id_comptable' => 1,
        );
        $this->db->where_in('scv_id', array(2, 3, 4, 7))
            ->update('t_societes_vendeuses', $data);
    }

    /**
     * Migration rollback method
     *
     * @return void
     */
    public function down() {
        $data = array(
            'scv_id_comptable' => 0,
        );
        $this->db->where_in('scv_id', array(2, 3, 4, 7))
            ->update('t_societes_vendeuses', $data);
    }
}

// EOF