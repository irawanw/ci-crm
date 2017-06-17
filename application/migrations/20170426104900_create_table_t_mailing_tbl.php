<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_t_mailing_tbl extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_mailing_tbl` (
  `mailing_id` int(10) unsigned NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `campaign_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `shortname` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `mailing_type` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `mailtemplate_id` int(10) UNSIGNED DEFAULT '0',
  `is_template` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `deleted` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `target_expression` text COLLATE utf8_unicode_ci,
  `change_date` datetime DEFAULT NULL,
  `mailinglist_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `needs_target` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `archived` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `cms_has_classic_content` int(1) NOT NULL DEFAULT '0',
  `dynamic_template` int(1) NOT NULL DEFAULT '0',
  `openaction_id` int(11) UNSIGNED DEFAULT '0',
  `clickaction_id` int(11) UNSIGNED DEFAULT '0',
  PRIMARY KEY (  `mailing_id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;
EOT;
		$this->db->query($sql);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE `t_mailing_tbl`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
