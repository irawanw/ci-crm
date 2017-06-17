<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_origine_generale_contacts extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts`  ADD `ctc_origine_generale` VARCHAR(100) NULL  AFTER `ctc_origine`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE  `t_contacts` DROP  `ctc_origine_generale`;
EOT;
        $this->db->query($sql);
    }
}
