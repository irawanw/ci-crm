<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_servers extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_servers` CHANGE  `payer_avec`  `cb_utilsée` INT( 11 ) NOT NULL ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_servers`
  DROP `cb_utilsée` ;
EOT;
        $this->db->query($sql);
    }
}