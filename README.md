# Pulse Laravel

`bhhaskin/pulse-laravel` provides a lightweight Laravel package that exposes API endpoints consumed by **Pulse JS**. It offers a drop-in route group with sensible defaults and can be extended to surface real-time metrics or diagnostic data for frontend dashboards.

## Installation

```bash
composer require bhhaskin/pulse-laravel
```

The package is auto-discovered by Laravel so no manual service provider registration is required.

## Configuration

Publish the configuration file to customize the route prefix or middleware stack applied to the beacon endpoint:

```bash
php artisan vendor:publish --tag=pulse-config
```

Key options inside `config/pulse.php`:

- `prefix`: URL segment that prefixes all Pulse routes. Defaults to `pulse`.
- `middleware`: Middleware applied to the Pulse route group. Defaults to the `api` stack.

## Routes

Once installed the package registers the `/pulse` endpoint (unless you change the prefix) which is designed to be triggered via `navigator.sendBeacon`. The included controller currently acknowledges requests with an empty `204 No Content` response, giving you a clean slate to hook into data sinks or job dispatching as Pulse evolves.

## Data Flow

- Incoming batches are captured once in the `pulse_raw_batches` table; the batch processor validates each event and immediately materializes reporting records.
- The processor keeps the downstream tables in sync:
  - `pulse_clients` keeps per-client aggregates.
  - `pulse_sessions` ties events back to a client session.
  - `pulse_devices` stores the first-party device fingerprint details (category, OS, viewport, capabilities, browser name/version, etc.) alongside the associated user agent.
  - `pulse_events` holds query-friendly event records and keeps the original `payload` blob intact so you can enrich analytics with any custom fields Pulse JS sends along.
- Incoming events are validated before persistence; malformed records are skipped so downstream tables stay clean.
- The package auto-loads its migrations; publish them via `php artisan vendor:publish --tag=pulse-migrations` if you need to override the schema.

Because the processing job implements `ShouldQueue`, make sure your queue worker is running in production, or set `QUEUE_CONNECTION=sync` if you prefer in-process execution.

## Factories & Seeders

- Publish the package factories with `php artisan vendor:publish --tag=pulse-factories` and seeders with `php artisan vendor:publish --tag=pulse-seeders` if you want to customise or extend them inside your application.
- You can quickly generate demo data by running the included seeder: `php artisan db:seed --class="\Bhhaskin\Pulse\Database\Seeders\PulseDatabaseSeeder"`.
- When writing package-aware tests, the model factories are auto-loaded so you can use helpers like `PulseClient::factory()` out of the box.

## Testing

Run the test suite locally with:

```bash
composer test
```

The tests rely on [`orchestra/testbench`](https://github.com/orchestral/testbench) to bootstrap a lightweight Laravel application context for package development.
