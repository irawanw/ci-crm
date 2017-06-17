<?php class Droits {
    protected $CI;

    /******************************
     * Vérification des droits
    ******************************/
    public function verifie() {
        $this->CI =& get_instance();

        // pas de vérification si appel ajax
        if ($this->CI->input->is_ajax_request()) return;

        // récupération des droits
        include 'application/config/droits.php';

        // récupération du profil
        $profil = $this->CI->session->profil;
        if (! isset($profil)) $profil = 'public';

        // récupération de la route
        $controleur = $this->CI->uri->rsegment(1);
        $methode = $this->CI->uri->rsegment(2);
        $cle = $controleur.'.'.$methode;

        // vérification
        if (array_key_exists($cle,$droits)) {
           if (! in_array($profil,$droits[$cle])) redirect('');
        }
    }
}
// EOF