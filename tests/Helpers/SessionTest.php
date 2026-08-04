<?php
namespace Tests\Helpers;

use App\Helpers\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        $_SESSION = [];
    }

    public function testStartInitializesSession(): void
    {
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    public function testSetAndGet(): void
    {
        Session::set('key', 'value');
        $this->assertEquals('value', Session::get('key'));
    }

    public function testGetReturnsDefaultWhenNotSet(): void
    {
        $this->assertNull(Session::get('nonexistent'));
        $this->assertEquals('default', Session::get('nonexistent', 'default'));
    }

    public function testHasReturnsTrueWhenSet(): void
    {
        Session::set('key', 'value');
        $this->assertTrue(Session::has('key'));
    }

    public function testHasReturnsFalseWhenNotSet(): void
    {
        $this->assertFalse(Session::has('nonexistent'));
    }

    public function testRemove(): void
    {
        Session::set('key', 'value');
        Session::remove('key');
        $this->assertFalse(Session::has('key'));
    }

    public function testDestroy(): void
    {
        Session::set('key', 'value');
        Session::destroy();
        $this->assertFalse(Session::has('key'));
    }

    public function testFlash(): void
    {
        Session::flash('success', 'Operation completed');
        $this->assertEquals('Operation completed', Session::getFlash('success'));
        $this->assertNull(Session::getFlash('success'));
    }

    public function testGetFlashReturnsDefaultAfterConsumed(): void
    {
        Session::flash('key', 'val');
        Session::getFlash('key');
        $this->assertNull(Session::getFlash('key'));
    }

    public function testSetAuthenticated(): void
    {
        Session::setAuthenticated(1, 'user@test.com', 'Test User');
        $this->assertTrue(Session::isAuthenticated());
        $this->assertEquals(1, Session::getUserId());
        $this->assertEquals('Test User', Session::getUserName());
    }

    public function testIsAuthenticatedReturnsFalseByDefault(): void
    {
        $this->assertFalse(Session::isAuthenticated());
    }

    public function testGetUserIdReturnsNullWhenNotAuthenticated(): void
    {
        $this->assertNull(Session::getUserId());
    }

    public function testGetAvatarReturnsNullWhenNotSet(): void
    {
        $this->assertNull(Session::getAvatar());
    }

    public function testGetAvatarReturnsNullForDefault(): void
    {
        Session::set('user_avatar', 'default.png');
        $this->assertNull(Session::getAvatar());
    }

    public function testGetAvatarReturnsValueWhenSet(): void
    {
        Session::set('user_avatar', 'avatar123.jpg');
        $this->assertEquals('avatar123.jpg', Session::getAvatar());
    }
}
