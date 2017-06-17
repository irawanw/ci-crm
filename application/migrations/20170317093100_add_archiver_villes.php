<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_villes extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_villes` ADD `vil_archiver` DATETIME NULL AFTER `vil_cp`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_villes` DROP `vil_archiver`;
EOT;
        $this->db->query($sql);
    }
}