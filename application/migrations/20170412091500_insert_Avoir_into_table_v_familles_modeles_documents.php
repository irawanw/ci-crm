<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Insert_Avoir_into_table_v_familles_modeles_documents extends CI_Migration {

    public function up() {
        $sql =  'INSERT INTO `v_familles_modeles_documents`
(`vfd_id`, `vfd_famille_modele`)
VALUES(7, "Avoir")';
        $this->db->query($sql);
    }

    public function down() {
        $sql =  'DELETE FROM `v_famille_modeles_documents` WHERE `vfd_id` = 7 AND `vfd_famille_modele` = "Avoir"';
        $this->db->query($sql);
    }
}

// EOF