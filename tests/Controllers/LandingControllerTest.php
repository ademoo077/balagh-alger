<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;

class LandingControllerTest extends TestCase
{
    private string $baseUrl = 'http://localhost:8000';

    public function testLandingPageReturns200(): void
    {
        $ch = curl_init($this->baseUrl . '/');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 5,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);
    }

    public function testStatsApiReturnsJson(): void
    {
        $ch = curl_init($this->baseUrl . '/api/landing-stats');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);
        $this->assertStringContainsString('application/json', $contentType);
    }

    public function testStatsApiReturnsValidStructure(): void
    {
        $ch = curl_init($this->baseUrl . '/api/landing-stats');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('today', $data);
        $this->assertArrayHasKey('in_progress', $data);
        $this->assertArrayHasKey('resolved_month', $data);
    }

    public function testStatsApiValuesAreIntegers(): void
    {
        $ch = curl_init($this->baseUrl . '/api/landing-stats');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($body, true);
        $this->assertIsInt($data['today']);
        $this->assertIsInt($data['in_progress']);
        $this->assertIsInt($data['resolved_month']);
    }
}
