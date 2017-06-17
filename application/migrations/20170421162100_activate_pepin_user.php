<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Activate_pepin_user extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
UPDATE `t_employes`
SET `emp_etat` = 1
WHERE `emp_id` = 8;
EOT;
      $this->db->query($sql);  
    }


    public function down() {
        $sql = <<<'EOT'
UPDATE `t_employes`
SET `emp_etat` = 0
WHERE `emp_id` = 8;
EOT;
        $this->db->query($sql);
    }
}

// EOF