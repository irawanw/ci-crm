<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_vehicules extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_vehicules` ADD  `formule_assurance` VARCHAR( 255 ) NOT NULL AFTER  `list_reparation` ,
ADD  `prix_annuel_assurance` INT( 11 ) NOT NULL AFTER  `formule_assurance` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_vehicules`
  DROP `formule_assurance`,
  DROP `prix_annuel_assurance`;
EOT;
        $this->db->query($sql);
    }
}