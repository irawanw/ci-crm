<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_table_t_pages_jaunes_for_subsending extends CI_Migration {	
	public function up() {
		$sql = <<<'EOT'
ALTER TABLE `t_pages_jaunes`
	ADD  `segment_numero` INT( 11 ) NULL AFTER `message`,
	CHANGE `software` `software` INT(11) NOT NULL,
    DROP `operateur_qui_envoie`, 
	DROP `date_envoi`, 
	DROP `date_limite_de_fin`, 
	DROP `openemm`, 
	DROP `verification_number`, 
	DROP `number_sent`, 
	DROP `quantite_envoyer`, 
	DROP `quantite_envoyee`, 
	DROP `copy_mail`, 
	DROP `number_sent_through`, 
	DROP `number_sent_mail` ;
EOT;
		$this->db->query($sql);

	}

	public function down() {

		$sql = <<<'EOT'
ALTER TABLE `t_pages_jaunes`
	DROP `segment_numero`,
	CHANGE `software` `software` VARCHAR(55) NULL,
	ADD `operateur_qui_envoie` int(11) NOT NULL,
  	ADD `date_envoi` date NOT NULL,
  	ADD `date_limite_de_fin` date NOT NULL,
  	ADD `openemm` int(11) NOT NULL,
  	ADD `verification_number` varchar(5) NOT NULL,
  	ADD `number_sent` int(11) NOT NULL,
  	ADD `quantite_envoyer` int(11) NOT NULL,
  	ADD `quantite_envoyee` int(11) NOT NULL,
  	ADD `copy_mail` int(11) NOT NULL,
  	ADD `number_sent_through` int(11) NOT NULL,
  	ADD `number_sent_mail` int(11) NOT NULL ;
EOT;
        $this->db->query($sql);
		
	}

}

/* End of file 20170425233000_create_table_t_softwares.php */
/* Location: .//tmp/fz3temp-1/20170425233000_create_table_t_softwares.php */