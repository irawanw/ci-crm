<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_sendinblue_child extends CI_Migration {


	public function up() {
		$sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_sendinblue_child` (
  `sendinblue_child_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `operateur_qui_envoie` int(11) NOT NULL,
  `stats` varchar(55) NOT NULL,
  `segment_part` int(11) NOT NULL,
  `quantite_envoyee` int(11) NOT NULL,
  `open` int(11) NOT NULL,
  `open_pourcentage` double NOT NULL,
  `sendinblue` int(11) NOT NULL,
  `deliv_sur_test_orange` varchar(55) NOT NULL,
  `deliv_sur_test_free` varchar(55) NOT NULL,
  `deliv_sur_test_sfr` varchar(55) NOT NULL,
  `deliv_sur_test_gmail` varchar(55) NOT NULL,
  `deliv_sur_test_microsoft` varchar(55) NOT NULL,
  `deliv_sur_test_yahoo` varchar(55) NOT NULL,
  `deliv_sur_test_ovh` varchar(55) NOT NULL,
  `deliv_sur_test_oneandone` varchar(55) NOT NULL,
  `physical_server` varchar(55) NOT NULL,
  `smtp` varchar(55) NOT NULL,
  `rotation` varchar(5) NOT NULL,
  `inactive` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`sendinblue_child_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;
EOT;
    
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
DROP TABLE IF EXISTS `t_sendinblue_child`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170516075500_create_table_t_sendinblue_child.php */
/* Location: .//tmp/fz3temp-1/20170516075500_create_table_t_sendinblue_child.php */