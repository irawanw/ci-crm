<?php

/**
 * @file
 * 
 * Functions to support the documentation and examples.
 *   
 */

/**
 * Computes base root, base path, and base url.
 * 
 * This code is adapted from Drupal function conf_init, see:
 * http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/conf_init/6
 * 
 */
function htmltodocx_paths() {
  
  if (!isset($_SERVER['SERVER_PROTOCOL']) || ($_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.0' && $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.1')) {
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
  }

  if (isset($_SERVER['HTTP_HOST'])) {
    // As HTTP_HOST is user input, ensure it only contains characters allowed
    // in hostnames. See RFC 952 (and RFC 2181).
    // $_SERVER['HTTP_HOST'] is lowercased here per specifications.
    $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
    if (!htmltodocx_valid_http_host($_SERVER['HTTP_HOST'])) {
      // HTTP_HOST is invalid, e.g. if containing slashes it may be an attack.
      header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
      exit;
    }
  }
  else {
    // Some pre-HTTP/1.1 clients will not send a Host header. Ensure the key is
    // defined for E_ALL compliance.
    $_SERVER['HTTP_HOST'] = '';
  }

  // Create base URL
  $base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

  $base_url = $base_root .= '://' . $_SERVER['HTTP_HOST'];

  // $_SERVER['SCRIPT_NAME'] can, in contrast to $_SERVER['PHP_SELF'], not
  // be modified by a visitor.
  if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
    $base_path = "/$dir";
    $base_url .= $base_path;
    $base_path .= '/';
  }
  else {
    $base_path = '/';
  }
  
  return array(
    'base_path' => $base_path,
    'base_url' => $base_url,
    'base_root' => $base_root,
  );
  
}

/**
 * Check for valid http host.
 * 
 * This code is adapted from function drupal_valid_http_host, see:
 * http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_valid_http_host/6
 * 
 * @param mixed $host
 * @return int
 */
function htmltodocx_valid_http_host($host) {
  return preg_match('/^\[?(?:[a-z0-9-:\]_]+\.?)+$/', $host);
}

function htmltodocx_styles_example() {
  
  // Set of default styles - 
  // to set initially whatever the element is:
  // NB - any defaults not set here will be provided by PHPWord.
  $styles['default'] = 
    array (
      'size' => 11,
    );
  
  // Element styles:
  // The keys of the elements array are valid HTML tags;
  // The arrays associated with each of these tags is a set
  // of PHPWord style definitions.
  $styles['elements'] = 
    array (
      'h1' => array (
        'bold' => TRUE,
        'size' => 20,
        ),
      'h2' => array (
        'bold' => TRUE,
        'size' => 15,
        'spaceAfter' => 150,
        ),
      'h3' => array (
        'size' => 12,
        'bold' => TRUE,
        'spaceAfter' => 100,
        ),
      'li' => array (
        ),
      'ol' => array (
        'spaceBefore' => 200,
        ),
      'ul' => array (
        'spaceAfter' => 150,
        ),
      'b' => array (
        'bold' => TRUE,
        ),
      'em' => array (
        'italic' => TRUE,
        ),
      'i' => array (
        'italic' => TRUE,
        ),
      'strong' => array (
        'bold' => TRUE,
        ),
      'b' => array (
        'bold' => TRUE,
        ),
      'sup' => array (
        'superScript' => TRUE,
        'size' => 6,
        ), // Superscript not working in PHPWord 
      'u' => array (
        'underline' => PHPWord_Style_Font::UNDERLINE_SINGLE,
        ),
      'a' => array (
        'color' => '0000FF',
        'underline' => PHPWord_Style_Font::UNDERLINE_SINGLE,
        ),
      'table' => array (
        // Note that applying a table style in PHPWord applies the relevant style to
        // ALL the cells in the table. So, for example, the borderSize applied here
        // applies to all the cells, and not just to the outer edges of the table:
        'borderColor' => '000000',  
        'borderSize' => 10,
        ),
      'th' => array (
        'borderColor' => '000000',
        'borderSize' => 10,
        ),
      'td' => array (
        'borderColor' => '000000',
        'borderSize' => 10,
        ),
      );
      
  // Classes:
  // The keys of the classes array are valid CSS classes;
  // The array associated with each of these classes is a set
  // of PHPWord style definitions.
  // Classes will be applied in the order that they appear here if
  // more than one class appears on an element.
  $styles['classes'] = 
    array (
      'underline' => array (
        'underline' => PHPWord_Style_Font::UNDERLINE_SINGLE,
        ),
       'purple' => array (
        'color' => '901391',
       ),
       'green' => array (
        'color' => '00A500',
       ),
      );
  
  // Inline style definitions, of the form:
  // array(css attribute-value - separated by a colon and a single space => array of
  // PHPWord attribute value pairs.    
  $styles['inline'] = 
    array(
      'text-decoration: underline' => array (
        'underline' => PHPWord_Style_Font::UNDERLINE_SINGLE,
      ),
      'vertical-align: left' => array (
        'align' => 'left',
      ),
      'vertical-align: middle' => array (
        'align' => 'center',
      ),
      'vertical-align: right' => array (
        'align' => 'right',
      ),
    );
    
  return $styles;
}