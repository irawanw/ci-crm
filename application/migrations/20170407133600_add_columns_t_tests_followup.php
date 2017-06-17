<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_t_tests_followup extends CI_Migration {
	
	public function up() {
		$sql = <<<'EOT'
			ALTER TABLE `t_tests_followup`  ADD `number_simultaneaous_mails` INT(11) NOT NULL  AFTER `deliver_after_send`,  ADD `email_subject_changed` INT(11) NOT NULL  AFTER `number_simultaneaous_mails`;
EOT;
		$this->db->query($sql);
	}
	
	public function down() {
		$sql = <<<'EOT'
			ALTER TABLE `t_tests_followup`
			DROP `number_simultaneaous_mails`,
			DROP `email_subject_changed`;
EOT;
		$this->db->query($sql);
	}
}