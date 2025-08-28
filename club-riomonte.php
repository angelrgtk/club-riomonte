<?php
/*
Plugin Name: Club Riomonte
Description: Plugin para administrar miembros del Club Riomonte.
Version: 1.0.0
Author: Angel Alemany
Author URI: https://www.linkedin.com/in/alemanydev/
Plugin URI: https://github.com/angelrgtk/club-riomonte
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLUB_RIOMONTE_VERSION', '1.0.0');
define('CLUB_RIOMONTE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLUB_RIOMONTE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation hook
register_activation_hook(__FILE__, 'club_riomonte_activate');

function club_riomonte_activate()
{
    require_once CLUB_RIOMONTE_PLUGIN_DIR . 'includes/class-database.php';
    Club_Riomonte_Database::create_table();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'club_riomonte_deactivate');

function club_riomonte_deactivate()
{
    // Clean up if needed
}

// Initialize the plugin
function club_riomonte_init()
{
    require_once CLUB_RIOMONTE_PLUGIN_DIR . 'includes/class-club-riomonte.php';
    new Club_Riomonte();
}

add_action('plugins_loaded', 'club_riomonte_init');
