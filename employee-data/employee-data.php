<?php
/**
 * Plugin Name: Employee Data
 * Plugin URI: -
 * Description: A custom plugin to manage employee data using a custom post type.
 * Version: 1.0.0
 * Author: Jawad Zahid
 * Author URI: -
 * Text Domain: employee-data
 *
 * @package Employee Data
 */

defined('ABSPATH') || exit;

if (!defined('EMP_PLUGIN_DIR')) {
    define('EMP_PLUGIN_DIR', plugin_dir_path(__FILE__));
}


// Include helpers.php and includes.php
require_once EMP_PLUGIN_DIR . 'helpers/helpers.php';
require_once EMP_PLUGIN_DIR . 'includes/includes.php';


// Enqueue style.css and script.js
function wc_plugin_enqueue_assets()
{
    // Enqueue plugin styles
    wp_enqueue_style(
        'emp-plugin',
        plugins_url('assets/css/style.css', __FILE__),
        [],
        '1.0.0'
    );

    // Enqueue plugin script
    wp_enqueue_script(
        'emp-plugin',
        plugins_url('assets/js/script.js', __FILE__),
        ['jquery'],
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'wc_plugin_enqueue_assets');