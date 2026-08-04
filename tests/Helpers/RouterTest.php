<?php
namespace Tests\Helpers;

use App\Helpers\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private array $serverBackup;

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testBaseUrlReturnsHttpByDefault(): void
    {
        unset($_SERVER['HTTPS'], $_SERVER['SERVER_PORT'], $_SERVER['HTTP_X_FORWARDED_PROTO']);
        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertEquals('http://example.com', Router::baseUrl());
    }

    public function testBaseUrlReturnsHttpsWhenServerPortIs443(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals('https://example.com', Router::baseUrl());
    }

    public function testBaseUrlReturnsHttpsWhenHttpsIsOn(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals('https://example.com', Router::baseUrl());
    }

    public function testBaseUrlRespectsXForwardedProto(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->assertEquals('https://example.com', Router::baseUrl());
    }

    public function testUrlBuildsFullUrl(): void
    {
        $_SERVER['HTTP_HOST'] = 'balagh.dz';
        unset($_SERVER['HTTPS']);
        $this->assertEquals('http://balagh.dz/reports/123', Router::url('/reports/123'));
    }

    public function testUrlHandlesPathWithoutLeadingSlash(): void
    {
        $_SERVER['HTTP_HOST'] = 'test.dz';
        $this->assertEquals('http://test.dz/foo', Router::url('foo'));
    }

    public function testUrlPrefixedWithSlash(): void
    {
        $_SERVER['HTTP_HOST'] = 'test.dz';
        $result = Router::url('/api/stats');
        $this->assertEquals('http://test.dz/api/stats', $result);
    }

    public function testLoadAcceptsRoutes(): void
    {
        $routes = ['GET /' => ['ReportController', 'index']];
        Router::load($routes);
        $this->assertTrue(true);
    }
}
