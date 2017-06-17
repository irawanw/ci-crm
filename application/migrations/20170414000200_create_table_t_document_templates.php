<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_document_templates extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_document_templates` (
  `tpl_id` int(11) NOT NULL AUTO_INCREMENT,
  `tpl_nom` varchar(50) NOT NULL,
  `tpl_content` text NOT NULL,
  `tpl_created_date` datetime DEFAULT NULL,
  `tpl_inactive` datetime DEFAULT NULL,
  `tpl_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
EOT;
		$this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE IF EXISTS `t_document_templates`;
EOT;
        $this->db->query($sql);
    }
}

// EOF