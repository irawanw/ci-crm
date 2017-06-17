<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_statistiques_prospection_contact extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts`  ADD `ctc_commercial_charge` INT(11) NOT NULL  AFTER `ctc_livr_info`,  ADD `ctc_enseigne` INT(11) NOT NULL  AFTER `ctc_commercial_charge`,  ADD `ctc_statistiques` BOOLEAN NOT NULL  AFTER `ctc_enseigne`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE  `t_contacts` DROP  `ctc_commercial_charge`, DROP `ctc_enseigne`, DROP `ctc_statistiques` ;
EOT;
        $this->db->query($sql);
    }
}