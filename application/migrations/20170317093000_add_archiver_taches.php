<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_taches extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_taches`  ADD `tac_archiver` DATETIME NULL  AFTER `tac_inactif`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_taches`  DROP `tac_archiver`;
EOT;
        $this->db->query($sql);
    }
}