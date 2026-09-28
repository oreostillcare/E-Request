<?php

/**
 * Minimal server-side Supabase REST client used by the legacy PHP UI.
 *
 * The secret key is read from the environment and is never sent to the browser.
 */
function load_project_environment(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($envFile)) {
        return;
    }

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

final class SupabaseClient
{
    private string $restUrl;
    private string $apiKey;

    public function __construct()
    {
        load_project_environment();

        $url = rtrim((string) getenv('SUPABASE_URL'), '/');
        $key = (string) getenv('SUPABASE_SECRET_KEY');
        if ($url === '' || $key === '') {
            throw new RuntimeException('Supabase server environment variables are not configured.');
        }

        $this->restUrl = $url . '/rest/v1';
        $this->apiKey = $key;
    }

    public function select(string $table, array $filters = [], array $options = []): array
    {
        $query = ['select' => $options['select'] ?? '*'];
        foreach ($filters as $column => $expression) {
            $query[$column] = $expression;
        }
        foreach (['order', 'limit', 'offset'] as $option) {
            if (array_key_exists($option, $options)) {
                $query[$option] = (string) $options[$option];
            }
        }

        return $this->request('GET', $table, $query)['data'];
    }

    public function insert(string $table, array $values): array
    {
        return $this->request('POST', $table, [], $values, 'return=representation')['data'];
    }

    public function upsert(string $table, array $values, string $conflictColumn): array
    {
        return $this->request(
            'POST',
            $table,
            ['on_conflict' => $conflictColumn],
            $values,
            'resolution=merge-duplicates,return=representation'
        )['data'];
    }

    public function update(string $table, array $values, array $filters): bool
    {
        $result = $this->request('PATCH', $table, $filters, $values, 'return=minimal');
        return $result['status'] >= 200 && $result['status'] < 300;
    }

    public function delete(string $table, array $filters): bool
    {
        $result = $this->request('DELETE', $table, $filters, null, 'return=minimal');
        return $result['status'] >= 200 && $result['status'] < 300;
    }

    private function request(
        string $method,
        string $table,
        array $query = [],
        ?array $body = null,
        ?string $prefer = null
    ): array {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $table)) {
            throw new InvalidArgumentException('Invalid Supabase table name.');
        }

        $url = $this->restUrl . '/' . $table;
        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'apikey: ' . $this->apiKey,
            'User-Agent: E-Request-Server/1.0',
        ];

        // Legacy service-role keys are JWTs. New sb_secret keys use only apikey.
        if (str_starts_with($this->apiKey, 'eyJ')) {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }
        if ($prefer !== null) {
            $headers[] = 'Prefer: ' . $prefer;
        }

        $http = [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'timeout' => 20,
        ];
        if ($body !== null) {
            $encoded = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($encoded === false) {
                throw new RuntimeException('Unable to encode a Supabase request.');
            }
            $http['content'] = $encoded;
        }

        $response = @file_get_contents($url, false, stream_context_create(['http' => $http]));
        if (function_exists('http_get_last_response_headers')) {
            $responseHeaders = http_get_last_response_headers() ?: [];
        } else {
            // Compatibility fallback for PHP versions before 8.4.
            $responseHeaders = $http_response_header ?? [];
        }

        $status = 0;
        foreach ($responseHeaders as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
                $status = (int) $matches[1];
            }
        }

        if ($response === false && $status === 0) {
            throw new RuntimeException('Unable to reach Supabase.');
        }

        $decoded = $response !== '' && $response !== false ? json_decode($response, true) : [];
        if ($status < 200 || $status >= 300) {
            $message = is_array($decoded) && isset($decoded['message'])
                ? (string) $decoded['message']
                : 'Unexpected Supabase response.';
            throw new RuntimeException('Supabase request failed: ' . $message);
        }

        return [
            'status' => $status,
            'data' => is_array($decoded) ? $decoded : [],
            'headers' => $responseHeaders,
        ];
    }
}

