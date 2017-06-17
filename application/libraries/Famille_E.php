<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 07/07/15
 * Time: 11:37
 */
/**
 * Famille de catalogue emailing
 */
class Famille_E extends Famille_catalogue {
    const CODE = 'E';

    public function __construct() {
        parent::__construct();
    }

    /******************************
     * Exploitation du catalogue téléchargé
     ******************************/
    public function exploite($id,$data) {
        $erreurs = array();
        $articles = array();
        $codes = array();

        // contrôle des données
        foreach($data as $ligne) {
            $code_article = $ligne[0];
            if (array_key_exists($code_article,$codes)) {
                $codes[$code_article] += 1;
            }
            else {
                $codes[$code_article] = 1;
            }
            if (count($ligne) <10) {
                $erreurs[] = "Article $code_article : le catalogue Emailing doit avoir au moins 10 colonnes";
                return $erreurs;
            }
            if (!is_numeric($ligne[3]) OR $ligne[3] < 0) {
                $erreurs[] = "Article $code_article : le prix doit être un nombre positif ou null";
            }
            if ($ligne[4] != 'non' AND $ligne[4] !='oui') {
                $erreurs[] = "Article $code_article : la colonne 'Sélectionnable' doit être 'oui' ou 'non'";
            }
            $infos = array(
                $ligne[6], // quantité
                $ligne[7], // type de message
                $ligne[8], // statistiques
                $ligne[9], // type sending
                $ligne[10] // commentaire
            );

            // préparation de l'insertion
            $article = array(
                'art_code' => $ligne[0],
                'art_description' => $ligne[1],
                'art_libelle' => $ligne[2],
                'art_prix' => $ligne[3],
                'art_selection' => ($ligne[4]=='oui')?1:0,
                'art_prod' => $ligne[5],
                'art_data' => serialize($infos),
                'art_catalogue' => $id
            );
            $articles[] = $article;
        }

        // contrôle des doublons
        foreach ($codes as $c=>$n) {
            if ($n != 1) {
                $erreurs[] = "Le code article $c est présent $n fois";
            }
        }

        // sortie en erreur
        if (count($erreurs) > 0) {
            return $erreurs;
        }

        // suppression des éventuels articles précédents
        $this->CI->db->where('art_catalogue',$id)
            ->delete('t_articles');

        // insertion
        foreach ($articles as $article) {
            $this->CI->db->insert('t_articles',$article);
        }
        if ($this->CI->db->insert_id() == 0) {
            return false;
        }
        return true;
    }

    /******************************
     * En-tête pour l'exportation
     ******************************/
    public function en_tete() {
        $en_tete = parent::en_tete();
        $en_tete[] = 'Quantity';
        $en_tete[] = 'Type Message';
        $en_tete[] = 'Statistics';
        $en_tete[] = 'Type Sending';
        $en_tete[] = 'Commentaires';
        return $en_tete;
    }

    /******************************
     * Catalogue en service
     ******************************/
    public function catalogue() {
        $catalogue = parent::catalogue_en_service($this::CODE);
        if ($catalogue === false) {
            return false;
        }
		foreach ($catalogue as $article) {
            $data = unserialize($article->art_data);
            $article->art_qty 					= $data[0];
            if (count($data) >= 4) {
                $article->art_message = $data[1];
                $article->art_statistic = $data[2];
                $article->art_type_sending = $data[3];
            }
            else {
                $article->art_message = '';;
                $article->art_statistic = '';
                $article->art_type_sending = '';
            }
        }
		
		//filtering catalogue
		if($this->CI->input->get('email_count') != ''){
			
			$email_count 		= $this->CI->input->get('email_count');
			$filter_parameters 	= $this->CI->input->get('filter_parameters');
			$filter_parameters 	= json_decode($filter_parameters, true);			
			
			//we need loop each two items 
			//since the filter paramater formed by a pair of value
			$art_info			= '';
			for ($i=0; $i<count($filter_parameters)-1; $i+=2) {
				$temp 			= preg_split('/_/', $filter_parameters[$i]['name']);
				$filter_type	= $filter_parameters[$i]['value'];
				$value			= $filter_parameters[$i+1]['value'];
				$field_name		= $temp[2];
				$art_info 	   .= $field_name.' ('.$filter_type.') '.$value.'; ';
			}
			
			if($email_count>=1000000) 	$range = '>=1000001';
			elseif($email_count>300000) $range = '300001-1000000';
			elseif($email_count>50000) 	$range = '50001-300000';
			elseif($email_count>10000) 	$range = '10001-50000';
			elseif($email_count>1000) 	$range = '1001-10000';
			elseif($email_count<=1000) 	$range = '0-1000';						
			
			$i = 0;
			foreach($catalogue as $key=>$value){				
				$data = unserialize($value->art_data);
				if($data[0] == $range || $data[0] == '-'){
					
					$catalogue_filtered[$i] = $value;
					if($data[0] == $range){
						$catalogue_filtered[$i]->art_qty_insert = $email_count;
						$catalogue_filtered[$i]->art_info = $art_info;
					}
					else
						$catalogue_filtered[$i]->art_qty_insert = 1;
					
					$i++;
				} else {
					unset($catalogue[$key]);
				}				
			}
			$catalogue = $catalogue_filtered;
		}		
        return $catalogue;
    }

    /******************************
     * Préparation des emailing
     ******************************/
    public function preparation() {
        $data = array(
            'title' => "disponible",
            'page' => "_production/emailing/preparation",
            'values' => array(
            )
        );
        return $data;
    }

    /******************************
     * Suivi de production des emailing
     ******************************/
    public function suivi() {
        $data = array(
            'title' => "disponible",
            'page' => "_production/emailing/suivi",
            'values' => array(
            )
        );
        return $data;
    }

	public function comptage($param=0){	
		$dimitrios_url = "http://dev.bal-idf.com/contact_db/v.6.3/";
		
		$ch = curl_init();
		$url = $dimitrios_url."access/check.php";
		$fields = array(
					"login_key"		=> 'aD3',
					"login_pass" 	=> 'zxc'
				);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "login_key=aD3&login_pass=zxc");
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.txt');  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.txt');

		$filter = '';
		foreach($param as $key=>$value){
			if($value != '')
				$filter .= $key.'='.$value.'&';
		}		
		$data 	= "th_filter_function=getcount&".$filter;

		//execute post
		$result = curl_exec($ch);

		curl_setopt($ch, CURLOPT_URL, $dimitrios_url."db/get.data.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($ch);

		curl_close($ch);
		
		print_r($result);		
	}	
}
// EOF