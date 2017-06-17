<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */

class M_demande_devis_commerciaux extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAdwords($commercial = 0, $periods = 0)
    {
        $arrCond = array(
            'week'   => 'date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day)',
            'month'  => 'month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );
        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('count(*) as total_adwords');
                $this->db->from('t_contacts tc');
				$this->db->where('ctc_origine_generale',2);
                $this->db->where($val);
				if ($commercial != 0) {
                    $this->db->where_in('tc.ctc_commercial_charge', $commercial);
					debug($commercial,1);
                }
                $this->db->order_by('tc.ctc_date_creation', 'DESC');
                $sql 	= $this->db->get();
                $value	= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
				$facture = $this->getNombre($commercial,$periods);
				$result[$key] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
				);
            }
        } 
		else 
		{
            $val = $arrCond[$periods];
            $this->db->select('count(*) as total_adwords');
            $this->db->from('t_contacts tc');
			$this->db->where('ctc_origine_generale',2);
            if ($commercial != 0) {
                $this->db->where_in('tc.ctc_commercial_charge', $commercial);
            }
            $this->db->where($val);
            $this->db->order_by('tc.ctc_date_creation', 'DESC');
            $sql		= $this->db->get();
            $value		= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
			$facture 	= $this->getNombre($commercial,$periods);
			$result[$periods] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
			);
        }
        return $result;
    }

	public function getEmailing($commercial = 0, $periods = 0)
    {
        $arrCond = array(
            'week'   => 'date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day)',
            'month'  => 'month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );
        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('count(*) as total_adwords');
                $this->db->from('t_contacts tc');
				$this->db->where('ctc_origine_generale',1);
                if ($commercial != 0) {
                    $this->db->where_in('tc.ctc_commercial_charge', $commercial);
                }
                $this->db->where($val);
                $this->db->order_by('tc.ctc_date_creation', 'DESC');
                $sql 	= $this->db->get();
                $value	= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
				$facture = $this->getNombre($commercial,$periods);
				$result[$key] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
				);
            }
        } 
		else 
		{
            $val = $arrCond[$periods];
            $this->db->select('count(*) as total_adwords');
            $this->db->from('t_contacts tc');
			$this->db->where('ctc_origine_generale',1);
            if ($commercial != 0) {
                $this->db->where_in('tc.ctc_commercial_charge', $commercial);
            }
            $this->db->where($val);
            $this->db->order_by('tc.ctc_date_creation', 'DESC');
            $sql		= $this->db->get();
            $value		= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
			$facture 	= $this->getNombre($commercial,$periods);
			$result[$periods] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
			);
        }
        return $result;
    }
	
	public function getAutre($commercial = 0, $periods = 0)
    {
        $arrCond = array(
            'week'   => 'date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day)',
            'month'  => 'month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );
        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('count(*) as total_adwords');
                $this->db->from('t_contacts tc');
				$this->db->where_not_in('ctc_origine_generale',array('1','2'));
                if ($commercial != 0) {
                    $this->db->where_in('tc.ctc_commercial_charge', $commercial);
                }
                $this->db->where($val);
                $this->db->order_by('tc.ctc_date_creation', 'DESC');
                $sql 	= $this->db->get();
                $value	= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
				$facture = $this->getNombre($commercial,$periods);
				$result[$key] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
				);
            }
        } 
		else 
		{
            $val = $arrCond[$periods];
            $this->db->select('count(*) as total_adwords');
            $this->db->from('t_contacts tc');
			$this->db->where_not_in('ctc_origine_generale',array('1','2'));
            if ($commercial != 0) {
                $this->db->where_in('tc.ctc_commercial_charge', $commercial);
            }
            $this->db->where($val);
            $this->db->order_by('tc.ctc_date_creation', 'DESC');
            $sql		= $this->db->get();
            $value		= ($sql->num_rows() == 0) ? 0 : $sql->row('total_adwords');
			$facture 	= $this->getNombre($commercial,$periods);
			$result[$periods] = array(
								'origine' => $value, 
								'nombre' => $facture['nombre'], 
								'total_ht' => $facture['total_ht']
			);
        }
        return $result;
    }

	public function getNombre($commercial = 0,$periods = 0)
	{
	    $arrCond = array(
            'week'   => 'date(tf.fac_date) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) ',
            'month'  => 'month(tf.fac_date) = "' . date("n") . '"and year(tf.fac_date) = "' . date("Y") . '" OR
								month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(tf.fac_date) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(tf.fac_date) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(tf.fac_date) between date_add(date(now()),interval -6 month) and date_add(date(now()),interval 0 month) OR
								date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(tf.fac_date) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year) OR
								date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );

        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('ctc_id,count(tf.fac_id) as total_factures, sum(fac_montant_ht) as total_ht');
                $this->db->from('t_contacts tc');
                $this->db->join('t_devis td', 'tc.ctc_id = td.dvi_client', 'left');
                $this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
                $this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande', 'left');

                if ($commercial != 0) {
                    $this->db->where_in('tc.ctc_commercial_charge', $commercial);
                }
				
                $where = "`ctc_statistiques` = true AND (`fac_inactif` IS NULL OR `ctc_signe` = 1 ) AND ($val)";

	            $this->db->where($where);            
	            $this->db->order_by('tf.fac_date', 'DESC');
	            $this->db->group_by('ctc_id');
            
                $this->db->order_by('tf.fac_date', 'DESC');
                $this->db->group_by('ctc_id');

                $sql     = $this->db->get();
                $nombre   = $sql->num_rows();
                $results = $sql->result();

                $total_ht = 0;
                foreach ($results as $row) {
                    $total_ht += $row->total_ht;
                }

                $result = array('nombre' => $nombre, 'total_ht' => $total_ht);
            }
        } else {
            $val = $arrCond[$periods];
            $this->db->select('ctc_id,count(tf.fac_id) as total_factures, sum(fac_montant_ht) as total_ht');
            $this->db->from('t_contacts tc');
            $this->db->join('t_devis td', 'tc.ctc_id = td.dvi_client', 'left');
            $this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
            $this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande', 'left');

            if ($commercial != 0) {
                $this->db->where('tc.ctc_commercial_charge', $commercial);
            }
            if ($enseignes != 0) {
                $this->db->where('tc.ctc_enseigne', $enseignes);
            }
            if ($generale != 0) {
                $this->db->where('tc.ctc_origine_generale', $generale);
            }

            $where = "`ctc_statistiques` = true AND (`fac_inactif` IS NULL OR `ctc_signe` = 1 ) AND ($val)";

            $this->db->where($where);            
            $this->db->order_by('tf.fac_date', 'DESC');
            $this->db->group_by('ctc_id');
            $sql = $this->db->get();
            $value   = $sql->num_rows();
            $results = $sql->result();

            $total_ht = 0;
            foreach ($results as $row) {
                $total_ht += $row->total_ht;
            }

            $result[$periods] = array(
                'value'    => $value,
                'total_ht' => $total_ht,
            );
        }
        return $result;
	}
	
    public function getTotalFactures($commercial = 0, $periods = 0, $generale = 0, $origine = 0, $enseignes = 0)
    {
        $arrCond = array(
            'week'   => 'date(tf.fac_date) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) ',
            'month'  => 'month(tf.fac_date) = "' . date("n") . '"and year(tf.fac_date) = "' . date("Y") . '" OR
								month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(tf.fac_date) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(tf.fac_date) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day) OR
								date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(tf.fac_date) between date_add(date(now()),interval -6 month) and date_add(date(now()),interval 0 month) OR
								date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(tf.fac_date) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year) OR
								date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );

        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('ctc_id,count(tf.fac_id) as total_factures, sum(fac_montant_ht) as total_ht');
                $this->db->from('t_contacts tc');
                $this->db->join('t_devis td', 'tc.ctc_id = td.dvi_client', 'left');
                $this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
                $this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande', 'left');

                if ($commercial != 0) {
                    $this->db->where('tc.ctc_commercial_charge', $commercial);
                }
                if ($enseignes != 0) {
                    $this->db->where('tc.ctc_enseigne', $enseignes);
                }
                if ($generale != 0) {
                    $this->db->where('tc.ctc_origine_generale', $generale);
                }

                $where = "`ctc_statistiques` = true AND (`fac_inactif` IS NULL OR `ctc_signe` = 1 ) AND ($val)";

	            $this->db->where($where);            
	            $this->db->order_by('tf.fac_date', 'DESC');
	            $this->db->group_by('ctc_id');
            
                $this->db->order_by('tf.fac_date', 'DESC');
                $this->db->group_by('ctc_id');

                $sql     = $this->db->get();
                $value   = $sql->num_rows();
                $results = $sql->result();

                $total_ht = 0;
                foreach ($results as $row) {
                    $total_ht += $row->total_ht;
                }

                $result[$key] = array(
                    'value'    => $value,
                    'total_ht' => $total_ht,
                );
            }
        } else {
            $val = $arrCond[$periods];
            $this->db->select('ctc_id,count(tf.fac_id) as total_factures, sum(fac_montant_ht) as total_ht');
            $this->db->from('t_contacts tc');
            $this->db->join('t_devis td', 'tc.ctc_id = td.dvi_client', 'left');
            $this->db->join('t_commandes tco', 'td.dvi_id = tco.cmd_devis', 'left');
            $this->db->join('t_factures tf', 'tco.cmd_id = tf.fac_commande', 'left');

            if ($commercial != 0) {
                $this->db->where('tc.ctc_commercial_charge', $commercial);
            }
            if ($enseignes != 0) {
                $this->db->where('tc.ctc_enseigne', $enseignes);
            }
            if ($generale != 0) {
                $this->db->where('tc.ctc_origine_generale', $generale);
            }

            $where = "`ctc_statistiques` = true AND (`fac_inactif` IS NULL OR `ctc_signe` = 1 ) AND ($val)";

            $this->db->where($where);            
            $this->db->order_by('tf.fac_date', 'DESC');
            $this->db->group_by('ctc_id');
            $sql = $this->db->get();
            $value   = $sql->num_rows();
            $results = $sql->result();

            $total_ht = 0;
            foreach ($results as $row) {
                $total_ht += $row->total_ht;
            }

            $result[$periods] = array(
                'value'    => $value,
                'total_ht' => $total_ht,
            );
        }
        return $result;
    }

    public function getOrigine($commercial, $periods, $generale, $origine, $enseignes)
    {
        $this->db->select('origine_id,origine_name,origine_group,generale_name as group_name');
        $this->db->join('v_types_origine_generale','generale_id=origine_group','left');
        if($origine !=0)
        {
            $this->db->where('origine_id',$origine);
        }

        if($generale != 0) {
            $this->db->where('origine_group', $generale);
        }

        $sql    = $this->db->get('v_types_origine_prospect');
        $result = $sql->result_array();
        
        $i=0;
        $res = array();
        foreach($result as $row)
        {
            $list = $this->_origineDevis($row['origine_id'],$commercial,$periods,$generale,$origine,$enseignes);            
            $res[$i][$row['origine_group']] = $row['group_name'];

            foreach($list as $key => $val)
            {               
                $res[$i][$row['origine_name']][$key] = $val['value'];
                $res[$i]['nombre_signe'][$key] = $val['nombre_signe']['value'];
                                
                if($val['value'] != 0){
                    $sum = ($val['nombre_signe']['value']/$val['value'])*100;
                } else {
                    $sum = 0;
                }               
                
                $res[$i]['%_signe'][$key] = round($sum).'%';
                $res[$i]['ca_signe'][$key] = $val['nombre_signe']['total_ht'];
            }
            $i++;
        }

        $customResult = array();
        $this->db->select('generale_id as id,generale_name as name');
        if($generale != 0) {
            $this->db->where('generale_id', $generale);
        }
        
        $groups = $this->db->get('v_types_origine_generale')->result();

        $resultTotal = array();

        foreach($groups as $group) {
            $total = array();
            foreach($res as $key => $rows) {
                if($group->id == key($rows)) {
                    $customResult[$group->id][] = $rows;
                }
            }

            $group_id = $group->id;
            $listGroup = $this->_origineDevis(0,$commercial,$periods,$group_id,$origine,$enseignes);   

            foreach($listGroup as $key => $val)
            {               
                $total[$group->name][$key] = $val['value'];
                $total['nombre_signe'][$key] = $val['nombre_signe']['value'];
                                
                if($val['value'] != 0){
                    $sum = ($val['nombre_signe']['value']/$val['value'])*100;
                } else {
                    $sum = 0;
                }               
                
                $total['%_signe'][$key] = round($sum).'%';
                $total['ca_signe'][$key] = $val['nombre_signe']['total_ht'];
            }

            $resultTotal[$group->id] = $total; 
        }

        return array(
            'resultDetail' => $customResult,
            'resultTotal' => $resultTotal
        );
    }

    private function _origineDevis($listorigine = 0, $commercial, $periods, $generale, $origine, $enseignes)
    {
        $signe  = 0;
        $result = array();

        $arrCond = array(
            'week'   => 'date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day)',
            'month'  => 'month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );

        if ($periods == "all") {
            foreach ($arrCond as $key => $val) {
                $this->db->select('count(ctc_id) as origine');
                $this->db->from('t_contacts tc');
                $this->db->join('t_employes te', 'tc.ctc_commercial = te.emp_id', 'left');
                $this->db->join('v_types_origine_prospect top', 'top.origine_id = tc.ctc_origine', 'left');

                if ($commercial != 0) {
                    $this->db->where('tc.ctc_commercial_charge', $commercial);
                }

                if ($enseignes != 0) {
                    $this->db->where('tc.ctc_enseigne', $enseignes);
                }

                if ($generale != 0) {
                    $this->db->where('top.origine_group', $generale);
                }

                $this->db->where($val);                
                $this->db->where('tc.ctc_statistiques', 1);
                if($listorigine != 0) {
                    $this->db->where('top.origine_id', $listorigine);
                }
                $sql = $this->db->get();

                $res          = $sql->row();
                $nombre       = $this->_nombre($listorigine, $commercial, $key, $generale, $origine, $enseignes);
                $result[$key] = array('value' => $res->origine, 'nombre_signe' => $nombre);
            }
        } else {
            $val = $arrCond[$periods];
            $this->db->select('count(ctc_id) as origine');
            $this->db->from('t_contacts tc');
            $this->db->join('t_employes te', 'tc.ctc_commercial = te.emp_id', 'left');
            $this->db->join('v_types_origine_prospect top', 'top.origine_id = tc.ctc_origine', 'left');
            if ($commercial != 0) {
                $this->db->where('tc.ctc_commercial_charge', $commercial);
            }
            if ($enseignes != 0) {
                $this->db->where('tc.ctc_enseigne', $enseignes);
            }
            if ($generale != 0) {
                $this->db->where('top.origine_group', $generale);
            }

            $this->db->where($val);
            if($listorigine != 0) {
                    $this->db->where('top.origine_id', $listorigine);
                }
            $this->db->where('tc.ctc_statistiques', 1);
            $sql              = $this->db->get();
            $res              = $sql->row();
            $nombre           = $this->_nombre($listorigine, $commercial, $periods, $generale, $origine, $enseignes);
            $result[$periods] = array('value' => $res->origine, 'nombre_signe' => $nombre);
        }
        return $result;
    }

    private function _nombre($listorigine = 0, $commercial, $periods, $generale, $origine, $enseignes)
    {
        $result = array();

        $arrCond = array(
            'week'   => 'date(tf.fac_date) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) AND
								date(ctc_date_creation) between date_add(date(now()),interval -7 day) and date_sub(date(now()),interval 0 day) ',
            'month'  => 'month(tf.fac_date) = "' . date("n") . '"and year(tf.fac_date) = "' . date("Y") . '" AND
								month(ctc_date_creation) = "' . date("n") . '"and year(ctc_date_creation) = "' . date("Y") . '"',
            'day30'  => 'date(tf.fac_date) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day) AND
								date(ctc_date_creation) between date_add(date(now()),interval -30 day) and date_sub(date(now()),interval 0 day)',
            'day90'  => 'date(tf.fac_date) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day) AND
								date(ctc_date_creation) between date_add(date(now()),interval -90 day) and date_sub(date(now()),interval 0 day)',
            'month6' => 'date(tf.fac_date) between date_add(date(now()),interval -6 month) and date_add(date(now()),interval 0 month) AND
								date(ctc_date_creation) between date_add(date(now()),interval -6 month) and date_sub(date(now()),interval 0 month)',
            'year'   => 'date(tf.fac_date) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year) AND
								date(ctc_date_creation) between date_add(date(now()),interval -1 year) and date_sub(date(now()),interval 0 year)',
        );

        $val = $arrCond[$periods];
        $this->db->select('count(tf.fac_id) as origine, sum(fac_montant_ht) as total_ht');
        $this->db->from('t_factures tf');
        $this->db->join('t_commandes tco', 'tco.cmd_id = tf.fac_commande', 'left');
        $this->db->join('t_devis td', 'td.dvi_id = tco.cmd_devis', 'left');
        $this->db->join('t_contacts tc', 'tc.ctc_id = td.dvi_client', 'left');
        $this->db->join('v_types_origine_prospect top', 'top.origine_id = tc.ctc_origine', 'left');
        if ($commercial != 0) {
            $this->db->where('tc.ctc_commercial_charge', $commercial);
        }
        if ($enseignes != 0) {
            $this->db->where('tc.ctc_enseigne', $enseignes);
        }
        if ($generale != 0) {
            $this->db->where('top.origine_group', $generale);
        }
        $this->db->where($val);
        if($listorigine != 0) {
                    $this->db->where('top.origine_id', $listorigine);
                }
        $this->db->where('tf.fac_inactif', null);
        $this->db->where('tc.ctc_statistiques', 1);
        $this->db->group_by('ctc_id');

        $sql     = $this->db->get();
        $value   = $sql->num_rows();
        $results = $sql->result();

        $total_ht = 0;
        foreach ($results as $row) {
            $total_ht += $row->total_ht;
        }

        $result = array(
            'value'    => $value,
            'total_ht' => $total_ht,
        );
        return $result;
    }
}