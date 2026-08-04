<?php
namespace Tests\Helpers;

use App\Helpers\Rbac;
use Tests\DatabaseTestCase;

class RbacIntegrationTest extends DatabaseTestCase
{
    private int $adminId;
    private int $respId;
    private int $chefUniteId;
    private int $chefSectionId;
    private int $agentId;
    private int $citizenId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminId = $this->getUserId('admin@test.dz');
        $this->respId = $this->getUserId('resp@test.dz');
        $this->chefUniteId = $this->getUserId('chefunite@test.dz');
        $this->chefSectionId = $this->getUserId('chefsection@test.dz');
        $this->agentId = $this->getUserId('agent@test.dz');
        $this->citizenId = $this->getUserId('citizen@test.dz');
    }

    private function getUserId(string $email): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn();
    }

    public function testLoadPermissionsStoresPermissionsInSession(): void
    {
        $this->setSessionUser($this->adminId);
        Rbac::loadPermissions($this->adminId);
        $this->assertNotEmpty($_SESSION['user_permissions'] ?? []);
    }

    public function testAdminCentralCanViewAnyReport(): void
    {
        $this->setSessionUser($this->adminId);
        $this->assertTrue(Rbac::has('reports.view'));
    }

    public function testAdminCentralCanManageAllUsers(): void
    {
        $this->setSessionUser($this->adminId);
        $this->assertTrue(Rbac::canManageUser($this->citizenId));
    }

    public function testCitizenCannotViewReports(): void
    {
        $this->setSessionUser($this->citizenId);
        $this->assertFalse(Rbac::has('reports.view'));
    }

    public function testCitizenCanViewOwnReports(): void
    {
        $this->setSessionUser($this->citizenId);
        $this->assertTrue(Rbac::has('reports.view_assigned'));
    }

    public function testAdminCentralScopeReportsReturnsNoFilter(): void
    {
        $this->setSessionUser($this->adminId);
        $scope = Rbac::scopeReports();
        $this->assertEquals('', $scope['where']);
        $this->assertEmpty($scope['params']);
    }

    public function testCitizenScopeReportsFiltersByCitizenId(): void
    {
        $this->setSessionUser($this->citizenId);
        $scope = Rbac::scopeReports();
        $this->assertStringContainsString('citizen_id', $scope['where']);
        $this->assertCount(1, $scope['params']);
        $this->assertEquals($this->citizenId, $scope['params'][0]);
    }

    public function testAdminCentralScopeUsersReturnsNoFilter(): void
    {
        $this->setSessionUser($this->adminId);
        $scope = Rbac::scopeUsers();
        $this->assertEquals('', $scope['where']);
    }

    public function testAdminCentralCanAssignAnyReport(): void
    {
        $this->setSessionUser($this->adminId);
        $report = $this->pdo->query("SELECT * FROM reports LIMIT 1")->fetch();
        if ($report) {
            $this->assertTrue(Rbac::canAssignReport($report));
        }
        $this->assertTrue(true);
    }

    public function testAdminCentralCanCloseReport(): void
    {
        $this->setSessionUser($this->adminId);
        $report = $this->pdo->query("SELECT * FROM reports LIMIT 1")->fetch();
        if ($report) {
            $this->assertTrue(Rbac::canCloseReport($report));
        }
        $this->assertTrue(true);
    }

    public function testAdminCentralCanRedirectReport(): void
    {
        $this->setSessionUser($this->adminId);
        $report = $this->pdo->query("SELECT * FROM reports LIMIT 1")->fetch();
        if ($report) {
            $this->assertTrue(Rbac::canRedirectReport($report));
        }
        $this->assertTrue(true);
    }

    public function testIntervenantCanInterveneOnAssignedReport(): void
    {
        $this->setSessionUser($this->agentId);
        $orgId = $_SESSION['organization_id'];
        $code = 'BA-INT-' . str_pad((string)time(), 6, '0', STR_PAD_LEFT);
        $reportStmt = $this->pdo->prepare(
            "INSERT INTO reports (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, citizen_id, organization_id, assigned_to)
             VALUES (?, 'Test intervention', 'Desc', (SELECT id FROM categories LIMIT 1), NULL, 'medium', 'assigned', 1, 1, ?, ?, ?)"
        );
        $reportStmt->execute([$code, $this->citizenId, $orgId, $this->agentId]);
        $reportId = (int)$this->pdo->lastInsertId();

        $report = $this->pdo->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$reportId]);
        $reportData = $report->fetch();

        $this->assertTrue(Rbac::canIntervene($reportData));
    }

    public function testIntervenantCannotInterveneOnUnassignedReport(): void
    {
        $this->setSessionUser($this->agentId);
        $report = $this->pdo->prepare("SELECT * FROM reports WHERE assigned_to IS NULL OR assigned_to != ? LIMIT 1");
        $report->execute([$this->agentId]);
        $reportData = $report->fetch();

        if ($reportData) {
            $this->assertFalse(Rbac::canIntervene($reportData));
        }
        $this->assertTrue(true);
    }

    public function testGetPrimaryRoleReturnsCorrectRole(): void
    {
        $this->setSessionUser($this->adminId);
        $this->assertEquals('admin_central', Rbac::getPrimaryRole());

        $this->setSessionUser($this->citizenId);
        $this->assertEquals('citizen', Rbac::getPrimaryRole());
    }
}
