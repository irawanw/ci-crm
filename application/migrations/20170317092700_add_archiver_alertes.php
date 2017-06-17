<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_alertes extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_alertes` ADD `ale_archiver` DATETIME NULL AFTER `ale_inactif`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_alertes` DROP `ale_archiver` ;
EOT;
        $this->db->query($sql);
    }
}