<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Recreate_table_t_pages_jaunes_child extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
ALTER TABLE `t_pages_jaunes_child`
DROP `date_envoi`,
DROP `date_limite_de_fin`,
DROP `quantite_envoyer`,
ADD `open` int(11) NOT NULL AFTER `quantite_envoyee`,
ADD `open_pourcentage` double(9,2) NOT NULL AFTER `open`;
EOT;
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
ALTER TABLE `t_pages_jaunes_child`
ADD `date_envoi` date DEFAULT NULL AFTER `operateur_qui_envoie`,
ADD  `date_limite_de_fin` date DEFAULT NULL AFTER `date_envoi`,
ADD  `quantite_envoyer` int(11) NOT NULL AFTER `date_limite_de_fin`,
DROP `open`,
DROP `open_pourcentage`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170513033000_recreate_table_t_pages_jaunes_child.php */
/* Location: .//tmp/fz3temp-1/20170513033000_recreate_table_t_pages_jaunes_child.php */