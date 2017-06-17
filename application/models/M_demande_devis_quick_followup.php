<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_demande_devis_quick_followup extends MY_Model
{
    private $generales;

    public function __construct()
    {
        parent::__construct();
        $this->generales = array(
            'all' => '',
            '1'   => 'E-mailing',
            '2'   => 'Adwords',
        );
    }

    /******************************
     *
     ******************************/
    public function get_monthly_report($rangeDate = null)
    {
        echo $rangeDate;

        $data        = array();
        $month_param = $this->get_month_range($rangeDate);
        $generales   = $this->generales;

        foreach ($generales as $id => $val) {
            $data[$id] = $this->get_monthly_data($id, $month_param);
        }

        return $data;
    }

    /******************************
     *
     ******************************/
    public function get_weekly_report($rangeDate = null)
    {
        $data       = array();
        $week_param = $this->get_week_range($rangeDate);
        $generales  = $this->generales;

        foreach ($generales as $id => $val) {
            $data[$id] = $this->get_weekly_data($id, $week_param);
        }

        return $data;
    }

    protected function get_monthly_data($generale, $months)
    {
        $result         = array();
        $where_generale = "";

        if (count($months) > 0) {

            if ($generale != "all") {
                $where_generale .= "WHERE origine_group = $generale";
            }

            foreach ($months as $i => $monthVal) {
                $fields         = "";
                $data_per_month = array();
                $month          = $monthVal['m'];
                $year           = $monthVal['y'];
                $id             = $monthVal['id'];

                if ($where_generale == "") {
                    $where_month = "WHERE month(ctc_date_creation)=$month AND year(ctc_date_creation)=$year";
                } else {
                    $where_month = "AND month(ctc_date_creation)=$month AND year(ctc_date_creation)=$year";
                }

                $fields .= "count(distinct ctc_id) as nombre,";
                $fields .= "count(distinct if((fac_id != '' OR ctc_signe = 1),ctc_id, null)) as signe,";
                $fields .= "SUM(fac_montant_ht) as ca";

                $table = "(SELECT utl_id as commercial,
                $fields
                FROM `t_utilisateurs`
                LEFT JOIN `t_contacts` ON `ctc_commercial_charge` = `utl_id`
                LEFT JOIN `t_devis` ON `dvi_client`=`ctc_id`
                LEFT JOIN `t_commandes` ON `cmd_devis`=`dvi_id`
                LEFT JOIN `t_factures` ON `fac_commande`=`cmd_id` AND 
											`fac_etat` = 2 AND
											(`fac_inactif` IS NULL OR `fac_inactif` = '0000-00-00 00:00:00')
                LEFT JOIN `v_types_origine_prospect` `top` ON `top`.`origine_id` = `ctc_origine`
                LEFT JOIN `v_types_origine_generale` ON `generale_id`=`origine_group`
                $where_generale $where_month AND ctc_statistiques = 1
                GROUP BY `utl_id`)";

                $query      = $this->db->query($table);
                $all_ca     = 0;
                $all_signe  = 0;
                $all_nombre = 0;
                $all_taux   = 0;

                foreach ($query->result() as $row) {
                    $row->ca = ($row->ca == null) ? 0 : $row->ca;

                    if ($row->nombre > 0) {
                        $taux = round($row->signe / $row->nombre, 4) * 100;
                    } else {
                        $taux = $row->signe * 100;
                    }

                    $row->taux                        = $taux . "%";
                    $data_per_month[$row->commercial] = $row;

                    $all_ca += $row->ca;
                    $all_nombre += $row->nombre;
                    $all_signe += $row->signe;
                }

                $allObj             = new stdClass;
                $allObj->commercial = 0;
                $allObj->ca         = $all_ca;
                $allObj->nombre     = $all_nombre;
                $allObj->signe      = $all_signe;

                if ($all_nombre > 0) {
                    $all_taux = round($all_signe / $all_nombre, 4) * 100;
                } else {
                    $all_taux = $all_signe * 100;
                }

                $allObj->taux = $all_taux . "%";

                $data_per_month[0] = $allObj;

                $result[$id] = $data_per_month;
            }

            return $result;
        }
    }

    protected function get_weekly_data($generale, $weeks)
    {
        $result         = array();
        $where_generale = "";

        if (count($weeks) > 0) {

            if ($generale != "all") {
                $where_generale .= "WHERE origine_group = $generale";
            }

            foreach ($weeks as $i => $week) {
                $fields        = "";
                $data_per_week = array();

                if ($where_generale == "") {
                    $where_week = "WHERE `ctc_date_creation` BETWEEN DATE('$week') AND DATE_ADD('$week',INTERVAL '1' WEEK)";
                } else {
                    $where_week = "AND `ctc_date_creation` BETWEEN DATE('$week') AND DATE_ADD('$week',INTERVAL '1' WEEK)";
                }

                $fields .= "count(distinct ctc_id) as nombre,";
                $fields .= "count(distinct if((fac_id != '' OR ctc_signe = 1),ctc_id, null)) as signe,";
                $fields .= "SUM(fac_montant_ht) as ca";

                $table = "(SELECT utl_id as commercial,
                $fields
                FROM `t_utilisateurs`
                LEFT JOIN `t_contacts` ON `ctc_commercial_charge` = `utl_id`
                LEFT JOIN `t_devis` ON `dvi_client`=`ctc_id`
                LEFT JOIN `t_commandes` ON `cmd_devis`=`dvi_id`
                LEFT JOIN `t_factures` ON `fac_commande`=`cmd_id` AND 
											`fac_etat` = 2 AND
											(`fac_inactif` IS NULL OR `fac_inactif` = '0000-00-00 00:00:00')
                LEFT JOIN `v_types_origine_prospect` `top` ON `top`.`origine_id` = `ctc_origine`
                LEFT JOIN `v_types_origine_generale` ON `generale_id`=`origine_group`
                $where_generale $where_week AND ctc_statistiques = 1
                GROUP BY `utl_id`)";

                $query      = $this->db->query($table);
                $all_ca     = 0;
                $all_signe  = 0;
                $all_nombre = 0;
                $all_taux   = 0;

                foreach ($query->result() as $row) {
                    $row->ca = ($row->ca == null) ? 0 : $row->ca;

                    if ($row->nombre > 0) {
                        $taux = round($row->signe / $row->nombre, 4) * 100;
                    } else {
                        $taux = $row->signe * 100;
                    }

                    $row->taux                       = $taux . "%";
                    $data_per_week[$row->commercial] = $row;

                    $all_ca += $row->ca;
                    $all_nombre += $row->nombre;
                    $all_signe += $row->signe;
                }

                $allObj             = new stdClass;
                $allObj->commercial = 0;
                $allObj->ca         = $all_ca;
                $allObj->nombre     = $all_nombre;
                $allObj->signe      = $all_signe;

                if ($all_nombre > 0) {
                    $all_taux = round($all_signe / $all_nombre, 4) * 100;
                } else {
                    $all_taux = $all_signe * 100;
                }

                $allObj->taux = $all_taux . "%";

                $data_per_week[0] = $allObj;

                $result[$week] = $data_per_week;
            }

            return $result;
        }
    }

    public function get_month_range($range)
    {
        $months = array();

        if ($range == null && $range == "") {
            $startDate = date("Y-m-d", strtotime("-3 month"));
            $start     = (new DateTime($startDate))->modify('first day of this month');
            $end       = (new DateTime())->modify('first day of this month');
            $interval  = DateInterval::createFromDateString('1 month');
            $period    = new DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $months[] = array(
                    'm'    => (int) $dt->format("m"),
                    'y'    => $dt->format("Y"),
                    'id'   => (int) $dt->format("m") . "_" . $dt->format("Y"),
                    'name' => $dt->format("F"),
                );
            }

            $months = array_reverse($months);
        } else {
            $rangeDateArr = explode("-", $range);

            $startDate = $rangeDateArr[0];
            $endDate   = $rangeDateArr[1];

            $start = (new DateTime($startDate));
            $end   = (new DateTime($endDate));

            if ($startDate == $endDate) {
                $end->modify('+1 day');
            }

            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                //echo print_r($dt->format("m")); die();
                $months[] = array(
                    'm'    => (int) $dt->format("m"),
                    'y'    => $dt->format("Y"),
                    'id'   => (int) $dt->format("m") . "_" . $dt->format("Y"),
                    'name' => $dt->format("F"),
                );
            }

            $months = array_reverse($months);

        }

        return $months;
    }

    public function test($rangeDate)
    {
        echo print_r($this->get_month_range($rangeDate));
    }

    public function get_week_range($range = null)
    {
        $weeks = array();

        if ($range == null && $range == "") {
            $startDate = date("Y-m-d", strtotime("-3 month"));
            $start     = (new DateTime($startDate))->modify('first day of this month');
            $end       = (new DateTime());
            $interval  = DateInterval::createFromDateString('1 week');
            $period    = new DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $weeks[] = $dt->format("Y-m-d");
            }

            $weeks = array_reverse($weeks);
        } else {
            $rangeDateArr = explode("-", $range);

            $startDate = $rangeDateArr[0];
            $endDate   = $rangeDateArr[1];

            $start = (new DateTime($startDate));
            $end   = (new DateTime($endDate));

            if ($startDate == $endDate) {
                $end->modify('+1 day');
            }

            $interval = DateInterval::createFromDateString('1 week');
            $period   = new DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                //echo print_r($dt->format("m")); die();
                $weeks[] = $dt->format("Y-m-d");
            }

            $weeks = array_reverse($weeks);                    
        }

        return $weeks;
    }
}

// EOF
