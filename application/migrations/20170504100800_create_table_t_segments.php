<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_segments extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_segments` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `userid` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `filtering` varchar(1500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;
    
    $this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_segments`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
