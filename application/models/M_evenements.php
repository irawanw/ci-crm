<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_evenements extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('evn_date','datetime',"Date"),
                array('evn_objet','text',"Objet"),
                array('evn_duree','text',"Durée"),
                array('evn_contenu','text',"Contenu"),
                array('ctc_nom','ref',"Client",'contacts','evn_client','ctc_nom'),
                array('vnt_evenement','ref',"Nature",'v_natures'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'evn_date' => array("Date",'date','evn_date',true),
               'evn_nature' => array("Nature",'select',array('evn_nature','vnt_id','vnt_evenement'),false),
               'evn_objet' => array("Objet",'text','evn_objet',true),
               'evn_duree' => array("Durée",'text','evn_duree',false),
               'evn_contenu' => array("Contenu",'textarea','evn_contenu',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des évènements d'un client /prospect
    ******************************/
    public function liste_par_client($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $evn_objet = formatte_sql_lien('evenements/detail','evn_id','evn_objet');
        $evn_date = formatte_sql_date_heure('evn_date');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("evn_id AS RowID,evn_id,$evn_objet,$evn_date,evn_duree,evn_contenu,$ctc_nom,vnt_evenement",false);
        $this->db->join('t_contacts','ctc_id=evn_client','left');
        $this->db->join('v_natures','vnt_id=evn_nature','left');
        $this->db->where("evn_client",$pere);
        $this->db->where('evn_inactif is null');
        //$this->db->order_by("evn_date desc");
        $this->db->stop_cache();

        $table = 't_evenements';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_client_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_client_filterable_columns() {
    $filterable_columns = array(
            'evn_date'=>'datetime',
            'evn_objet'=>'char',
            'evn_duree'=>'char',
            'evn_contenu'=>'char',
            'ctc_nom'=>'char',
            'vnt_evenement'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouvel évènement
    ******************************/
    public function nouveau($data) {
        $id = $this->_insert('t_evenements', $data);
        return $id;
    }

    /******************************
    * Nouvel appel
    ******************************/
    public function nouvel_appel($data) {
        $id = $this->_insert('t_evenements', $data);
        return $id;
    }

    /******************************
    * Détail d'un évènement
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("evn_id,evn_client,ctc_nom,ctc_client_prospect,vcp_type,evn_date,evn_nature,vnt_evenement,evn_objet,evn_duree,evn_contenu",false);
        $this->db->join('t_contacts','ctc_id=evn_client','left');
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->join('v_natures','vnt_id=evn_nature','left');
        $this->db->where('evn_id',$id);
        $this->db->where('evn_inactif is null');
        $q = $this->db->get('t_evenements');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un évènement
    ******************************/
    public function maj($data,$id) {
        $q = $this->db->where('evn_id',$id)->get('t_evenements');
        $res =  $this->_update('t_evenements',$data,$id,'evn_id');
        return $res;
    }

/******************************
    * Suppression d'un évènement
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('evn_id',$id)->get('t_evenements');

            $res = $this->_delete('t_evenements',$id,'evn_id','evn_inactif');
        return $res;
    }

    /******************************
     * Nouvel email pour un correspondant
     ******************************/
    public function nouvel_email_cor($data) {

        // récupération des informations du correspondant et du contact
        $this->load->model('m_correspondants');
        $correspondant = $this->m_correspondants->detail($data['evn_client']);
        if ($correspondant === false) {
            return false;
        }
        $this->load->model('m_contacts');
        $contact = $this->m_contacts->detail($correspondant->cor_contact);
        if ($contact === false) {
            return false;
        }
        $data['evn_client'] = $correspondant->cor_contact;
        if ($correspondant->cor_email == '') {
            $this->session->set_flashdata('danger',"Le correspondant n'a pas d'adresse email");
            return false;
        }

        // envoi de l'email
        $this->load->library('email');
        $resultat = $this->email->send_one($correspondant->cor_email,'',$data['evn_objet'],$data['evn_contenu']);
        if ($resultat === false) {
            $this->session->set_flashdata('danger',"Erreur technique lors de l'envoi de l'email");
            return false;
        }
        return $this->_insert('t_evenements', $data);
    }

    /******************************
     * Nouvel email pour un contact
     ******************************/
    public function nouvel_email_ctc($data) {

        // récupération des informations du contact
        $this->load->model('m_contacts');
        $contact = $this->m_contacts->detail($data['evn_client']);
        if ($contact === false) {
            return false;
        }
        if ($contact->ctc_email == '') {
            $this->session->set_flashdata('danger',"Le contact n'a pas d'adresse email");
            return false;
        }

        // envoi de l'email
        $this->load->library('email');
        $resultat = $this->email->send_one($contact->ctc_email,'',$data['evn_objet'],$data['evn_contenu']);
        if ($resultat === false) {
            $this->session->set_flashdata('danger',"Erreur technique lors de l'envoi de l'email");
            return false;
        }
        return $this->_insert('t_evenements', $data);
    }

}
// EOF
