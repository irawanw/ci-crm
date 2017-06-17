<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic devis interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Devis_rest extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
       // $this->methods['devises_get']['limit'] = 500; // 500 requests per hour per devis/key
        //$this->methods['devises_post']['limit'] = 100; // 100 requests per hour per devis/key
        //$this->methods['devises_delete']['limit'] = 50; // 50 requests per hour per devis/key
        $this->load->model('m_devis');
    }

    public function index_get($id = null)
    {
        $clientId = $this->query('clientId');
        $order = $this->query('order') ? $this->query('order') : null;
        $sort = $this->query('sort') ? $this->query('sort') : "ASC";

        if($clientId) {
            $clientId = (int) $clientId;
            $devises = $this->m_devis->get_all_by_client($order, $sort, $clientId);
        } else {
            // Deviss from a data store e.g. database
            $devises = $this->m_devis->get_all($order, $sort);
        }

        // $id = $this->get('id');

        // If the id parameter doesn't exist return all the devises

        if ($id === NULL)
        {
            // Check if the devises data store contains devises (in case the database result returns NULL)
            if ($devises)
            {
                // Set the response and exit
                $this->response($devises, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No devises were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular devis.

        $id = (int) $id;

        // Validate the id.
        if ($id <= 0)
        {
            // Invalid id, set the response and exit.
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Get the devis from the array, using the id as key for retreival.
        // Usually a model is to be used for this.

        $devis = NULL;

        if ($id)
        {
            $devis = $this->m_devis->get($id);
        }

        if (!empty($devis))
        {
            $this->set_response($devis, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Devis could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
}
