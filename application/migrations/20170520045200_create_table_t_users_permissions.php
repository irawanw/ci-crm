<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_users_permissions extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_users_permissions` (
  `usp_id` int(11) NOT NULL AUTO_INCREMENT,
  `usp_utilisateurs` int(9) NOT NULL,
  `usp_table` text NOT NULL,
  `usp_fields` text NOT NULL,
  `inactive` datetime NOT NULL,
  `deleted` datetime NOT NULL,
  PRIMARY KEY (`usp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
    
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
DROP TABLE IF EXISTS `t_users_permissions`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170520045200_create_table_t_users_permissions.php */
/* Location: .//tmp/fz3temp-1/20170520045200_create_table_t_users_permissions.php */