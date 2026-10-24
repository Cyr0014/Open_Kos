APP_NAME=OpenKOS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://example.com
TRUSTED_PROXIES=

# Recommended production settings behind a reverse proxy / load balancer.
# Use either a comma-separated list of trusted proxy IPs or '*' when you want
# the app to trust the full proxy chain in front of it.
# TRUSTED_PROXIES=127.0.0.1,10.0.0.0/8
# TRUSTED_PROXIES=*

# HTTP listener exposed by the OpenKOS web container.
# Keep 127.0.0.1 when using a reverse proxy on the same host.
OPENKOS_HTTP_BIND=127.0.0.1
OPENKOS_HTTP_PORT=8080

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=openkos
DB_USERNAME=sail
DB_PASSWORD=password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=
# SETTINGS_CACHE_STORE=
# SETTINGS_CACHE_TTL=3600

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=redis
# REDIS_SCHEME=tls
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

OPENKOS_MARKETPLACE_URL=https://marketplace.openkos.id
OPENKOS_MARKETPLACE_CONNECT_TIMEOUT=3
OPENKOS_MARKETPLACE_TIMEOUT=15
OPENKOS_MARKETPLACE_MAX_RESPONSE_BYTES=1048576
OPENKOS_MARKETPLACE_MAX_ARTIFACT_BYTES=67108864

# Optional, explicit load-test fixtures. These are never run by DatabaseSeeder.
LOAD_TEST_FIXTURES_ENABLED=false
LOAD_TEST_OWNER_EMAIL=
LOAD_TEST_OWNER_PASSWORD=
LOAD_TEST_ADMIN_EMAIL=
LOAD_TEST_ADMIN_PASSWORD=
LOAD_TEST_STAFF_EMAIL=
LOAD_TEST_STAFF_PASSWORD=
LOAD_TEST_TENANT_EMAIL=
LOAD_TEST_TENANT_PASSWORD=
LOAD_TEST_DATASET_ENABLED=false

<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:payment-webhook', 'webhook.secure'])->group(function () {
    Route::post('webhooks/payment/{gateway}', PaymentWebhookController::class)
        ->where('gateway', '.+')
        ->name('webhooks.payment');
});
