<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_column_software_t_tests_followup extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_tests_followup` CHANGE `software` `software` INT(11) DEFAULT 0;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_tests_followup` CHANGE `software` `software` VARCHAR(100) DEFAULT NULL;
EOT;
        $this->db->query($sql);
    }
}