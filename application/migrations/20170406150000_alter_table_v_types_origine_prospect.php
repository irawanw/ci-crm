<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_v_types_origine_prospect extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `v_types_origine_prospect`  ADD `origine_group` INT(11) NOT NULL  AFTER `origine_id`,  ADD   INDEX  (`origine_group`);
EOT;
        $this->db->query($sql);

		$sqlTruncate = <<<'EOT'
TRUNCATE `v_types_origine_prospect`;
EOT;
        $this->db->query($sqlTruncate);        

         $sqlInsert = <<<'EOT'
INSERT INTO `v_types_origine_prospect` (`origine_id`, `origine_group`,`origine_name`) VALUES
(1, 1, 'E-mailing publimailer'),
(2, 1, 'E-mailing bal-idf'),
(3, 1, 'E-mailing streetbuzz'),
(4, 1, 'E-mailing distrib-orleans'),
(5, 2, 'Adwords bal-idf'),
(6, 2, 'Adwords publimail'),
(7, 2, 'Adwords streetbuzz'),
(8, 2, 'Adwords distrib-orleans'),
(9, 3, 'Prospection Claire'),
(10, 3, 'Prospection Ingrid'),
(11, 4, 'Flyers GAL'),
(12, 4, 'Flyers DUMOULIN'),
(13, 4, 'Flyers autres'),
(14, 5, 'Appel entrant inconnu'),
(15, 6, 'Parrainage'),
(16, 7, 'Fax-mailing'),
(17, 8, 'SMS mailing');
EOT;

      $this->db->query($sqlInsert);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts` DROP `origine_group`;
EOT;
        $this->db->query($sql);
    }
}