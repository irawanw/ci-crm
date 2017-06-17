<?php

/**
 * Class MY_Controller
 *
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Session $session
 */
class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('MY_Exceptions');
    }

    /**
     * Hook to handle profiler information for AJAX calls
     *
     * @param $output
     */
    public function _output($output) {
        // If profiler is not enabled, or if we output HTML, or there is no profiler info
        // embedded in the output, then just "echo" the result normally
        $content_type = $this->output->get_header('Content-Type');
        if (!$this->output->enable_profiler
            || !strcasecmp($content_type, 'text/html')
            || !$content_type
            || strpos($output, '<div id="codeigniter_profiler"') === false) {
            echo $output;
        } else {
            // Reprocess the profiler info to be suitable for HTTP headers
            $profiler_pos = strpos($output, '<div id="codeigniter_profiler');
            $profiler_html = substr($output, $profiler_pos);
            $profiler_id = mt_rand();

            // Store the original HTML output for the profiler in a temporary session data
            $this->load->library('session');
            $this->session->set_tempdata('ci_profiler_'.$profiler_id, '<html><body>'.$profiler_html.'</body></html>', 60);
            header('X-CI-Profiler-Report: '.site_url('profiler/index/'.$profiler_id));

            // Display some profiler info as HTTP headers
            // (We only display data that are simple enough)
            $profiler = explode('<fieldset ', $profiler_html);
            $db = 1;

            foreach ($profiler as $html) {
                if (preg_match('/^id="ci_profiler_([^"]+)"/', $html, $matches)) {
                    $section = $matches[1];
                    // Exclude some profiler sections from the HTTP simple output
                    if (in_array($section, array('post', 'http_headers', 'get', 'csession', 'config'))) {
                        continue;
                    }
                    $section = str_replace('_', '-', $section);
                    $html = preg_replace('!<legend[^>]+>[^>]+</legend>!', '', '<a '.$html);
                    $TRs = explode('</tr>', $html);
                    if ($TRs) {
                        foreach ($TRs as $item => $TR) {
                            $TDs = explode('</td>', $TR);
                            $data = array();
                            foreach ($TDs as $TD) {
                                $text = trim(str_replace(array("\n", '&nbsp;'), array('', ' '), strip_tags($TD)));
                                if (strlen($text) > 0) {
                                    $data[] = $text;
                                }
                            }
                            if ($data) {
                                if ($section == 'benchmarks') {
                                    header('X-CI-Profiler-'.$section.'-'.$item.': '.implode(': ', $data), false);
                                } else {
                                    header('X-CI-Profiler-'.$section.': '.implode(': ', $data), false);
                                }
                            }
                        }
                    }
                } elseif (preg_match('/^[^>]+>\s*<legend[^>]+>[^<]+DATABASE:([^<]+)</', $html, $matches)) {
                    $text = trim(str_replace(array("\n", '&nbsp;'), array('', ' '), strip_tags($matches[1])), ' (');
                    if (strlen($text)) {
                        header('X-CI-Profiler-database-'.$db.': '.$text, false);
                        ++$db;
                    }
                }
            }

            // Display the output without the profiler data
            echo substr($output, 0, $profiler_pos);
        }
    }

    /**
     * Prépare le flashdata et la réponse AJAX pour des méthodes qui performent une action
     *
     * @param $ajax          boolean Est-ce une réponse AJAX ?
     * @param $success       boolean TRUE si succès, sinon FALSE
     * @param null $message  string  Message à afficher. Si aucun message n'est passé, les défauts sont :
     * <ul>
     *  <li>En cas d'erreur : "Un problème technique est survenu - veuillez réessayer ultérieurement"</li>
     *  <li>En cas de succès : "Opération effectuée"</li>
     * </ul>
     * @param null $notif    string  Niveau de notification approprié :
     * <ul>
     *  <li>danger</li>
     *  <li>warning</li
     *  <li>info</li>
     * </ul>
     * @param null $ajaxData array  Informations additionelles à renvoyer en AJAX
     *
     * @return void
     */
    protected function my_set_action_response($ajax, $success, $message = null, $notif = null, $ajaxData = null) {
        if ($notif === null) {
            $notif = ($success) ? 'info' : 'danger';
        }
        if (!$success) {
            if ($message === null) {
                $message = "Un problème technique est survenu - veuillez réessayer ultérieurement";
            }
            if (null === $this->session->flashdata($notif)) {
                $this->session->set_flashdata($notif, $message);
            }
        } else {
            if ($message === null) {
                $message = "Opération effectuée";
            }
            $this->session->set_flashdata($notif, $message);
        }
        if ($ajax) {
            $payload = array(
                "success" => $success,
                'notif'   => $notif,
                'message' => $message,
            );
            if (is_array($ajaxData) && !empty($ajaxData)) {
                $payload['data'] = $ajaxData;
            }
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($payload));
        }
    }

    /**
     * Prépare l'affichage d'un formulaire
     *
     * @param $ajax boolean    Est-ce une réponse AJAX ?
     * @param $data array      Les informations pour la vue
     * @param string $layout   La vue à utiliser pour une réponse non-AJAX
     *
     * @return void
     */
    protected function my_set_form_display_response($ajax, $data, $layout = 'layouts/standard') {
        if (!$ajax) {
            $this->load->view($layout,$data);
            return;
        }

        // Réponse AJAX
        if (!isset($data['modal'])) {
            $data['modal'] = true;
        }

        $html = $this->load->view("layouts/ajax", $data, true);

        // Si on réaffiche le formulaire après un appel POST
        // ça veut dire qu'il y a eu un échec lors de la validation
        if ($this->input->post()) {
            $message = validation_errors() ;
            $reponse = array(
                "data"=>$html,
                "success"=>false,
                "notif"=>"error",
                "message"=>$message,
            );
            log_message('debug', $message) ;
        }
        else {
            $reponse = array(
                "data"=>$html,
                "success"=>true,
            );
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($reponse));
    }

    /**
     * Prépare l'affichage d'un vue simple
     *
     * @param $ajax boolean    Est-ce une réponse AJAX ?
     * @param $data array      Les informations pour la vue
     * @param string $layout   La vue à utiliser pour une réponse non-AJAX
     *
     * @return void
     */
    protected function my_set_display_response($ajax, $data, $layout = 'layouts/standard') {
        if (!$ajax) {
            $this->load->view($layout,$data);
            return;
        }

        // Réponse AJAX
        if (!isset($data['modal'])) {
            $data['modal'] = true;
        }
        $html = $this->load->view("layouts/ajax", $data, true);
        $this->output->set_content_type('application/json')->set_output(json_encode(array("success"=>true, "data"=>$html)));
    }

    protected function my_controleur_from_class($className) {
        return strtolower(preg_replace('/([^\\\\]+)$/', '\1', $className));
    }

    /**
     * get columns header for export data
     * @param  [Array] $champs champs
     * @return [Array]         columns
     */
    protected function _get_export_columns($champs)
    {
        $columns = array();

        $exclude_column = array('invisible', 'checkbox');

        foreach($champs as $champ) {
            if($champ[1] != '') {
                if(array_key_exists(3, $champ)) {
                    if(!in_array($champ[3], $exclude_column)) {
                        $columns[] = array('name'=>$champ[0], 'format'=>$champ[1],'title'=> $champ[2]);
                    }
                } else {
                    if($champ[0] != 'RowID') {
                        $columns[] = array('name'=>$champ[0], 'format'=>$champ[1],'title'=> $champ[2]);
                    }
                }
            }
        }

        return $columns;
    }

    protected function _export_xls($params)
    {
        foreach($params as $paramKey => $paramValue) {
            ${$paramKey} = $paramValue;
        }

        $this->load->library('excel');
        $arrColumn = $this->_get_export_columns($columns);

        $arrExcel = array(
            'sNAMESS'=>'crm', 
            'sFILNAM'=> $filename,
            'columns'=>$arrColumn, 
            'records'=> $records,
        );

        if(isset($headers)) {
            $arrExcel['headers'] = $headers;
        }

        $url = $this->excel->export($arrExcel);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array('status'=> true, 'url' => $url)));
    }
}

// EOF