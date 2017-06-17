<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_usp_type_to_t_users_permissions extends CI_Migration {

	public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_users_permissions` ADD `usp_type` VARCHAR(50) NOT NULL AFTER `usp_table`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE  `t_users_permissions` DROP  `usp_type` ;
EOT;
        $this->db->query($sql);
    }

}

/* End of file 20170519024000_alter_table_t_utilisateurs_for_ensigne.php */
/* Location: .//tmp/fz3temp-1/20170519024000_alter_table_t_utilisateurs_for_ensigne.php */