<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_articles_distribution_price extends CI_Migration {

  public function up() {
    $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_articles_distribution_price` (
  `adp_id` int(11) NOT NULL AUTO_INCREMENT,
  `adp_option` varchar(100) NOT NULL,
  `adp_value` varchar(100) NOT NULL,
  `adp_percentage` double(9,2) NOT NULL,
  PRIMARY KEY (`adp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;
    
    $this->db->query($sql);
  }

  public function down() {
    $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_articles_distribution_price`;
EOT;
        $this->db->query($sql);
  }

}

/* End of file 20170517214000_create_table_t_mailchimp.php */
/* Location: .//tmp/fz3temp-1/20170517214000_create_table_t_mailchimp.php */