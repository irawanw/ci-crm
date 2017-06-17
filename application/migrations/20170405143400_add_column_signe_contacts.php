<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_signe_contacts extends CI_Migration {
	
	public function up() {
		$sql = <<<'EOT'
			ALTER TABLE `t_contacts`  ADD `ctc_signe` BOOLEAN NOT NULL  AFTER `ctc_statistiques`;
EOT;
		$this->db->query($sql);
	}
	
	public function down() {
		$sql = <<<'EOT'
			ALTER TABLE `t_contacts`
			DROP `ctc_signe`;
EOT;
		$this->db->query($sql);
	}
}