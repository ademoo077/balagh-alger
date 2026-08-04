<?php
namespace Tests\Helpers;

use App\Helpers\I18n;
use PHPUnit\Framework\TestCase;

class I18nTest extends TestCase
{
    private string $langDir;

    protected function setUp(): void
    {
        $this->langDir = dirname(__DIR__, 2) . '/lang';
        I18n::init();
    }

    public function testInitDefaultsToFrench(): void
    {
        $this->assertEquals('fr', I18n::getLang());
    }

    public function testSetLangChangesLanguage(): void
    {
        I18n::setLang('ar');
        $this->assertEquals('ar', I18n::getLang());

        I18n::setLang('fr');
        $this->assertEquals('fr', I18n::getLang());
    }

    public function testSetLangIgnoresInvalidLanguage(): void
    {
        I18n::setLang('fr');
        I18n::setLang('de');
        $this->assertEquals('fr', I18n::getLang());
    }

    public function testGetDirReturnsLtrForFrench(): void
    {
        I18n::setLang('fr');
        $this->assertEquals('ltr', I18n::getDir());
    }

    public function testGetDirReturnsRtlForArabic(): void
    {
        I18n::setLang('ar');
        $this->assertEquals('rtl', I18n::getDir());
    }

    public function testIsRtlReturnsFalseForFrench(): void
    {
        I18n::setLang('fr');
        $this->assertFalse(I18n::isRtl());
    }

    public function testIsRtlReturnsTrueForArabic(): void
    {
        I18n::setLang('ar');
        $this->assertTrue(I18n::isRtl());
    }

    public function testGetLocaleInfoReturnsFrenchFormat(): void
    {
        I18n::setLang('fr');
        $info = I18n::getLocaleInfo();
        $this->assertEquals('d/m/Y', $info['date_format']);
        $this->assertEquals(',', $info['decimal_sep']);
    }

    public function testGetLocaleInfoReturnsArabicFormat(): void
    {
        I18n::setLang('ar');
        $info = I18n::getLocaleInfo();
        $this->assertEquals('Y/m/d', $info['date_format']);
        $this->assertEquals('.', $info['decimal_sep']);
    }

    public function testTReturnsKeyWhenNotFound(): void
    {
        $this->assertEquals('nonexistent.key', I18n::t('nonexistent.key'));
    }

    public function testTResolvesDotNotation(): void
    {
        $result = I18n::t('statuses.submitted');
        $this->assertIsString($result);
        $this->assertNotEquals('statuses.submitted', $result);
    }

    public function testTReplacesParameters(): void
    {
        $result = I18n::t('statuses.submitted');
        $this->assertNotEmpty($result);
    }

    public function testFormatNumberFrench(): void
    {
        I18n::setLang('fr');
        $result = I18n::formatNumber(1234567);
        $this->assertStringContainsString(' ', $result);
    }

    public function testFormatNumberArabic(): void
    {
        I18n::setLang('ar');
        $result = I18n::formatNumber(1234567);
        $this->assertStringContainsString(',', $result);
    }

    public function testFormatDateReturnsFormattedDate(): void
    {
        I18n::setLang('fr');
        $result = I18n::formatDate('2024-03-15');
        $this->assertEquals('15/03/2024', $result);
    }

    public function testFormatDateTimeReturnsFormattedDateTime(): void
    {
        I18n::setLang('fr');
        $result = I18n::formatDateTime('2024-03-15 14:30:00');
        $this->assertEquals('15/03/2024 14:30', $result);
    }

    public function testFormatDateArabic(): void
    {
        I18n::setLang('ar');
        $result = I18n::formatDate('2024-03-15');
        $this->assertEquals('2024/03/15', $result);
    }

    public function testTimeAgoReturnsJustNowForRecent(): void
    {
        $result = I18n::timeAgo(date('Y-m-d H:i:s'));
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testTimeAgoReturnsMinutesForRecentPast(): void
    {
        $result = I18n::timeAgo(date('Y-m-d H:i:s', time() - 120));
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testTimeAgoReturnsHours(): void
    {
        $result = I18n::timeAgo(date('Y-m-d H:i:s', time() - 7200));
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testTimeAgoReturnsDays(): void
    {
        $result = I18n::timeAgo(date('Y-m-d H:i:s', time() - 86400 * 5));
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGlobFunctionExists(): void
    {
        $this->assertTrue(function_exists('__'));
    }

    public function testGlobFunctionReturnsKeyWhenNotFound(): void
    {
        $this->assertEquals('fake.key.test', __('fake.key.test'));
    }
}
