<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_t_rbl_liste extends CI_Migration {

    public function up() {
        $sql = <<<'EOT'
ALTER TABLE  `t_rbl_liste` ADD  `rbl_url` VARCHAR( 255 ) NOT NULL AFTER  `rbl_nom` ,
ADD  `rbl_abuse_mail` VARCHAR( 255 ) NOT NULL AFTER  `rbl_url` ,
ADD  `rbl_delistable` VARCHAR( 255 ) NOT NULL AFTER  `rbl_abuse_mail` ;
EOT;
        $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_rbl_liste`
  DROP `rbl_url`,
  DROP `rbl_abuse_mail`,
  DROP `rbl_delistable`;
EOT;
        $this->db->query($sql);
    }
}