<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Shortcode
{

    public function register_shortcode()
    {
        add_shortcode('club_riomonte_lookup', array($this, 'shortcode_callback'));
    }

    public function shortcode_callback($atts)
    {
        // Set default attributes
        $atts = shortcode_atts(array(
            'title' => 'Consulta de Miembro',
            'search_label' => 'Ingresa tu Número de Documento',
            'button_text' => 'Buscar'
        ), $atts);

        $member = null;
        $error_message = '';
        $search_value = '';

        // Handle form submission
        if (isset($_POST['club_search_submit']) && !empty($_POST['club_search_id'])) {
            $search_value = sanitize_text_field($_POST['club_search_id']);
            $member = Club_Riomonte_Database::get_member_by_search($search_value);

            if (!$member) {
                $error_message = 'No se encontró ningún miembro con el número de documento proporcionado. Por favor verifica tu información e intenta nuevamente.';
            }
        }

        // Include the display partial
        ob_start();
        require CLUB_RIOMONTE_PLUGIN_DIR . 'public/partials/shortcode-display.php';
        return ob_get_clean();
    }
}
