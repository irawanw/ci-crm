<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_manual_sending_child extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_manual_sending_child` (
  `manual_sending_child_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `operateur_qui_envoie` int(11) NOT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_limite_de_fin` date DEFAULT NULL,
  `segment_part` int(11) NOT NULL,
  `verification_number` varchar(30) NOT NULL,
  `quantite_envoyer` int(11) NOT NULL,
  `quantite_envoyee` int(11) NOT NULL,
  `number_sent` int(11) NOT NULL,
  `openemm` int(11) NOT NULL,
  `deliv_sur_test_orange` varchar(55) NOT NULL,
  `deliv_sur_test_free` varchar(55) NOT NULL,
  `deliv_sur_test_sfr` varchar(55) NOT NULL,
  `deliv_sur_test_gmail` varchar(55) NOT NULL,
  `deliv_sur_test_microsoft` varchar(55) NOT NULL,
  `deliv_sur_test_yahoo` varchar(55) NOT NULL,
  `deliv_sur_test_ovh` varchar(55) NOT NULL,
  `deliv_sur_test_oneandone` varchar(55) NOT NULL,
  `manual_sender` int(11) NOT NULL,
  `speed_hours` decimal(7,5) NOT NULL,
  `number_hours` int(11) NOT NULL,
  `inactive` datetime NOT NULL,
  `deleted` datetime NOT NULL,
  PRIMARY KEY (`manual_sending_child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
EOT;
		$this->db->query($sql);
		
        $sql = <<<'EOT'
ALTER TABLE  `t_manual_sending_child` CHANGE  `manual_sending_child_id`  `manual_sending_child_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
EOT;
		$this->db->query($sql);
    }
    public function down() {
        $sql = <<<'EOT'
DROP TABLE `t_manual_sending_child`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
