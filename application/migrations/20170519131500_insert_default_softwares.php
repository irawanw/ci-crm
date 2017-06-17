<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_default_softwares extends CI_Migration {

  public function up() {
    $sqlDelete = <<<'EOT'
TRUNCATE TABLE `t_softwares`;   
EOT;
    echo $this->db->query($sqlDelete);

    $sql = <<<'EOT'
INSERT INTO `t_softwares` (`software_id`, `software_nom`, `inactive`, `deleted`) VALUES
(1, 'open emm', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'pages jaunes', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'manual', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'maxbulk', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'sendgrid', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'sendinblue', '2017-05-19 00:00:00', '0000-00-00 00:00:00'),
(7, 'airmail', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'mailchimp', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
EOT;
    
    $this->db->query($sql);
  }

  public function down() {
    $sqlDelete = <<<'EOT'
TRUNCATE TABLE `t_softwares`;   
EOT;
    echo $this->db->query($sqlDelete);

    $sql = <<<'EOT'
INSERT INTO `t_softwares` (`software_id`, `software_nom`, `inactive`, `deleted`) VALUES
(1, 'open emm', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'pages jaunes', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'manual', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'maxbulk', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'sendgrid', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'sendinblue', '2017-05-19 00:00:00', '0000-00-00 00:00:00'),
(7, 'airmail', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'mailchimp', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
EOT;
    
    $this->db->query($sql);
  }

}

/* End of file 20170517214000_create_table_t_mailchimp.php */
/* Location: .//tmp/fz3temp-1/20170517214000_create_table_t_mailchimp.php */