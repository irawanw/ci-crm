<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_servers3 extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_servers` ADD  `pas_engage` TINYINT( 1 ) NOT NULL AFTER  `date_de_resiliation` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_servers`
  DROP `pas_engage` ;
EOT;
        $this->db->query($sql);
    }
}