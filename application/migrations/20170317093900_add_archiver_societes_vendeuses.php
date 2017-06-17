<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_societes_vendeuses extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_societes_vendeuses`  ADD `scv_archiver` DATETIME NULL  AFTER `scv_format_avoir`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_societes_vendeuses`  DROP `scv_archiver`;
EOT;
        $this->db->query($sql);
    }
}