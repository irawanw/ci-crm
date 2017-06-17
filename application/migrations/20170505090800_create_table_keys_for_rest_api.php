<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_keys_for_rest_api extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
EOT;
    
    $this->db->query($sql);

      $sqlTruncate = <<<'EOT'
TRUNCATE TABLE `keys`;
EOT;
    $this->db->query($sqlTruncate);
      $sqlInsert = <<<'EOT'
INSERT INTO `keys` (`id`, `user_id`, `key`, `level`, `ignore_limits`, `is_private_key`, `ip_addresses`, `date_created`) VALUES
(1, 1, 'D9dqvZ5O1iCV1ecAEvGydnb68Fzoe1Ey7WMlgU3W', 1, 3, 0, NULL, 0);
EOT;
    $this->db->query($sqlInsert);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE IF EXISTS `keys`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
