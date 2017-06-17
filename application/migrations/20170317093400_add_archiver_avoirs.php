<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_avoirs extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_avoirs`  ADD `avr_archiver` DATETIME NULL  AFTER `avr_fichier`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_avoirs`  DROP `avr_archiver`;
EOT;
        $this->db->query($sql);
    }
}