<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_column_autre_que_societe_t_cartes_blues extends CI_Migration {

	public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_cartes_blues` CHANGE `autre_que_société` `autre_que_societe` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_cartes_blues` CHANGE `autre_que_societe` `autre_que_société` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
EOT;
        $this->db->query($sql);
    }

}

/* End of file 20170519024000_alter_table_t_utilisateurs_for_ensigne.php */
/* Location: .//tmp/fz3temp-1/20170519024000_alter_table_t_utilisateurs_for_ensigne.php */