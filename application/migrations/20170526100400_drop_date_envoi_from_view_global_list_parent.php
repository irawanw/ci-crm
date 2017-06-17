<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_date_envoi_from_view_global_list_parent extends CI_Migration {

	public function up() 
    {
       $sqlDrop = <<<'EOT'
DROP VIEW IF EXISTS `global_view_parent`;
EOT;
      echo $this->db->query($sqlDrop);
      
        $sql = <<<'EOT'
CREATE VIEW `global_view_parent` AS
SELECT
  `tpj`.`pages_jaunes_id` AS `id`,
  `tpj`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tpj`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_type`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tpj`.`segment_numero` AS `segment_numero`,  
  `tpj`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tpj`.`quantite_envoyer` AS `quantite_envoyer`,
  `tpj`.`inactive` AS `inactive`,
  `tpj`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_pages_jaunes` `tpj`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tpj`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tpj`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tpj`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tpj`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tpj`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `tms`.`manual_sending_id` AS `id`,
  `tms`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tms`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tms`.`segment_numero` AS `segment_numero`,  
  `tms`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tms`.`quantite_envoyer` AS `quantite_envoyer`,
  `tms`.`inactive` AS `inactive`,
  `tms`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_manual_sending` `tms`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tms`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tms`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tms`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tms`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tms`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `tmb`.`max_bulk_id` AS `id`,
  `tmb`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tmb`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tmb`.`segment_numero` AS `segment_numero`,  
  `tmb`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tmb`.`quantite_envoyer` AS `quantite_envoyer`,
  `tmb`.`inactive` AS `inactive`,
  `tmb`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_max_bulk` `tmb`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tmb`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tmb`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tmb`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tmb`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tmb`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `toe`.`openemm_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,  
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_openemm` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
  
UNION
SELECT
  `toe`.`sendgrid_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,  
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_sendgrid` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )  
UNION
SELECT
  `toe`.`sendinblue_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,  
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_sendinblue` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )


UNION
SELECT
  `toe`.`mailchimp_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,  
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_mailchimp` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
  
UNION
SELECT
  `toe`.`airmail_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,  
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_airmail` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `teml`.`emailing_id` AS `id`,
  `teml`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `teml`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `teml`.`segment_numero` AS `segment_numero`,  
  `teml`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `teml`.`quantite_envoyer` AS `quantite_envoyer`,
  `teml`.`inactive` AS `inactive`,
  `teml`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_emailing` `teml`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`teml`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`teml`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `teml`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `teml`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`teml`.`software` = `tsw`.`software_id`)
      )
    )
  );
EOT;
    
    $this->db->query($sql);
    
  }

	public function down() {
		$sqlDrop = <<<'EOT'
DROP VIEW IF EXISTS `global_view_parent`;
EOT;
		echo $this->db->query($sqlDrop);
      
/*	  
        $sql = <<<'EOT'
CREATE VIEW `global_view_parent` AS
SELECT
  `tpj`.`pages_jaunes_id` AS `id`,
  `tpj`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tpj`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_type`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tpj`.`segment_numero` AS `segment_numero`,
  `tpj`.`date_envoi` AS `date_envoi`,
  `tpj`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tpj`.`quantite_envoyer` AS `quantite_envoyer`,
  `tpj`.`inactive` AS `inactive`,
  `tpj`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_pages_jaunes` `tpj`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tpj`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tpj`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tpj`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tpj`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tpj`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `tms`.`manual_sending_id` AS `id`,
  `tms`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tms`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tms`.`segment_numero` AS `segment_numero`,
  `tms`.`date_envoi` AS `date_envoi`,
  `tms`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tms`.`quantite_envoyer` AS `quantite_envoyer`,
  `tms`.`inactive` AS `inactive`,
  `tms`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_manual_sending` `tms`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tms`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tms`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tms`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tms`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tms`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `tmb`.`max_bulk_id` AS `id`,
  `tmb`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `tmb`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `tmb`.`segment_numero` AS `segment_numero`,
  `tmb`.`date_envoi` AS `date_envoi`,
  `tmb`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `tmb`.`quantite_envoyer` AS `quantite_envoyer`,
  `tmb`.`inactive` AS `inactive`,
  `tmb`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_max_bulk` `tmb`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`tmb`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`tmb`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `tmb`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `tmb`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`tmb`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `toe`.`openemm_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,
  `toe`.`date_envoi` AS `date_envoi`,
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_openemm` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
  
UNION
SELECT
  `toe`.`sendgrid_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,
  `toe`.`date_envoi` AS `date_envoi`,
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_sendgrid` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )  
UNION
SELECT
  `toe`.`sendinblue_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,
  `toe`.`date_envoi` AS `date_envoi`,
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_sendinblue` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )


UNION
SELECT
  `toe`.`mailchimp_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,
  `toe`.`date_envoi` AS `date_envoi`,
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_mailchimp` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
  
UNION
SELECT
  `toe`.`airmail_id` AS `id`,
  `toe`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `toe`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `toe`.`segment_numero` AS `segment_numero`,
  `toe`.`date_envoi` AS `date_envoi`,
  `toe`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `toe`.`quantite_envoyer` AS `quantite_envoyer`,
  `toe`.`inactive` AS `inactive`,
  `toe`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_airmail` `toe`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`toe`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`toe`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `toe`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `toe`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`toe`.`software` = `tsw`.`software_id`)
      )
    )
  )
