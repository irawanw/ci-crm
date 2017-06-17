<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_v_types_origine_prospect extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `v_types_origine_prospect` (
  `origine_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `origine_name` varchar(30) NOT NULL,
  PRIMARY KEY (`origine_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;
EOT;
      echo $this->db->query($sql);

      $sqlInsert = <<<'EOT'
INSERT INTO `v_types_origine_prospect` (`origine_id`, `origine_name`) VALUES
(1, 'E-mailing publimailer'),
(2, 'E-mailing bal-idf'),
(3, 'E-mailing streetbuzz'),
(4, 'E-mailing distrib-orleans'),
(5, 'Adwords bal-idf'),
(6, 'Adwords publimail'),
(7, 'Adwords streetbuzz'),
(8, 'Adwords distrib-orleans'),
(9, 'Flyers GAL'),
(10, 'Flyers DUMOULIN'),
(11, 'Flyers autres'),
(12, 'Prospection Claire'),
(13, 'Prospection Ingrid'),
(14, 'Appel entrant inconnu'),
(15, 'Parrainage'),
(16, 'Fax-mailing'),
(17, 'SMS mailing');
EOT;

      $this->db->query($sqlInsert);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE v_types_origine_prospect;
EOT;
        $this->db->query($sql);
    }
}

// EOF