<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_t_message_lists extends CI_Migration {
	
	public function up() {
		$sql = <<<'EOT'
			ALTER TABLE `t_message_list`  ADD `software` VARCHAR(100) NOT NULL  AFTER `type`,  ADD `client` INT(11) NOT NULL  AFTER `software`,  ADD `produit_vendu` VARCHAR(100) NOT NULL  AFTER `client`,  ADD `database` VARCHAR(100) NOT NULL  AFTER `produit_vendu`;
EOT;
		$this->db->query($sql);
	}
	
	public function down() {
		$sql = <<<'EOT'
			ALTER TABLE `t_message_list`
			DROP `software`,
			DROP `client`,
			DROP `produit_vendu`,
			DROP `database`,
EOT;
		$this->db->query($sql);
	}
}