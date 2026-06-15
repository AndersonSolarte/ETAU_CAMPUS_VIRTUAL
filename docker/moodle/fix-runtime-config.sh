#!/usr/bin/env bash
set -euo pipefail

CONFIG_FILE="/var/www/html/config.php"
MOODLE_URL="${MOODLE_URL:-http://localhost:8080}"

if [[ ! -f "${CONFIG_FILE}" ]]; then
  echo "config.php not found."
  exit 1
fi

php <<'PHP'
<?php
$file = '/var/www/html/config.php';
$url = getenv('MOODLE_URL') ?: 'http://localhost:8080';
$redishost = getenv('REDIS_HOST') ?: 'redis';
$redisport = (int)(getenv('REDIS_PORT') ?: 6379);
$redispassword = getenv('REDIS_PASSWORD') ?: '';
$sessionprefix = getenv('MOODLE_SESSION_PREFIX') ?: 'taucv_sess_';
$reverseproxyaddresses = getenv('MOODLE_REVERSEPROXY_ADDRESSES') ?: '172.16.0.0/12,127.0.0.1';
$config = file_get_contents($file);

$config = preg_replace(
    "/\\\$CFG->wwwroot\\s*=\\s*'[^']*';/",
    "\$CFG->wwwroot   = '{$url}';",
    $config
);

if (strpos($config, '$CFG->reverseproxy') === false) {
    $config = str_replace(
        "\$CFG->admin     = 'admin';",
        "\$CFG->admin     = 'admin';\n\n\$CFG->reverseproxy = true;\n\$CFG->reverseproxyaddresses = '{$reverseproxyaddresses}';\n\$CFG->sslproxy = false;\n\$CFG->localcachedir = '/var/www/moodledata/localcache';\n\$CFG->session_handler_class = '\\\\core\\\\session\\\\redis';\n\$CFG->session_redis_host = '{$redishost}';\n\$CFG->session_redis_port = {$redisport};\n\$CFG->session_redis_auth = '{$redispassword}';\n\$CFG->session_redis_database = 0;\n\$CFG->session_redis_prefix = '{$sessionprefix}';\n\$CFG->session_redis_acquire_lock_timeout = 120;\n\$CFG->session_redis_lock_expire = 7200;",
        $config
    );
} else {
    $config = preg_replace("/\\\$CFG->reverseproxy\\s*=\\s*[^;]*;/", "\$CFG->reverseproxy = true;", $config);
    if (preg_match("/\\\$CFG->reverseproxyaddresses\\s*=\\s*.*?;/", $config)) {
        $config = preg_replace("/\\\$CFG->reverseproxyaddresses\\s*=\\s*.*?;/", "\$CFG->reverseproxyaddresses = '{$reverseproxyaddresses}';", $config);
    } else {
        $config = preg_replace("/\\\$CFG->reverseproxy\\s*=\\s*[^;]*;/", "\$CFG->reverseproxy = true;\n\$CFG->reverseproxyaddresses = '{$reverseproxyaddresses}';", $config, 1);
    }
    $config = preg_replace("/\\\$CFG->sslproxy\\s*=\\s*[^;]*;/", "\$CFG->sslproxy = false;", $config);
}

file_put_contents($file, $config);
PHP

chmod 644 "${CONFIG_FILE}"
chown www-data:www-data "${CONFIG_FILE}" || true

echo "Moodle runtime config updated."
