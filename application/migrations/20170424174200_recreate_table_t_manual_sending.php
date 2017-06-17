<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_recreate_table_t_manual_sending extends CI_Migration 
{
    public function up() 
    {
        $sqlDrop = <<<'EOT'
        DROP TABLE IF EXISTS `t_manual_sending`;
EOT;
        $this->db->query($sqlDrop);  
        $sql = <<<'EOT'
CREATE TABLE `t_manual_sending` (
  `manual_sending_id` int(11) NOT NULL AUTO_INCREMENT,
  `software` varchar(55) NOT NULL,
  `client` int(11) NOT NULL,
  `commande` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `segment_numero` int(11) NOT NULL,
  `inactive` datetime NOT NULL,
  `deleted` datetime NOT NULL,
  PRIMARY KEY (`manual_sending_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
		$this->db->query($sql);
    }

    public function down() {
        $sqlDrop = <<<'EOT'
DROP TABLE `t_manual_sending`;
EOT;
      $this->db->query($sqlDrop);
  $sqlRecreate = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_manual_sending` (
  `manual_sending_id` int(11) NOT NULL AUTO_INCREMENT,
  `software` varchar(55) NOT NULL,
  `operateur_qui_envoie` int(11) NOT NULL,
  `date_envoi` date NOT NULL,
  `date_limite_de_fin` date NOT NULL,
  `client` int(11) NOT NULL,
  `commande` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `segment_numero` int(11) NOT NULL,
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
  PRIMARY KEY (`manual_sending_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
EOT;
        $this->db->query($sqlRecreate);
    }
}

// EOF
