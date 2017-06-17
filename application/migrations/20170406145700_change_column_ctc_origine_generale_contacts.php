<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_column_ctc_origine_generale_contacts extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts` CHANGE `ctc_origine_generale` `ctc_origine_generale` INT(11) NULL DEFAULT NULL;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts` CHANGE `ctc_origine_generale` `ctc_origine_generale` VARCHAR(100) NULL;
EOT;
        $this->db->query($sql);
    }
}