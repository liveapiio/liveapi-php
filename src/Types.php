<?php

declare(strict_types=1);

namespace LiveApi;

final class Value
{
    /** @param array<string, mixed> $row */
    public static function str(array $row, string $key): ?string
    {
        return isset($row[$key]) && $row[$key] !== null ? (string) $row[$key] : null;
    }

    /** @param array<string, mixed> $row */
    public static function float(array $row, string $key): ?float
    {
        return isset($row[$key]) && $row[$key] !== null ? (float) $row[$key] : null;
    }

    /** @param array<string, mixed> $row */
    public static function bool(array $row, string $key): bool
    {
        return (bool) ($row[$key] ?? false);
    }

    /** @param array<string, mixed> $row */
    public static function nullableBool(array $row, string $key): ?bool
    {
        return isset($row[$key]) && $row[$key] !== null ? (bool) $row[$key] : null;
    }

    /** @param array<string, mixed> $row */
    public static function int(array $row, string $key): int
    {
        return (int) ($row[$key] ?? 0);
    }

    /** @param array<string, mixed> $row */
    public static function nullableInt(array $row, string $key): ?int
    {
        return isset($row[$key]) && $row[$key] !== null ? (int) $row[$key] : null;
    }
}

final class Currency
{
    public function __construct(public readonly string $code, public readonly string $name, public readonly ?string $symbol) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["code"] ?? ""), (string) ($row["name"] ?? ""), Value::str($row, "symbol"));
    }
}

final class Language
{
    public function __construct(public readonly string $code, public readonly string $name) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["code"] ?? ""), (string) ($row["name"] ?? ""));
    }
}

final class Country
{
    /** @param list<Currency> $currencies
     * @param list<Language> $languages
     * @param array<string, mixed>|null $geoshape
     * @param array<string, mixed>|null $geoshapeAttribution */
    public function __construct(
        public readonly string $code,
        public readonly string $iso3,
        public readonly ?string $numeric,
        public readonly string $name,
        public readonly string $officialName,
        public readonly ?string $nameTr,
        public readonly ?string $capital,
        public readonly ?string $region,
        public readonly ?string $subregion,
        public readonly ?string $tld,
        public readonly ?string $flag,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?float $areaKm2,
        public readonly bool $independent,
        public readonly array $currencies,
        public readonly array $languages,
        public readonly ?string $callingCode,
        public readonly bool $inEu,
        public readonly ?array $geoshape = null,
        public readonly ?array $geoshapeAttribution = null,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $currencies = [];
        foreach (is_array($row["currencies"] ?? null) ? $row["currencies"] : [] as $item) {
            if (is_array($item)) {
                $currencies[] = Currency::from($item);
            }
        }
        $languages = [];
        foreach (is_array($row["languages"] ?? null) ? $row["languages"] : [] as $item) {
            if (is_array($item)) {
                $languages[] = Language::from($item);
            }
        }
        return new self(
            (string) ($row["code"] ?? ""),
            (string) ($row["iso3"] ?? ""),
            Value::str($row, "numeric"),
            (string) ($row["name"] ?? ""),
            (string) ($row["official_name"] ?? ""),
            Value::str($row, "name_tr"),
            Value::str($row, "capital"),
            Value::str($row, "region"),
            Value::str($row, "subregion"),
            Value::str($row, "tld"),
            Value::str($row, "flag"),
            Value::float($row, "latitude"),
            Value::float($row, "longitude"),
            Value::float($row, "area_km2"),
            Value::bool($row, "independent"),
            $currencies,
            $languages,
            Value::str($row, "calling_code"),
            Value::bool($row, "in_eu"),
            is_array($row["geoshape"] ?? null) ? $row["geoshape"] : null,
            is_array($row["geoshape_attribution"] ?? null) ? $row["geoshape_attribution"] : null,
        );
    }
}

final class Division
{
    /** @param list<Division> $children */
    public function __construct(
        public readonly string $id,
        public readonly string $country,
        public readonly string $code,
        public readonly string $name,
        public readonly int $level,
        public readonly ?string $parentId,
        public readonly array $children,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $children = [];
        foreach (is_array($row["children"] ?? null) ? $row["children"] : [] as $item) {
            if (is_array($item)) {
                $children[] = self::from($item);
            }
        }
        return new self(
            (string) ($row["id"] ?? ""),
            (string) ($row["country"] ?? ""),
            (string) ($row["code"] ?? ""),
            (string) ($row["name"] ?? ""),
            Value::int($row, "level"),
            Value::str($row, "parent_id"),
            $children,
        );
    }
}

