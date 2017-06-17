<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_ips_domaines_servers extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE `t_servers` CHANGE `domaines` `domaines` VARCHAR(100) NULL,CHANGE `ips` `ips` VARCHAR(100) NULL;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_servers` CHANGE `domaines` `domaines` INT(11) NULL,CHANGE `ips` `ips` INT(11) NULL;
EOT;
        $this->db->query($sql);
    }
}