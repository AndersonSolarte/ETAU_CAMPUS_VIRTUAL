<?php
namespace local_tau_ai_provider;

defined('MOODLE_INTERNAL') || die();

class client {

    private string $base_url;
    private string $api_key;
    private int $timeout;

    public function __construct() {
        $this->base_url = rtrim(get_config('local_tau_ai_provider', 'api_url') ?: 'http://localhost:4000/api', '/');
        $this->api_key  = get_config('local_tau_ai_provider', 'api_key') ?: '';
        $this->timeout  = (int)(get_config('local_tau_ai_provider', 'timeout') ?: 60);
    }

    public function post(string $endpoint, array $data): array {
        return $this->request('POST', $endpoint, $data);
    }

    public function get(string $endpoint, array $params = []): array {
        $url = $this->base_url . '/' . ltrim($endpoint, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $this->request('GET', $url, []);
    }

    private function request(string $method, string $endpoint, array $data): array {
        $url = (filter_var($endpoint, FILTER_VALIDATE_URL))
            ? $endpoint
            : $this->base_url . '/' . ltrim($endpoint, '/');

        [$response, $http_code, $error] = $this->perform_request($method, $url, $data);

        if ($error && $this->should_retry_with_host_gateway($url)) {
            $fallbackurl = $this->replace_localhost_with_host_gateway($url);
            [$response, $http_code, $error] = $this->perform_request($method, $fallbackurl, $data);
        }

        if ($error) {
            throw new \moodle_exception('api_connection_error', 'local_tau_ai_provider', '', $error);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('api_invalid_response', 'local_tau_ai_provider', '', $response);
        }
        if ($http_code >= 400) {
            $msg = $decoded['message'] ?? $decoded['error'] ?? "HTTP $http_code";
            throw new \moodle_exception('api_error', 'local_tau_ai_provider', '', $msg);
        }

        return $decoded;
    }

    private function perform_request(string $method, string $url, array $data): array {
        $curl = curl_init();
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        if ($this->api_key) {
            $headers[] = 'X-Api-Key: ' . $this->api_key;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        return [$response, $http_code, $error];
    }

    private function should_retry_with_host_gateway(string $url): bool {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '';
        return in_array($host, ['localhost', '127.0.0.1'], true);
    }

    private function replace_localhost_with_host_gateway(string $url): string {
        return preg_replace('#//(localhost|127\.0\.0\.1)([:/])#', '//host.docker.internal$2', $url, 1) ?? $url;
    }

    public static function instance(): self {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}
