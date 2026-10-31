<?php

declare(strict_types=1);

require __DIR__ . "/../src/Types.php";

$rates = LiveApi\Rates::from([
    "source" => "tcmb",
    "base" => "TRY",
    "date" => "2025-12-31",
    "fetched_at" => "2025-12-31T15:00:00Z",
    "rates" => [[
        "code" => "USD",
        "name" => "US Dollar",
        "unit" => 1,
        "source_unit" => 1,
        "forex_buying" => 1.1,
        "forex_selling" => null,
        "banknote_buying" => null,
        "banknote_selling" => null,
    ]],
]);

if ($rates->tcmb[0]->code !== "USD" || $rates->reference !== []) {
    fwrite(STDERR, "rates decode failed\n");
    exit(1);
}

$phone = LiveApi\PhoneValidation::from([
    "valid" => true,
    "possible" => true,
    "reason" => null,
    "e164" => "+905551112233",
    "national" => null,
    "international" => null,
    "country" => "TR",
    "calling_code" => "90",
    "type" => "MOBILE",
]);

if ($phone->e164 !== "+905551112233") {
    fwrite(STDERR, "phone decode failed\n");
    exit(1);
}

echo "ok\n";
