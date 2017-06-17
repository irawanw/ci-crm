<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
* Created by PhpStorm.
* User: bernardtrevisan_imac
* Date: 
* Time: 
*/
class M_correspondants extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_champs($type)
    {
        $champs = array(
            'read' => array(
                array('vcv_civilite','ref',"Civilite",'v_civilites'),
                array('cor_prenom','text',"Prénom"),
                array('cor_nom','text',"Nom"),
                array('cor_login','text',"Login"),
                array('cor_description','text',"Description"),
                array('cor_adresse','text',"Adresse"),
                array('cor_cp','text',"Code postal"),
                array('cor_ville','text',"Ville"),
                array('cor_complement','text',"Complément adresse"),
                array('cor_telephone1','text',"Téléphone 1"),
                array('cor_telephone2','text',"Téléphone 2"),
                array('cor_fax','text',"Fax"),
                array('cor_email','text',"Email"),
                array('cor_msg_distr','text',"Messages de distribution"),
                array('cor_msg_cmd','text',"Messages de commandes"),
                array('ctc_nom','ref',"Contact",'contacts','cor_contact','ctc_nom'),
                array('vcp_type','ref',"Type",'v_clients_prospects'),
                array('RowID','text',"__DT_Row_ID")
            ),
            'write' => array(
               'cor_contact' => array("Contact",'select',array('cor_contact','ctc_id','ctc_nom'),false),
               'cor_civilite' => array("Civilite",'radio-h',array('cor_civilite','vcv_id','vcv_civilite'),false),
               'cor_prenom' => array("Prénom",'text','cor_prenom',false),
               'cor_nom' => array("Nom",'text','cor_nom',false),
               'cor_login' => array("Login",'text','cor_login',false),
               'cor_description' => array("Description",'textarea','cor_description',false),
               'cor_adresse' => array("Adresse",'textarea','cor_adresse',false),
               'cor_cp' => array("Code postal",'number','cor_cp',false),
               'cor_ville' => array("Ville",'text','cor_ville',false),
               'cor_complement' => array("Complément adresse",'text','cor_complement',false),
               'cor_telephone1' => array("Téléphone 1",'number','cor_telephone1',false),
               'cor_telephone2' => array("Téléphone 2",'number','cor_telephone2',false),
               'cor_fax' => array("Fax",'number','cor_fax',false),
               'cor_email' => array("Email",'email','cor_email',false),
               'cor_msg_distr' => array("Messages de distribution",'checkbox','cor_msg_distr',false),
               'cor_msg_cmd' => array("Messages de commandes",'checkbox','cor_msg_cmd',false)
            )
        );

        return $champs[$type];
    }

    /******************************
    * Liste des correspondants
    ******************************/
    public function liste($void,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $cor_nom = formatte_sql_lien('correspondants/detail','cor_id','cor_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("cor_id AS RowID,cor_id,$cor_nom,vcv_civilite,cor_prenom,cor_login,cor_description,cor_adresse,cor_cp,cor_ville,cor_complement,cor_telephone1,cor_telephone2,cor_fax,cor_email,cor_msg_distr,cor_msg_cmd,$ctc_nom,vcp_type",false);
        $this->db->join('v_civilites','vcv_id=cor_civilite','left');
        $this->db->join('t_contacts','ctc_id=cor_contact','left');
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->where('cor_inactif is null');
        //$this->db->order_by("cor_nom asc");
        $this->db->stop_cache();

        $table = 't_correspondants';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_filterable_columns() {
        $filterable_columns = array(
            'vcv_civilite'=>'char',
            'cor_prenom'=>'char',
            'cor_nom'=>'char',
            'cor_login'=>'char',
            'cor_description'=>'char',
            'cor_adresse'=>'char',
            'cor_cp'=>'char',
            'cor_ville'=>'char',
            'cor_complement'=>'char',
            'cor_telephone1'=>'char',
            'cor_telephone2'=>'char',
            'cor_fax'=>'char',
            'cor_email'=>'char',
            'cor_msg_distr'=>'char',
            'cor_msg_cmd'=>'char',
            'ctc_nom'=>'char',
            'vcp_type'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
     * Liste Option
     ******************************/
    public function liste_option($pere = null)
    {
        $this->db->select('cor_id as id, CONCAT(cor_nom, ", ", cor_prenom) as value')
            ->where('cor_inactif IS NULL')
            ->order_by('cor_nom', 'ASC');
        if ($pere > 0) {
            $this->db->where('cor_contact', $pere);
        }
        $sql = $this->db->get('t_correspondants');
        if ($sql->num_rows() > 0) {
            $resultat = $sql->result();
            return $resultat;
        }
        return array();
    }

    /******************************
    * Liste des correspondants d'un contact
    ******************************/
    public function liste_par_contact($pere,$limit=10, $offset=1, $filters=NULL, $ordercol=2, $ordering="asc") {

        // première partie du select, mis en cache
        $this->db->start_cache();

        // lecture des informations
        $cor_nom = formatte_sql_lien('correspondants/detail','cor_id','cor_nom');
        $ctc_nom = formatte_sql_lien('contacts/detail','ctc_id','ctc_nom');
        $this->db->select("cor_id AS RowID,cor_id,$cor_nom,$ctc_nom,vcp_type,cor_prenom,cor_adresse,cor_ville,cor_telephone1,cor_telephone2,cor_fax,cor_email",false);
        $this->db->join('t_contacts','ctc_id=cor_contact','left');
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->where("cor_contact",$pere);
        $this->db->where('cor_inactif is null');
        //$this->db->order_by("cor_nom asc");
        $this->db->stop_cache();

        $table = 't_correspondants';

        // aliases
        $aliases = array(

        );

        $resultat = $this->_filtre($table,$this->liste_par_contact_filterable_columns(),$aliases,$limit,$offset,$filters,$ordercol,$ordering);
        $this->db->flush_cache();

        return $resultat;
    }

    /******************************
    * Return filterable columns
    ******************************/
    public function liste_par_contact_filterable_columns() {
    $filterable_columns = array(
            'ctc_nom'=>'char',
            'vcp_type'=>'char',
            'cor_nom'=>'char',
            'cor_prenom'=>'char',
            'cor_adresse'=>'char',
            'cor_ville'=>'char',
            'cor_telephone1'=>'char',
            'cor_telephone2'=>'char',
            'cor_fax'=>'char',
            'cor_email'=>'char'
        );
        return $filterable_columns;
    }

    /******************************
    * Nouveau correspondant
    ******************************/
    public function nouveau($data) {
        if (isset($data['cor_mot_de_passe'])) {
            $salt = substr(md5(uniqid(rand(), true)), 0, 6);
            $data['cor_mot_de_passe'] = sha1($data['cor_mot_de_passe'].$salt);
            $data['sys_salt'] = $salt;
        }
        $id = $this->_insert('t_correspondants', $data);
        return $id;
    }

    /******************************
    * Détail d'un correspondant
    ******************************/
    public function detail($id) {

        // lecture des informations
        $this->db->select("cor_id,cor_contact,ctc_nom,ctc_client_prospect,vcp_type,cor_civilite,vcv_civilite,cor_nom,cor_prenom,cor_description,cor_adresse,cor_cp,cor_ville,cor_complement,cor_telephone1,cor_telephone2,cor_fax,cor_email,cor_msg_distr,cor_msg_cmd",false);
        $this->db->join('t_contacts','ctc_id=cor_contact','left');
        $this->db->join('v_clients_prospects','vcp_id=ctc_client_prospect','left');
        $this->db->join('v_civilites','vcv_id=cor_civilite','left');
        $this->db->where('cor_id',$id);
        $this->db->where('cor_inactif is null');
        $q = $this->db->get('t_correspondants');
        if ($q->num_rows() > 0) {
            $resultat = $q->row();
            return $resultat;
        }
        else {
            return null;
        }
    }

    /******************************
    * Mise à jour d'un correspondant
    ******************************/
    public function maj($data,$id) {
        if (array_key_exists('cor_mot_de_passe',$data)) {
            if ($data['cor_mot_de_passe'] != '') {
                $q = $this->db->select('sys_salt')
                    ->where('cor_id',$id)
                    ->get('t_correspondants');
                if ($q->num_rows() > 0) {
                    $data['cor_mot_de_passe'] = sha1($data['cor_mot_de_passe'].$q->row()->sys_salt);
                }
            }
            else {
                unset($data['cor_mot_de_passe']);
            }
        }
        $q = $this->db->where('cor_id',$id)->get('t_correspondants');
        $res =  $this->_update('t_correspondants',$data,$id,'cor_id');
        return $res;
    }

/******************************
    * Suppression d'un correspondant
    ******************************/
    public function suppression($id) {
        $q = $this->db->where('cor_id',$id)->get('t_correspondants');

            $res = $this->_delete('t_correspondants',$id,'cor_id','cor_inactif');
        return $res;
    }

}
// EOF
