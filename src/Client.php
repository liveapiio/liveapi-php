<?php

declare(strict_types=1);

namespace LiveApi;

final class Client
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl = "https://liveapi.io",
        private readonly float $timeout = 30,
    ) {
    }

    public function callerIp(): IpLocation
    {
        return IpLocation::from($this->json("GET", "/v1/ip"));
    }

    public function lookupIp(string $ip): IpLocation
    {
        return IpLocation::from($this->json("GET", "/v1/ip/" . rawurlencode($ip)));
    }

    public function callerField(string $field): string
    {
        return $this->text("/v1/ip/" . rawurlencode($field));
    }

    public function lookupField(string $ip, string $field): string
    {
        return $this->text("/v1/ip/" . rawurlencode($ip) . "/" . rawurlencode($field));
    }

    /** @return list<Country> */
    public function countries(?string $query = null): array
    {
        return array_map(Country::from(...), $this->json("GET", "/v1/countries", query: ["q" => $query]));
    }

    public function country(string $code): Country
    {
        return Country::from($this->json("GET", "/v1/countries/" . rawurlencode($code)));
    }

    /** @return list<Division> */
    public function divisions(string $country): array
    {
        return array_map(Division::from(...), $this->json("GET", "/v1/countries/" . rawurlencode($country) . "/divisions"));
    }

    public function divisionByCode(string $country, string $code): Division
    {
        return Division::from($this->json("GET", "/v1/countries/" . rawurlencode($country) . "/divisions/" . rawurlencode($code)));
    }

    /** @return list<Division> */
    public function divisionChildrenByCode(string $country, string $code): array
    {
        $path = "/v1/countries/" . rawurlencode($country) . "/divisions/" . rawurlencode($code) . "/children";
        return array_map(Division::from(...), $this->json("GET", $path));
    }

    public function division(string $id): Division
    {
        return Division::from($this->json("GET", "/v1/divisions/" . rawurlencode($id)));
    }

    /** @return list<Division> */
    public function divisionChildren(string $id): array
    {
        return array_map(Division::from(...), $this->json("GET", "/v1/divisions/" . rawurlencode($id) . "/children"));
    }

    /** @return list<Currency> */
    public function currencies(): array
    {
        return array_map(Currency::from(...), $this->json("GET", "/v1/currencies"));
    }

    /** @return list<CallingCode> */
    public function callingCodes(?string $country = null): array
    {
        return array_map(CallingCode::from(...), $this->json("GET", "/v1/phones/codes", query: ["country" => $country]));
    }

    public function validatePhone(string $number, ?string $country = null): PhoneValidation
    {
        $body = ["number" => $number];
        if ($country !== null && $country !== "") {
            $body["country"] = $country;
        }
        return PhoneValidation::from($this->json("POST", "/v1/phones/validate", $body));
    }

    /** @return list<PlateFormat> */
    public function plateFormats(): array
    {
        return array_map(PlateFormat::from(...), $this->json("GET", "/v1/plates/formats"));
    }

    public function validatePlate(string $plate, string $country): PlateValidation
    {
        return PlateValidation::from($this->json("POST", "/v1/plates/validate", ["plate" => $plate, "country" => $country]));
    }

    public function rates(?string $source = null, ?string $date = null): Rates
    {
        return Rates::from($this->json("GET", "/v1/rates", query: ["source" => $source, "date" => $date]));
    }

    public function convert(string $from, string $to, ?float $amount = null, ?string $source = null, ?string $date = null, ?string $tcmbRate = null): Conversion
    {
        return Conversion::from($this->json("GET", "/v1/rates/convert", query: [
            "from" => $from,
            "to" => $to,
            "amount" => $amount === null ? null : (string) $amount,
            "source" => $source,
            "date" => $date,
            "tcmb_rate" => $tcmbRate,
        ]));
    }

    /** @param array{latitude?: float, longitude?: float, country?: string} $from
     * @param array{latitude?: float, longitude?: float, country?: string} $to */
    public function distance(array $from, array $to): Distance
    {
        return Distance::from($this->json("POST", "/v1/geo/distance", ["from" => $from, "to" => $to]));
    }

    public function validateIban(string $iban): IbanValidation
    {
        return IbanValidation::from($this->json("POST", "/v1/iban/validate", ["iban" => $iban]));
    }

    public function validateIdentity(string $number): TrNumberValidation
    {
        return TrNumberValidation::from($this->json("POST", "/v1/tr/identity/validate", ["number" => $number]));
    }

    public function validateTaxNumber(string $number): TrNumberValidation
    {
        return TrNumberValidation::from($this->json("POST", "/v1/tr/tax-number/validate", ["number" => $number]));
    }

    public function holidays(string $country, ?int $year = null): HolidayList
    {
        return HolidayList::from($this->json("GET", "/v1/holidays", query: [
            "country" => $country,
            "year" => $year === null ? null : (string) $year,
        ]));
    }

    /** @param array{country: string, start: string, days?: int, end?: string, weekend?: list<int>} $request */
    public function businessDays(array $request): BusinessDays
    {
        return BusinessDays::from($this->json("POST", "/v1/business-days", $request));
    }

    public function timezone(?float $latitude = null, ?float $longitude = null, ?string $country = null): Timezone
    {
        return Timezone::from($this->json("GET", "/v1/timezone", query: [
            "lat" => $latitude === null ? null : (string) $latitude,
            "lon" => $longitude === null ? null : (string) $longitude,
            "country" => $country,
        ]));
    }

    public function validateEmail(string $email, ?bool $dns = null): EmailCheck
    {
        $body = ["email" => $email];
        if ($dns !== null) {
            $body["dns"] = $dns;
        }
        return EmailCheck::from($this->json("POST", "/v1/email/validate", $body));
    }

    public function validateVat(string $vatNumber, ?string $country = null): VatCheck
    {
        $body = ["vat_number" => $vatNumber];
        if ($country !== null && $country !== "") {
            $body["country"] = $country;
        }
        return VatCheck::from($this->json("POST", "/v1/vat/validate", $body));
    }

    /** @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @return array<string, mixed>|list<mixed> */
    private function json(string $method, string $path, ?array $body = null, array $query = []): array
    {
        [$status, $raw] = $this->send($method, $path, $query, $body, "application/json");
        if ($status < 200 || $status >= 300) {
            throw $this->failure($status, $raw);
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new LiveApiException($status, "invalid_response", "LiveAPI returned a response that is not JSON.");
        }
        return $decoded;
    }

    /** @param array<string, scalar|null> $query */
    private function text(string $path): string
    {
        [$status, $raw] = $this->send("GET", $path, [], null, "text/plain");
        if ($status < 200 || $status >= 300) {
            throw $this->failure($status, $raw);
        }
        return $raw;
    }

    /** @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @return array{0: int, 1: string} */
    private function send(string $method, string $path, array $query, ?array $body, string $accept): array
    {
        if (trim($this->token) === "") {
            throw new LiveApiException(401, "unauthorized", "Bearer token is missing.");
        }
        $url = rtrim($this->baseUrl, "/") . $path;
        $params = [];
        foreach ($query as $key => $value) {
            if ($value !== null && $value !== "") {
                $params[$key] = $value;
            }
        }
        if ($params !== []) {
            $url .= "?" . http_build_query($params);
        }
        $header = "Authorization: Bearer {$this->token}\r\nAccept: {$accept}\r\nUser-Agent: liveapi-php/0.1.0\r\n";
        $content = "";
        if ($body !== null) {
            $content = json_encode($body, JSON_THROW_ON_ERROR);
            $header .= "Content-Type: application/json\r\n";
        }
        $context = stream_context_create([
            "http" => [
                "method" => $method,
                "header" => $header,
                "content" => $content,
                "timeout" => $this->timeout,
                "ignore_errors" => true,
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        $status = 0;
        foreach ($http_response_header ?? [] as $line) {
            if (preg_match("#^HTTP/\S+\s+(\d{3})#", $line, $match) === 1) {
                $status = (int) $match[1];
            }
        }
        if ($raw === false) {
            throw new LiveApiException($status, "transport", "The LiveAPI request failed.");
        }
        return [$status, $raw];
    }

    private function failure(int $status, string $raw): LiveApiException
    {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded["error"]["message"]) && is_string($decoded["error"]["message"])) {
            $code = isset($decoded["error"]["code"]) && is_string($decoded["error"]["code"]) ? $decoded["error"]["code"] : "";
            return new LiveApiException($status, $code, $decoded["error"]["message"]);
        }
        $message = trim($raw);
        return new LiveApiException($status, "", $message === "" ? "LiveAPI request failed." : $message);
    }
}
