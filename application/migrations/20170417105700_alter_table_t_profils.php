<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_table_t_profils extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
ALTER TABLE `t_profils` CHANGE `prf_nom` `prf_nom` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', ADD  `prf_description` VARCHAR( 255 ) NOT NULL AFTER  `prf_nom` ;
EOT;
      echo $this->db->query($sql);

      $sqlTruncate = <<<'EOT'
TRUNCATE `t_profils`;
EOT;
      echo $this->db->query($sqlTruncate);

      $sqlInsert = <<<'EOT'
INSERT INTO `t_profils` (`prf_id`, `prf_nom`,`prf_description`) VALUES
(1, 'Administrateur', 'administrateur'),
(2, 'Commercial','utilisateur avec pouvoirs illimités'),
(3, 'Droits limités', 'utilisateur avec pouvoirs définis'),
(4, 'Aucuns droits', 'utilisateurs sans pouvoir de modification');
EOT;

      $this->db->query($sqlInsert);
    }


    public function down() {
        $sql = <<<'EOT'
ALTER TABLE `t_profils` CHANGE `prf_nom` `prf_nom` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', DROP `prf_description`;
EOT;
        $this->db->query($sql);
    }
}

// EOF