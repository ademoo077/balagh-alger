<?php
namespace Tests\Helpers;

use App\Helpers\DeadlineHelper;
use PHPUnit\Framework\TestCase;

class DeadlineHelperTest extends TestCase
{
    public function testGetDeadlineAddsDays(): void
    {
        $result = DeadlineHelper::getDeadline('2024-01-01 00:00:00', 7);
        $this->assertEquals('2024-01-08 00:00:00', $result);
    }

    public function testGetDeadlineWithZeroDays(): void
    {
        $result = DeadlineHelper::getDeadline('2024-01-01 12:00:00', 0);
        $this->assertEquals('2024-01-01 12:00:00', $result);
    }

    public function testGetDeadlineWithLargeDays(): void
    {
        $result = DeadlineHelper::getDeadline('2024-01-01 00:00:00', 30);
        $this->assertEquals('2024-01-31 00:00:00', $result);
    }

    public function testGetStatusReturnsTerminatedForResolved(): void
    {
        $result = DeadlineHelper::getStatus('2024-01-01', 7, 'resolved');
        $this->assertEquals('Terminé', $result['label']);
        $this->assertEquals('success', $result['class']);
        $this->assertNull($result['days_left']);
        $this->assertFalse($result['is_late']);
    }

    public function testGetStatusReturnsTerminatedForClosed(): void
    {
        $result = DeadlineHelper::getStatus('2024-01-01', 7, 'closed');
        $this->assertEquals('Terminé', $result['label']);
    }

    public function testGetStatusReturnsTerminatedForRejected(): void
    {
        $result = DeadlineHelper::getStatus('2024-01-01', 7, 'rejected');
        $this->assertEquals('Terminé', $result['label']);
    }

    public function testGetStatusReturnsLateForOverdue(): void
    {
        $result = DeadlineHelper::getStatus(
            date('Y-m-d H:i:s', strtotime('-10 days')),
            7,
            'submitted'
        );
        $this->assertTrue($result['is_late']);
        $this->assertEquals('danger', $result['class']);
        $this->assertGreaterThan(0, $result['days_left']);
    }

    public function testGetStatusReturnsWarningForLast20Percent(): void
    {
        $createdAt = date('Y-m-d H:i:s', time() - 86400 * 6);
        $result = DeadlineHelper::getStatus($createdAt, 7, 'submitted');
        $this->assertStringContainsString('restant', $result['label']);
        $this->assertFalse($result['is_late']);
    }

    public function testRenderBadgeReturnsTerminatedHtml(): void
    {
        $html = DeadlineHelper::renderBadge('2024-01-01', 7, 'resolved');
        $this->assertStringContainsString('bg-success', $html);
        $this->assertStringContainsString('Terminé', $html);
        $this->assertStringContainsString('fa-check', $html);
    }

    public function testRenderBadgeReturnsLateHtml(): void
    {
        $html = DeadlineHelper::renderBadge(
            date('Y-m-d H:i:s', strtotime('-10 days')),
            7,
            'submitted'
        );
        $this->assertStringContainsString('bg-danger', $html);
        $this->assertStringContainsString('fa-exclamation-triangle', $html);
    }

    public function testRenderBadgeReturnsWarningHtml(): void
    {
        $createdAt = date('Y-m-d H:i:s', time() - 86400 * 6);
        $html = DeadlineHelper::renderBadge($createdAt, 7, 'submitted');
        $this->assertStringContainsString('bg-warning', $html);
    }

    public function testRenderBadgeReturnsOkHtml(): void
    {
        $html = DeadlineHelper::renderBadge(date('Y-m-d H:i:s'), 7, 'submitted');
        $this->assertStringContainsString('bg-success', $html);
    }
}
