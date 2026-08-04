<?php
namespace Tests\Helpers;

use App\Helpers\Csrf;
use App\Helpers\Session;
use PHPUnit\Framework\TestCase;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        if (Session::has('csrf_token')) {
            Session::remove('csrf_token');
        }
    }

    public function testGenerateReturns32ByteHexToken(): void
    {
        $token = Csrf::generate();
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    public function testGenerateStoresTokenInSession(): void
    {
        $token = Csrf::generate();
        $this->assertEquals($token, Session::get('csrf_token'));
    }

    public function testVerifyReturnsTrueForValidToken(): void
    {
        $token = Csrf::generate();
        $this->assertTrue(Csrf::verify($token));
    }

    public function testVerifyReturnsFalseForInvalidToken(): void
    {
        Csrf::generate();
        $this->assertFalse(Csrf::verify('invalid-token'));
    }

    public function testVerifyReturnsFalseWhenNoTokenInSession(): void
    {
        Session::remove('csrf_token');
        $this->assertFalse(Csrf::verify('anything'));
    }

    public function testFieldReturnsHiddenInput(): void
    {
        $token = Csrf::generate();
        $field = Csrf::field();
        $this->assertStringContainsString('hidden', $field);
        $this->assertStringContainsString('_token', $field);
        $this->assertStringContainsString($token, $field);
    }

    public function testMetaReturnsMetaTag(): void
    {
        $meta = Csrf::meta();
        $this->assertStringContainsString('csrf-token', $meta);
        $this->assertStringContainsString('content="', $meta);
        $this->assertMatchesRegularExpression('/^<meta name="csrf-token" content="[0-9a-f]{64}">$/', $meta);
    }

    public function testGenerateGeneratesUniqueTokens(): void
    {
        $t1 = Csrf::generate();
        $t2 = Csrf::generate();
        $this->assertNotEquals($t1, $t2);
    }
}
