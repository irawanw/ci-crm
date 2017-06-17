<?php
// ------------------------------------------------------------------------

if (!function_exists('anchor_popup')) {
    /**
     * Anchor Link - Pop-up version
     *
     * Creates an anchor based on the local URL. The link
     * opens a new window based on the attributes specified.
     *
     * @param string                $uri        the URL
     * @param string                $title      the link title
     * @param array|string|boolean  $attributes any attributes for the popup window
     * <ul>
     *  <li>FALSE for a regular new fullsize window or tab</li>
     *  <li>array of key / value pairs</li>
     *  <li>A formatted string of properties suitable for a JavaScript windows.open() call</li>
     * </ul>
     * @param string|array          $cssClasses A list of CSS classes to assign to the &lt;a> tag
     *
     * @return string
     *
     * @link http://www.w3schools.com/jsref/met_win_open.asp
     */
    function anchor_popup($uri = '', $title = '', $attributes = FALSE, $cssClasses = null)
    {
        $title = (string)$title;
        $site_url = preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri);

        if ($title === '') {
            $title = $site_url;
        }

        if (is_array($cssClasses)) {
            $cssClasses = implode(' ', $cssClasses);
        }
        $cssClasses = trim($cssClasses);
        $class = ($cssClasses != '') ? ' class="'.$cssClasses.'"' : '';

        if ($attributes === FALSE) {
            return '<a href="' . $site_url . '" target="_blank"'.$class.'>' . $title . '</a>';
        }

        if (!is_array($attributes)) {
            $attributes = array($attributes);

            $window_name = '_blank';
        } elseif (!empty($attributes['window_name'])) {
            $window_name = $attributes['window_name'];
            unset($attributes['window_name']);
        } else {
            $window_name = '_blank';
        }

        foreach (array('width' => '800', 'height' => '600', 'scrollbars' => 'yes', 'menubar' => 'no', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0') as $key => $val) {
            $atts[$key] = isset($attributes[$key]) ? $attributes[$key] : $val;
            unset($attributes[$key]);
        }

        $attributes = _stringify_attributes($attributes);

        return '<a href="' . $site_url
            . '" onclick="window.open(\'' . $site_url . "', '" . $window_name . "', '" . _stringify_attributes($atts, TRUE) . "'); return false;\""
            . $attributes . $class . '>' . $title . '</a>';
    }
}

//EOF