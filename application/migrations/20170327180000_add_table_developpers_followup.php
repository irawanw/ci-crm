<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_developpers_followup extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_developpers_followup` (
  `dev_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cor_tiket` varchar(100) DEFAULT NULL,
  `priorite` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT NULL,
  `descriptif` text,
  `developpeur` int(11) DEFAULT NULL,
  `date_demande` date DEFAULT NULL,
  `date_de_fin_souhaitee` date DEFAULT NULL,
  `etat` varchar(30) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `inactive` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`dev_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE  `t_developpers_followup` ;
EOT;
        $this->db->query($sql);
    }
}