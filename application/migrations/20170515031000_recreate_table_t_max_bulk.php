<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Recreate_table_t_max_bulk extends CI_Migration {

	public function up() { 
        $sql = <<<'EOT'
ALTER TABLE `t_max_bulk` 
ADD `date_envoi` date DEFAULT NULL AFTER `segment_numero`,
ADD  `date_limite_de_fin` date DEFAULT NULL AFTER `date_envoi`,
ADD  `quantite_envoyer` int(11) NOT NULL AFTER `date_limite_de_fin`;
EOT;
    $this->db->query($sql);
  }

  public function down() {    
  $sql = <<<'EOT'
ALTER TABLE `t_max_bulk`
DROP `date_envoi`,
DROP `date_limite_de_fin`,
DROP `quantite_envoyer`;  
EOT;
        $this->db->query($sql);
  }

}

/* End of file 20170513033000_recreate_table_t_max_bulk.php */
/* Location: .//tmp/fz3temp-1/20170513033000_recreate_table_t_max_bulk.php */