final class IpLocation
{
    /** @param list<string> $languageCodes */
    public function __construct(
        public readonly ?string $ip,
        public readonly ?string $version,
        public readonly ?string $city,
        public readonly ?string $region,
        public readonly ?string $regionCode,
        public readonly ?string $countryCode,
        public readonly ?string $countryCodeIso3,
        public readonly ?string $countryName,
        public readonly ?string $countryNameTr,
        public readonly ?string $countryCapital,
        public readonly ?string $countryTld,
        public readonly ?string $countryFlag,
        public readonly ?string $continentCode,
        public readonly ?bool $inEu,
        public readonly ?string $postal,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $timezone,
        public readonly ?string $utcOffset,
        public readonly ?string $countryCallingCode,
        public readonly ?string $currency,
        public readonly ?string $currencyName,
        public readonly ?string $languages,
        public readonly array $languageCodes,
        public readonly ?float $countryArea,
        public readonly ?string $asn,
        public readonly ?string $org,
        public readonly ?string $colo,
        public readonly ?string $metroCode,
        public readonly ?string $browserLanguage,
        public readonly ?string $source,
        public readonly ?string $attribution,
        public readonly ?string $attributionUrl,
        public readonly ?bool $isTor = null,
        public readonly ?bool $isHosting = null,
        public readonly ?string $hostingProvider = null,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $codes = [];
        foreach (is_array($row["language_codes"] ?? null) ? $row["language_codes"] : [] as $code) {
            $codes[] = (string) $code;
        }
        return new self(
            Value::str($row, "ip"),
            Value::str($row, "version"),
            Value::str($row, "city"),
            Value::str($row, "region"),
            Value::str($row, "region_code"),
            Value::str($row, "country_code"),
            Value::str($row, "country_code_iso3"),
            Value::str($row, "country_name"),
            Value::str($row, "country_name_tr"),
            Value::str($row, "country_capital"),
            Value::str($row, "country_tld"),
            Value::str($row, "country_flag"),
            Value::str($row, "continent_code"),
            Value::nullableBool($row, "in_eu"),
            Value::str($row, "postal"),
            Value::float($row, "latitude"),
            Value::float($row, "longitude"),
            Value::str($row, "timezone"),
            Value::str($row, "utc_offset"),
            Value::str($row, "country_calling_code"),
            Value::str($row, "currency"),
            Value::str($row, "currency_name"),
            Value::str($row, "languages"),
            $codes,
            Value::float($row, "country_area"),
            Value::str($row, "asn"),
            Value::str($row, "org"),
            Value::str($row, "colo"),
            Value::str($row, "metro_code"),
            Value::str($row, "browser_language"),
            Value::str($row, "source"),
            Value::str($row, "attribution"),
            Value::str($row, "attribution_url"),
            Value::nullableBool($row, "is_tor"),
            Value::nullableBool($row, "is_hosting"),
            Value::str($row, "hosting_provider"),
        );
    }
}

final class CallingCode
{
    public function __construct(public readonly string $country, public readonly string $name, public readonly string $callingCode) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["country"] ?? ""), (string) ($row["name"] ?? ""), (string) ($row["calling_code"] ?? ""));
    }
}

final class PhoneValidation
{
    public function __construct(
        public readonly bool $valid,
        public readonly bool $possible,
        public readonly ?string $reason,
        public readonly ?string $e164,
        public readonly ?string $national,
        public readonly ?string $international,
        public readonly ?string $country,
        public readonly ?string $callingCode,
        public readonly ?string $type,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::bool($row, "valid"), Value::bool($row, "possible"), Value::str($row, "reason"), Value::str($row, "e164"), Value::str($row, "national"), Value::str($row, "international"), Value::str($row, "country"), Value::str($row, "calling_code"), Value::str($row, "type"));
    }
}

final class PlateFormat
{
    public function __construct(public readonly string $country, public readonly string $description, public readonly string $example) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["country"] ?? ""), (string) ($row["description"] ?? ""), (string) ($row["example"] ?? ""));
    }
}

final class PlateValidation
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $reason,
        public readonly ?string $normalized,
        public readonly string $country,
        public readonly ?string $provinceCode,
        public readonly ?string $provinceName,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::bool($row, "valid"), Value::str($row, "reason"), Value::str($row, "normalized"), (string) ($row["country"] ?? ""), Value::str($row, "province_code"), Value::str($row, "province_name"));
    }
}

final class ReferenceRate
{
    public function __construct(public readonly string $code, public readonly float $rate) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["code"] ?? ""), (float) ($row["rate"] ?? 0));
    }
}

