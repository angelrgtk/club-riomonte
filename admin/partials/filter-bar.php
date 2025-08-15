<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="get" action="" style="margin-bottom: 20px;">
    <input type="hidden" name="page" value="club-riomonte">
    <!-- Estado de suscripción basado en fecha de expiración; se elimina filtro explícito de activo/inactivo -->
    <label for="expiration_date">Estado por Expiración:</label>
    <select name="expiration_date" id="expiration_date">
        <option value="">Todos</option>
        <option value="expired" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expired') echo 'selected'; ?>>Expirados</option>
        <option value="expiring_7_days" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expiring_7_days') echo 'selected'; ?>>Expira en los próximos 7 días</option>
        <option value="expiring_30_days" <?php if (isset($_GET['expiration_date']) && $_GET['expiration_date'] == 'expiring_30_days') echo 'selected'; ?>>Expira en los próximos 30 días</option>
    </select>
    <button type="submit" class="button">Buscar</button>
</form>