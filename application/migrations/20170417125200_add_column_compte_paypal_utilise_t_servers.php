<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_compte_paypal_utilise_t_servers extends CI_Migration {
	
	public function up() {
		$sql = <<<'EOT'
			ALTER TABLE `t_servers`  ADD `compte_paypal_utilise` VARCHAR(255) NOT NULL AFTER `virtualisation_server`;
EOT;
		$this->db->query($sql);
	}
	
	public function down() {
		$sql = <<<'EOT'
			ALTER TABLE `t_servers`
			DROP `compte_paypal_utilise`;
EOT;
		$this->db->query($sql);
	}
}