<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function club_riomonte_display_member_form($member = null)
{
    $is_edit = !empty($member);
    $title = $is_edit ? 'Editar Miembro' : 'Agregar Nuevo Miembro';
    $button_text = $is_edit ? 'Actualizar Miembro' : 'Agregar Miembro';

?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
        <a href="<?php echo admin_url('admin.php?page=club-riomonte'); ?>" class="page-title-action">Volver al Listado</a>
        <hr class="wp-header-end" style="margin-bottom: 30px;">

        <form method="post" class="club-riomonte-form">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="gov_id">Cédula</label>
                        </th>
                        <td>
                            <input type="text" id="gov_id" name="gov_id"
                                value="<?php echo $is_edit ? esc_attr($member->gov_id) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="first_name">Nombre</label>
                        </th>
                        <td>
                            <input type="text" id="first_name" name="first_name"
                                value="<?php echo $is_edit ? esc_attr($member->first_name) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="last_name">Apellido</label>
                        </th>
                        <td>
                            <input type="text" id="last_name" name="last_name"
                                value="<?php echo $is_edit ? esc_attr($member->last_name) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="birth_date">Fecha de Nacimiento</label>
                        </th>
                        <td>
                            <input type="date" id="birth_date" name="birth_date"
                                value="<?php echo $is_edit ? esc_attr($member->birth_date) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="email">Email</label>
                        </th>
                        <td>
                            <input type="email" id="email" name="email"
                                value="<?php echo $is_edit ? esc_attr($member->email) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="phone">Teléfono</label>
                        </th>
                        <td>
                            <input type="text" id="phone" name="phone"
                                value="<?php echo $is_edit ? esc_attr($member->phone) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="profile_picture">Foto de Perfil</label>
                        </th>
                        <td>
                            <div class="profile-picture-container">
                                <input type="hidden" id="profile_picture_id" name="profile_picture_id"
                                    value="<?php echo $is_edit ? esc_attr($member->profile_picture) : ''; ?>">
                                <div id="profile_picture_preview" style="margin-bottom: 10px;">
                                    <?php if ($is_edit && $member->profile_picture): ?>
                                        <?php $image_url = wp_get_attachment_image_url($member->profile_picture, 'thumbnail'); ?>
                                        <?php if ($image_url): ?>
                                            <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;">
                                        <?php else: ?>
                                            <p>Imagen no encontrada</p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p>No hay imagen seleccionada</p>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="select_profile_picture" class="button">Seleccionar Imagen</button>
                                <button type="button" id="remove_profile_picture" class="button"
                                    style="margin-left: 10px;<?php echo ($is_edit && $member->profile_picture) ? '' : ' display: none;'; ?>">
                                    Remover Imagen
                                </button>
                                <p class="description">Selecciona una imagen de la Biblioteca de Medios de WordPress</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="active_subscription">Suscripción Activa</label>
                        </th>
                        <td>
                            <input type="checkbox" id="active_subscription" name="active_subscription"
                                <?php echo ($is_edit && $member->active_subscription) ? ' checked' : ''; ?>>
                            <span class="description">Marca si el miembro tiene una suscripción activa</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="next_payment_date">Próxima Fecha de Pago</label>
                        </th>
                        <td>
                            <input type="date" id="next_payment_date" name="next_payment_date"
                                value="<?php echo $is_edit ? esc_attr($member->next_payment_date) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="notes">Notas</label>
                        </th>
                        <td>
                            <textarea id="notes" name="notes" rows="5" cols="50" class="large-text"><?php
                                                                                                    echo $is_edit ? esc_textarea($member->notes) : '';
                                                                                                    ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr($button_text); ?>">
                <a href="<?php echo admin_url('admin.php?page=club-riomonte'); ?>" class="button button-secondary" style="margin-left: 10px;">Cancelar</a>
            </p>
        </form>
    </div>
<?php
}
?>