<?php
if ( ! function_exists('form_unique_id'))
{
    /**
     * Generates a form unique ID
     *
     * @return	string
     */
    function form_unique_id($controller = '', $method = '', $prefix = 'form', $separator = '-')
    {
        static $ids = array();

        $baseName = $prefix;
        if ($controller != '') {
            $baseName .= $separator.$controller;
            if ($method != '') {
                $baseName .= $separator.$method;
            }
        }
        if (!isset($ids[$baseName])) {
            $ids[$baseName] = 1;
        } else {
            ++$ids[$baseName];
        }
        return $baseName.$separator.$ids[$baseName];
    }
}

//EOF