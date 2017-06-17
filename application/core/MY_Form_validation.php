<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Form_validation extends CI_Form_validation {
	/**
	 * Valid Emails
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if (!preg_match('/\n|,/', $str))
		{
			return $this->valid_email(trim($str));
		}

		foreach (preg_split('/\n|,/', $str) as $email)
		{
			if (trim($email) !== '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	/**
	 * Valid Urls
	 * @param  string
	 * @return bool
	 */
    public function valid_urls($str)
	{
		if (!preg_match('/\n|,/', $str))
		{
			return $this->valid_url(trim($str));
		}

		foreach (preg_split('/\n|,/', $str) as $url)
		{
			if (trim($url) !== '' && $this->valid_url(trim($url)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
     * Validate URL format
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function valid_url($str){
        $pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";              
        if (!preg_match($pattern, $str)){            
            return FALSE;
        }
 		return TRUE; 		       
    }     

	/* check if valid domain */
	public function valid_domain($str) {
		return (!empty($str) && preg_match('/(?=^.{1,254}$)(^(?:(?!\d|-)[a-z0-9\-]{1,63}(?<!-)\.)+(?:[a-z]{2,})$)/i', $str) > 0);
	}		
}