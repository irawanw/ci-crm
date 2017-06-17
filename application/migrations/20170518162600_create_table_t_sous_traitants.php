<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_sous_traitants extends CI_Migration {

	
	public function up() {
		$sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_sous_traitants` (
`sous_traitants_id` int(11) NOT NULL,
  `societe` varchar(150) NOT NULL,
  `ville` varchar(150) NOT NULL,
  `total_distribuer` varchar(150) NOT NULL,
  `pavillons` varchar(150) NOT NULL,
  `residences` varchar(100) NOT NULL,
  `hlm` varchar(100) NOT NULL,
  `client` varchar(50) NOT NULL,
  `prix_max` varchar(150) NOT NULL,
  `type_doc` varchar(100) NOT NULL,
  `type_client` varchar(15) NOT NULL,
  `date_limite` varchar(25) NOT NULL,
  `semaine_prevue` varchar(150) NOT NULL,
  `sous_traitant_demande` varchar(50) NOT NULL,
  `tel_sous_traitant` varchar(15) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `sous_inactif` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
EOT;
    
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
DROP TABLE IF EXISTS `t_sous_traitants`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170515222700_create_table_t_sendgrid.php */
/* Location: .//tmp/fz3temp-1/20170515222700_create_table_t_sendgrid.php */