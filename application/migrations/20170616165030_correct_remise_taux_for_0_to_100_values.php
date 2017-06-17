<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_Correct_remise_taux_for_0_to_100_values extends CI_Migration {

    public function up() {

        $sql = '
UPDATE t_articles_devis
SET ard_remise_taux = ROUND(ard_remise_ht / ard_prix / ard_quantite * 100, 2)
WHERE ard_remise_taux <> 0
      AND ard_quantite > 0 AND ard_prix <> 0
      AND ard_remise_ht / ard_prix / ard_quantite * 100 <= 100;
';
        $this->db->query($sql);

        $sql = '
UPDATE t_lignes_factures
SET lif_remise_taux = ROUND(lif_remise_ht / lif_prix / lif_quantite * 100, 2)
WHERE lif_remise_taux <> 0
      AND lif_quantite > 0 AND lif_prix <> 0
      AND lif_remise_ht / lif_prix / lif_quantite * 100 <= 100;
';
        $this->db->query($sql);

        $sql = '
UPDATE t_lignes_avoirs
SET lia_remise_taux = ROUND(lia_remise_ht / lia_prix / lia_quantite * 100, 2)
WHERE lia_remise_taux <> 0
      AND lia_quantite > 0 AND lia_prix <> 0
      AND lia_remise_ht / lia_prix / lia_quantite * 100 <= 100;
';
        $this->db->query($sql);
    }

    public function down() {
        // Do nothing
    }
}

// EOF