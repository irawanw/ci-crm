<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_contacts extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts`  ADD `ctc_archiver` DATETIME NULL  AFTER `ctc_livr_info`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_contacts`  ADD `ctc_archiver`;
EOT;
        $this->db->query($sql);
    }
}