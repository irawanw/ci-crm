<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_feuille_de_route extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_champs($type, $data = null)
    {
        $champs = array(
            'read.parent' => array(
                array('checkbox', 'text', "&nbsp", 'checkbox'),
                // array('id', 'text', "id#", 'id'),
                array('feuille_de_route_id', 'text', "ID#", 'feuille_de_route_id'),
                array('date', 'text', "Date de la feuille", 'date'),
                array('nom_du_tri', 'text', "Nom du tri", 'nom_du_tri'),
                //array('consulter_tri', 'text', "Consulter Tri", 'consulter_tri'),
                array('ville_name', 'text', "Ville", 'ville_name'),
                array('secteur_name', 'text', "Secteur", 'secteur_name'),
                array('distributeur_name', 'text', "Distributeur", 'distributeur_name'),
                array('etat', 'text', "Etat", 'etat'),
            ),
            'read.child'  => array(
                //array('checkbox', 'text', "&nbsp", 'checkbox'),
                array('number', 'text', "id#", 'number'),                
                array('ville_name', 'text', "Ville", 'ville_name'),
                array('fdr_ok', 'text', "FDR OK", 'fdr_ok'),
                array('fdr_total', 'text', "FDR Total", 'fdr_total'),
                array('tri_amorce', 'text', "TRI Amorcé", 'tri_amorce'),
                array('tri_total', 'text', "TRI Total", 'tri_total'),
            ),
            'write.parent' => array(),
            'write.child' => array(),
        );

        if ($data != null) {
            $result = $champs[$type . "." . $data];
        } else {
            $result = array_merge($champs[$type . ".parent"], $champs[$type . ".child"]);
        }

        return $result;
    }

    /******************************
     * Liste test mails Data
     ******************************/
    public function liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_feuille_de_routes';
        // première partie du select, mis en cache  
        $nom_du_tri = "vil_nom as nom_du_tri";
        $ville_name = "vil_nom as ville_name";
        $secteur_name = "sec_nom as secteur_name";
        $distributeur_name = "emp_nom as distributeur_name";
        $date = formatte_sql_date('date');

        $this->db->start_cache();
        $this->db->select("
                feuille_de_route_id as RowID,
                feuille_de_route_id,
				feuilles_de_tri_id,
                $date,
                $nom_du_tri,
                'Voir Tri' as consulter_tri,
                $ville_name,
                vil_id,
                $secteur_name,
                $distributeur_name,
                emp_id,
                'En cours' as etat
                ");

        $this->db->join('t_secteurs', 'sec_id=secteur_id');
        $this->db->join('t_villes', 'vil_id=sec_ville');
        $this->db->join('t_employes', 'distributeur=emp_id');
        $this->db->group_by($table.'.feuille_de_route_id');
        $this->db->stop_cache();
        // aliases
        $aliases = array(        	
        	'ville_name' => "vil_nom",
        	'nom_du_tri' => "vil_nom",
        	'secteur_name' => "sec_nom",
        	'distributeur_name' => "emp_nom"
        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $id = $resultat['data'][$i]->feuille_de_route_id;          
			$feuilles_de_tri_id = $resultat['data'][$i]->feuilles_de_tri_id;
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $id . '">';
			$resultat['data'][$i]->feuille_de_route_id = '<a href="feuille_de_route/detail/'.$id.'">'.$id.'</a>';
			$resultat['data'][$i]->date = '<a href="feuille_de_route/detail/'.$id.'">'.$resultat['data'][$i]->date.'</a>';
			$resultat['data'][$i]->ville_name = '<a href="feuille_de_route/detail/'.$id.'">'.$resultat['data'][$i]->ville_name.'</a>';
			$resultat['data'][$i]->secteur_name = '<a href="feuille_de_route/detail/'.$id.'">'.$resultat['data'][$i]->secteur_name.'</a>';
			$resultat['data'][$i]->nom_du_tri = '<a href="feuilles_de_tri/group/'.$feuilles_de_tri_id.'">'.$resultat['data'][$i]->nom_du_tri.'</a>';
			$resultat['data'][$i]->distributeur_name = '<a href="employes/detail/'.$resultat['data'][$i]->emp_id.'">'.$resultat['data'][$i]->distributeur_name.'</a>';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'feuille_de_route_id' => 'char',    
			'date' => 'date',
			'nom_du_tri' => 'char',
			'ville_name' => 'char',
			'secteur_name' => 'char',
			'distributeur_name' => 'char',			
        );

        return $filterable_columns;
    }	

     /******************************
     * Liste test mails Data
     ******************************/
    public function ville_liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $table = 't_feuille_de_routes';
        // première partie du select, mis en cache
        $this->db->start_cache();
        $this->db->select("
        		vil_id as RowID,
                feuille_de_route_id as id,
                vil_nom as ville_name,                        
                0 as fdr_ok,
                COUNT(*) as fdr_total,
                0 as tri_amorce,
                0 as tri_total
                ");
       
        $this->db->join('t_secteurs as tsc', 'secteur_id=tsc.sec_id', 'left');
        $this->db->join('t_villes tvi', 'tvi.vil_id=tsc.sec_ville', 'left');        
        $this->db->group_by('tsc.sec_ville');
        $this->db->stop_cache();
        // aliases
        $aliases = array(
        	'ville_name' => 'vil_nom'
        );

        $resultat = $this->_filtre($table, $this->ville_liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        for ($i = 0; $i < count($resultat['data']); $i++) {                                         
            $resultat['data'][$i]->number = $i + 1;
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function ville_liste_filterable_columns()
    {
        $filterable_columns = array(
            'ville_name' => 'char' 
        );

        return $filterable_columns;
    }

    public function detail($id)
    {
        //get detail fdr
        $query = $this->db->select("
                    tfr.secteur_id,
                    tfr.date,
                    tfr.date_distribution,
                    tsc.sec_nom as secteur_name,
                    tem.emp_adresse as distributeur_address,
                    tem.emp_cp as distributeur_code,
                    tem.emp_ville as distributeur_ville,
                    tem.emp_telephone1 as distributeur_phone,
                    tem.emp_prenom as distributeur_firstname,
                    tem.emp_nom as distributeur_lastname,
                    tem.emp_h_jour as nombre_par_jour,
                    tem.emp_ptc as poids_total_vehicule,
                    tem.emp_immatriculation as immatriculation,
                    vts.vts_type as secteur_type,
                    tfr.total_boites_hlm,
                    tfr.total_boites_res,
                    tfr.total_boites_pav
                ")
            ->from('t_feuille_de_routes tfr')
            ->join('t_secteurs tsc','tsc.sec_id=tfr.secteur_id')
            ->join('t_employes tem','tem.emp_id=tfr.distributeur')
            ->join('v_types_secteurs vts','vts.vts_id=tsc.sec_type','left')
            ->where('tfr.feuille_de_route_id', $id)
            ->get();

        $fdr = $query->row();

        //get addresses particulleres
        if($fdr) {
            $addresses = $this->db->select('adr_adresse as address,adr_cp as code,adr_ville as ville')
                                  ->where('adr_secteur',$fdr->secteur_id)
                                  ->get('t_adresses')->result();
            $fdr->addresses = $addresses;
        }

        return $fdr;

    }

    public function detail_ville($ville_id)
    {
        $this->db->select("
                feuille_de_route_id as RowID,
                feuille_de_route_id as id,
                sec_nom,                        
                date as date_limite,
                date_du_tri as date_feuille_de_tri,
                0 as fdr_ok,
                COUNT(*) as fdr_total,                
                ");
       
        $this->db->join('t_secteurs as tsc', 'secteur_id=tsc.sec_id', 'left');
        $this->db->join('t_feuilles_de_tri tft', 't_feuille_de_routes.feuilles_de_tri_id=tft.feuilles_de_tri_id','left');
        $this->db->where('tsc.sec_ville', $ville_id);
        $this->db->group_by('tsc.sec_id');
        $query = $this->db->get('t_feuille_de_routes');

        return $query->result();
    }

    public function remove($id)
    {
        return $this->db->delete('t_feuille_de_routes', array('feuille_de_route_id' => $id));
    }
}
// EOF
