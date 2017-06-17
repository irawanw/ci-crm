<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_v_types_origine_generale extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `v_types_origine_generale` (
  `generale_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `generale_name` varchar(100) NOT NULL,
  PRIMARY KEY (`generale_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;
EOT;
      echo $this->db->query($sql);

      $sqlInsert = <<<'EOT'
INSERT INTO `v_types_origine_generale` (`generale_id`, `generale_name`) VALUES
(1, 'E-mailing'),
(2, 'Adwords'),
(3, 'Prospection'),
(4, 'Flyers'),
(5, 'Appel entrant'),
(6, 'Parrainage'),
(7, 'Fax-mailing'),
(8, 'SMS-mailing');
EOT;

      $this->db->query($sqlInsert);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE `v_types_origine_generale`;
EOT;
        $this->db->query($sql);
    }
}

// EOF