# Legacy Delivery: refactor con patrones de diseño

**ES** · Refactor de un marketplace de delivery en Laravel que funciona pero está mal diseñado. Mi trabajo fue detectar los problemas de diseño, aplicar el patrón adecuado sin romper los tests y documentar cada decisión en una bitácora: qué problema vi, qué patrón apliqué, cuál descarté y qué costo acepté.

**EN** · Refactoring of a working but poorly designed Laravel delivery marketplace. I identified design problems, applied the right pattern while keeping the test suite green, and documented every decision in a log: the problem, the pattern I applied, the one I ruled out, and the trade-off I accepted.

| | |
|---|---|
| **Autora / Author** | Kathleen Argueta |
| **Curso / Course** | DA0921 Patrones de Diseño de Software, ESEN (2026) |
| **Stack** | PHP 8.4 · Laravel · PHPUnit · Docker |
| **Portafolio** | [kathleenargueta.github.io](https://kathleenargueta.github.io) |

## Lo que hice / What I did

| Entrega | Problema en el legacy | Decisión |
|---|---|---|
| Tarea corta 1 | Code smells en el dominio | Inventario de smells en formato de tabla |
| Mini-entrega 1 (SOLID) | Violaciones de SOLID en `Customer`, `Notification`, `OrderItem` y `CourierController` | SRP, OCP, ISP y DIP: `NotifiableInterface`, `CourierPolicy` para autorización e inyección en `AppServiceProvider` |
| Mini-entrega 2 (Strategy) | `Discount::apply` con un `switch` que crece con cada promoción | Una clase por tipo de descuento (`app/Discounts/`) detrás de `DiscountInterface` |
| Mini-entrega 2 (Template Method) | `Customer::placeOrder` partido por un `if` entre efectivo y Wompi | `OrderProcessor` abstracto con `CashOrderProcessor` y `WompiOrderProcessor` (`app/Processors/`) |

Bitácoras: [Mini-entrega 2](bitacoras/me2-bitacora.md) · [Mini-entrega 1](https://github.com/KathleenArgueta/legacy-delivery-refactor/tree/81d356152f1d1aaa0920138294faa9b63dc96352/bitacoras)

> Compartido con autorización del catedrático. / Shared with the instructor's permission.

---

## Sobre el proyecto base

Marketplace de delivery multi-vendor construido en PHP 8.3 / Laravel 13.
Usado como caso de estudio en **DA0921 Patrones de Diseño de Software — ESEN, ciclo 2026-2**.

El código funciona. Está mal diseñado. Esa es la premisa.

---

## Contenido

- [Inicio rápido](#inicio-rápido)
- [Credenciales de prueba](#credenciales-de-prueba)
- [Tests](#tests)
- [Cómo trabajar en este repo](#cómo-trabajar-en-este-repo)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Tags de Git](#tags-de-git)

---

## Inicio rápido

Lee `SETUP_GUIDE.md` para instrucciones detalladas. Versión corta:

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Sistema disponible en `http://localhost:8000` · API en `http://localhost:8000/api`

## Credenciales de prueba

| Rol      | Email                | Contraseña |
|----------|----------------------|------------|
| Admin    | admin@legacy.dev     | Password1  |
| Customer | customer1@legacy.dev | Password1  |
| Vendor   | vendor1@legacy.dev   | Password1  |
| Courier  | courier1@legacy.dev  | Password1  |

## Tests

```bash
docker compose exec app php artisan test
```

Los tests deben pasar en verde antes y después de cada refactor. Son tu red de seguridad.

## Cómo trabajar en este repo

Lee `STUDENT_GUIDE.md`. El flujo es:

1. Crear un branch por cada mini-entrega (`me1`, `me2`, ...)
2. Refactorizar aplicando el patrón asignado
3. Verificar que los tests siguen en verde
4. Escribir la bitácora en `/bitacoras/`
5. Abrir un Pull Request a `main`

## Estructura del proyecto

```
app/
  Models/          — 13 entidades del dominio (Order, Customer, Vendor, Discount, ...)
  Http/Controllers/— Controllers API y Web
  Services/        — Servicios externos (pagos, email, SMS, push)
  Reports/         — Generadores de reportes (PDF, CSV, Excel)
  Support/         — Logger
  BadPatterns/     — Galería de anti-patrones (no tocar)
resources/views/   — Vistas Blade
tests/
  Unit/            — Tests unitarios de modelos
  Feature/         — Tests de integración via HTTP
bitacoras/         — Tus bitácoras de decisiones (una por mini-entrega)
```

## Tags de Git

| Tag | Estado |
|-----|--------|
| `v0` | Punto de partida — el legacy tal como fue entregado |
| `v1` | Referencia tras Mini-entrega 1 |
| `v2` | Referencia tras Mini-entrega 2 |
| `v3` | Referencia tras Mini-entrega 3 |
| `v4` | Referencia tras Mini-entrega 4 |
| `v5` | Referencia tras Mini-entrega 5 |
