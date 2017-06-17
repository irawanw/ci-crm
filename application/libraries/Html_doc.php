<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//require_once APPPATH . '/third_party/vendor/autoload.php';

class Html_doc
{
    private $path_save;
    private $path_temp;
    private $path_download;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('export');
        $this->path_save     = $this->CI->config->item('path_save_contact_document');
        $this->path_temp     = $this->CI->config->item('path_temp_contact_document');
        $this->path_download = $this->CI->config->item('path_download_document');
        $this->CI->load->model(array('m_contacts', 'm_document_table'));

        $this->CI->load->library('parser');
    }

    public function generate_contact_document($template, $contact_ids, $content)
    {
        if (!is_dir($this->path_save)) {
            throw new MY_Exceptions_NoSuchFolder('Folder for generate not exist, please see your configuration export file');
        }

        if (!is_dir($this->path_temp)) {
            throw new MY_Exceptions_NoSuchFolder('Folder for generate not exist, please see your configuration export file');
        }

        $saveFolder   = $this->path_save;
        $downloadPath = base_url() . $this->path_download;
        $document_nom = $this->CI->m_contacts->getTemplate($template);

        if (!$document_nom) {
            throw new MY_Exceptions_NoSuchTemplate('Pas de document template ' . $template);
        }

        if (count($contact_ids) == 1) {
            /**
             * Generate Single Document
             */
            //$document_nom   = $this->CI->m_contacts->getTemplate($template);
            $contact_nom = str_replace(" ", "_", $this->CI->m_contacts->getContacts($contact_ids[0]));

            if (!$document_nom) {
                throw new MY_Exceptions_NoSuchTemplate('Pas de document template ' . $template);
            }

            if (!$contact_nom) {
                throw new MY_Exceptions_NoSuchRecord('Pas de contact ' . $contact_ids[0]);
            }

            $filename = str_replace(" ", "_", $document_nom) . '_' . $contact_nom . "_" . date("YmdHis");
            $filename = strtolower($filename);

            $content_html = '<div class="break">';
            $content_html .= $content;
            $content_html .= '</div>';

            if ($this->save_html_to_file($saveFolder, $content_html, $filename . '.html')) {
                if (file_exists($saveFolder . $filename . '.html')) {
                    $result = array(
                        'status'   => true,
                        'filename' => $filename,
                        'message'  => "Document html a été créé",
                    );

                    return $result;
                } else {
                    throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
                }
            } else {
                throw new MY_Exceptions_NoSuchFile('Pas de contact Documents html associée');
            }
        } else {
            $totalContact   = count($contact_ids);
            $success_docs   = 0;
            $path_files     = array();
            $path_downloads = array();
            $content_html   = '';

            foreach ($contact_ids as $id) {
                $contact_nom = str_replace(" ", "_", $this->CI->m_contacts->getContacts($id));
                if (!$contact_nom) {
                    throw new MY_Exceptions_NoSuchRecord('Pas de contact ' . $id);
                }

                $contact_detail = $this->CI->m_contacts->detail($id);
                foreach ($contact_detail as $key => $val) {
                    $contact_data[$key] = $val;
                }

                $content_temp = $this->CI->parser->parse_string($content, $contact_data);

                $content_html .= '<div class="break">';
                $content_html .= $content_temp;
                $content_html .= '</div>';
                $content_html .= '<hr class="no-print">';
            }

            $filename = str_replace(" ", "_", $document_nom) . "_" . date("YmdHis");
            $filename = strtolower($filename);

            if ($this->save_html_to_file($saveFolder, $content_html, $filename . '.html')) {
                if (file_exists($saveFolder . $filename . '.html')) {
                    $result = array(
                        'status'   => true,
                        'filename' => $filename,
                        'message'  => "Document html a été créé",
                    );

                    return $result;
                } else {
                    throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
                }
            } else {
                throw new MY_Exceptions_NoSuchFile('Pas de contact Documents html associée');
            }
        }
    }

    public function get_path_document_contact($filename)
    {
        $path      = $this->path_download;
        $path_file = $path . $filename . ".html";

        return $path_file;
    }

    public function delete_document_contact($filename)
    {
        $path = $this->path_save;
        unlink($path.$filename.".html");
    }

    protected function save_html_to_file($TempFolder, $content, $path)
    {
        $fullpath = $TempFolder . $path;
        return (bool) file_put_contents($fullpath, $content);
    }
}
