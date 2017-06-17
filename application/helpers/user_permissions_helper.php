<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('viewable_fields')) {
    function viewable_fields($module, $type)
    {
        $CI = &get_instance();

        $CI->load->model('m_users_permissions');

        $username = $CI->session->utl_login;

        if($module) {
            $fields = $CI->m_users_permissions->get_viewable_fields($module, $type, $username);
            return $fields;
        } else {
            return array();
        }
    }
}

if (!function_exists('verify_viewable_field')) {
    function verify_viewable_field($field, $viewable_fields)
    {
        $total_viewable_fields = count($viewable_fields);
        if ($total_viewable_fields == 0) {
            $is_viewable = true;
        } else {
            $is_viewable = in_array($field, $viewable_fields);
        }

        return $is_viewable;
    }
}
