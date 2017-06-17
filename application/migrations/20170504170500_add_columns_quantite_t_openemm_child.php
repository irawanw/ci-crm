<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_quantite_t_openemm_child extends CI_Migration {
	
	public function up() {
		
		$sql = <<<'EOT'
ALTER TABLE `t_openemm_child`  ADD `quantite_envoyer` INT(11) NOT NULL  AFTER `segment_part`,  ADD `quantite_envoyee` INT(11) NOT NULL  AFTER `quantite_envoyer`;		
EOT;
		$this->db->query($sql);
		
	}
	
	public function down() {
		
		    $sql = <<<'EOT'
ALTER TABLE `t_openemm_child` DROP `quantite_envoyer`, DROP `quantite_envoyee`;
EOT;
		$this->db->query($sql);		
	}
}
