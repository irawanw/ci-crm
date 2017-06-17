<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_t_tests_followup extends CI_Migration {

    public function up() {
    	$sql = "CREATE TABLE `t_tests_followup` (
    			`followup_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			`message_id` int(11) NOT NULL DEFAULT '0',
    			`software` varchar(100) DEFAULT NULL,
    			`domain_used_send` varchar(100) DEFAULT NULL,
    			`domain_check_before_send` varchar(15) DEFAULT NULL,
    			`domain_check_after_send` varchar(15) DEFAULT NULL,
    			`rbl_blacklists` varchar(100) DEFAULT NULL,
    			`lien_desabo` varchar(3) DEFAULT NULL,
    			`provider_id` int(11) NOT NULL DEFAULT '0',
    			`q_day` varchar(50) DEFAULT NULL,
    			`q_hour` varchar(50) DEFAULT NULL,
    			`database_used` varchar(100) DEFAULT NULL,
    			`test_mail_used` int(11) NOT NULL DEFAULT '0',
    			`deliver_before_send` varchar(15) DEFAULT NULL,
    			`deliver_after_send` varchar(15) DEFAULT NULL,
    			`inactive` datetime DEFAULT NULL,
    			`deleted` datetime DEFAULT NULL
    			)";
    	$this->db->query($sql);
    }

    public function down() {
    	$sql = "DROP t_tests_followup";
    	$this->db->query($sql);
    }
}

// EOF