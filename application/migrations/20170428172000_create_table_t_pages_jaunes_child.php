<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_pages_jaunes_child extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_pages_jaunes_child` (
  `pages_jaunes_child_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `segment_part` int(11) DEFAULT NULL,
  `operateur_qui_envoie` int(11) NOT NULL,
  `date_envoi` date NOT NULL,
  `date_limite_de_fin` date NOT NULL,
  `openemm` int(11) NOT NULL,
  `verification_number` varchar(5) NOT NULL,
  `number_sent` int(11) NOT NULL,
  `quantite_envoyer` int(11) NOT NULL,
  `quantite_envoyee` int(11) NOT NULL,
  `copy_mail` int(11) NOT NULL,
  `number_sent_through` int(11) NOT NULL,
  `number_sent_mail` int(11) NOT NULL,
  `inactive` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`pages_jaunes_child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
    
    $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_pages_jaunes_child`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
