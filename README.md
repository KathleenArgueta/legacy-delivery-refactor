# Legacy Delivery: refactoring with design patterns

Refactoring of a working but poorly designed Laravel delivery marketplace. I identified design problems, applied the right pattern while keeping the test suite green, and documented every decision in a log: the problem I saw, the pattern I applied, the one I ruled out, and the trade-off I accepted.

| | |
|---|---|
| **Author** | Kathleen Argueta |
| **Course** | DA0921 Software Design Patterns, ESEN (2026) |
| **Stack** | PHP 8.4 · Laravel · PHPUnit · Docker |
| **Portfolio** | [kathleenargueta.github.io](https://kathleenargueta.github.io) |

## What I did

| Assignment | Problem in the legacy code | Decision |
|---|---|---|
| Short assignment 1 | Code smells across the domain | Inventory of smells in a table |
| Milestone 1 (SOLID) | SOLID violations in `Customer`, `Notification`, `OrderItem` and `CourierController` | SRP, OCP, ISP and DIP: `NotifiableInterface`, `CourierPolicy` for authorization, and injection in `AppServiceProvider` |
| Milestone 2 (Strategy) | `Discount::apply` with a `switch` that grows with every new promotion | One class per discount type (`app/Discounts/`) behind `DiscountInterface` |
| Milestone 2 (Template Method) | `Customer::placeOrder` split by an `if` between cash and Wompi payments | Abstract `OrderProcessor` with `CashOrderProcessor` and `WompiOrderProcessor` (`app/Processors/`) |

Decision logs (in Spanish): [Milestone 2](bitacoras/me2-bitacora.md) · [Milestone 1](https://github.com/KathleenArgueta/legacy-delivery-refactor/tree/81d356152f1d1aaa0920138294faa9b63dc96352/bitacoras)

> Shared with the instructor's permission.

---

## About the base project (original course README, in Spanish)

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
