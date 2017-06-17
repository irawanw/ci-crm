<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_date_envoi_to_view_global_list_child extends CI_Migration {

	public function up() 
    {     
       $sqlDrop = <<<'EOT'
DROP VIEW IF EXISTS `global_view_child`;
EOT;

      echo $this->db->query($sqlDrop);
      
        $sql = <<<'EOT'
CREATE VIEW `global_view_child` AS
SELECT
  `tpj`.`pages_jaunes_child_id` AS `id`,
  `tpj`.`parent_id` AS `parent_id`,
  `tpj_p`.`software` AS `software_id`,
  `tpj`.`date_envoi` AS `date_envoi`,
  `tpj`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`, 
  '' AS `stats`, 
  `tpj`.`quantite_envoyee` AS `quantite_envoyee`,
  `tpj`.`open` AS `open`,
  `tpj`.`open_pourcentage` AS `open_pourcentage`,
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
  `tmb`.`date_envoi` AS `date_envoi`,
  `tmb`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  '' AS `stats`,
  `tmb`.`quantite_envoyee` AS `quantite_envoyee`,
  `tmb`.`open` AS `open`,
  `tmb`.`open_pourcentage` AS `open_pourcentage`,
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
  `tms`.`date_envoi` AS `date_envoi`,
  `tms`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  '' AS `stats`,
  `tms`.`quantite_envoyee` AS `quantite_envoyee`,
  `tms`.`open` AS `open`,
  `tms`.`open_pourcentage` AS `open_pourcentage`,
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
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`open` AS `open`,
  `top`.`open_pourcentage` AS `open_pourcentage`,
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
)
UNION
SELECT
  `top`.`sendgrid_child_id` AS `id`,
  `top`.`parent_id` AS `parent_id`,
  `top_p`.`software` AS `software_id`,
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`open` AS `open`,
  `top`.`open_pourcentage` AS `open_pourcentage`,
  `top`.`sendgrid` AS `sendgrid`,
  '' AS `sendgrid_number_of_open`,
  '' AS `sendgrid_open_rate_pct`,
  '' AS `sendgrid_number_of_click`,
  '' AS `sendgrid_click_rate_pct`,
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
      `t_sendgrid_child` `top`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `top`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_sendgrid` `top_p` ON(
      (
        `top`.`parent_id` = `top_p`.`sendgrid_id`
      ) 
    )
)
UNION
SELECT
  `top`.`sendinblue_child_id` AS `id`,
  `top`.`parent_id` AS `parent_id`,
  `top_p`.`software` AS `software_id`,
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`open` AS `open`,
  `top`.`open_pourcentage` AS `open_pourcentage`,
  `top`.`sendinblue` AS `sendinblue`,
  '' AS `sendinblue_number_of_open`,
  '' AS `sendinblue_open_rate_pct`,
  '' AS `sendinblue_number_of_click`,
  '' AS `sendinblue_click_rate_pct`,
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
      `t_sendinblue_child` `top`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `top`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_sendinblue` `top_p` ON(
      (
        `top`.`parent_id` = `top_p`.`sendinblue_id`
      ) 
    )
)
UNION
SELECT
  `top`.`mailchimp_child_id` AS `id`,
  `top`.`parent_id` AS `parent_id`,
  `top_p`.`software` AS `software_id`,
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`open` AS `open`,
  `top`.`open_pourcentage` AS `open_pourcentage`,
  '' AS `mailchimp`,
  '' AS `mailchimp_number_of_open`,
  '' AS `mailchimp_open_rate_pct`,
  '' AS `mailchimp_number_of_click`,
  '' AS `mailchimp_click_rate_pct`,
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
      `t_mailchimp_child` `top`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `top`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_mailchimp` `top_p` ON(
      (
        `top`.`parent_id` = `top_p`.`mailchimp_id`
      ) 
    )
)
UNION
SELECT
  `top`.`airmail_child_id` AS `id`,
  `top`.`parent_id` AS `parent_id`,
  `top_p`.`software` AS `software_id`,
  `top`.`date_envoi` AS `date_envoi`,
  `top`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `top`.`stats` AS `stats`,
  `top`.`quantite_envoyee` AS `quantite_envoyee`,
  `top`.`open` AS `open`,
  `top`.`open_pourcentage` AS `open_pourcentage`,
  '' AS `airmail`,
  '' AS `airmail_number_of_open`,
  '' AS `airmail_open_rate_pct`,
  '' AS `airmail_number_of_click`,
  '' AS `airmail_click_rate_pct`,
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
      `t_airmail_child` `top`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `top`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_airmail` `top_p` ON(
      (
        `top`.`parent_id` = `top_p`.`airmail_id`
      ) 
    )
)
UNION
SELECT
  `teml`.`emailing_child_id` AS `id`,
  `teml`.`parent_id` AS `parent_id`,
  `teml_p`.`software` AS `software_id`,
  `teml`.`date_envoi` AS `date_envoi`,
  `teml`.`segment_part` AS `segment_part`,
  '' AS `segment_nom`,
  '' AS `segment_first_critere`,
  '' AS `segment_second_critere`,
  '' AS `segment_many_criterias`,
  `teml`.`stats` AS `stats`,
  `teml`.`quantite_envoyee` AS `quantite_envoyee`,
  `teml`.`open` AS `open`,
  `teml`.`open_pourcentage` AS `open_pourcentage`,
  '' AS `openemm`,
  '' AS `openemm_number_of_open`,
  '' AS `openemm_open_rate_pct`,
  '' AS `openemm_number_of_click`,
  '' AS `openemm_click_rate_pct`,
  '' AS `verification_number`,
  '' AS `number_sent_through`,
  '' AS `number_sent_mail`,
  `teml`.`deliv_sur_test_orange` AS `deliv_sur_test_orange`,
  `teml`.`deliv_sur_test_free` AS `deliv_sur_test_free`,
  `teml`.`deliv_sur_test_sfr` AS `deliv_sur_test_sfr`,
  `teml`.`deliv_sur_test_gmail` AS `deliv_sur_test_gmail`,
  `teml`.`deliv_sur_test_microsoft` AS `deliv_sur_test_microsoft`,
  `teml`.`deliv_sur_test_yahoo` AS `deliv_sur_test_yahoo`,
  `teml`.`deliv_sur_test_ovh` AS `deliv_sur_test_ovh`,
  `teml`.`deliv_sur_test_oneandone` AS `deliv_sur_test_oneandone`,
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
  `teml`.`physical_server` AS `physical_server`,
  '' AS `provider`,
  '' AS `ip`,
  `teml`.`smtp` AS `smtp`,
  `teml`.`rotation` AS `rotation`,
  '' AS `domain`,
  '' AS `computer`,
  '' AS `manual_sender`,
  '' AS `manual_sender_domain`,
  '' AS `copy_mail`,
  '' AS `speed_hours`,
  '' AS `number_hours`,
  `teml`.`inactive` AS `inactive`,
  `teml`.`deleted` AS `deleted`
FROM
  (
    (
      `t_emailing_child` `teml`
    LEFT JOIN
      `t_employes` `tem` ON(
        (
          `teml`.`operateur_qui_envoie` = `tem`.`emp_id`
        )
      )
    )
  LEFT JOIN
    `t_emailing` `teml_p` ON(
      (
        `teml`.`parent_id` = `teml_p`.`emailing_id`
      ) 
    )
);
EOT;
    
    $this->db->query($sql);
    
  }

    public function down() {
      $sqlDrop = <<<'EOT'
DROP VIEW IF EXISTS `global_view_child`;
EOT;

      $this->db->query($sqlDrop);
	  
    }
}