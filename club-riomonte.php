<?php
/*
Plugin Name: Club Riomonte
Description: Plugin para administrar miembros del Club Riomonte.
Version: 1.0.0
Author: Angel Alemany
Author URI: https://www.linkedin.com/in/alemanydev/
*/

?>

<?php
// Función que se ejecutará al activar el plugin
function club_riomonte_activate()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'club_riomonte_members';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        gov_id text NOT NULL,
        first_name text NOT NULL,
        last_name text NOT NULL,
        birth_date date NOT NULL,
        email varchar(100) NOT NULL,
        phone text NOT NULL,
        profile_picture int(11),
        is_deleted boolean NOT NULL DEFAULT FALSE,
        active_subscription boolean NOT NULL,
        next_payment_date date NOT NULL,
        notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registrar la función de activación
register_activation_hook(__FILE__, 'club_riomonte_activate');

// Add a menu to the WordPress admin panel
add_action('admin_menu', 'club_riomonte_menu');

function club_riomonte_menu()
{
    add_menu_page(
        'Club Riomonte', // Page title
        'Club Riomonte', // Menu title
        'manage_options', // Capability
        'club-riomonte', // Menu slug
        'club_riomonte_page', // Function
        'dashicons-groups', // Icon
        6 // Position
    );
}

// Handle actions before displaying any page
function club_riomonte_handle_actions()
{
    if (!isset($_GET['page']) || $_GET['page'] !== 'club-riomonte') {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'club_riomonte_members';

    // Handle create action
    if (isset($_GET['action']) && $_GET['action'] === 'create') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gov_id = sanitize_text_field($_POST['gov_id']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $birth_date = sanitize_text_field($_POST['birth_date']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $profile_picture_id = intval($_POST['profile_picture_id']);
            $active_subscription = isset($_POST['active_subscription']) ? 1 : 0;
            $next_payment_date = sanitize_text_field($_POST['next_payment_date']);
            $notes = sanitize_textarea_field($_POST['notes']);

            $wpdb->insert(
                $table_name,
                [
                    'gov_id' => $gov_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'birth_date' => $birth_date,
                    'email' => $email,
                    'phone' => $phone,
                    'profile_picture' => $profile_picture_id,
                    'active_subscription' => $active_subscription,
                    'next_payment_date' => $next_payment_date,
                    'notes' => $notes
                ]
            );

            wp_redirect(admin_url('admin.php?page=club-riomonte&message=created'));
            exit;
        }
        return;
    }

    // Handle edit and delete actions
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $id = intval($_GET['id']);

        if ($action === 'delete' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            $wpdb->update(
                $table_name,
                ['is_deleted' => 1],
                ['id' => $id]
            );
            wp_redirect(admin_url('admin.php?page=club-riomonte&message=deleted'));
            exit;
        }

        if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gov_id = sanitize_text_field($_POST['gov_id']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $birth_date = sanitize_text_field($_POST['birth_date']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $profile_picture_id = intval($_POST['profile_picture_id']);
            $active_subscription = isset($_POST['active_subscription']) ? 1 : 0;
            $next_payment_date = sanitize_text_field($_POST['next_payment_date']);
            $notes = sanitize_textarea_field($_POST['notes']);

            $wpdb->update(
                $table_name,
                [
                    'gov_id' => $gov_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'birth_date' => $birth_date,
                    'email' => $email,
                    'phone' => $phone,
                    'profile_picture' => $profile_picture_id,
                    'active_subscription' => $active_subscription,
                    'next_payment_date' => $next_payment_date,
                    'notes' => $notes
                ],
                ['id' => $id]
            );

            wp_redirect(admin_url('admin.php?page=club-riomonte&message=updated'));
            exit;
        }
    }
}

// Hook to handle actions early
add_action('admin_init', 'club_riomonte_handle_actions');

// Enqueue media scripts and styles
function club_riomonte_admin_scripts($hook)
{
    if ($hook != 'toplevel_page_club-riomonte') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('club-riomonte-admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'club_riomonte_admin_scripts');

// Register shortcode for frontend member lookup
add_shortcode('club_riomonte_lookup', 'club_riomonte_lookup_shortcode');

function club_riomonte_lookup_shortcode($atts)
{
    // Set default attributes
    $atts = shortcode_atts(array(
        'title' => 'Consulta de Miembro',
        'search_label' => 'Ingresa tu Cédula o ID de Miembro',
        'button_text' => 'Buscar'
    ), $atts);

    global $wpdb;
    $table_name = $wpdb->prefix . 'club_riomonte_members';
    $output = '';
    $member = null;
    $error_message = '';
    $search_value = '';

    // Handle form submission
    if (isset($_POST['club_search_submit']) && !empty($_POST['club_search_id'])) {
        $search_value = sanitize_text_field($_POST['club_search_id']);

        // Search by gov_id first, then by id
        $member = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE (gov_id = %s OR id = %s) AND is_deleted = 0",
            $search_value,
            $search_value
        ));

        if (!$member) {
            $error_message = 'No se encontró ningún miembro con el ID proporcionado. Por favor verifica tu información e intenta nuevamente.';
        }
    }

    // Add responsive CSS
    $output .= '<style>
        .club-riomonte-lookup-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .club-search-form {
            margin-bottom: 30px;
            text-align: center;
        }
        .club-search-input {
            padding: 12px;
            width: 100%;
            max-width: 400px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .club-search-input:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0,115,170,0.2);
        }
        .club-search-button {
            background: #0073aa;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 15px;
        }
        .club-search-button:hover {
            background: #005a87;
        }
        .club-member-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .club-member-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .club-profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            margin-bottom: 15px;
        }
        .club-profile-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin-bottom: 15px;
            border: 4px solid #fff;
        }
        .club-member-name {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .club-member-id {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .club-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin: 0;
        }
        .club-details-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .club-details-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .club-details-label {
            font-weight: 600;
            color: #555;
            width: 35%;
        }
        .club-details-value {
            color: #333;
        }
        @media (max-width: 600px) {
            .club-riomonte-lookup-container {
                margin: 10px;
                padding: 15px;
            }
            .club-member-header {
                padding: 20px 15px;
            }
            .club-profile-image, .club-profile-placeholder {
                width: 100px;
                height: 100px;
            }
            .club-member-name {
                font-size: 24px;
            }
            .club-details-table td {
                padding: 12px 10px;
                display: block;
                width: 100% !important;
            }
            .club-details-label {
                border-bottom: none;
                padding-bottom: 5px;
                font-weight: bold;
            }
            .club-details-value {
                padding-top: 0;
                margin-bottom: 15px;
            }
        }
    </style>';

    // Start building the output
    $output .= '<div class="club-riomonte-lookup-container">';

    // Title
    $output .= '<h3 style="text-align: center; margin-bottom: 30px; color: #333; font-size: 24px;">' . esc_html($atts['title']) . '</h3>';

    // Search form
    $output .= '<form method="post" class="club-search-form">';
    $output .= '<div style="margin-bottom: 20px;">';
    $output .= '<label for="club_search_id" style="display: block; margin-bottom: 10px; font-weight: 600; color: #555; font-size: 16px;">' . esc_html($atts['search_label']) . '</label>';
    $output .= '<input type="text" id="club_search_id" name="club_search_id" value="' . esc_attr($search_value) . '" required class="club-search-input" placeholder="ej., 12345 o CED123456">';
    $output .= '</div>';
    $output .= '<button type="submit" name="club_search_submit" class="club-search-button">' . esc_html($atts['button_text']) . '</button>';
    $output .= '</form>';

    // Display error message
    if ($error_message) {
        $output .= '<div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center;">';
        $output .= '<strong>Error:</strong> ' . esc_html($error_message);
        $output .= '</div>';
    }

    // Display member information if found
    if ($member) {
        $output .= '<div style="background: #e8f5e8; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">';
        $output .= '<strong>✅ ¡Miembro Encontrado!</strong> Aquí está su información:';
        $output .= '</div>';

        $output .= '<div class="club-member-card">';

        // Member photo and basic info header
        $output .= '<div class="club-member-header">';

        // Profile picture
        if ($member->profile_picture) {
            $image_url = wp_get_attachment_image_url($member->profile_picture, 'medium');
            if ($image_url) {
                $output .= '<img src="' . esc_url($image_url) . '" class="club-profile-image" alt="Profile Picture">';
            } else {
                $output .= '<div class="club-profile-placeholder">👤</div>';
            }
        } else {
            $output .= '<div class="club-profile-placeholder">👤</div>';
        }

        $output .= '<h2 class="club-member-name">' . esc_html($member->first_name . ' ' . $member->last_name) . '</h2>';
        $output .= '<p class="club-member-id">ID de Miembro: #' . esc_html($member->id) . '</p>';
        $output .= '</div>';

        // Member details table
        $output .= '<div style="padding: 0;">';
        $output .= '<table class="club-details-table">';

        $fields = array(
            'Cédula' => $member->gov_id,
            'Email' => '<a href="mailto:' . esc_attr($member->email) . '" style="color: #0073aa; text-decoration: none;">' . esc_html($member->email) . '</a>',
            'Teléfono' => '<a href="tel:' . esc_attr($member->phone) . '" style="color: #0073aa; text-decoration: none;">' . esc_html($member->phone) . '</a>',
            'Fecha de Nacimiento' => date('j \d\e F \d\e Y', strtotime($member->birth_date)),
            'Estado de Suscripción' => $member->active_subscription ? '<span style="color: #4caf50; font-weight: 600;">✅ Activa</span>' : '<span style="color: #f44336; font-weight: 600;">❌ Inactiva</span>',
            'Próximo Pago' => $member->next_payment_date ? date('j \d\e F \d\e Y', strtotime($member->next_payment_date)) : 'N/A',
            'Miembro Desde' => date('j \d\e F \d\e Y', strtotime($member->created_at))
        );

        if (!empty($member->notes)) {
            $fields['Notas'] = nl2br(esc_html($member->notes));
        }

        foreach ($fields as $label => $value) {
            $output .= '<tr>';
            $output .= '<td class="club-details-label">' . esc_html($label) . '</td>';
            $output .= '<td class="club-details-value">' . $value . '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';
        $output .= '</div>';
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}

// Main admin page function
function club_riomonte_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'club_riomonte_members';

    // Handle messages
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if ($message === 'updated') {
            echo '<div class="notice notice-success is-dismissible"><p>¡Miembro actualizado exitosamente!</p></div>';
        } elseif ($message === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>¡Miembro eliminado exitosamente!</p></div>';
        } elseif ($message === 'created') {
            echo '<div class="notice notice-success is-dismissible"><p>¡Miembro creado exitosamente!</p></div>';
        }
    }

    // Handle create form
    if (isset($_GET['action']) && $_GET['action'] === 'create') {
        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">Agregar Nuevo Miembro</h1>';
        echo '<a href="' . admin_url('admin.php?page=club-riomonte') . '" class="page-title-action">Volver al Listado</a>';
        echo '<hr class="wp-header-end" style="margin-bottom: 30px;">';

        echo '<form method="post" class="club-riomonte-form">';
        echo '<table class="form-table" role="presentation">';
        echo '<tbody>';
        echo '<tr><th scope="row"><label for="gov_id">Cédula</label></th><td><input type="text" id="gov_id" name="gov_id" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="first_name">Nombre</label></th><td><input type="text" id="first_name" name="first_name" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="last_name">Apellido</label></th><td><input type="text" id="last_name" name="last_name" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="birth_date">Fecha de Nacimiento</label></th><td><input type="date" id="birth_date" name="birth_date" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="email">Email</label></th><td><input type="email" id="email" name="email" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="phone">Teléfono</label></th><td><input type="text" id="phone" name="phone" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="profile_picture">Foto de Perfil</label></th><td>';
        echo '<div class="profile-picture-container">';
        echo '<input type="hidden" id="profile_picture_id" name="profile_picture_id" value="">';
        echo '<div id="profile_picture_preview" style="margin-bottom: 10px;">';
        echo '<p>No hay imagen seleccionada</p>';
        echo '</div>';
        echo '<button type="button" id="select_profile_picture" class="button">Seleccionar Imagen</button>';
        echo '<button type="button" id="remove_profile_picture" class="button" style="margin-left: 10px; display: none;">Remover Imagen</button>';
        echo '<p class="description">Selecciona una imagen de la Biblioteca de Medios de WordPress</p>';
        echo '</div>';
        echo '</td></tr>';
        echo '<tr><th scope="row"><label for="active_subscription">Suscripción Activa</label></th><td><input type="checkbox" id="active_subscription" name="active_subscription"> <span class="description">Marca si el miembro tiene una suscripción activa</span></td></tr>';
        echo '<tr><th scope="row"><label for="next_payment_date">Próxima Fecha de Pago</label></th><td><input type="date" id="next_payment_date" name="next_payment_date" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="notes">Notas</label></th><td><textarea id="notes" name="notes" rows="5" cols="50" class="large-text"></textarea></td></tr>';
        echo '</tbody>';
        echo '</table>';
        echo '<p class="submit">';
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Agregar Miembro">';
        echo '<a href="' . admin_url('admin.php?page=club-riomonte') . '" class="button button-secondary" style="margin-left: 10px;">Cancelar</a>';
        echo '</p>';
        echo '</form>';
        echo '</div>';
        return;
    }

    // Handle edit form
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if (!$member) {
            echo '<div class="wrap"><div class="notice notice-error"><p>Miembro no encontrado.</p></div></div>';
            return;
        }

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">Editar Miembro</h1>';
        echo '<a href="' . admin_url('admin.php?page=club-riomonte') . '" class="page-title-action">Volver al Listado</a>';
        echo '<hr class="wp-header-end" style="margin-bottom: 30px;">';

        echo '<form method="post" class="club-riomonte-form">';
        echo '<table class="form-table" role="presentation">';
        echo '<tbody>';
        echo '<tr><th scope="row"><label for="gov_id">Cédula</label></th><td><input type="text" id="gov_id" name="gov_id" value="' . esc_attr($member->gov_id) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="first_name">Nombre</label></th><td><input type="text" id="first_name" name="first_name" value="' . esc_attr($member->first_name) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="last_name">Apellido</label></th><td><input type="text" id="last_name" name="last_name" value="' . esc_attr($member->last_name) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="birth_date">Fecha de Nacimiento</label></th><td><input type="date" id="birth_date" name="birth_date" value="' . esc_attr($member->birth_date) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="email">Email</label></th><td><input type="email" id="email" name="email" value="' . esc_attr($member->email) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="phone">Teléfono</label></th><td><input type="text" id="phone" name="phone" value="' . esc_attr($member->phone) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="profile_picture">Foto de Perfil</label></th><td>';
        echo '<div class="profile-picture-container">';
        echo '<input type="hidden" id="profile_picture_id" name="profile_picture_id" value="' . esc_attr($member->profile_picture) . '">';
        echo '<div id="profile_picture_preview" style="margin-bottom: 10px;">';

        if ($member->profile_picture) {
            $image_url = wp_get_attachment_image_url($member->profile_picture, 'thumbnail');
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;">';
            } else {
                echo '<p>Imagen no encontrada</p>';
            }
        } else {
            echo '<p>No hay imagen seleccionada</p>';
        }

        echo '</div>';
        echo '<button type="button" id="select_profile_picture" class="button">Seleccionar Imagen</button>';
        echo '<button type="button" id="remove_profile_picture" class="button" style="margin-left: 10px;' . ($member->profile_picture ? '' : ' display: none;') . '">Remover Imagen</button>';
        echo '<p class="description">Selecciona una imagen de la Biblioteca de Medios de WordPress</p>';
        echo '</div>';
        echo '</td></tr>';
        echo '<tr><th scope="row"><label for="active_subscription">Suscripción Activa</label></th><td><input type="checkbox" id="active_subscription" name="active_subscription"' . ($member->active_subscription ? ' checked' : '') . '> <span class="description">Marca si el miembro tiene una suscripción activa</span></td></tr>';
        echo '<tr><th scope="row"><label for="next_payment_date">Próxima Fecha de Pago</label></th><td><input type="date" id="next_payment_date" name="next_payment_date" value="' . esc_attr($member->next_payment_date) . '" class="regular-text" required></td></tr>';
        echo '<tr><th scope="row"><label for="notes">Notas</label></th><td><textarea id="notes" name="notes" rows="5" cols="50" class="large-text">' . esc_textarea($member->notes) . '</textarea></td></tr>';
        echo '</tbody>';
        echo '</table>';
        echo '<p class="submit">';
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Actualizar Miembro">';
        echo '<a href="' . admin_url('admin.php?page=club-riomonte') . '" class="button button-secondary" style="margin-left: 10px;">Cancelar</a>';
        echo '</p>';
        echo '</form>';
        echo '</div>';
        return;
    }

    // Handle delete confirmation
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if (!$member) {
            echo '<div class="wrap"><div class="notice notice-error"><p>Miembro no encontrado.</p></div></div>';
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>Eliminar Miembro</h1>';
        echo '<div class="notice notice-warning">';
        echo '<p><strong>¿Estás seguro de que quieres eliminar este miembro?</strong></p>';
        echo '<p>Miembro: ' . esc_html($member->first_name . ' ' . $member->last_name) . ' (' . esc_html($member->email) . ')</p>';
        echo '<p>Esta acción no se puede deshacer.</p>';
        echo '</div>';
        echo '<p>';
        echo '<a href="?page=club-riomonte&action=delete&id=' . $id . '&confirm=yes" class="button button-primary">Sí, Eliminar</a> ';
        echo '<a href="?page=club-riomonte" class="button button-secondary">Cancelar</a>';
        echo '</p>';
        echo '</div>';
        return;
    }

    // Default view - list all members
    $members = $wpdb->get_results("SELECT * FROM $table_name WHERE is_deleted = 0 ORDER BY created_at DESC");

    echo '<div class="wrap">';
    echo '<div class="notice notice-info">';
    echo '<p>Usa el shortcode <code>[club_riomonte_lookup]</code> para mostrar el formulario de consulta de miembros en cualquier página o post.</p>';
    echo '<p>Atributos opcionales:</p>';
    echo '<ul style="list-style-type: disc; margin-left: 20px;">';
    echo '<li><code>title</code> - Título personalizado para el formulario (por defecto: "Consulta de Miembro")</li>';
    echo '<li><code>search_label</code> - Etiqueta personalizada para el campo de búsqueda (por defecto: "Ingresa tu Cédula o ID de Miembro")</li>';
    echo '<li><code>button_text</code> - Texto personalizado para el botón (por defecto: "Buscar")</li>';
    echo '</ul>';
    echo '<p>Ejemplo: <code>[club_riomonte_lookup title="Buscar Miembro" search_label="Ingresa ID" button_text="Consultar"]</code></p>';
    echo '</div>';
    echo '<h1 class="wp-heading-inline">Miembros Club Riomonte</h1>';
    echo '<a href="?page=club-riomonte&action=create" class="page-title-action">Agregar Nuevo Miembro</a>';
    echo '<hr class="wp-header-end" style="margin-bottom: 30px;">';

    if (empty($members)) {
        echo '<div class="notice notice-info"><p>No se encontraron miembros. <a href="?page=club-riomonte&action=create">Agrega tu primer miembro</a>.</p></div>';
    } else {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col" class="manage-column column-id">ID</th>';
        echo '<th scope="col" class="manage-column column-gov-id">Cédula</th>';
        echo '<th scope="col" class="manage-column column-name">Nombre</th>';
        echo '<th scope="col" class="manage-column column-email">Email</th>';
        echo '<th scope="col" class="manage-column column-phone">Teléfono</th>';
        echo '<th scope="col" class="manage-column column-subscription">Suscripción</th>';
        echo '<th scope="col" class="manage-column column-actions">Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($members as $member) {
            echo '<tr>';
            echo '<td class="column-id"><strong>' . esc_html($member->id) . '</strong></td>';
            echo '<td class="column-gov-id">' . esc_html($member->gov_id) . '</td>';
            echo '<td class="column-name">' . esc_html($member->first_name . ' ' . $member->last_name) . '</td>';
            echo '<td class="column-email"><a href="mailto:' . esc_attr($member->email) . '">' . esc_html($member->email) . '</a></td>';
            echo '<td class="column-phone">' . esc_html($member->phone) . '</td>';
            echo '<td class="column-subscription">';
            if ($member->active_subscription) {
                echo '<span class="dashicons dashicons-yes-alt" style="color: green;" title="Activa"></span> Activa';
            } else {
                echo '<span class="dashicons dashicons-dismiss" style="color: red;" title="Inactiva"></span> Inactiva';
            }
            echo '</td>';
            echo '<td class="column-actions">';
            echo '<a href="?page=club-riomonte&action=edit&id=' . $member->id . '" class="button button-small">Editar</a> ';
            echo '<a href="?page=club-riomonte&action=delete&id=' . $member->id . '" class="button button-small button-link-delete">Eliminar</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
    echo '</div>';
}
