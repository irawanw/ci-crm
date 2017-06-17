<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_archiver_taux_tva extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_taux_tva`  ADD `tva_archiver` DATETIME NULL  AFTER `tva_date`;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_taux_tva`  DROP `tva_archiver`;
EOT;
        $this->db->query($sql);
    }
}