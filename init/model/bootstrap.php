<?php

require_once __DIR__ . '/supabase_client.php';

final class SupabaseSessionHandler implements SessionHandlerInterface
{
    private SupabaseClient $db;
    private int $ttl;

    public function __construct(SupabaseClient $db, int $ttl = 7200)
    {
        $this->db = $db;
        $this->ttl = $ttl;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        try {
            $rows = $this->db->select(
                'app_sessions',
                ['session_id' => 'eq.' . $id, 'expires_at' => 'gt.' . gmdate('c')],
                ['select' => 'payload', 'limit' => 1]
            );
            return isset($rows[0]['payload']) ? (string) $rows[0]['payload'] : '';
        } catch (Throwable $error) {
            error_log($error->getMessage());
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        try {
            $this->db->upsert('app_sessions', [
                'session_id' => $id,
                'payload' => $data,
                'expires_at' => gmdate('c', time() + $this->ttl),
            ], 'session_id');
            return true;
        } catch (Throwable $error) {
            error_log($error->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        try {
            return $this->db->delete('app_sessions', ['session_id' => 'eq.' . $id]);
        } catch (Throwable $error) {
            error_log($error->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $this->db->delete('app_sessions', ['expires_at' => 'lt.' . gmdate('c')]);
            return 1;
        } catch (Throwable $error) {
            error_log($error->getMessage());
            return false;
        }
    }
}

function configure_app_session(): void
{
    static $configured = false;
    if ($configured || session_status() !== PHP_SESSION_NONE) {
        return;
    }
    $configured = true;

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_name('erequest_session');
    session_set_cookie_params([
        'lifetime' => 7200,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_set_save_handler(new SupabaseSessionHandler(new SupabaseClient()), true);
}

configure_app_session();

function require_authenticated_session(string $key): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $id = (int) ($_SESSION[$key] ?? 0);
    if ($id <= 0) {
        http_response_code(401);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Unauthorized');
    }

    return $id;
}

