<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . '/third_party/vendor/autoload.php';

class Word
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

    public function create_document_contact($template, $contact_id, $content, $combine = false)
    {
        if (!is_dir($this->path_save)) {
            throw new MY_Exceptions_NoSuchFolder('Folder for generate not exist, please see your configuration export file');
        }

        if (!is_dir($this->path_temp)) {
            throw new MY_Exceptions_NoSuchFolder('Folder for generate not exist, please see your configuration export file');
        }

        $SaveFolder   = $this->path_save;
        $TempFolder   = $this->path_temp;
        $DownloadPath = base_url() . $this->path_download;
        $document_nom = $this->CI->m_contacts->getTemplate($template);
        if (!$document_nom) {
            throw new MY_Exceptions_NoSuchTemplate('Pas de document template ' . $template);
        }

        if (count($contact_id) == 1) {
            /**
             * Generate Single Document
             */
            //$document_nom   = $this->CI->m_contacts->getTemplate($template);
            $contact_nom = str_replace(" ", "_", $this->CI->m_contacts->getContacts($contact_id));

            if (!$document_nom) {
                throw new MY_Exceptions_NoSuchTemplate('Pas de document template ' . $template);
            }

            if (!$contact_nom) {
                throw new MY_Exceptions_NoSuchRecord('Pas de contact ' . $contact_id);
            }

            $filename = str_replace(" ", "_", $document_nom) . '_' . $contact_nom . "_" . date("YmdHis");

            if ($this->save_html_to_file($TempFolder, $content, $filename . '.html')) {
                //if file docx success generate
				putenv("PATH=/sbin:/bin:/usr/sbin:/usr/bin");
                $convert = exec('cd ' . $TempFolder . ' && unoconv -f doc -o ' . $SaveFolder . $filename . '.doc ' . $filename . '.html 2>&1', $output);

                if (file_exists($SaveFolder . $filename . '.doc')) {
                    unlink($TempFolder . $filename . '.html');
                    $fileUrl = $DownloadPath . $filename . '.doc';

                    $result = array(
                        'status'  => true,
                        'fileUrl' => $fileUrl,
                        'message' => "Document a été créé",
                    );

                    //insert to table t_document_table
                    $data = array(
                        'filename'      => $filename . ".doc",
                        'template'      => $template,
                        'content'       => $content,
                        'client_id'     => $contact_id,
                        'date_generate' => date("Y-m-d"),
                    );

                    $this->CI->m_document_table->nouveau($data);

                    return $result;
                } else {
                    throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
                }
            } else {
                throw new MY_Exceptions_NoSuchFile('Pas de contact Documents html associée');
            }
        } else {
            /**
             * Generate Many Document
             */

            $totalContact   = count($contact_id);
            $success_docs   = 0;
            $path_files     = array();
            $path_downloads = array();

            if ($combine == false) {
                foreach ($contact_id as $id) {
                    $contact_nom = str_replace(" ", "_", $this->CI->m_contacts->getContacts($id));
                    if (!$contact_nom) {
                        throw new MY_Exceptions_NoSuchRecord('Pas de contact ' . $id);
                    }

                    $contact_detail = $this->CI->m_contacts->detail($id);
                    foreach ($contact_detail as $key => $val) {
                        $contact_data[$key] = $val;
                    }

                    $filename     = str_replace(" ", "_", $document_nom) . '_' . $contact_nom . "_" . date("YmdHis");
                    $content_temp = $this->CI->parser->parse_string($content, $contact_data);

                    if ($this->save_html_to_file($TempFolder, $content_temp, $filename . '.html')) {
						putenv("PATH=/sbin:/bin:/usr/sbin:/usr/bin");
                        $convert = exec('cd ' . $TempFolder . ' && unoconv -f doc -o ' . $SaveFolder . $filename . '.doc ' . $filename . '.html 2>&1', $output);
                        //if file docx success generate
                        if (file_exists($SaveFolder . $filename . '.doc')) {
                            unlink($TempFolder . $filename . '.html');
                            ++$success_docs;

                            $path_files[]     = $SaveFolder . $filename . '.doc';
                            $path_downloads[] = $DownloadPath . $filename . '.doc';

                            //add to zip content
                            $filedata = read_file($SaveFolder . $filename . '.doc');
                            $this->CI->zip->add_data($filename . ".doc", $filedata);
                            //saving data
                            $data = array(
                                'filename'      => $filename . ".doc",
                                'template'      => $template,
                                'content'       => $content,
                                'client_id'     => $id,
                                'date_generate' => date("Y-m-d"),
                            );
                            $this->CI->m_document_table->nouveau($data);
                        } else {
                            throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
                        }
                    } else {
                        throw new MY_Exceptions_NoSuchFile('Pas de contact Documents html associée');
                    }
                }

                $zip_name = "documents_contact_" . rand() . ".zip";
                //$filezip_name = "./fichiers/contacts/documents/".$zip_name;
                $filezip_name = $SaveFolder . $zip_name;
                $zip_content  = $this->CI->zip->get_zip();

                //check file zip exist or not
                if (file_put_contents($filezip_name, $zip_content) != false) {
                    $result = array(
                        'status'  => true,
                        'fileUrl' => $DownloadPath . $zip_name,
                        'message' => "Document a été créé",
                    );
                    return $result;
                } else {
                    throw new MY_Exceptions_NoSuchFile('Pas de contact Documents zip associée');
                }
            } else {
            	$content_html = '';
                foreach ($contact_id as $id) {
                    $contact_nom = str_replace(" ", "_", $this->CI->m_contacts->getContacts($id));
                    if (!$contact_nom) {
                        throw new MY_Exceptions_NoSuchRecord('Pas de contact ' . $id);
                    }

                    $contact_detail = $this->CI->m_contacts->detail($id);
                    foreach ($contact_detail as $key => $val) {
                        $contact_data[$key] = $val;
                    }

                    $content_temp = $this->CI->parser->parse_string($content, $contact_data);

                    $content_html .= $content_temp;
                    $content_html .= '<br clear="all" style="page-break-before:always" />';
                }

                $template_combine = $this->get_template_combine();
                $content_combine  = $this->CI->parser->parse_string($template_combine, array('content' => $content_html));
                $filename         = str_replace(" ", "_", $document_nom) . "_" . date("YmdHis");

                if ($this->save_html_to_file($TempFolder, $content_combine, $filename . '.html')) {
                    //if file docx success generate
					putenv("PATH=/sbin:/bin:/usr/sbin:/usr/bin");
                    $convert = exec('cd ' . $TempFolder . ' && unoconv -f doc -o ' . $SaveFolder . $filename . '.doc ' . $filename . '.html 2>&1', $output);

                    if (file_exists($SaveFolder . $filename . '.doc')) {
                        unlink($TempFolder . $filename . '.html');
                        $fileUrl = $DownloadPath . $filename . '.doc';

                        $result = array(
                            'status'  => true,
                            'fileUrl' => $fileUrl,
                            'message' => "Document a été créé",
                        );

                        //insert to table t_document_table
                        $data = array(
                            'filename'      => $filename . ".doc",
                            'template'      => $template,
                            'content'       => $content,
                            'client_id'     => 0,
                            'date_generate' => date("Y-m-d"),
                        );

                        $this->CI->m_document_table->nouveau($data);

                        return $result;
                    } else {
                        throw new MY_Exceptions_NoSuchFile('Pas de contact Documents associée');
                    }
                } else {
                    throw new MY_Exceptions_NoSuchFile('Pas de contact Documents html associée');
                }

            }
        }
    }

    protected function save_html_to_file($TempFolder, $content, $path)
    {
        $fullpath = $TempFolder . $path;
        return (bool) file_put_contents($fullpath, $content);
    }

    public function get_template_combine()
    {
        $template = <<<'EOT'
<html>
<head>
	<title></title>
</head>
<body>
	{content}
</body>
</html>
EOT;

        return $template;
    }
}
