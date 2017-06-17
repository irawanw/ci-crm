<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_commandes_trier extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function liste()
    {
        $query = $this->db->select('td.dvi_date,
                                    td.dvi_id,
                                    td.dvi_client,
                                    tc.ctc_nom,
                                    tcmd.cmd_reference,
                                    tsc.sec_ville,
                                    tv.vil_nom,
                                    tfa.feuilles_de_tri_article_id
                                    ')
            ->from('t_distributions tdb')
            ->join('t_devis td', 'td.dvi_id=tdb.devis_id')
            ->join('t_commandes tcmd', 'tcmd.cmd_devis=td.dvi_id')
            ->join('t_secteurs tsc', 'tdb.secteur_id=tsc.sec_id')
            ->join('t_villes tv','tsc.sec_ville=tv.vil_id')
            ->join('t_contacts tc', 'tc.ctc_id=td.dvi_client')
            ->join('t_feuilles_de_tri_article tfa','tfa.distribution_id=tdb.distribution_id','left')
            //->where('tfa.distribution_id', NULL)            
            ->where('td.dvi_inactif', NULL)            
            ->group_by('td.dvi_client, tsc.sec_ville')
            ->order_by('tfa.distribution_id','DESC')
            ->get();

        $rows = $query->result();

        $clients = array();
        $villes  = array();
        $orders  = array();
        $lists   = array();

        foreach ($rows as $row) {
            if (!array_key_exists($row->dvi_client, $clients)) {
                $clients[$row->dvi_client] = array('name' => $row->ctc_nom, 'feuilles_de_tri_article_id' => $row->feuilles_de_tri_article_id);
            }
            
            $ville_id     = $row->sec_ville;

            if (!array_key_exists($ville_id, $villes)) {
                $villes[$ville_id] = array('id' => $ville_id, 'name' => $row->vil_nom);
            }

            $row->ville_id                       = $ville_id;
            $orders[$ville_id][$row->dvi_client] = $row;
        }

        foreach ($villes as $ville) {
            $data        = new stdClass();
            $ville_id    = $ville['id'];
            $data->ville = '<a href="' . site_url('commandes_trier/ville/' . $ville['id']) . '">'.$ville['name'].'</a>';

            foreach ($clients as $id => $client) {
                if (array_key_exists($id, $orders[$ville_id])) {
                    $order       = $orders[$ville_id][$id];
                    $date        = $order->dvi_date;
                    $date        = date("d-m-Y", strtotime($date));
                    $link_date   = '<a href="' . site_url('commandes_trier/detail/' . $order->dvi_id) . '">' . $date . '<br>' . $order->cmd_reference. '</a>';

                    if($client['feuilles_de_tri_article_id'] == "") {
                        $data->$client['name'] = $link_date;                        
                    } else {
                        $data->$client['name'] = "";    
                    }
                } else {
                    $data->$client['name'] = "";
                }
            }

            $lists[] = $data;
        }

        return $lists;
    }

    public function detail($id)
    {        
        $commande = $this->get_commande_detail($id);
        $devis    = $this->get_devis_detail($id);
        $secteurs = $this->get_secteurs_detail($id);

        return array(
            'commande' => $commande,
            'devis'    => $devis,
            'secteurs' => $secteurs,
        );
    }

    public function get_ville_name($id)
    {
        $row = $this->db->get_where('t_villes', array('vil_id' => $id))->row();

        return $row->vil_nom;
    }

    /******************************
     * Détail d'une commande
     ******************************/
    public function get_commande_detail($devis_id)
    {
        $this->load->helper("calcul_devis");

        // lecture des informations
        $this->db->select("cmd_id,cmd_reference,cmd_date,cmd_devis,dvi_reference,dvi_id,dvi_correspondant,cor_nom,dvi_client,ctc_nom,cor_nom,dvi_tva,0 AS cmd_p_facture,0 AS cmd_p_regle,cmd_etat,vec_etat", false);
        $this->db->join('t_devis', 'dvi_id=cmd_devis', 'left');
        $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->join('v_etats_commandes', 'vec_id=cmd_etat', 'left');
        $this->db->where('dvi_id', $devis_id);
        $this->db->where('cmd_inactif is null');
        $q = $this->db->get('t_commandes');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            $resultat = calcul_devis($resultat);
            return $resultat;
        } else {
            return null;
        }
    }

    /******************************
     * Détail d'un devis
     ******************************/
    public function get_devis_detail($devis_id)
    {

        // lecture des informations
        $this->db->select("dvi_id,dvi_reference,dvi_date,dvi_chaleur,vch_degre,dvi_client,ctc_nom,dvi_correspondant,cor_nom,dvi_societe_vendeuse,scv_nom,dvi_montant_ht,dvi_montant_ttc,dvi_etat,ved_etat,dvi_tva,dvi_notes,dvi_fichier", false);
        $this->db->join('v_chaleur', 'vch_id=dvi_chaleur', 'left');
        $this->db->join('t_contacts', 'ctc_id=dvi_client', 'left');
        $this->db->join('t_correspondants', 'cor_id=dvi_correspondant', 'left');
        $this->db->join('t_societes_vendeuses', 'scv_id=dvi_societe_vendeuse', 'left');
        $this->db->join('v_etats_devis', 'ved_id=dvi_etat', 'left');
        $this->db->where('dvi_id', $devis_id);
        $this->db->where('dvi_inactif is null');
        $q = $this->db->get('t_devis');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        } else {
            return null;
        }
    }

    public function get_secteurs_detail($devis_id)
    {
        $query = $this->db->select('ard_info')
            ->from('t_articles_devis')
            ->where('ard_code', 'D')
            ->where('ard_devis', $devis_id)
            ->get();
        $rows = $query->result();

        $villes    = array();
        $hlm_array = array();
        $res_array = array();
        $pav_array = array();
        $result    = array();

        foreach ($rows as $row) {
            $boite        = substr($row->ard_info, 0, 3);
            $ville_string = substr($row->ard_info, 3);
            $ville_arr    = explode(":", $ville_string);
            $ville_id     = $ville_arr[0];
            $secteur_id   = $ville_arr[1];

            if (!array_key_exists($ville_id, $villes)) {
                $secteur_ids = array();

                if (!in_array($secteur_id, $secteur_ids) && $secteur_id != 0) {
                    array_push($secteur_ids, $secteur_id);
                }

                $villes[$ville_id] = $secteur_ids;
            } else {
                $secteur_ids = $villes[$ville_id];

                if (!in_array($secteur_id, $secteur_ids) && $secteur_id != 0) {
                    array_push($secteur_ids, $secteur_id);
                }

                $villes[$ville_id] = $secteur_ids;
            }

            if ($secteur_id != 0) {
                switch ($boite) {
                    case 'HLM':
                        $hlm_array[] = $ville_id . "-" . $secteur_id;
                        break;
                    case 'RES':
                        $res_array[] = $ville_id . "-" . $secteur_id;
                        break;
                    case 'PAV':
                        $pav_array[] = $ville_id . "-" . $secteur_id;
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                switch ($boite) {
                    case 'HLM':
                        $hlm_array[] = $ville_id;
                        break;
                    case 'RES':
                        $res_array[] = $ville_id;
                        break;
                    case 'PAV':
                        $pav_array[] = $ville_id;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        foreach ($villes as $id => $vals) {
            $is_all = count($vals) > 0 ? false : true;

            if (count($vals) > 0) {
                $query = $this->db->select('sec_id, sec_nom,sec_ville,vil_nom,sec_hlm AS hlm,sec_pav as pav,sec_res as res')
                    ->from('t_secteurs')
                    ->join('t_villes', 'vil_id=sec_ville')
                    ->where_in('sec_id', $vals)
                    ->get();
            } else {
                $query = $this->db->select('sec_id, sec_nom,sec_ville,vil_nom,sec_hlm AS hlm,sec_pav as pav,sec_res as res')
                    ->from('t_secteurs')
                    ->join('t_villes', 'vil_id=sec_ville')
                    ->where_in('sec_ville', $id)
                    ->get();
            }

            $rows     = $query->result();
            $secteurs = array();

            foreach ($rows as $row) {
                if ($is_all) {
                    $hlm = in_array($id, $hlm_array) ? $row->hlm : 0;
                    $res = in_array($id, $res_array) ? $row->res : 0;
                    $pav = in_array($id, $pav_array) ? $row->pav : 0;
                } else {
                    $hlm = in_array($id . "-" . $row->sec_id, $hlm_array) ? $row->hlm : 0;
                    $res = in_array($id . "-" . $row->sec_id, $res_array) ? $row->res : 0;
                    $pav = in_array($id . "-" . $row->sec_id, $pav_array) ? $row->pav : 0;
                }

                $secteurs[] = array(
                    'secteur_name' => $row->sec_nom,
                    'hlm'          => $hlm,
                    'res'          => $res,
                    'pav'          => $pav,
                );
            }

            $result[] = array(
                'ville_name' => $rows[0]->vil_nom,
                'secteurs'   => $secteurs,
            );
        }

        return $result;
    }

    /*
    public function ville_liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc")
    {
        $table = 't_articles_devis';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $ard_info = "SUBSTR(ard_info,4) as ard_info";
        
        $this->db->select("$ard_info,
                                    dvi_date,
                                    ctc_nom,
                                    cmd_reference,
                                    ", false);

        $this->db->join('t_devis', 'dvi_id=ard_devis');
        $this->db->join('t_commandes', 'cmd_devis=dvi_id');
        $this->db->join('t_contacts', 'ctc_id=dvi_client');
        $this->db->where('ard_code', 'D');       

        if ($this->input->get('ville_id')) {
            $ville_id = $this->input->get('ville_id');
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->ville_liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        $villes = array();

        //add checkbox into data
        $n=0;        

        $number = 1;
        foreach ($resultat['data'] as $row) {
            $ville_arr    = explode(":", $row->ard_info);    
            if ($ville_arr[0] == $ville_id) {
                if (empty(array_filter($villes))) {
                    $data = new stdClass();
                    $data->number = $number;
                    $data->ctc_nom = $row->ctc_nom;
                    $data->dvi_date = $row->dvi_date;
        
        $data->cmd_reference = $row->cmd_reference;
                    $villes[] = $data;
                }elseif($row->cmd_reference != $villes[$n]->cmd_reference){
                    $data = new stdClass();
                    $data->number = $number;
                    $data->ctc_nom = $row->ctc_nom;
                    $data->dvi_date = $row->dvi_date;
                    $data->cmd_reference = $row->cmd_reference;
                    $villes[] = $data;
                    $n++;
                }                

                $number++;
            }
            
        }

        $resultat['data'] = $villes;
        $resultat['recordsTotal'] = count($villes);
        $resultat['recordsFiltered'] = count($villes);

        return $resultat;
    }
    */
    
    public function ville_liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc"){

        $ordercol = "secteur_id";

        if ($this->input->get('ville_id')) {
            $ville_id = $this->input->get('ville_id');
        }
        $table = 't_distributions';
        $secteur = "t_secteurs.sec_nom as secteur";
        
        $this->db->start_cache();
        $query = $this->db->select($table.'.*,                                    
                                    sec_nom as secteur,
                                    cmd_reference,
                                    dvi_date,
                                    ctc_nom')               
                    ->from('t_distributions')                   
                    ->join('t_devis', 'dvi_id=devis_id')
                    ->join('t_commandes', 'cmd_devis=devis_id')
                    ->join('t_contacts', 'ctc_id=dvi_client')
                    ->join('t_secteurs', 'sec_id=secteur_id')
                    ->join('t_villes', 'vil_id=sec_ville')
                    ->join('t_feuilles_de_tri_article tfa', 'tfa.distribution_id=t_distributions.distribution_id','left')                    
                    //->join('t_feuilles_de_tri_article tfa_2', 'tfa_2.article_devis_id=ard_id','left')                    
                    //->where('tfa_2.article_devis_id', NULL)
                    ->where('tfa.distribution_id', NULL)
                    ->where("t_secteurs.sec_ville", $ville_id);
                    //->group_by('secteur_id, ard_devis');

        $this->db->stop_cache();
        // aliases
        $aliases = array(
        );              
        
        $resultat = $this->_filtre($table,$this->ville_liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();
        
        //add checkbox into data
        for($i=0; $i<count($resultat['data']); $i++){
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="'.$resultat['data'][$i]->distribution_id.'">';
        }   

        $resultat['recordsTotal'] = count($resultat['data']);
        $resultat['recordsFiltered'] = count($resultat['data']);

        return $resultat;                   
    }

    public function ville_liste_filterable_columns()
    {
        $filterable_columns = array(
            
        );

        return $filterable_columns;
    }

    
    public function ville($id){
        $query = $this->db->select('SUBSTR(tad.ard_info,4) as ard_info,
                                    td.dvi_date,
                                    tc.ctc_nom,
                                    tcmd.cmd_reference,
                                    ')
            ->from('t_articles_devis tad')
            ->join('t_devis td', 'td.dvi_id=tad.ard_devis')
            ->join('t_commandes tcmd', 'tcmd.cmd_devis=td.dvi_id')
            ->join('t_contacts tc', 'tc.ctc_id=td.dvi_client')
            ->where('tad.ard_code', 'D')
            ->get();
        $rows = $query->result_array();
        $villes    = array();
        $n=0;
        foreach ($rows as $row) {
            $ville_arr    = explode(":", $row['ard_info']);    
            if ($ville_arr[0]==$id) {
                if (empty(array_filter($villes))) {
                    $data['ctc_nom'] = $row['ctc_nom'];
                    $data['dvi_date'] = $row['dvi_date'];
                    $data['cmd_reference'] = $row['cmd_reference'];
                    $villes[] = $data;
                }elseif($row['cmd_reference'] != $villes[$n]['cmd_reference']){
                    $data['ctc_nom'] = $row['ctc_nom'];
                    $data['dvi_date'] = $row['dvi_date'];
                    $data['cmd_reference'] = $row['cmd_reference'];
                    $villes[] = $data;
                    $n++;
                }                
            }
            
        }
        return $villes;
    }

    public function testville($id)
    {
        $query = $this->db->select('tad.ard_info, 
                                    tad.ard_quantite,
                                    td.dvi_date,
                                    tc.ctc_nom,
                                    tcmd.cmd_reference,
                                    ')
            ->from('t_articles_devis tad')
            ->join('t_devis td', 'td.dvi_id=tad.ard_devis')
            ->join('t_commandes tcmd', 'tcmd.cmd_devis=td.dvi_id')
            ->join('t_contacts tc', 'tc.ctc_id=td.dvi_client')
            ->where('tad.ard_code', 'D')
            ->get();
        $rows = $query->result_array();
        $villes    = array();
        $n=0;
        foreach ($rows as $row) {
            $boite        = substr($row['ard_info'], 0, 3);
            $ville_string = substr($row['ard_info'], 3);
            $ville_arr    = explode(":", $ville_string);
            $ville_id     = $ville_arr[0];  
            $secteur_id   = $ville_arr[1];
            if ($ville_id==$id) {
                $villes[$secteur_id][] = $row;
                /*switch ($boite) {
                    case 'HLM':
                    $data['hlm'] = $row['ard_quantite'];
                    break;
                    case 'RES':
                    $data['res'] = $row['ard_quantite'];
                    break;
                    case 'PAV':
                    $data['pav'] = $row['ard_quantite'];
                    break;
                    default:
                            # code...
                    break;
                }
                !empty($data['hlm']) ? $data['hlm'] : $data['hlm']=0;  
                !empty($data['res']) ? $data['res'] : $data['res']=0;  
                !empty($data['pav']) ? $data['pav'] : $data['pav']=0;  
                $data['ctc_nom'] = $row['ctc_nom'];
                $data['dvi_date'] = $row['dvi_date'];
                $data['cmd_reference'] = $row['cmd_reference'];
                $data['secteur'] = $secteur_id;
                $villes[] = $data;*/
                /*if (empty(array_filter($villes))) {
                    switch ($boite) {
                        case 'HLM':
                            $data['hlm'] = $row['ard_quantite'];
                            break;
                        case 'RES':
                            $data['res'] = $row['ard_quantite'];
                            break;
                        case 'PAV':
                            $data['pav'] = $row['ard_quantite'];
                            break;
                        default:
                            # code...
                            break;
                    }
                    !empty($data['hlm']) ? $data['hlm'] : $data['hlm']=0;  
                    !empty($data['res']) ? $data['res'] : $data['res']=0;  
                    !empty($data['pav']) ? $data['pav'] : $data['pav']=0;  
                    $data['ctc_nom'] = $row['ctc_nom'];
                    $data['dvi_date'] = $row['dvi_date'];
                    $data['cmd_reference'] = $row['cmd_reference'];
                    $data['secteur'] = $secteur_id;
                    $villes[] = $data;
                }elseif($secteur_id != $villes[$n]['secteur']){
                    switch ($boite) {
                        case 'HLM':
                            $data['hlm'] = $row['ard_quantite'];
                            break;
                        case 'RES':
                            $data['res'] = $row['ard_quantite'];
                            break;
                        case 'PAV':
                            $data['pav'] = $row['ard_quantite'];
                            break;
                        default:
                            # code...
                            break;
                    }
                    !empty($data['hlm']) ? $data['hlm'] : $data['hlm']=0;  
                    !empty($data['res']) ? $data['res'] : $data['res']=0;  
                    !empty($data['pav']) ? $data['pav'] : $data['pav']=0;  
                    $data['ctc_nom'] = $row['ctc_nom'];
                    $data['dvi_date'] = $row['dvi_date'];
                    $data['cmd_reference'] = $row['cmd_reference'];
                    $data['secteur'] = $secteur_id;
                    $villes[] = $data;
                    $n++;
                }   */             
            }            
        }
        return $villes;
    }

    public function valider_ce_tri($ville_id)
    {		
		/*
        $check = $this->db->get_where('t_feuilles_de_tri', array('ville_id' => $ville_id));

        if($check->num_rows() == 0) {
            $date = date("Y-m-d");
            $data = array(
                'ville_id' => $ville_id,
                'date_du_tri' => $date
            );

            $this->db->insert('t_feuilles_de_tri', $data);

            $insert_id = $this->db->insert_id();

            if($insert_id) {
                $query = $this->db->select('ard_id as id')
                                  ->where("SUBSTRING(ard_info, 4) LIKE '$ville_id:%'")
                                  ->get('t_articles_devis');

                if($query->num_rows() > 0) {
                    $articles = array();

                    foreach($query->result() as $row)
                    {
                        $articles[] = array(
                            'ville_id' => $ville_id,
                            'article_devis_id' => $row->id
                        );
                    }

                    $this->db->insert_batch('t_feuilles_de_tri_article', $articles); 
                }

                return $insert_id;
            } else {
                return false;
            }
        } else {
            return true;
        }
		*/		
		
		$date = date("Y-m-d");
		$data = array(
			'ville_id' => $ville_id,
			'date_du_tri' => $date
		);
		$this->db->insert('t_feuilles_de_tri', $data);
        $insert_id = $this->db->insert_id();
		
		$articles = array();
		$ids = array();		
		foreach($this->input->post() as $row){			
			//get article_devis_id for reference
			$check = $this->db->get_where('t_distributions', array('distribution_id' => $row))->result();		
			$articles[] = array(
				'feuilles_de_tri_id' => $insert_id,		
				'distribution_id' => $row,
			);
		}
		$this->db->insert_batch('t_feuilles_de_tri_article', $articles); 
		$group_id = $this->db->insert_id();		
		
		return $insert_id;
    }

}
