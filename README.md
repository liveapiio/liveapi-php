# liveapi-php

PHP client for the [LiveAPI](https://liveapi.io) reference-data API.

The token spends calls on your account. Keep it in an environment variable on your server. Do not put it in a browser page or a mobile app.

```php
$client = new LiveApi\Client(getenv('LIVEAPI_TOKEN'));
$phone = $client->validatePhone('0555 111 22 33', 'TR');
```

```
composer config repositories.liveapi vcs https://github.com/liveapiio/liveapi-php
composer require liveapiio/liveapi-php:dev-main
```

Docs: https://liveapi.io/docs

IP lookups include `attribution` and `attribution_url`. Keep those fields when you store or show a DB-IP result. A validation result with `valid` false is still HTTP 200 and uses one call. Only a non-2xx response is free.
