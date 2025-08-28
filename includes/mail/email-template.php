<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template de email para Club Riomonte
 * 
 * Variables disponibles:
 * - $member: Objeto con datos del miembro
 * - $custom_message: Mensaje personalizado del administrador
 * - $site_name: Nombre del sitio
 * - $site_url: URL del sitio
 */

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($site_name); ?></title>
    <style>
        /* Estilos CSS inline para compatibilidad con clientes de email */
        .email-container {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        .email-body {
            background: #fff;
            padding: 30px 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .greeting {
            color: #333;
            margin-top: 0;
        }

        .custom-message {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #0073aa;
            margin: 20px 0;
        }

        .divider {
            border: none;
            height: 1px;
            background: #eee;
            margin: 30px 0;
        }

        .member-info {
            background: #f0f8f0;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            color: #666;
        }

        .member-info strong {
            color: #333;
        }

        .status-active {
            color: #28a745;
        }

        .status-inactive {
            color: #dc3545;
        }

        .email-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
        }

        .email-footer a {
            color: #0073aa;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header del email -->
        <div class="email-header">
            <h1>Club Riomonte</h1>
            <p>Comunicación Oficial</p>
        </div>

        <!-- Cuerpo del email -->
        <div class="email-body">
            <h2 class="greeting">Hola <?php echo esc_html($member->first_name); ?>,</h2>

            <!-- Mensaje personalizado -->
            <div class="custom-message">
                <?php echo nl2br(esc_html($custom_message)); ?>
            </div>

            <hr class="divider">

            <!-- Información del miembro -->
            <div class="member-info">
                <strong>Información de tu membresía:</strong><br><br>
                <strong>Nombre:</strong> <?php echo esc_html($member->first_name . ' ' . $member->last_name); ?><br>
                <strong>Cédula:</strong> <?php echo esc_html($member->gov_id); ?><br>
                <strong>Email:</strong> <?php echo esc_html($member->email); ?><br>
                <strong>Estado:</strong>
                <?php
                $is_active = !empty($member->expiration_date) && $member->expiration_date >= date('Y-m-d');
                if ($is_active): ?>
                    <span class="status-active">✅ Activa</span>
                <?php else: ?>
                    <span class="status-inactive">❌ Inactiva</span>
                <?php endif; ?>
                <br>
                <?php if ($member->expiration_date): ?>
                    <strong>Expira:</strong> <?php echo date_i18n('j \\d\\e F \\d\\e Y', strtotime($member->expiration_date)); ?><br>
                <?php endif; ?>
                <?php if ($member->last_payment_date): ?>
                    <strong>Último Pago:</strong> <?php echo date_i18n('j \\d\\e F \\d\\e Y', strtotime($member->last_payment_date)); ?><br>
                <?php endif; ?>
            </div>

            <!-- Footer del email -->
            <div class="email-footer">
                <p>Este email fue enviado desde <strong><?php echo esc_html($site_name); ?></strong></p>
                <p><a href="<?php echo esc_url($site_url); ?>">Visitar nuestro sitio web</a></p>
            </div>
        </div>
    </div>
</body>

</html>