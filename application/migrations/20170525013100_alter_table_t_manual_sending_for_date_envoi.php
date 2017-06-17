<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_t_manual_sending_for_date_envoi extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
ALTER TABLE  `t_manual_sending` DROP  `date_envoi` ;
EOT;
        $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
ALTER TABLE  `t_manual_sending` ADD  `date_envoi` DATE NULL AFTER  `segment_numero` ;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170525013100_alter_table_t_manual_sending_for_date_envoi.php */
/* Location: .//tmp/fz3temp-1/20170525013100_alter_table_t_manual_sending_for_date_envoi.php */