<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_societe_t_sous_traitants extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
ALTER TABLE `t_sous_traitants` CHANGE `societe` `societe` INT(11) NOT NULL;
EOT;
        $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
ALTER TABLE `t_sous_traitants` CHANGE `societe` `societe` VARCHAR(100) NOT NULL;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170525014200_alter_table_t_sendinblue_child_for_date_envoi.php */
/* Location: .//tmp/fz3temp-1/20170525014200_alter_table_t_sendinblue_child_for_date_envoi.php */