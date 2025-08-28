<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Obtener los filtros
$filters = [
    'expiration_date' => isset($_GET['expiration_date']) ? $_GET['expiration_date'] : null,
    'search_term' => isset($_GET['search_term']) && strlen($_GET['search_term']) >= 3 ? $_GET['search_term'] : null,
    'is_public' => isset($_GET['is_public']) ? $_GET['is_public'] : null,
];

// Obtener los miembros filtrados
$members = Club_Riomonte_Database::get_all_members($filters);

?>

<div class="wrap">
    <div class="notice notice-info">
        <p>Usa el shortcode <code>[club_riomonte_lookup]</code> para mostrar el formulario de consulta de miembros en cualquier página o post.</p>
        <p>Atributos opcionales:</p>
        <ul style="list-style-type: disc; margin-left: 20px;">
            <li><code>title</code> - Título personalizado para el formulario (por defecto: "Consulta de Miembro")</li>
            <li><code>search_label</code> - Etiqueta personalizada para el campo de búsqueda (por defecto: "Ingresa tu Cédula o ID de Miembro")</li>
            <li><code>button_text</code> - Texto personalizado para el botón (por defecto: "Buscar")</li>
        </ul>
        <p>Ejemplo: <code>[club_riomonte_lookup title="Buscar Miembro" search_label="Ingresa ID" button_text="Consultar"]</code></p>
    </div>

    <h1 class="wp-heading-inline">Miembros Club Riomonte</h1>
    <a href="?page=club-riomonte&action=create" class="page-title-action">Agregar Nuevo Miembro</a>
    <hr class="wp-header-end" style="margin-bottom: 30px;">

    <?php
    // Include the filter bar - always show it
    include CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/filter-bar.php';
    ?>

    <?php if (empty($members)): ?>
        <div class="notice notice-info">
            <p>No se encontraron miembros. <a href="?page=club-riomonte&action=create">Agrega tu primer miembro</a>.</p>
        </div>
    <?php else: ?>
        <?php require_once CLUB_RIOMONTE_PLUGIN_DIR . 'admin/partials/member-list.php'; ?>
    <?php endif; ?>
</div>