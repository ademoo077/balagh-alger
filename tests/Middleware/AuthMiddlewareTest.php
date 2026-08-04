<?php
namespace Tests\Middleware;

use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use PHPUnit\Framework\TestCase;

class AuthMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        $_SESSION = [];
    }

    public function testRequireAuthDoesNotThrowWhenAuthenticated(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        AuthMiddleware::requireAuth();
        $this->assertTrue(true);
    }

    public function testRequireRoleDoesNotThrowWithMatchingRole(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['admin_central']);
        AuthMiddleware::requireRole('admin_central');
        $this->assertTrue(true);
    }

    public function testRequireRoleWithAnyOfMultipleRoles(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['chef_section']);
        AuthMiddleware::requireRole('admin_central', 'chef_section');
        $this->assertTrue(true);
    }

    public function testRequireStaffDoesNotThrowWhenStaff(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['intervenant']);
        AuthMiddleware::requireStaff();
        $this->assertTrue(true);
    }

    public function testRequireStaffWithAdminCentral(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['admin_central']);
        AuthMiddleware::requireStaff();
        $this->assertTrue(true);
    }

    public function testRequireMinLevelDoesNotThrowWithSufficientLevel(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['admin_central']);
        AuthMiddleware::requireMinLevel(3);
        $this->assertTrue(true);
    }

    public function testRequireMinLevelWithExactLevel(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['chef_section']);
        AuthMiddleware::requireMinLevel(4);
        $this->assertTrue(true);
    }

    public function testRequirePermissionDoesNotThrowWithGrantedPermission(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['admin_central']);
        AuthMiddleware::requirePermission('anything');
        $this->assertTrue(true);
    }

    public function testRequireAnyPermissionDoesNotThrowWithOneMatch(): void
    {
        Session::setAuthenticated(1, 'test@test.com', 'Test');
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['reports.view']);
        AuthMiddleware::requireAnyPermission(['reports.view', 'users.create']);
        $this->assertTrue(true);
    }
}
