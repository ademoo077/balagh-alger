<?php
namespace App\Helpers;

class Queue {
    private static ?\Redis $redis = null;
    private static array $config = [];

    public static function connection(): \Redis {
        if (self::$redis !== null) {
            return self::$redis;
        }

        self::$config = require APP_PATH . '/Config/redis.php';

        self::$redis = new \Redis();
        $host = self::$config['host'];
        $port = self::$config['port'];

        $connected = self::$redis->connect($host, $port, 2.0);
        if (!$connected) {
            throw new \RuntimeException("Redis connection failed: {$host}:{$port}");
        }

        if (!empty(self::$config['password'])) {
            self::$redis->auth(self::$config['password']);
        }

        self::$redis->select(self::$config['queue_db']);
        self::$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);

        return self::$redis;
    }

    public static function disconnect(): void {
        if (self::$redis !== null) {
            self::$redis->close();
            self::$redis = null;
        }
    }

    public static function isAvailable(): bool {
        try {
            $redis = self::connection();
            return $redis->ping() !== false;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Push a job onto the queue
     */
    public static function push(string $jobClass, array $data = [], string $queue = 'default'): void {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        $job = [
            'id' => bin2hex(random_bytes(16)),
            'job' => $jobClass,
            'data' => $data,
            'queue' => $queue,
            'attempts' => 0,
            'max_tries' => 3,
            'created_at' => microtime(true),
        ];

        $redis->lPush("{$prefix}queue:{$queue}", json_encode($job));
    }

    /**
     * Pop a job from the queue (blocking wait)
     */
    public static function pop(string $queue = 'default', int $timeout = 5): ?array {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        $result = $redis->brPop(["{$prefix}queue:{$queue}"], $timeout);

        if (!$result || !is_array($result) || count($result) < 2) {
            return null;
        }

        return json_decode($result[1], true);
    }

    /**
     * Push a job to the delayed queue (for retry)
     */
    public static function later(int $delaySeconds, string $jobClass, array $data = [], string $queue = 'default'): void {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        $job = [
            'id' => bin2hex(random_bytes(16)),
            'job' => $jobClass,
            'data' => $data,
            'queue' => $queue,
            'attempts' => 0,
            'max_tries' => 3,
            'created_at' => microtime(true),
            'available_at' => microtime(true) + $delaySeconds,
        ];

        $redis->zAdd("{$prefix}delayed", ['score' => (float)(microtime(true) + $delaySeconds)], json_encode($job));
    }

    /**
     * Move ready delayed jobs to the main queue
     */
    public static function releaseDelayed(): int {
        $redis = self::connection();
        $prefix = self::$config['prefix'];
        $now = microtime(true);

        $jobs = $redis->zRangeByScore("{$prefix}delayed", (string)0, (string)$now);
        $count = 0;

        foreach ($jobs as $jobJson) {
            $pipe = $redis->multi(\Redis::MULTI);
            $pipe->zRem("{$prefix}delayed", $jobJson);
            $job = json_decode($jobJson, true);
            $pipe->lPush("{$prefix}queue:{$job['queue']}", $jobJson);
            $pipe->exec();
            $count++;
        }

        return $count;
    }

    /**
     * Move a failed job to the failed jobs list
     */
    public static function fail(array $job, string $error = ''): void {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        $job['failed_at'] = microtime(true);
        $job['error'] = $error;

        $redis->lPush("{$prefix}failed", json_encode($job));
        $redis->lTrim("{$prefix}failed", 0, 499); // Keep last 500
    }

    /**
     * Get queue sizes
     */
    public static function sizes(): array {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        return [
            'default' => (int) $redis->lLen("{$prefix}queue:default"),
            'high' => (int) $redis->lLen("{$prefix}queue:high"),
            'low' => (int) $redis->lLen("{$prefix}queue:low"),
            'emails' => (int) $redis->lLen("{$prefix}queue:emails"),
            'push' => (int) $redis->lLen("{$prefix}queue:push"),
            'delayed' => (int) $redis->zCard("{$prefix}delayed"),
            'failed' => (int) $redis->lLen("{$prefix}failed"),
        ];
    }

    /**
     * Get failed jobs
     */
    public static function getFailed(int $limit = 50): array {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        return $redis->lRange("{$prefix}failed", 0, $limit - 1);
    }

    /**
     * Retry a failed job
     */
    public static function retryFailed(int $index = 0): ?array {
        $redis = self::connection();
        $prefix = self::$config['prefix'];

        $jobJson = $redis->lIndex("{$prefix}failed", $index);
        if (!$jobJson) return null;

        $job = json_decode($jobJson, true);
        $redis->lRem("{$prefix}failed", 1, $jobJson);

        $job['attempts'] = 0;
        unset($job['failed_at'], $job['error']);

        self::push($job['job'], $job['data'], $job['queue']);

        return $job;
    }

    /**
     * Clear all failed jobs
     */
    public static function flushFailed(): void {
        $redis = self::connection();
        $prefix = self::$config['prefix'];
        $redis->del("{$prefix}failed");
    }

    /**
     * Dispatch a job (alias for push)
     */
    public static function dispatch(string $jobClass, array $data = [], string $queue = ''): void {
        if ($queue === '' && class_exists($jobClass)) {
            $ref = new \ReflectionClass($jobClass);
            $ctor = $ref->getConstructor();
            $needsArgs = $ctor && count(array_filter($ctor->getParameters(), fn($p) => !$p->isDefaultValueAvailable())) > 0;
            if (!$needsArgs) {
                $tmp = new $jobClass();
                $queue = method_exists($tmp, 'queue') ? $tmp->queue() : 'default';
            } else {
                $queue = 'default';
            }
        }
        self::push($jobClass, $data, $queue ?: 'default');
    }
}
