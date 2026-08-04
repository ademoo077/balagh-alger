<?php
return [
    'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
    'port' => (int)(getenv('REDIS_PORT') ?: 6379),
    'password' => getenv('REDIS_PASSWORD') ?: '',
    'database' => (int)(getenv('REDIS_DB') ?: 0),
    'queue_db' => (int)(getenv('REDIS_QUEUE_DB') ?: 1),
    'prefix' => 'balagh:',
];
