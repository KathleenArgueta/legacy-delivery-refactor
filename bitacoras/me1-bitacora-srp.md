# Bitácora · Mini-entrega 1 —  Single Responsability Principle - SRP

**Nombre:** Kathleen Argueta
**Fecha:** 2026-05-20
**Mini-entrega:** N°1
**Módulo refactorizado:** Couriers

---

## 1. ¿Qué problema de diseño identifiqué en el legacy?

Identifiqué una violación de SRP en el CourierController, lineas 19 a 22 y luego 39 a la 41. El controlador, que solo debería estar pendiente de recibir la petición y devolver una respuesta, se está metiendo manejando la lógica de autorización. Se pone a validar roles de admin y courier; a comparar IDs de usuario, a mano, en cada método. Esto está fatal porque el controlador termina teniendo más de una responsabilidad. Si en un futuro me piden cambiar la ruta de la API o si se le antoja cambiar quién tiene permiso de editar a un repartidor, me toca venir a este mismo archivo a mover todo. Está haciendo tareas que nada que ver con su función principal.

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Http/Controllers/CourierController.php, líneas 14-20
if ($user->role !== 'courier' || $courier->user_id !== $user->id) {
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema?

Saqué toda esa lógica de permisos y la mandé a una Policy de Laravel. La idea es dejar de saturar al controlador con validaciones de seguridad y delegárselo a una clase que sí sepa qué onda con los permisos. Ahora el controlador está mucho más smooth porque solo pregunta si el usuario tiene permiso y ya, dejando que la Policy se encargue de las reglas.

**Nombra las clases nuevas o modificadas en tu refactor:**

- CourierController — Modificada: Le quité el montón de ifs de validación y ahora solo llama a la Policy.

- CourierPolicy — newbie: Es la que ahora se encarga de decidir quién puede o no meterle mano a los datos del repartidor.

---

## 3. ¿Qué patrón descartaste y por qué?

Descarté incoporar toda esa lógica en un Middleware. Aunque el Middleware limpia el controlador, es un dolor de cabeza cuando la regla depende de datos específicos del modelo. Me hubiera tocado andar parseando la URL o haciendo consultas extra solo para validar el permiso. La Policy es mucho más natural en Laravel porque ya recibe el objeto Courier listo para validar.

---

## 4. ¿Qué trade-off aceptaste?

Acepté el costo de fragmentar la lógica en más archivos. Ahora agregué una clase nueva CourierPolicy y eso significa que si alguien quiere entender el flujo completo de cómo se actualiza un repartidor, ya no le basta con ver el controlador; tiene que saltar a la Policy para ver si tiene permiso y luego volver. También acepté que el AuthServiceProvider se vuelva un poquito más pesado al tener que mapear estas relaciones. Sacrifiqué la centralización de ver todo en un solo archivo a cambio de tener orden.

---

## 5. ¿Qué cambiarías si tuvieras que hacerlo de nuevo?

Si tuviera más tiempo, también sacaría la validación de los datos latitude y  longitude a un Form Request, para que el controlador no tenga ni que saber qué campos son obligatorios y quede todavía más limpio.
