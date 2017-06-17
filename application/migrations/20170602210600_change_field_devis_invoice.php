<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_field_devis_invoice extends CI_Migration {

	public function up() {
		$sql = <<<'EOT'
ALTER TABLE  `t_avoirs` CHANGE  `avr_montant_ttc`  `avr_montant_ttc` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql);
        $sql1 = <<<'EOT'
ALTER TABLE  `t_articles_devis` CHANGE  `ard_prix`  `ard_prix` DECIMAL( 10, 4 ) NOT NULL DEFAULT  '0.0000',
CHANGE  `ard_remise_ht`  `ard_remise_ht` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `ard_remise_ttc`  `ard_remise_ttc` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql1);
        $sql2 = <<<'EOT'
UPDATE IGNORE `t_devis` SET  `dvi_rappel` =  '2020-02-02 00:00:00' WHERE  `t_devis`.`dvi_rappel` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql2);
        $sql3 = <<<'EOT'
ALTER TABLE  `t_devis` CHANGE  `dvi_montant_htnr`  `dvi_montant_htnr` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `dvi_montant_ht`  `dvi_montant_ht` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `dvi_montant_ttc`  `dvi_montant_ttc` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql3);
        $sql4 = <<<'EOT'
UPDATE IGNORE `t_devis` SET  `dvi_rappel` =  '0000-00-00 00:00:00' WHERE  `t_devis`.`dvi_rappel` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql4);
        $sql5 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_rappel` =  '2020-02-02 00:00:00' WHERE  `fac_rappel` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql5);
		$sql6 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_inactif` =  '2020-02-02 00:00:00' WHERE  `fac_inactif` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql6);
        $sql7 = <<<'EOT'
ALTER TABLE  `t_factures` CHANGE  `fac_montant_htnr`  `fac_montant_htnr` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_ht`  `fac_montant_ht` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_tva`  `fac_montant_tva` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_ttc`  `fac_montant_ttc` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_regle`  `fac_regle` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_reste`  `fac_reste` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql7);
        $sql8 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_rappel` =  '0000-00-00 00:00:00' WHERE  `fac_rappel` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql8);
        $sql9 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_inactif` =  '0000-00-00 00:00:00' WHERE  `fac_inactif` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql9);
        $sql10 = <<<'EOT'
ALTER TABLE  `t_imputations` CHANGE  `ipu_montant`  `ipu_montant` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql10);
        $sql11 = <<<'EOT'
ALTER TABLE  `t_lignes_factures` CHANGE  `lif_prix`  `lif_prix` DECIMAL( 10, 4 ) NOT NULL DEFAULT  '0.000',
CHANGE  `lif_remise_ht`  `lif_remise_ht` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `lif_remise_ttc`  `lif_remise_ttc` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql11);
        $sql12 = <<<'EOT'
UPDATE IGNORE `t_reglements` SET  `rgl_date` =  '2020-02-02' WHERE  `rgl_date` ='0000-00-00';
EOT;
        $this->db->query($sql12);
        $sql13 = <<<'EOT'
ALTER TABLE  `t_reglements` CHANGE  `rgl_montant`  `rgl_montant` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql13);
        $sql14 = <<<'EOT'
UPDATE IGNORE `t_reglements` SET  `rgl_date` =  '0000-00-00' WHERE  `rgl_date` ='2020-02-02';
EOT;
        $this->db->query($sql14);
	}

	public function down() {
		/*
		$sql = <<<'EOT'
ALTER TABLE  `t_avoirs` CHANGE  `avr_montant_ttc`  `avr_montant_ttc` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql);
        $sql1 = <<<'EOT'
ALTER TABLE  `t_articles_devis` CHANGE  `ard_prix`  `ard_prix` DECIMAL( 10, 4 ) NOT NULL DEFAULT  '0.0000',
CHANGE  `ard_remise_ht`  `ard_remise_ht` DECIMAL( 6, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `ard_remise_ttc`  `ard_remise_ttc` DECIMAL( 6, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql1);
        $sql2 = <<<'EOT'
UPDATE IGNORE `t_devis` SET  `dvi_rappel` =  '2020-02-02 00:00:00' WHERE  `t_devis`.`dvi_rappel` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql2);
        $sql3 = <<<'EOT'
ALTER TABLE  `t_devis` CHANGE  `dvi_montant_htnr`  `dvi_montant_htnr` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `dvi_montant_ht`  `dvi_montant_ht` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `dvi_montant_ttc`  `dvi_montant_ttc` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql3);
        $sql4 = <<<'EOT'
UPDATE IGNORE `t_devis` SET  `dvi_rappel` =  '0000-00-00 00:00:00' WHERE  `t_devis`.`dvi_rappel` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql4);
        $sql5 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_rappel` =  '2020-02-02 00:00:00' WHERE  `fac_rappel` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql5);
		$sql6 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_inactif` =  '2020-02-02 00:00:00' WHERE  `fac_inactif` ='0000-00-00 00:00:00';
EOT;
        $this->db->query($sql6);
        $sql7 = <<<'EOT'
ALTER TABLE  `t_factures` CHANGE  `fac_montant_htnr`  `fac_montant_htnr` DECIMAL( 9, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_ht`  `fac_montant_ht` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_tva`  `fac_montant_tva` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_montant_ttc`  `fac_montant_ttc` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_regle`  `fac_regle` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `fac_reste`  `fac_reste` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql7);
        $sql8 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_rappel` =  '0000-00-00 00:00:00' WHERE  `fac_rappel` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql8);
        $sql9 = <<<'EOT'
UPDATE IGNORE `t_factures` SET  `fac_inactif` =  '0000-00-00 00:00:00' WHERE  `fac_inactif` ='2020-02-02 00:00:00';
EOT;
        $this->db->query($sql9);
        $sql10 = <<<'EOT'
ALTER TABLE  `t_imputations` CHANGE  `ipu_montant`  `ipu_montant` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql10);
        $sql11 = <<<'EOT'
ALTER TABLE  `t_lignes_factures` CHANGE  `lif_prix`  `lif_prix` DECIMAL( 7, 3 ) NOT NULL DEFAULT  '0.000',
CHANGE  `lif_remise_ht`  `lif_remise_ht` DECIMAL( 6, 2 ) NOT NULL DEFAULT  '0.00',
CHANGE  `lif_remise_ttc`  `lif_remise_ttc` DECIMAL( 6, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql11);
        $sql12 = <<<'EOT'
UPDATE IGNORE `t_reglements` SET  `rgl_date` =  '2020-02-02' WHERE  `rgl_date` ='0000-00-00';
EOT;
        $this->db->query($sql12);
        $sql13 = <<<'EOT'
ALTER TABLE  `t_reglements` CHANGE  `rgl_montant`  `rgl_montant` DECIMAL( 8, 2 ) NOT NULL DEFAULT  '0.00';
EOT;
        $this->db->query($sql13);
        $sql14 = <<<'EOT'
UPDATE IGNORE `t_reglements` SET  `rgl_date` =  '0000-00-00' WHERE  `rgl_date` ='2020-02-02';
EOT;
        $this->db->query($sql14);
		*/
	}

}

/* End of file 20170602210600_change_field_devis_invoice.php */
/* Location: .//tmp/fz3temp-1/20170602210600_change_field_devis_invoice.php */