final class TcmbRate
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly int $unit,
        public readonly float $sourceUnit,
        public readonly ?float $forexBuying,
        public readonly ?float $forexSelling,
        public readonly ?float $banknoteBuying,
        public readonly ?float $banknoteSelling,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["code"] ?? ""), (string) ($row["name"] ?? ""), Value::int($row, "unit"), (float) ($row["source_unit"] ?? 0), Value::float($row, "forex_buying"), Value::float($row, "forex_selling"), Value::float($row, "banknote_buying"), Value::float($row, "banknote_selling"));
    }
}

final class Rates
{
    /** @param list<ReferenceRate> $reference
     * @param list<TcmbRate> $tcmb */
    public function __construct(
        public readonly string $source,
        public readonly string $base,
        public readonly ?string $date,
        public readonly string $fetchedAt,
        public readonly array $reference,
        public readonly array $tcmb,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $reference = [];
        $tcmb = [];
        foreach (is_array($row["rates"] ?? null) ? $row["rates"] : [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (($row["source"] ?? "") === "tcmb") {
                $tcmb[] = TcmbRate::from($item);
            } else {
                $reference[] = ReferenceRate::from($item);
            }
        }
        return new self((string) ($row["source"] ?? ""), (string) ($row["base"] ?? ""), Value::str($row, "date"), (string) ($row["fetched_at"] ?? ""), $reference, $tcmb);
    }
}

final class Conversion
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly float $amount,
        public readonly float $rate,
        public readonly float $result,
        public readonly string $source,
        public readonly string $base,
        public readonly ?string $date,
        public readonly ?string $tcmbRate,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["from"] ?? ""), (string) ($row["to"] ?? ""), (float) ($row["amount"] ?? 0), (float) ($row["rate"] ?? 0), (float) ($row["result"] ?? 0), (string) ($row["source"] ?? ""), (string) ($row["base"] ?? ""), Value::str($row, "date"), Value::str($row, "tcmb_rate"));
    }
}

final class Place
{
    public function __construct(public readonly float $latitude, public readonly float $longitude, public readonly ?string $country, public readonly ?string $label) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((float) ($row["latitude"] ?? 0), (float) ($row["longitude"] ?? 0), Value::str($row, "country"), Value::str($row, "label"));
    }
}

final class Distance
{
    public function __construct(public readonly float $kilometers, public readonly Place $from, public readonly Place $to) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $from = is_array($row["from"] ?? null) ? $row["from"] : [];
        $to = is_array($row["to"] ?? null) ? $row["to"] : [];
        return new self((float) ($row["kilometers"] ?? 0), Place::from($from), Place::from($to));
    }
}

final class IbanValidation
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $reason,
        public readonly ?string $iban,
        public readonly ?string $formatted,
        public readonly ?string $country,
        public readonly ?string $checkDigits,
        public readonly ?string $bban,
        public readonly ?string $bankCode,
        public readonly ?string $bankName,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::bool($row, "valid"), Value::str($row, "reason"), Value::str($row, "iban"), Value::str($row, "formatted"), Value::str($row, "country"), Value::str($row, "check_digits"), Value::str($row, "bban"), Value::str($row, "bank_code"), Value::str($row, "bank_name"));
    }
}

final class TrNumberValidation
{
    public function __construct(public readonly bool $valid, public readonly ?string $reason, public readonly string $type, public readonly bool $registryChecked) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::bool($row, "valid"), Value::str($row, "reason"), (string) ($row["type"] ?? ""), Value::bool($row, "registry_checked"));
    }
}

final class Holiday
{
    public function __construct(public readonly string $date, public readonly string $name, public readonly string $localName, public readonly bool $halfDay, public readonly bool $nationwide) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["date"] ?? ""), (string) ($row["name"] ?? ""), (string) ($row["local_name"] ?? ""), Value::bool($row, "half_day"), Value::bool($row, "nationwide"));
    }
}

final class HolidayList
{
    /** @param list<Holiday> $holidays */
    public function __construct(public readonly string $country, public readonly int $year, public readonly string $source, public readonly array $holidays) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $holidays = [];
        foreach (is_array($row["holidays"] ?? null) ? $row["holidays"] : [] as $item) {
            if (is_array($item)) {
                $holidays[] = Holiday::from($item);
            }
        }
        return new self((string) ($row["country"] ?? ""), Value::int($row, "year"), (string) ($row["source"] ?? ""), $holidays);
    }
}

