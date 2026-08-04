<?php
namespace Tests\Helpers;

use App\Helpers\Badge;
use Tests\DatabaseTestCase;

class BadgeIntegrationTest extends DatabaseTestCase
{
    private int $citizenId;

    protected function setUp(): void
    {
        parent::setUp();
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['citizen@test.dz']);
        $this->citizenId = (int)$stmt->fetchColumn();
    }

    public function testAwardReturnsTrueForValidBadge(): void
    {
        $result = Badge::award($this->citizenId, 'first_report');
        $this->assertTrue($result);
    }

    public function testAwardReturnsFalseForInvalidBadgeKey(): void
    {
        $result = Badge::award($this->citizenId, 'nonexistent_badge');
        $this->assertFalse($result);
    }

    public function testAwardDoesNotDuplicateBadge(): void
    {
        Badge::award($this->citizenId, 'first_report');
        $result = Badge::award($this->citizenId, 'first_report');
        $this->assertFalse($result);
    }

    public function testGetUserBadgesReturnsEarnedBadges(): void
    {
        Badge::award($this->citizenId, 'first_report');
        Badge::award($this->citizenId, 'commenter');

        $badges = Badge::getUserBadges($this->citizenId);
        $this->assertCount(2, $badges);
        $keys = array_column($badges, 'badge_key');
        $this->assertContains('first_report', $keys);
        $this->assertContains('commenter', $keys);
    }

    public function testGetUserBadgesReturnsEmptyForNewUser(): void
    {
        $badges = Badge::getUserBadges($this->citizenId);
        $this->assertEmpty($badges);
    }

    public function testCheckAndAwardAwardsFirstReportBadge(): void
    {
        $orgStmt = $this->pdo->query("SELECT id FROM organizations LIMIT 1");
        $orgId = (int)$orgStmt->fetchColumn();

        $code = 'BA-CA-' . str_pad((string)time(), 8, '0', STR_PAD_LEFT);
        for ($i = 0; $i < 3; $i++) {
            $this->pdo->prepare(
                "INSERT INTO reports (tracking_code, title, description, category_id, daira_id, commune_id, citizen_id, organization_id, status, priority)
                 VALUES (?, 'Test', 'Desc', (SELECT id FROM categories LIMIT 1), 1, 1, ?, ?, 'submitted', 'medium')"
            )->execute([$code . $i, $this->citizenId, $orgId]);
        }

        $earned = Badge::checkAndAward($this->citizenId);
        $this->assertContains('first_report', $earned);
    }

    public function testGetUserStatsReturnsCorrectCounts(): void
    {
        $orgStmt = $this->pdo->query("SELECT id FROM organizations LIMIT 1");
        $orgId = (int)$orgStmt->fetchColumn();

        $code = 'BA-US-' . str_pad((string)time(), 8, '0', STR_PAD_LEFT);
        for ($i = 0; $i < 5; $i++) {
            $this->pdo->prepare(
                "INSERT INTO reports (tracking_code, title, description, category_id, daira_id, commune_id, citizen_id, organization_id, status, priority)
                 VALUES (?, 'Test', 'Desc', (SELECT id FROM categories LIMIT 1), 1, 1, ?, ?, 'submitted', 'medium')"
            )->execute([$code . $i, $this->citizenId, $orgId]);
        }

        $stats = Badge::getUserStats($this->citizenId);
        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(0, $stats['resolved']);
        $this->assertEquals(5, $stats['active']);
        $this->assertEquals(0, $stats['rate']);
    }
}
