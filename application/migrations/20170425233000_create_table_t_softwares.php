<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_softwares extends CI_Migration {

	
	public function up() {
		$sql = <<<'EOT'
		CREATE TABLE  IF NOT EXISTS `t_softwares` (
		 `software_id` INT( 11 ) NOT NULL ,
		 `software_nom` VARCHAR( 100 ) NOT NULL ,
		 `inactive` DATETIME NOT NULL ,
		 `deleted` DATETIME NOT NULL ,
		PRIMARY KEY (  `software_id` )
		) ENGINE = INNODB DEFAULT CHARSET = utf8 AUTO_INCREMENT =0;
EOT;
		$this->db->query($sql);
		
        $sql = <<<'EOT'
  		ALTER TABLE  `t_softwares` CHANGE  `software_id`  `software_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
EOT;
		$this->db->query($sql);

	}

	public function down() {

		$sql = <<<'EOT'
DROP TABLE `t_softwares`;
EOT;
        $this->db->query($sql);
		
	}

}

/* End of file 20170425233000_create_table_t_softwares.php */
/* Location: .//tmp/fz3temp-1/20170425233000_create_table_t_softwares.php */