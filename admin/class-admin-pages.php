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
                'created' => '¡Miembro creado exitosamente!'
            );

            if (isset($notices[$message])) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notices[$message]) . '</p></div>';
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
            'active_subscription' => isset($post_data['active_subscription']) ? 1 : 0,
            'next_payment_date' => sanitize_text_field($post_data['next_payment_date']),
            'notes' => sanitize_textarea_field($post_data['notes'])
        );
    }
}
