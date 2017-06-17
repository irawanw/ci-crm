<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_table_t_contacts extends CI_Migration {
	
	public function up() {
		$sql = <<<'EOT'
			ALTER TABLE `t_contacts`
			ADD `ctc_marche` VARCHAR(3) NOT NULL AFTER `ctc_client_prospect`,
			ADD `ctc_date_marche` DATETIME NOT NULL AFTER `ctc_marche`,
			ADD `ctc_alerte` VARCHAR(3) NOT NULL AFTER `ctc_date_marche`,
			ADD `ctc_date_alerte` DATETIME NOT NULL AFTER `ctc_alerte`,
			ADD `ctc_remarques_sur_marche` VARCHAR(500) NOT NULL AFTER `ctc_date_alerte`;
EOT;
		$this->db->query($sql);
	}
	
	public function down() {
		$sql = <<<'EOT'
			ALTER TABLE `t_contacts`
			DROP `ctc_marche`,
			DROP `ctc_date_marche`,
			DROP `ctc_alerte`,
			DROP `ctc_date_alerte`,
			DROP `ctc_remarques_sur_marche`;
EOT;
		$this->db->query($sql);
	}
}