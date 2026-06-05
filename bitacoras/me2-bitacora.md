# Bitácora · Mini-entrega 2 — Patrones Bloque A - Strategy

**Nombre:** Kathleen Abigail Argueta Gómez
**Fecha:** 2026-06-05
**Mini-entrega:** N°2
**Módulo refactorizado:** Descuento

---

## 1. ¿Qué problema de diseño identifiqué en el legacy - Strategy ?

_El archivo del modelo del Discount.php me llamó la atención porque cuando estaba entendiendo cómo se realizaban las promociones, me topé con el problema del switch que crece y crece sin parar. Entonces, gracias a ello el método apply evalúa un condicional enorme para saber qué tipo de descuento aplicar . Se nota que la via fácil que se implementó en este código fue ir agregando cases por cada promo nueva. Gracias a esa decisión, se viola el principio Open Close ya que hay que modificar la clase principal cada vez._

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Models/Discount.php, línea 32
switch ($this->type) {
    case 'percentage':
        $discount = $order->subtotal * ($this->value / 100);
```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema - Strategy?

_Me fui por Strategy. Básicamente, el modelo no tiene por qué saber cómo calcular cada descuento, solo necesita que alguien le dé el resultado. Resuelve el problema porque separa la decisión de qué cálculo ejecutar del código que realmente lo utiliza. En lugar de tener el switch hardcodeado, extraje la lógica de cada tipo de descuento y cree una clase por cada uno, dentro de una carpeta que se llama Discounts (no los puse en modelos porque no estan en la BD) y dejé que el modelo Discount delegue el cálculo usando un mapa de variantes._

**Nombra las clases nuevas o modificadas en tu refactor:**

- App\Models\Discount — Actúa como el context, solo mantiene el registro y delega la acción, ya no calcula nada

- App\Contracts\DiscountInterface — Es mi Strategy, el contrato que me asegura que todos los descuentos cumplan con tener el método calculate

- App\Discounts\PercentageDiscount (y los otros 4 tipos de descuento) — Las estrategias, cada una encapsulando su propio algoritmo sin estorbarse entre sí

---

## 3. ¿Qué patrón descartaste y por qué - Strategy?

_Dudé un poco con State, porque a nivel de código la estructura se ve casi idéntica donde hay un objeto que delega en otro y que implementa una interfaz. Sin embargo, no le di mayor importancia porque el descuento no pasa por etapas de vida ni cambia de comportamiento por sí solo. Al descuento se le asigna un tipo desde afuera una sola vez y se ejecuta, no hay cambios entre un estado y otro. Por eso, me fui con strategy._

---

## Bitácora · Mini-entrega 2 — Patrones Bloque A - Template Method

---

## 1. ¿Qué problema de diseño identifiqué en el legacy - Template Method?

_Aunque en entregas pasadas ya había refactorizado Customer.php para arreglar otros smells con los principios de diseño, al darle otra revisada me di cuenta de que el método placeOrder seguía siendo un large method. Adicional a eso, justo a la mitad del proceso la lógica se parte en dos con un if para ver si el pago es en efectivo o con Wompi. Toda la validación del carrito, el cobro y los correos están ahí. Por esa lógica se viola el principio de responsabilidad única SRP, ya que si modificó algo de Wompi me arriesgo a romper la validación o el envío de correos._

**Cita el fragmento del legacy que ilustra el problema** (2-5 líneas de código, con su archivo y número de línea):

```php
// archivo: app/Models/Customer.php, línea 118
if ($paymentMethod === 'cash') {
    $payment = PaymentInCash::create([
// codigo zzz
} else {
    $wompiHandler = new \App\Services\Payments\WompiHandler();
```

---

## 2. ¿Qué patrón aplicaste y por qué resuelve este problema - Template Method?

_Apliqué template method porque el esqueleto del proceso de compra siempre es igual, lo único que cambia es el pasito de cómo cobrar. Entonces, se resuelve el problema extrayendo la lógica del proceso de compra a una clase abstracta y creando subclases que vayan llenando cada método de cobro, ya sea efectivo o wompy. Es estar reciclando el code en vez de estar haciendo uno a uno._

**Nombra las clases nuevas o modificadas en tu refactor:**

- App\Models\Customer — Quedó limpio, ahora solo instancia el procesador que toca y le pasa la información.

- App\Processors\OrderProcessor — Es mi clase abstracta que define el el proceso de compra y deja el paso de processPayment vacío.

- App\Processors\CashOrderProcessor y el de wompi — Las subclases que heredan el OrderProcessor y solo le ponen su propia forma de cobrar.

---

## 3. ¿Qué patrón descartaste y por qué - Template Method?

_Descarté strategy porque es más que todo para intercambiar los comportamientos. Howewer, con strategy se asume que se va a cambiar el algoritmo por completo. Si lo usaba, habría tenido que duplicar la validación y los correos en la estrategia de efectivo y repetirlos otra vez en la de Wompi. A pues como el proceso es general y no cambia, a excepción del proceso de pago, era mejor opción template method._

---

## 4. ¿Qué trade-off aceptaste?

_Me tocó soportar y aceptar el trade-off de crear más clases. Siendo específica, agregué 9 archivos nuevos al proyecto que son: 5 clases para las estrategias de descuento, 1 para el muy archivo de la interfaz, 1 clase abstracta para el esqueleto del pedido y 2 subclases para los métodos de pago. Pasé de tener la lógica amontonada en Customer.php y Discount.php a desmenuzarlo creando carpetas nuevas por aparte app/Discounts/, app/Processors/. La de contracts ya estaba.

Lo que se volvió más complejo es seguir el hilo de ejecución; ya no se puede leer todo el proceso de corrido, sino que ahora el código anda saltando de una carpeta a otra, lo que hace que debuggear vaya a ser un poco más rebuscado.

El escenario futuro que se hace más difícil con esta decisión es si el día de mañana me piden, por ejemplo, aplicar un descuento que dependa exclusivamente del método de pago tipo "10% de descuento solo si pagas con Wompi". Ahora gracias a ello los descuentos y los pagos están súper separados y el proceso de la orden quedó fijo, implementar una regla de negocio que cruce esas dos cosas me obligaría a romper el template o a hacer mas trabajo para pasar esa información._

---

## 5. ¿Qué cambiarías si tuvieras que hacerlo de nuevo?

_Cambiaría ese último if que dejé en Customer.php para elegir el método de pago, y mejor usaría un patrón de Factory Method para crear los procesadores._
