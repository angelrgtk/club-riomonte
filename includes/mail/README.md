# Templates de Email - Club Riomonte

Este directorio contiene los templates de email utilizados por el plugin Club Riomonte.

## Archivos

### `email-template.php`

Template principal para emails enviados a los miembros del club.

#### Variables disponibles:

- `$member` - Objeto con todos los datos del miembro
- `$custom_message` - Mensaje personalizado ingresado por el administrador
- `$site_name` - Nombre del sitio WordPress
- `$site_url` - URL del sitio WordPress

#### Datos del miembro disponibles:

- `$member->id` - ID único del miembro
- `$member->gov_id` - Cédula o documento de identidad
- `$member->first_name` - Nombre
- `$member->last_name` - Apellido
- `$member->email` - Correo electrónico
- `$member->phone` - Teléfono
- `$member->birth_date` - Fecha de nacimiento
- `$member->expiration_date` - Fecha de expiración de membresía
- `$member->last_payment_date` - Fecha del último pago
- `$member->is_public` - Si el miembro es público (1) o privado (0)
- `$member->created_at` - Fecha de creación del registro

## Personalización

### Estilos CSS

Los estilos están incluidos inline en el `<head>` del template para máxima compatibilidad con clientes de email.

### Modificar el diseño

1. Edita `email-template.php` directamente
2. Los cambios se aplicarán automáticamente a todos los emails enviados
3. Mantén los estilos CSS inline para compatibilidad

### Crear templates adicionales

1. Crea un nuevo archivo PHP en este directorio
2. Modifica el método `build_email_template()` en `class-admin-pages.php` para usar el nuevo template
3. Asegúrate de incluir las mismas variables disponibles

## Buenas prácticas

- **Estilos inline**: Usa estilos CSS inline para máxima compatibilidad
- **Imágenes**: Usa URLs absolutas para imágenes
- **Texto**: Siempre escapa el output con `esc_html()` y `esc_url()`
- **Responsive**: Considera dispositivos móviles en el diseño
- **Testing**: Prueba en diferentes clientes de email

## Compatibilidad

Este template ha sido diseñado para funcionar en:

- Gmail
- Outlook (todas las versiones)
- Apple Mail
- Yahoo Mail
- Dispositivos móviles

## Ejemplo de uso

```php
// En class-admin-pages.php
$template = $this->build_email_template($member, $custom_message);
wp_mail($to, $subject, $template, $headers);
```
