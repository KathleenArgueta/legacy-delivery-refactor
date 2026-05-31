# Bitácora · Mini-entrega 1 — Dependency Inversion Principle - DIP

**Nombre:** Kathleen Argueta
**Fecha:** 2026-05-20
**Mini-entrega:** N1
**Módulo refactorizado:** Notificaciones

---

## 1. ¿Qué problema de diseño identifiqué en el legacy?

En el método send del modelo notification. La clase está esclavizada a implementaciones porque usa el operador new para levantar servicios como EmailService o SMSService directamente en su lógica. Esto es un error de arquitectura, ya que el modelo no debería estar pendiente de detalles de bajo nivel. Si el proveedor de notificaciones cambia o queremos correr pruebas, el código se rompe porque está enlazado a esas clases externas. Básicamente, la notificación está siendo una fábrica de servicios

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Models/Notification.php, líneas 58-60
if ($this->channel === 'email') {
    $service = new \App\Services\EmailService();
```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema?

Implementé inyección de dependencias apoyándome en un service provider. En lugar de permitir que el modelo instancie servicios a lo loco, definí un contrato para que el modelo solo se preocupe por dar la orden de envío. Ahora, el flujo es mucho más estable, el sistema le entrega al modelo la herramienta ya lista para usar.

**Nombra las clases nuevas o modificadas en tu refactor:**

- Notification — refactoed: Se eliminaron los new y la cadena de if que decidía qué servicio crear.

- NotificationChannelInterface — newbie: El contrato que establece cómo debe lucir cualquier canal de envío.

---

## 3. ¿Qué patrón descartaste y por qué?

Descarté el uso de un Static Facade. Aunque las Facades de Laravel ocultan el new, por debajo siguen manteniendo un acoplamiento oculto que hace que testear sea un calvario. Preferí la inyección porque hace que las dependencias de la clase sean totalmente transparentes y fáciles de swappear si el negocio lo pide.

---

## 4. ¿Qué trade-off aceptaste?

La configuración del sistema requiere más puntos de contacto. Ya no basta con ver el modelo; ahora hay que entender cómo está configurado el container de Laravel en el AppServiceProvider. Se gana una barbaridad en flexibilidad, pero el flujo se vuelve un poco más abstracto para alguien que no conozca el patrón.

---

## 5. ¿Qué cambiarías si tuvieras que hacerlo de nuevo?

Sin duda, montaría un Manager pattern para gestionar los canales, permitiendo que el sistema decida en tiempo de ejecución qué proveedor usar sin que el modelo de notificación tenga que enterarse de nada
