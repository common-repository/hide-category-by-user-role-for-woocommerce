<?php
/**
 * Loads the plugin files
 *
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load basic setup. Plugin list links, text domain, footer links etc.
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/basic-setup.php' );

// Load admin setup. Register menus and settings
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/admin-ui-setup.php' );

// Render Admin UI
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/admin-ui-render.php' );

// Load Admin Templates / Hide by Category
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/template-parts/get-hide-by-category-tab.php' );

// Load Admin Templates / Hide by Role
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/template-parts/get-hide-by-role-tab.php' );

// Load Admin Templates / Settings
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/template-parts/get-settings-tab.php' );

// Load Admin Templates / Filter
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'admin/template-parts/get-filter-template.php' );

// Do plugin operations
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'functions/do.php' );

// Load Public setup. Register menus and settings
require_once( TSWCHC_STARTER_PLUGIN_DIR . 'public/public-ui-setup.php' );
