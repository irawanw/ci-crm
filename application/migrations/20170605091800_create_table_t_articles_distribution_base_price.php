<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_articles_distribution_base_price extends CI_Migration {

  public function up() {
    $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_articles_distribution_base_price` (
  `adb_id` int(11) NOT NULL AUTO_INCREMENT,
  `adb_secteur` int(11) NOT NULL,
  `adb_baseprice` double(9,2) NOT NULL,
  PRIMARY KEY (`adb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;
    
    $this->db->query($sql);
	
  $sql = <<<'EOT'
INSERT INTO `t_articles_distribution_base_price` (`adb_id`, `adb_secteur`, `adb_baseprice`) VALUES
(1, 1, 0.05),
(2, 2, 0.05),
(3, 3, 0.05),
(4, 4, 0.06),
(5, 5, 0.05),
(6, 6, 0.08),
(7, 7, 0.1),
(8, 8, 0.12);
EOT;
    
    $this->db->query($sql);	
  }

  public function down() {
    $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_articles_distribution_base_price`;
EOT;
        $this->db->query($sql);
  }

}
