<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_servers2 extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_servers` ADD  `ajouter_des_sites_hébergés` VARCHAR( 255 ) NOT NULL AFTER  `domaines` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_servers`
  DROP `ajouter_des_sites_hébergés` ;
EOT;
        $this->db->query($sql);
    }
}