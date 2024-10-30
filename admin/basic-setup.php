<?php
/**
 * Basic setup functions for the plugin
 *
 * @since 1.0
 * @function	tswchc_activate_plugin()		Plugin activatation todo list
 * @function	tswchc_load_plugin_textdomain()	Load plugin text domain
 * @function	tswchc_settings_link()			Print direct link to plugin settings in plugins list in admin
 * @function	tswchc_plugin_row_meta()		Add donate and other links to plugins list
 * @function	tswchc_footer_text()			Admin footer text
 * @function	tswchc_footer_version()			Admin footer version
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin activatation todo list
 *
 * This function runs when user activates the plugin. Used in register_activation_hook in the main plugin file.
 * @since 1.0
 */
function tswchc_activate_plugin() {

  // Require Woommerce plugin
  if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
    // Stop activation redirect and show error
    wp_die('Sorry, but this plugin requires Woocommerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
  }

}
