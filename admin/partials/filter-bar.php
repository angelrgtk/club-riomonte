<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="get" action="" style="margin-bottom: 20px;">
    <input type="hidden" name="page" value="club-riomonte">

    <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
        <div>
            <label for="search_term">Buscar:</label>
            <input type="text"
                name="search_term"
                id="search_term"
                value="<?php echo isset($_GET['search_term']) ? esc_attr($_GET['search_term']) : ''; ?>"
                placeholder="Buscar por cédula, email, nombre o apellido..."
                style="width: 300px;"
                minlength="3">
        </div>

        <div>
            <label for="expiration_date">Estado por Expiración:</label>
            <select name="expiration_date" id="expiration_date">
                <option value="">Todos</option>
                <option value="expired" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expired') echo 'selected'; ?>>Expirados</option>
                <option value="expiring_7_days" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expiring_7_days') echo 'selected'; ?>>Expira en los próximos 7 días</option>
                <option value="expiring_30_days" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expiring_30_days') echo 'selected'; ?>>Expira en los próximos 30 días</option>
            </select>
        </div>

        <div>
            <label for="is_public">Privacidad:</label>
            <select name="is_public" id="is_public">
                <option value="">Todos</option>
                <option value="1" <?php if (isset($_GET['is_public']) && $_GET['is_public'] == '1') echo 'selected'; ?>>Publico</option>
                <option value="0" <?php if (isset($_GET['is_public']) && $_GET['is_public'] == '0') echo 'selected'; ?>>Privado</option>
            </select>
        </div>

        <div>
            <button type="submit" class="button">Buscar</button>
            <?php if (!empty($_GET['search_term']) || !empty($_GET['expiration_date']) || !empty($_GET['is_public'])): ?>
                <a href="?page=club-riomonte" class="button button-link-delete">Limpiar</a>
            <?php endif; ?>
        </div>

        <div>
            <a href="?page=club-riomonte&action=export_csv<?php
                                                            $export_params = [];
                                                            if (!empty($_GET['search_term'])) $export_params[] = 'search_term=' . urlencode($_GET['search_term']);
                                                            if (!empty($_GET['expiration_date'])) $export_params[] = 'expiration_date=' . urlencode($_GET['expiration_date']);
                                                            if (!empty($_GET['is_public'])) $export_params[] = 'is_public=' . urlencode($_GET['is_public']);
                                                            echo !empty($export_params) ? '&' . implode('&', $export_params) : '';
                                                            ?>" class="button button-primary" style="background: #0073aa;">
                <span class="dashicons dashicons-download" style="margin-right: 5px; margin-top: 3px;"></span>
                Exportar CSV
            </a>
        </div>
    </div>
</form>