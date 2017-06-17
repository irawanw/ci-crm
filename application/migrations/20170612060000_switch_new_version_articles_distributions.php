<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Switch_new_version_articles_distributions extends CI_Migration {

	public function up() {
		//switch articles distribution into new version
        $this->load->model('m_articles_distributions');
		$this->m_articles_distributions->generate_distribution_articles();
    }

    public function down() {
		//get latest version of article distribution catalogue 
        $sql = "SELECT MAX(cat_id) as cat_id FROM `t_catalogues` WHERE cat_famille = 3";
        $query 	= $this->db->query($sql);
		$cat_id	= $query->row()->cat_id;
		
		//delete latest version of article distribution catalogue 
		$sql = "DELETE FROM `t_catalogues` WHERE cat_famille = 3 AND cat_id = $cat_id";
		$this->db->query($sql);
		
		//clearing new catalogue distribution from article table
		$sql = "DELETE FROM `t_articles` WHERE art_catalogue = $cat_id";
		$this->db->query($sql);
    }

}

