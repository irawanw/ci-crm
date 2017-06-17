<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_t_contacts_commentaires extends CI_Migration {

	public function up() {
		$this->dbforge->add_field(array(
				'comment_id' => array(
						'type' => 'INT',
						'constraint' => 11,
						'unsigned' => TRUE,
						'auto_increment' => FALSE
				),
				'comment_desc' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
				),
		));
		$this->dbforge->add_key('comment_id', TRUE);
		$this->dbforge->create_table('t_contacts_commentaires');
	}
	
    public function down() {
    	$this->dbforge->drop_table('t_contacts_commentaires');
    }
}