# News Aggregator Backend

## Requirements

Either
- PHP >= 8.3

or

- docker engine
- docker compose

## Installation

### Copy the `.env` file

```bash
cp .env.example .env
```

## With Docker

### Set API keys for news data sources on `.env` file

```bash
NEWSAPI_KEY=<newsapi-key>
GUARDIAN_KEY=<theguardian-key>
NYT_KEY=<newyorktimes-skey>
```

### Database settings

Since the test database is being run via docker, use the following configuration on `.env` file:

```
DB_HOST=na-mysql
DB_PORT=3306
DB_DATABASE=<database>
DB_USERNAME=<username>
DB_PASSWORD=<password>
```

### Composer install

```bash
make install
```

### Composer update

```bash
make update
```

### Start containers in detached mode

```bash
make upd
```

### Stop and remove the containers

```bash
make down
```

### Stop and remove the containers along with MySQL volumes

```bash
make clear
```

### Apply database migrations and seed

```bash
make migrate
```

### Apply fresh database migrations and seed

```bash
make migrate-fresh
```

## Usage
The API is available at http://localhost:8080/api/v1. Set custom `API_PUBLIC_PORT` on `.env.example` if needed. 

OpenAPI documentation is available at http://localhost:8080/docs/api.

Open API document in JSON format describing full API is available at http://localhost:8080/docs/api.json and also at [api.json](api.json). It can be imported to Postman as collection.

phpMyAdmin can be accessed at http://localhost:8889. Set custom `PMA_PUBLIC_PORT` on `.env.example` if needed.

## Troubleshooting

### Clear all performance caches set by Laravel framework

```bash
make cc
```

### Open bash shell inside api container

```bash
make bash
```

### See status of docker containers

```bash
make ps
```

### Fix code style

```bash
make pint
```

## TODO

- **Automated Testing:** Implement integration tests for API endpoints.
- **CI/CD Pipeline:** Set up a GitHub Actions workflow for test suite and code quality.
