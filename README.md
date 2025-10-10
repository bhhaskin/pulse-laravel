# Pulse Laravel

`bhhaskin/pulse-laravel` provides a lightweight Laravel package that exposes API endpoints consumed by **Pulse JS**. It offers a drop-in route group with sensible defaults and can be extended to surface real-time metrics or diagnostic data for frontend dashboards.

## Installation

```bash
composer require bhhaskin/pulse-laravel
```

The package is auto-discovered by Laravel so no manual service provider registration is required.

## Configuration

Publish the configuration file to customize the route prefix, middleware stack, or stub data returned by the endpoint:

```bash
php artisan vendor:publish --tag=pulse-laravel-config
```

Key options inside `config/pulse-laravel.php`:

- `prefix`: URL segment that prefixes all Pulse routes. Defaults to `pulse`.
- `middleware`: Middleware applied to the Pulse route group. Defaults to the `api` stack.

## Routes

Once installed the package registers the `/pulse` endpoint (unless you change the prefix) which is designed to be triggered via `navigator.sendBeacon`. The included controller currently acknowledges requests with an empty `204 No Content` response, giving you a clean slate to hook into data sinks or job dispatching as Pulse evolves.

## Testing

Run the test suite locally with:

```bash
composer test
```

The tests rely on [`orchestra/testbench`](https://github.com/orchestral/testbench) to bootstrap a lightweight Laravel application context for package development.
