<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_table_t_openemm_for_subsending extends CI_Migration {  
  public function up() {
    $sql = <<<'EOT'
ALTER TABLE `t_openemm` 
ADD `segment_numero` INT(11) NULL AFTER `message`,
CHANGE `software` `software` INT(11) NOT NULL,
CHANGE `inactive` `inactive` datetime DEFAULT NULL,
CHANGE `deleted` `deleted` datetime DEFAULT NULL,
DROP `operateur_qui_envoie`, 
DROP `date_envoi`, 
DROP `date_limite_de_fin`, 
DROP `stats`, 
DROP `segment_part`, 
DROP `openemm`, 
DROP `deliv_sur_test_orange`, 
DROP `deliv_sur_test_free`, 
DROP `deliv_sur_test_sfr`, 
DROP `deliv_sur_test_gmail`, 
DROP `deliv_sur_test_microsoft`, 
DROP `deliv_sur_test_yahoo`, 
DROP `deliv_sur_test_ovh`, 
DROP `deliv_sur_test_oneandone`, 
DROP `physical_server`, 
DROP `smtp`, 
DROP `rotation`;
EOT;
    $this->db->query($sql);

  }

  public function down() {

    $sql = <<<'EOT'
ALTER TABLE `t_openemm`
  DROP `segment_numero`,
  CHANGE `software` `software` VARCHAR(55) NULL,
  ADD `operateur_qui_envoie` int(11) NOT NULL,
  ADD `date_envoi` date DEFAULT NULL,
  ADD `date_limite_de_fin` date DEFAULT NULL,
  ADD `stats` varchar(55) NOT NULL,
  ADD `segment_part` int(11) NOT NULL,
  ADD `openemm` int(11) NOT NULL,
  ADD `deliv_sur_test_orange` varchar(55) NOT NULL,
  ADD `deliv_sur_test_free` varchar(55) NOT NULL,
  ADD `deliv_sur_test_sfr` varchar(55) NOT NULL,
  ADD `deliv_sur_test_gmail` varchar(55) NOT NULL,
  ADD `deliv_sur_test_microsoft` varchar(55) NOT NULL,
  ADD `deliv_sur_test_yahoo` varchar(55) NOT NULL,
  ADD `deliv_sur_test_ovh` varchar(55) NOT NULL,
  ADD `deliv_sur_test_oneandone` varchar(55) NOT NULL,
  ADD `physical_server` varchar(55) NOT NULL,
  ADD `smtp` varchar(55) NOT NULL,
  ADD `rotation` varchar(5) NOT NULL;
EOT;
        $this->db->query($sql);
    
  }

}

/* End of file 20170425233000_create_table_t_softwares.php */
/* Location: .//tmp/fz3temp-1/20170425233000_create_table_t_softwares.php */