final class BusinessDays
{
    /** @param list<int> $weekend
     * @param list<string> $holidaysSkipped */
    public function __construct(
        public readonly string $country,
        public readonly string $start,
        public readonly ?int $days,
        public readonly string $end,
        public readonly int $businessDays,
        public readonly array $weekend,
        public readonly array $holidaysSkipped,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $weekend = [];
        foreach (is_array($row["weekend"] ?? null) ? $row["weekend"] : [] as $day) {
            $weekend[] = (int) $day;
        }
        $skipped = [];
        foreach (is_array($row["holidays_skipped"] ?? null) ? $row["holidays_skipped"] : [] as $day) {
            $skipped[] = (string) $day;
        }
        return new self((string) ($row["country"] ?? ""), (string) ($row["start"] ?? ""), Value::nullableInt($row, "days"), (string) ($row["end"] ?? ""), Value::int($row, "business_days"), $weekend, $skipped);
    }
}

final class Timezone
{
    /** @param list<string> $timezones */
    public function __construct(
        public readonly string $timezone,
        public readonly ?string $utcOffset,
        public readonly ?string $abbreviation,
        public readonly bool $isDst,
        public readonly string $localTime,
        public readonly ?string $countryCode,
        public readonly array $timezones,
        public readonly string $source,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $zones = [];
        foreach (is_array($row["timezones"] ?? null) ? $row["timezones"] : [] as $zone) {
            $zones[] = (string) $zone;
        }
        return new self((string) ($row["timezone"] ?? ""), Value::str($row, "utc_offset"), Value::str($row, "abbreviation"), Value::bool($row, "is_dst"), (string) ($row["local_time"] ?? ""), Value::str($row, "country_code"), $zones, (string) ($row["source"] ?? ""));
    }
}

final class MxRecord
{
    public function __construct(public readonly int $priority, public readonly string $exchange) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::int($row, "priority"), (string) ($row["exchange"] ?? ""));
    }
}

final class EmailCheck
{
    /** @param list<MxRecord> $mxRecords */
    public function __construct(
        public readonly string $email,
        public readonly bool $valid,
        public readonly ?string $reason,
        public readonly ?string $normalized,
        public readonly ?string $localPart,
        public readonly ?string $domain,
        public readonly bool $syntaxValid,
        public readonly bool $dnsChecked,
        public readonly ?bool $domainExists,
        public readonly ?bool $acceptsMail,
        public readonly array $mxRecords,
        public readonly bool $disposable,
        public readonly bool $role,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $records = [];
        foreach (is_array($row["mx_records"] ?? null) ? $row["mx_records"] : [] as $item) {
            if (is_array($item)) {
                $records[] = MxRecord::from($item);
            }
        }
        return new self((string) ($row["email"] ?? ""), Value::bool($row, "valid"), Value::str($row, "reason"), Value::str($row, "normalized"), Value::str($row, "local_part"), Value::str($row, "domain"), Value::bool($row, "syntax_valid"), Value::bool($row, "dns_checked"), Value::nullableBool($row, "domain_exists"), Value::nullableBool($row, "accepts_mail"), $records, Value::bool($row, "disposable"), Value::bool($row, "role"));
    }
}

final class VatCheck
{
    public function __construct(
        public readonly string $countryCode,
        public readonly string $vatNumber,
        public readonly bool $valid,
        public readonly ?string $name,
        public readonly ?string $address,
        public readonly string $requestDate,
        public readonly string $source,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["country_code"] ?? ""), (string) ($row["vat_number"] ?? ""), Value::bool($row, "valid"), Value::str($row, "name"), Value::str($row, "address"), (string) ($row["request_date"] ?? ""), (string) ($row["source"] ?? ""));
    }
}

final class IdValidator
{
    public function __construct(
        public readonly string $country,
        public readonly string $type,
        public readonly string $name,
        public readonly string $holder,
        public readonly string $description,
        public readonly string $example,
        public readonly string $path,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["country"] ?? ""), (string) ($row["type"] ?? ""), (string) ($row["name"] ?? ""), (string) ($row["holder"] ?? ""), (string) ($row["description"] ?? ""), (string) ($row["example"] ?? ""), (string) ($row["path"] ?? ""));
    }
}

final class IdValidation
{
    /** @param array<string, mixed>|null $details */
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $normalized,
        public readonly string $country,
        public readonly string $type,
        public readonly ?string $reason,
        public readonly ?array $details,
        public readonly bool $registryChecked,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self(Value::bool($row, "valid"), Value::str($row, "normalized"), (string) ($row["country"] ?? ""), (string) ($row["type"] ?? ""), Value::str($row, "reason"), is_array($row["details"] ?? null) ? $row["details"] : null, Value::bool($row, "registry_checked"));
    }
}

