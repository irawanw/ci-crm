<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_forge $forge
 * @property CI_DB_query_builder $db
 */
class Migration_insert_into_t_reglements_for_le_colporteur_factures extends CI_Migration {

    public function up() {
        $this->db->trans_start();

        $q = $this->db->select('MAX(fac_date) AS fac_date_max, scv_id')
            ->join('t_commandes', 'fac_commande = cmd_id', 'inner')
            ->join('t_devis', 'cmd_devis = dvi_id', 'inner')
            ->join('t_societes_vendeuses', 'dvi_societe_vendeuse = scv_id', 'inner')
            ->where('fac_inactif IS NULL')
            ->where('dvi_inactif IS NULL')
            ->where('cmd_inactif IS NULL')
            ->where_in('scv_nom', array('Le Colporteur', 'PUBLIMAIL'))
            ->group_by('scv_id')
            ->get('t_factures');
        $rgl_date = array();
        foreach ($q->result() as $row) {
            $rgl_date[$row->scv_id] = $row->fac_date_max;
        }

        $q = $this->db->select('dvi_client, dvi_societe_vendeuse, SUM(fac_reste) AS fac_reste_total')
            ->join('t_commandes', 'cmd_devis = dvi_id', 'inner')
            ->join('t_factures', 'fac_commande = cmd_id', 'inner')
            ->join('t_societes_vendeuses', 'dvi_societe_vendeuse = scv_id', 'inner')
            ->where('fac_inactif IS NULL')
            ->where('dvi_inactif IS NULL')
            ->where('cmd_inactif IS NULL')
            ->where('fac_reste > 0')
            ->where('fac_reprise', 1)
            ->where('fac_etat', 2)
            ->where_in('scv_nom', array('Le Colporteur', 'PUBLIMAIL'))
            ->group_by(array('dvi_client', 'dvi_societe_vendeuse'))
            ->get('t_devis');

        foreach ($q->result() as $row) {
            if (!isset($rgl_date[$row->dvi_societe_vendeuse])) {
                $rgl_date[$row->dvi_societe_vendeuse] = date('Y-m-d');
            }
            $data = array(
                'rgl_date' => $rgl_date[$row->dvi_societe_vendeuse],
                'rgl_montant' => $row->fac_reste_total,
                'rgl_type' => 1,   // Chèque (see `v_types_reglements` table)
                'rgl_cheque' => 'Reprise du '.date('Y-m-d'),
                'rgl_banque' => 'Reprise',
                'rgl_client' => $row->dvi_client,
                'rgl_reference' => 'Reprise',
                'rgl_archiver' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('t_reglements', $data);

            $rgl_id = $this->db->insert_id();

            // Les réglèments ne sont pas typés par société vendeuse, il faut
            // donc récupérer les factures qui correspondent au règlement créé,
            // sinon on a des problèmes avec des clients qui ont des factures
            // reprises avec plusieurs enseignes.
            $sql = '
INSERT INTO `t_imputations`
(`ipu_montant`, `ipu_reglement`, `ipu_facture`, `ipu_avoir`, `ipu_profits`)
SELECT `fac_reste`, `rgl_id`, `fac_id`, 0, 0
FROM `t_factures`
    INNER JOIN `t_commandes` ON (`fac_commande` = `cmd_id`)
    INNER JOIN `t_devis` ON (`cmd_devis` = `dvi_id`)
    INNER JOIN `t_reglements` ON (`rgl_client` = `dvi_client` AND `rgl_montant` >= `fac_reste`)
WHERE `rgl_banque` = "Reprise"
  AND `rgl_type` = 1
  AND `fac_reprise` = 1
  AND `fac_etat` = 2
  AND `fac_reste` > 0
  AND `dvi_societe_vendeuse` = '.$row->dvi_societe_vendeuse.'
  AND `dvi_client` = '.$row->dvi_client.'
  AND `rgl_id` = '.$rgl_id;

            $this->db->query($sql);
        }

        // Set to zero the amount due for factures we just processed
        $sql = '
UPDATE `t_factures`
    INNER JOIN `t_imputations` ON (`ipu_facture` = `fac_id` AND `ipu_montant` = `fac_reste`)
    INNER JOIN `t_reglements` ON (`ipu_reglement` = `rgl_id`)
SET `fac_regle` = `fac_montant_ttc`,
  `fac_reste` = 0.0
WHERE `rgl_banque` = "Reprise"
  AND `rgl_type` = 1
  AND `fac_reprise` = 1
  AND `fac_etat` = 2
  AND `fac_reste` > 0
  AND `ipu_avoir` = 0
  AND `ipu_profits` = 0
';
        $this->db->query($sql);

        // Set to sero the amount due for factures that have a negative due amount
        $sql = '
UPDATE `t_factures`
    INNER JOIN `t_commandes` ON (`fac_commande` = `cmd_id`)
    INNER JOIN `t_devis` ON (`cmd_devis` = `dvi_id`)
    INNER JOIN `t_societes_vendeuses` ON (`dvi_societe_vendeuse` = `scv_id`)
SET `fac_reste` = 0.0
WHERE `fac_reprise` = 1
  AND `fac_etat` = 2
  AND `fac_reste` < 0
  AND `fac_inactif` IS NULL
  AND `dvi_inactif` IS NULL
  AND `cmd_inactif` IS NULL
  AND `scv_nom` IN ("Le Colporteur", "PUBLIMAIL")
';
        $this->db->query($sql);

        $this->db->trans_commit();
    }

    public function down() {
        // Can't undo
    }

}
