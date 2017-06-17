<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: jausions
 * Date: 25-Feb-17
 * Time: 19:38
 */

if ( ! function_exists('view_exists'))
{
    function view_exists($view)
    {
        return file_exists(FCPATH.'application'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$view.'.php');
    }
}

// EOF