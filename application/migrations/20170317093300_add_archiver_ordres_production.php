<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_ordres_production extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_ordres_production`  ADD `opr_archiver` DATETIME NULL  AFTER `opr_etat`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_ordres_production`  DROP `opr_archiver`;
EOT;
        $this->db->query($sql);
    }
}