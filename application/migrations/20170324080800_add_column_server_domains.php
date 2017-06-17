<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_server_domains extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_domains` ADD  `server` INT NOT NULL AFTER  `nom` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_domains`
  DROP `server` ;
EOT;
        $this->db->query($sql);
    }
}