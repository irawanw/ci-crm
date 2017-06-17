<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_catalogues extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_catalogues`  ADD `cat_archiver` DATETIME NULL  AFTER `cat_date`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_catalogues`  DROP `cat_archiver`;
EOT;
        $this->db->query($sql);
    }
}