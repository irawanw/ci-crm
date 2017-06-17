<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Remap_t_utilisateurs extends CI_Migration
{
    public function up()
    {
        //get all users
        $query = $this->db->select('utl_id,utl_login,utl_actif,utl_employe')->where('utl_actif', 1)->get('t_utilisateurs');

        if ($query->num_rows() > 0) {
            $date = date("Y-m-d");
            foreach ($query->result() as $user) {
                //check if have employee
                $employee_id = $user->utl_employe;
                if ($employee_id != 0) {
                    //set actif selected employee
                    $this->db->update('t_employes', array('emp_etat' => 1), array('emp_id' => $employee_id));
                } else {
                    //create employee
                    $data = array(
                        'emp_nom'         => $user->utl_login,
                        'emp_etat'        => 1,
                        'emp_civilite'    => 0,
                        'emp_date_entree' => $date,
                        'emp_date_sortie' => $date,
                    );

                    $this->db->insert('t_employes', $data);
                    $id = $this->db->insert_id();

                    //set relation user with employee
                    if($id) {
                      $this->db->update('t_utilisateurs', array('utl_employe' => $id), array('utl_id' => $user->utl_id));
                    }
                }
            }
        }
    }

    public function down()
    {

    }
}

// EOF
