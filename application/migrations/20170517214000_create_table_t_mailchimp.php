<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_t_mailchimp extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS  `t_mailchimp` (
 `mailchimp_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `software` INT( 11 ) NOT NULL ,
 `message` INT( 11 ) NOT NULL ,
 `segment_numero` INT( 11 ) DEFAULT NULL ,
 `client` INT( 11 ) NOT NULL ,
 `commande` INT( 11 ) NOT NULL ,
 `date_envoi` DATE DEFAULT NULL ,
 `date_limite_de_fin` DATE DEFAULT NULL ,
 `quantite_envoyer` INT( 11 ) NOT NULL ,
 `inactive` DATETIME DEFAULT NULL ,
 `deleted` DATETIME DEFAULT NULL ,
 PRIMARY KEY (  `mailchimp_id` )
) ENGINE = INNODB DEFAULT CHARSET = utf8;
EOT;
    
    $this->db->query($sql);
	}

	public function down() {
		$sql = <<<'EOT'
DROP TABLE IF EXISTS `t_mailchimp`;
EOT;
        $this->db->query($sql);
	}

}

/* End of file 20170517214000_create_table_t_mailchimp.php */
/* Location: .//tmp/fz3temp-1/20170517214000_create_table_t_mailchimp.php */