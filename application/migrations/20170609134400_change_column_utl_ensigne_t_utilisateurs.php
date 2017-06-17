<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_column_utl_ensigne_t_utilisateurs extends CI_Migration {

	public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_utilisateurs` CHANGE `utl_ensigne` `utl_ensigne` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_utilisateurs` CHANGE `utl_ensigne` `utl_ensigne` INT(11) NOT NULL;
EOT;
        $this->db->query($sql);
    }

}

