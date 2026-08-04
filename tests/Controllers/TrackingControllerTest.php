<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;

class TrackingControllerTest extends TestCase
{
    private string $baseUrl = 'http://localhost:8000';

    public function testTrackingPageReturns200(): void
    {
        $ch = curl_init($this->baseUrl . '/suivi');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);
    }

    public function testTrackingWithValidCodeRedirects(): void
    {
        $ch = curl_init($this->baseUrl . '/suivi?code=BA-TEST');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(302, $httpCode);
    }
}
