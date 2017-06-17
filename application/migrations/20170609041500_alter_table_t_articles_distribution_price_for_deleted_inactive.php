<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_t_articles_distribution_price_for_deleted_inactive extends CI_Migration {

	
	public function up() {
		$sql = <<<'EOT'
ALTER TABLE  `t_articles_distribution_price` ADD  `inactive` DATETIME NULL DEFAULT NULL ,
ADD  `deleted` DATETIME NULL DEFAULT NULL ;
EOT;
        $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
ALTER TABLE `t_articles_distribution_price` DROP `inactive`, DROP `deleted`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170609041500_alter_table_t_articles_distribution_price_for_deleted_inactive.php */
/* Location: .//tmp/fz3temp-1/20170609041500_alter_table_t_articles_distribution_price_for_deleted_inactive.php */