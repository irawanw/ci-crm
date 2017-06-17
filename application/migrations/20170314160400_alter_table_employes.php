<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_employes extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_employes` ADD  `emp_indemnite_kilometrique` DECIMAL( 10, 2 ) NOT NULL AFTER  `emp_immatriculation` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE  `t_employes` DROP  `emp_indemnite_kilometrique` ;
EOT;
        $this->db->query($sql);
    }
}