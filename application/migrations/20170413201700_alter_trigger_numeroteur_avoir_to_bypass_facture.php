<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_Forge $dbforge
 */
class Migration_Alter_trigger_numeroteur_avoir_to_bypass_facture extends CI_Migration {

    public function up() {
        $sql = 'DROP TRIGGER `numeroteur_avoir`';
        $this->db->query($sql);

        $sql = '
CREATE TRIGGER `numeroteur_avoir` BEFORE INSERT ON `t_avoirs` FOR EACH ROW BEGIN
    SELECT scv_no_avoir, scv_format_avoir
    INTO @numero, @format
    FROM t_societes_vendeuses
    WHERE scv_id = NEW.avr_societe_vendeuse;
    
    SET NEW.avr_numero = @numero + 1;
    
    UPDATE t_societes_vendeuses
    SET scv_no_avoir = NEW.avr_numero
    WHERE scv_id = NEW.avr_societe_vendeuse;
    
    SET NEW.avr_reference = REPLACE (REPLACE(@format,\'%\',LPAD(NEW.avr_numero,4,\'0\')),\'#\',DATE_FORMAT(NOW(),\'%Y\'));
END
';
        $this->db->query($sql);
    }

    public function down() {
        $sql = 'DROP TRIGGER IF EXISTS `numeroteur_avoir`';
        $this->db->query($sql);

        $sql = '
CREATE TRIGGER `numeroteur_avoir` BEFORE INSERT ON `t_avoirs` FOR EACH ROW BEGIN
    SET @commande = (SELECT fac_commande FROM t_factures WHERE fac_id=NEW.avr_facture);
    SET @devis = (SELECT cmd_devis FROM t_commandes WHERE cmd_id=@commande);
    SET @enseigne = (SELECT dvi_societe_vendeuse FROM t_devis WHERE dvi_id=@devis);
    SELECT scv_no_avoir,scv_format_avoir INTO @numero,@format FROM t_societes_vendeuses WHERE scv_id=@enseigne;
    SET NEW.avr_numero = @numero+1;
    UPDATE t_societes_vendeuses SET scv_no_avoir=NEW.avr_numero WHERE scv_id=@enseigne;
    SET NEW.avr_reference = REPLACE (REPLACE(@format,\'%\',LPAD(NEW.avr_numero,4,\'0\')),\'#\',DATE_FORMAT(NOW(),\'%Y\'));
END
';
        $this->db->query($sql);
    }
}

// EOF