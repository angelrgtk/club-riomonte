<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Admin
{

    private $admin_pages;

    public function __construct()
    {
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/class-admin-pages.php';
        $this->admin_pages = new Club_Riomonte_Admin_Pages();
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Club Riomonte',
            'Club Riomonte',
            'manage_options',
            'club-riomonte',
            array($this->admin_pages, 'display_main_page'),
            'dashicons-groups',
            6
        );
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook != 'toplevel_page_club-riomonte') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'club-riomonte-admin',
            CLUB_RIOMONTE_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            CLUB_RIOMONTE_VERSION,
            true
        );
    }

    public function handle_actions()
    {
        if (!isset($_GET['page']) || $_GET['page'] !== 'club-riomonte') {
            return;
        }

        $this->admin_pages->handle_actions();
    }
}