final class PostalCodeCountry
{
    public function __construct(
        public readonly string $country,
        public readonly int $count,
        public readonly bool $partial,
        public readonly string $updated,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["country"] ?? ""), Value::int($row, "count"), Value::bool($row, "partial"), (string) ($row["updated"] ?? ""));
    }
}

final class PostalPlace
{
    public function __construct(
        public readonly string $postalCode,
        public readonly string $place,
        public readonly ?string $admin1,
        public readonly ?string $admin1Code,
        public readonly ?string $admin2,
        public readonly ?string $admin2Code,
        public readonly ?string $admin3,
        public readonly ?string $admin3Code,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?int $accuracy,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["postal_code"] ?? ""), (string) ($row["place"] ?? ""), Value::str($row, "admin1"), Value::str($row, "admin1_code"), Value::str($row, "admin2"), Value::str($row, "admin2_code"), Value::str($row, "admin3"), Value::str($row, "admin3_code"), Value::float($row, "latitude"), Value::float($row, "longitude"), Value::nullableInt($row, "accuracy"));
    }
}

final class PostalCodeLookup
{
    /**
     * @param list<PostalPlace> $places
     * @param array<string, string> $attribution LiveAPI in-house postal dataset.
     */
    public function __construct(
        public readonly string $country,
        public readonly string $query,
        public readonly string $postalCode,
        public readonly string $match,
        public readonly bool $partialData,
        public readonly ?string $coverageNote,
        public readonly array $places,
        public readonly string $updated,
        public readonly array $attribution,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $places = [];
        foreach (is_array($row["places"] ?? null) ? $row["places"] : [] as $item) {
            if (is_array($item)) {
                $places[] = PostalPlace::from($item);
            }
        }
        return new self((string) ($row["country"] ?? ""), (string) ($row["query"] ?? ""), (string) ($row["postal_code"] ?? ""), (string) ($row["match"] ?? ""), Value::bool($row, "partial_data"), Value::str($row, "coverage_note"), $places, (string) ($row["updated"] ?? ""), is_array($row["attribution"] ?? null) ? $row["attribution"] : []);
    }
}

final class ReverseGeocode
{
    /**
     * @param array{code: ?string, name: string, type: ?string}|null $admin1
     * @param array{id: ?string, name: string, type: ?string}|null $admin2
     */
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $countryCode,
        public readonly ?string $countryName,
        public readonly ?array $admin1,
        public readonly ?array $admin2,
        public readonly string $precision,
        public readonly string $attribution,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((float) ($row["latitude"] ?? 0), (float) ($row["longitude"] ?? 0), (string) ($row["country_code"] ?? ""), Value::str($row, "country_name"), is_array($row["admin1"] ?? null) ? $row["admin1"] : null, is_array($row["admin2"] ?? null) ? $row["admin2"] : null, (string) ($row["precision"] ?? ""), (string) ($row["attribution"] ?? ""));
    }
}

final class UserAgent
{
    /**
     * @param array{name: ?string, version: ?string, major: ?string} $browser
     * @param array{name: ?string, version: ?string} $engine
     * @param array{name: ?string, version: ?string} $os
     * @param array{type: string, vendor: ?string, model: ?string} $device
     * @param array{architecture: ?string} $cpu
     */
    public function __construct(
        public readonly string $ua,
        public readonly array $browser,
        public readonly array $engine,
        public readonly array $os,
        public readonly array $device,
        public readonly array $cpu,
        public readonly bool $isBot,
        public readonly ?string $botMatch,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        $part = static fn (string $key): array => is_array($row[$key] ?? null) ? $row[$key] : [];
        return new self((string) ($row["ua"] ?? ""), $part("browser"), $part("engine"), $part("os"), $part("device"), $part("cpu"), Value::bool($row, "is_bot"), Value::str($row, "bot_match"));
    }
}

final class BarcodeValidation
{
    public function __construct(
        public readonly string $input,
        public readonly string $normalized,
        public readonly bool $valid,
        public readonly ?string $type,
        public readonly ?string $reason,
        public readonly ?string $checkDigit,
        public readonly ?string $expectedCheckDigit,
        public readonly ?string $gtin14,
        public readonly ?string $isbn10,
        public readonly ?string $isbn13,
    ) {}

    /** @param array<string, mixed> $row */
    public static function from(array $row): self
    {
        return new self((string) ($row["input"] ?? ""), (string) ($row["normalized"] ?? ""), Value::bool($row, "valid"), Value::str($row, "type"), Value::str($row, "reason"), Value::str($row, "check_digit"), Value::str($row, "expected_check_digit"), Value::str($row, "gtin14"), Value::str($row, "isbn10"), Value::str($row, "isbn13"));
    }
}
