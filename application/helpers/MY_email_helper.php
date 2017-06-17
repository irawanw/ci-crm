<?php defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if ( ! function_exists('get_email_address'))
{
    /**
     * Tries to locate an address email for a "correspondant" or a contact
     *
     * @param integer $correspondant_id
     * @param integer $contact_id
     * @return NULL|string
     */
    function get_email_address($correspondant_id = null, $contact_id = null)
    {
        $CI =& get_instance();
        $CI->load->database();

        $email = null;
        if ($correspondant_id) {
            $q = $CI->db->where('cor_id', $correspondant_id)
                ->where('LENGTH(cor_email)>0')
                ->get('t_correspondants');
            if ($q->num_rows() == 1) {
                $email = $q->row()->cor_email;
            }
        }

        if ($email === null && $contact_id) {
            $q = $CI->db->where('ctc_id', $contact_id)
                ->where('LENGTH(ctc_email)>0')
                ->get('t_contacts');
            if ($q->num_rows() == 1) {
                $email = $q->row()->ctc_email;
            }
        }

        return $email;
    }
}
