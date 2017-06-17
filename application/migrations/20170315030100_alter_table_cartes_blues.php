<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_cartes_blues extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_cartes_blues` ADD  `autre_que_société` VARCHAR( 255 ) NOT NULL AFTER  `societe` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_cartes_blues`
  DROP `autre_que_société` ;
EOT;
        $this->db->query($sql);
    }
}