<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_etat_t_domains extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_domains` ADD  `etat` VARCHAR( 50 ) NOT NULL AFTER  `host` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_domains`
  DROP `etat` ;
EOT;
        $this->db->query($sql);
    }
}