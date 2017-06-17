<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date:
 * Time:
 */
class M_feuilles_de_tri extends MY_Model
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
                array('feuilles_de_tri_id', 'text', "id#", 'feuilles_de_tri_id'),
                array('vil_nom', 'text', "Ville", 'vil_nom'),
                array('date_du_tri', 'text', "Date du tri", 'date_du_tri'),
            ),
            'read.child'  => array(
                array('checkbox', 'text', "&nbsp;", 'checkbox'),
                array('dvi_date', 'date', "Date", 'dvi_date'),
                array('sec_nom', 'text', 'Secteur', 'sec_nom'),
                array('ctc_nom', 'text', "Client", 'ctc_nom'),
                array('cmd_reference', 'text', "Commande Ref", 'cmd_reference'),
                array('hlm', 'text', "HLM", 'hlm'),
                array('res', 'text', "RES", 'res'),
                array('pav', 'text', "PAV", 'pav'),
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
        $table = 't_feuilles_de_tri';
        // première partie du select, mis en cache
        $date_du_tri = formatte_sql_date("date_du_tri");
        $this->db->start_cache();
        $this->db->select("
                feuilles_de_tri_id as RowID,
                feuilles_de_tri_id,
                vil_nom,
                $date_du_tri");

        $this->db->join('t_villes', 't_villes.vil_id=ville_id');
        switch ($void) {
            case 'archived':
                $this->db->where($table . '.inactive is NOT NULL');
                break;
            case 'deleted':
                //$this->db->where($table.'.vil_inactif is NOT NULL');
                break;
            case 'all':
                break;
            default:
                $this->db->where($table . '.inactive is NULL');
                //$this->db->where($table.'.deleted is NULL');
                break;
        }

        $this->db->stop_cache();
        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table, $this->liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $id                                       = $resultat['data'][$i]->feuilles_de_tri_id;
            $resultat['data'][$i]->feuilles_de_tri_id = '<a href="' . site_url('feuilles_de_tri/group/' . $id) . '">' . $id . '</a>';
            $resultat['data'][$i]->checkbox           = '<input type="checkbox" name="ids[]" value="' . $id . '">';
            $resultat['data'][$i]->vil_nom            = '<a href="' . site_url('feuilles_de_tri/group/' . $id) . '">' . $resultat['data'][$i]->vil_nom . '</a>';
            $resultat['data'][$i]->date_du_tri        = '<a href="' . site_url('feuilles_de_tri/group/' . $id) . '">' . $resultat['data'][$i]->date_du_tri . '</a>';
        }

        return $resultat;
    }

    /******************************
     * Return filterable columns
     ******************************/
    public function liste_filterable_columns()
    {
        $filterable_columns = array(
            'feuilles_de_tri_id' => 'char',
            'vil_nom'            => 'char',
            'date_du_tri'        => 'date',
        );

        return $filterable_columns;
    }

    public function get_group($id)
    {
        $result = $this->db->select('vil_nom as ville, date_du_tri')
            ->join('t_villes', 'vil_id=ville_id')
            ->get_where('t_feuilles_de_tri', array('feuilles_de_tri_id' => $id))->row();

        //$date_du_tri = $feuilles_de_tri->date_du_tri;
        $date_du_tri = date("d/m/Y", strtotime($result->date_du_tri));

        $result->date_du_tri = $date_du_tri;

        return $result;

    }

    public function group_liste($void, $limit = 10, $offset = 1, $filters = null, $ordercol = 2, $ordering = "asc")
    {
        $ordercol = "secteur_id";

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $feuilles_de_tri = $this->db->get_where('t_feuilles_de_tri', array('feuilles_de_tri_id' => $id))->row();
        $ville_id        = $feuilles_de_tri->ville_id;
        $date_du_tri     = date("d/m/Y", strtotime($feuilles_de_tri->date_du_tri));
        $villes          = $this->db->get_where('t_villes', array('vil_id' => $ville_id))->row();
        $vil_nom         = $villes->vil_nom;

        $table = 't_distributions';

        $this->db->start_cache();
        $query = $this->db->select($table . '.*,
                                    sec_nom,
                                    cmd_reference,
                                    dvi_date,
                                    ctc_nom')
            ->from('t_distributions')
            ->join('t_devis', 'dvi_id=devis_id')
            ->join('t_commandes', 'cmd_devis=devis_id')
            ->join('t_contacts', 'ctc_id=dvi_client')
            ->join('t_secteurs', 'sec_id=secteur_id')
            ->join('t_villes', 'vil_id=sec_ville')
            ->join('t_feuilles_de_tri_article tfa', 'tfa.distribution_id=t_distributions.distribution_id', 'right')
            ->where('tfa.feuilles_de_tri_id', $id)
            ->where("t_secteurs.sec_ville", $ville_id);

        $this->db->stop_cache();
        // aliases
        $aliases = array(
        );

        $resultat = $this->_filtre($table, $this->group_liste_filterable_columns(), $aliases, $limit, $offset, $filters, $ordercol, $ordering);
        $this->db->flush_cache();

        //add checkbox into data
        for ($i = 0; $i < count($resultat['data']); $i++) {
            $resultat['data'][$i]->checkbox = '<input type="checkbox" name="ids[]" value="' . $resultat['data'][$i]->distribution_id . '">';
        }

        $resultat['date_du_tri'] = $date_du_tri;
        $resultat['vil_nom']     = $vil_nom;

        return $resultat;
    }

    public function group_liste_filterable_columns()
    {
        $filterable_columns = array(
            'dvi_date'      => 'date',
            'cmd_reference' => 'char',
            'sec_nom'       => 'char',
            'ctc_nom'       => 'char',
            'hlm'           => 'int',
            'res'           => 'int',
            'pav'           => 'pav',

        );

        return $filterable_columns;
    }

    public function remove($id)
    {
        $this->db->delete('t_feuilles_de_tri', array('feuilles_de_tri_id' => $id));
        return $this->db->delete('t_feuilles_de_tri_article', array('feuilles_de_tri_id' => $id));
    }

    public function check_exist_group($id)
    {
        $row = $this->db->get_where('t_feuilles_de_tri', array('feuilles_de_tri_id' => $id))->row();

        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function get_list_form_fdr($ids)
    {
        $query = $this->db->select('tdb.*,
                           tdb.secteur_id,
                           tdv.dvi_date as date_distribution,
                           tsc.sec_nom as secteur_name,
                           tct.ctc_nom as client_name')
            ->from('t_distributions tdb')
            ->join('t_secteurs tsc', 'tdb.secteur_id=tsc.sec_id')
            ->join('t_devis tdv', 'tdb.devis_id=tdv.dvi_id')
            ->join('t_contacts tct', 'tct.ctc_id=tdv.dvi_client')
            ->where_in('tdb.secteur_id', $ids)
            ->get();

        $result = $query->result();
        $data   = array();

        foreach ($result as $row) {
            $data[$row->secteur_id][] = $row;
        }

        return $data;
    }

    public function nouveau_fdr()
    {
        $feuilles_de_tri_id         = $this->input->post('feuilles_de_tri_id[]');
        $secteur_id                 = $this->input->post('secteur_id[]');
        $person                     = $this->input->post('person[]');
        $date_distribution          = $this->input->post('date_distribution[]');
        $date                       = $this->input->post('date[]');
        $distributeur               = $this->input->post('distributeur[]');
        $total_boites_hlm           = $this->input->post('total_boites_hlm[]');
        $total_boites_res           = $this->input->post('total_boites_res[]');
        $total_boites_pav           = $this->input->post('total_boites_pav[]');
        $boites_supplementaires_hlm = $this->input->post('boites_supplementaires_hlm[]');
        $boites_supplementaires_res = $this->input->post('boites_supplementaires_res[]');
        $boites_supplementaires_pav = $this->input->post('boites_supplementaires_pav[]');
        $temps_de_distribution_hlm  = $this->input->post('temps_de_distribution_hlm[]');
        $temps_de_distribution_res  = $this->input->post('temps_de_distribution_res[]');
        $temps_de_distribution_pav  = $this->input->post('temps_de_distribution_pav[]');

        $data = array();

        foreach ($secteur_id as $i => $id) {
            $data[] = array(
                'feuilles_de_tri_id'         => $feuilles_de_tri_id[$i],
                'secteur_id'                 => $id,
                'person'                     => $person[$i],
                'date_distribution'          => $date_distribution[$i],
                'date'                       => $date[$i],
                'distributeur'               => $distributeur[$i],
                'poids_consideration'        => $this->input->post('poids_consideration-' . $id),
                'tri_a_la_main'              => $this->input->post('tri_a_la_main-' . $id),
                'total_boites_hlm'           => $total_boites_hlm[$i],
                'total_boites_res'           => $total_boites_res[$i],
                'total_boites_pav'           => $total_boites_pav[$i],
                'boites_supplementaires_hlm' => $boites_supplementaires_hlm[$i],
                'boites_supplementaires_res' => $boites_supplementaires_res[$i],
                'boites_supplementaires_pav' => $boites_supplementaires_pav[$i],
                'temps_de_distribution_hlm'  => $temps_de_distribution_hlm[$i],
                'temps_de_distribution_res'  => $temps_de_distribution_res[$i],
                'temps_de_distribution_pav'  => $temps_de_distribution_pav[$i],
            );
        }

        $this->db->insert_batch('t_feuille_de_routes', $data);

        return true;
    }

    public function checkbox_persons()
    {
        $person_types = array("Salarié", "Sous-traitant");
        return $this->db->select('vtu_id as id, vtu_type as name')->where_in('vtu_type', $person_types)->order_by('vtu_type')->get('v_types_utilisateurs')->result();
    }

    public function person_liste($type = null)
    {
        if ($type == null) {
            $person_types = $this->checkbox_persons();
            $type         = $person_types[0]->id;
        }

        $query = $this->db->select('utl_id as id,emp_nom as name')
            ->from('t_utilisateurs')
            ->join('v_types_utilisateurs', 'vtu_id=utl_type', 'left')
            ->join('t_employes', 'emp_id=utl_employe', 'left')
            ->where('utl_type', $type)
            ->get();

        return $query->result();
    }

    /******************************
     *
     ******************************/
    public function archive($id)
    {
        return $this->_delete('t_feuilles_de_tri', $id, 'feuilles_de_tri_id', 'inactive');
    }

    /******************************
     *
     ******************************/
    public function unremove($id)
    {
        $data = array('inactive' => null);
        return $this->_update('t_feuilles_de_tri', $data, $id, 'feuilles_de_tri_id');
    }

}
// EOF
