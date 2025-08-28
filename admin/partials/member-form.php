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
            <div class="form-container" style="display: flex;">
                <div class="main-box" style="flex: 3;">
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

                        </tbody>
                    </table>
                </div>
                <div class="side-box" style="flex: 1; padding-left: 20px;">
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
                    <div class="is_public" style="margin: 20px 0;">
                        <div class="switch-container" style="display: flex; align-items: center; gap: 10px; margin: 10px 0;">
                            <span class="switch-label-text">Privado</span>
                            <label class="switch">
                                <input type="checkbox" id="is_public" name="is_public" value="1"
                                    <?php echo ($is_edit && $member->is_public) || (!$is_edit) ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                            <span class="switch-label-text">Público</span>
                        </div>
                        <p class="description">Si es público, el miembro aparecerá en el listado de miembros públicos.</p>
                    </div>
                    <div class="expiration-date" style="margin: 20px 0;">
                        <label for="expiration_date">Fecha de Expiración</label>
                        <input type="date" id="expiration_date" name="expiration_date"
                            value="<?php echo $is_edit ? esc_attr($member->expiration_date) : ''; ?>"
                            class="regular-text" required>
                        <p class="description">La suscripción se considera activa si hoy es menor o igual a esta fecha.</p>
                    </div>
                    <div class="last-payment-date" style="margin: 20px 0;">
                        <label for="last_payment_date">Último Pago</label>
                        <input type="date" id="last_payment_date" name="last_payment_date"
                            value="<?php echo $is_edit ? esc_attr($member->last_payment_date) : ''; ?>"
                            class="regular-text">
                        <p class="description">Fecha del último pago registrado (opcional).</p>
                    </div>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr($button_text); ?>">
                        <a href="<?php echo admin_url('admin.php?page=club-riomonte'); ?>" class="button button-secondary" style="margin-left: 10px;">Cancelar</a>
                    </p>
                </div>
            </div>
        </form>

        <!-- Sección de Notas - Separada del formulario principal -->
        <?php if ($is_edit): ?>
            <div class="notes-section-wrapper" style="margin-top: 30px;">
                <div class="notes-section" style="background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 20px;">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px;">Notas del Miembro</h3>

                    <!-- Formulario independiente para agregar nueva nota -->
                    <div class="add-note-form">
                        <h4>Agregar Nueva Nota</h4>
                        <form method="post" id="add-note-form" style="background: #f8f8f8; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                            <input type="hidden" name="action" value="add_note">
                            <input type="hidden" name="member_id" value="<?php echo $member->id; ?>">
                            <textarea name="note_text" id="note_text" rows="4" cols="50" class="large-text" placeholder="Escriba su nota aquí..." required style="width: 100%; resize: vertical; border: 1px solid #ddd; border-radius: 3px; padding: 8px;"></textarea>
                            <br><br>
                            <input type="submit" name="submit_note" class="button button-secondary" value="Agregar Nota">
                        </form>
                    </div>

                    <!-- Mostrar notas existentes -->
                    <div class="existing-notes" style="margin-bottom: 20px;">
                        <?php
                        $notes = Club_Riomonte_Database::get_member_notes($member->id);
                        if (!empty($notes)):
                        ?>
                            <div class="notes-list">
                                <?php foreach ($notes as $note): ?>
                                    <div class="note-item" style="background: #f9f9f9; border-left: 4px solid #0073aa; padding: 12px; margin-bottom: 10px; border-radius: 3px;">
                                        <div class="note-content" style="margin-bottom: 8px;">
                                            <?php echo nl2br(esc_html($note->text)); ?>
                                        </div>
                                        <div class="note-meta" style="font-size: 12px; color: #666;">
                                            <strong>Fecha:</strong> <?php echo date_i18n('j \d\e F \d\e Y \a \l\a\s H:i', strtotime($note->date)); ?>
                                            <span style="margin-left: 15px;">
                                                <a href="?page=club-riomonte&action=delete_note&note_id=<?php echo $note->id; ?>&member_id=<?php echo $member->id; ?>"
                                                    onclick="return confirm('¿Estás seguro de que quieres eliminar esta nota?');"
                                                    style="color: #a00; text-decoration: none;">Eliminar</a>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic;">No hay notas para este miembro.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sección de Envío de Email - Solo para miembros existentes -->
            <div class="email-section-wrapper" style="margin-top: 30px;">
                <div class="email-section" style="background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 20px;">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px;">
                        <span class="dashicons dashicons-email-alt" style="margin-right: 8px;"></span>
                        Enviar Email al Miembro
                    </h3>
                    <p style="color: #666; margin-bottom: 20px;">
                        Envía un mensaje personalizado al email: <strong><?php echo esc_html($member->email); ?></strong>
                    </p>

                    <!-- Formulario independiente para enviar email -->
                    <form method="post" id="send-email-form" style="background: #f0f8f0; padding: 20px; border-radius: 5px; border: 1px solid #28a745;">
                        <input type="hidden" name="action" value="send_email">
                        <input type="hidden" name="member_id" value="<?php echo $member->id; ?>">

                        <div style="margin-bottom: 15px;">
                            <label for="email_subject" style="display: block; font-weight: 600; margin-bottom: 5px;">Asunto:</label>
                            <input type="text" name="email_subject" id="email_subject"
                                class="large-text"
                                placeholder="Ej: Información importante sobre tu membresía"
                                required
                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label for="email_text" style="display: block; font-weight: 600; margin-bottom: 5px;">Mensaje:</label>
                            <textarea name="email_text" id="email_text" rows="8"
                                class="large-text"
                                placeholder="Escribe tu mensaje aquí..."
                                required
                                style="width: 100%; resize: vertical; border: 1px solid #ddd; border-radius: 3px; padding: 8px;"></textarea>
                            <p class="description" style="margin-top: 5px; font-size: 12px; color: #666;">
                                Se incluirá automáticamente el nombre del miembro y la información del club.
                            </p>
                        </div>

                        <div style="text-align: center;">
                            <input type="submit" name="submit_email" class="button button-primary"
                                value="📧 Enviar Email"
                                style="background: #28a745; border-color: #28a745; font-size: 14px; padding: 8px 20px;"
                                onclick="return confirm('¿Estás seguro de que quieres enviar este email a <?php echo esc_js($member->first_name . ' ' . $member->last_name); ?>?');">
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}
?>

