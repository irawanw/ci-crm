<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_document_table extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_document_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `template` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `client_id` int(11) NOT NULL,
  `date_generate` date NOT NULL,
  `inactive` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
EOT;
		$this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE `t_document_table`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
