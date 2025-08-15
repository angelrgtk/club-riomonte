<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="get" action="" style="margin-bottom: 20px;">
    <input type="hidden" name="page" value="club-riomonte">
    <label for="subscription_status">Estado de Suscripción:</label>
    <select name="subscription_status" id="subscription_status">
        <option value="">Todos</option>
        <option value="active" <?php if (isset($_GET['subscription_status']) && $_GET['subscription_status'] == 'active') echo 'selected'; ?>>Activa</option>
        <option value="inactive" <?php if (isset($_GET['subscription_status']) && $_GET['subscription_status'] == 'inactive') echo 'selected'; ?>>Inactiva</option>
    </select>
    <button type="submit" class="button">Buscar</button>
</form>