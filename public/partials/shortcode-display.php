<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// This file is included from the shortcode class
// Variables available: $atts, $member, $error_message, $search_value

?>

<style>
    .club-riomonte-lookup-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .club-riomonte-lookup-container * {
        box-sizing: border-box;
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
        box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.2);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .club-member-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    .club-profile-image {
        width: 300px;
        height: 300px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .club-profile-placeholder {
        width: 200px;
        height: 200px;
        border-radius: 8px;
        background: #f5f5f5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .club-member-info {
        flex: 1;
    }

    .club-member-name {
        margin: 0 0 10px 0;
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }

    .club-member-id {
        margin: 0;
        color: #666;
        font-size: 16px;
    }

    .club-details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .club-details-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .club-details-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .club-details-label {
        font-weight: 600;
        font-size: 18px;
        color: #555;
        width: 40%;
    }

    .club-details-value {
        color: #333;
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .club-riomonte-lookup-container {
            margin: 10px;
            padding: 15px;
        }

        .club-member-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .club-profile-image,
        .club-profile-placeholder {
            width: 150px;
            height: 150px;
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
</style>

<div class="club-riomonte-lookup-container">

    <!-- Title -->
    <h3 style="text-align: center; margin-bottom: 30px; color: #333; font-size: 24px;">
        <?php echo esc_html($atts['title']); ?>
    </h3>

    <!-- Search form -->
    <form method="post" class="club-search-form">
        <div style="margin-bottom: 20px;">
            <label for="club_search_id" style="display: block; margin-bottom: 10px; font-weight: 600; color: #555; font-size: 16px;">
                <?php echo esc_html($atts['search_label']); ?>
            </label>
            <input type="text" id="club_search_id" name="club_search_id"
                value="<?php echo esc_attr($search_value); ?>"
                required class="club-search-input"
                placeholder="Número de documento...">
        </div>
        <button type="submit" name="club_search_submit" class="club-search-button">
            <?php echo esc_html($atts['button_text']); ?>
        </button>
    </form>

    <!-- Display error message -->
    <?php if ($error_message): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
            <strong>Error:</strong> <?php echo esc_html($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Display member information if found -->
    <?php if ($member): ?>
        <div style="background: #e8f5e8; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500; font-size: 18px;">
            <strong>✅ ¡Miembro Encontrado!</strong> Aquí está tu información:
        </div>

        <div class="club-member-card">
            <div class="club-member-header">
                <?php if ($member->profile_picture): ?>
                    <?php $image_url = wp_get_attachment_image_url($member->profile_picture, 'large'); ?>
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" class="club-profile-image" alt="Profile Picture">
                    <?php else: ?>
                        <div class="club-profile-placeholder">👤</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="club-profile-placeholder">👤</div>
                <?php endif; ?>

                <h2 class="club-member-name"><?php echo esc_html($member->first_name . ' ' . $member->last_name); ?></h2>
                <p class="club-member-id">Cédula: <?php echo esc_html($member->gov_id); ?></p>
            </div>

            <div style="padding: 20px;">
                <table class="club-details-table">
                    <?php
                    $fields = array(
                        'Estado de Suscripción' => (!empty($member->expiration_date) && $member->expiration_date >= date('Y-m-d')) ? '<span style="color: #4caf50; font-weight: 600;">✅ Activa</span>' : '<span style="color: #f44336; font-weight: 600;">❌ Inactiva</span>',
                        'Expira' => $member->expiration_date ? date_i18n('j \\d\\e F \\d\\e Y', strtotime($member->expiration_date)) : 'N/A',
                        'Miembro Desde' => date_i18n('j \\d\\e F \\d\\e Y', strtotime($member->created_at))
                    );

                    foreach ($fields as $label => $value):
                    ?>
                        <tr>
                            <td class="club-details-label"><?php echo esc_html($label); ?></td>
                            <td class="club-details-value"><?php echo $value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>