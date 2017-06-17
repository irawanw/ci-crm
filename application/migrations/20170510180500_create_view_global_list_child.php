<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_view_global_list_child extends CI_Migration 
{
    public function up() 
    {
        $sqlDrop = <<<'EOT'
DROP VIEW IF EXISTS `global_view_child`;
EOT;
      echo $this->db->query($sqlDrop);
        $sql = <<<'EOT'
CREATE SQL SECURITY DEFINER VIEW `global_view_child` AS
SELECT
  `tpj`.`pages_jaunes_child_id` AS `id`,
  `tpj`.`parent_id` AS `parent_id`,
  `tpj_p`.`software` AS `software_id`,
  `tpj`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `tpj`.`date_envoi` AS `date_envoi`,
  `tpj`.`date_limite_de_fin` AS `date_limite_de_fin`,
  '' AS `stats`,
  `tpj`.`quantite_envoyer` AS `quantite_envoyer`,
  `tpj`.`quantite_envoyee` AS `quantite_envoyee`,
  `tpj`.`openemm` AS `openemm`,
  '' AS `openemm_number_of_open`,
  '' AS `openemm_open_rate_pct`,
  '' AS `openemm_number_of_click`,
  '' AS `openemm_click_rate_pct`,
  `tpj`.`verification_number` AS `verification_number`,
  `tpj`.`number_sent_through` AS `number_sent_through`,
  `tpj`.`number_sent_mail` AS `number_sent_mail`,
  '' AS `deliv_sur_test_orange`,
  '' AS `deliv_sur_test_free`,
  '' AS `deliv_sur_test_sfr`,
  '' AS `deliv_sur_test_gmail`,
  '' AS `deliv_sur_test_microsoft`,
  '' AS `deliv_sur_test_yahoo`,
  '' AS `deliv_sur_test_ovh`,
  '' AS `deliv_sur_test_oneandone`,
  '' AS `deliv_reelle_bounce`,
  '' AS `deliv_reelle_bounce_percentage_pct`,
  '' AS `deliv_reelle_hard_bounce_rate_pct`,
  '' AS `deliv_reelle_soft_bounce_rate_pct`,
  '' AS `deliv_reelle_orange`,
  '' AS `deliv_reelle_free`,
  '' AS `deliv_reelle_sfr`,
  '' AS `deliv_reelle_gmail`,
  '' AS `deliv_reelle_microsoft`,
  '' AS `deliv_reelle_yahoo`,
  '' AS `deliv_reelle_ovh`,
  '' AS `deliv_reelle_oneandone`,
  `tem`.`emp_nom` AS `operateur_qui_envoie`,
  `tpj`.`number_sent` AS `number_sent`,
  '' AS `physical_server`,
  '' AS `provider`,
  '' AS `ip`,
  '' AS `smtp`,
  '' AS `rotation`,
  '' AS `domain`,
  '' AS `computer`,
  '' AS `manual_sender`,
  '' AS `manual_sender_domain`,
  `tpm`.`mail` AS `copy_mail`,
  '' AS `speed_hours`,
  '' AS `number_hours`,
  `tpj`.`inactive` AS `inactive`,
  `tpj`.`deleted` AS `deleted`
FROM
  (
    (
      (
        `t_pages_jaunes_child` `tpj`
      LEFT JOIN
        `t_employes` `tem` ON(
          (
            `tpj`.`operateur_qui_envoie` = `tem`.`emp_id`
          )
        )
      )
    LEFT JOIN
      `t_production_mails` `tpm` ON(
        (
          `tpj`.`copy_mail` = `tpm`.`production_mails_id`
        )
      )
    )
    LEFT JOIN
      `t_pages_jaunes` `tpj_p` ON(
        (
          `tpj`.`parent_id` = `tpj_p`.`pages_jaunes_id`
        ) 
      )
  )
UNION
SELECT
  `tmb`.`max_bulk_child_id` AS `id`,
  `tmb`.`parent_id` AS `parent_id`,
  `tmb_p`.`software` AS `software_id`,
  `tmb`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `tmb`.`date_envoi` AS `date_envoi`,
  `tmb`.`date_limite_de_fin` AS `date_limite_de_fin`,
  '' AS `stats`,
  `tmb`.`quantite_envoyer` AS `quantite_envoyer`,
  `tmb`.`quantite_envoyee` AS `quantite_envoyee`,
  `tmb`.`openemm` AS `openemm`,
  '' AS `openemm_number_of_open`,
  '' AS `openemm_open_rate_pct`,
  '' AS `openemm_number_of_click`,
  '' AS `openemm_click_rate_pct`,
  `tmb`.`verification_number` AS `verification_number`,
  '' AS `number_sent_through`,
  '' AS `number_sent_mail`,
  `tmb`.`deliv_sur_test_orange` AS `deliv_sur_test_orange`,
  `tmb`.`deliv_sur_test_free` AS `deliv_sur_test_free`,
  `tmb`.`deliv_sur_test_sfr` AS `deliv_sur_test_sfr`,
  `tmb`.`deliv_sur_test_gmail` AS `deliv_sur_test_gmail`,
  `tmb`.`deliv_sur_test_microsoft` AS `deliv_sur_test_microsoft`,
  `tmb`.`deliv_sur_test_yahoo` AS `deliv_sur_test_yahoo`,
  `tmb`.`deliv_sur_test_ovh` AS `deliv_sur_test_ovh`,
  `tmb`.`deliv_sur_test_oneandone` AS `deliv_sur_test_oneandone`,
  '' AS `deliv_reelle_bounce`,
  '' AS `deliv_reelle_bounce_percentage_pct`,
  '' AS `deliv_reelle_hard_bounce_rate_pct`,
  '' AS `deliv_reelle_soft_bounce_rate_pct`,
  '' AS `deliv_reelle_orange`,
  '' AS `deliv_reelle_free`,
  '' AS `deliv_reelle_sfr`,
  '' AS `deliv_reelle_gmail`,
  '' AS `deliv_reelle_microsoft`,
  '' AS `deliv_reelle_yahoo`,
  '' AS `deliv_reelle_ovh`,
  '' AS `deliv_reelle_oneandone`,
  `tem`.`emp_nom` AS `operateur_qui_envoie`,
  `tmb`.`number_sent` AS `number_sent`,
  `tmb`.`physical_server` AS `physical_server`,
  '' AS `provider`,
  '' AS `ip`,
  `tmb`.`smtp` AS `smtp`,
  '' AS `rotation`,
  '' AS `domain`,
  `tmb`.`computer` AS `computer`,
  '' AS `manual_sender`,
  '' AS `manual_sender_domain`,
  '' AS `copy_mail`,
  '' AS `speed_hours`,
  '' AS `number_hours`,
  `tmb`.`inactive` AS `inactive`,
  `tmb`.`deleted` AS `deleted`
FROM
  (
    (
      `t_max_bulk_child` `tmb`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `tmb`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
      `t_max_bulk` `tmb_p` ON(
        (
          `tmb`.`parent_id` = `tmb_p`.`max_bulk_id`
        ) 
      )
  )
UNION
SELECT
  `tms`.`manual_sending_child_id` AS `id`,
  `tms`.`parent_id` AS `parent_id`,
  `tms_p`.`software` AS `software_id`,
  `tms`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `tms`.`date_envoi` AS `date_envoi`,
  `tms`.`date_limite_de_fin` AS `date_limite_de_fin`,
  '' AS `stats`,
  `tms`.`quantite_envoyer` AS `quantite_envoyer`,
  `tms`.`quantite_envoyee` AS `quantite_envoyee`,
  `tms`.`openemm` AS `openemm`,
  '' AS `openemm_number_of_open`,
  '' AS `openemm_open_rate_pct`,
  '' AS `openemm_number_of_click`,
  '' AS `openemm_click_rate_pct`,
  `tms`.`verification_number` AS `verification_number`,
  '' AS `number_sent_through`,
  '' AS `number_sent_mail`,
  `tms`.`deliv_sur_test_orange` AS `deliv_sur_test_orange`,
  `tms`.`deliv_sur_test_free` AS `deliv_sur_test_free`,
  `tms`.`deliv_sur_test_sfr` AS `deliv_sur_test_sfr`,
  `tms`.`deliv_sur_test_gmail` AS `deliv_sur_test_gmail`,
  `tms`.`deliv_sur_test_microsoft` AS `deliv_sur_test_microsoft`,
  `tms`.`deliv_sur_test_yahoo` AS `deliv_sur_test_yahoo`,
  `tms`.`deliv_sur_test_ovh` AS `deliv_sur_test_ovh`,
  `tms`.`deliv_sur_test_oneandone` AS `deliv_sur_test_oneandone`,
  '' AS `deliv_reelle_bounce`,
  '' AS `deliv_reelle_bounce_percentage_pct`,
  '' AS `deliv_reelle_hard_bounce_rate_pct`,
  '' AS `deliv_reelle_soft_bounce_rate_pct`,
  '' AS `deliv_reelle_orange`,
  '' AS `deliv_reelle_free`,
  '' AS `deliv_reelle_sfr`,
  '' AS `deliv_reelle_gmail`,
  '' AS `deliv_reelle_microsoft`,
  '' AS `deliv_reelle_yahoo`,
  '' AS `deliv_reelle_ovh`,
  '' AS `deliv_reelle_oneandone`,
  `tem`.`emp_nom` AS `operateur_qui_envoie`,
  `tms`.`number_sent` AS `number_sent`,
  '' AS `physical_server`,
  '' AS `provider`,
  '' AS `ip`,
  '' AS `smtp`,
  '' AS `rotation`,
  '' AS `domain`,
  '' AS `computer`,
  `tpm`.`mail` AS `manual_sender`,
  `tpm`.`domain` AS `manual_sender_domain`,
  '' AS `copy_mail`,
  `tms`.`speed_hours` AS `speed_hours`,
  `tms`.`number_hours` AS `number_hours`,
  `tms`.`inactive` AS `inactive`,
  `tms`.`deleted` AS `deleted`
FROM
  (
    (
      (
        `t_manual_sending_child` `tms`
      LEFT JOIN
        `t_employes` `tem` ON(
          (
            `tms`.`operateur_qui_envoie` = `tem`.`emp_id`
          )
        )
      )
    LEFT JOIN
      `t_production_mails` `tpm` ON(
        (
          `tms`.`manual_sender` = `tpm`.`production_mails_id`
        )
      )
    )
  LEFT JOIN
    `t_manual_sending` `tms_p` ON(
      (
        `tms`.`parent_id` = `tms_p`.`manual_sending_id`
      ) 
    )
  )
UNION
SELECT
  `top`.`openemm_child_id` AS `id`,
  `top`.`parent_id` AS `parent_id`,
  `top_p`.`software` AS `software_id`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`date_limite_de_fin` AS `date_limite_de_fin`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyer` AS `quantite_envoyer`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`openemm` AS `openemm`,
  '' AS `openemm_number_of_open`,
  '' AS `openemm_open_rate_pct`,
  '' AS `openemm_number_of_click`,
  '' AS `openemm_click_rate_pct`,
  '' AS `verification_number`,
  '' AS `number_sent_through`,
  '' AS `number_sent_mail`,
  `top`.`deliv_sur_test_orange` AS `deliv_sur_test_orange`,
  `top`.`deliv_sur_test_free` AS `deliv_sur_test_free`,
  `top`.`deliv_sur_test_sfr` AS `deliv_sur_test_sfr`,
  `top`.`deliv_sur_test_gmail` AS `deliv_sur_test_gmail`,
  `top`.`deliv_sur_test_microsoft` AS `deliv_sur_test_microsoft`,
  `top`.`deliv_sur_test_yahoo` AS `deliv_sur_test_yahoo`,
  `top`.`deliv_sur_test_ovh` AS `deliv_sur_test_ovh`,
  `top`.`deliv_sur_test_oneandone` AS `deliv_sur_test_oneandone`,
  '' AS `deliv_reelle_bounce`,
  '' AS `deliv_reelle_bounce_percentage_pct`,
  '' AS `deliv_reelle_hard_bounce_rate_pct`,
  '' AS `deliv_reelle_soft_bounce_rate_pct`,
  '' AS `deliv_reelle_orange`,
  '' AS `deliv_reelle_free`,
  '' AS `deliv_reelle_sfr`,
  '' AS `deliv_reelle_gmail`,
  '' AS `deliv_reelle_microsoft`,
  '' AS `deliv_reelle_yahoo`,
  '' AS `deliv_reelle_ovh`,
  '' AS `deliv_reelle_oneandone`,
  `tem`.`emp_nom` AS `operateur_qui_envoie`,
  '' AS `number_sent`,
  `top`.`physical_server` AS `physical_server`,
  '' AS `provider`,
  '' AS `ip`,
  `top`.`smtp` AS `smtp`,
  `top`.`rotation` AS `rotation`,
  '' AS `domain`,
  '' AS `computer`,
  '' AS `manual_sender`,
  '' AS `manual_sender_domain`,
  '' AS `copy_mail`,
  '' AS `speed_hours`,
  '' AS `number_hours`,
  `top`.`inactive` AS `inactive`,
  `top`.`deleted` AS `deleted`
FROM
  (
    (
      `t_openemm_child` `top`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `top`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_openemm` `top_p` ON(
      (
        `top`.`parent_id` = `top_p`.`openemm_id`
      ) 
    )
);
EOT;
    
    $this->db->query($sql);
    
  }

    public function down() {
        $sql = <<<'EOT'
DROP VIEW IF EXISTS `global_view_child`;
EOT;
        $this->db->query($sql);
    }
}

// EOF