UNION
SELECT
  `teml`.`emailing_id` AS `id`,
  `teml`.`software` AS `software_id`,
  `tsw`.`software_nom` AS `software_name`,
  `teml`.`client` AS `client_id`,
  `tc`.`ctc_nom` AS `client_name`,
  `tcm`.`cmd_id` AS `cmd_id`,
  `tcm`.`cmd_reference` AS `cmd_name`,
  `fcv`.`facture_id` AS `facture_id`,
  `fcv`.`facture_name` AS `facture_name`,
  (
    `fcv`.`total_ht` *(1 - `fcv`.`remise`)
  ) AS `ht`,
  `tml`.`message_list_id` AS `message_id`,
  `tml`.`name` AS `message_name`,
  `tml`.`message` AS `message_view`,
  `tml`.`lien_pour_telecharger` AS `message_lien`,
  `tml`.`object` AS `message_object`,
  `tml`.`type` AS `message_lien`,
  `vf`.`vfm_famille` AS `message_famille`,
  `tsv`.`scv_nom` AS `message_societe`,
  `te1`.`emp_nom` AS `message_commercial`,
  `tml`.`email` AS `message_email`,
  `tml`.`telephone` AS `message_telephone`,
  `teml`.`segment_numero` AS `segment_numero`,
  `teml`.`date_envoi` AS `date_envoi`,
  `teml`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `teml`.`quantite_envoyer` AS `quantite_envoyer`,
  `teml`.`inactive` AS `inactive`,
  `teml`.`deleted` AS `deleted`
FROM
  (
    (
      (
        (
          (
            (
              (
                (
                  (
                    `t_emailing` `teml`
                  LEFT JOIN
                    `t_contacts` `tc` ON((`teml`.`client` = `tc`.`ctc_id`))
                  )
                LEFT JOIN
                  `t_commandes` `tcm` ON((`teml`.`commande` = `tcm`.`cmd_id`))
                )
              LEFT JOIN
                `factures_view` `fcv` ON(
                  (
                    `teml`.`commande` = `fcv`.`fac_commande`
                  )
                )
              )
            LEFT JOIN
              `t_message_list` `tml` ON(
                (
                  `teml`.`message` = `tml`.`message_list_id`
                )
              )
            )
          LEFT JOIN
            `t_societes_vendeuses` `tsv` ON((`tsv`.`scv_id` = `tml`.`societe`))
          )
        LEFT JOIN
          `v_familles` `vf` ON(
            (
              `vf`.`vfm_id` = `tml`.`famille_darticles`
            )
          )
        )
      LEFT JOIN
        `t_employes` `te1` ON((`te1`.`emp_id` = `tml`.`salesman`))
      )
    LEFT JOIN
      `t_softwares` `tsw` ON(
        (`teml`.`software` = `tsw`.`software_id`)
      )
    )
  );
EOT;
        $this->db->query($sql);
		*/
	}
}

/* End of file 20170518053000_recreate_view_global_list_parent.php */
/* Location: .//tmp/fz3temp-1/20170518053000_recreate_view_global_list_parent.php */