<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_t_utilisateurs_for_ensigne extends CI_Migration {

	public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_utilisateurs` ADD  `utl_ensigne` INT( 9 ) NOT NULL AFTER  `utl_inactif` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE  `t_utilisateurs` DROP  `utl_ensigne` ;
EOT;
        $this->db->query($sql);
    }

}

/* End of file 20170519024000_alter_table_t_utilisateurs_for_ensigne.php */
/* Location: .//tmp/fz3temp-1/20170519024000_alter_table_t_utilisateurs_for_ensigne.php */