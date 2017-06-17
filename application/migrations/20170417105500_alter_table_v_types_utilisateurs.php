<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_v_types_utilisateurs extends CI_Migration 
{
    public function up() 
    {
      $sqlTruncate = <<<'EOT'
TRUNCATE `v_types_utilisateurs`;
EOT;
      echo $this->db->query($sqlTruncate);

      $sqlInsert = <<<'EOT'
INSERT INTO `v_types_utilisateurs` (`vtu_id`, `vtu_type`) VALUES
(1, 'commercial'),
(2, 'sous-traitant distribution'),
(3, 'secrétaire'),
(4, 'chef de centre'),
(5, 'gérant'),
(6, 'développeur'),
(7, 'utilisateur ponctuel'),
(8, 'sous-traitant e-mailing'),
(9, 'sous-traitant divers');
EOT;

      $this->db->query($sqlInsert);
    }


    public function down() {

    }
}

// EOF