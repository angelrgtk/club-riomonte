<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte
{

    private $version;
    private $admin;
    private $public;
    private $shortcode;

    public function __construct()
    {
        $this->version = CLUB_RIOMONTE_VERSION;
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies()
    {
        // Load database class
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'includes/class-database.php';

        // Load admin functionality
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'includes/class-admin.php';
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/class-admin-pages.php';

        // Load public functionality
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'includes/class-shortcode.php';
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'public/class-public.php';

        // Initialize classes
        $this->admin = new Club_Riomonte_Admin();
        $this->public = new Club_Riomonte_Public();
        $this->shortcode = new Club_Riomonte_Shortcode();
    }

    private function define_admin_hooks()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this->admin, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_admin_scripts'));
            add_action('admin_init', array($this->admin, 'handle_actions'));
        }
    }

    private function define_public_hooks()
    {
        // Register shortcode
        add_action('init', array($this->shortcode, 'register_shortcode'));
    }

    public function get_version()
    {
        return $this->version;
    }
}
