<?php
namespace Tests\Helpers;

use App\Helpers\Badge;
use PHPUnit\Framework\TestCase;

class BadgeTest extends TestCase
{
    public function testGetDefinitionsReturnsArray(): void
    {
        $defs = Badge::getDefinitions();
        $this->assertIsArray($defs);
    }

    public function testGetDefinitionsHasExpectedKeys(): void
    {
        $defs = Badge::getDefinitions();
        $expectedKeys = ['first_report', 'report_5', 'report_10', 'report_25', 'report_50',
                         'first_resolved', 'resolved_5', 'resolved_10', 'speedster', 'commenter'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $defs);
        }
    }

    public function testGetDefinitionsEachHasRequiredFields(): void
    {
        $defs = Badge::getDefinitions();
        foreach ($defs as $key => $def) {
            $this->assertArrayHasKey('name', $def, "Badge $key missing name");
            $this->assertArrayHasKey('icon', $def, "Badge $key missing icon");
            $this->assertArrayHasKey('color', $def, "Badge $key missing color");
            $this->assertArrayHasKey('desc', $def, "Badge $key missing desc");
            $this->assertIsString($def['name']);
            $this->assertIsString($def['icon']);
            $this->assertIsString($def['color']);
            $this->assertIsString($def['desc']);
        }
    }

    public function testGetDefinitionsReturnsTenBadges(): void
    {
        $this->assertCount(10, Badge::getDefinitions());
    }
}
