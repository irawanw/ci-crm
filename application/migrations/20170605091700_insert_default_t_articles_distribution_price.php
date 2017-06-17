<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_default_t_articles_distribution_price extends CI_Migration {

  public function up() {
    $sql = <<<'EOT'
INSERT INTO `t_articles_distribution_price` (`adp_id`, `adp_option`, `adp_value`, `adp_percentage`) VALUES
(1, 'habitat', 'TOUT', 0.00),
(2, 'habitat', 'PAV', 25.00),
(3, 'habitat', 'PAV+RES', 10.00),
(4, 'habitat', 'RES+HLM', -10.00),
(5, 'habitat', 'HLM', -10.00),
(6, 'habitat', 'RES', 0.00),
(7, 'document', 'FLYER', 0.00),
(8, 'document', 'MAGAZINE', 25.00),
(9, 'document', 'GUIDE+200G', 100.00),
(10, 'type_distribution', 'SOLO', 50.00),
(11, 'type_distribution', 'POIGNEE', 0.00),
(12, 'delai', 'URGENCE', 50.00),
(13, 'delai', 'SEMAINE', 0.00),
(14, 'delai', 'MOIS', -10.00),
(15, 'delai', '3 MOIS', -25.00),
(16, 'delai', '6 MOIS', -50.00),
(17, 'delai', '12 MOIS', -75.00),
(18, 'controle', 'NORMAL', 0.00),
(19, 'controle', 'RENFORCE', 10.00),
(20, 'controle', 'INTEGRAL', 25.00),
(21, 'document', 'OBJET PUBLICITAIRE', 100.00);
EOT;
    
    $this->db->query($sql);
  }

  public function down() {
  	if ($this->db->table_exists('t_articles_distribution_price'))
	{
    $sql = <<<'EOT'
TRUNCATE `t_articles_distribution_price`;
EOT;
        $this->db->query($sql);
    }
  }

}

/* End of file 20170517214000_create_table_t_mailchimp.php */
/* Location: .//tmp/fz3temp-1/20170517214000_create_table_t_mailchimp.php */