<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Admin_Pages
{

    public function display_main_page()
    {
        // Handle messages
        $this->display_admin_notices();

        // Handle different actions
        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            switch ($action) {
                case 'create':
                    $this->display_create_page();
                    return;
                case 'edit':
                    if (isset($_GET['id'])) {
                        $this->display_edit_page(intval($_GET['id']));
                        return;
                    }
                    break;
                case 'delete':
                    if (isset($_GET['id'])) {
                        $this->display_delete_page(intval($_GET['id']));
                        return;
                    }
                    break;
            }
        }

        // Default: display member list
        $this->display_member_list();
    }

    private function display_admin_notices()
    {
        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $notices = array(
                'updated' => '¡Miembro actualizado exitosamente!',
                'deleted' => '¡Miembro eliminado exitosamente!',
                'created' => '¡Miembro creado exitosamente!',
                'note_added' => '¡Nota agregada exitosamente!',
                'note_deleted' => '¡Nota eliminada exitosamente!',
                'note_error' => 'Error al procesar la nota. Intente nuevamente.',
                'empty_note' => 'La nota no puede estar vacía.',
                'email_sent' => '¡Email enviado exitosamente!',
                'email_error' => 'Error al enviar el email. Intente nuevamente.',
                'email_invalid_data' => 'Error: Datos de email inválidos.'
            );

            if (isset($notices[$message])) {
                $class = in_array($message, ['note_error', 'empty_note', 'email_error', 'email_invalid_data']) ? 'notice-error' : 'notice-success';
                echo '<div class="notice ' . $class . ' is-dismissible"><p>' . esc_html($notices[$message]) . '</p></div>';
            }
        }
    }

    private function display_member_list()
    {
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/admin-display.php';
    }

    private function display_create_page()
    {
        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/member-form.php';
        club_riomonte_display_member_form();
    }

    private function display_edit_page($id)
    {
        $member = Club_Riomonte_Database::get_member($id);

        if (!$member) {
            echo '<div class="wrap"><div class="notice notice-error"><p>Miembro no encontrado.</p></div></div>';
            return;
        }

        require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/member-form.php';
        club_riomonte_display_member_form($member);
    }

    private function display_delete_page($id)
    {
        $member = Club_Riomonte_Database::get_member($id);

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
    }

    public function handle_actions()
    {
        // Handle notes actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'add_note') {
                $this->process_add_note();
                return;
            }

            // Handle email sending
            if (isset($_POST['action']) && $_POST['action'] === 'send_email') {
                $this->process_send_email();
                return;
            }
        }

        // Handle AJAX delete note action
        if (isset($_GET['action']) && $_GET['action'] === 'delete_note' && isset($_GET['note_id'])) {
            $this->process_delete_note();
            return;
        }

        // Handle CSV export action
        if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
            $this->process_csv_export();
            return;
        }

        // Handle create action
        if (isset($_GET['action']) && $_GET['action'] === 'create') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->process_create_member();
            }
            return;
        }

        // Handle edit and delete actions
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $action = $_GET['action'];
            $id = intval($_GET['id']);

            if ($action === 'delete' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
                Club_Riomonte_Database::delete_member($id);
                wp_redirect(admin_url('admin.php?page=club-riomonte&message=deleted'));
                exit;
            }

            if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->process_update_member($id);
            }
        }
    }

    private function process_create_member()
    {
        $data = $this->sanitize_member_data($_POST);

        Club_Riomonte_Database::create_member($data);
        wp_redirect(admin_url('admin.php?page=club-riomonte&message=created'));
        exit;
    }

    private function process_update_member($id)
    {
        $data = $this->sanitize_member_data($_POST);

        Club_Riomonte_Database::update_member($id, $data);
        wp_redirect(admin_url('admin.php?page=club-riomonte&message=updated'));
        exit;
    }

    private function sanitize_member_data($post_data)
    {
        return array(
            'gov_id' => sanitize_text_field($post_data['gov_id']),
            'first_name' => sanitize_text_field($post_data['first_name']),
            'last_name' => sanitize_text_field($post_data['last_name']),
            'birth_date' => sanitize_text_field($post_data['birth_date']),
            'email' => sanitize_email($post_data['email']),
            'phone' => sanitize_text_field($post_data['phone']),
            'profile_picture' => intval($post_data['profile_picture_id']),
            'expiration_date' => sanitize_text_field($post_data['expiration_date']),
            'last_payment_date' => !empty($post_data['last_payment_date']) ? sanitize_text_field($post_data['last_payment_date']) : null,
            'is_public' => isset($post_data['is_public']) ? 1 : 0
        );
    }

    private function process_add_note()
    {
        if (!isset($_POST['member_id']) || !isset($_POST['note_text'])) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&message=error'));
            exit;
        }

        $member_id = intval($_POST['member_id']);
        $note_text = sanitize_textarea_field($_POST['note_text']);

        if (empty($note_text)) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=empty_note'));
            exit;
        }

        $result = Club_Riomonte_Database::create_note($member_id, $note_text);

        if ($result) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=note_added'));
        } else {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=note_error'));
        }
        exit;
    }

    private function process_delete_note()
    {
        if (!isset($_GET['note_id']) || !isset($_GET['member_id'])) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&message=error'));
            exit;
        }

        $note_id = intval($_GET['note_id']);
        $member_id = intval($_GET['member_id']);

        $result = Club_Riomonte_Database::delete_note($note_id);

        if ($result) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=note_deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=note_error'));
        }
        exit;
    }

    private function process_send_email()
    {
        // Validate required fields
        if (!isset($_POST['member_id']) || !isset($_POST['email_subject']) || !isset($_POST['email_text'])) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&message=email_invalid_data'));
            exit;
        }

        $member_id = intval($_POST['member_id']);
        $email_subject = sanitize_text_field($_POST['email_subject']);
        $email_text = sanitize_textarea_field($_POST['email_text']);

        // Validate data
        if (empty($member_id) || empty($email_subject) || empty($email_text)) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=email_invalid_data'));
            exit;
        }

        // Get member data
        $member = Club_Riomonte_Database::get_member($member_id);
        if (!$member) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&message=email_error'));
            exit;
        }

        // Prepare email content
        $to = $member->email;
        $subject = $email_subject;

        // Create a nice email template
        $message = $this->build_email_template($member, $email_text);

        // Set email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>'
        );

        // Send email using WordPress wp_mail function
        $sent = wp_mail($to, $subject, $message, $headers);

        // Redirect with appropriate message
        if ($sent) {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=email_sent'));
        } else {
            wp_redirect(admin_url('admin.php?page=club-riomonte&action=edit&id=' . $member_id . '&message=email_error'));
        }
        exit;
    }

    private function build_email_template($member, $custom_message)
    {
        $site_name = get_option('blogname');
        $site_url = get_option('home');

        // Iniciar output buffering para capturar el HTML del template
        ob_start();

        // Incluir el template de email
        include CLUB_RIOMONTE_PLUGIN_DIR . 'includes/mail/email-template.php';

        // Obtener el contenido y limpiar el buffer
        $template = ob_get_clean();

        return $template;
    }

    private function process_csv_export()
    {
        // Get the same filters used in the main page
        $filters = [
            'expiration_date' => isset($_GET['expiration_date']) ? $_GET['expiration_date'] : null,
            'search_term' => isset($_GET['search_term']) && strlen($_GET['search_term']) >= 3 ? $_GET['search_term'] : null,
            'is_public' => isset($_GET['is_public']) ? $_GET['is_public'] : null,
        ];

        // Get filtered members
        $members = Club_Riomonte_Database::get_all_members($filters);

        // Set headers for CSV download
        $filename = 'miembros_club_riomonte_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        // Create file handle
        $output = fopen('php://output', 'w');

        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV Headers
        $headers = [
            'ID',
            'Cédula',
            'Nombre',
            'Apellido',
            'Fecha de Nacimiento',
            'Email',
            'Teléfono',
            'Estado de Suscripción',
            'Fecha de Expiración',
            'Último Pago',
            'Privacidad',
            'Fecha de Creación'
        ];

        fputcsv($output, $headers);

        // Add member data
        foreach ($members as $member) {
            $today = date('Y-m-d');
            $is_active = !empty($member->expiration_date) && $member->expiration_date >= $today;

            $row = [
                $member->id,
                $member->gov_id,
                $member->first_name,
                $member->last_name,
                $member->birth_date ? date_i18n('j/m/Y', strtotime($member->birth_date)) : '',
                $member->email,
                $member->phone,
                $is_active ? 'Activa' : 'Inactiva',
                $member->expiration_date ? date_i18n('j/m/Y', strtotime($member->expiration_date)) : '',
                $member->last_payment_date ? date_i18n('j/m/Y', strtotime($member->last_payment_date)) : '',
                $member->is_public ? 'Público' : 'Privado',
                date_i18n('j/m/Y H:i', strtotime($member->created_at))
            ];

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
