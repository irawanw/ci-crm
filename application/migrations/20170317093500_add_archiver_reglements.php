<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_reglements extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_reglements`  ADD `rgl_archiver` DATETIME NULL  AFTER `rgl_client`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_reglements`  DROP `rgl_archiver`;
EOT;
        $this->db->query($sql);
    }
}