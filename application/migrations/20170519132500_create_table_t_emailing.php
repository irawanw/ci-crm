<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_emailing extends CI_Migration {

  public function up() {
    $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_emailing` (
  `emailing_id` int(11) NOT NULL AUTO_INCREMENT,
  `software` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  `commande` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `segment_numero` int(11) DEFAULT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_limite_de_fin` date DEFAULT NULL,
  `quantite_envoyer` int(11) NOT NULL,
  `inactive` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`emailing_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
EOT;
    
    $this->db->query($sql);
  }

  public function down() {
    $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_emailing`;
EOT;
        $this->db->query($sql);
  }

}

/* End of file 20170517214000_create_table_t_mailchimp.php */
/* Location: .//tmp/fz3temp-1/20170517214000_create_table_t_mailchimp.php */