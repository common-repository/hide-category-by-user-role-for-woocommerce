<?php

/**
 * Operations of the plugin are included here.
 *
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/*
*  WOO Get Categories
*/

function tswchc_get_woo_categories() {

  $taxonomy     = 'product_cat';
  $orderby      = 'name';
  $empty        = 0;

  $args = array(
    'taxonomy'     => $taxonomy,
    'orderby'      => $orderby,
    'hide_empty'   => $empty
  );

  return get_categories($args);
}

/*
*  TS-WCHC Get Category Hierarchy
*/


function tswchc_get_categories_hierarchy() {

  $categories = tswchc_get_woo_categories();

  foreach ($categories as $key => $category) {

    $category->children = tswchc_get_woo_category_childs($category);
  }

  return $categories;
}

function tswchc_get_available_roles() {

  global $wp_roles;

  $wp_roles->roles['guest'] = ['name' => 'Guest'];

  ksort($wp_roles->roles);

  return $wp_roles->roles;
}

/*
*  TS-WCHC Get Category Childs
*/

function tswchc_get_woo_category_childs($parent_cat) {

  $taxonomy     = 'product_cat';
  $orderby      = 'name';
  $empty        = 0;

  $args = array(
    'taxonomy'     => $taxonomy,
    'orderby'      => $orderby,
    'hide_empty'   => $empty
  );

  $defaults = array(
    'parent' => $parent_cat->term_id,
    'hide_empty' => false
  );

  $r = wp_parse_args($args, $defaults);

  $terms = get_terms($taxonomy, $r);

  $children = array();

  foreach ($terms as $term) {

    $term->children = tswchc_get_woo_category_childs($term);

    $children[$term->term_id] = $term;
  }

  return $children;
}

/*
* Pretty Dump or debuggin
*/

function tswchc_dump($args) {
  $print = print_r($args, 1);
  if (isset($_GET['dev'])) {
    echo "<pre>$print</pre>";
  }
}

/*
* Check if hide rule is setup
* rule, hiden_rules
*/

function tswchc_is_checked($category, $role, $prev_rules) {

  if (is_array($prev_rules) && count($prev_rules)) {
    foreach ($prev_rules as $key => $rule) {
      if ($rule->category == $category && $rule->role == $role) {
        return true;
      }
    }
  }

  return false;
}

/*
*/

function tswchc_get_rules($slug, $prev_rules, $by_role = false) {

  $count = 0;

  if (is_array($prev_rules) && count($prev_rules)) {

    foreach ($prev_rules as $key => $rule) {

      if ($by_role && ($rule->role == $slug)) {

        $count++;
      } else if ($rule->category == $slug) {

        $count++;
      }
    }
  }

  return $count;
}

/**/

function tswchc_css_rules_worker($css) {

  $css = preg_replace('/\s+/', ' ', $css);
  $css = preg_replace('!/\*.*?\*/!s', '', $css);
  $css = str_replace('#ts-wchc-message', '', $css);

  $lines = explode("}", $css);
  $output = '';
  foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) {
      $output .= $line . "\n";
      continue;
    }
    if (strpos($line, '{') !== false) {
      list($selector, $properties) = explode('{', $line, 2);
      $selector = trim($selector);
      $properties = trim($properties);
      $selector = '#ts-wchc-message ' . $selector;
      $modified_line = $selector . ' {' . $properties;
      $output .= $modified_line . "}\n";
    } else {
      $output .= $line . "\n";
    }
  }
  return $output;
}

/**/

add_action('wp_ajax_tswchc_generate_plugin_options_json', 'tswchc_generate_plugin_options_json');

function tswchc_generate_plugin_options_json() {
  $options = array();
  $file_name = 'tswchc_plugin_options.json';
  $prefix = 'tswchc_';
  $exclude = array('tswchc_version');
  $upload_dir = wp_upload_dir();
  $all_options = wp_load_alloptions();


  foreach ($all_options as $option_name => $option_value) {
    if (strpos($option_name, $prefix) === 0 && !in_array($option_name, $exclude)) {
      $clean_option_name = str_replace($prefix, '', $option_name);
      $options[$clean_option_name] = $option_value;
    }
  }

  $json_string = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

  $file_path = $upload_dir['basedir'] . '/' . $file_name;

  file_put_contents($file_path, $json_string);

  $response = new stdClass();

  $response->file_path = $upload_dir['baseurl'] . '/' . $file_name;

  echo json_encode($response);

  wp_die();
}

/**/

add_action('wp_ajax_tswchc_import_plugin_options_json', 'tswchc_import_plugin_options_json');

function tswchc_import_plugin_options_json() {

  $prefix = 'tswchc_';

  $response = new stdClass();

  $success = true;

  $response->message =  "Your settings have been successfully imported.";

  foreach ($_POST['settings'] as $key => $setting) {

    $option = $prefix . $key;

    delete_option($option);

    $update = update_option($option, stripslashes($setting));

    if (!$update) {
      $success = false;
    }
  }

  if (!$success) {
    $response->message = "Errors were encountered while importing one or more settings. Please review your configuration to ensure everything looks as you expect.";
    $response->errors = true;
  }

  echo json_encode($response);

  wp_die();
}

/**/

add_action('wp_ajax_tswchc_reset_plugin_options', 'tswchc_reset_plugin_options');

function tswchc_reset_plugin_options() {
  global $wpdb;

  $prefix = 'tswchc_';
  $exclude = array('tswchc_version');

  // Prepare the SQL query to get only the options with the specified prefix
  $like_prefix = $wpdb->esc_like($prefix) . '%';
  $placeholders = implode(',', array_fill(0, count($exclude), '%s'));

  // Build the SQL query to exclude specific options
  $sql = $wpdb->prepare(
    "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_name NOT IN ($placeholders)",
    array_merge(array($like_prefix), $exclude)
  );

  $options = $wpdb->get_col($sql);

  foreach ($options as $option_name) {
    delete_option($option_name);
  }

  $response = new stdClass();
  $response->message = "Your settings have been successfully reset.";

  echo json_encode($response);

  wp_die();
}
