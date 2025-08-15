<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Public
{

    public function __construct()
    {
        // Add any public-facing hooks here
        // For now, we mainly use this for shortcodes
    }

    public function enqueue_public_scripts()
    {
        // Enqueue public scripts if needed in the future
        wp_enqueue_script(
            'club-riomonte-public',
            CLUB_RIOMONTE_PLUGIN_URL . 'assets/js/public.js',
            array('jquery'),
            CLUB_RIOMONTE_VERSION,
            true
        );
    }

    public function enqueue_public_styles()
    {
        // Enqueue public styles if needed in the future
        wp_enqueue_style(
            'club-riomonte-public',
            CLUB_RIOMONTE_PLUGIN_URL . 'assets/css/public.css',
            array(),
            CLUB_RIOMONTE_VERSION,
            'all'
        );
    }
}
