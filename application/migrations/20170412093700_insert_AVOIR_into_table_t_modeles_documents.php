<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Insert_AVOIR_into_table_t_modeles_documents extends CI_Migration {

    public function up() {

        // mod_type "2" is "Message" from v_types_modeles_documents table
        // mod_famille "7" is "Avoir" from v_familles_modeles_documents table
        // mod_disque "1" is "Modèles" from t_disques_archivage

        $sql =  'INSERT INTO `t_modeles_documents`
(`mod_nom`, `mod_type`, `mod_description`, `mod_sujet`, `mod_disque`, `mod_famille`)
VALUES("AVOIR", 2, "Message envoi avoir", "Envoi d\'avoir", 1, 7)';
        $this->db->query($sql);
    }

    public function down() {
        $sql =  'DELETE FROM `t_modeles_documents` WHERE `mod_nom` = "AVOIR" AND `mod_type` = 2 AND `mod_famille` = 7';
        $this->db->query($sql);
    }
}

// EOF