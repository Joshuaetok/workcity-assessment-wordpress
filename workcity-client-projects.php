<?php
/**
 * Plugin Name:     Workcity Client Projects
 * Plugin URI:      https://github.com/yourusername/workcity-assessment-wordpress
 * Description:     Client Projects Management Plugin for Workcity Africa assessment.
 * Version:         1.0.0
 * Author:          Joshua Etok
 * Author URI:      https://etokjoshua.github.io/portfolio
 * Text Domain:     workcity-client-projects
 * Domain Path:     /languages
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants
define( 'WCP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCP_URL',  plugin_dir_url( __FILE__ ) );

// Load translations
function wcp_load_textdomain() {
    load_plugin_textdomain(
        'workcity-client-projects',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'init', 'wcp_load_textdomain' );

// Include core classes
require_once WCP_PATH . 'includes/class-cpt-client-project.php';
require_once WCP_PATH . 'includes/class-admin-ui.php';
require_once WCP_PATH . 'includes/class-shortcode.php';
require_once WCP_PATH . 'includes/class-capabilities.php';
require_once WCP_PATH . 'includes/class-ajax-handler.php';
require_once WCP_PATH . 'includes/class-rest-api.php';

// Initialize plugin
function wcp_init() {
    \WCP\CPT_Client_Project::init();
    \WCP\Admin_UI::init();
    \WCP\Shortcode::init();
}
add_action( 'plugins_loaded', 'wcp_init' );

// Activation & deactivation hooks for custom capabilities
register_activation_hook( __FILE__,   ['\WCP\Capabilities', 'add_caps'] );
register_deactivation_hook( __FILE__, ['\WCP\Capabilities', 'remove_caps'] );
