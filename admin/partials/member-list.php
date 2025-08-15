<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the filter bar
include CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/filter-bar.php';

?>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-id">ID</th>
            <th scope="col" class="manage-column column-gov-id">Cédula</th>
            <th scope="col" class="manage-column column-name">Nombre</th>
            <th scope="col" class="manage-column column-email">Email</th>
            <th scope="col" class="manage-column column-phone">Teléfono</th>
            <th scope="col" class="manage-column column-subscription">Suscripción</th>
            <th scope="col" class="manage-column column-actions">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td class="column-id">
                    <strong><?php echo esc_html($member->id); ?></strong>
                </td>
                <td class="column-gov-id">
                    <?php echo esc_html($member->gov_id); ?>
                </td>
                <td class="column-name">
                    <?php echo esc_html($member->first_name . ' ' . $member->last_name); ?>
                </td>
                <td class="column-email">
                    <a href="mailto:<?php echo esc_attr($member->email); ?>">
                        <?php echo esc_html($member->email); ?>
                    </a>
                </td>
                <td class="column-phone">
                    <?php echo esc_html($member->phone); ?>
                </td>
                <td class="column-subscription">
                    <?php if ($member->active_subscription): ?>
                        <span class="dashicons dashicons-yes-alt" style="color: green;" title="Activa"></span> Activa
                    <?php else: ?>
                        <span class="dashicons dashicons-dismiss" style="color: red;" title="Inactiva"></span> Inactiva
                    <?php endif; ?>
                </td>
                <td class="column-actions">
                    <a href="?page=club-riomonte&action=edit&id=<?php echo $member->id; ?>" class="button button-small">Editar</a>
                    <a href="?page=club-riomonte&action=delete&id=<?php echo $member->id; ?>" class="button button-small button-link-delete">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>