<style>
    /* Switch moderno para privacidad */
    .switch-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }

    .switch-label-text {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s ease;
        border-radius: 30px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s ease;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    input:checked+.slider {
        background-color: #0073aa;
        box-shadow: inset 0 2px 4px rgba(0, 115, 170, 0.3);
    }

    input:focus+.slider {
        box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.3);
    }

    input:checked+.slider:before {
        transform: translateX(30px);
    }

    /* Animación de hover */
    .slider:hover {
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 0 0 2px rgba(0, 115, 170, 0.1);
    }

    .form-container {
        display: flex;
        flex-wrap: wrap;
    }

    .main-box {
        flex: 3;
    }

    .side-box {
        flex: 1;
        padding-left: 20px;
    }

    @media (max-width: 768px) {
        .form-container {
            flex-direction: column;
        }

        .main-box,
        .side-box {
            flex: 1 0 100%;
            padding-left: 0;
        }
    }

    /* Estilos para las notas */
    .notes-section-wrapper {
        margin-top: 30px;
    }

    .notes-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .notes-section h3 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #0073aa;
        padding-bottom: 10px;
    }

    .note-item {
        background: #f9f9f9;
        border-left: 4px solid #0073aa;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 3px;
        transition: background-color 0.2s ease;
    }

    .note-item:hover {
        background: #f0f0f0;
    }

    .note-content {
        margin-bottom: 8px;
        line-height: 1.5;
        color: #333;
    }

    .note-meta {
        font-size: 12px;
        color: #666;
        border-top: 1px solid #e0e0e0;
        padding-top: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .note-meta a.delete-note {
        color: #a00;
        text-decoration: none;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 3px;
        transition: background-color 0.2s ease;
    }

    .note-meta a.delete-note:hover {
        background: #f0f0f0;
        color: #d00;
    }

    .add-note-form {
        margin-bottom: 20px;
        background: #f8f8f8;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .add-note-form h4 {
        margin-top: 0;
        color: #333;
    }

    .add-note-form textarea {
        width: 100%;
        resize: vertical;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 8px;
    }

    .add-note-form textarea:focus {
        border-color: #0073aa;
        box-shadow: 0 0 0 1px #0073aa;
        outline: none;
    }

    /* Estilos para la sección de email */
    .email-section-wrapper {
        margin-top: 30px;
    }

    .email-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .email-section h3 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #28a745;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }

    #send-email-form {
        background: #f0f8f0;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #28a745;
    }

    #send-email-form input[type="text"],
    #send-email-form textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        transition: border-color 0.2s ease;
    }

    #send-email-form input[type="text"]:focus,
    #send-email-form textarea:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 1px #28a745;
        outline: none;
    }

    #send-email-form .button-primary {
        background: #28a745;
        border-color: #28a745;
        transition: background-color 0.2s ease;
    }

    #send-email-form .button-primary:hover {
        background: #218838;
        border-color: #218838;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchInput = document.getElementById('is_public');
        const switchLabels = document.querySelectorAll('.switch-label-text');

        function updateLabels() {
            if (switchInput.checked) {
                // Público - resaltar el texto "Público"
                switchLabels[0].style.color = '#666'; // Privado
                switchLabels[1].style.color = '#0073aa'; // Público
                switchLabels[1].style.fontWeight = '600';
                switchLabels[0].style.fontWeight = '500';
            } else {
                // Privado - resaltar el texto "Privado"
                switchLabels[0].style.color = '#d63638'; // Privado
                switchLabels[1].style.color = '#666'; // Público
                switchLabels[0].style.fontWeight = '600';
                switchLabels[1].style.fontWeight = '500';
            }
        }

        // Actualizar al cargar la página
        updateLabels();

        // Actualizar cuando cambie el switch
        switchInput.addEventListener('change', updateLabels);

        // Efecto de click en las etiquetas
        switchLabels.forEach(function(label, index) {
            label.addEventListener('click', function() {
                switchInput.checked = (index === 1); // 0 = Privado, 1 = Público
                updateLabels();
            });

            // Hacer las etiquetas clickeables visualmente
            label.style.cursor = 'pointer';
            label.style.userSelect = 'none';
            label.style.transition = 'all 0.2s ease';
        });
    });
</script>