# Product Service (Symfony 6.4)

A Symfony microservice that manages products, publishes product events to RabbitMQ and consumes order-related events.

## Prerequisites
- Docker
- Docker Compose

## Quick overview
- REST endpoints:
  - POST /products
  - GET /products
  - GET /products/{id}
- RabbitMQ for messaging (publish on create/update; consume order events).
- Postgres (product_db) as the service database (can be adjusted to MySQL if desired).

## Important notes before starting
- The application must connect to the database container host (service name in docker-compose, e.g. `product_db`) — do not use `localhost` when running PHP inside a container.
- Ensure PHP image used by Docker has needed PDO extensions (e.g. `pdo_pgsql` for Postgres or `pdo_mysql` for MySQL) installed during the build.
- Ensure `bin/console` exists in the project root in the built image before `composer install` runs.


## Installation & setup

1. Clone project
   ```sh
   git clone https://github.com/arman424/product_service
   cd product_service
   ```

2. Copy/inspect environment
   ```sh
   cp .env.example .env
   ```

3. Bring containers up (build a fresh image)
   ```sh
   docker compose up -d --build
   ```

4. Install PHP dependencies inside the running PHP container (if not already baked into image)
   ```sh
   docker compose exec product_service composer install --no-interaction --prefer-dist --optimize-autoloader
   ```

5. Create database and run migrations (run from inside the app container)
   ```sh
   docker compose exec product_service php bin/console doctrine:database:create --if-not-exists
   docker compose exec product_service php bin/console doctrine:migrations:migrate --no-interaction
   ```

6. Test APIs (example)
   ```sh
   curl -X POST http://localhost:YOUR_PORT/products \
     -H "Content-Type: application/json" \
     -d '{"name":"Coffee Mug","price":12.99,"quantity":100}'
   ```

## Consume order updates (exact command)
To start consuming RabbitMQ `order_events` (run interactively):
```sh
docker exec -it product_service php bin/console messenger:consume order_events
```

## Notes about message handlers
- Incoming reserve requests should be handled by a message handler that:
  - checks product availability in the product DB,
  - decrements stock when available and publishes `ProductReservedEvent`,
  - or publishes `ProductOutOfStockEvent` if not enough stock.
