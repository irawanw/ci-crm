<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_sendgrid_stats extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_sendgrid_stats` (
  `sendgrid_id` int(11) NOT NULL,
  `total` int(11) DEFAULT NULL,
  `current` int(11) DEFAULT NULL,
  `number_of_open` int(11) DEFAULT NULL,
  `open_rate` decimal(7,5) DEFAULT NULL,
  `number_of_click` int(11) DEFAULT NULL,
  `click_rate` decimal(7,5) DEFAULT NULL,
  `bounce` int(11) DEFAULT NULL,
  `bounce_rate` decimal(7,5) DEFAULT NULL,
  `hard_bounce_rate` decimal(7,5) DEFAULT NULL,
  `soft_bounce_rate` decimal(7,5) DEFAULT NULL,
  PRIMARY KEY (`sendgrid_id`),
  UNIQUE KEY `sendgrid_id` (`sendgrid_id`),
  KEY `sendgrid_id_2` (`sendgrid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
 
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
DROP TABLE IF EXISTS `t_sendgrid_stats`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170515223600_create_table_t_sendgrid_stats.php */
/* Location: .//tmp/fz3temp-1/20170515223600_create_table_t_sendgrid_stats.php */