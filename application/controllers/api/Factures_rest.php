<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic facture interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Factures_rest extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
       // $this->methods['factures_get']['limit'] = 500; // 500 requests per hour per facture/key
        //$this->methods['factures_post']['limit'] = 100; // 100 requests per hour per facture/key
        //$this->methods['factures_delete']['limit'] = 50; // 50 requests per hour per facture/key
        $this->load->model('m_factures');
    }

    public function index_get($id = null)
    {
        $devisId = $this->query('devisId');
        $order = $this->query("order") ? $this->query("order") : null;
        $sort = $this->query("sort") ? $this->query("sort") : "ASC";

        if($devisId) {
            $devisId = (int) $devisId;
            $factures = $this->m_factures->get_all_by_devis($order, $sort, $devisId);
        } else {
            // Factures from a data store e.g. database
            $factures = $this->m_factures->get_all($order, $sort);
        }

        // $id = $this->get('id');

        // If the id parameter doesn't exist return all the factures

        if ($id === NULL)
        {
            // Check if the factures data store contains factures (in case the database result returns NULL)
            if ($factures)
            {
                // Set the response and exit
                $this->response($factures, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No factures were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular facture.

        $id = (int) $id;

        // Validate the id.
        if ($id <= 0)
        {
            // Invalid id, set the response and exit.
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Get the facture from the array, using the id as key for retreival.
        // Usually a model is to be used for this.

        $facture = NULL;

        if ($id)
        {
            $facture = $this->m_factures->get($id);
        }

        if (!empty($facture))
        {
            $this->set_response($facture, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Facture could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
}
