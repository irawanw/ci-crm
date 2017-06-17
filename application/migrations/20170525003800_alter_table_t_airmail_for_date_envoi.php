<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_t_airmail_for_date_envoi extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
ALTER TABLE  `t_airmail` DROP  `date_envoi` ;
EOT;
        $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
ALTER TABLE  `t_airmail` ADD  `date_envoi` DATE NULL AFTER  `commande` ;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170525003800_alter_table_t_airmail_for_date_envoi.php */
/* Location: .//tmp/fz3temp-1/20170525003800_alter_table_t_airmail_for_date_envoi.php */