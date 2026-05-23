# Bitácora · Mini-entrega 1 — Open Closed Principle - OCP

**Nombre:** Kathleen Argueta
**Fecha:** 2026-05-20
**Mini-entrega:** N1
**Módulo refactorizado:** Ordenes

---

## 1. ¿Qué problema de diseño identifiqué en el legacy?

Hay una violación del principio OCP en OrderItem.php. El modelo está usando cadenas de if y elseif para adivinar qué objeto retornar según el item_type y para recalcular el precio. Esto es un error porque el código no está cerrado a la modificación; está amarrado a los tipos existentes. Si el cliente decide vender suscripciones o servicios, me tocaría venir a modificar este archivo para meter otro elseif. Está mal porque cada vez que el negocio crezca, este archivo va a terminar siendo un enredo de validaciones cuando su función debe ser de representar un item.

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Models/OrderItem.php, líneas 15-19
if ($this->item_type === 'product') {
    return Product::find($this->item_id);
} elseif ($this->item_type === 'bundle') {
    return ProductBundle::find($this->item_id);
}
```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema?

Apliqué polimorfismo a través de las relaciones morphTo de Laravel. En lugar de tener un método con mil ifs intentando identificar qué es cada cosa, delegué esa responsabilidad a la base de datos y al motor de Eloquent. Ahora OrderItem simplemente apunta a un item o "comprable". Si el día de mañana agrego 10 tipos de productos nuevos, no tengo que tocar ni una línea de este modelo, porque ya es capaz de extenderse sin ser modificado.

**Nombra las clases nuevas o modificadas en tu refactor:**

- OrderItem — refactorizada: Borré los métodos con lógica de decisión y los reemplacé por una relación mucho más friendly.

---

## 3. ¿Qué patrón descartaste y por qué?

Descarté crear una tabla única que guardara todos los productos, bundles y futuros servicios mezclados. Sería un relajo de base de datos porque me llenaría de columnas con valores null y datos que no corresponden a todos los tipos.

---

## 4. ¿Qué trade-off aceptaste?

Ahora la base de datos es más estricta con los nombres de las clases. Al usar relaciones, en la columna item_type se guarda el namespace completo. Si a alguien se le ocurre renombrar una clase y se le olvida actualizar la base, la relación se malogra. Pero prefiero ese riesgo a seguir manteniendo una lista infinita de if y else.

---

## 5. ¿Qué cambiarías si tuvieras que hacerlo de nuevo?

Probablemente definiría un Morph Map en el AppServiceProvider para usar nombres cortos y así evitar que el diseño se malee si decido mover las clases de carpeta en el futuro.
