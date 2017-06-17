<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_plaintes extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_plaintes`  ADD `pla_archiver` DATETIME NULL  AFTER `pla_secteur`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_plaintes`  DROP `pla_archiver`;
EOT;
        $this->db->query($sql);
    }
}