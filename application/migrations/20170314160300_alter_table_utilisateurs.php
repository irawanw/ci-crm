<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_utilisateurs extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_utilisateurs` ADD  `utl_archiver` DATETIME NULL ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_utilisateurs`
  DROP `utl_archiver` ;
EOT;
        $this->db->query($sql);
    }
}