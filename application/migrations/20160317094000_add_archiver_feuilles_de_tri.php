<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_feuilles_de_tri extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_feuilles_de_tri`  ADD `inactive` DATETIME NULL  AFTER `date_du_tri`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_feuilles_de_tri`  DROP `inactive`;
EOT;
        $this->db->query($sql);
    }
}