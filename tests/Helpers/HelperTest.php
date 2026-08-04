<?php
namespace Tests\Helpers;

use App\Helpers\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testSanitizeTrimsAndEscapesHtml(): void
    {
        $result = Helper::sanitize('  <script>alert("xss")</script>  ');
        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $result);
    }

    public function testSanitizeReturnsEmptyStringForWhitespace(): void
    {
        $this->assertEquals('', Helper::sanitize('   '));
    }

    public function testSanitizePreservesNormalText(): void
    {
        $this->assertEquals('hello world', Helper::sanitize('  hello world  '));
    }

    public function testGenerateTrackingCodeFormat(): void
    {
        $code = Helper::generateTrackingCode();
        $this->assertMatchesRegularExpression('/^BA-\d{4}-\d{6}$/', $code);
    }

    public function testGenerateTrackingCodeYearIsCurrent(): void
    {
        $code = Helper::generateTrackingCode();
        $this->assertStringStartsWith('BA-' . date('Y') . '-', $code);
    }

    public function testGenerateTrackingCodeIsUnique(): void
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = Helper::generateTrackingCode();
        }
        $this->assertEquals(count($codes), count(array_unique($codes)));
    }

    public function testSlugifyBasic(): void
    {
        $this->assertEquals('hello-world', Helper::slugify('Hello World'));
    }

    public function testSlugifyRemovesSpecialChars(): void
    {
        $this->assertEquals('bonjour-paris', Helper::slugify('Bonjour, Paris!'));
    }

    public function testSlugifyHandlesAccents(): void
    {
        $result = Helper::slugify('Procès-verbal');
        $this->assertMatchesRegularExpression('/^[a-z-]+$/', $result);
    }

    public function testSlugifyTrimsDashes(): void
    {
        $this->assertEquals('test', Helper::slugify('--test--'));
    }

    public function testSlugifyCollapsesMultipleDashes(): void
    {
        $this->assertEquals('a-b-c', Helper::slugify('a   b   c'));
    }

    public function testFormatPhone10Digits(): void
    {
        $this->assertEquals('05 55 12 34 56', Helper::formatPhone('0555123456'));
    }

    public function testFormatPhoneWithSpacesAlready(): void
    {
        $this->assertEquals('05 55 12 34 56', Helper::formatPhone('05 55 12 34 56'));
    }

    public function testFormatPhoneNon10Digits(): void
    {
        $this->assertEquals('123', Helper::formatPhone('123'));
    }

    public function testGetStatusBadgeReturnsHtml(): void
    {
        $badge = Helper::getStatusBadge('submitted');
        $this->assertStringContainsString('span', $badge);
        $this->assertStringContainsString('badge', $badge);
        $this->assertStringContainsString('bg-info', $badge);
    }

    public function testGetStatusBadgeWithUnknownStatus(): void
    {
        $badge = Helper::getStatusBadge('unknown_status');
        $this->assertStringContainsString('badge', $badge);
        $this->assertStringContainsString('bg-secondary', $badge);
    }

    public function testGetStatusBadgeForAllStatuses(): void
    {
        $statuses = ['submitted', 'acknowledged', 'assigned', 'in_progress',
                     'pending_review', 'validated', 'resolved', 'closed', 'rejected'];
        foreach ($statuses as $s) {
            $badge = Helper::getStatusBadge($s);
            $this->assertStringContainsString('badge', $badge, "Status: $s");
        }
    }

    public function testGetPriorityBadgeReturnsHtml(): void
    {
        $badge = Helper::getPriorityBadge('high');
        $this->assertStringContainsString('badge', $badge);
        $this->assertStringContainsString('bg-danger', $badge);
    }

    public function testGetPriorityBadgeWithUnknownPriority(): void
    {
        $badge = Helper::getPriorityBadge('unknown');
        $this->assertStringContainsString('bg-secondary', $badge);
    }

    public function testGetPriorityBadgeForAllPriorities(): void
    {
        $priorities = ['low', 'medium', 'high', 'urgent'];
        foreach ($priorities as $p) {
            $badge = Helper::getPriorityBadge($p);
            $this->assertStringContainsString('badge', $badge, "Priority: $p");
        }
    }
}
