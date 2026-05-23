# Bitácora · Mini-entrega 1 — Interface Segregation Principle - ISP

**Nombre:** Kathleen Argueta
**Fecha:** 2026-05-20
**Mini-entrega:** N°1
**Módulo refactorizado:** User

---

## 1. ¿Qué problema de diseño identifiqué en el legacy?

En el modelo User.php la clase está amontonando todos los parámetros y métodos en un solo lugar. Esto obliga a que un admin cargue con lógica de notificaciones de pedidos que solo le competen al customer, o que un repartidor tenga acceso a métodos que quizás no usa. Básicamente, el modelo User está obligando a cada rol a heredar comportamientos fuera de lugar

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Models/User.php, líneas 10 a 12
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}

```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema?

Apliqué la segregación de interfaces y traits. En vez de tener un User que maneje todas las acciones, segregué las responsabilidades. Creé un contrato NotifiableInterface para que solo los modelos que de verdad necesitan alertas lo firmen. Ahora el flujo es mucho más lineal y el modelo User solo mantiene lo básico de la cuenta y delega los métodos extra a interfaces que si lo usan. Así, ya no le pedimos a un Admin que sepa manejar notificaciones de entrega si no le corresponde.

**Nombra las clases nuevas o modificadas en tu refactor:**

- User — Refactored: Se le quitaron los traits que no son comunes a todos los roles para aligerar la clase.

- NotifiableInterface — newbie: El contrato define qué modelos pueden recibir info, separando esta capacidad del modelo anterior.

---

## 3. ¿Qué patrón descartaste y por qué?

No usé la herencia multi tabla. Ya que, aunque eso separaría los datos, en el código seguiría teniendo el mismo enredo de interfaces si no separo los contratos. La segregación por interfaces es más directa, menos dañina con la base de datos y resuelve el problema de raíz

---

## 4. ¿Qué trade-off aceptaste?

Ahora la gestión de notificaciones es un poquito más exigente. Ya no puedo asumir que cualquier entidad User tiene los métodos de Notifiable; ahora me toca validar si el usuario implementa la interfaz antes de tirarle una alerta.

---

## 5. ¿Qué cambiarías si tuvieras que hacerlo de nuevo?

Me gustaría usar el State pattern para que el comportamiento del usuario cambie según su rol, eliminando de paso ese montón de métodos isCustomer, isVendor, etc., que solo ensucian el modelo
