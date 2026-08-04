<?php
namespace Tests\Helpers;

use App\Helpers\Rbac;
use App\Helpers\Session;
use PHPUnit\Framework\TestCase;

class RbacTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        $_SESSION = [];
    }

    public function testGetPrimaryRoleReturnsNullWhenNoRoles(): void
    {
        $this->assertNull(Rbac::getPrimaryRole());
    }

    public function testGetPrimaryRoleReturnsHighestRole(): void
    {
        Session::set('user_roles', ['citizen', 'intervenant']);
        $this->assertEquals('intervenant', Rbac::getPrimaryRole());
    }

    public function testGetPrimaryRoleReturnsAdminCentralHighest(): void
    {
        Session::set('user_roles', ['citizen', 'chef_section', 'admin_central']);
        $this->assertEquals('admin_central', Rbac::getPrimaryRole());
    }

    public function testHasReturnsTrueForAdminCentral(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::has('anything.at.all'));
    }

    public function testHasReturnsTrueWhenPermissionGranted(): void
    {
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['reports.view']);
        $this->assertTrue(Rbac::has('reports.view'));
    }

    public function testHasReturnsFalseWhenPermissionMissing(): void
    {
        Session::set('user_roles', ['citizen']);
        Session::set('user_permissions', []);
        $this->assertFalse(Rbac::has('reports.view'));
    }

    public function testHasAnyReturnsTrueWhenOneMatches(): void
    {
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['reports.view']);
        $this->assertTrue(Rbac::hasAny(['reports.view', 'users.create']));
    }

    public function testHasAnyReturnsFalseWhenNoneMatch(): void
    {
        Session::set('user_roles', ['citizen']);
        Session::set('user_permissions', []);
        $this->assertFalse(Rbac::hasAny(['reports.view', 'users.create']));
    }

    public function testHasAllReturnsTrueWhenAllMatch(): void
    {
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['reports.view', 'reports.comment']);
        $this->assertTrue(Rbac::hasAll(['reports.view', 'reports.comment']));
    }

    public function testHasAllReturnsFalseWhenOneMissing(): void
    {
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['reports.view']);
        $this->assertFalse(Rbac::hasAll(['reports.view', 'users.create']));
    }

    public function testIsRoleReturnsTrueForSingleMatch(): void
    {
        Session::set('user_roles', ['citizen']);
        $this->assertTrue(Rbac::isRole('citizen'));
    }

    public function testIsRoleReturnsTrueForAnyMatch(): void
    {
        Session::set('user_roles', ['citizen', 'intervenant']);
        $this->assertTrue(Rbac::isRole('admin_central', 'citizen'));
    }

    public function testIsRoleReturnsFalseWhenNoMatch(): void
    {
        Session::set('user_roles', ['citizen']);
        $this->assertFalse(Rbac::isRole('admin_central'));
    }

    public function testMinLevelReturnsTrueForHighEnough(): void
    {
        Session::set('user_roles', ['chef_section']);
        $this->assertTrue(Rbac::minLevel(4));
    }

    public function testMinLevelReturnsFalseForNotHighEnough(): void
    {
        Session::set('user_roles', ['intervenant']);
        $this->assertFalse(Rbac::minLevel(4));
    }

    public function testMinLevelReturnsFalseWhenNoPrimaryRole(): void
    {
        $this->assertFalse(Rbac::minLevel(1));
    }

    public function testIsStaffReturnsTrueForNonCitizen(): void
    {
        Session::set('user_roles', ['intervenant']);
        $this->assertTrue(Rbac::isStaff());
    }

    public function testIsStaffReturnsFalseForCitizen(): void
    {
        Session::set('user_roles', ['citizen']);
        $this->assertFalse(Rbac::isStaff());
    }

    public function testCanManageUsersDelegatesToHasAny(): void
    {
        Session::set('user_roles', ['chef_section']);
        Session::set('user_permissions', ['users.create']);
        $this->assertTrue(Rbac::canManageUsers());
    }

    public function testCanCreateRoleReturnsTrueWhenHigherLevel(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::canCreateRole('citizen'));
    }

    public function testCanCreateRoleReturnsFalseWhenSameLevel(): void
    {
        Session::set('user_roles', ['chef_section']);
        $this->assertFalse(Rbac::canCreateRole('chef_section'));
    }

    public function testCanCreateRoleReturnsFalseWhenLowerLevel(): void
    {
        Session::set('user_roles', ['intervenant']);
        $this->assertFalse(Rbac::canCreateRole('chef_section'));
    }

    public function testCanCreateRoleReturnsFalseWhenNoPrimaryRole(): void
    {
        $this->assertFalse(Rbac::canCreateRole('citizen'));
    }

    public function testCanViewDashboardDelegatesToHas(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::canViewDashboard());
    }

    public function testCanViewStatsDelegatesToHas(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::canViewStats());
    }

    public function testCanViewAuditDelegatesToHas(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::canViewAudit());
    }

    public function testCanManageSettingsDelegatesToHas(): void
    {
        Session::set('user_roles', ['admin_central']);
        $this->assertTrue(Rbac::canManageSettings());
    }

    public function testCanViewDashboardReturnsFalseWithoutPermission(): void
    {
        Session::set('user_roles', ['citizen']);
        $this->assertFalse(Rbac::canViewDashboard());
    }
}
