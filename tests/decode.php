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

$barcode = LiveApi\BarcodeValidation::from(["input" => "4006381333931", "normalized" => "4006381333931", "valid" => true, "type" => "ean13", "reason" => null, "check_digit" => "1", "expected_check_digit" => "1", "gtin14" => "04006381333931", "isbn10" => null, "isbn13" => null]);
$id = LiveApi\IdValidation::from(["valid" => true, "normalized" => "12345678Z", "country" => "ES", "type" => "nif", "reason" => null, "registry_checked" => false]);
$postal = LiveApi\PostalCodeLookup::from(["country" => "DE", "query" => "10115", "postal_code" => "10115", "match" => "exact", "partial_data" => false, "coverage_note" => null, "places" => [["postal_code" => "10115", "place" => "Berlin", "latitude" => 52.5, "longitude" => 13.4, "accuracy" => 4]], "updated" => "2026-10-01", "attribution" => ["source" => "GeoNames", "url" => "https://www.geonames.org", "license" => "CC BY 4.0"]]);
$ua = LiveApi\UserAgent::from(["ua" => "x", "browser" => ["name" => "Chrome", "version" => "1", "major" => "1"], "engine" => [], "os" => [], "device" => ["type" => "desktop"], "cpu" => [], "is_bot" => false, "bot_match" => null]);
$ip = LiveApi\IpLocation::from(["language_codes" => [], "is_tor" => false, "is_hosting" => true, "hosting_provider" => "Hetzner"]);

if ($barcode->type !== "ean13" || !$id->valid || $postal->places[0]->place !== "Berlin" || $ua->browser["name"] !== "Chrome" || $ip->isHosting !== true || $ip->hostingProvider !== "Hetzner") {
    fwrite(STDERR, "new endpoint decode failed\n");
    exit(1);
}

echo "ok\n";
