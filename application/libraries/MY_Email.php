<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 24/08/15
 * Time: 10:10
 */
class MY_Email extends CI_Email {

    /******************************
     * Envoi d'un email à un destinataire
     ******************************/
    public function send_one($to,$from,$subject,$body,$pj='') {

        // modification éventuelle du destinataire
        $CI =& get_instance();
        $production = $CI->session->utl_en_production;
        if ($production == 0) {
            $subject = $subject." (to $to)";
            $to = $CI->config->item('email_to');
        }

        // envoi du message
        if ($from == '') {
            $from = $CI->config->item('email_from');
        }
        $config['protocol'] = $CI->config->item('email_protocol');
        $config['smtp_host'] = $CI->config->item('email_smtp_host');
        $smtp_user = $CI->config->item('email_smtp_user');
        if ($smtp_user) {
            $config['smtp_user'] = $smtp_user;
            $config['smtp_pass'] = $CI->config->item('email_smtp_pass');
        }
        $config['mailtype'] = 'html';
        $this->initialize($config);

        $this->from($from);
        $this->to($to);
        $this->subject($subject);
        $body_html = str_replace("\n","<br />",$body);
        $this->message($body_html);
        $this->set_alt_message($body);
        if ($pj != '') {
            $this->attach($pj);
        }
        $res = $this->send();
        return $res;
    }
}
// EOF