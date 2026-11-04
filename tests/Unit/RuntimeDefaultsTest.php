<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RuntimeDefaultsTest extends TestCase
{
    public function test_shipped_defaults_do_not_require_a_database(): void
    {
        $root = dirname(__DIR__, 2);

        $session = file_get_contents($root.'/config/session.php');
        $cache = file_get_contents($root.'/config/cache.php');
        $queue = file_get_contents($root.'/config/queue.php');
        $env = file_get_contents($root.'/.env.example');

        $this->assertIsString($session);
        $this->assertIsString($cache);
        $this->assertIsString($queue);
        $this->assertIsString($env);

        $this->assertStringContainsString("env('SESSION_DRIVER', 'cookie')", $session);
        $this->assertStringContainsString("env('CACHE_STORE', 'file')", $cache);
        $this->assertStringContainsString("env('QUEUE_CONNECTION', 'sync')", $queue);
        $this->assertStringContainsString('SESSION_DRIVER=cookie', $env);
        $this->assertStringContainsString('CACHE_STORE=file', $env);
        $this->assertStringContainsString('QUEUE_CONNECTION=sync', $env);
        $this->assertStringContainsString('DB_CONNECTION=sqlite', $env);
        $this->assertStringNotContainsString("env('OPENAI_API_KEY')", file_get_contents($root.'/resources/views/santa.blade.php') ?: '');
    }
}
