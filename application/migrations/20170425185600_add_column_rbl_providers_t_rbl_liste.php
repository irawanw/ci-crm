<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_rbl_providers_t_rbl_liste extends CI_Migration {
	
	public function up() {
		if (!$this->db->field_exists('rbl_providers', 't_rbl_liste'))
		{
		$sql = <<<'EOT'
			ALTER TABLE `t_rbl_liste`  ADD `rbl_providers` VARCHAR(255) NOT NULL AFTER `rbl_delistable`;
EOT;
		$this->db->query($sql);
		}
	}
	
	public function down() {
		if ($this->db->field_exists('rbl_providers', 't_rbl_liste'))
		{
		    $sql = <<<'EOT'
			ALTER TABLE `t_rbl_liste`
			DROP `rbl_providers`;
EOT;
		$this->db->query($sql);
		}		
	}
}
