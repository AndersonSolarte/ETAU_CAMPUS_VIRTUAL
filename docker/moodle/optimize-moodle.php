<?php
declare(strict_types=1);

try {
    define('CLI_SCRIPT', true);

    $configfile = '/var/www/html/config.php';
    $wwwroot = getenv('MOODLE_URL') ?: 'http://localhost:8080';
    $redishost = getenv('REDIS_HOST') ?: 'redis';
    $redisport = (int)(getenv('REDIS_PORT') ?: 6379);
    $redispassword = getenv('REDIS_PASSWORD') ?: '';
    $sessionprefix = getenv('MOODLE_SESSION_PREFIX') ?: 'taucv_sess_';
    $reverseproxyaddresses = getenv('MOODLE_REVERSEPROXY_ADDRESSES') ?: '172.16.0.0/12,127.0.0.1';

    $config = file_get_contents($configfile);
    $config = preg_replace("/\\\$CFG->wwwroot\\s*=\\s*'[^']*';/", "\$CFG->wwwroot   = '{$wwwroot}';", $config);

    $settings = [
        'reverseproxy' => "\$CFG->reverseproxy = true;",
        'reverseproxyaddresses' => "\$CFG->reverseproxyaddresses = '{$reverseproxyaddresses}';",
        'sslproxy' => "\$CFG->sslproxy = false;",
        'localcachedir' => "\$CFG->localcachedir = '/var/www/html/localcache';",
        'session_handler_class' => "\$CFG->session_handler_class = '\\core\\session\\redis';",
        'session_redis_host' => "\$CFG->session_redis_host = '{$redishost}';",
        'session_redis_port' => "\$CFG->session_redis_port = {$redisport};",
        'session_redis_auth' => "\$CFG->session_redis_auth = '{$redispassword}';",
        'session_redis_database' => "\$CFG->session_redis_database = 0;",
        'session_redis_prefix' => "\$CFG->session_redis_prefix = '{$sessionprefix}';",
        'session_redis_acquire_lock_timeout' => "\$CFG->session_redis_acquire_lock_timeout = 120;",
        'session_redis_lock_expire' => "\$CFG->session_redis_lock_expire = 7200;",
        'session_redis_lock_retry' => "\$CFG->session_redis_lock_retry = 100;",
        'session_redis_connection_timeout' => "\$CFG->session_redis_connection_timeout = 3;",
    ];

    $insertblock = [];
    foreach ($settings as $key => $setting) {
        $pattern = "/\\\$CFG->{$key}\\s*=\\s*.*?;/";
        if (preg_match($pattern, $config)) {
            $config = preg_replace($pattern, $setting, $config);
        } else {
            $insertblock[] = $setting;
        }
    }

    if ($insertblock) {
        $config = str_replace(
            "\$CFG->admin     = 'admin';",
            "\$CFG->admin     = 'admin';\n\n" . implode("\n", $insertblock),
            $config
        );
    }

    file_put_contents($configfile, $config);

    require($configfile);

    global $CFG;

    require_once($CFG->dirroot . '/cache/classes/config_writer.php');

    $storeconfig = [
        'server' => sprintf(
            '%s:%d',
            getenv('REDIS_HOST') ?: 'redis',
            (int)(getenv('REDIS_PORT') ?: 6379)
        ),
        'prefix' => getenv('MOODLE_CACHE_PREFIX') ?: 'taucv_cache_',
        'password' => getenv('REDIS_PASSWORD') ?: '',
        'serializer' => 1,
        'compressor' => 0,
        'connectiontimeout' => 2,
        'locktimeout' => 600,
        'lockwait' => 60,
    ];

    $storename = 'tau_redis_cache';
    $config = \core_cache\config_writer::instance();
    $stores = $config->get_all_stores();

    if (array_key_exists($storename, $stores)) {
        $config->edit_store_instance($storename, 'redis', $storeconfig);
    } else {
        $config->add_store_instance($storename, 'redis', $storeconfig);
    }

    $requeststores = [];
    foreach ($config->get_mode_mappings() as $mapping) {
        if ((int)$mapping['mode'] === \core_cache\store::MODE_REQUEST) {
            $requeststores[] = $mapping['store'];
        }
    }

    if (!$requeststores) {
        $requeststores = array_keys($config->get_stores(\core_cache\store::MODE_REQUEST));
    }

    $config->set_mode_mappings([
        \core_cache\store::MODE_APPLICATION => [$storename],
        \core_cache\store::MODE_SESSION => [$storename],
        \core_cache\store::MODE_REQUEST => $requeststores,
    ]);

    set_config('extramemorylimit', '512M');
    set_config('themedesignermode', '0');
    set_config('cachejs', '1');
    set_config('debug', '0');
    set_config('perfdebug', '0');
    set_config('cronclionly', '1');
    set_config('task_adhoc_concurrency_limit', '4', 'tool_task');

    fwrite(STDOUT, "Moodle performance settings applied.\n");
} catch (Throwable $e) {
    fwrite(STDERR, $e::class . ': ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
