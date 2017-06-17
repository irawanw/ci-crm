<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_vues_add_columns_default_default_by_admin extends CI_Migration {

	public function up() {
        $sql1 = <<<'EOT'
ALTER TABLE  `t_vues` ADD  `vue_default` BOOLEAN NULL DEFAULT NULL AFTER  `vue_proprietaire` ;
EOT;
        $this->db->query($sql1);
        $sql2 = <<<'EOT'
ALTER TABLE  `t_vues` ADD  `vue_default_by_admin` TINYINT NULL DEFAULT NULL AFTER  `vue_default` ;
EOT;
        $this->db->query($sql2);
	}

	public function down() {
        $sql1 = <<<'EOT'
ALTER TABLE  `t_vues` DROP COLUMN  `vue_default`;
EOT;
        $this->db->query($sql1);
        $sql2 = <<<'EOT'
ALTER TABLE  `t_vues` DROP COLUMN  `vue_default_by_admin` ;
EOT;
        $this->db->query($sql2);
	}

}

/* End of file 20170608092900_alter_table_vues_add_columns_default_default_by_admin */